<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
            'role' => UserRole::class,
        ];
    }

    /**
     * Commandes passees par ce client.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Commandes prises en charge par ce chef.
     */
    public function kitchenOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'chef_id');
    }

    /**
     * Livraisons effectuees par ce livreur.
     */
    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class, 'livreur_id');
    }

    /**
     * Avis laisses par cet utilisateur.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Notifications applicatives (table maison decrite dans le README).
     *
     * Cette relation surcharge celle du trait Notifiable, qui suppose une
     * table polymorphe differente de notre schema.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function hasRole(UserRole $role): bool
    {
        return $this->role === $role;
    }

    public function isClient(): bool
    {
        return $this->hasRole(UserRole::Client);
    }

    public function isChef(): bool
    {
        return $this->hasRole(UserRole::Chef);
    }

    public function isLivreur(): bool
    {
        return $this->hasRole(UserRole::Livreur);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(UserRole::Admin);
    }
}
