<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
use App\Models\Anggota;
use App\Models\Product;
use App\Models\Setting;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Mike42\Escpos\Printer;
use App\Models\OrderProduct;
use App\Models\PaymentMethod;
use Mike42\Escpos\EscposImage;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Repeater;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\OrderResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use App\Filament\Resources\OrderResource\RelationManagers;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;





class OrderResource extends Resource implements HasShieldPermissions
{
    public static function getPermissionPrefixes(): array
    {
        return [
            'view_any',
            'create',
            'update',
            'delete_any',
        ];
    }
    protected static ?string $model = Order::class;



    protected static ?string $navigationIcon = 'heroicon-m-shopping-bag';

    protected static ?string $navigationLabel = 'Penjualan';

    protected static ?string $pluralLabel = 'Penjualan';

    protected static ?string $navigationGroup = 'Menejemen keuangan';

    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Select::make('name')
                            ->label('Anggota')
                            ->options(Anggota::pluck('nama_lengkap', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $anggota = Anggota::find($state);
                                    $set('name', $anggota->nama_lengkap);
                                } else {
                                    $set('name', null);
                                }
                            })
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255)
                            ->columnSpan(1),
                        Forms\Components\DatePicker::make('birthday'),
                    ]),
                Forms\Components\Section::make('Produk dipesan')->schema([
                    self::getItemsRepeater(),
                ]),


                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('discount')
                            ->label('Diskon')
                            ->options([
                                0 => '0%',
                                5 => '5%',
                                10 => '10%',
                                15 => '15%',
                                20 => '20%',
                                25 => '25%',
                                30 => '30%',
                            ])
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                self::updateTotalPrice($get, $set);
                            })
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('total_price')
                            ->required()
                            ->readOnly()
                            ->columnSpan(1)
                            ->numeric(),

                        Forms\Components\Select::make('payment_method_id')
                            ->relationship('paymentMethod', 'name')
                            ->reactive()
                            ->columnSpan(1),
                        Forms\Components\Hidden::make('is_cash')
                            ->dehydrated(),
                        Forms\Components\Textarea::make('note')
                            ->columnSpanFull()
                            ->columnSpan(2),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount')
                    ->numeric()
                    ->suffix('%'),
                Tables\Columns\TextColumn::make('whatsapp')
                    ->searchable(),
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled'
                    ])
                    ->searchable(),
                Tables\Columns\TextColumn::make('paymentMethod.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                // Action::make('Print')
                //     ->label('Cetak')
                //     ->hidden(fn () => Setting::first()->value('print_via_mobile'))
                //     ->action(function (Order $record) {
                //         try {

                //             $order = Order::findOrFail($record->id);
                //             $order_items = OrderProduct::where('order_id', $order->id)->get();
                //             $setting = Setting::first();

                //             // Sesuaikan nama printer Anda
                //             $connector = new WindowsPrintConnector($setting->name_printer);
                //             $printer = new Printer($connector);


                //             // Muat gambar logo

                //             $logo = EscposImage::load(public_path('storage/'. $setting->image), true);


                //               // Lebar kertas (58mm: 32 karakter, 80mm: 48 karakter)
                //             $lineWidth = 32;

                //             // Fungsi untuk merapikan teks
                //             function formatRow($name, $qty, $price, $lineWidth) {
                //                 $nameWidth = 16; // Alokasi 16 karakter untuk nama produk
                //                 $qtyWidth = 8;   // Alokasi 8 karakter untuk Qty
                //                 $priceWidth = 8; // Alokasi 8 karakter untuk Harga

                //                 // Bungkus nama produk jika panjangnya melebihi alokasi
                //                 $nameLines = str_split($name, $nameWidth);

                //                 // Siapkan variabel untuk hasil format
                //                 $output = '';

                //                 // Tambahkan semua baris nama produk kecuali yang terakhir
                //                 for ($i = 0; $i < count($nameLines) - 1; $i++) {
                //                     $output .= str_pad($nameLines[$i], $lineWidth) . "\n"; // Baris dengan nama saja
                //                 }

                //                 // Baris terakhir dengan Qty dan Harga
                //                 $lastLine = $nameLines[count($nameLines) - 1]; // Baris terakhir dari nama
                //                 $lastLine = str_pad($lastLine, $nameWidth);   // Tambahkan padding untuk nama
                //                 $qty = str_pad($qty, $qtyWidth, " ", STR_PAD_BOTH); // Qty di tengah
                //                 $price = str_pad($price, $priceWidth, " ", STR_PAD_LEFT); // Harga di kanan

                //                 // Gabungkan semua
                //                 $output .= $lastLine . $qty . $price;

                //                 return $output;
                //             }


                //             // Header Struk
                //             $printer->setJustification(Printer::JUSTIFY_CENTER);
                //             $printer->bitImage($logo); // Cetak gambar logo
                //             $printer->setTextSize(1, 2);
                //             $printer->setEmphasis(true); // Tebal
                //             $printer->text($setting->shop . "\n");
                //             $printer->setTextSize(1, 1);
                //             $printer->setEmphasis(false); // Tebal
                //             $printer->text($setting->address . "\n");
                //             $printer->text($setting->phone ."\n");
                //             $printer->text("================================\n");

                //             // Detail Transaksi
                //             $printer->setJustification(Printer::JUSTIFY_LEFT);
                //             if ($record->name) {
                //                 $printer->text("Nama: " . $record->name . "\n");
                //             }
                //             if ($record->paymentMethod->is_cash) {
                //                 $printer->text("Pembayaran: " . $record->paymentMethod->name . "\n");
                //             } else {
                //                 $printer->text("Pembayaran: " . $record->paymentMethod->name . "\n");
                //             }
                //             $printer->text("Tanggal: " . $record->created_at->format('d-m-Y H:i:s') . "\n");
                //             $printer->text("================================\n");
                //             $printer->text(formatRow("Nama Barang", "Qty", "Harga", $lineWidth) . "\n");
                //             $printer->text("--------------------------------\n");
                //             foreach ($order_items as $item) {
                //                 $product = Product::find( $item->product_id);
                //                 $printer->text(formatRow($product->name ,$item->quantity , number_format($item->unit_price), $lineWidth) . "\n");
                //             }

                //             $printer->text("--------------------------------\n");

                //             $total = 0;
                //             foreach($order_items as $item) {
                //                 $total += $item->quantity * $item->unit_price;
                //             }
                //             $printer->setEmphasis(true); // Tebal
                //             $printer->text(formatRow("Total","",number_format($total), $lineWidth) . "\n");
                //             $printer->setEmphasis(false); // Tebal

                //             // Footer Struk
                //             $printer->setJustification(Printer::JUSTIFY_CENTER);
                //             $printer->text("================================\n");
                //             $printer->text("Terima Kasih!\n");
                //             $printer->text("================================\n");

                //             $printer->cut();
                //             $printer->close();
                //             Notification::make()
                //             ->title('Struk berhasil dicetak')
                //             ->success()
                //             ->icon('heroicon-o-printer')
                //             ->send();
                //         } catch (\Exception $e) {
                //             Notification::make()
                //             ->title('Printer tidak terdaftar')
                //             ->icon('heroicon-o-printer')
                //             ->danger()
                //             ->send();
                //         }

                //     })
                //     ->icon('heroicon-o-printer')
                //     ->color('amber'),

                Action::make('PrintPDF')
                    ->label('Cetak Ulang Struk')
                    ->action(function (Order $record) {
                        $order = Order::findOrFail($record->id);
                        $order_items = OrderProduct::where('order_id', $order->id)->get();
                        $setting = Setting::first();

                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('struk', [
                            'order' => $order,
                            'order_items' => $order_items,
                            'setting' => $setting
                        ])->setPaper([0, 0, 226.77, 700], 'portrait');

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, 'struk-' . $record->id . '.pdf');
                    })
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success'),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            // Kembalikan stok untuk setiap order yang akan dihapus
                            foreach ($records as $record) {
                                if ($record->orderProducts) {
                                    foreach ($record->orderProducts as $orderProduct) {
                                        $product = $orderProduct->product;
                                        if ($product) {
                                            Log::info('Mengembalikan stok produk (bulk delete):', [
                                                'product_id' => $product->id,
                                                'old_stock' => $product->stock,
                                                'returned_quantity' => $orderProduct->quantity,
                                                'new_stock' => $product->stock + $orderProduct->quantity
                                            ]);

                                            $product->increment('stock', $orderProduct->quantity);
                                        }
                                    }
                                }
                            }
                        })
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getItemsRepeater(): Repeater
    {
        return Repeater::make('orderProducts')
            ->relationship()
            ->live()
            ->columns([
                'md' => 10,
            ])
            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                self::updateTotalPrice($get, $set);
            })
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Produk')
                    ->required()
                    ->options(Product::query()->where('stock', '>', 1)->pluck('name', 'id'))
                    ->columnSpan([
                        'md' => 5
                    ])
                    ->afterStateHydrated(function (Forms\Set $set, Forms\Get $get, $state) {
                        $product = Product::find($state);
                        $set('unit_price', $product->price ?? 0);
                        $set('stock', $product->stock ?? 0);
                    })
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        $product = Product::find($state);
                        $set('unit_price', $product->price ?? 0);
                        $set('stock', $product->stock ?? 0);
                        $quantity = $get('quantity') ?? 1;
                        $stock = $get('stock');
                        self::updateTotalPrice($get, $set);
                    })
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->columnSpan([
                        'md' => 1
                    ])
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        $stock = $get('stock');
                        if ($state > $stock) {
                            $set('quantity', $stock);
                            Notification::make()
                                ->title('Stok tidak mencukupi')
                                ->warning()
                                ->send();
                        }

                        self::updateTotalPrice($get, $set);
                    }),
                Forms\Components\TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->readOnly()
                    ->columnSpan([
                        'md' => 1
                    ]),
                Forms\Components\TextInput::make('unit_price')
                    ->label('Harga saat ini')
                    ->required()
                    ->numeric()
                    ->readOnly()
                    ->columnSpan([
                        'md' => 3
                    ]),

            ]);
    }

    protected static function updateTotalPrice(Forms\Get $get, Forms\Set $set): void
    {
        $selectedProducts = collect($get('orderProducts'))->filter(fn($item) => !empty($item['product_id']) && !empty($item['quantity']));

        $prices = Product::find($selectedProducts->pluck('product_id'))->pluck('price', 'id');
        $subtotal = $selectedProducts->reduce(function ($total, $product) use ($prices) {
            return $total + ($prices[$product['product_id']] * $product['quantity']);
        }, 0);

        // Hitung diskon
        $discountRate = (int) $get('discount') ?? 0;
        $discountAmount = ($subtotal * $discountRate) / 100;
        $total = $subtotal - $discountAmount;

        $set('total_price', $total);
    }

    protected static function updateExcangePaid(Forms\Get $get, Forms\Set $set): void
    {
        $paidAmount = (int) $get('paid_amount') ?? 0;
        $totalPrice = (int) $get('total_price') ?? 0;
        $exchangePaid = $paidAmount - $totalPrice;
        $set('change_amount', $exchangePaid);
    }
}
