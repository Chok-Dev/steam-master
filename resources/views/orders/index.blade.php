@extends('layouts.app')

@section('title', 'รายการสั่งซื้อของฉัน')
@section('subtitle', 'ประวัติการสั่งซื้อและติดตามสถานะ')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">หน้าหลัก</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">รายการสั่งซื้อของฉัน</li>
@endsection

@section('content')
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">รายการสั่งซื้อทั้งหมด</h3>
        </div>
        <div class="block-content">
            @if($orders->isEmpty())
                <div class="py-4 text-center">
                    <div class="mb-3">
                        <i class="fa fa-shopping-cart fa-4x text-muted"></i>
                    </div>
                    <h3 class="h4 fw-normal mb-3">คุณยังไม่มีการสั่งซื้อ</h3>
                    <p class="text-muted">
                        เริ่มต้นค้นหาเกมที่คุณชื่นชอบและเริ่มการซื้อขายกับเรา
                    </p>
                    <a href="{{ route('products.index') }}" class="btn btn-alt-primary">
                        <i class="fa fa-gamepad me-1"></i> ดูสินค้าทั้งหมด
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-vcenter">
                        <thead>
                            <tr>
                                <th>เลขออเดอร์</th>
                                <th>วันที่สั่งซื้อ</th>
                                <th>ยอดรวม</th>
                                <th>สถานะ</th>
                                <th class="text-center">รายละเอียด</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('orders.show', $order) }}">#{{ $order->order_number }}</a>
                                    </td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ number_format($order->total_amount, 2) }} ฿</td>
                                    <td>
                                        @if($order->status === 'pending')
                                            <span class="badge bg-warning">รอดำเนินการ</span>
                                        @elseif($order->status === 'processing')
                                            <span class="badge bg-info">กำลังดำเนินการ</span>
                                        @elseif($order->status === 'completed')
                                            <span class="badge bg-success">สำเร็จ</span>
                                        @elseif($order->status === 'canceled')
                                            <span class="badge bg-danger">ยกเลิก</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $order->status }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-alt-primary">
                                            <i class="fa fa-eye me-1"></i> ดูรายละเอียด
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-2">
                    {{ $orders->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
@endsection