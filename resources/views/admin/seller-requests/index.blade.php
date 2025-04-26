@extends('layouts.app')

@section('title', 'จัดการคำขอเป็นผู้ขาย')
@section('subtitle', 'พิจารณาและจัดการคำขอเป็นผู้ขายในระบบ')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">หน้าหลัก</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">แดชบอร์ดแอดมิน</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">คำขอเป็นผู้ขาย</li>
@endsection

@section('content')
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">คำขอเป็นผู้ขายทั้งหมด</h3>
            <div class="block-options">
                <a href="{{ route('admin.seller-requests.index') }}" class="btn btn-alt-secondary">
                    <i class="fa fa-refresh me-1"></i> รีเฟรช
                </a>
            </div>
        </div>
        <div class="block-content">
            <!-- ฟิลเตอร์และการค้นหา -->
            <div class="mb-4">
                <form action="{{ route('admin.seller-requests.index') }}" method="GET" class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-body">
                                <i class="fa fa-search"></i>
                            </span>
                            <input type="text" class="form-control" name="search" placeholder="ค้นหาชื่อหรืออีเมล" value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="sort">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>ล่าสุด</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>เก่าสุด</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex">
                            <button type="submit" class="btn btn-alt-primary me-2">
                                <i class="fa fa-filter me-1"></i> กรอง
                            </button>
                            <a href="{{ route('admin.seller-requests.index') }}" class="btn btn-alt-secondary">
                                <i class="fa fa-times me-1"></i> ล้าง
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- ตารางคำขอเป็นผู้ขาย -->
            <div class="table-responsive">
                <table class="table table-striped table-vcenter">
                    <thead>
                        <tr>
                            <th>ผู้ใช้</th>
                            <th>อีเมล</th>
                            <th>ชื่อร้านค้า</th>
                            <th>วันที่ขอ</th>
                            <th class="text-center">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sellerRequests as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <img class="img-avatar img-avatar32" src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('assets/codebase/media/avatars/avatar10.jpg') }}" alt="{{ $user->name }}">
                                        </div>
                                        <div class="ms-2">{{ $user->name }}</div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->seller_details['name'] ?? $user->name . ' Shop' }}</td>
                                <td>{{ $user->seller_request_at ? Carbon\Carbon::parse($user->seller_request_at)->format('d/m/Y H:i') : '-' }}</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-alt-success" data-bs-toggle="modal" data-bs-target="#modal-view-request-{{ $user->id }}">
                                            <i class="fa fa-eye me-1"></i> ดูรายละเอียด
                                        </button>
                                    </div>
                                    
                                    <!-- Modal ดูรายละเอียดคำขอ -->
                                    <div class="modal fade" id="modal-view-request-{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="modal-view-request-{{ $user->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">รายละเอียดคำขอเป็นผู้ขาย</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-4">
                                                        <div class="d-flex align-items-center mb-3">
                                                            <div class="flex-shrink-0">
                                                                <img class="img-avatar img-avatar64" src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('assets/codebase/media/avatars/avatar10.jpg') }}" alt="{{ $user->name }}">
                                                            </div>
                                                            <div class="ms-3">
                                                                <h4 class="mb-0">{{ $user->name }}</h4>
                                                                <p class="mb-0 text-muted">{{ $user->email }}</p>
                                                                <p class="mb-0 text-muted">สมัครเมื่อ: {{ $user->created_at->format('d/m/Y') }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mb-4">
                                                        <h5>ชื่อร้านค้า</h5>
                                                        <p>{{ json_decode($user->seller_details)->name ?? $user->name . ' Shop' }}</p>
                                                    </div>
                                                    
                                                    <div class="mb-4">
                                                        <h5>คำอธิบายร้านค้า</h5>
                                                        <div class="p-3 bg-body-light rounded text-wrap">
                                                            <p class="mb-0 ">{{ json_decode($user->seller_details)->description  }}</p>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mb-4">
                                                        <h5>ข้อมูลเพิ่มเติม</h5>
                                                        <div class="table-responsive">
                                                            <table class="table table-borderless">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="fw-semibold" style="width: 30%;">ยืนยันอีเมล</td>
                                                                        <td>
                                                                            @if($user->email_verified_at)
                                                                                <span class="badge bg-success">ยืนยันแล้ว</span>
                                                                            @else
                                                                                <span class="badge bg-warning">ยังไม่ยืนยัน</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="fw-semibold">ยืนยันตัวตน</td>
                                                                        <td>
                                                                            @if($user->is_verified)
                                                                                <span class="badge bg-success">ยืนยันแล้ว</span>
                                                                            @else
                                                                                <span class="badge bg-warning">ยังไม่ยืนยัน</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="fw-semibold">วันที่ขอ</td>
                                                                        <td>{{ $user->seller_request_at ? Carbon\Carbon::parse($user->seller_request_at)->format('d/m/Y H:i') : '-' }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- แบบฟอร์มการอนุมัติ/ปฏิเสธ -->
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <form action="{{ route('admin.seller-requests.approve', $user) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success w-100">
                                                                    <i class="fa fa-check me-1"></i> อนุมัติคำขอ
                                                                </button>
                                                            </form>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#modal-reject-request-{{ $user->id }}">
                                                                <i class="fa fa-times me-1"></i> ปฏิเสธคำขอ
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-alt-secondary" data-bs-dismiss="modal">ปิด</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Modal ปฏิเสธคำขอ -->
                                    <div class="modal fade" id="modal-reject-request-{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="modal-reject-request-{{ $user->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.seller-requests.reject', $user) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">ปฏิเสธคำขอเป็นผู้ขาย</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>คุณกำลังจะปฏิเสธคำขอเป็นผู้ขายของ <strong>{{ $user->name }}</strong></p>
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label" for="rejection_reason">เหตุผลในการปฏิเสธ (ถ้ามี)</label>
                                                            <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" placeholder="ระบุเหตุผลในการปฏิเสธคำขอนี้ เพื่อแจ้งให้ผู้ใช้ทราบ"></textarea>
                                                        </div>
                                                        
                                                        <div class="alert alert-warning">
                                                            <i class="fa fa-exclamation-triangle me-1"></i> การปฏิเสธคำขอจะแจ้งให้ผู้ใช้ทราบ ผู้ใช้สามารถยื่นคำขอใหม่ได้ในภายหลัง
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-alt-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                                        <button type="submit" class="btn btn-danger">
                                                            <i class="fa fa-times me-1"></i> ยืนยันการปฏิเสธ
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">ไม่พบคำขอเป็นผู้ขายที่รอการพิจารณา</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- การแบ่งหน้า -->
            <div class="d-flex justify-content-center mt-4">
                {{ $sellerRequests->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
@endsection