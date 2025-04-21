@extends('layouts.app')

@section('title', 'จัดการสินค้า')
@section('subtitle', 'จัดการรายการสินค้าทั้งหมดในระบบ')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">หน้าหลัก</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">แดชบอร์ดแอดมิน</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">จัดการสินค้า</li>
@endsection

@section('content')
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">สินค้าทั้งหมด</h3>
            <div class="block-options">
                <a href="{{ route('admin.products.index') }}" class="btn btn-alt-secondary">
                    <i class="fa fa-refresh me-1"></i> รีเฟรช
                </a>
            </div>
        </div>
        <div class="block-content">
            <!-- ฟิลเตอร์และการค้นหา -->
            <div class="mb-4">
                <form action="{{ route('admin.products.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-body">
                                <i class="fa fa-search"></i>
                            </span>
                            <input type="text" class="form-control" name="search" placeholder="ค้นหาชื่อสินค้า"
                                value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="category">
                            <option value="">ทุกหมวดหมู่</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="status">
                            <option value="">ทุกสถานะ</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>พร้อมขาย
                            </option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>รอดำเนินการ
                            </option>
                            <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>ขายแล้ว</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="seller_id">
                            <option value="">ทุกผู้ขาย</option>
                            @foreach ($sellers as $seller)
                                <option value="{{ $seller->id }}"
                                    {{ request('seller_id') == $seller->id ? 'selected' : '' }}>
                                    {{ $seller->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex">
                            <button type="submit" class="btn btn-alt-primary me-2">
                                <i class="fa fa-filter me-1"></i> กรอง
                            </button>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-alt-secondary">
                                <i class="fa fa-times me-1"></i> ล้าง
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- ตารางสินค้า -->
            <div class="table-responsive">
                <table class="table table-striped table-vcenter">
                    <thead>
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>สินค้า</th>
                            <th>หมวดหมู่</th>
                            <th>ราคา</th>
                            <th>ประเภท</th>
                            <th>สถานะ</th>
                            <th>ผู้ขาย</th>
                            <th>วันที่เพิ่ม</th>
                            <th class="text-center" style="width: 120px;">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $product->name }}</div>
                                    <div class="fs-sm text-muted">{{ Str::limit($product->description, 50) }}</div>
                                </td>
                                <td>{{ $product->category->name }}</td>
                                <td>{{ number_format($product->price, 2) }} ฿</td>
                                <td>
                                    @if ($product->type === 'steam_key')
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
                                    @if ($product->status === 'available')
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
                                <td>{{ $product->created_at->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.products.show', $product) }}"
                                            class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip"
                                            title="ดูรายละเอียด">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product) }}"
                                            class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="แก้ไข">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <!-- เปลี่ยนสถานะสินค้า -->
                                        <button type="button" class="btn btn-sm btn-alt-secondary"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-cog"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            @if ($product->status !== 'available')
                                                <form action="{{ route('admin.products.change-status', $product) }}"
                                                    method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="available">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fa fa-check text-success me-1"></i> ตั้งเป็นพร้อมขาย
                                                    </button>
                                                </form>
                                            @endif
                                            @if ($product->status !== 'pending')
                                                <form action="{{ route('admin.products.change-status', $product) }}"
                                                    method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="pending">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fa fa-clock-o text-warning me-1"></i> ตั้งเป็นรอดำเนินการ
                                                    </button>
                                                </form>
                                            @endif
                                            @if ($product->status !== 'sold')
                                                <form action="{{ route('admin.products.change-status', $product) }}"
                                                    method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="sold">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fa fa-money text-primary me-1"></i> ตั้งเป็นขายแล้ว
                                                    </button>
                                                </form>
                                            @endif
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                                onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบสินค้านี้?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item">
                                                    <i class="fa fa-trash text-danger me-1"></i> ลบสินค้า
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">ไม่พบข้อมูลสินค้า</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- การแบ่งหน้า -->
            <div class="d-flex justify-content-center mt-4">
                {{ $products->appends(request()->except('page'))->links() }}
            </div>
        </div>
    </div>
@endsection
