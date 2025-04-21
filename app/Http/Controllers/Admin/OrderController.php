<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'orderItems.product.user']);
        
        // กรองตามสถานะ
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // กรองตามผู้ซื้อ
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        
        // ค้นหาตามรหัสออเดอร์
        if ($request->has('search') && $request->search) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }
        
        // เรียงลำดับ
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'total_asc':
                    $query->orderBy('total_amount', 'asc');
                    break;
                case 'total_desc':
                    $query->orderBy('total_amount', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'newest':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $orders = $query->paginate(20);
        $users = User::where('role', 'user')->get();
        
        return view('admin.orders.index', compact('orders', 'users'));
    }

    /**
     * แสดงรายละเอียดออเดอร์
     */
    public function show(Order $order)
    {
        $order->load(['user', 'orderItems.product.user', 'transactions']);
        
        return view('admin.orders.show', compact('order'));
    }

    /**
     * แสดงฟอร์มแก้ไขออเดอร์
     */
    public function edit(Order $order)
    {
        $order->load(['user', 'orderItems.product.user', 'transactions']);
        $users = User::where('role', 'user')->get();
        
        return view('admin.orders.edit', compact('order', 'users'));
    }

    /**
     * อัพเดทข้อมูลออเดอร์
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,completed,failed,canceled',
            'notes' => 'nullable|string',
        ]);
        
        // เริ่ม transaction เพื่อให้มั่นใจว่าข้อมูลถูกบันทึกทั้งหมดหรือไม่มีการบันทึกเลย
        DB::beginTransaction();
        
        try {
            $oldStatus = $order->status;
            $newStatus = $request->status;
            
            $order->status = $newStatus;
            $order->notes = $request->notes;
            $order->save();
            
            // ถ้าสถานะเปลี่ยนเป็น canceled ให้อัพเดทสถานะสินค้าด้วย
            if ($newStatus === 'canceled' && $oldStatus !== 'canceled') {
                foreach ($order->orderItems as $item) {
                    if ($item->status !== 'delivered') {
                        $item->status = 'canceled';
                        $item->save();
                        
                        // อัพเดทสถานะสินค้ากลับเป็น available
                        $product = $item->product;
                        if ($product && $product->status === 'pending') {
                            $product->status = 'available';
                            $product->save();
                        }
                    }
                }
                
                // ถ้ามีการชำระเงินแล้ว ให้สร้างรายการคืนเงิน
                if ($order->isPaid()) {
                    $payment = $order->transactions()
                        ->where('type', 'payment')
                        ->where('status', 'successful')
                        ->first();
                    
                    if ($payment) {
                        Transaction::create([
                            'order_id' => $order->id,
                            'user_id' => $order->user_id,
                            'transaction_id' => 'REF-' . time(),
                            'amount' => $payment->amount,
                            'type' => 'refund',
                            'status' => 'successful',
                            'notes' => 'คืนเงินจากการยกเลิกออเดอร์',
                        ]);
                        
                        // คืนเงินเข้าวอลเล็ตผู้ซื้อ
                        $buyer = $order->user;
                        $buyer->balance += $payment->amount;
                        $buyer->save();
                    }
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'อัพเดทออเดอร์เรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
}
