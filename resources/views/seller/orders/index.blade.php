@extends('layouts.app')
@section('title', 'รายการออเดอร์')
@section('subtitle', 'จัดการออเดอร์สินค้าของคุณ')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">หน้าหลัก</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('seller.dashboard') }}">แดชบอร์ดผู้ขาย</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">รายการออเดอร์</li>
@endsection
@section('content')
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">ออเดอร์ทั้งหมด</h3>
            <div class="block-options">
                <div class="dropdown">
                    <button type="button" class="btn btn-sm btn-alt-secondary" id="dropdown-status-filter"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-fw fa-filter me-1"></i> กรองสถานะ
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-status-filter">
                        <a class="dropdown-item {{ request('status') == '' ? 'active' : '' }}"
                            href="{{ route('seller.orders.index') }}">ทั้งหมด</a>
                        <a class="dropdown-item {{ request('status') == 'pending' ? 'active' : '' }}"
                            href="{{ route('seller.orders.index', ['status' => 'pending']) }}">รอส่งมอบ</a>
                        <a class="dropdown-item {{ request('status') == 'delivered' ? 'active' : '' }}"
                            href="{{ route('seller.orders.index', ['status' => 'delivered']) }}">ส่งมอบแล้ว</a>
                        <a class="dropdown-item {{ request('status') == 'canceled' ? 'active' : '' }}"
                            href="{{ route('seller.orders.index', ['status' => 'canceled']) }}">ยกเลิก</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="block-content">
            <div class="table-responsive">
                <table class="table table-striped table-vcenter">
                    <thead>
                        <tr>
                            <th>ออเดอร์</th>
                            <th>ผู้ซื้อ</th>
                            <th>สินค้า</th>
                            <th>ราคา</th>
                            <th>สถานะ</th>
                            <th>วันที่</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orderItems as $item)
                            <tr>
                                <td>
                                    <a
                                        href="{{ route('seller.orders.show', $item->order) }}">#{{ $item->order->order_number }}</a>
                                </td>
                                <td>
                                    {{ $item->order->user->name }}
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $item->product->name }}</div>
                                    <div class="fs-sm text-muted">{{ $item->product->category->name }}</div>
                                </td>
                                <td>{{ number_format($item->price, 2) }} ฿</td>
                                <td>
                                    @if ($item->status === 'pending')
                                        <span class="badge bg-warning">รอส่งมอบ</span>
                                    @elseif($item->status === 'delivered')
                                        <span class="badge bg-success">ส่งมอบแล้ว</span>
                                    @elseif($item->status === 'canceled')
                                        <span class="badge bg-danger">ยกเลิก</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $item->status }}</span>
                                    @endif
                                </td>
                                <td>{{ $item->created_at->format('d/m/Y H') }}</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('seller.orders.show', $item->order) }}"
                                            class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip"
                                            title="ดูรายละเอียด">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        @if ($item->status === 'pending')
                                            <button type="button" class="btn btn-sm btn-alt-success" data-bs-toggle="modal"
                                                data-bs-target="#modal-deliver-key-{{ $item->id }}"
                                                data-bs-toggle="tooltip" title="ส่งมอบรหัส">
                                                <i class="fa fa-key"></i>
                                            </button>
                                        @endif
                                    </div>
                                    <!-- Modal ส่งมอบรหัส -->
                                    @if ($item->status === 'pending')
                                        <div class="modal fade" id="modal-deliver-key-{{ $item->id }}" tabindex="-1"
                                            role="dialog" aria-labelledby="modal-deliver-key-{{ $item->id }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <form action="{{ route('seller.products.deliver', $item) }}"
                                                        method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">ส่งมอบรหัสเกม</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>คุณกำลังจะส่งมอบรหัสเกม
                                                                <strong>{{ $item->product->name }}</strong> ให้กับผู้ซื้อ
                                                                <strong>{{ $item->order->user->name }}</strong></p>

                                                            <div class="mb-4">
                                                                <label class="form-label"
                                                                    for="key_data_{{ $item->id }}">ใส่รหัสเกมที่ต้องการส่งมอบ</label>
                                                                <textarea class="form-control" id="key_data_{{ $item->id }}" name="key_data" rows="3"
                                                                    placeholder="ใส่รหัสเกม, ข้อมูลบัญชี หรือข้อมูลอื่นๆ ที่ต้องการส่งให้ผู้ซื้อ" required></textarea>
                                                                <div class="form-text">
                                                                    <i class="fa fa-info-circle me-1"></i>
                                                                    รหัสนี้จะถูกเข้ารหัสเพื่อความปลอดภัยและจะแสดงเฉพาะกับผู้ซื้อเท่านั้น
                                                                </div>
                                                            </div>

                                                            <div class="alert alert-info">
                                                                <i class="fa fa-info-circle me-1"></i>
                                                                การส่งมอบรหัสจะทำให้สถานะสินค้าเปลี่ยนเป็น "ขายแล้ว"
                                                                และคุณจะได้รับเงินหลังจากหักค่าธรรมเนียม 5% เข้าบัญชีของคุณ
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-alt-secondary"
                                                                data-bs-dismiss="modal">ยกเลิก</button>
                                                            <button type="submit" class="btn btn-alt-success">
                                                                <i class="fa fa-check me-1"></i> ยืนยันการส่งมอบ
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">ไม่พบรายการออเดอร์</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $orderItems->links() }}
            </div>
        </div>
    </div>
@endsection
