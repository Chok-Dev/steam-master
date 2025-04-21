<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'total_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    /**
     * ความสัมพันธ์กับ User (ผู้ซื้อ)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ความสัมพันธ์กับ OrderItems
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * ความสัมพันธ์กับ Transaction
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * ความสัมพันธ์กับ Review
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * ความสัมพันธ์กับ Message
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * คำนวณราคาสินค้าทั้งหมดใน Order
     */
    public function calculateTotal()
    {
        return $this->orderItems->sum('price');
    }

    /**
     * ตรวจสอบว่าออเดอร์ถูกชำระเงินแล้วหรือไม่
     */
    public function isPaid()
    {
        return $this->transactions()
            ->where('type', 'payment')
            ->where('status', 'successful')
            ->exists();
    }

    /**
     * เรียกดูทุกออเดอร์ที่อยู่ในสถานะรอดำเนินการ
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * เรียกดูทุกออเดอร์ที่อยู่ในสถานะกำลังดำเนินการ
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    /**
     * เรียกดูทุกออเดอร์ที่อยู่ในสถานะสำเร็จแล้ว
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * เรียกดูทุกออเดอร์ที่อยู่ในสถานะยกเลิก
     */
    public function scopeCanceled($query)
    {
        return $query->where('status', 'canceled');
    }
}
