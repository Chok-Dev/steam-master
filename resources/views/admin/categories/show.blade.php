@extends('layouts.app')

@section('title', 'รายละเอียดหมวดหมู่')
@section('subtitle', $category->name)

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
    <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">ข้อมูลหมวดหมู่</h3>
                    <div class="block-options">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-alt-primary">
                            <i class="fa fa-pencil me-1"></i> แก้ไข
                        </a>
                    </div>
                </div>
                <div class="block-content">
                    <div class="text-center mb-4">
                        @if($category->image)
                            <img class="img-fluid img-thumbnail" src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" style="max-height: 200px;">
                        @else
                            <img class="img-fluid img-thumbnail" src="{{ asset('assets/codebase/media/avatars/avatar0.jpg') }}" alt="{{ $category->name }}" style="max-height: 200px;">
                        @endif
                        <div class="mt-2">
                            <h4 class="mb-0">{{ $category->name }}</h4>
                            <p class="text-muted">{{ $category->slug }}</p>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-borderless table-vcenter">
                            <tbody>
                                <tr>
                                    <td class="fw-semibold">หมวดหมู่หลัก</td>
                                    <td>{{ $category->parent ? $category->parent->name : 'ไม่มี' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">จำนวนสินค้า</td>
                                    <td>{{ $category->products->count() }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">หมวดหมู่ย่อย</td>
                                    <td>{{ $category->children->count() }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">วันที่สร้าง</td>
                                    <td>{{ $category->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">อัพเดทล่าสุด</td>
                                    <td>{{ $category->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    @if($category->description)
                        <div class="mt-4">
                            <h5>คำอธิบาย</h5>
                            <p>{{ $category->description }}</p>
                        </div>
                    @endif
                    
                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-alt-primary">
                            <i class="fa fa-pencil me-1"></i> แก้ไขหมวดหมู่
                        </a>
                        
                        @if(!$category->products->count() && !$category->children->count())
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบหมวดหมู่นี้?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fa fa-trash me-1"></i> ลบหมวดหมู่
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            
            @if($category->children->count() > 0)
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">หมวดหมู่ย่อย</h3>
                    </div>
                    <div class="block-content">
                        <div class="list-group">
                            @foreach($category->children as $childCategory)
                                <a href="{{ route('admin.categories.show', $childCategory) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <span>{{ $childCategory->name }}</span>
                                    <span class="badge bg-primary rounded-pill">{{ $childCategory->products->count() }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-md-8">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">สินค้าในหมวดหมู่</h3>
                    <div class="block-options">
                        <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-alt-primary">
                            <i class="fa fa-plus me-1"></i> เพิ่มสินค้า
                        </a>
                    </div>
                </div>
                <div class="block-content">
                    @if($products->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-vcenter">
                                <thead>
                                    <tr>
                                        <th>สินค้า</th>
                                        <th>ราคา</th>
                                        <th>ประเภท</th>
                                        <th>สถานะ</th>
                                        <th>ผู้ขาย</th>
                                        <th class="text-center">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $product->name }}</div>
                                                <div class="fs-sm text-muted">{{ Str::limit($product->description, 50) }}</div>
                                            </td>
                                            <td>{{ number_format($product->price, 2) }} ฿</td>
                                            <td>
                                                @if($product->type === 'steam_key')
                                                    <span class="badge bg-primary">Steam Key</span>
                                                @elseif($product->type === 'origin_key')
                                                    <span class="badge bg-info">Origin Key</span>
                                                @elseif($product->type === 'gog_key')
                                                    <span class="badge bg-success">GOG Key</span>
                                                @elseif($product->type === 'uplay_key')
                                                    <span class="badge bg-warning">Uplay Key</span>
                                                @elseif($product->type === 'battlenet_key')
                                                    <span class="badge bg-danger">Battle.net Key</span>
                                                @elseif($product->type === 'account')
                                                    <span class="badge bg-dark">Account</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $product->type }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($product->status === 'available')
                                                    <span class="badge bg-success">พร้อมขาย</span>
                                                @elseif($product->status === 'pending')
                                                    <span class="badge bg-warning">รอดำเนินการ</span>
                                                @elseif($product->status === 'sold')
                                                    <span class="badge bg-primary">ขายแล้ว</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $product->status }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.users.show', $product->user) }}">
                                                    {{ $product->user->name }}
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="ดูรายละเอียด">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="แก้ไข">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $products->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle me-1"></i> ยังไม่มีสินค้าในหมวดหมู่นี้
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection