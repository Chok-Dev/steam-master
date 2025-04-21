@extends('layouts.app')

@section('title', 'แก้ไขข้อมูลผู้ใช้')
@section('subtitle', 'แก้ไขข้อมูลของ ' . $user->name)

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">หน้าหลัก</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">แดชบอร์ดแอดมิน</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.users.index') }}">จัดการผู้ใช้</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.users.show', $user) }}">{{ $user->name }}</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">แก้ไขข้อมูล</li>
@endsection

@section('content')
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">แก้ไขข้อมูลผู้ใช้</h3>
            <div class="block-options">
                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-alt-secondary">
                    <i class="fa fa-arrow-left me-1"></i> กลับ
                </a>
            </div>
        </div>
        <div class="block-content">
            <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label" for="name">ชื่อผู้ใช้</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label" for="email">อีเมล</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-4">
                            <label class="form-label" for="role">บทบาท</label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>ผู้ใช้ทั่วไป</option>
                                <option value="seller" {{ old('role', $user->role) == 'seller' ? 'selected' : '' }}>ผู้ขาย</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>แอดมิน</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-4">
                            <label class="form-label" for="is_verified">สถานะยืนยัน</label>
                            <select class="form-select @error('is_verified') is-invalid @enderror" id="is_verified" name="is_verified">
                                <option value="1" {{ old('is_verified', $user->is_verified) ? 'selected' : '' }}>ยืนยันแล้ว</option>
                                <option value="0" {{ old('is_verified', $user->is_verified) ? '' : 'selected' }}>ยังไม่ยืนยัน</option>
                            </select>
                            @error('is_verified')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-4">
                            <label class="form-label" for="balance">ยอดเงินในวอลเล็ต (บาท)</label>
                            <input type="number" class="form-control @error('balance') is-invalid @enderror" id="balance" name="balance" value="{{ old('balance', $user->balance) }}" min="0" step="0.01">
                            @error('balance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label" for="bio">ประวัติส่วนตัว</label>
                    <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="4">{{ old('bio', $user->bio) }}</textarea>
                    @error('bio')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label class="form-label" for="avatar">รูปโปรไฟล์</label>
                    <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar" accept="image/*">
                    @error('avatar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if($user->avatar)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="img-thumbnail" style="max-height: 100px;">
                        </div>
                    @endif
                </div>
                
                <h4 class="mt-4 mb-3">เปลี่ยนรหัสผ่าน</h4>
                <p class="text-muted">เว้นว่างไว้หากไม่ต้องการเปลี่ยนรหัสผ่าน</p>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label" for="password">รหัสผ่านใหม่</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" minlength="8">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label" for="password_confirmation">ยืนยันรหัสผ่านใหม่</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" minlength="8">
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save me-1"></i> บันทึกข้อมูล
                    </button>
                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-alt-secondary">
                        ยกเลิก
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection