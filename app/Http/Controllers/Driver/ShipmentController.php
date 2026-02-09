<?php

declare(strict_types=1);

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\Transaction;
use App\Models\TransactionStatusLog;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;

/**
 * ShipmentController - Driver Delivery Management
 * 
 * DeepUI: Mobile-first interface untuk kurir.
 * DeepDive: Upload foto bukti ke Cloudinary.
 * DeepState: Auto-update transaction status + Audit Trail.
 */
class ShipmentController extends Controller
{
    protected CloudinaryService $cloudinary;

    public function __construct(CloudinaryService $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    /**
     * Driver dashboard - list assigned deliveries.
     * 
     * DeepPerformance: Cached query + limit untuk responsiveness.
     * DeepLogic: Hanya tampilkan tugas yang ditugaskan ke driver ini.
     * 
     * @return View
     */
    public function dashboard(): View
    {
        $driver = auth()->guard('driver')->user();
        $cacheKey = "driver_dashboard_{$driver->id}";

        // DeepPerformance: Cache dashboard data for 30 seconds
        $data = Cache::remember($cacheKey, 30, function () use ($driver) {
            // DeepFilter: Tugas yang ditugaskan ke driver ini
            // Status: pending (baru ditugaskan) atau picked_up (sedang diantar)
            $myTasks = Shipment::with([
                'transaction:id,transaction_code,customer_id,total_cost,delivery_address',
                'transaction.customer:id,name,address,phone_number'
            ])
                ->where('courier_id', $driver->id)
                ->whereIn('status', ['pending', 'picked_up'])
                ->orderBy('assigned_at', 'asc')
                ->limit(20)
                ->get();

            // History: Tugas yang sudah selesai hari ini
            $completedToday = Shipment::with([
                'transaction:id,transaction_code,customer_id',
                'transaction.customer:id,name'
            ])
                ->where('courier_id', $driver->id)
                ->where('status', 'delivered')
                ->whereDate('completed_at', today())
                ->orderBy('completed_at', 'desc')
                ->limit(10)
                ->get();

            return compact('myTasks', 'completedToday');
        });

        return view('driver.dashboard', [
            'myTasks' => $data['myTasks'],
            'completedToday' => $data['completedToday'],
            'driver' => $driver,
        ]);
    }

    /**
     * Start delivery - mark shipment as picked up.
     * 
     * DeepLogic: Driver mengambil barang dari laundry dan mulai perjalanan.
     * 
     * @param Transaction $transaction
     * @return RedirectResponse
     */
    public function startDelivery(Transaction $transaction): RedirectResponse
    {
        $driver = auth()->guard('driver')->user();

        // DeepSecurity: Hanya shipment yang ditugaskan ke driver ini
        $shipment = Shipment::where('transaction_id', $transaction->id)
            ->where('courier_id', $driver->id)
            ->where('status', 'pending')
            ->firstOrFail();

        // Update status ke picked_up (barang sudah diambil, dalam perjalanan)
        $shipment->update([
            'status' => 'picked_up',
        ]);

        // Clear cache
        Cache::forget("driver_dashboard_{$driver->id}");

        return redirect()->route('driver.delivery.show', $transaction)
            ->with('success', 'Pengiriman dimulai! Hati-hati di jalan.');
    }

    /**
     * Show delivery detail page.
     * 
     * @param Transaction $transaction
     * @return View
     */
    public function show(Transaction $transaction): View
    {
        $transaction->load(['customer', 'details.service', 'shipments']);
        
        $shipment = $transaction->shipments()
            ->where('courier_id', auth()->guard('driver')->id())
            ->latest()
            ->first();

        return view('driver.delivery-detail', [
            'transaction' => $transaction,
            'shipment' => $shipment,
        ]);
    }

    /**
     * Complete delivery with photo proof.
     * 
     * DeepDive: Upload foto ke Cloudinary.
     * DeepState: Auto-update transaction status ke 'completed'.
     * DeepLog: Mencatat history perubahan status untuk audit.
     * 
     * @param Request $request
     * @param Transaction $transaction
     * @return RedirectResponse
     */
    public function complete(Request $request, Transaction $transaction): RedirectResponse
    {
        $validated = $request->validate([
            'proof_photo' => 'required|image|max:5120', // Max 5MB
            'notes' => 'nullable|string|max:500',
        ]);

        $driver = auth()->guard('driver')->user();

        // DeepSecurity: Hanya shipment yang ditugaskan ke driver ini
        $shipment = Shipment::where('transaction_id', $transaction->id)
            ->where('courier_id', $driver->id)
            ->where('status', 'picked_up')
            ->firstOrFail();

        // DeepDive: Upload ke Cloudinary dengan optimasi
        $proofUrl = $this->cloudinary->uploadDeliveryProof(
            $request->file('proof_photo'),
            $transaction->transaction_code
        );

        // Update shipment
        $shipment->update([
            'status' => 'delivered',
            'completed_at' => now(),
            'photo_proof_url' => $proofUrl,
            'notes' => $validated['notes'] ?? null,
        ]);

        // DeepState: Auto-update transaction status & Log History
        $oldStatus = $transaction->status;
        $transaction->update(['status' => 'completed']);

        // Create Audit Log
        TransactionStatusLog::create([
            'transaction_id' => $transaction->id,
            'changed_by' => $driver->id,
            'previous_status' => $oldStatus,
            'new_status' => 'completed',
            'notes' => 'Pengiriman selesai oleh kurir: ' . ($validated['notes'] ?? '-'),
        ]);

        // Clear cache
        Cache::forget("driver_dashboard_{$driver->id}");

        return redirect()->route('driver.dashboard')
            ->with('success', 'Pengiriman selesai! Bukti foto sudah disimpan.');
    }
}
