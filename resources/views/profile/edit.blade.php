@extends('layouts.app')

@section('title', 'แก้ไขโปรไฟล์')
@section('subtitle', 'แก้ไขข้อมูลส่วนตัวของคุณ')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">หน้าหลัก</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('profile.show') }}">โปรไฟล์</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">แก้ไขโปรไฟล์</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <!-- แก้ไขข้อมูลส่วนตัว -->
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">แก้ไขข้อมูลส่วนตัว</h3>
                </div>
                <div class="block-content">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('put')

                        <div class="mb-4">
                            <label class="form-label" for="name">ชื่อผู้ใช้</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="email">อีเมล</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                                <div class="mt-2 text-sm text-muted">
                                    อีเมลของคุณยังไม่ได้รับการยืนยัน

                                    <button id="send-verification" type="button" class="btn btn-sm btn-alt-primary">
                                        คลิกที่นี่เพื่อส่งลิงก์ยืนยันอีเมลอีกครั้ง
                                    </button>
                                </div>

                                @if (session('status') === 'verification-link-sent')
                                    <div class="mt-2 text-sm text-success">
                                        ลิงก์ยืนยันใหม่ได้ถูกส่งไปยังอีเมลของคุณแล้ว
                                    </div>
                                @endif
                            @endif
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="avatar">รูปโปรไฟล์</label>
                            <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar"
                                name="avatar" accept="image/*">
                            @error('avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            @if ($user->avatar)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}"
                                        class="img-avatar img-avatar96">
                                </div>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="bio">ประวัติส่วนตัว</label>
                            <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="4">{{ old('bio', $user->bio) }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <button type="submit" class="btn btn-alt-primary">
                                <i class="fa fa-save me-1"></i> บันทึกข้อมูล
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- แก้ไขรหัสผ่าน -->
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">แก้ไขรหัสผ่าน</h3>
                </div>
                <div class="block-content">
                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf
                        @method('put')

                        <div class="mb-4">
                            <label class="form-label" for="current_password">รหัสผ่านปัจจุบัน</label>
                            <input type="password"
                                class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                                id="current_password" name="current_password" required>
                            @error('current_password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="password">รหัสผ่านใหม่</label>
                            <input type="password"
                                class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                                id="password" name="password" required>
                            @error('password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="password_confirmation">ยืนยันรหัสผ่านใหม่</label>
                            <input type="password"
                                class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                                id="password_confirmation" name="password_confirmation" required>
                            @error('password_confirmation', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <button type="submit" class="btn btn-alt-warning">
                                <i class="fa fa-key me-1"></i> เปลี่ยนรหัสผ่าน
                            </button>
                        </div>

                        @if (session('status') === 'password-updated')
                            <div class="alert alert-success">
                                <i class="fa fa-check me-1"></i> เปลี่ยนรหัสผ่านเรียบร้อยแล้ว
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- ข้อมูลบัญชี -->
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">ข้อมูลบัญชี</h3>
                </div>
                <div class="block-content">
                    <p>
                        <i class="fa fa-user me-1"></i> <strong>ชื่อผู้ใช้:</strong> {{ $user->name }}
                    </p>
                    <p>
                        <i class="fa fa-envelope me-1"></i> <strong>อีเมล:</strong> {{ $user->email }}
                    </p>
                    <p>
                        <i class="fa fa-id-badge me-1"></i> <strong>บทบาท:</strong>
                        @if ($user->role === 'admin')
                            แอดมิน
                        @elseif($user->role === 'seller')
                            ผู้ขาย
                        @else
                            ผู้ใช้ทั่วไป
                        @endif
                    </p>
                    <p>
                        <i class="fa fa-calendar me-1"></i> <strong>เข้าร่วมเมื่อ:</strong>
                        {{ $user->created_at->format('d/m/Y') }}
                    </p>
                    <p>
                        <i class="fa fa-wallet me-1"></i> <strong>ยอดเงินในบัญชี:</strong>
                        {{ number_format($user->balance, 2) }} ฿
                    </p>

                    @if ($user->role === 'user')
                        <div class="mt-4">
                            @if (isset($user->seller_request_status))
                                @if ($user->seller_request_status === 'pending')
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle me-1"></i> คำขอเป็นผู้ขายของคุณอยู่ระหว่างการพิจารณา
                                    </div>
                                @elseif($user->seller_request_status === 'rejected')
                                    <div class="alert alert-danger">
                                        <i class="fa fa-exclamation-circle me-1"></i> คำขอเป็นผู้ขายของคุณถูกปฏิเสธ
                                        @if (isset($user->seller_details['rejection_reason']))
                                            <p class="mb-0 mt-2"><strong>เหตุผล:</strong>
                                                {{ $user->seller_details['rejection_reason'] }}</p>
                                        @endif
                                    </div>
                                    <a href="{{ route('seller.request') }}" class="btn btn-alt-success btn-sm w-100">
                                        <i class="fa fa-store me-1"></i> ส่งคำขอเป็นผู้ขายอีกครั้ง
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('seller.request') }}" class="btn btn-alt-success btn-sm w-100">
                                    <i class="fa fa-store me-1"></i> ขอเป็นผู้ขาย
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- ลบบัญชี -->
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title text-danger">ลบบัญชี</h3>
                </div>
                <div class="block-content">
                    <p>เมื่อบัญชีของคุณถูกลบ ข้อมูลและทรัพยากรทั้งหมดจะถูกลบอย่างถาวร โปรดดาวน์โหลดข้อมูลหรือข้อมูลใดๆ
                        ที่คุณต้องการเก็บไว้ก่อนลบบัญชีของคุณ</p>

                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                        data-bs-target="#modal-delete-account">
                        <i class="fa fa-trash me-1"></i> ลบบัญชีของฉัน
                    </button>

                    <!-- Modal ยืนยันการลบบัญชี -->
                    <div class="modal fade" id="modal-delete-account" tabindex="-1" role="dialog"
                        aria-labelledby="modal-delete-account" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <form action="{{ route('profile.destroy') }}" method="POST">
                                    @csrf
                                    @method('delete')

                                    <div class="modal-header">
                                        <h5 class="modal-title">ยืนยันการลบบัญชี</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>คุณแน่ใจหรือไม่ว่าต้องการลบบัญชีของคุณ? เมื่อบัญชีของคุณถูกลบ
                                            ข้อมูลและทรัพยากรทั้งหมดจะถูกลบอย่างถาวร การกระทำนี้ไม่สามารถย้อนกลับได้</p>

                                        <div class="mb-4">
                                            <label class="form-label" for="delete_password">ยืนยันรหัสผ่านของคุณ</label>
                                            <input type="password" class="form-control" id="delete_password"
                                                name="password" placeholder="รหัสผ่าน" required>
                                        </div>

                                        @error('password', 'userDeletion')
                                            <div class="alert alert-danger">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-alt-secondary"
                                            data-bs-dismiss="modal">ยกเลิก</button>
                                        <button type="submit" class="btn btn-danger">ลบบัญชีของฉัน</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // สคริปต์สำหรับส่งลิงก์ยืนยันอีเมล
            const sendVerificationButton = document.getElementById('send-verification');
            if (sendVerificationButton) {
                sendVerificationButton.addEventListener('click', function() {
                    // สร้างฟอร์มสำหรับส่ง POST request
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('verification.send') }}';
                    form.style.display = 'none';

                    // เพิ่ม CSRF token
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    document.body.appendChild(form);
                    form.submit();
                });
            }
        });
    </script>
@endpush
