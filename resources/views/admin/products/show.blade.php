@extends('layouts.app')

@section('title', 'รายละเอียดสินค้า')
@section('subtitle', $product->name)

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">หน้าหลัก</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">แดชบอร์ดแอดมิน</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.products.index') }}">จัดการสินค้า</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
@endsection

@section('content')
    <div class="row">
        <!-- รายละเอียดสินค้า -->
        <div class="col-md-8">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">รายละเอียดสินค้า</h3>
                    <div class="block-options">
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-alt-primary">
                            <i class="fa fa-pencil me-1"></i> แก้ไข
                        </a>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-alt-secondary">
                            <i class="fa fa-arrow-left me-1"></i> กลับ
                        </a>
                    </div>
                </div>
                <div class="block-content">
                    <div class="row">
                        <div class="col-md-12">
                            <h2 class="mb-2">{{ $product->name }}</h2>
                            <div class="d-flex flex-wrap mb-4">
                                <div class="me-4 mb-2">
                                    <span class="badge bg-primary fs-6">{{ number_format($product->price, 2) }} ฿</span>
                                </div>
                                <div class="me-4 mb-2">
                                    @if ($product->status === 'available')
                                        <span class="badge bg-success fs-6">พร้อมขาย</span>
                                    @elseif($product->status === 'pending')
                                        <span class="badge bg-warning fs-6">รอดำเนินการ</span>
                                    @elseif($product->status === 'sold')
                                        <span class="badge bg-primary fs-6">ขายแล้ว</span>
                                    @else
                                        <span class="badge bg-secondary fs-6">{{ $product->status }}</span>
                                    @endif
                                </div>
                                <div class="me-4 mb-2">
                                    @if ($product->type === 'steam_key')
                                        <span class="badge bg-primary fs-6">Steam Key</span>
                                    @elseif($product->type === 'origin_key')
                                        <span class="badge bg-info fs-6">Origin Key</span>
                                    @elseif($product->type === 'gog_key')
                                        <span class="badge bg-success fs-6">GOG Key</span>
                                    @elseif($product->type === 'uplay_key')
                                        <span class="badge bg-warning fs-6">Uplay Key</span>
                                    @elseif($product->type === 'battlenet_key')
                                        <span class="badge bg-danger fs-6">Battle.net Key</span>
                                    @elseif($product->type === 'account')
                                        <span class="badge bg-dark fs-6">Account</span>
                                    @else
                                        <span class="badge bg-secondary fs-6">{{ $product->type }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h4>ข้อมูลทั่วไป</h4>
                                <div class="table-responsive">
                                    <table class="table table-borderless table-vcenter">
                                        <tbody>
                                            <tr>
                                                <td class="fw-semibold" style="width: 120px;">หมวดหมู่</td>
                                                <td>{{ $product->category->name }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold">ผู้ขาย</td>
                                                <td>
                                                    <a href="{{ route('admin.users.show', $product->user) }}">
                                                        {{ $product->user->name }}
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold">จำนวนผู้เข้าชม</td>
                                                <td>{{ $product->views }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold">วันที่ลงขาย</td>
                                                <td>{{ $product->created_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold">อัพเดทล่าสุด</td>
                                                <td>{{ $product->updated_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-4">
                                <h4>ข้อมูลเพิ่มเติม</h4>
                                @if (!empty($product->attributes) && is_array($product->attributes))
                                    <h2 class="content-heading">ข้อมูลเพิ่มเติม</h2>
                                    <div class="table-responsive mb-4">
                                        <table class="table table-bordered table-striped table-vcenter">
                                            <tbody>
                                                @foreach ($product->attributes as $key => $value)
                                                    <tr>
                                                        <td style="width: 30%"><strong>{{ ucfirst($key) }}</strong></td>
                                                        <td>{{ $value }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted">ไม่มีข้อมูลเพิ่มเติม</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h4>คำอธิบาย</h4>
                        <div class="bg-body-light p-3 rounded">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    </div>

                    <!-- ประวัติออเดอร์ -->
                    @if ($product->orderItems->count() > 0)
                        <div class="mb-4">
                            <h4>ประวัติการซื้อขาย</h4>
                            <div class="table-responsive">
                                <table class="table table-striped table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>ออเดอร์</th>
                                            <th>ผู้ซื้อ</th>
                                            <th>ราคา</th>
                                            <th>สถานะ</th>
                                            <th>วันที่ซื้อ</th>
                                            <th>วันที่ส่งมอบ</th>
                                            <th class="text-center">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($product->orderItems as $item)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('admin.orders.show', $item->order) }}">
                                                        #{{ $item->order->order_number }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.users.show', $item->order->user) }}">
                                                        {{ $item->order->user->name }}
                                                    </a>
                                                </td>
                                                <td>{{ number_format($item->price, 2) }} ฿</td>
                                                <td>
                                                    @if ($item->status === 'pending')
                                                        <span class="badge bg-warning">รอส่งมอบ</span>
                                                    @elseif($item->status === 'delivered')
                                                        <span class="badge bg-success">ส่งมอบแล้ว</span>
                                                    @elseif($item->status === 'refunded')
                                                        <span class="badge bg-danger">คืนเงินแล้ว</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $item->status }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                                <td>{{ $item->delivered_at ? $item->delivered_at->format('d/m/Y H:i') : '-' }}
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('admin.orders.show', $item->order) }}"
                                                        class="btn btn-sm btn-alt-primary">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- การจัดการสินค้า -->
        <div class="col-md-4">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">การจัดการสินค้า</h3>
                </div>
                <div class="block-content">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-alt-primary">
                            <i class="fa fa-pencil me-1"></i> แก้ไขสินค้า
                        </a>

                        <!-- เปลี่ยนสถานะสินค้า -->
                        <div class="btn-group w-100">
                            <button type="button" class="btn btn-alt-secondary dropdown-toggle w-100"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-cog me-1"></i> เปลี่ยนสถานะสินค้า
                            </button>
                            <div class="dropdown-menu dropdown-menu-end w-100">
                                @if ($product->status !== 'available')
                                    <form action="{{ route('admin.products.change-status', $product) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="available">
                                        <button type="submit" class="dropdown-item">
                                            <i class="fa fa-check text-success me-1"></i> ตั้งเป็นพร้อมขาย
                                        </button>
                                    </form>
                                @endif
                                @if ($product->status !== 'pending')
                                    <form action="{{ route('admin.products.change-status', $product) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="pending">
                                        <button type="submit" class="dropdown-item">
                                            <i class="fa fa-clock-o text-warning me-1"></i> ตั้งเป็นรอดำเนินการ
                                        </button>
                                    </form>
                                @endif
                                @if ($product->status !== 'sold')
                                    <form action="{{ route('admin.products.change-status', $product) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="sold">
                                        <button type="submit" class="dropdown-item">
                                            <i class="fa fa-money text-primary me-1"></i> ตั้งเป็นขายแล้ว
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        @if (!$product->orderItems()->exists())
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบสินค้านี้?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fa fa-trash me-1"></i> ลบสินค้า
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ข้อมูลผู้ขาย -->
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">ข้อมูลผู้ขาย</h3>
                </div>
                <div class="block-content">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <img class="img-avatar img-avatar48"
                                src="{{ $product->user->avatar ? asset('storage/' . $product->user->avatar) : asset('assets/codebase/media/avatars/avatar10.jpg') }}"
                                alt="{{ $product->user->name }}">
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="fw-semibold mb-0">
                                <a href="{{ route('admin.users.show', $product->user) }}">{{ $product->user->name }}</a>
                            </p>
                            <p class="fs-sm text-muted mb-0">
                                <i class="fa fa-star text-warning"></i>
                                {{ number_format($product->user->average_rating, 1) }}/5.0
                                ({{ $product->user->receivedReviews->count() }} รีวิว)
                            </p>
                        </div>
                    </div>

                    <div class="row text-center mb-3">
                        <div class="col-4">
                            <div class="fs-sm fw-semibold text-uppercase text-muted">สินค้า</div>
                            <div class="fs-4 fw-bold">{{ $product->user->products->count() }}</div>
                        </div>
                        <div class="col-4">
                            <div class="fs-sm fw-semibold text-uppercase text-muted">ขายแล้ว</div>
                            <div class="fs-4 fw-bold">{{ $product->user->products->where('status', 'sold')->count() }}
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="fs-sm fw-semibold text-uppercase text-muted">เข้าร่วม</div>
                            <div class="fs-4 fw-bold">{{ $product->user->created_at->diffForHumans(null, true) }}</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <a href="{{ route('admin.users.show', $product->user) }}"
                            class="btn btn-alt-primary btn-sm w-100">
                            <i class="fa fa-user me-1"></i> ดูโปรไฟล์ผู้ขาย
                        </a>
                    </div>

                    <div class="mb-3">
                        <a href="{{ route('admin.products.index', ['seller_id' => $product->user->id]) }}"
                            class="btn btn-alt-secondary btn-sm w-100">
                            <i class="fa fa-list me-1"></i> ดูสินค้าทั้งหมดของผู้ขาย
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
