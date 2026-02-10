<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Payment;
use App\Services\Payment\PaymentGatewayFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * PaymentController - Handle public payment flow
 * 
 * DeepArchitecture: Gateway-agnostic payment handling
 * DeepSecurity: Validate transaction ownership via dual-key
 */
class PaymentController extends Controller
{
    /**
     * Initiate payment (generate QR or Snap token)
     * 
     * @param Request $request
     * @param Transaction $transaction
     * @return \Illuminate\Http\JsonResponse
     */
    public function initiate(Request $request, Transaction $transaction)
    {
        // DeepSecurity: Validate remaining balance
        $remainingBalance = $transaction->getRemainingBalance();
        
        if ($remainingBalance <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi sudah lunas'
            ], 400);
        }

        try {
            $gateway = PaymentGatewayFactory::make();
            $paymentData = $gateway->createPayment($transaction);
            
            return response()->json([
                'success' => true,
                'gateway' => $gateway->getProviderName(),
                'data' => $paymentData,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Payment Initiation Error: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id,
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memulai pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirm payment (for demo mode)
     * 
     * DeepLogic: Create pending payment record, wait for admin approval
     * 
     * @param Request $request
     * @param Transaction $transaction
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirm(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'payment_id' => 'required|string',
        ]);

        // DeepSecurity: Check if already paid
        if ($transaction->getRemainingBalance() <= 0) {
            return redirect()->route('public.payment.success', $transaction->id)
                ->with('message', 'Transaksi sudah lunas');
        }

        DB::beginTransaction();
        try {
            $gateway = PaymentGatewayFactory::make();
            
            // Create payment record
            $payment = Payment::create([
                'transaction_id' => $transaction->id,
                'amount' => $transaction->getRemainingBalance(),
                'payment_method' => 'qris',
                'gateway_provider' => $gateway->getProviderName(),
                'status' => 'pending', // Will be updated by Payment model observer
                'gateway_status' => 'pending', // Waiting admin approval
                'transaction_reference' => $validated['payment_id'],
                'payment_date' => now(),
                'notes' => 'Pembayaran via ' . strtoupper($gateway->getProviderName()) . ' - Menunggu verifikasi admin',
            ]);

            DB::commit();

            // DeepUX: Redirect to success page with contextual message
            return redirect()->route('public.payment.success', $transaction->id)
                ->with('payment_id', $payment->id);
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment Confirmation Error: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id,
            ]);
            
            return back()->with('error', 'Gagal mengkonfirmasi pembayaran. Silakan coba lagi.');
        }
    }

    /**
     * Payment success page with contextual messaging
     * 
     * DeepUX: Dynamic message based on transaction state
     * 
     * @param Transaction $transaction
     * @return \Illuminate\View\View
     */
    public function success(Transaction $transaction)
    {
        // Reload transaction to get latest status
        $transaction->refresh();
        $transaction->load(['customer', 'payments' => function($q) {
            $q->latest();
        }]);

        // DeepLogic: Contextual messaging based on state
        $message = $this->getContextualMessage($transaction);

        return view('public.payment-success', [
            'transaction' => $transaction,
            'message' => $message,
        ]);
    }

    /**
     * Get contextual message based on transaction state
     * 
     * DeepUX: Smart messaging untuk guide user next action
     * 
     * @param Transaction $transaction
     * @return array
     */
    protected function getContextualMessage(Transaction $transaction): array
    {
        $latestPayment = $transaction->payments()->latest()->first();
        $isDemo = $latestPayment && $latestPayment->gateway_provider === 'demo';

        // Case 1: Demo payment pending approval
        if ($isDemo && $latestPayment->gateway_status === 'pending') {
            return [
                'title' => 'Pembayaran Sedang Diverifikasi',
                'subtitle' => 'Mohon tunggu sebentar',
                'body' => 'Pembayaran Anda sedang diverifikasi oleh tim kami. Proses ini biasanya memakan waktu 5-10 menit. Kami akan mengirimkan notifikasi WhatsApp setelah pembayaran dikonfirmasi.',
                'icon' => 'clock',
                'color' => 'warning',
            ];
        }

        // Case 2: Payment approved/completed
        if ($transaction->payment_status === 'paid') {
            // Sub-case: Cucian masih dalam proses
            if (in_array($transaction->status, ['pending', 'processing'])) {
                return [
                    'title' => 'Pembayaran Berhasil!',
                    'subtitle' => 'Cucian Anda sedang diproses',
                    'body' => 'Terima kasih atas pembayarannya. Cucian Anda sedang dalam proses pencucian dan akan selesai pada ' . $transaction->estimated_completion_date->format('d M Y') . '. Kami akan mengirimkan notifikasi saat cucian sudah siap.',
                    'icon' => 'check',
                    'color' => 'success',
                ];
            }

            // Sub-case: Cucian ready, no delivery
            if ($transaction->status === 'ready' && !$transaction->is_delivery) {
                return [
                    'title' => 'Pembayaran Berhasil!',
                    'subtitle' => 'Cucian siap diambil',
                    'body' => 'Cucian Anda sudah bersih, wangi, dan siap diambil di toko kami. Silakan datang ke ' . config('app.address') . ' untuk mengambil cucian Anda.',
                    'icon' => 'check',
                    'color' => 'success',
                ];
            }

            // Sub-case: Cucian ready, with delivery
            if ($transaction->status === 'ready' && $transaction->is_delivery) {
                return [
                    'title' => 'Pembayaran Berhasil!',
                    'subtitle' => 'Cucian siap diantar',
                    'body' => 'Cucian Anda sudah siap dan akan segera diantar oleh kurir kami. Kurir akan menghubungi Anda sebelum pengiriman. Terima kasih!',
                    'icon' => 'truck',
                    'color' => 'success',
                ];
            }

            // Sub-case: Completed
            if ($transaction->status === 'completed') {
                return [
                    'title' => 'Transaksi Selesai',
                    'subtitle' => 'Terima kasih!',
                    'body' => 'Transaksi Anda telah selesai. Terima kasih telah mempercayakan laundry Anda kepada kami. Sampai jumpa lagi!',
                    'icon' => 'check',
                    'color' => 'success',
                ];
            }
        }

        // Case 3: Partial payment
        if ($transaction->payment_status === 'partial') {
            $remaining = $transaction->getRemainingBalance();
            
            // Sub-case: Ready for pickup/delivery, need final payment
            if ($transaction->status === 'ready') {
                if ($transaction->is_delivery) {
                    return [
                        'title' => 'Pembayaran DP Berhasil',
                        'subtitle' => 'Sisa pembayaran saat kurir tiba',
                        'body' => "Terima kasih atas pembayaran DP. Cucian Anda sudah siap dan akan diantar oleh kurir. Sisa pembayaran sebesar Rp " . number_format($remaining, 0, ',', '.') . " dapat dibayarkan saat kurir tiba di lokasi Anda.",
                        'icon' => 'truck',
                        'color' => 'info',
                    ];
                } else {
                    return [
                        'title' => 'Pembayaran DP Berhasil',
                        'subtitle' => 'Sisa pembayaran saat pengambilan',
                        'body' => "Terima kasih atas pembayaran DP. Cucian Anda sudah siap diambil. Sisa pembayaran sebesar Rp " . number_format($remaining, 0, ',', '.') . " dapat dibayarkan saat Anda mengambil cucian di toko kami.",
                        'icon' => 'info',
                        'color' => 'info',
                    ];
                }
            }

            // Sub-case: Still processing
            return [
                'title' => 'Pembayaran DP Berhasil',
                'subtitle' => 'Cucian sedang diproses',
                'body' => "Terima kasih atas pembayaran DP. Cucian Anda sedang dalam proses dan akan selesai pada " . $transaction->estimated_completion_date->format('d M Y') . ". Sisa pembayaran sebesar Rp " . number_format($remaining, 0, ',', '.') . " dapat dibayarkan saat pengambilan.",
                'icon' => 'check',
                'color' => 'success',
            ];
        }

        // Default fallback
        return [
            'title' => 'Pembayaran Diterima',
            'subtitle' => 'Terima kasih',
            'body' => 'Pembayaran Anda telah kami terima. Silakan cek status cucian Anda secara berkala.',
            'icon' => 'check',
            'color' => 'success',
        ];
    }
}
