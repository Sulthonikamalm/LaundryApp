<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Exports\TransactionsExport;
use App\Exports\FinancialReportExport;
use Filament\Forms;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * ReportsPage - Halaman Laporan & Export
 * 
 * DeepSecurity: Hanya Owner yang bisa mengakses halaman ini.
 * DeepUI: Form filter yang intuitif untuk menghasilkan laporan.
 * DeepSecrethacking: Validasi akses sebelum export.
 */
class ReportsPage extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-report';

    protected static ?string $navigationLabel = 'Laporan';

    protected static ?string $title = 'Laporan & Export';

    protected static ?string $navigationGroup = 'Analitik';

    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.pages.reports-page';

    // Form State
    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?string $status = null;
    public ?string $paymentStatus = null;
    public ?string $reportType = 'transactions';

    /**
     * DeepSecurity: Check authorization before rendering.
     */
    public static function canAccess(): bool
    {
        return auth()->user()?->isOwner() ?? false;
    }

    /**
     * Mount with default values.
     */
    public function mount(): void
    {
        // Default: Current month
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    /**
     * Form schema for filters.
     */
    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Filter Laporan')
                ->schema([
                    Forms\Components\Grid::make(4)
                        ->schema([
                            Forms\Components\DatePicker::make('startDate')
                                ->label('Dari Tanggal')
                                ->required(),

                            Forms\Components\DatePicker::make('endDate')
                                ->label('Sampai Tanggal')
                                ->required(),

                            Forms\Components\Select::make('status')
                                ->label('Status Order')
                                ->options([
                                    '' => 'Semua Status',
                                    'pending' => 'Pending',
                                    'processing' => 'Proses',
                                    'ready' => 'Siap Diambil',
                                    'completed' => 'Selesai',
                                    'cancelled' => 'Dibatalkan',
                                ])
                                ->placeholder('Semua Status'),

                            Forms\Components\Select::make('paymentStatus')
                                ->label('Status Pembayaran')
                                ->options([
                                    '' => 'Semua',
                                    'unpaid' => 'Belum Bayar',
                                    'partial' => 'DP/Sebagian',
                                    'paid' => 'Lunas',
                                ])
                                ->placeholder('Semua'),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('reportType')
                                ->label('Jenis Laporan')
                                ->options([
                                    'transactions' => 'Laporan Transaksi',
                                    'financial' => 'Laporan Keuangan (Revenue per Layanan)',
                                ])
                                ->required()
                                ->default('transactions'),
                        ]),
                ]),
        ];
    }

    /**
     * Export action.
     * 
     * DeepSecurity: Double-check authorization.
     * 
     * @return BinaryFileResponse
     */
    public function export(): BinaryFileResponse
    {
        // DeepSecrethacking: Ownership re-check
        if (!auth()->user()?->isOwner()) {
            abort(403, 'Unauthorized');
        }

        $filename = $this->reportType === 'financial' 
            ? 'laporan_keuangan_' . now()->format('Y-m-d_His') . '.xlsx'
            : 'laporan_transaksi_' . now()->format('Y-m-d_His') . '.xlsx';

        if ($this->reportType === 'financial') {
            return Excel::download(
                new FinancialReportExport($this->startDate, $this->endDate),
                $filename
            );
        }

        $export = (new TransactionsExport())
            ->forDateRange($this->startDate, $this->endDate)
            ->withStatus($this->status ?: null)
            ->withPaymentStatus($this->paymentStatus ?: null);

        return Excel::download($export, $filename);
    }

    /**
     * Preview stats before export.
     */
    public function getPreviewStats(): array
    {
        $query = \App\Models\Transaction::query()
            ->whereBetween('order_date', [$this->startDate, $this->endDate]);

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->paymentStatus) {
            $query->where('payment_status', $this->paymentStatus);
        }

        return [
            'total_transactions' => $query->count(),
            'total_revenue' => $query->sum('total_cost'),
            'total_paid' => $query->sum('total_paid'),
            'outstanding' => $query->sum('total_cost') - $query->sum('total_paid'),
        ];
    }
}
