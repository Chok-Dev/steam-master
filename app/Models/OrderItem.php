<?php

namespace App\Models;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'price',
        'status',
        'key_data',
        'delivered_at',
        'confirmed_at',
        'is_confirmed'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'delivered_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'is_confirmed' => 'boolean',
    ];

    /**
     * ความสัมพันธ์กับ Order
     */
    public function getIsConfirmedAttribute()
    {
        return $this->confirmed_at !== null;
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * ความสัมพันธ์กับ Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * ดึงข้อมูลรหัสเกมที่ถูกเข้ารหัสไว้
     */
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

    /**
     * ตรวจสอบว่าสินค้าถูกส่งมอบแล้วหรือไม่
     */
    public function isDelivered()
    {
        return $this->status === 'delivered' && $this->delivered_at !== null;
    }

    /**
     * เรียกดูทุก OrderItem ที่อยู่ในสถานะรอส่งมอบ
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * เรียกดูทุก OrderItem ที่อยู่ในสถานะส่งมอบแล้ว
     */
    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    /**
     * เรียกดูทุก OrderItem ที่อยู่ในสถานะคืนเงิน
     */
    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }
}
