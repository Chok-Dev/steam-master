<!-- resources/views/admin/products/create.blade.php -->
@extends('layouts.app')

@section('title', 'เพิ่มสินค้าใหม่')
@section('subtitle', 'เพิ่มรายการสินค้าใหม่เข้าระบบ')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">หน้าหลัก</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">แดชบอร์ดแอดมิน</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.products.index') }}">จัดการสินค้า</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">เพิ่มสินค้าใหม่</li>
@endsection

@section('content')
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">เพิ่มสินค้าใหม่</h3>
            <div class="block-options">
                <a href="{{ route('admin.products.index') }}" class="btn btn-alt-secondary">
                    <i class="fa fa-arrow-left me-1"></i> กลับ
                </a>
            </div>
        </div>
        <div class="block-content">
            <form action="{{ route('admin.products.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label" for="name">ชื่อสินค้า</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label" for="price">ราคา (บาท)</label>
                            <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" step="0.01" min="0" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-4">
                            <label class="form-label" for="category_id">หมวดหมู่</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                <option value="">เลือกหมวดหมู่</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-4">
                            <label class="form-label" for="user_id">ผู้ขาย</label>
                            <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                <option value="">เลือกผู้ขาย</option>
                                @foreach($sellers as $seller)
                                    <option value="{{ $seller->id }}" {{ old('user_id') == $seller->id ? 'selected' : '' }}>
                                        {{ $seller->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-4">
                            <label class="form-label" for="status">สถานะ</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>พร้อมขาย</option>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>รอดำเนินการ</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label" for="type">ประเภทสินค้า</label>
                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                        <option value="">เลือกประเภทสินค้า</option>
                        <option value="steam_key" {{ old('type') == 'steam_key' ? 'selected' : '' }}>Steam Key</option>
                        <option value="origin_key" {{ old('type') == 'origin_key' ? 'selected' : '' }}>Origin Key</option>
                        <option value="gog_key" {{ old('type') == 'gog_key' ? 'selected' : '' }}>GOG Key</option>
                        <option value="uplay_key" {{ old('type') == 'uplay_key' ? 'selected' : '' }}>Uplay Key</option>
                        <option value="battlenet_key" {{ old('type') == 'battlenet_key' ? 'selected' : '' }}>Battle.net Key</option>
                        <option value="account" {{ old('type') == 'account' ? 'selected' : '' }}>Account</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label class="form-label" for="description">คำอธิบาย</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="6" required>{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="form-label" for="key_data">รหัสเกม/คีย์</label>
                    <textarea class="form-control @error('key_data') is-invalid @enderror" id="key_data" name="key_data" rows="3" placeholder="ใส่รหัสเกม หรือข้อมูลบัญชีที่ต้องส่งให้ผู้ซื้อ"></textarea>
                    <div class="form-text">
                        <i class="fa fa-info-circle me-1"></i> รหัสนี้จะถูกเข้ารหัสเพื่อความปลอดภัยและจะถูกส่งให้ผู้ซื้อทันทีหลังชำระเงิน
                    </div>
                    @error('key_data')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <h4 class="mt-4 mb-3">ข้อมูลเพิ่มเติม</h4>
                
                <div id="attributes-container">
                    <div class="row mb-2 attribute-row">
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="attributes[keys][]" placeholder="คุณสมบัติ (เช่น แพลตฟอร์ม, ภาษา)">
                        </div>
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="attributes[values][]" placeholder="ค่า (เช่น Steam, ภาษาอังกฤษ)">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-alt-danger remove-attribute">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <button type="button" class="btn btn-alt-success" id="add-attribute">
                        <i class="fa fa-plus me-1"></i> เพิ่มข้อมูลเพิ่มเติม
                    </button>
                </div>
                
                <div class="mb-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save me-1"></i> บันทึกสินค้า
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-alt-secondary">
                        ยกเลิก
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // เพิ่มคุณสมบัติเพิ่มเติม
        document.getElementById('add-attribute').addEventListener('click', function() {
            const container = document.getElementById('attributes-container');
            const newRow = document.createElement('div');
            newRow.className = 'row mb-2 attribute-row';
            newRow.innerHTML = `
                <div class="col-md-5">
                    <input type="text" class="form-control" name="attributes[keys][]" placeholder="คุณสมบัติ (เช่น แพลตฟอร์ม, ภาษา)">
                </div>
                <div class="col-md-5">
                    <input type="text" class="form-control" name="attributes[values][]" placeholder="ค่า (เช่น Steam, ภาษาอังกฤษ)">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-alt-danger remove-attribute">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            `;
            container.appendChild(newRow);
            
            // เพิ่ม event listener สำหรับปุ่มลบที่เพิ่งเพิ่มเข้ามา
            newRow.querySelector('.remove-attribute').addEventListener('click', function() {
                container.removeChild(newRow);
            });
        });
        
        // ลบคุณสมบัติเพิ่มเติม
        document.querySelectorAll('.remove-attribute').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('.attribute-row');
                row.parentNode.removeChild(row);
            });
        });
    });
</script>
@endpush