<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Database\Eloquent\Builder;

/**
 * TransactionsExport - Export Transaksi ke Excel
 * 
 * DeepScale: Menggunakan chunking untuk efisiensi memori.
 * DeepSecurity: Data sensitif (HP) di-mask untuk privasi.
 * DeepSecrethacking: Sanitasi data untuk mencegah CSV injection.
 */
class TransactionsExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading, WithStyles, ShouldAutoSize
{

    protected ?string $startDate = null;
    protected ?string $endDate = null;
    protected ?string $status = null;
    protected ?string $paymentStatus = null;

    /**
     * Set date range filter.
     */
    public function forDateRange(?string $startDate, ?string $endDate): self
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * Set status filter.
     */
    public function withStatus(?string $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Set payment status filter.
     */
    public function withPaymentStatus(?string $paymentStatus): self
    {
        $this->paymentStatus = $paymentStatus;
        return $this;
    }

    /**
     * Query builder for export.
     * 
     * DeepPerformance: Menggunakan query builder dengan eager loading.
     * 
     * @return Builder
     */
    public function query(): Builder
    {
        $query = Transaction::query()
            ->with(['customer', 'creator', 'details.service'])
            ->orderBy('order_date', 'desc');

        // Apply filters
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('order_date', [$this->startDate, $this->endDate]);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->paymentStatus) {
            $query->where('payment_status', $this->paymentStatus);
        }

        return $query;
    }

    /**
     * Column headings.
     * 
     * @return array
     */
    public function headings(): array
    {
        return [
            'Kode Nota',
            'Tanggal Order',
            'Pelanggan',
            'Telepon',
            'Layanan',
            'Total Tagihan',
            'Total Dibayar',
            'Sisa Tagihan',
            'Status Order',
            'Status Pembayaran',
            'Kasir',
            'Estimasi Selesai',
        ];
    }

    /**
     * Map transaction to row.
     * 
     * DeepSecurity: Sanitasi dan masking data sensitif.
     * DeepReasoning: Menggunakan price_at_transaction untuk total yang akurat.
     * 
     * @param Transaction $transaction
     * @return array
     */
    public function map($transaction): array
    {
        // DeepSecrethacking: Sanitasi untuk mencegah CSV/Excel injection
        $customerName = $this->sanitize($transaction->customer?->name ?? 'Unknown');
        
        // DeepSecurity: Masking nomor HP (08123***456)
        $phone = $this->maskPhone($transaction->customer?->phone_number ?? '');

        // Gabungkan layanan dari detail
        $services = $transaction->details->map(function ($detail) {
            return $detail->service?->service_name . ' (' . $detail->quantity . ')';
        })->implode(', ');

        // DeepReasoning: Sisa tagihan dihitung dari data real
        $remaining = $transaction->total_cost - $transaction->total_paid;

        // Status translation
        $statusLabels = [
            'pending' => 'Pending',
            'processing' => 'Proses',
            'ready' => 'Siap Diambil',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        $paymentLabels = [
            'unpaid' => 'Belum Bayar',
            'partial' => 'DP/Sebagian',
            'paid' => 'Lunas',
        ];

        return [
            $transaction->transaction_code,
            $transaction->order_date?->format('d/m/Y'),
            $customerName,
            $phone,
            $services,
            $transaction->total_cost,
            $transaction->total_paid,
            $remaining,
            $statusLabels[$transaction->status] ?? $transaction->status,
            $paymentLabels[$transaction->payment_status] ?? $transaction->payment_status,
            $transaction->creator?->name ?? 'System',
            $transaction->estimated_completion_date?->format('d/m/Y'),
        ];
    }

    /**
     * Chunk size for memory efficiency.
     * 
     * DeepScale: Memproses 500 baris sekaligus untuk efisiensi.
     * 
     * @return int
     */
    public function chunkSize(): int
    {
        return 500;
    }

    /**
     * Excel styling.
     * 
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            // Header row styling
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'], // Indigo
                ],
            ],
        ];
    }

    /**
     * Sanitize string to prevent CSV/Excel injection.
     * 
     * DeepSecrethacking: Mencegah formula injection seperti =CMD(), @SUM(), dll.
     * 
     * @param string $value
     * @return string
     */
    protected function sanitize(string $value): string
    {
        $dangerousChars = ['=', '+', '-', '@', "\t", "\r", "\n"];
        
        foreach ($dangerousChars as $char) {
            if (str_starts_with($value, $char)) {
                return "'" . $value; // Prefix dengan quote
            }
        }

        return $value;
    }

    /**
     * Mask phone number for privacy.
     * 
     * DeepSecurity: Melindungi PII pelanggan.
     * 08123456789 -> 08123***789
     * 
     * @param string $phone
     * @return string
     */
    protected function maskPhone(string $phone): string
    {
        if (strlen($phone) < 8) {
            return $phone;
        }

        $visible = 5; // First 5 chars
        $tail = 3; // Last 3 chars
        $masked = str_repeat('*', max(0, strlen($phone) - $visible - $tail));

        return substr($phone, 0, $visible) . $masked . substr($phone, -$tail);
    }
}
