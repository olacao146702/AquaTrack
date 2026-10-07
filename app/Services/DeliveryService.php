<?php

namespace App\Services;

use App\Models\DeliveryRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeliveryService
{
    private const TRANSITIONS = [
        'Pending'          => ['Assigned', 'Cancelled'],
        'Assigned'         => ['Out for Delivery', 'Cancelled'],
        'Out for Delivery' => ['Delivered'],
    ];

    public function __construct(private SaleService $sales) {}

    public function assign(DeliveryRequest $delivery, User $rider): void
    {
        abort_unless($rider->role === 'delivery' && $rider->status === 'Active', 422, 'Invalid rider.');
        $this->guard($delivery, 'Assigned');
        $delivery->update(['assigned_to' => $rider->id, 'status' => 'Assigned']);
    }

    public function updateStatus(DeliveryRequest $delivery, string $status, User $actor): void
    {
        $this->guard($delivery, $status);

        DB::transaction(function () use ($delivery, $status, $actor) {
            $delivery->update(['status' => $status]);

            // A completed delivery becomes a sale, so it never has to be encoded twice.
            if ($status === 'Delivered') {
                $sale = $this->sales->record($actor, 'Delivery', $delivery->customer_id, [
                    ['product_id' => $delivery->product_id, 'quantity' => $delivery->quantity],
                ]);
                $delivery->update(['sale_id' => $sale->id, 'completed_at' => now()]);
            }
        });
    }

    private function guard(DeliveryRequest $delivery, string $next): void
    {
        if (! in_array($next, self::TRANSITIONS[$delivery->status] ?? [], true)) {
            throw ValidationException::withMessages([
                'status' => "A {$delivery->status} delivery cannot be changed to {$next}.",
            ]);
        }
    }
}
