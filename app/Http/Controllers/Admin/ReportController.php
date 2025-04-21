<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function overview(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfDay();

        // สถิติพื้นฐาน
        $totalUsers = User::where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->count();

        $totalSellers = User::where('role', 'seller')
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->count();

        $totalOrders = Order::where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->count();

        $totalSales = Transaction::where('type', 'payment')
            ->where('status', 'successful')
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->sum('amount');

        $totalCommission = Transaction::where('type', 'payment')
            ->where('status', 'successful')
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->sum('amount') * 0.05; // ค่าคอมมิชชั่น 5%

        // ข้อมูลสำหรับกราฟยอดขายรายวัน
        $dailySales = [];

        $period = Carbon::parse($startDate)->daysUntil($endDate);
        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $dailySales[$dateString] = 0;
        }

        $salesData = Transaction::where('type', 'payment')
            ->where('status', 'successful')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->get();

        foreach ($salesData as $data) {
            $dailySales[$data->date] = (float) $data->total;
        }

        // ข้อมูลสำหรับกราฟจำนวนออเดอร์รายวัน
        $dailyOrders = [];

        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $dailyOrders[$dateString] = 0;
        }

        $ordersData = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->get();

        foreach ($ordersData as $data) {
            $dailyOrders[$data->date] = (int) $data->total;
        }

        // ข้อมูลสำหรับกราฟผู้ใช้ใหม่รายวัน
        $dailyUsers = [];

        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $dailyUsers[$dateString] = 0;
        }

        $usersData = User::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->get();

        foreach ($usersData as $data) {
            $dailyUsers[$data->date] = (int) $data->total;
        }

        // 10 อันดับสินค้าขายดี
        $topProducts = \App\Models\Product::withCount(['orderItems' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }])
            ->orderBy('order_items_count', 'desc')
            ->take(10)
            ->get();

        // 10 อันดับผู้ขายยอดเยี่ยม
        $topSellers = User::where('role', 'seller')
            ->withCount(['products as sold_count' => function ($query) use ($startDate, $endDate) {
                $query->where('status', 'sold')
                    ->whereBetween('updated_at', [$startDate, $endDate]);
            }])
            ->orderBy('sold_count', 'desc')
            ->take(10)
            ->get();

        return view('admin.reports.overview', compact(
            'startDate',
            'endDate',
            'totalUsers',
            'totalSellers',
            'totalOrders',
            'totalSales',
            'totalCommission',
            'dailySales',
            'dailyOrders',
            'dailyUsers',
            'topProducts',
            'topSellers'
        ));
    }

    /**
     * แสดงรายงานการขาย
     */
    public function sales(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfDay();

        // สรุปยอดขายตามหมวดหมู่
        $salesByCategory = \App\Models\OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('order_items.created_at', [$startDate, $endDate])
            ->where('order_items.status', 'delivered')
            ->selectRaw('categories.name, SUM(order_items.price) as total_sales')
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_sales', 'desc')
            ->get();

        // สรุปยอดขายตามประเภทสินค้า
        $salesByType = \App\Models\OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('order_items.created_at', [$startDate, $endDate])
            ->where('order_items.status', 'delivered')
            ->selectRaw('products.type, SUM(order_items.price) as total_sales')
            ->groupBy('products.type')
            ->orderBy('total_sales', 'desc')
            ->get();

        // สรุปยอดขายรายวัน
        $dailySales = [];

        $period = Carbon::parse($startDate)->daysUntil($endDate);
        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $dailySales[$dateString] = 0;
        }

        $salesData = \App\Models\OrderItem::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'delivered')
            ->selectRaw('DATE(created_at) as date, SUM(price) as total')
            ->groupBy('date')
            ->get();

        foreach ($salesData as $data) {
            $dailySales[$data->date] = (float) $data->total;
        }

        // สรุปยอดขายรายเดือน
        $monthlySales = [];

        $startMonth = Carbon::parse($startDate)->startOfMonth();
        $endMonth = Carbon::parse($endDate)->endOfMonth();
        $period = Carbon::parse($startMonth)->monthsUntil($endMonth);

        foreach ($period as $date) {
            $monthString = $date->format('Y-m');
            $monthlySales[$monthString] = 0;
        }

        $salesData = \App\Models\OrderItem::whereBetween('created_at', [$startMonth, $endMonth])
            ->where('status', 'delivered')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(price) as total')
            ->groupBy('month')
            ->get();

        foreach ($salesData as $data) {
            $monthlySales[$data->month] = (float) $data->total;
        }

        return view('admin.reports.sales', compact(
            'startDate',
            'endDate',
            'salesByCategory',
            'salesByType',
            'dailySales',
            'monthlySales'
        ));
    }

    /**
     * แสดงรายงานผู้ใช้
     */
    public function users(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfDay();

        // จำนวนผู้ใช้ใหม่รายวัน
        $dailyNewUsers = [];

        $period = Carbon::parse($startDate)->daysUntil($endDate);
        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $dailyNewUsers[$dateString] = 0;
        }

        $userData = User::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->get();

        foreach ($userData as $data) {
            $dailyNewUsers[$data->date] = (int) $data->total;
        }

        // จำนวนผู้ใช้แต่ละประเภท
        $usersByRole = User::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('role, COUNT(*) as total')
            ->groupBy('role')
            ->get();

        // ผู้ใช้ที่ซื้อมากที่สุด
        $topBuyers = User::withCount(['orders as total_orders' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }])
            ->withSum(['transactions as total_spent' => function ($query) use ($startDate, $endDate) {
                $query->where('type', 'payment')
                    ->where('status', 'successful')
                    ->whereBetween('created_at', [$startDate, $endDate]);
            }], 'amount')
            ->orderBy('total_spent', 'desc')
            ->take(10)
            ->get();

        // อัตราการเติบโตของผู้ใช้เทียบกับเดือนก่อน
        $currentMonthUsers = User::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()])
            ->count();
        $lastMonthUsers = User::whereBetween('created_at', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()])
            ->count();

        $userGrowthRate = $lastMonthUsers > 0 ? (($currentMonthUsers - $lastMonthUsers) / $lastMonthUsers) * 100 : 0;

        return view('admin.reports.users', compact(
            'startDate',
            'endDate',
            'dailyNewUsers',
            'usersByRole',
            'topBuyers',
            'currentMonthUsers',
            'lastMonthUsers',
            'userGrowthRate'
        ));
    }

    /**
     * ส่งออกรายงานเป็น CSV
     */
    public function exportSalesCsv(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfDay();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sales_report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($startDate, $endDate) {
            $file = fopen('php://output', 'w');

            // หัวตาราง
            fputcsv($file, ['วันที่', 'หมายเลขออเดอร์', 'ชื่อสินค้า', 'หมวดหมู่', 'ราคา', 'ผู้ซื้อ', 'ผู้ขาย']);

            // ข้อมูล
            \App\Models\OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->join('users as buyers', 'orders.user_id', '=', 'buyers.id')
                ->join('users as sellers', 'products.user_id', '=', 'sellers.id')
                ->whereBetween('order_items.created_at', [$startDate, $endDate])
                ->where('order_items.status', 'delivered')
                ->orderBy('order_items.created_at', 'desc')
                ->select(
                    'order_items.created_at',
                    'orders.order_number',
                    'products.name',
                    'categories.name as category_name',
                    'order_items.price',
                    'buyers.name as buyer_name',
                    'sellers.name as seller_name'
                )
                ->chunk(100, function ($sales) use ($file) {
                    foreach ($sales as $sale) {
                        fputcsv($file, [
                            $sale->created_at->format('Y-m-d H:i:s'),
                            $sale->order_number,
                            $sale->name,
                            $sale->category_name,
                            $sale->price,
                            $sale->buyer_name,
                            $sale->seller_name
                        ]);
                    }
                });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
