@extends('layouts.app')

@section('title', 'จัดการผู้ใช้')
@section('subtitle', 'จัดการข้อมูลผู้ใช้ทั้งหมดในระบบ')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">หน้าหลัก</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">แดชบอร์ดแอดมิน</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">จัดการผู้ใช้</li>
@endsection

@section('content')
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">ผู้ใช้ทั้งหมด</h3>
            <div class="block-options">
                <a href="{{ route('admin.users.index') }}" class="btn btn-alt-secondary">
                    <i class="fa fa-refresh me-1"></i> รีเฟรช
                </a>
            </div>
        </div>
        <div class="block-content">
            <!-- ฟิลเตอร์และการค้นหา -->
            <div class="mb-4">
                <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-body">
                                <i class="fa fa-search"></i>
                            </span>
                            <input type="text" class="form-control" name="search" placeholder="ค้นหาชื่อหรืออีเมล" value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="role">
                            <option value="">ทุกประเภท</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>แอดมิน</option>
                            <option value="seller" {{ request('role') == 'seller' ? 'selected' : '' }}>ผู้ขาย</option>
                            <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>ผู้ใช้ทั่วไป</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="is_verified">
                            <option value="">ทุกสถานะยืนยัน</option>
                            <option value="1" {{ request('is_verified') == '1' ? 'selected' : '' }}>ยืนยันแล้ว</option>
                            <option value="0" {{ request('is_verified') == '0' ? 'selected' : '' }}>ยังไม่ยืนยัน</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="sort">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>ล่าสุด</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>เก่าสุด</option>
                            <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>ชื่อ (A-Z)</option>
                            <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>ชื่อ (Z-A)</option>
                            <option value="email_asc" {{ request('sort') == 'email_asc' ? 'selected' : '' }}>อีเมล (A-Z)</option>
                            <option value="email_desc" {{ request('sort') == 'email_desc' ? 'selected' : '' }}>อีเมล (Z-A)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex">
                            <button type="submit" class="btn btn-alt-primary me-2">
                                <i class="fa fa-filter me-1"></i> กรอง
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-alt-secondary">
                                <i class="fa fa-times me-1"></i> ล้าง
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- ตารางผู้ใช้ -->
            <div class="table-responsive">
                <table class="table table-striped table-vcenter">
                    <thead>
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>ผู้ใช้</th>
                            <th>อีเมล</th>
                            <th>บทบาท</th>
                            <th>สถานะ</th>
                            <th>วันที่ลงทะเบียน</th>
                            <th>เข้าระบบล่าสุด</th>
                            <th class="text-center" style="width: 150px;">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <img class="img-avatar img-avatar32" src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('assets/codebase/media/avatars/avatar10.jpg') }}" alt="{{ $user->name }}">
                                        </div>
                                        <div class="ms-2">{{ $user->name }}</div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->role === 'admin')
                                        <span class="badge bg-primary">แอดมิน</span>
                                    @elseif($user->role === 'seller')
                                        <span class="badge bg-success">ผู้ขาย</span>
                                    @else
                                        <span class="badge bg-info">ผู้ใช้</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->is_verified)
                                        <span class="badge bg-success">ยืนยันแล้ว</span>
                                    @else
                                        <span class="badge bg-warning">ยังไม่ยืนยัน</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                <td>{{ $user->last_active_at ? $user->last_active_at->format('d/m/Y H:i') : 'ไม่มีข้อมูล' }}</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="ดูรายละเอียด">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="แก้ไข">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.users.toggle-verification', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $user->is_verified ? 'btn-alt-warning' : 'btn-alt-success' }}" data-bs-toggle="tooltip" title="{{ $user->is_verified ? 'ยกเลิกการยืนยัน' : 'ยืนยันตัวตน' }}">
                                                <i class="fa {{ $user->is_verified ? 'fa-times' : 'fa-check' }}"></i>
                                            </button>
                                        </form>
                                        @if(!$user->products()->exists() && !$user->orders()->exists() && !$user->transactions()->exists() && $user->role !== 'admin')
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบผู้ใช้นี้?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-alt-danger" data-bs-toggle="tooltip" title="ลบ">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">ไม่พบข้อมูลผู้ใช้</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- การแบ่งหน้า -->
            <div class="d-flex justify-content-center mt-4">
                {{ $users->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
@endsection