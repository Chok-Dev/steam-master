@extends('layouts.app')

@section('title', 'แก้ไขหมวดหมู่')
@section('subtitle', 'แก้ไขข้อมูลหมวดหมู่ ' . $category->name)

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
    <li class="breadcrumb-item active" aria-current="page">แก้ไขหมวดหมู่</li>
@endsection

@section('content')
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">แก้ไขหมวดหมู่</h3>
            <div class="block-options">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-alt-secondary">
                    <i class="fa fa-arrow-left me-1"></i> กลับ
                </a>
            </div>
        </div>
        <div class="block-content">
            <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-4">
                            <label class="form-label" for="name">ชื่อหมวดหมู่</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $category->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label" for="parent_id">หมวดหมู่หลัก</label>
                            <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                                <option value="">ไม่มีหมวดหมู่หลัก</option>
                                @foreach($categories as $parentCategory)
                                    <option value="{{ $parentCategory->id }}" {{ old('parent_id', $category->parent_id) == $parentCategory->id ? 'selected' : '' }}>
                                        {{ $parentCategory->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label" for="description">คำอธิบาย</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5">{{ old('description', $category->description) }}</textarea>
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
                        
                        <div id="image-preview" class="text-center mt-3" style="{{ $category->image ? '' : 'display: none;' }}">
                            <img src="{{ $category->image ? asset('storage/' . $category->image) : '' }}" alt="รูปภาพตัวอย่าง" class="img-fluid img-thumbnail" style="max-height: 200px;">
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <button type="submit" class="btn btn-alt-primary">
                        <i class="fa fa-save me-1"></i> บันทึกการเปลี่ยนแปลง
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
