<?php

declare(strict_types=1);

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\Transaction;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * ShipmentController - Driver Delivery Management
 * 
 * DeepUI: Mobile-first interface untuk kurir.
 * DeepDive: Upload foto bukti ke Cloudinary.
 * DeepState: Auto-update transaction status.
 */
class ShipmentController extends Controller
{
    protected CloudinaryService $cloudinary;

    public function __construct(CloudinaryService $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    /**
     * Driver dashboard - list pending deliveries.
     * 
     * DeepPerformance: Cached query + limit untuk responsiveness.
     * 
     * @return View
     */
    public function dashboard(): View
    {
        $driver = auth()->guard('driver')->user();
        $cacheKey = "driver_dashboard_{$driver->id}";

        // DeepPerformance: Cache dashboard data for 30 seconds
        $data = \Illuminate\Support\Facades\Cache::remember($cacheKey, 30, function () use ($driver) {
            // Transaksi yang siap dikirim (status = ready) tanpa shipment completed
            $pendingDeliveries = Transaction::with(['customer:id,name,address,phone_number'])
                ->select(['id', 'transaction_code', 'customer_id', 'estimated_completion_date'])
                ->where('status', 'ready')
                ->whereDoesntHave('shipments', function ($query) {
                    $query->where('status', 'completed'); // Fixed: was 'delivered'
                })
                ->orderBy('estimated_completion_date', 'asc')
                ->limit(20) // DeepPerformance: Limit to 20 items
                ->get();

            // Pengiriman hari ini yang ditangani driver ini
            $myDeliveries = Shipment::with(['transaction:id,transaction_code,customer_id', 'transaction.customer:id,name,address'])
                ->select(['id', 'transaction_id', 'status', 'customer_address', 'created_at']) // Fixed: customer_address
                ->where('courier_id', $driver->id) // Fixed: was assigned_driver_id
                ->whereDate('created_at', today())
                ->orderBy('created_at', 'desc')
                ->limit(15)
                ->get();

            return compact('pendingDeliveries', 'myDeliveries');
        });

        return view('driver.dashboard', [
            'pendingDeliveries' => $data['pendingDeliveries'],
            'myDeliveries' => $data['myDeliveries'],
            'driver' => $driver,
        ]);
    }

    /**
     * Start delivery - assign driver to transaction.
     * 
     * @param Transaction $transaction
     * @return RedirectResponse
     */
    public function startDelivery(Transaction $transaction): RedirectResponse
    {
        $driver = auth()->guard('driver')->user();

        // Check if shipment already exists
        $existingShipment = Shipment::where('transaction_id', $transaction->id)
            ->where('status', '!=', 'cancelled')
            ->first();

        if ($existingShipment) {
            return back()->with('error', 'Pengiriman sudah diproses.');
        }

        // Create shipment record
        Shipment::create([
            'transaction_id' => $transaction->id,
            'courier_id' => $driver->id, // Fixed: was assigned_driver_id
            'shipment_type' => 'delivery',
            'status' => 'in_progress', // Fixed: actual enum value
            'scheduled_at' => now(),
            'customer_address' => $transaction->customer->address, // Fixed: was address
        ]);

        return redirect()->route('driver.delivery.show', $transaction)
            ->with('success', 'Pengiriman dimulai!');
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
            ->where('courier_id', auth()->guard('driver')->id()) // Fixed: was assigned_driver_id
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

        $shipment = Shipment::where('transaction_id', $transaction->id)
            ->where('courier_id', $driver->id) // Fixed: was assigned_driver_id
            ->where('status', '!=', 'completed') // Fixed: was 'delivered'
            ->firstOrFail();

        // DeepDive: Upload ke Cloudinary dengan optimasi
        $proofUrl = $this->cloudinary->uploadDeliveryProof(
            $request->file('proof_photo'),
            $transaction->transaction_code
        );

        // Update shipment
        $shipment->update([
            'status' => 'completed', // Fixed: was 'delivered'
            'completed_at' => now(), // Fixed: was 'delivered_at'
            'photo_proof_url' => $proofUrl, // Fixed: was 'proof_image_url'
            'notes' => $validated['notes'] ?? null, // Fixed: was 'delivery_notes'
        ]);

        // DeepState: Auto-update transaction status
        $transaction->update(['status' => 'completed']);

        return redirect()->route('driver.dashboard')
            ->with('success', 'Pengiriman selesai! Bukti foto sudah disimpan.');
    }
}
