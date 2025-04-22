<?php

namespace App\Models;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'type',
        'status',
        'attributes',
        'views',
        'key_data',
        'mafile_path',
        'steam_auth_data' // เพิ่มฟิลด์นี้
    ];
    public function getDecryptedKeyAttribute()
    {
        if (!$this->key_data) {
            return null;
        }
        
        try {
            return Crypt::decryptString($this->key_data);
        } catch (\Exception $e) {
            return null;
        }
    }
    protected $casts = [
        'attributes' => 'array',  // สำคัญ: cast attributes เป็น array
        'price' => 'decimal:2',
    ];
    public function reviews()
    {
        return $this->hasMany(Review::class, 'seller_id', 'user_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function incrementViews()
    {
        $this->increment('views');
    }
    
    // ช่วยให้แน่ใจว่า attributes เป็น array เสมอ แม้ว่าจะเป็น null
    public function getAttributesAttribute($value)
    {
        if (is_null($value)) {
            return [];
        }
        
        // ถ้าเป็น string และสามารถ decode ได้ ให้ decode
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }
        
        return is_array($value) ? $value : [];
    }
}