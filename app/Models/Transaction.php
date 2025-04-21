<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'transaction_id',
        'amount',
        'type',
        'status',
        'notes',
        'payment_details',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_details' => 'array',
    ];

    /**
     * ความสัมพันธ์กับ Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * ความสัมพันธ์กับ User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * เรียกดูทุกธุรกรรมที่เป็นการชำระเงิน
     */
    public function scopePayment($query)
    {
        return $query->where('type', 'payment');
    }

    /**
     * เรียกดูทุกธุรกรรมที่เป็นการจ่ายเงิน (ให้ผู้ขาย)
     */
    public function scopePayout($query)
    {
        return $query->where('type', 'payout');
    }

    /**
     * เรียกดูทุกธุรกรรมที่เป็นการคืนเงิน
     */
    public function scopeRefund($query)
    {
        return $query->where('type', 'refund');
    }

    /**
     * เรียกดูทุกธุรกรรมที่สำเร็จ
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'successful');
    }

    /**
     * เรียกดูทุกธุรกรรมที่กำลังรอดำเนินการ
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * เรียกดูทุกธุรกรรมที่ล้มเหลว
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
