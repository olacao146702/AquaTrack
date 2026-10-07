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
        if ($rider->role !== 'delivery' || $rider->status !== 'Active') {
            throw ValidationException::withMessages(['assigned_to' => 'Choose an active delivery rider.']);
        }

        DB::transaction(function () use ($delivery, $rider) {
            $d = $this->lock($delivery);
            $this->guard($d, 'Assigned');
            $d->update(['assigned_to' => $rider->id, 'status' => 'Assigned']);
        });
    }

    public function updateStatus(DeliveryRequest $delivery, string $status, User $actor): void
    {
        DB::transaction(function () use ($delivery, $status, $actor) {
            // Re-read under a lock: a double click or two users can't complete the same delivery twice.
            $d = $this->lock($delivery);
            $this->guard($d, $status);
            $d->update(['status' => $status]);

            // A completed delivery becomes a sale, so it never has to be encoded twice.
            if ($status === 'Delivered') {
                $sale = $this->sales->record($actor, 'Delivery', $d->customer_id, [
                    ['product_id' => $d->product_id, 'quantity' => $d->quantity],
                ]);
                $d->update(['sale_id' => $sale->id, 'completed_at' => now()]);
            }
        });
    }

    private function lock(DeliveryRequest $delivery): DeliveryRequest
    {
        return DeliveryRequest::lockForUpdate()->findOrFail($delivery->id);
    }

    private function guard(DeliveryRequest $delivery, string $next): void
    {
        if (! in_array($next, self::TRANSITIONS[$delivery->status] ?? [], true)) {
            throw ValidationException::withMessages([
                'status' => "Delivery #{$delivery->id} is already {$delivery->status} and cannot be changed to {$next}.",
            ]);
        }
    }
}
