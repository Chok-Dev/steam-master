<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    // แสดงหน้าชำระเงิน
    public function checkout(Request $request)
    {
        // ตรวจสอบว่ามี product_id ที่ต้องการซื้อใน session หรือไม่
        $productId = session('product_to_buy');
        if (!$productId) {
            return redirect()->route('products.index')->with('error', 'ไม่พบสินค้าที่ต้องการชำระเงิน');
        }

        // ดึงข้อมูลสินค้า
        $product = Product::findOrFail($productId);

        // ตรวจสอบสถานะสินค้าอีกครั้ง (เผื่อมีคนซื้อไปแล้ว)
        if ($product->status !== 'available') {
            return redirect()->route('products.show', $product)->with('error', 'สินค้านี้ไม่พร้อมขายในขณะนี้');
        }

        // สร้างข้อมูลสำหรับแสดงในหน้าชำระเงิน
        $checkoutData = [
            'product' => $product,
            'total' => $product->price
        ];

        return view('payments.checkout', compact('checkoutData'));
    }

    // ประมวลผลการชำระเงิน - เฉพาะวอลเล็ต
    public function process(Request $request)
    {
        // ตรวจสอบว่ามี product_id ที่ต้องการซื้อใน session หรือไม่
        $productId = session('product_to_buy');
        if (!$productId) {
            return redirect()->route('products.index')->with('error', 'ไม่พบสินค้าที่ต้องการชำระเงิน');
        }

        // ดึงข้อมูลสินค้า
        $product = Product::findOrFail($productId);

        // ตรวจสอบสถานะสินค้าอีกครั้ง (เผื่อมีคนซื้อไปแล้ว)
        if ($product->status !== 'available') {
            return redirect()->route('products.show', $product)->with('error', 'สินค้านี้ไม่พร้อมขายในขณะนี้');
        }

        // ตรวจสอบยอดเงินในวอลเล็ต
        $user = auth()->user();
        if ($user->balance < $product->price) {
            return redirect()->route('topup')->with('error', 'ยอดเงินในวอลเล็ตไม่เพียงพอ กรุณาเติมเงินก่อนทำการชำระเงิน');
        }

        // เริ่ม transaction เพื่อความปลอดภัยของข้อมูล
        DB::beginTransaction();

        try {
            // 1. หักเงินจากวอลเล็ตของผู้ซื้อ
            $user->balance -= $product->price;
            $user->save();

            // 2. สร้างออเดอร์ใหม่
            $order = new Order();
            $order->user_id = $user->id;
            $order->order_number = 'ORD-' . Str::random(10);
            $order->total_amount = $product->price;
            $order->status = 'processing'; // เปลี่ยนเป็น processing เลยเพราะชำระเงินแล้ว
            $order->save();

            // 3. สร้าง order item
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $product->id;
            $orderItem->price = $product->price;
            $orderItem->status = 'pending';
            $orderItem->save();

            // 4. อัพเดทสถานะสินค้า
            $product->status = 'pending';
            $product->save();

            // 5. บันทึกธุรกรรมการชำระเงิน
            $transaction = new Transaction();
            $transaction->order_id = $order->id;
            $transaction->user_id = $user->id;
            $transaction->transaction_id = 'TR' . time();
            $transaction->amount = $order->total_amount;
            $transaction->type = 'payment';
            $transaction->status = 'successful';
            $transaction->payment_details = [
                'method' => 'wallet',
                'time' => now()->toDateTimeString(),
            ];
            $transaction->save();

            // 6. ถ้าสินค้ามีรหัสเกมเตรียมไว้แล้ว ให้ส่งมอบทันที
            if ($product->key_data) {
                $orderItem->key_data = $product->key_data;
                $orderItem->status = 'delivered';
                $orderItem->delivered_at = now();
                $orderItem->save();

                // เปลี่ยนสถานะสินค้าเป็นขายแล้ว
                $product->status = 'sold';
                $product->save();

                // จ่ายเงินให้ผู้ขาย
                $this->payToSeller($orderItem);
            }

            // 7. ลบข้อมูลใน session
            session()->forget('product_to_buy');

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'ชำระเงินสำเร็จ! เงินถูกหักจากวอลเล็ตของคุณเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('products.show', $product)
                ->with('error', 'เกิดข้อผิดพลาดในการชำระเงิน: ' . $e->getMessage());
        }
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
        // ตรวจสอบว่าผู้ใช้ปัจจุบันเป็นผู้ซื้อของ order นี้
        if ($orderItem->order->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'คุณไม่มีสิทธิ์ยืนยันรายการนี้');
        }

        // ตรวจสอบว่า order item อยู่ในสถานะที่ถูกส่งมอบแล้ว
        if ($orderItem->status !== 'delivered') {
            return redirect()->back()->with('error', 'รายการนี้ยังไม่พร้อมสำหรับการยืนยัน');
        }

        // ตรวจสอบว่ายังไม่ได้ยืนยันรับสินค้า
        if ($orderItem->is_confirmed) {
            return redirect()->back()->with('error', 'รายการนี้ได้รับการยืนยันแล้ว');
        }

        // อัพเดทสถานะ order item เป็น confirmed
        $orderItem->update([
            'status' => 'confirmed',
            'is_confirmed' => true,
            'confirmed_at' => now()
        ]);

        // ตรวจสอบว่าทุก item ใน order ถูกยืนยันแล้วหรือไม่
        $pendingItems = $orderItem->order->orderItems()->where(function ($query) {
            $query->where('status', 'pending')
                ->orWhere('status', 'delivered');
        })->count();

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
        $transaction->notes = 'จ่ายเงินให้ผู้ขายจากการยืนยันรับสินค้าของผู้ซื้อ';
        $transaction->save();

        // เพิ่มเงินเข้าบัญชีผู้ขาย
        $seller = $orderItem->product->user;
        $seller->increment('balance', $transaction->amount);

        return redirect()->back()->with('success', 'ยืนยันการรับรหัสเกมเรียบร้อยแล้ว เงินได้ถูกโอนไปยังผู้ขายแล้ว');
    }

    public function toupIndex()
    {
        return view('truemoney.index');
    }
    public function toupTruemoney()
    {
        return view('truemoney.topup');
    }
    public function toupChillpay()
    {
        return view('truemoney.topupchillpay');
    }
}
