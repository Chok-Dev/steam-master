
<!-- resources/views/admin/categories/create.blade.php -->
@extends('layouts.app')

@section('title', 'เพิ่มหมวดหมู่ใหม่')
@section('subtitle', 'เพิ่มหมวดหมู่สินค้าใหม่เข้าสู่ระบบ')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">หน้าหลัก</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">แดชบอร์ดแอดมิน</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.categories.index') }}">จัดการหมวดหมู่</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">เพิ่มหมวดหมู่ใหม่</li>
@endsection

@section('content')
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">เพิ่มหมวดหมู่ใหม่</h3>
            <div class="block-options">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-alt-secondary">
                    <i class="fa fa-arrow-left me-1"></i> กลับ
                </a>
            </div>
        </div>
        <div class="block-content">
            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-4">
                            <label class="form-label" for="name">ชื่อหมวดหมู่</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label" for="parent_id">หมวดหมู่หลัก</label>
                            <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                                <option value="">ไม่มีหมวดหมู่หลัก</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('parent_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label" for="description">คำอธิบาย</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-4">
                            <label class="form-label" for="image">รูปภาพ</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">ขนาดที่แนะนำ: 200x200 พิกเซล</div>
                        </div>
                        
                        <div id="image-preview" class="text-center mt-3" style="display: none;">
                            <img src="" alt="รูปภาพตัวอย่าง" class="img-fluid img-thumbnail" style="max-height: 200px;">
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <button type="submit" class="btn btn-alt-primary">
                        <i class="fa fa-save me-1"></i> บันทึกหมวดหมู่
                    </button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-alt-secondary">
                        ยกเลิก
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // แสดงตัวอย่างรูปภาพก่อนอัพโหลด
    document.getElementById('image').addEventListener('change', function(e) {
        var reader = new FileReader();
        reader.onload = function(event) {
            document.querySelector('#image-preview img').src = event.target.result;
            document.getElementById('image-preview').style.display = 'block';
        }
        reader.readAsDataURL(e.target.files[0]);
    });
</script>
@endpush