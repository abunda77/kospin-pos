<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit="loadData" class="space-y-6">
            {{ $this->form }}
        </form>

        <div class="p-6 bg-white rounded-lg shadow">
            <div class="mb-6">
                <h3 class="text-2xl font-semibold text-gray-900">Ringkasan Penjualan</h3>
                <p class="text-gray-500">Periode: {{ Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
            </div>

            <div class="mb-8 p-6 bg-primary-50 rounded-lg">
                <div class="text-lg text-gray-600">Total Penjualan:</div>
                <div class="text-3xl font-bold text-primary-600">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</div>
            </div>

            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3">Tanggal</th>
                            <th scope="col" class="px-6 py-3">Nama Pelanggan</th>
                            <th scope="col" class="px-6 py-3">Metode Pembayaran</th>
                            <th scope="col" class="px-6 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    {{ $order->created_at->format('d M Y H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $order->name ?? 'Umum' }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $order->paymentMethod->name }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr class="bg-white border-b">
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                    Tidak ada data penjualan untuk periode ini
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="font-semibold text-gray-900">
                            <td colspan="3" class="px-6 py-4 text-right">Total</td>
                            <td class="px-6 py-4 text-right">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
