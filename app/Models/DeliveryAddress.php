<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryAddress extends Model
{
    use HasFactory;

    protected $table = 'delivery_addresses';

    protected $fillable = [
        'cook_id',
        'name',
        'address',
        'latitude',
        'longitude',
        'is_regular',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_regular' => 'boolean',
    ];

    /**
     * Relations
     */
    public function cook(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cook_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function deliveryStops(): HasMany
    {
        return $this->hasMany(DeliveryStop::class);
    }
}
