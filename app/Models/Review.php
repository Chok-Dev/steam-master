<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'seller_id',
        'order_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    /**
     * ความสัมพันธ์กับ User (ผู้รีวิว)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ความสัมพันธ์กับ User (ผู้ขาย)
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * ความสัมพันธ์กับ Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * เรียกดูทุกรีวิวที่มีคะแนนมากกว่าหรือเท่ากับค่าที่กำหนด
     */
    public function scopeRatingMin($query, $rating)
    {
        return $query->where('rating', '>=', $rating);
    }

    /**
     * เรียกดูทุกรีวิวที่มีคะแนนน้อยกว่าหรือเท่ากับค่าที่กำหนด
     */
    public function scopeRatingMax($query, $rating)
    {
        return $query->where('rating', '<=', $rating);
    }

    /**
     * เรียกดูทุกรีวิวสำหรับผู้ขายที่กำหนด
     */
    public function scopeForSeller($query, $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }
}
