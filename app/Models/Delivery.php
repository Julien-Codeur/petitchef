<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Delivery extends Model
{
    use HasFactory;
    protected $fillable = [
        'cook_id',
        'served_date',
        'planned_time',
        'status',
        'route_order',
        'estimated_total_duration',
    ];

    protected $casts = [
        'served_date' => 'date',
        'route_order' => 'json',
    ];

    /**
     * Relations
     */
    public function cook(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cook_id');
    }

    public function stops(): HasMany
    {
        return $this->hasMany(DeliveryStop::class)->orderBy('sequence_order');
    }

    /**
     * Scopes
     */
    public function scopeForCook(Builder $query, int $cookId): Builder
    {
        return $query->where('cook_id', $cookId);
    }

    public function scopeOfTheDay(Builder $query): Builder
    {
        return $query->where('served_date', today());
    }

    /**
     * Helpers
     */
    public function isReadyToLeave(): bool
    {
        return $this->status === 'prête_à_partir';
    }

    public function markReadyToLeave(): void
    {
        $this->update(['status' => 'prête_à_partir']);
    }

    public function markInRoute(): void
    {
        $this->update(['status' => 'en_route']);
    }

    public function markComplete(): void
    {
        $this->update(['status' => 'complète']);
    }

    /**
     * Get total number of items in delivery
     */
    public function getTotalItems(): int
    {
        return $this->stops->sum(function ($stop) {
            return count(json_decode($stop->orders_ids, true) ?? []);
        });
    }
}
