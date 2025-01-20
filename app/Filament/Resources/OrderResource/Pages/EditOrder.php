<?php

namespace App\Filament\Resources\OrderResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Log;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\OrderResource;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($record) {
                    // Pastikan record dan orderProducts ada
                    if ($record && $record->orderProducts) {
                        // Kembalikan stok produk sebelum menghapus order
                        foreach ($record->orderProducts as $orderProduct) {
                            $product = $orderProduct->product;
                            if ($product) {
                                // Tambahkan logging untuk memastikan nilai yang diupdate
                                Log::info('Mengembalikan stok produk:', [
                                    'product_id' => $product->id,
                                    'old_stock' => $product->stock,
                                    'returned_quantity' => $orderProduct->quantity,
                                    'new_stock' => $product->stock + $orderProduct->quantity
                                ]);

                                $product->increment('stock', $orderProduct->quantity);
                            }
                        }
                    }
                })
                ->after(function () {
                    // Refresh halaman setelah penghapusan
                    $this->redirect($this->getResource()::getUrl('index'));
                })
        ];
    }
}
