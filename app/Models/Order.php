<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'client_id',
        'cook_id',
        'delivery_address_id',
        'served_date',
        'pickup_time',
        'location_name',
        'total_price',
        'status',
        'payment_method',
        'notes_client',
        'estimated_delivery_time',
    ];

    protected $casts = [
        'served_date' => 'date',
        'total_price' => 'decimal:2',
    ];

    /**
     * Relations
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function cook(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cook_id');
    }

    public function deliveryAddress(): BelongsTo
    {
        return $this->belongsTo(DeliveryAddress::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scopes
     */
    public function scopeForCook(Builder $query, int $cookId): Builder
    {
        return $query->where('cook_id', $cookId);
    }

    public function scopeForClient(Builder $query, int $clientId): Builder
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeOfTheDay(Builder $query): Builder
    {
        return $query->where('served_date', today());
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Helpers
     */
    public function isReceived(): bool
    {
        return $this->status === 'reçue';
    }

    public function isInPreparation(): bool
    {
        return $this->status === 'en_préparation';
    }

    public function isReady(): bool
    {
        return $this->status === 'prête';
    }

    public function isDelivered(): bool
    {
        return $this->status === 'livrée';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'annulée';
    }

    public function markInPreparation(): void
    {
        $this->update(['status' => 'en_préparation']);
    }

    public function markReady(): void
    {
        $this->update(['status' => 'prête']);
    }

    public function markDelivered(): void
    {
        $this->update(['status' => 'livrée']);
    }

    public function markCancelled(): void
    {
        $this->update(['status' => 'annulée']);
    }
}
