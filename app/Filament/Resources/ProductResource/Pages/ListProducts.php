<?php

namespace App\Filament\Resources\ProductResource\Pages;

use Filament\Actions;
use App\Models\Product;
use Filament\Actions\Action;
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Resources\Components\Tab;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProductResource;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('printEmptyStock')
                ->label('Produk Kosong')
                ->icon('heroicon-s-printer')
                ->color('warning')
                ->action(function () {
                    $products = Product::where('stock', 0)->get();
                    
                    $pdf = Pdf::loadView('pdf.empty-stock-products', [
                        'products' => $products,
                        'date' => now()->format('d/m/Y H:i:s')
                    ]);
    
                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, 'empty-stock-products.pdf');
                }),
            Action::make('importProducts')
                    ->label('Import Product')
                    ->icon('heroicon-s-arrow-down-tray')
                    ->color('danger')
                    ->form([
                        FileUpload::make('attachment')
                            ->label('Upload Template Product')
                    ])
                    ->action(function (array $data) {
                        $file = public_path('storage/'. $data['attachment']);

                        try {
                            Excel::import(new ProductImport, $file);
                            Notification::make()
                                ->title('Product imported')
                                ->success()
                                ->send();
                        } catch(\Exception $e) {
                            dd($e);
                            Notification::make()
                                    ->title('Product failed to import')
                                    ->danger()
                                    ->send();
                        }
                    }),
            Action::make("Download Template")
                ->url(route('download-template'))
                ->color('success'),
            Actions\CreateAction::make(),
        ];
    }

    protected function setFlashMessage()
    {
        $error = Session::get('error');

        if ($error) {
            $this->notify($error, 'danger');
            Session::forget('error');
        }
    }

    public function getTabs(): array
    {
        return [
        'all' => Tab::make(),
        'Stock Banyak' => Tab::make()
        ->modifyQueryUsing(fn (Builder $query) => $query->where('stock', '>', 10))
        ->badge(Product::query()->where('stock', '>', 10)->count())
        ->badgeColor('success'),
        'Stock Sedikit' => Tab::make()
            ->modifyQueryUsing(fn (Builder $query) => $query->where( 'stock', '<', 10 ,)->where('stock', '>', 0))
            ->badge(Product::query()->where('stock', '<', 10)->where('stock', '>', 0)->count())
            ->badgeColor('warning'),
        'Stock Habis' => Tab::make()
            ->modifyQueryUsing(fn (Builder $query) => $query->where('stock', '=', 0))
            ->badge(Product::query()->where('stock', '<=', 0)->count())
            ->badgeColor('danger'),
        ];
    }
}