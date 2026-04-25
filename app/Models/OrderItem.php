<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'dish_id',
        'quantity',
        'unit_price',
        'customization_notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    /**
     * Relations
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function dish(): BelongsTo
    {
        return $this->belongsTo(Dish::class);
    }

    /**
     * Helpers
     */
    public function subtotal(): float
    {
        return (float) ($this->unit_price * $this->quantity);
    }
}
