@extends('layouts.app')
@section('title', 'รายละเอียดออเดอร์')
@section('subtitle', 'ออเดอร์ #' . $order->order_number)
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">หน้าหลัก</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('seller.dashboard') }}">แดชบอร์ดผู้ขาย</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('seller.orders.index') }}">รายการออเดอร์</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">ออเดอร์ #{{ $order->order_number }}</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">รายละเอียดออเดอร์</h3>
                    <div class="block-options">
                        <a href="{{ route('seller.orders.index') }}" class="btn btn-sm btn-alt-secondary">
                            <i class="fa fa-arrow-left me-1"></i> กลับ
                        </a>
                    </div>
                </div>
                <div class="block-content">
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <div class="fs-sm">
                                <div class="fw-semibold">เลขออเดอร์</div>
                                <div class="text-muted">#{{ $order->order_number }}</div>
                            </div>
                            <div class="fs-sm">
                                <div class="fw-semibold">วันที่สั่งซื้อ</div>
                                <div class="text-muted">{{ $order->created_at->format('d/m/Y H') }}</div>
                            </div>
                            <div class="fs-sm">
                                <div class="fw-semibold">สถานะออเดอร์</div>
                                <div>
                                    @if ($order->status === 'pending')
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
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0 me-2">
                                    <img class="img-avatar img-avatar48"
                                        src="{{ $buyer->avatar ? asset('storage/' . $buyer->avatar) : asset('media/avatars/avatar15.jpg') }}"
                                        alt="{{ $buyer->name }}">
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ $buyer->name }}</div>
                                    <div class="fs-sm text-muted">{{ $buyer->email }}</div>
                                </div>
                            </div>
                            <div class="fs-sm">
                                <div class="fw-semibold">วันที่สมัคร</div>
                                <div class="text-muted">{{ $buyer->created_at->format('d/m/Y') }}</div>
                            </div>
                            <div class="mt-3">
                                <a href="#" class="btn btn-sm btn-alt-primary"
                                    onclick="event.preventDefault(); document.getElementById('send-message-form').submit();">
                                    <i class="fa fa-envelope me-1"></i> ส่งข้อความถึงผู้ซื้อ
                                </a>
                                <form id="send-message-form" action="{{ route('messages.show', $buyer) }}" method="GET"
                                    style="display: none;"></form>
                            </div>
                        </div>
                    </div>
                    <!-- รายการสินค้า -->
                    <h4 class="mb-3">รายการสินค้าของคุณในออเดอร์นี้</h4>
                    <div class="table-responsive">
                        <table class="table table-borderless table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th>สินค้า</th>
                                    <th class="text-center">ราคา</th>
                                    <th class="text-center">สถานะ</th>
                                    <th class="text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orderItems as $item)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $item->product->name }}</div>
                                            <div class="fs-sm text-muted">
                                                @if ($item->product->type === 'steam_key')
                                                    <span class="badge bg-primary">Steam Key</span>
                                                @elseif($item->product->type === 'origin_key')
                                                    <span class="badge bg-info">Origin Key</span>
                                                @elseif($item->product->type === 'gog_key')
                                                    <span class="badge bg-success">GOG Key</span>
                                                @elseif($item->product->type === 'uplay_key')
                                                    <span class="badge bg-warning">Uplay Key</span>
                                                @elseif($item->product->type === 'battlenet_key')
                                                    <span class="badge bg-danger">Battle.net Key</span>
                                                @elseif($item->product->type === 'account')
                                                    <span class="badge bg-dark">Account</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $item->product->type }}</span>
                                                @endif
                                                <span class="ms-1">{{ $item->product->category->name }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            {{ number_format($item->price, 2) }} ฿
                                        </td>
                                        <td class="text-center">
                                            @if ($item->status === 'pending')
                                                <span class="badge bg-warning">รอส่งมอบ</span>
                                            @elseif($item->status === 'delivered')
                                                <span class="badge bg-success">ส่งมอบแล้ว</span>
                                                <div class="fs-xs text-muted">
                                                    {{ $item->delivered_at->format('d/m/Y H:i') }}</div>
                                            @elseif($item->status === 'canceled')
                                                <span class="badge bg-danger">ยกเลิก</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $item->status }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($item->status === 'pending')
                                                <button type="button" class="btn btn-sm btn-alt-success"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modal-deliver-key-{{ $item->id }}">
                                                    <i class="fa fa-key me-1"></i> ส่งมอบรหัส
                                                </button>

                                                <!-- Modal ส่งมอบรหัส -->
                                                <div class="modal fade" id="modal-deliver-key-{{ $item->id }}"
                                                    tabindex="-1" role="dialog"
                                                    aria-labelledby="modal-deliver-key-{{ $item->id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <form action="{{ route('seller.products.deliver', $item) }}"
                                                                method="POST">
                                                                @csrf
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">ส่งมอบรหัสเกม</h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>คุณกำลังจะส่งมอบรหัสเกม
                                                                        <strong>{{ $item->product->name }}</strong>
                                                                        ให้กับผู้ซื้อ <strong>{{ $buyer->name }}</strong>
                                                                    </p>

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
                                                                        และคุณจะได้รับเงินหลังจากหักค่าธรรมเนียม 5%
                                                                        เข้าบัญชีของคุณ
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
                                            @elseif($item->status === 'delivered')
                                                <button type="button" class="btn btn-sm btn-alt-secondary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modal-view-key-{{ $item->id }}">
                                                    <i class="fa fa-eye me-1"></i> ดูรหัสที่ส่ง
                                                </button>

                                                <!-- Modal แสดงรหัส -->
                                                <div class="modal fade" id="modal-view-key-{{ $item->id }}"
                                                    tabindex="-1" role="dialog"
                                                    aria-labelledby="modal-view-key-{{ $item->id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">รหัสที่ส่งให้ผู้ซื้อ</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label
                                                                        for="view-key-{{ $item->id }}">รหัสที่ส่งมอบแล้ว</label>
                                                                    <div class="input-group">
                                                                        <textarea class="form-control" id="view-key-{{ $item->id }}" rows="3" readonly>{{ $item->decryptedKey }}</textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group mt-3">
                                                                    <div class="alert alert-success mb-0">
                                                                        <i class="fa fa-check-circle me-1"></i>
                                                                        คุณได้ส่งมอบรหัสนี้เมื่อ
                                                                        {{ $item->delivered_at->format('d/m/Y H:i') }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-alt-secondary"
                                                                    data-bs-dismiss="modal">ปิด</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">ข้อมูลการเงิน</h3>
                </div>
                <div class="block-content">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td class="fw-semibold">ยอดขายรวม</td>
                                    <td class="text-end">{{ number_format($orderItems->sum('price'), 2) }} ฿</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">ค่าธรรมเนียม (5%)</td>
                                    <td class="text-end">{{ number_format($orderItems->sum('price') * 0.05, 2) }} ฿</td>
                                </tr>
                                <tr class="table-active">
                                    <td class="fw-bold">ยอดที่จะได้รับ</td>
                                    <td class="text-end fw-bold">{{ number_format($orderItems->sum('price') * 0.95, 2) }}
                                        ฿</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-info mt-3">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="fa fa-info-circle fa-2x"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="alert-heading">นโยบายการรับเงิน</h5>
                                <p class="mb-0">ยอดเงินจะถูกโอนเข้าบัญชีของคุณทันทีหลังจากที่คุณส่งมอบรหัสเกมให้ผู้ซื้อ
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">ประวัติการทำรายการ</h3>
                </div>
                <div class="block-content">
                    <ul class="timeline timeline-alt">
                        <li class="timeline-event">
                            <div class="timeline-event-icon bg-primary">
                                <i class="fa fa-shopping-cart"></i>
                            </div>
                            <div class="timeline-event-block">
                                <div class="timeline-event-time">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                                <div class="timeline-event-content">
                                    <p class="mb-0">ผู้ซื้อสั่งซื้อสินค้า</p>
                                </div>
                            </div>
                        </li>
                        @if ($order->status === 'processing')
                            <li class="timeline-event">
                                <div class="timeline-event-icon bg-info">
                                    <i class="fa fa-money-bill"></i>
                                </div>
                                <div class="timeline-event-block">
                                    <div class="timeline-event-time">
                                        {{ $order->transactions()->where('type', 'payment')->where('status', 'successful')->first()->created_at->format('d/m/Y H:i') }}
                                    </div>
                                    <div class="timeline-event-content">
                                        <p class="mb-0">ผู้ซื้อชำระเงินแล้ว</p>
                                    </div>
                                </div>
                            </li>
                        @endif
                        @foreach ($orderItems as $item)
                            @if ($item->status === 'delivered')
                                <li class="timeline-event">
                                    <div class="timeline-event-icon bg-success">
                                        <i class="fa fa-key"></i>
                                    </div>
                                    <div class="timeline-event-block">
                                        <div class="timeline-event-time">{{ $item->delivered_at->format('d/m/Y H:i') }}
                                        </div>
                                        <div class="timeline-event-content">
                                            <p class="mb-0">คุณส่งมอบรหัสเกม {{ $item->product->name }}</p>
                                        </div>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                        @if ($order->status === 'canceled')
                            <li class="timeline-event">
                                <div class="timeline-event-icon bg-danger">
                                    <i class="fa fa-times"></i>
                                </div>
                                <div class="timeline-event-block">
                                    <div class="timeline-event-time">{{ $order->updated_at->format('d/m/Y H:i') }}</div>
                                    <div class="timeline-event-content">
                                        <p class="mb-0">ออเดอร์ถูกยกเลิก</p>
                                    </div>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
