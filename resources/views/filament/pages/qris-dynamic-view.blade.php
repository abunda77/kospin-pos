<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Merchant Name</p>
            <p class="text-base font-semibold">{{ $record->merchant_name }}</p>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Amount</p>
            <p class="text-base font-semibold">Rp {{ number_format($record->amount, 0, ',', '.') }}</p>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Fee Type</p>
            <p class="text-base font-semibold">{{ $record->fee_type }}</p>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Fee Value</p>
            <p class="text-base font-semibold">
                @if($record->fee_type === 'Persentase')
                    {{ $record->fee_value }}%
                @else
                    Rp {{ number_format($record->fee_value, 0, ',', '.') }}
                @endif
            </p>
        </div>
        @if($record->qrisStatic)
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Source QRIS</p>
            <p class="text-base font-semibold">{{ $record->qrisStatic->name }}</p>
        </div>
        @endif
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Created By</p>
            <p class="text-base font-semibold">{{ $record->creator?->name ?? '-' }}</p>
        </div>
        <div class="col-span-2">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Generated At</p>
            <p class="text-base font-semibold">{{ $record->created_at->format('d M Y H:i:s') }}</p>
        </div>
    </div>

    @if($record->qr_image_path && Storage::disk('public')->exists($record->qr_image_path))
    <div class="flex flex-col items-center space-y-2 pt-4 border-t">
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">QR Code</p>
        <img src="{{ Storage::disk('public')->url($record->qr_image_path) }}" 
             alt="QRIS QR Code" 
             class="w-64 h-64 border rounded-lg shadow-sm">
    </div>
    @endif

    <div class="pt-4 border-t">
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">QRIS String</p>
        <div class="bg-gray-100 dark:bg-gray-800 p-3 rounded-lg">
            <code class="text-xs break-all">{{ $record->qris_string }}</code>
        </div>
    </div>
</div>
