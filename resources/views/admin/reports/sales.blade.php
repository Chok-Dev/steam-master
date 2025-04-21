@extends('layouts.app')

@section('title', 'รายงานการขาย')
@section('subtitle', 'รายงานยอดขายและวิเคราะห์ข้อมูลการขาย')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">หน้าหลัก</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">แดชบอร์ดแอดมิน</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.reports.overview') }}">รายงาน</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">รายงานการขาย</li>
@endsection

@section('content')
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">ตัวกรองรายงาน</h3>
        </div>
        <div class="block-content">
            <form action="{{ route('admin.reports.sales') }}" method="GET" class="row">
                <div class="col-md-4">
                    <div class="mb-4">
                        <label class="form-label" for="start_date">วันที่เริ่มต้น</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-4">
                        <label class="form-label" for="end_date">วันที่สิ้นสุด</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-4">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex">
                            <button type="submit" class="btn btn-alt-primary w-100 me-2">
                                <i class="fa fa-search me-1"></i> แสดงรายงาน
                            </button>
                            <a href="{{ route('admin.reports.export.sales', request()->all()) }}" class="btn btn-alt-success">
                                <i class="fa fa-file-excel me-1"></i> ส่งออก
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- สรุปยอดขายตามหมวดหมู่ -->
        <div class="col-md-6">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">ยอดขายตามหมวดหมู่</h3>
                </div>
                <div class="block-content">
                    <div style="height: 300px;">
                        <canvas id="sales-by-category-chart"></canvas>
                    </div>
                    <div class="table-responsive mt-4">
                        <table class="table table-sm table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th>หมวดหมู่</th>
                                    <th class="text-end">ยอดขาย</th>
                                    <th class="text-end">สัดส่วน</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalCategorySales = $salesByCategory->sum('total_sales');
                                @endphp
                                @foreach($salesByCategory as $category)
                                    <tr>
                                        <td>{{ $category->name }}</td>
                                        <td class="text-end">{{ number_format($category->total_sales, 2) }} ฿</td>
                                        <td class="text-end">{{ number_format(($category->total_sales / ($totalCategorySales ?: 1)) * 100, 2) }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>รวม</th>
                                    <th class="text-end">{{ number_format($totalCategorySales, 2) }} ฿</th>
                                    <th class="text-end">100.00%</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- สรุปยอดขายตามประเภทสินค้า -->
        <div class="col-md-6">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">ยอดขายตามประเภทสินค้า</h3>
                </div>
                <div class="block-content">
                    <div style="height: 300px;">
                        <canvas id="sales-by-type-chart"></canvas>
                    </div>
                    <div class="table-responsive mt-4">
                        <table class="table table-sm table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th>ประเภท</th>
                                    <th class="text-end">ยอดขาย</th>
                                    <th class="text-end">สัดส่วน</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalTypeSales = $salesByType->sum('total_sales');
                                @endphp
                                @foreach($salesByType as $type)
                                    <tr>
                                        <td>
                                            @if($type->type === 'steam_key')
                                                <span class="badge bg-primary">Steam Key</span>
                                            @elseif($type->type === 'origin_key')
                                                <span class="badge bg-info">Origin Key</span>
                                            @elseif($type->type === 'gog_key')
                                                <span class="badge bg-success">GOG Key</span>
                                            @elseif($type->type === 'uplay_key')
                                                <span class="badge bg-warning">Uplay Key</span>
                                            @elseif($type->type === 'battlenet_key')
                                                <span class="badge bg-danger">Battle.net Key</span>
                                            @elseif($type->type === 'account')
                                                <span class="badge bg-dark">Account</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $type->type }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format($type->total_sales, 2) }} ฿</td>
                                        <td class="text-end">{{ number_format(($type->total_sales / ($totalTypeSales ?: 1)) * 100, 2) }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>รวม</th>
                                    <th class="text-end">{{ number_format($totalTypeSales, 2) }} ฿</th>
                                    <th class="text-end">100.00%</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- กราฟยอดขายรายวัน/รายเดือน -->
    <div class="row">
        <div class="col-md-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">แนวโน้มยอดขาย</h3>
                    <div class="block-options">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-alt-secondary" id="view-daily-btn">รายวัน</button>
                            <button type="button" class="btn btn-alt-secondary" id="view-monthly-btn">รายเดือน</button>
                        </div>
                    </div>
                </div>
                <div class="block-content">
                    <div id="daily-chart-container" style="height: 400px;">
                        <canvas id="daily-sales-chart"></canvas>
                    </div>
                    <div id="monthly-chart-container" style="height: 400px; display: none;">
                        <canvas id="monthly-sales-chart"></canvas>
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
        // Chart.js Global Configuration
        Chart.defaults.color = '#666';
        Chart.defaults.font.family = 'IBM Plex Sans Thai, sans-serif';
        
        // กราฟยอดขายตามหมวดหมู่
        var categoryCtx = document.getElementById('sales-by-category-chart').getContext('2d');
        var categoryData = @json($salesByCategory);
        var categoryChart = new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: categoryData.map(item => item.name),
                datasets: [{
                    data: categoryData.map(item => item.total_sales),
                    backgroundColor: [
                        'rgba(78, 115, 223, 0.8)',
                        'rgba(28, 200, 138, 0.8)',
                        'rgba(54, 185, 204, 0.8)',
                        'rgba(246, 194, 62, 0.8)',
                        'rgba(231, 74, 59, 0.8)',
                        'rgba(104, 109, 224, 0.8)',
                        'rgba(156, 136, 255, 0.8)',
                        'rgba(242, 19, 93, 0.8)',
                        'rgba(0, 206, 201, 0.8)',
                        'rgba(250, 130, 49, 0.8)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12
                        }
                    }
                },
                cutout: '70%'
            }
        });
        
        // กราฟยอดขายตามประเภทสินค้า
        var typeCtx = document.getElementById('sales-by-type-chart').getContext('2d');
        var typeData = @json($salesByType);
        var typeChart = new Chart(typeCtx, {
            type: 'doughnut',
            data: {
                labels: typeData.map(item => {
                    if (item.type === 'steam_key') return 'Steam Key';
                    if (item.type === 'origin_key') return 'Origin Key';
                    if (item.type === 'gog_key') return 'GOG Key';
                    if (item.type === 'uplay_key') return 'Uplay Key';
                    if (item.type === 'battlenet_key') return 'Battle.net Key';
                    if (item.type === 'account') return 'Account';
                    return item.type;
                }),
                datasets: [{
                    data: typeData.map(item => item.total_sales),
                    backgroundColor: [
                        'rgba(78, 115, 223, 0.8)',
                        'rgba(28, 200, 138, 0.8)',
                        'rgba(54, 185, 204, 0.8)',
                        'rgba(246, 194, 62, 0.8)',
                        'rgba(231, 74, 59, 0.8)',
                        'rgba(104, 109, 224, 0.8)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12
                        }
                    }
                },
                cutout: '70%'
            }
        });
        
        // กราฟยอดขายรายวัน
        var dailyCtx = document.getElementById('daily-sales-chart').getContext('2d');
        var dailyData = @json($dailySales);
        var dailyChart = new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: Object.keys(dailyData),
                datasets: [{
                    label: 'ยอดขายประจำวัน (บาท)',
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    data: Object.values(dailyData),
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                maintainAspectRatio: false,
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
        
        // กราฟยอดขายรายเดือน
        var monthlyCtx = document.getElementById('monthly-sales-chart').getContext('2d');
        var monthlyData = @json($monthlySales);
        var monthlyChart = new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(monthlyData).map(month => {
                    let [year, monthNum] = month.split('-');
                    return ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'][parseInt(monthNum) - 1] + ' ' + year;
                }),
                datasets: [{
                    label: 'ยอดขายประจำเดือน (บาท)',
                    backgroundColor: 'rgba(78, 115, 223, 0.8)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 1,
                    data: Object.values(monthlyData)
                }]
            },
            options: {
                maintainAspectRatio: false,
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
        
        // สลับระหว่างกราฟรายวัน/รายเดือน
        document.getElementById('view-daily-btn').addEventListener('click', function() {
            document.getElementById('daily-chart-container').style.display = 'block';
            document.getElementById('monthly-chart-container').style.display = 'none';
            this.classList.add('active');
            document.getElementById('view-monthly-btn').classList.remove('active');
        });
        
        document.getElementById('view-monthly-btn').addEventListener('click', function() {
            document.getElementById('daily-chart-container').style.display = 'none';
            document.getElementById('monthly-chart-container').style.display = 'block';
            this.classList.add('active');
            document.getElementById('view-daily-btn').classList.remove('active');
        });
        
        // Set default active button
        document.getElementById('view-daily-btn').classList.add('active');
    });
</script>
@endpush