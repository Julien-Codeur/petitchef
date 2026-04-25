<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryStop extends Model
{
    use HasFactory;

    protected $table = 'delivery_stops';

    protected $fillable = [
        'delivery_id',
        'delivery_address_id',
        'location_name',
        'sequence_order',
        'orders_ids',
        'status',
        'estimated_arrival_time',
        'actual_arrival_time',
    ];

    protected $casts = [
        'orders_ids' => 'json',
    ];

    /**
     * Relations
     */
    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    public function deliveryAddress(): BelongsTo
    {
        return $this->belongsTo(DeliveryAddress::class);
    }

    /**
     * Helpers
     */
    public function getOrderIds(): array
    {
        return $this->orders_ids ?? [];
    }

    public function isDelivered(): bool
    {
        return $this->status === 'livrée';
    }

    public function markDelivered(): void
    {
        $this->update([
            'status' => 'livrée',
            'actual_arrival_time' => now()->toTimeString(),
        ]);
    }
}
