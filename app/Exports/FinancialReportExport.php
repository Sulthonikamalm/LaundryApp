<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\TransactionDetail;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Database\Eloquent\Builder;

/**
 * FinancialReportExport - Laporan Pendapatan per Layanan
 * 
 * DeepReasoning: Menggunakan price_at_transaction untuk akurasi historis.
 * DeepScale: Chunking untuk data besar.
 * DeepSecurity: Hanya Owner yang bisa mengakses.
 */
class FinancialReportExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading, WithStyles, ShouldAutoSize, WithTitle
{

    protected string $startDate;
    protected string $endDate;

    public function __construct(string $startDate, string $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function title(): string
    {
        return 'Laporan Pendapatan';
    }

    /**
     * Query builder.
     * 
     * DeepReasoning: Query berdasarkan TransactionDetail untuk mendapatkan
     * price_at_transaction yang akurat secara historis.
     */
    public function query(): Builder
    {
        return TransactionDetail::query()
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('services', 'transaction_details.service_id', '=', 'services.id')
            ->whereBetween('transactions.order_date', [$this->startDate, $this->endDate])
            ->where('transactions.status', '!=', 'cancelled')
            ->whereNull('transaction_details.deleted_at')
            ->select([
                'transactions.transaction_code',
                'transactions.order_date',
                'services.service_name',
                'services.service_type',
                'transaction_details.quantity',
                'transaction_details.price_at_transaction', // Snapshot price!
                'transaction_details.subtotal',
            ])
            ->orderBy('transactions.order_date', 'desc');
    }

    public function headings(): array
    {
        return [
            'Kode Nota',
            'Tanggal',
            'Layanan',
            'Tipe',
            'Jumlah/Berat',
            'Harga Satuan (Snapshot)',
            'Subtotal',
        ];
    }

    public function map($row): array
    {
        return [
            $row->transaction_code,
            \Carbon\Carbon::parse($row->order_date)->format('d/m/Y'),
            $row->service_name,
            ucfirst($row->service_type),
            $row->quantity,
            $row->price_at_transaction,
            $row->subtotal,
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '059669'], // Green
                ],
            ],
        ];
    }
}
