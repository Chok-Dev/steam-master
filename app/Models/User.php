<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'bio',
        'avatar',
        'is_verified',
        'balance',
        'last_active_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_active_at' => 'datetime',
        'is_verified' => 'boolean',
        'balance' => 'decimal:2',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function receivedReviews()
    {
        return $this->hasMany(Review::class, 'seller_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getAverageRatingAttribute()
    {
        return $this->receivedReviews()->avg('rating') ?: 0;
    }

    public function getIsSeller()
    {
        return $this->role === 'seller';
    }

    public function getIsAdmin()
    {
        return $this->role === 'admin';
    }
}
