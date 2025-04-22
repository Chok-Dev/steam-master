@extends('layouts.app')

@section('title', 'สินค้าของฉัน')
@section('subtitle', 'จัดการรายการสินค้าที่คุณลงขาย')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">หน้าหลัก</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('seller.dashboard') }}">แดชบอร์ดผู้ขาย</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">สินค้าของฉัน</li>
@endsection

@section('content')
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">สินค้าทั้งหมดของฉัน</h3>
            <div class="block-options">
                <a href="{{ route('seller.products.create') }}" class="btn btn-alt-primary">
                    <i class="fa fa-plus me-1"></i> เพิ่มสินค้าใหม่
                </a>
            </div>
        </div>
        <div class="block-content">
            @if($products->isEmpty())
                <div class="py-4 text-center">
                    <div class="mb-3">
                        <i class="fa fa-shopping-cart fa-4x text-muted"></i>
                    </div>
                    <h3 class="h4 fw-normal mb-3">คุณยังไม่มีสินค้า</h3>
                    <p class="text-muted">
                        เริ่มต้นลงขายสินค้าของคุณและเริ่มสร้างรายได้กับเรา
                    </p>
                    <a href="{{ route('seller.products.create') }}" class="btn btn-alt-primary">
                        <i class="fa fa-plus me-1"></i> เพิ่มสินค้าใหม่
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-vcenter">
                        <thead>
                            <tr>
                                <th>สินค้า</th>
                                <th>หมวดหมู่</th>
                                <th>ราคา</th>
                                <th>สถานะ</th>
                                <th>วันที่เพิ่ม</th>
                                <th class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $product->name }}</div>
                                        <div class="fs-sm text-muted">{{ $product->type }}</div>
                                    </td>
                                    <td>{{ $product->category->name }}</td>
                                    <td>{{ number_format($product->price, 2) }} ฿</td>
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
                                    <td>{{ $product->created_at->format('d/m/Y') }}</td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="ดูรายละเอียด">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('seller.products.edit', $product) }}" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="แก้ไข">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <form action="{{ route('seller.products.destroy', $product) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบสินค้านี้?');" style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="ลบ">
                                                    <i class="fa fa-trash text-danger"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
@endsection