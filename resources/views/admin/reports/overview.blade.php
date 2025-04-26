@extends('layouts.app')

@section('title', 'ภาพรวมระบบ')
@section('subtitle', 'รายงานภาพรวมของระบบทั้งหมด')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">หน้าหลัก</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">แดชบอร์ดแอดมิน</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">รายงานภาพรวม</li>
@endsection

@section('content')
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">ตัวกรองรายงาน</h3>
        </div>
        <div class="block-content">
            <form action="{{ route('admin.reports.overview') }}" method="GET" class="row">
                <div class="col-md-4">
                    <div class="mb-4">
                        <label class="form-label" for="start_date">วันที่เริ่มต้น</label>
                        <input type="date" class="form-control" id="start_date" name="start_date"
                            value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-4">
                        <label class="form-label" for="end_date">วันที่สิ้นสุด</label>
                        <input type="date" class="form-control" id="end_date" name="end_date"
                            value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-4">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-alt-primary w-100">
                            <i class="fa fa-search me-1"></i> แสดงรายงาน
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

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
                        <div class="fs-1 fw-bold">{{ $totalOrders }}</div>
                        <div class="fs-sm fw-semibold text-uppercase text-muted">ออเดอร์</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="block block-rounded">
                <div class="block-content">
                    <div class="py-4 text-center">
                        <div class="fs-1 fw-bold">{{ number_format($totalSales, 2) }} ฿</div>
                        <div class="fs-sm fw-semibold text-uppercase text-muted">ยอดขาย</div>
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
                    <h3 class="block-title">ยอดขายรายวัน</h3>
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
                    <h3 class="block-title">ยอดออเดอร์รายวัน</h3>
                </div>
                <div class="block-content block-content-full">
                    <div class="py-3 px-4" style="height: 360px;">
                        <canvas id="orders-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">สินค้าขายดี</h3>
                </div>
                <div class="block-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th>สินค้า</th>
                                    <th>ราคา</th>
                                    <th>หมวดหมู่</th>
                                    <th>จำนวนขาย</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts as $product)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.products.show', $product) }}">{{ $product->name }}</a>
                                        </td>
                                        <td>{{ number_format($product->price, 2) }} ฿</td>
                                        <td>{{ $product->category->name }}</td>
                                        <td>{{ $product->order_items_count }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">ไม่พบข้อมูล</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">ผู้ขายยอดเยี่ยม</h3>
                </div>
                <div class="block-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th>ผู้ขาย</th>
                                    <th>จำนวนขาย</th>
                                    <th>เข้าร่วมเมื่อ</th>
                                    <th>คะแนน</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topSellers as $seller)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.users.show', $seller) }}">{{ $seller->name }}</a>
                                        </td>
                                        <td>{{ $seller->sold_count }}</td>
                                        <td>{{ $seller->created_at->format('d/m/Y') }}</td>
                                        <td>{{ number_format($seller->average_rating, 1) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">ไม่พบข้อมูล</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/plugins/chart.js/chart.umd.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // กราฟยอดขาย
            var salesCtx = document.getElementById('sales-chart').getContext('2d');
            var salesChart = new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: @json(array_keys($dailySales)),
                    datasets: [{
                        label: 'ยอดขาย (บาท)',
                        backgroundColor: 'rgba(78, 115, 223, 0.05)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        data: @json(array_values($dailySales)),
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

            // กราฟออเดอร์
            var ordersCtx = document.getElementById('orders-chart').getContext('2d');
            var ordersChart = new Chart(ordersCtx, {
                type: 'line',
                data: {
                    labels: @json(array_keys($dailyOrders)),
                    datasets: [{
                        label: 'จำนวนออเดอร์',
                        backgroundColor: 'rgba(28, 200, 138, 0.05)',
                        borderColor: 'rgba(28, 200, 138, 1)',
                        data: @json(array_values($dailyOrders)),
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
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
