<?php

namespace App\Http\Controllers\Seller;

use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // สถิติพื้นฐาน
        $totalBalance = $user->balance;
        $totalProducts = $user->products()->count();
        $totalSold = $user->products()->where('status', 'sold')->count();
        $averageRating = $user->receivedReviews()->avg('rating') ?: 0;
        
        // ออเดอร์ล่าสุด
        $recentOrders = OrderItem::whereHas('product', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['order', 'product'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // ธุรกรรมล่าสุด
        $recentTransactions = $user->transactions()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // ข้อมูลสำหรับกราฟ
        // ข้อมูลยอดขาย 7 วันล่าสุด
        $salesData = [];
        $categoryData = [];
        
        // สร้างข้อมูลย้อนหลัง 7 วัน
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $salesData[$date] = 0;
        }
        
        // คำนวณยอดขายรายวัน
        $dailySales = Transaction::where('user_id', $user->id)
            ->where('type', 'payout')
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
        
        // ข้อมูลสัดส่วนหมวดหมู่
        $categories = Category::all();
        $categoryCounts = [];
        
        foreach ($categories as $category) {
            $count = $user->products()->where('category_id', $category->id)->count();
            if ($count > 0) {
                $categoryCounts[$category->name] = $count;
            }
        }
        
        $categoriesChartData = [
            'labels' => array_keys($categoryCounts),
            'data' => array_values($categoryCounts),
        ];
        
        return view('seller.dashboard', compact(
            'totalBalance',
            'totalProducts',
            'totalSold',
            'averageRating',
            'recentOrders',
            'recentTransactions',
            'salesChartData',
            'categoriesChartData'
        ));
    }

    /**
     * แสดงธุรกรรมทั้งหมด
     */
    public function transactions()
    {
        $transactions = auth()->user()->transactions()
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('seller.transactions', compact('transactions'));
    }
}
