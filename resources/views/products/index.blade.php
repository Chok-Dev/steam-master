@extends('layouts.app')

@section('title', 'สินค้าทั้งหมด')
@section('subtitle', 'รหัส Steam คุณภาพ ราคาถูก')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">หน้าหลัก</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">สินค้าทั้งหมด</li>
@endsection

@section('content')
    <div class="row">
        <!-- ฟิลเตอร์ด้านซ้าย -->
        <div class="col-md-3">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">ตัวกรอง</h3>
                </div>
                <div class="block-content">
                    <form action="{{ route('products.index') }}" method="GET">
                        <div class="mb-4">
                            <label class="form-label" for="category">หมวดหมู่</label>
                            <select class="form-select form-select-sm" id="category" name="category">
                                <option value="">ทั้งหมด</option>
                                @foreach ($categories as $category)
                                    <!-- อาจจะเป็นประมาณนี้ -->
                                    <option value="{{ $category->id }}"
                                        {{ request('category') == $category ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="price_min">ราคา</label>
                            <div class="row g-2">
                                <div class="col">
                                    <input type="number" class="form-control form-control-sm" id="price_min"
                                        name="price_min" placeholder="ต่ำสุด" value="{{ request('price_min') }}">
                                </div>
                                <div class="col">
                                    <input type="number" class="form-control form-control-sm" id="price_max"
                                        name="price_max" placeholder="สูงสุด" value="{{ request('price_max') }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="sort">เรียงตาม</label>
                            <select class="form-select form-select-sm" id="sort" name="sort">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>ใหม่ล่าสุด
                                </option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>ราคาต่ำ-สูง
                                </option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>
                                    ราคาสูง-ต่ำ</option>
                                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>ยอดนิยม
                                </option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <button type="submit" class="btn btn-sm btn-alt-primary w-100">
                                <i class="fa fa-filter me-1"></i> กรอง
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- รายการสินค้า -->
        <div class="col-md-9">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">สินค้าทั้งหมด <small>{{ $products->total() }} รายการ</small></h3>
                </div>
                <div class="block-content">
                    <div class="row">
                        @forelse($products as $product)
                            <div class="col-md-4 col-xl-4">
                                <div class="block block-rounded block-link-shadow h-100 mb-2">
                                    <div
                                        class="block-content block-content-full d-flex align-items-center justify-content-between p-3">
                                        <div>
                                            <div class="fs-5 fw-semibold mb-0">{{ Str::limit($product->name, 20) }}</div>
                                            <div class="fs-sm text-muted">{{ $product->category->name }}</div>
                                        </div>
                                        <div class="ms-3 text-nowrap">
                                            <span class="badge bg-primary">{{ number_format($product->price, 0) }} ฿</span>
                                        </div>
                                    </div>
                                    <div class="block-content block-content-full block-content-sm bg-body-light">
                                        <div class="d-flex justify-content-between">
                                            <div class="fs-sm">
                                                <i class="fa fa-eye text-muted me-1"></i> {{ $product->views }}
                                            </div>
                                            <a href="{{ route('products.show', $product) }}" class="fs-sm text-primary">
                                                รายละเอียด <i class="fa fa-arrow-right ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info">
                                    ไม่พบสินค้าที่ตรงกับเงื่อนไขที่คุณค้นหา
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $products->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
