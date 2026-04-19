<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'cook_id',
        'total_price',
        'pickup_time',
        'status',
        'note_client',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
            'pickup_time' => 'datetime:H:i',
        ];
    }

    /**
     * Get the client who placed this order
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the cook who is preparing this order
     */
    public function cook(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cook_id');
    }

    /**
     * Get all items in this order
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderDish::class, 'order_id');
    }

    /**
     * Get status label in French
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            'received' => 'Reçue',
            'preparing' => 'En préparation',
            'ready' => 'Prête',
            'delivered' => 'Livrée',
            'cancelled' => 'Annulée',
            default => $this->status,
        };
    }

    /**
     * Check if order can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['received', 'preparing']);
    }

    /**
     * Check if order can transition to next status
     */
    public function canTransition(string $newStatus): bool
    {
        $transitions = [
            'received' => ['preparing', 'cancelled'],
            'preparing' => ['ready', 'cancelled'],
            'ready' => ['delivered'],
            'delivered' => [],
            'cancelled' => [],
        ];

        return in_array($newStatus, $transitions[$this->status] ?? []);
    }

    /**
     * Scope to get orders by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get orders for a cook
     */
    public function scopeForCook($query, $cookId)
    {
        return $query->where('cook_id', $cookId);
    }

    /**
     * Scope to get orders for a client
     */
    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Get all reports about this order
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'reported_order_id');
    }
}
