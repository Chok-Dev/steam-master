@extends('layouts.app')

@section('title', 'จัดการหมวดหมู่')
@section('subtitle', 'จัดการหมวดหมู่สินค้าในระบบ')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">หน้าหลัก</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">แดชบอร์ดแอดมิน</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">จัดการหมวดหมู่</li>
@endsection

@section('content')
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">หมวดหมู่ทั้งหมด</h3>
            <div class="block-options">
                <a href="{{ route('admin.categories.create') }}" class="btn btn-alt-primary">
                    <i class="fa fa-plus me-1"></i> เพิ่มหมวดหมู่ใหม่
                </a>
            </div>
        </div>
        <div class="block-content">
            <div class="table-responsive">
                <table class="table table-striped table-vcenter">
                    <thead>
                        <tr>
                            <th style="width: 50px;">รูปภาพ</th>
                            <th>ชื่อหมวดหมู่</th>
                            <th>Slug</th>
                            <th>หมวดหมู่หลัก</th>
                            <th>จำนวนสินค้า</th>
                            <th class="text-center" style="width: 150px;">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>
                                    @if($category->image)
                                        <img class="img-avatar img-avatar48" src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}">
                                    @else
                                        <img class="img-avatar img-avatar48" src="{{ asset('assets/codebase/media/avatars/avatar0.jpg') }}" alt="{{ $category->name }}">
                                    @endif
                                </td>
                                <td class="fw-semibold">{{ $category->name }}</td>
                                <td>{{ $category->slug }}</td>
                                <td>{{ $category->parent ? $category->parent->name : '-' }}</td>
                                <td>{{ $category->products->count() }}</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="ดูรายละเอียด">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="แก้ไข">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบหมวดหมู่นี้?');" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="ลบ">
                                                <i class="fa fa-trash text-danger"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">ไม่พบข้อมูลหมวดหมู่</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection