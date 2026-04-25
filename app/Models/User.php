<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'phone', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relations pour Cuisinier
     */
    public function dishes(): HasMany
    {
        return $this->hasMany(Dish::class, 'cook_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'cook_id');
    }

    public function deliveryAddresses(): HasMany
    {
        return $this->hasMany(DeliveryAddress::class, 'cook_id');
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class, 'cook_id');
    }

    /**
     * Relations pour Client
     */
    public function clientOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'client_id');
    }

    /**
     * Role Helpers
     */
    public function isCook(): bool
    {
        return $this->role === 'cook';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDeliveryPerson(): bool
    {
        return $this->role === 'delivery_person';
    }
}
