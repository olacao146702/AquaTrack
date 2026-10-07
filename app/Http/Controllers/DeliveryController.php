<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DeliveryRequest;
use App\Models\Product;
use App\Models\User;
use App\Services\DeliveryService;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        $q = DeliveryRequest::with('customer', 'product', 'rider')->latest();

        // Riders only ever see their own assignments.
        if ($request->user()->role === 'delivery') {
            $q->where('assigned_to', $request->user()->id);
        }
        if ($request->filled('status')) {
            $q->where('status', $request->string('status'));
        }

        return view('deliveries.index', [
            'deliveries' => $q->paginate(20)->withQueryString(),
            'riders'     => User::where('role', 'delivery')->where('status', 'Active')->get(),
            'customers'  => Customer::orderBy('customer_name')->get(),
            'products'   => Product::where('is_active', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id'      => 'required|exists:customers,id',
            'product_id'       => 'required|exists:products,id',
            'quantity'         => 'required|integer|min:1|max:500',
            'delivery_address' => 'required|string|max:255',
            'preferred_date'   => 'required|date|after_or_equal:today',
            'preferred_time'   => 'nullable|date_format:H:i',
            'remarks'          => 'nullable|string|max:255',
        ]);
        DeliveryRequest::create($data);

        return back()->with('ok', 'Delivery request saved.');
    }

    public function assign(Request $request, DeliveryRequest $delivery, DeliveryService $service)
    {
        $rider = User::findOrFail($request->validate(['assigned_to' => 'required|integer'])['assigned_to']);
        $service->assign($delivery, $rider);

        return back()->with('ok', "Assigned to {$rider->full_name}.");
    }

    public function status(Request $request, DeliveryRequest $delivery, DeliveryService $service)
    {
        $user = $request->user();
        abort_if($user->role === 'delivery' && $delivery->assigned_to !== $user->id, 403);

        $status = $request->validate(['status' => 'required|in:Out for Delivery,Delivered,Cancelled'])['status'];
        abort_if($user->role === 'delivery' && $status === 'Cancelled', 403);

        $service->updateStatus($delivery, $status, $user);

        return back()->with('ok', "Delivery #{$delivery->id} marked {$status}.");
    }
}
