<?php

namespace App\Filament\Pages;

use App\Models\QrisDynamic;
use App\Models\QrisStatic;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class QrisDynamicGenerator extends Page implements HasForms, HasTable
{
    use HasPageShield, InteractsWithForms, InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';

    protected static string $view = 'filament.pages.qris-dynamic-generator';

    protected static ?string $navigationLabel = 'QRIS Generator';

    protected static ?string $title = 'QRIS Dynamic Generator';

    protected static ?string $navigationGroup = 'Manajemen Keuangan';

    protected static ?int $navigationSort = 10;

    public ?array $data = [];

    public ?string $dynamicQris = null;

    public ?string $merchantName = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('saved_qris')
                    ->label('Select Saved QRIS (Optional)')
                    ->placeholder('Choose from saved QRIS codes...')
                    ->options(QrisStatic::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $qris = QrisStatic::find($state);
                            if ($qris) {
                                $set('static_qris', $qris->qris_string);
                            }
                        }
                    })
                    ->columnSpanFull(),

                Textarea::make('static_qris')
                    ->label('Static QRIS Code')
                    ->placeholder('Paste your static QRIS string here or select from saved...')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),

                TextInput::make('amount')
                    ->label('Amount (Rp)')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->prefix('Rp')
                    ->placeholder('10000'),

                Select::make('fee_type')
                    ->label('Fee Type')
                    ->options([
                        'Rupiah' => 'Rupiah (Fixed Amount)',
                        'Persentase' => 'Percentage (%)',
                    ])
                    ->default('Rupiah')
                    ->reactive(),

                TextInput::make('fee_value')
                    ->label('Fee Value')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->suffix(fn ($get) => $get('fee_type') === 'Persentase' ? '%' : 'Rp')
                    ->placeholder('0'),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(QrisDynamic::query()->latest())
            ->columns([
                TextColumn::make('merchant_name')
                    ->label('Merchant')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('fee_type')
                    ->label('Fee Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Rupiah' => 'success',
                        'Persentase' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('fee_value')
                    ->label('Fee Value')
                    ->formatStateUsing(fn ($record) => $record->fee_type === 'Persentase' 
                        ? $record->fee_value . '%' 
                        : 'Rp ' . number_format($record->fee_value, 0, ',', '.')),
                TextColumn::make('qrisStatic.name')
                    ->label('Source QRIS')
                    ->default('-')
                    ->searchable(),
                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->default('-')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Generated At')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (QrisDynamic $record) {
                        if (!$record->qr_image_path || !Storage::disk('public')->exists($record->qr_image_path)) {
                            Notification::make()
                                ->title('Image Not Found')
                                ->body('QR code image is not available.')
                                ->warning()
                                ->send();
                            return;
                        }

                        $recordId = $record->getKey();
                        return response()->download(
                            Storage::disk('public')->path($record->qr_image_path),
                            'qris-dynamic-' . $recordId . '-' . now()->format('YmdHis') . '.png'
                        );
                    }),
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading('QRIS Details')
                    ->modalContent(fn (QrisDynamic $record) => view('filament.pages.qris-dynamic-view', ['record' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
                DeleteAction::make()
                    ->label('Delete')
                    ->before(function (QrisDynamic $record) {
                        // Delete QR image file
                        if ($record->qr_image_path && Storage::disk('public')->exists($record->qr_image_path)) {
                            Storage::disk('public')->delete($record->qr_image_path);
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('QRIS Deleted')
                            ->body('Dynamic QRIS has been deleted successfully.')
                    ),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\DeleteBulkAction::make()
                    ->before(function ($records) {
                        foreach ($records as $record) {
                            if ($record->qr_image_path && Storage::disk('public')->exists($record->qr_image_path)) {
                                Storage::disk('public')->delete($record->qr_image_path);
                            }
                        }
                    }),
            ]);
    }

    public function generate(): void
    {
        $data = $this->form->getState();

        try {
            $this->merchantName = $this->parseMerchantName($data['static_qris']);

            $this->dynamicQris = $this->generateDynamicQris(
                $data['static_qris'],
                $data['amount'],
                $data['fee_type'] ?? 'Rupiah',
                $data['fee_value'] ?? '0'
            );

            // Generate and save QR code image
            $qrImagePath = $this->generateQrImage();

            // Save to database
            QrisDynamic::create([
                'qris_static_id' => $data['saved_qris'] ?? null,
                'merchant_name' => $this->merchantName,
                'qris_string' => $this->dynamicQris,
                'amount' => $data['amount'],
                'fee_type' => $data['fee_type'] ?? 'Rupiah',
                'fee_value' => $data['fee_value'] ?? 0,
                'qr_image_path' => $qrImagePath,
                'created_by' => Auth::id(),
            ]);

            Notification::make()
                ->title('Dynamic QRIS Generated Successfully')
                ->body('QRIS has been saved to the list below.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error Generating QRIS')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function generateQrImage(): ?string
    {
        if (! $this->dynamicQris) {
            return null;
        }

        try {
            // Use endroid/qr-code library to generate QR image
            $builder = new Builder(
                writer: new PngWriter,
                writerOptions: [],
                validateResult: false,
                data: $this->dynamicQris,
                encoding: new Encoding('UTF-8'),
                size: 400,
                margin: 10,
            );

            $result = $builder->build();

            // Save to storage
            $filename = 'qris-generated/qris-dynamic-'.now()->format('YmdHis').'-'.uniqid().'.png';
            Storage::disk('public')->put($filename, $result->getString());

            // Store filename in session for download
            session(['last_generated_qr' => $filename]);

            Log::info('QR code image generated: '.$filename);

            return $filename;
        } catch (\Exception $e) {
            Log::error('Error generating QR image: '.$e->getMessage());
            Log::error('Stack trace: '.$e->getTraceAsString());
            return null;
        }
    }

    public function downloadImage()
    {
        $filename = session('last_generated_qr');

        if (! $filename || ! Storage::disk('public')->exists($filename)) {
            Notification::make()
                ->title('Image Not Found')
                ->body('Please generate QRIS first.')
                ->warning()
                ->send();

            return;
        }

        return response()->download(
            Storage::disk('public')->path($filename),
            'qris-dynamic-'.now()->format('YmdHis').'.png'
        );
    }

    public function resetForm(): void
    {
        $this->dynamicQris = null;
        $this->merchantName = null;
        $this->form->fill();

        Notification::make()
            ->title('Form Reset')
            ->info()
            ->send();
    }

    protected function parseMerchantName(string $qrisData): string
    {
        $tag = '59';
        $tagIndex = strpos($qrisData, $tag);

        if ($tagIndex === false) {
            return 'Merchant';
        }

        try {
            $lengthIndex = $tagIndex + strlen($tag);
            $lengthStr = substr($qrisData, $lengthIndex, 2);
            $length = intval($lengthStr);

            if ($length <= 0) {
                return 'Merchant';
            }

            $valueIndex = $lengthIndex + 2;
            $merchantName = substr($qrisData, $valueIndex, $length);

            return trim($merchantName) ?: 'Merchant';
        } catch (\Exception $e) {
            return 'Merchant';
        }
    }

    protected function generateDynamicQris(
        string $staticQris,
        string $amount,
        string $feeType,
        string $feeValue
    ): string {
        if (strlen($staticQris) < 4) {
            throw new \Exception('Invalid static QRIS data.');
        }

        // Remove CRC (last 4 characters)
        $qrisWithoutCrc = substr($staticQris, 0, -4);

        // Change from static (01) to dynamic (12)
        $step1 = str_replace('010211', '010212', $qrisWithoutCrc);

        // Split by merchant country code
        $parts = explode('5802ID', $step1);

        if (count($parts) !== 2) {
            throw new \Exception("QRIS data is not in the expected format (missing '5802ID').");
        }

        // Build amount tag
        $amountStr = strval(intval($amount)); // Remove leading zeros
        $amountTag = '54'.str_pad(strlen($amountStr), 2, '0', STR_PAD_LEFT).$amountStr;

        // Build fee tag if applicable
        $feeTag = '';
        if ($feeValue && floatval($feeValue) > 0) {
            if ($feeType === 'Rupiah') {
                $feeValueStr = strval(intval($feeValue));
                $feeTag = '55020256'.str_pad(strlen($feeValueStr), 2, '0', STR_PAD_LEFT).$feeValueStr;
            } else { // Persentase
                $feeTag = '55020357'.str_pad(strlen($feeValue), 2, '0', STR_PAD_LEFT).$feeValue;
            }
        }

        // Reconstruct payload
        $payload = $parts[0].$amountTag.$feeTag.'5802ID'.$parts[1];

        // Calculate and append CRC
        $finalCrc = $this->crc16($payload);

        return $payload.$finalCrc;
    }

    protected function crc16(string $str): string
    {
        $crc = 0xFFFF;
        $strlen = strlen($str);

        for ($c = 0; $c < $strlen; $c++) {
            $crc ^= ord($str[$c]) << 8;
            for ($i = 0; $i < 8; $i++) {
                if ($crc & 0x8000) {
                    $crc = ($crc << 1) ^ 0x1021;
                } else {
                    $crc = $crc << 1;
                }
            }
        }

        $hex = strtoupper(dechex($crc & 0xFFFF));

        return str_pad($hex, 4, '0', STR_PAD_LEFT);
    }
}
