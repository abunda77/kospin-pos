<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Support\Enums\IconPosition;
use App\Models\Product;
use App\Models\Order;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Number;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $startDate = ! is_null($this->filters['startDate'] ?? null) ?
        Carbon::parse($this->filters['startDate']) :
        null;

        $endDate = ! is_null($this->filters['endDate'] ?? null) ?
        Carbon::parse($this->filters['endDate']) :
        now();

        // Mengambil data dengan filter tanggal yang sama
        $dataPriceOrder = Order::whereBetween('created_at', [$startDate, $endDate])->pluck('total_price')->toArray();
        $dataPriceExpense = Expense::whereBetween('created_at', [$startDate, $endDate])->pluck('amount')->toArray();

        $product_count = Product::count();
        $order_count = Order::count();
        $omset = Order::whereBetween('created_at', [$startDate, $endDate])->sum('total_price');
        $expense = Expense::whereBetween('created_at', [$startDate, $endDate])->sum('amount');

        // Hitung data untuk grafik laba bersih
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy(function($item) {
                return $item->created_at->format('Y-m-d');
            });

        $expenses = Expense::whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy(function($item) {
                return $item->created_at->format('Y-m-d');
            });

        $profitData = [];
        $dates = collect([...$orders->keys(), ...$expenses->keys()])->unique();

        foreach ($dates as $date) {
            $dayOrders = $orders->get($date, collect())->sum('total_price');
            $dayExpenses = $expenses->get($date, collect())->sum('amount');
            $profitData[] = $dayOrders - $dayExpenses;
        }

        return [
            Stat::make('Order', $order_count),

            Stat::make('Pemasukan', 'Rp ' . number_format($omset,0,",","."))
                ->description('omset')
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                ->chart($dataPriceOrder)
                ->color('success'),

            Stat::make('Pengeluaran', 'Rp ' . number_format($expense,0,",","."))
                ->description('expense')
                ->descriptionIcon('heroicon-m-arrow-trending-down', IconPosition::Before)
                ->chart($dataPriceExpense)
                ->color('danger'),

            Stat::make('Laba Bersih', 'Rp ' . number_format($omset - $expense,0,",","."))
                ->description('Keuntungan bersih')
                ->chart($profitData)
                ->color('success'),
        ];
    }
}
