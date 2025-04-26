<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // กรองตามบทบาท
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        // กรองตามสถานะการยืนยัน
        if ($request->has('is_verified') && $request->is_verified !== null) {
            $query->where('is_verified', $request->is_verified);
        }

        // ค้นหาตามชื่อหรืออีเมล
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // เรียงลำดับ
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'email_asc':
                    $query->orderBy('email', 'asc');
                    break;
                case 'email_desc':
                    $query->orderBy('email', 'desc');
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

        $users = $query->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * แสดงรายละเอียดผู้ใช้
     */
    public function show(User $user)
    {
        // ดึงข้อมูลที่เกี่ยวข้อง
        $productsCount = $user->products()->count();
        $soldProductsCount = $user->products()->where('status', 'sold')->count();
        $ordersCount = $user->orders()->count();
        $totalSpent = $user->transactions()->where('type', 'payment')->where('status', 'successful')->sum('amount');
        $totalEarned = $user->transactions()->where('type', 'payout')->where('status', 'successful')->sum('amount');

        // 5 สินค้าล่าสุด
        $recentProducts = $user->products()->latest()->take(5)->get();

        // 5 ออเดอร์ล่าสุด
        $recentOrders = $user->orders()->latest()->take(5)->get();

        // 5 ธุรกรรมล่าสุด
        $recentTransactions = $user->transactions()->latest()->take(5)->get();

        return view('admin.users.show', compact(
            'user',
            'productsCount',
            'soldProductsCount',
            'ordersCount',
            'totalSpent',
            'totalEarned',
            'recentProducts',
            'recentOrders',
            'recentTransactions'
        ));
    }

    /**
     * แสดงฟอร์มแก้ไขผู้ใช้
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * อัพเดทข้อมูลผู้ใช้
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|string|in:admin,seller,user',
            'is_verified' => 'boolean',
            'balance' => 'nullable|numeric|min:0',
            'bio' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->is_verified = $request->is_verified ?? 0;
        $user->bio = $request->bio;

        if ($request->filled('balance')) {
            $user->balance = $request->balance;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // อัพโหลดรูปโปรไฟล์ (ถ้ามี)
        if ($request->hasFile('avatar')) {
            // ลบรูปเก่า (ถ้ามี)
            if ($user->avatar) {
                Storage::delete('public/' . $user->avatar);
            }

            // อัพโหลดรูปใหม่
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = str_replace('public/', '', $path);
        }

        $user->save();

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'อัพเดทข้อมูลผู้ใช้เรียบร้อยแล้ว');
    }

    /**
     * ลบผู้ใช้
     */
    public function destroy(User $user)
    {
        // ตรวจสอบข้อมูลที่เกี่ยวข้องก่อนลบ
        $hasOrders = $user->orders()->exists();
        $hasProducts = $user->products()->exists();
        $hasTransactions = $user->transactions()->exists();

        if ($hasOrders || $hasProducts || $hasTransactions) {
            return redirect()->back()->with('error', 'ไม่สามารถลบผู้ใช้ที่มีข้อมูลเกี่ยวข้องในระบบได้');
        }

        // ลบรูปโปรไฟล์ (ถ้ามี)
        if ($user->avatar) {
            Storage::delete('public/' . $user->avatar);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'ลบผู้ใช้เรียบร้อยแล้ว');
    }

    /**
     * ปรับเปลี่ยนสถานะการยืนยันตัวตน
     */
    public function toggleVerification(User $user)
    {
        $user->is_verified = !$user->is_verified;
        $user->save();

        $status = $user->is_verified ? 'ยืนยันตัวตนเรียบร้อยแล้ว' : 'ยกเลิกการยืนยันตัวตน';

        return redirect()->back()->with('success', $status);
    }

    /**
     * ปรับยอดเงินในวอลเล็ต (เพิ่ม/ลด)
     */
    public function adjustBalance(Request $request, User $user)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'operation' => 'required|in:add,subtract',
            'notes' => 'nullable|string',
        ]);

        $amount = abs($request->amount);
        $notes = $request->notes ?? 'การปรับยอดเงินโดยผู้ดูแลระบบ';

        if ($request->operation === 'add') {
            $user->balance += $amount;

            $systemOrder = Order::firstOrCreate(
                ['order_number' => 'SYSTEM-ADJUSTMENT'],
                [
                    'user_id' => $user->id,
                    'total_amount' => 0,
                    'status' => 'completed',
                    'notes' => 'System order for administrative transactions'
                ]
            );

            // Then use this order ID in your transaction creation:
            Transaction::create([
                'user_id' => $user->id,
                'order_id' => $systemOrder->id,  // Use the system order ID
                'transaction_id' => 'ADJ-' . time(),
                'amount' => $amount,
                'type' => 'topup',
                'status' => 'successful',
                'notes' => $notes,
            ]);
        } else {
            if ($user->balance < $amount) {
                return redirect()->back()->with('error', 'ยอดเงินในวอลเล็ตไม่เพียงพอ');
            }

            $user->balance -= $amount;

            $systemOrder = Order::firstOrCreate(
                ['order_number' => 'SYSTEM-ADJUSTMENT'],
                [
                    'user_id' => $user->id,
                    'total_amount' => 0,
                    'status' => 'completed',
                    'notes' => 'System order for administrative transactions'
                ]
            );
            Transaction::create([
                'user_id' => $user->id,
                'order_id' => $systemOrder->id,  // Use the system order ID
                'transaction_id' => 'ADJ-' . time(),
                'amount' => $amount,
                'type' => 'topup',
                'status' => 'withdrawal',
                'notes' => $notes,
            ]);
        }

        $user->save();

        return redirect()->back()->with('success', 'ปรับยอดเงินเรียบร้อยแล้ว');
    }
    /**
     * Display a listing of the seller requests.
     */
    public function sellerRequests(Request $request)
    {
        $query = User::where('seller_request_status', 'pending');

        // ค้นหาตามชื่อหรืออีเมล
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // เรียงลำดับ
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $query->orderBy('seller_request_at', 'asc');
                    break;
                case 'newest':
                default:
                    $query->orderBy('seller_request_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('seller_request_at', 'desc');
        }

        $sellerRequests = $query->paginate(10);

        return view('admin.seller-requests.index', compact('sellerRequests'));
    }

    /**
     * Approve a seller request.
     */
    public function approveSellerRequest(User $user)
    {
        if ($user->seller_request_status !== 'pending') {
            return redirect()->route('admin.seller-requests.index')
                ->with('error', 'คำขอไม่อยู่ในสถานะที่สามารถอนุมัติได้');
        }

        $user->role = 'seller';
        $user->seller_request_status = 'approved';
        $user->save();

        // TODO: Send notification to user (implement based on your notification system)

        return redirect()->route('admin.seller-requests.index')
            ->with('success', 'อนุมัติคำขอเป็นผู้ขายเรียบร้อยแล้ว');
    }

    /**
     * Reject a seller request.
     */
    public function rejectSellerRequest(Request $request, User $user)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:255',
        ]);

        if ($user->seller_request_status !== 'pending') {
            return redirect()->route('admin.seller-requests.index')
                ->with('error', 'คำขอไม่อยู่ในสถานะที่สามารถปฏิเสธได้');
        }

        $user->seller_request_status = 'rejected';

        // Store rejection reason
        $sellerDetails = json_decode($user->seller_details, true) ?? [];
        $sellerDetails['rejection_reason'] = $request->rejection_reason;
        $user->seller_details = json_encode($sellerDetails);

        $user->save();

        // TODO: Send notification to user (implement based on your notification system)

        return redirect()->route('admin.seller-requests.index')
            ->with('success', 'ปฏิเสธคำขอเป็นผู้ขายเรียบร้อยแล้ว');
    }
}
