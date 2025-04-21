@extends('layouts.app')

@section('title', 'แดชบอร์ดแอดมิน')
@section('subtitle', 'ภาพรวมการจัดการระบบ')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">หน้าหลัก</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">แดชบอร์ดแอดมิน</li>
@endsection

@section('content')
    <!-- สรุปภาพรวม -->
    <div class="row">
        <div class="col-6 col-md-3">
            <div class="block block-rounded">
                <div class="block-content">
                    <div class="py-4 text-center">
                        <div class="fs-1 fw-bold">{{ $totalUsers }}</div>
                        <div class="fs-sm fw-semibold text-uppercase text-muted">ผู้ใช้ทั้งหมด</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="block block-rounded">
                <div class="block-content">
                    <div class="py-4 text-center">
                        <div class="fs-1 fw-bold">{{ $totalSellers }}</div>
                        <div class="fs-sm fw-semibold text-uppercase text-muted">ผู้ขาย</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="block block-rounded">
                <div class="block-content">
                    <div class="py-4 text-center">
                        <div class="fs-1 fw-bold">{{ $totalProducts }}</div>
                        <div class="fs-sm fw-semibold text-uppercase text-muted">สินค้าทั้งหมด</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="block block-rounded">
                <div class="block-content">
                    <div class="py-4 text-center">
                        <div class="fs-1 fw-bold">{{ number_format($totalSales, 2) }} ฿</div>
                        <div class="fs-sm fw-semibold text-uppercase text-muted">ยอดขายทั้งหมด</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- กราฟและสถิติ -->
    <div class="row">
        <div class="col-md-8">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">ยอดขายล่าสุด (7 วันล่าสุด)</h3>
                </div>
                <div class="block-content block-content-full">
                    <div class="pt-3 px-4" style="height: 360px;">
                        <canvas id="sales-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">ออเดอร์ตามสถานะ</h3>
                </div>
                <div class="block-content block-content-full">
                    <div class="py-3 px-4" style="height: 360px;">
                        <canvas id="orders-status-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- ออเดอร์ล่าสุด -->
        <div class="col-md-6">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">ออเดอร์ล่าสุด</h3>
                    <div class="block-options">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-alt-primary">
                            <i class="fa fa-eye me-1"></i> ดูทั้งหมด
                        </a>
                    </div>
                </div>
                <div class="block-content">
                    <table class="table table-striped table-vcenter">
                        <thead>
                            <tr>
                                <th>ออเดอร์</th>
                                <th>ผู้ซื้อ</th>
                                <th>มูลค่า</th>
                                <th>สถานะ</th>
                                <th class="text-center" style="width: 80px;">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td>
                                        #{{ $order->order_number }}
                                        <div class="fs-sm text-muted">{{ $order->created_at->format('d/m/Y') }}</div>
                                    </td>
                                    <td>{{ $order->user->name }}</td>
                                    <td>{{ number_format($order->total_amount, 2) }} ฿</td>
                                    <td>
                                        @if($order->status === 'pending')
                                            <span class="badge bg-warning">รอดำเนินการ</span>
                                        @elseif($order->status === 'processing')
                                            <span class="badge bg-info">กำลังดำเนินการ</span>
                                        @elseif($order->status === 'completed')
                                            <span class="badge bg-success">สำเร็จ</span>
                                        @elseif($order->status === 'canceled')
                                            <span class="badge bg-danger">ยกเลิก</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $order->status }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="ดูรายละเอียด">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">ยังไม่มีออเดอร์</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- ผู้ใช้ลงทะเบียนล่าสุด -->
        <div class="col-md-6">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">ผู้ใช้ลงทะเบียนล่าสุด</h3>
                    <div class="block-options">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-alt-primary">
                            <i class="fa fa-eye me-1"></i> ดูทั้งหมด
                        </a>
                    </div>
                </div>
                <div class="block-content">
                    <table class="table table-striped table-vcenter">
                        <thead>
                            <tr>
                                <th>ผู้ใช้</th>
                                <th>บทบาท</th>
                                <th>อีเมล</th>
                                <th>วันที่ลงทะเบียน</th>
                                <th class="text-center" style="width: 80px;">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentUsers as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <img class="img-avatar img-avatar32" src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('assets/codebase/media/avatars/avatar10.jpg') }}" alt="{{ $user->name }}">
                                            </div>
                                            <div class="ms-2">{{ $user->name }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($user->role === 'admin')
                                            <span class="badge bg-primary">แอดมิน</span>
                                        @elseif($user->role === 'seller')
                                            <span class="badge bg-success">ผู้ขาย</span>
                                        @else
                                            <span class="badge bg-info">ผู้ใช้</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="ดูรายละเอียด">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">ยังไม่มีผู้ใช้</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- ธุรกรรมล่าสุด -->
        <div class="col-md-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">ธุรกรรมล่าสุด</h3>
                    <div class="block-options">
                        <a href="{{ route('admin.transactions') }}" class="btn btn-sm btn-alt-primary">
                            <i class="fa fa-eye me-1"></i> ดูทั้งหมด
                        </a>
                    </div>
                </div>
                <div class="block-content">
                    <table class="table table-striped table-vcenter">
                        <thead>
                            <tr>
                                <th>รหัส</th>
                                <th>ผู้ใช้</th>
                                <th>ออเดอร์</th>
                                <th>ประเภท</th>
                                <th>จำนวนเงิน</th>
                                <th>สถานะ</th>
                                <th>วันที่</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->transaction_id }}</td>
                                    <td>{{ $transaction->user->name }}</td>
                                    <td>
                                        @if($transaction->order)
                                            <a href="{{ route('admin.orders.show', $transaction->order) }}" data-bs-toggle="tooltip" title="ดูรายละเอียดออเดอร์">
                                                #{{ $transaction->order->order_number }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->type === 'payment')
                                            <span class="badge bg-primary">ชำระเงิน</span>
                                        @elseif($transaction->type === 'payout')
                                            <span class="badge bg-success">จ่ายให้ผู้ขาย</span>
                                        @elseif($transaction->type === 'refund')
                                            <span class="badge bg-warning">คืนเงิน</span>
                                        @elseif($transaction->type === 'topup')
                                            <span class="badge bg-info">เติมเงิน</span>
                                        @elseif($transaction->type === 'withdrawal')
                                            <span class="badge bg-danger">ถอนเงิน</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $transaction->type }}</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($transaction->amount, 2) }} ฿</td>
                                    <td>
                                        @if($transaction->status === 'successful')
                                            <span class="badge bg-success">สำเร็จ</span>
                                        @elseif($transaction->status === 'pending')
                                            <span class="badge bg-warning">รอดำเนินการ</span>
                                        @elseif($transaction->status === 'failed')
                                            <span class="badge bg-danger">ล้มเหลว</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $transaction->status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">ยังไม่มีธุรกรรม</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // กราฟยอดขาย
        var salesCtx = document.getElementById('sales-chart').getContext('2d');
        var salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: @json($salesChartData['labels']),
                datasets: [{
                    label: 'ยอดขาย (บาท)',
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    data: @json($salesChartData['data']),
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    }
                }
            }
        });
        
        // กราฟสถานะออเดอร์
        var ordersStatusCtx = document.getElementById('orders-status-chart').getContext('2d');
        var ordersStatusChart = new Chart(ordersStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['รอดำเนินการ', 'กำลังดำเนินการ', 'สำเร็จ', 'ยกเลิก'],
                datasets: [{
                    data: [
                        {{ \App\Models\Order::where('status', 'pending')->count() }},
                        {{ \App\Models\Order::where('status', 'processing')->count() }},
                        {{ \App\Models\Order::where('status', 'completed')->count() }},
                        {{ \App\Models\Order::where('status', 'canceled')->count() }}
                    ],
                    backgroundColor: [
                        'rgba(255, 193, 7, 0.8)',  // warning - yellow
                        'rgba(23, 162, 184, 0.8)', // info - blue
                        'rgba(40, 167, 69, 0.8)',  // success - green
                        'rgba(220, 53, 69, 0.8)'   // danger - red
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                cutout: '70%'
            }
        });
    });
</script>
@endpush