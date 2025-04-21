<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // สถิติพื้นฐาน
        $totalUsers = User::count();
        $totalSellers = User::where('role', 'seller')->count();
        $totalProducts = Product::count();
        $totalSales = Transaction::where('type', 'payment')->where('status', 'successful')->sum('amount');
        
        // ออเดอร์ล่าสุด
        $recentOrders = Order::with(['user', 'orderItems.product.user'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // ธุรกรรมล่าสุด
        $recentTransactions = Transaction::with(['user', 'order'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // ผู้ใช้ล่าสุด
        $recentUsers = User::orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // ข้อมูลสำหรับกราฟ
        // ข้อมูลยอดขาย 7 วันล่าสุด
        $salesData = [];
        
        // สร้างข้อมูลย้อนหลัง 7 วัน
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $salesData[$date] = 0;
        }
        
        // คำนวณยอดขายรายวัน
        $dailySales = Transaction::where('type', 'payment')
            ->where('status', 'successful')
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->get();
        
        foreach ($dailySales as $sale) {
            $salesData[$sale->date] = $sale->total;
        }
        
        // ข้อมูลกราฟยอดขาย
        $salesChartData = [
            'labels' => array_keys($salesData),
            'data' => array_values($salesData),
        ];
        
        return view('admin.dashboard', compact(
            'totalUsers',
            'totalSellers',
            'totalProducts',
            'totalSales',
            'recentOrders',
            'recentTransactions',
            'recentUsers',
            'salesChartData'
        ));
    }

    /**
     * แสดงธุรกรรมทั้งหมด
     */
    public function transactions()
    {
        $transactions = Transaction::with(['user', 'order'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.transactions', compact('transactions'));
    }
}
