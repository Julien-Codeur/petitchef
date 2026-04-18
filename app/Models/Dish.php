<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dish extends Model
{
    use HasFactory;

    protected $fillable = [
        'cook_id',
        'name',
        'description',
        'price',
        'available_qty',
        'photo_path',
        'served_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'served_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the cook who created this dish
     */
    public function cook(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cook_id');
    }

    /**
     * Get all order items for this dish
     */
    public function orderDishes(): HasMany
    {
        return $this->hasMany(OrderDish::class);
    }

    /**
     * Check if dish is available for ordering (today and active)
     */
    public function isAvailableToday(): bool
    {
        return $this->is_active && 
               $this->served_date->isToday() && 
               $this->available_qty > 0;
    }

    /**
     * Decrease available quantity after order
     */
    public function decreaseStock(int $quantity): bool
    {
        if ($this->available_qty < $quantity) {
            return false;
        }
        
        $this->available_qty -= $quantity;
        return $this->save();
    }

    /**
     * Scope to get today's dishes
     */
    public function scopeToday($query)
    {
        return $query->whereDate('served_date', today());
    }

    /**
     * Scope to get active dishes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get dishes by a specific cook
     */
    public function scopeByCook($query, $cookId)
    {
        return $query->where('cook_id', $cookId);
    }
}
