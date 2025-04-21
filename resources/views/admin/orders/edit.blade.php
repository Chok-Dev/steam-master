@extends('layouts.app')

@section('title', 'แก้ไขออเดอร์')
@section('subtitle', 'แก้ไขข้อมูลออเดอร์ #' . $order->order_number)

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">หน้าหลัก</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">แดชบอร์ดแอดมิน</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.orders.index') }}">จัดการออเดอร์</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.orders.show', $order) }}">ออเดอร์ #{{ $order->order_number }}</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">แก้ไขข้อมูล</li>
@endsection

@section('content')
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">แก้ไขข้อมูลออเดอร์</h3>
            <div class="block-options">
                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-alt-secondary">
                    <i class="fa fa-arrow-left me-1"></i> กลับ
                </a>
            </div>
        </div>
        <div class="block-content">
            <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label">เลขออเดอร์</label>
                            <div class="form-control-plaintext">#{{ $order->order_number }}</div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">ผู้ซื้อ</label>
                            <div class="form-control-plaintext">{{ $order->user->name }}</div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">วันที่สั่งซื้อ</label>
                            <div class="form-control-plaintext">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">มูลค่ารวม</label>
                            <div class="form-control-plaintext">{{ number_format($order->total_amount, 2) }} ฿</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label" for="status">สถานะออเดอร์</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="pending" {{ old('status', $order->status) === 'pending' ? 'selected' : '' }}>รอดำเนินการ</option>
                                <option value="processing" {{ old('status', $order->status) === 'processing' ? 'selected' : '' }}>กำลังดำเนินการ</option>
                                <option value="completed" {{ old('status', $order->status) === 'completed' ? 'selected' : '' }}>สำเร็จ</option>
                                <option value="canceled" {{ old('status', $order->status) === 'canceled' ? 'selected' : '' }}>ยกเลิก</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <strong class="text-danger">คำเตือน:</strong> การเปลี่ยนสถานะเป็น "ยกเลิก" จะทำให้สินค้าถูกคืนกลับไปเป็นสถานะพร้อมขาย และคืนเงินเข้าสู่บัญชีของผู้ซื้อโดยอัตโนมัติ
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label" for="notes">หมายเหตุ</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="5">{{ old('notes', $order->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <h4 class="mt-2 mb-3">รายการสินค้าในออเดอร์</h4>
                <div class="table-responsive mb-4">
                    <table class="table table-borderless table-striped table-vcenter">
                        <thead>
                            <tr>
                                <th>สินค้า</th>
                                <th>ผู้ขาย</th>
                                <th class="text-center">ราคา</th>
                                <th class="text-center">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderItems as $item)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $item->product->name }}</div>
                                        <div class="fs-sm text-muted">{{ $item->product->type }} | {{ $item->product->category->name }}</div>
                                    </td>
                                    <td>{{ $item->product->user->name }}</td>
                                    <td class="text-center">{{ number_format($item->price, 2) }} ฿</td>
                                    <td class="text-center">
                                        @if($item->status === 'pending')
                                            <span class="badge bg-warning">รอส่งมอบ</span>
                                        @elseif($item->status === 'delivered')
                                            <span class="badge bg-success">ส่งมอบแล้ว</span>
                                            <div class="fs-xs text-muted">{{ $item->delivered_at->format('d/m/Y H:i') }}</div>
                                        @elseif($item->status === 'refunded')
                                            <span class="badge bg-danger">คืนเงินแล้ว</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $item->status }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" class="fs-sm text-end fw-semibold">รวมทั้งสิ้น:</td>
                                <td class="text-center fs-sm fw-semibold">{{ number_format($order->total_amount, 2) }} ฿</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="mb-4">
                    <button type="submit" class="btn btn-alt-primary">
                        <i class="fa fa-save me-1"></i> บันทึกการเปลี่ยนแปลง
                    </button>
                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-alt-secondary">
                        ยกเลิก
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection