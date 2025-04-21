@extends('layouts.app')

@section('title', 'รายละเอียดผู้ใช้')
@section('subtitle', 'ข้อมูลของ ' . $user->name)

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">หน้าหลัก</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">แดชบอร์ดแอดมิน</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.users.index') }}">จัดการผู้ใช้</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">{{ $user->name }}</li>
@endsection

@section('content')
    <div class="row">
        <!-- ข้อมูลโปรไฟล์ -->
        <div class="col-md-4">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">ข้อมูลโปรไฟล์</h3>
                    <div class="block-options">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-alt-primary">
                            <i class="fa fa-pencil me-1"></i> แก้ไข
                        </a>
                    </div>
                </div>
                <div class="block-content">
                    <div class="text-center mb-4">
                        <img class="img-avatar img-avatar96"
                            src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('assets/codebase/media/avatars/avatar10.jpg') }}"
                            alt="{{ $user->name }}">
                        <div class="mt-2">
                            <h3 class="mb-0">{{ $user->name }}</h3>
                            <p class="text-muted">{{ $user->email }}</p>
                        </div>
                    </div>

                    <div class="row text-center">
                        <div class="col-4">
                            <div class="fs-sm fw-semibold text-uppercase text-muted">สินค้า</div>
                            <div class="fs-4 fw-bold">{{ $productsCount }}</div>
                        </div>
                        <div class="col-4">
                            <div class="fs-sm fw-semibold text-uppercase text-muted">ออเดอร์</div>
                            <div class="fs-4 fw-bold">{{ $ordersCount }}</div>
                        </div>
                        <div class="col-4">
                            <div class="fs-sm fw-semibold text-uppercase text-muted">ขายแล้ว</div>
                            <div class="fs-4 fw-bold">{{ $soldProductsCount }}</div>
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        <table class="table table-borderless table-vcenter">
                            <tbody>
                                <tr>
                                    <td class="fw-semibold">บทบาท</td>
                                    <td>
                                        @if ($user->role === 'admin')
                                            <span class="badge bg-primary">แอดมิน</span>
                                        @elseif($user->role === 'seller')
                                            <span class="badge bg-success">ผู้ขาย</span>
                                        @else
                                            <span class="badge bg-info">ผู้ใช้ทั่วไป</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">สถานะยืนยัน</td>
                                    <td>
                                        @if ($user->is_verified)
                                            <span class="badge bg-success">ยืนยันแล้ว</span>
                                        @else
                                            <span class="badge bg-warning">ยังไม่ยืนยัน</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">ยอดเงินในวอลเล็ต</td>
                                    <td>{{ number_format($user->balance, 2) }} ฿</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">ยอดซื้อทั้งหมด</td>
                                    <td>{{ number_format($totalSpent, 2) }} ฿</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">ยอดขายทั้งหมด</td>
                                    <td>{{ number_format($totalEarned, 2) }} ฿</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">วันที่ลงทะเบียน</td>
                                    <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">เข้าระบบล่าสุด</td>
                                    <td>{{ $user->last_active_at ? $user->last_active_at->format('d/m/Y H:i') : 'ไม่มีข้อมูล' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <h5>ประวัติส่วนตัว</h5>
                        <p>{{ $user->bio ?? 'ไม่มีข้อมูล' }}</p>
                    </div>

                    <div class="mt-4">
                        <div class="d-grid gap-2">
                            <form action="{{ route('admin.users.toggle-verification', $user) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="btn btn-alt-{{ $user->is_verified ? 'warning' : 'success' }} w-100">
                                    <i class="fa {{ $user->is_verified ? 'fa-times' : 'fa-check' }} me-1"></i>
                                    {{ $user->is_verified ? 'ยกเลิกการยืนยัน' : 'ยืนยันตัวตน' }}
                                </button>
                            </form>

                            <!-- ปรับยอดเงินในวอลเล็ต -->
                            <button type="button" class="btn btn-alt-primary" data-bs-toggle="modal"
                                data-bs-target="#modal-adjust-balance">
                                <i class="fa fa-wallet me-1"></i> ปรับยอดเงินในวอลเล็ต
                            </button>

                            @if (
                                !$user->products()->exists() &&
                                    !$user->orders()->exists() &&
                                    !$user->transactions()->exists() &&
                                    $user->role !== 'admin')
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                    onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบผู้ใช้นี้?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fa fa-trash me-1"></i> ลบผู้ใช้
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- แท็บข้อมูล -->
        <div class="col-md-8">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <ul class="nav nav-tabs nav-tabs-block" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="products-tab" data-bs-toggle="tab"
                                data-bs-target="#products" type="button" role="tab" aria-controls="products"
                                aria-selected="true">สินค้า</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders"
                                type="button" role="tab" aria-controls="orders" aria-selected="false">ออเดอร์</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="transactions-tab" data-bs-toggle="tab"
                                data-bs-target="#transactions" type="button" role="tab" aria-controls="transactions"
                                aria-selected="false">ธุรกรรม</button>
                        </li>
                    </ul>
                </div>
                <div class="block-content tab-content">
                    <!-- แท็บสินค้า -->
                    <div class="tab-pane fade show active" id="products" role="tabpanel"
                        aria-labelledby="products-tab">
                        <h4 class="mb-3">สินค้า {{ $user->role === 'seller' ? 'ที่ขาย' : 'ที่ซื้อ' }}</h4>

                        @if ($user->role === 'seller' && $recentProducts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>สินค้า</th>
                                            <th>ราคา</th>
                                            <th>หมวดหมู่</th>
                                            <th>สถานะ</th>
                                            <th class="text-center">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentProducts as $product)
                                            <tr>
                                                <td>{{ $product->name }}</td>
                                                <td>{{ number_format($product->price, 2) }} ฿</td>
                                                <td>{{ $product->category->name }}</td>
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
                                                <td class="text-center">
                                                    <a href="{{ route('admin.products.show', $product) }}"
                                                        class="btn btn-sm btn-alt-primary">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if ($productsCount > 5)
                                <div class="text-center mt-3">
                                    <a href="{{ route('admin.products.index', ['seller_id' => $user->id]) }}"
                                        class="btn btn-sm btn-alt-primary">
                                        <i class="fa fa-list me-1"></i> ดูสินค้าทั้งหมด ({{ $productsCount }})
                                    </a>
                                </div>
                            @endif
                        @elseif($user->role !== 'seller' && $recentOrders->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>ออเดอร์</th>
                                            <th>สินค้า</th>
                                            <th>ราคา</th>
                                            <th>วันที่ซื้อ</th>
                                            <th class="text-center">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentOrders as $order)
                                            @foreach ($order->orderItems as $item)
                                                <tr>
                                                    <td>#{{ $order->order_number }}</td>
                                                    <td>{{ $item->product->name }}</td>
                                                    <td>{{ number_format($item->price, 2) }} ฿</td>
                                                    <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                                    <td class="text-center">
                                                        <a href="{{ route('admin.orders.show', $order) }}"
                                                            class="btn btn-sm btn-alt-primary">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if ($ordersCount > 5)
                                <div class="text-center mt-3">
                                    <a href="{{ route('admin.orders.index', ['user_id' => $user->id]) }}"
                                        class="btn btn-sm btn-alt-primary">
                                        <i class="fa fa-list me-1"></i> ดูออเดอร์ทั้งหมด ({{ $ordersCount }})
                                    </a>
                                </div>
                            @endif
                        @else
                            <div class="alert alert-info">
                                ยังไม่มีข้อมูลสินค้า
                            </div>
                        @endif
                    </div>

                    <!-- แท็บออเดอร์ -->
                    <div class="tab-pane fade" id="orders" role="tabpanel" aria-labelledby="orders-tab">
                        <h4 class="mb-3">ออเดอร์ล่าสุด</h4>

                        @if ($recentOrders->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>ออเดอร์</th>
                                            <th>จำนวนสินค้า</th>
                                            <th>มูลค่ารวม</th>
                                            <th>สถานะ</th>
                                            <th>วันที่</th>
                                            <th class="text-center">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentOrders as $order)
                                            <tr>
                                                <td>#{{ $order->order_number }}</td>
                                                <td>{{ $order->orderItems->count() }}</td>
                                                <td>{{ number_format($order->total_amount, 2) }} ฿</td>
                                                <td>
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
                                                </td>
                                                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('admin.orders.show', $order) }}"
                                                        class="btn btn-sm btn-alt-primary">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if ($ordersCount > 5)
                                <div class="text-center mt-3">
                                    <a href="{{ route('admin.orders.index', ['user_id' => $user->id]) }}"
                                        class="btn btn-sm btn-alt-primary">
                                        <i class="fa fa-list me-1"></i> ดูออเดอร์ทั้งหมด ({{ $ordersCount }})
                                    </a>
                                </div>
                            @endif
                        @else
                            <div class="alert alert-info">
                                ยังไม่มีข้อมูลออเดอร์
                            </div>
                        @endif
                    </div>

                    <!-- แท็บธุรกรรม -->
                    <div class="tab-pane fade" id="transactions" role="tabpanel" aria-labelledby="transactions-tab">
                        <h4 class="mb-3">ธุรกรรมล่าสุด</h4>

                        @if ($recentTransactions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>รหัส</th>
                                            <th>ประเภท</th>
                                            <th>จำนวนเงิน</th>
                                            <th>สถานะ</th>
                                            <th>วันที่</th>
                                            <th>หมายเหตุ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentTransactions as $transaction)
                                            <tr>
                                                <td>{{ $transaction->transaction_id }}</td>
                                                <td>
                                                    @if ($transaction->type === 'payment')
                                                        <span class="badge bg-primary">ชำระเงิน</span>
                                                    @elseif($transaction->type === 'payout')
                                                        <span class="badge bg-success">รับเงิน</span>
                                                    @elseif($transaction->type === 'refund')
                                                        <span class="badge bg-warning">คืนเงิน</span>
                                                    @elseif($transaction->type === 'topup')
                                                        <span class="badge bg-info">เติมเงิน</span>
                                                    @elseif($transaction->type === 'withdrawal')
                                                        <span class="badge bg-danger">ถอนเงิน</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $transaction->type }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ number_format($transaction->amount, 2) }} ฿</td>
                                                <td>
                                                    @if ($transaction->status === 'successful')
                                                        <span class="badge bg-success">สำเร็จ</span>
                                                    @elseif($transaction->status === 'pending')
                                                        <span class="badge bg-warning">รอดำเนินการ</span>
                                                    @elseif($transaction->status === 'failed')
                                                        <span class="badge bg-danger">ล้มเหลว</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $transaction->status }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                                <td>{{ $transaction->notes ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="text-center mt-3">
                                <a href="{{ route('admin.transactions', ['user_id' => $user->id]) }}"
                                    class="btn btn-sm btn-alt-primary">
                                    <i class="fa fa-list me-1"></i> ดูธุรกรรมทั้งหมด
                                </a>
                            </div>
                        @else
                            <div class="alert alert-info">
                                ยังไม่มีข้อมูลธุรกรรม
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal ปรับยอดเงินในวอลเล็ต -->
    <div class="modal fade" id="modal-adjust-balance" tabindex="-1" role="dialog"
        aria-labelledby="modal-adjust-balance" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">ปรับยอดเงินในวอลเล็ต</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.users.adjust-balance', $user) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <p>ยอดเงินปัจจุบัน: <strong>{{ number_format($user->balance, 2) }} ฿</strong></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="amount">จำนวนเงิน (บาท)</label>
                            <input type="number" class="form-control" id="amount" name="amount" min="1"
                                step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="operation">การดำเนินการ</label>
                            <div class="space-y-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="operation_add" name="operation"
                                        value="add" checked>
                                    <label class="form-check-label" for="operation_add">
                                        <i class="fa fa-plus-circle text-success"></i> เพิ่มเงิน
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="operation_subtract"
                                        name="operation" value="subtract">
                                    <label class="form-check-label" for="operation_subtract">
                                        <i class="fa fa-minus-circle text-danger"></i> หักเงิน
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="notes">หมายเหตุ</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-alt-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-alt-primary">ยืนยัน</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
