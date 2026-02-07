<x-filament::page>
    {{-- Filter Form --}}
    <form wire:submit.prevent="export">
        {{ $this->form }}

        {{-- Preview Stats --}}
        @php
            $stats = $this->getPreviewStats();
        @endphp
        
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <x-filament::card>
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Transaksi</div>
                <div class="text-2xl font-bold text-primary-600">{{ number_format($stats['total_transactions']) }}</div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Pendapatan</div>
                <div class="text-2xl font-bold text-success-600">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Dibayar</div>
                <div class="text-2xl font-bold text-info-600">Rp {{ number_format($stats['total_paid'], 0, ',', '.') }}</div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Piutang</div>
                <div class="text-2xl font-bold {{ $stats['outstanding'] > 0 ? 'text-danger-600' : 'text-success-600' }}">
                    Rp {{ number_format($stats['outstanding'], 0, ',', '.') }}
                </div>
            </x-filament::card>
        </div>

        {{-- Export Button --}}
        <div class="mt-6 flex justify-end gap-4">
            <x-filament::button type="submit" color="success" icon="heroicon-o-download">
                Export ke Excel
            </x-filament::button>
        </div>
    </form>

    {{-- Info Box --}}
    <div class="mt-8 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
        <div class="flex items-start gap-3">
            <x-heroicon-o-information-circle class="w-6 h-6 text-blue-500 flex-shrink-0" />
            <div class="text-sm text-blue-700 dark:text-blue-300">
                <p class="font-semibold">Informasi Laporan</p>
                <ul class="mt-2 list-disc list-inside space-y-1">
                    <li><strong>Laporan Transaksi:</strong> Berisi daftar semua transaksi dengan detail pelanggan dan status.</li>
                    <li><strong>Laporan Keuangan:</strong> Berisi detail pendapatan per layanan menggunakan <em>harga snapshot</em> saat transaksi dibuat (bukan harga saat ini).</li>
                    <li>Nomor telepon pelanggan telah di-<em>masking</em> untuk melindungi privasi.</li>
                </ul>
            </div>
        </div>
    </div>
</x-filament::page>
