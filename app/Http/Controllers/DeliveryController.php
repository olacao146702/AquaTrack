<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DeliveryRequest;
use App\Models\Product;
use App\Models\User;
use App\Services\DeliveryService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeliveryController extends Controller
{
    private const STATUSES = ['Pending', 'Assigned', 'Out for Delivery', 'Delivered', 'Cancelled'];

    public function index(Request $request)
    {
        $q = DeliveryRequest::with('customer', 'product', 'rider')->latest();

        // Riders only ever see their own assignments.
        if ($request->user()->role === 'delivery') {
            $q->where('assigned_to', $request->user()->id);
        }
        // An unknown ?status= value is ignored instead of returning an empty or broken page.
        if (in_array($request->query('status'), self::STATUSES, true)) {
            $q->where('status', $request->query('status'));
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
            'customer_id'      => ['required', 'exists:customers,id'],
            'product_id'       => ['required', Rule::exists('products', 'id')->where('is_active', true)],
            'quantity'         => ['required', 'integer', 'min:1', 'max:500'],
            'delivery_address' => ['required', 'string', 'min:5', 'max:255'],
            'preferred_date'   => ['required', 'date', 'after_or_equal:today'],
            'preferred_time'   => ['nullable', 'date_format:H:i'],
            'remarks'          => ['nullable', 'string', 'max:255'],
        ], [
            'customer_id.required'          => 'Choose a customer.',
            'product_id.exists'             => 'The selected product is no longer available.',
            'quantity.*'                    => 'Quantity must be a whole number from 1 to 500.',
            'delivery_address.required'     => 'Enter the delivery address.',
            'delivery_address.min'          => 'The delivery address is too short.',
            'preferred_date.required'       => 'Choose a delivery date.',
            'preferred_date.after_or_equal' => 'The delivery date cannot be in the past.',
            'preferred_time.date_format'    => 'Enter the time like 14:30.',
        ]);
        DeliveryRequest::create($data);

        return back()->with('ok', 'Delivery request saved.');
    }

    public function assign(Request $request, DeliveryRequest $delivery, DeliveryService $service)
    {
        $riderId = $request->validate([
            'assigned_to' => ['required', Rule::exists('users', 'id')->where('role', 'delivery')->where('status', 'Active')],
        ], [
            'assigned_to.required' => 'Choose a rider first.',
            'assigned_to.exists'   => 'Choose an active delivery rider.',
        ])['assigned_to'];

        $rider = User::findOrFail($riderId);
        $service->assign($delivery, $rider);

        return back()->with('ok', "Assigned to {$rider->full_name}.");
    }

    public function status(Request $request, DeliveryRequest $delivery, DeliveryService $service)
    {
        $user = $request->user();
        abort_if($user->role === 'delivery' && $delivery->assigned_to !== $user->id, 403);

        $status = $request->validate([
            'status' => ['required', 'in:Out for Delivery,Delivered,Cancelled'],
        ], ['status.*' => 'That is not a valid delivery status.'])['status'];
        abort_if($user->role === 'delivery' && $status === 'Cancelled', 403);

        $service->updateStatus($delivery, $status, $user);

        return back()->with('ok', "Delivery #{$delivery->id} marked {$status}.");
    }
}
