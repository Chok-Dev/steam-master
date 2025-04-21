<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'order_id',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * ความสัมพันธ์กับ User (ผู้ส่ง)
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * ความสัมพันธ์กับ User (ผู้รับ)
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * ความสัมพันธ์กับ Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * เรียกดูทุกข้อความที่ยังไม่ได้อ่าน
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * เรียกดูทุกข้อความที่อ่านแล้ว
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * เรียกดูทุกข้อความที่ส่งถึงผู้ใช้
     */
    public function scopeToUser($query, $userId)
    {
        return $query->where('receiver_id', $userId);
    }

    /**
     * เรียกดูทุกข้อความที่ส่งจากผู้ใช้
     */
    public function scopeFromUser($query, $userId)
    {
        return $query->where('sender_id', $userId);
    }

    /**
     * เรียกดูการสนทนาระหว่างผู้ใช้สองคน
     */
    public function scopeConversation($query, $user1Id, $user2Id)
    {
        return $query->where(function ($q) use ($user1Id, $user2Id) {
            $q->where('sender_id', $user1Id)->where('receiver_id', $user2Id);
        })->orWhere(function ($q) use ($user1Id, $user2Id) {
            $q->where('sender_id', $user2Id)->where('receiver_id', $user1Id);
        })->orderBy('created_at', 'asc');
    }

    /**
     * อ่านข้อความ
     */
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }
}
