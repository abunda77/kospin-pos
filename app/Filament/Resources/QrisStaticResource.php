<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QrisStaticResource\Pages;
use App\Helpers\QrisHelper;
use App\Models\QrisStatic;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;

class QrisStaticResource extends Resource
{
    protected static ?string $model = QrisStatic::class;

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';

    protected static ?string $navigationLabel = 'QRIS Statis';

    protected static ?string $navigationGroup = 'Manajemen Keuangan';

    protected static ?int $navigationSort = 5;

    protected static ?string $pluralLabel = 'QRIS Statis';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Contoh: QRIS Akun Utama'),

                Forms\Components\Section::make('Input QRIS')
                    ->description('Upload gambar QRIS atau paste string QRIS secara manual')
                    ->schema([
                        Forms\Components\FileUpload::make('qris_image')
                            ->label('Upload Gambar QRIS')
                            ->image()
                            ->disk('public')
                            ->directory('qris-images')
                            ->imagePreviewHeight('200')
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg'])
                            ->maxSize(2048)
                            ->helperText('Upload gambar QR code (PNG/JPG, max 2MB). String QRIS akan diekstrak otomatis.')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get, $livewire) {
                                if ($state) {
                                    try {
                                        // Get the temporary uploaded file
                                        $file = is_string($state) ? $livewire->getTemporaryUploadedFile($state) : $state;

                                        if ($file) {
                                            $tempPath = $file->getRealPath();
                                            Log::info('Processing uploaded file from: '.$tempPath);

                                            // Read QRIS from uploaded image
                                            $qrisString = QrisHelper::readQrisFromImage($tempPath);

                                            if ($qrisString) {
                                                $set('qris_string', $qrisString);

                                                // Auto-detect merchant name
                                                $merchantName = QrisHelper::parseMerchantName($qrisString);
                                                $set('merchant_name', $merchantName);

                                                Notification::make()
                                                    ->title('QRIS Berhasil Terdeteksi')
                                                    ->body("Merchant: {$merchantName}")
                                                    ->success()
                                                    ->send();
                                            } else {
                                                Notification::make()
                                                    ->title('Gagal Membaca QRIS')
                                                    ->body('Tidak dapat mengekstrak string QRIS dari gambar. Silakan paste manual.')
                                                    ->warning()
                                                    ->send();
                                            }
                                        }
                                    } catch (\Exception $e) {
                                        Log::error('Error processing QRIS upload: '.$e->getMessage());
                                        Notification::make()
                                            ->title('Error Upload')
                                            ->body('Error memproses gambar. Silakan coba lagi.')
                                            ->danger()
                                            ->send();
                                    }
                                }
                            })
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('qris_string')
                            ->label('String QRIS Statis')
                            ->required()
                            ->rows(4)
                            ->placeholder('Paste kode QRIS statis Anda di sini atau upload gambar di atas...')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state && ! $get('merchant_name')) {
                                    // Auto-detect merchant name when pasting
                                    $merchantName = QrisHelper::parseMerchantName($state);
                                    $set('merchant_name', $merchantName);
                                }
                            })
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('merchant_name')
                    ->label('Nama Merchant')
                    ->maxLength(255)
                    ->placeholder('Terdeteksi otomatis dari QRIS')
                    ->helperText('Akan terisi otomatis ketika QRIS di-upload atau di-paste'),

                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('qris_image')
                    ->label('Gambar QR')
                    ->disk('public')
                    ->size(60)
                    ->defaultImageUrl(url('/images/qr-placeholder.png'))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->description(fn (QrisStatic $record): string => $record->description ?? ''),

                Tables\Columns\TextColumn::make('merchant_name')
                    ->label('Merchant')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-building-storefront')
                    ->iconColor('primary'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua')
                    ->trueLabel('Hanya Aktif')
                    ->falseLabel('Hanya Tidak Aktif'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalContent(fn (QrisStatic $record) => view('filament.resources.qris-static.view-modal', ['record' => $record]))
                    ->modalWidth('2xl'),

                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQrisStatics::route('/'),
            'create' => Pages\CreateQrisStatic::route('/create'),
            'edit' => Pages\EditQrisStatic::route('/{record}/edit'),
        ];
    }
}
