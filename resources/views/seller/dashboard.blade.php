@extends('layouts.app')

@section('title', 'แดชบอร์ดผู้ขาย')
@section('subtitle', 'ภาพรวมการขายของคุณ')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">หน้าหลัก</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">แดชบอร์ดผู้ขาย</li>
@endsection

@section('content')
    <!-- สรุปภาพรวม -->
    <div class="row">
        <div class="col-6 col-md-3">
            <div class="block block-rounded">
                <div class="block-content">
                    <div class="py-4 text-center">
                        <div class="fs-1 fw-bold">{{ number_format($totalBalance, 2) }} ฿</div>
                        <div class="fs-sm fw-semibold text-uppercase text-muted">ยอดเงินคงเหลือ</div>
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
                        <div class="fs-1 fw-bold">{{ $totalSold }}</div>
                        <div class="fs-sm fw-semibold text-uppercase text-muted">ขายแล้ว</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="block block-rounded">
                <div class="block-content">
                    <div class="py-4 text-center">
                        <div class="fs-1 fw-bold">{{ number_format($averageRating, 1) }}</div>
                        <div class="fs-sm fw-semibold text-uppercase text-muted">คะแนนเฉลี่ย</div>
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
                    <h3 class="block-title">ยอดขายล่าสุด</h3>
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
                    <h3 class="block-title">สัดส่วนหมวดหมู่</h3>
                </div>
                <div class="block-content block-content-full">
                    <div class="py-3 px-4" style="height: 360px;">
                        <canvas id="categories-chart"></canvas>
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
                        <a href="{{ route('seller.orders.index') }}" class="btn btn-sm btn-alt-primary">
                            <i class="fa fa-eye me-1"></i> ดูทั้งหมด
                        </a>
                    </div>
                </div>
                <div class="block-content">
                    <table class="table table-striped table-vcenter">
                        <thead>
                            <tr>
                                <th>ออเดอร์</th>
                                <th>สินค้า</th>
                                <th>ราคา</th>
                                <th>สถานะ</th>
                                <th class="text-center" style="width: 80px;">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $item)
                                <tr>
                                    <td>
                                        #{{ $item->order->order_number }}
                                        <div class="fs-sm text-muted">{{ $item->order->created_at->format('d/m/Y') }}</div>
                                    </td>
                                    <td>{{ Str::limit($item->product->name, 20) }}</td>
                                    <td>{{ number_format($item->price, 2) }} ฿</td>
                                    <td>
                                        @if($item->status === 'pending')
                                            <span class="badge bg-warning">รอส่งมอบ</span>
                                        @elseif($item->status === 'delivered')
                                            <span class="badge bg-success">ส่งมอบแล้ว</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $item->status }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('seller.orders.show', $item->order) }}" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="ดูรายละเอียด">
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
        
        <!-- ธุรกรรมล่าสุด -->
        <div class="col-md-6">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">ธุรกรรมล่าสุด</h3>
                    <div class="block-options">
                        <a href="{{ route('seller.transactions') }}" class="btn btn-sm btn-alt-primary">
                            <i class="fa fa-eye me-1"></i> ดูทั้งหมด
                        </a>
                    </div>
                </div>
                <div class="block-content">
                    <table class="table table-striped table-vcenter">
                        <thead>
                            <tr>
                                <th>รหัส</th>
                                <th>วันที่</th>
                                <th>ประเภท</th>
                                <th>จำนวนเงิน</th>
                                <th>สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->transaction_id }}</td>
                                    <td>{{ $transaction->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        @if($transaction->type === 'payment')
                                            <span class="badge bg-primary">ชำระเงิน</span>
                                        @elseif($transaction->type === 'payout')
                                            <span class="badge bg-success">รับเงิน</span>
                                        @elseif($transaction->type === 'refund')
                                            <span class="badge bg-warning">คืนเงิน</span>
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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">ยังไม่มีธุรกรรม</td>
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
        
        // กราฟหมวดหมู่
        var categoriesCtx = document.getElementById('categories-chart').getContext('2d');
        var categoriesChart = new Chart(categoriesCtx, {
            type: 'doughnut',
            data: {
                labels: @json($categoriesChartData['labels']),
                datasets: [{
                    data: @json($categoriesChartData['data']),
                    backgroundColor: [
                        'rgba(78, 115, 223, 0.8)',
                        'rgba(28, 200, 138, 0.8)',
                        'rgba(54, 185, 204, 0.8)',
                        'rgba(246, 194, 62, 0.8)',
                        'rgba(231, 74, 59, 0.8)'
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