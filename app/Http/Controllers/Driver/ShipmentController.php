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
     * @return View
     */
    public function dashboard(): View
    {
        $driver = auth()->guard('driver')->user();

        // Transaksi yang siap dikirim (status = ready) atau sedang dalam pengiriman
        $pendingDeliveries = Transaction::with(['customer', 'shipments'])
            ->where('status', 'ready')
            ->whereDoesntHave('shipments', function ($query) {
                $query->where('status', 'delivered');
            })
            ->orderBy('estimated_completion_date', 'asc')
            ->get();

        // Pengiriman hari ini yang ditangani driver ini
        $myDeliveries = Shipment::with(['transaction.customer'])
            ->where('assigned_driver_id', $driver->id)
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('driver.dashboard', [
            'pendingDeliveries' => $pendingDeliveries,
            'myDeliveries' => $myDeliveries,
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
            'assigned_driver_id' => $driver->id,
            'shipment_type' => 'delivery',
            'status' => 'picked_up',
            'picked_up_at' => now(),
            'address' => $transaction->customer->address,
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
            ->where('assigned_driver_id', auth()->guard('driver')->id())
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
            ->where('assigned_driver_id', $driver->id)
            ->where('status', '!=', 'delivered')
            ->firstOrFail();

        // DeepDive: Upload ke Cloudinary dengan optimasi
        $proofUrl = $this->cloudinary->uploadDeliveryProof(
            $request->file('proof_photo'),
            $transaction->transaction_code
        );

        // Update shipment
        $shipment->update([
            'status' => 'delivered',
            'delivered_at' => now(),
            'proof_image_url' => $proofUrl,
            'delivery_notes' => $validated['notes'] ?? null,
        ]);

        // DeepState: Auto-update transaction status
        $transaction->update(['status' => 'completed']);

        return redirect()->route('driver.dashboard')
            ->with('success', 'Pengiriman selesai! Bukti foto sudah disimpan.');
    }
}
