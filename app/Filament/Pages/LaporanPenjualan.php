<?php

namespace App\Filament\Pages;

use App\Models\Order;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanPenjualan extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Laporan Penjualan';
    protected static ?string $pluralLabel = 'Laporan Penjualan';
    protected static ?string $title = 'Laporan Penjualan';
    protected static ?string $description = 'Laporan Penjualan';
    protected static ?string $navigationGroup = 'Menejemen keuangan';

    protected static string $view = 'filament.pages.laporan-penjualan';

    public ?array $data = [];
    public $startDate;
    public $endDate;
    public $orders;
    public $totalPenjualan = 0;

    public function mount()
    {
        $this->startDate = now()->startOfMonth();
        $this->endDate = now()->endOfMonth();
        $this->loadData();
    }

    protected function getFormSchema(): array
    {
        return [
            DatePicker::make('startDate')
                ->label('Tanggal Mulai')
                ->required()
                ->default(now()->startOfMonth())
                ->reactive()
                ->afterStateUpdated(fn () => $this->loadData()),

            DatePicker::make('endDate')
                ->label('Tanggal Akhir')
                ->required()
                ->default(now()->endOfMonth())
                ->reactive()
                ->afterStateUpdated(fn () => $this->loadData()),
        ];
    }

    public function loadData()
    {
        $this->orders = Order::query()
            ->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ])
            ->get();

        $this->totalPenjualan = $this->orders->sum('total_price');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Cetak PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    $pdf = Pdf::loadView('pdf.laporan-penjualan', [
                        'startDate' => Carbon::parse($this->startDate)->format('d M Y'),
                        'endDate' => Carbon::parse($this->endDate)->format('d M Y'),
                        'orders' => $this->orders,
                        'totalPenjualan' => $this->totalPenjualan,
                    ])->setPaper('a4');

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, 'laporan-penjualan.pdf');
                })
                ->color('success'),
        ];
    }
}
