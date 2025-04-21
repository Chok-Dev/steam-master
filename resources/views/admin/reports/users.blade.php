@extends('layouts.app')

@section('title', 'รายงานข้อมูลผู้ใช้')
@section('subtitle', 'การวิเคราะห์และสถิติของผู้ใช้งานในระบบ')

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
    <li class="breadcrumb-item active" aria-current="page">รายงานข้อมูลผู้ใช้</li>
@endsection

@section('content')
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">ตัวกรองรายงาน</h3>
        </div>
        <div class="block-content">
            <form action="{{ route('admin.reports.users') }}" method="GET" class="row">
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
                        <button type="submit" class="btn btn-alt-primary w-100">
                            <i class="fa fa-search me-1"></i> แสดงรายงาน
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- สรุปผู้ใช้ -->
        <div class="col-md-6">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">ข้อมูลผู้ใช้ในช่วงเวลาที่เลือก</h3>
                </div>
                <div class="block-content">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="py-3">
                                <div class="item item-circle bg-primary-light mx-auto">
                                    <i class="fa fa-users text-primary"></i>
                                </div>
                                <div class="fs-1 fw-bold mt-3">{{ $currentMonthUsers }}</div>
                                <div class="fs-sm fw-semibold text-uppercase text-muted">ผู้ใช้ใหม่ทั้งหมด</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="py-3">
                                <div class="item item-circle bg-success-light mx-auto">
                                    <i class="fa fa-chart-line text-success"></i>
                                </div>
                                <div class="fs-1 fw-bold mt-3">{{ number_format($userGrowthRate, 2) }}%</div>
                                <div class="fs-sm fw-semibold text-uppercase text-muted">อัตราการเติบโต</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="py-3">
                                <div class="item item-circle bg-info-light mx-auto">
                                    <i class="fa fa-store text-info"></i>
                                </div>
                                <div class="fs-1 fw-bold mt-3">{{ $usersByRole->where('role', 'seller')->first()->total ?? 0 }}</div>
                                <div class="fs-sm fw-semibold text-uppercase text-muted">ผู้ขายใหม่</div>
                            </div>
                        </div>
                    </div>

                    <div class="block block-rounded mb-0">
                        <div class="block-header block-header-default">
                            <h3 class="block-title fs-sm">ผู้ใช้ตามประเภท</h3>
                        </div>
                        <div class="block-content bg-body-light">
                            <div style="height: 200px;">
                                <canvas id="users-by-role-chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- แนวโน้มผู้ใช้ใหม่ -->
        <div class="col-md-6">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">แนวโน้มผู้ใช้ใหม่</h3>
                </div>
                <div class="block-content">
                    <div style="height: 350px;">
                        <canvas id="new-users-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- ผู้ซื้อที่มียอดสั่งซื้อสูงสุด -->
        <div class="col-md-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">ผู้ซื้อยอดเยี่ยม</h3>
                </div>
                <div class="block-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th>ผู้ใช้</th>
                                    <th>อีเมล</th>
                                    <th>จำนวนออเดอร์</th>
                                    <th>ยอดใช้จ่าย</th>
                                    <th>วันที่ลงทะเบียน</th>
                                    <th class="text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topBuyers as $buyer)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-2">
                                                    <img class="img-avatar img-avatar48" src="{{ $buyer->avatar ? asset('storage/' . $buyer->avatar) : asset('media/avatars/avatar10.jpg') }}" alt="{{ $buyer->name }}">
                                                </div>
                                                <div>{{ $buyer->name }}</div>
                                            </div>
                                        </td>
                                        <td>{{ $buyer->email }}</td>
                                        <td>{{ $buyer->total_orders }}</td>
                                        <td>{{ number_format($buyer->total_spent ?? 0, 2) }} ฿</td>
                                        <td>{{ $buyer->created_at->format('d/m/Y') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.users.show', $buyer) }}" class="btn btn-sm btn-alt-primary">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">ไม่พบข้อมูล</td>
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
        // Chart.js Global Configuration
        Chart.defaults.color = '#666';
        Chart.defaults.font.family = 'IBM Plex Sans Thai, sans-serif';
        
        // กราฟผู้ใช้ตามประเภท
        var roleCtx = document.getElementById('users-by-role-chart').getContext('2d');
        var roleData = @json($usersByRole);
        var roleChart = new Chart(roleCtx, {
            type: 'pie',
            data: {
                labels: roleData.map(item => {
                    if (item.role === 'admin') return 'แอดมิน';
                    if (item.role === 'seller') return 'ผู้ขาย';
                    if (item.role === 'user') return 'ผู้ใช้ทั่วไป';
                    return item.role;
                }),
                datasets: [{
                    data: roleData.map(item => item.total),
                    backgroundColor: [
                        'rgba(78, 115, 223, 0.8)',
                        'rgba(28, 200, 138, 0.8)',
                        'rgba(54, 185, 204, 0.8)'
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
                }
            }
        });
        
        // กราฟแนวโน้มผู้ใช้ใหม่
        var newUsersCtx = document.getElementById('new-users-chart').getContext('2d');
        var newUsersData = @json($dailyNewUsers);
        var newUsersChart = new Chart(newUsersCtx, {
            type: 'line',
            data: {
                labels: Object.keys(newUsersData),
                datasets: [{
                    label: 'จำนวนผู้ใช้ใหม่',
                    backgroundColor: 'rgba(54, 185, 204, 0.05)',
                    borderColor: 'rgba(54, 185, 204, 1)',
                    data: Object.values(newUsersData),
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
                        },
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    });
</script>
@endpush