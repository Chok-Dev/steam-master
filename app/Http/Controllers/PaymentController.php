<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function checkout(Order $order)
    {
        // แสดงหน้าชำระเงิน
        return view('payments.checkout', compact('order'));
    }

    public function process(Request $request, Order $order)
    {
        // จำลองการชำระเงินสำเร็จ
        $transaction = new Transaction();
        $transaction->order_id = $order->id;
        $transaction->user_id = auth()->id();
        $transaction->transaction_id = 'TR' . time();
        $transaction->amount = $order->total_amount;
        $transaction->type = 'payment';
        $transaction->status = 'successful';
        $transaction->payment_details = [
            'method' => $request->payment_method,
            'time' => now()->toDateTimeString(),
        ];
        $transaction->save();

        // อัพเดทสถานะออเดอร์
        $order->update(['status' => 'processing']);

        // แจ้งเตือนผู้ขาย
        foreach ($order->orderItems as $item) {
            $seller = $item->product->user;
            if ($item->product->key_data) {
                // ถ้าสินค้ามีรหัสเกมเตรียมไว้แล้ว ให้คัดลอกไปที่ OrderItem และเปลี่ยนสถานะเป็นส่งมอบแล้ว
                $item->key_data = $item->product->key_data;
                $item->status = 'delivered';
                $item->delivered_at = now();
                $item->save();

                // เปลี่ยนสถานะสินค้าเป็นขายแล้ว
                $item->product->status = 'sold';
                $item->product->save();

                // จ่ายเงินให้ผู้ขาย
                $this->payToSeller($item);
            }
        }

        return redirect()->route('orders.show', $order)
            ->with('success', 'ชำระเงินสำเร็จ! คุณจะได้รับรหัสเกมเมื่อผู้ขายยืนยันการชำระเงิน');
    }
    
    private function payToSeller(OrderItem $item)
    {
        // สร้าง transaction สำหรับการจ่ายเงินให้ผู้ขาย
        $transaction = new Transaction();
        $transaction->order_id = $item->order_id;
        $transaction->user_id = $item->product->user_id; // ผู้ขาย
        $transaction->transaction_id = 'PO' . time();
        $transaction->amount = $item->price * 0.95; // หักค่าคอมมิชชั่น 5%
        $transaction->type = 'payout';
        $transaction->status = 'successful';
        $transaction->notes = 'จ่ายเงินให้ผู้ขายอัตโนมัติ (สินค้ามีรหัสพร้อมส่ง)';
        $transaction->save();

        // เพิ่มเงินเข้าบัญชีผู้ขาย
        $seller = $item->product->user;
        $seller->increment('balance', $transaction->amount);
    }

    public function escrowRelease(OrderItem $orderItem)
    {
        // อัพเดทสถานะ order item เป็น delivered
        $orderItem->update([
            'status' => 'delivered',
            'delivered_at' => now()
        ]);

        // ตรวจสอบว่าทุก item ใน order ถูกส่งมอบแล้วหรือไม่
        $pendingItems = $orderItem->order->orderItems()->where('status', 'pending')->count();
        if ($pendingItems === 0) {
            $orderItem->order->update(['status' => 'completed']);
        }

        // สร้าง transaction สำหรับการจ่ายเงินให้ผู้ขาย
        $transaction = new Transaction();
        $transaction->order_id = $orderItem->order_id;
        $transaction->user_id = $orderItem->product->user_id; // ผู้ขาย
        $transaction->transaction_id = 'PO' . time();
        $transaction->amount = $orderItem->price * 0.95; // หักค่าคอมมิชชั่น 5%
        $transaction->type = 'payout';
        $transaction->status = 'successful';
        $transaction->save();

        // เพิ่มเงินเข้าบัญชีผู้ขาย
        $seller = $orderItem->product->user;
        $seller->increment('balance', $transaction->amount);

        return redirect()->back()->with('success', 'การชำระเงินสำเร็จ! เงินได้ถูกโอนไปยังผู้ขายแล้ว');
    }
}
