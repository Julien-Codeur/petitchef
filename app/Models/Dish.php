<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Dish extends Model
{
    use HasFactory;
    protected $fillable = [
        'cook_id',
        'name',
        'description',
        'price',
        'initial_qty',
        'available_qty',
        'photo_path',
        'is_active',
        'served_date',
        'critical_threshold',
        'is_critical_notified',
    ];

    protected $casts = [
        'served_date' => 'date',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_critical_notified' => 'boolean',
    ];

    /**
     * Relations
     */
    public function cook(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cook_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scopes
     */
    public function scopeOfTheDay(Builder $query): Builder
    {
        return $query
            ->where('served_date', today())
            ->where('is_active', true);
    }

    public function scopeForCook(Builder $query, int $cookId): Builder
    {
        return $query->where('cook_id', $cookId);
    }

    public function scopeCritical(Builder $query): Builder
    {
        return $query->whereRaw('available_qty <= critical_threshold AND available_qty > 0');
    }

    public function scopeExhausted(Builder $query): Builder
    {
        return $query->where('available_qty', 0);
    }

    /**
     * Helpers
     */
    public function isCritical(): bool
    {
        return $this->available_qty <= $this->critical_threshold && $this->available_qty > 0;
    }

    public function isExhausted(): bool
    {
        return $this->available_qty === 0;
    }

    public function decrementStock(int $quantity): bool
    {
        if ($this->available_qty < $quantity) {
            return false;
        }

        $this->update([
            'available_qty' => $this->available_qty - $quantity,
            'is_critical_notified' => false, // Reset notification flag
        ]);

        return true;
    }
}
