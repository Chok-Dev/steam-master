@extends('layouts.app')

@section('title', 'จัดการออเดอร์')
@section('subtitle', 'จัดการรายการออเดอร์ทั้งหมดในระบบ')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">หน้าหลัก</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">แดชบอร์ดแอดมิน</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">จัดการออเดอร์</li>
@endsection

@section('content')
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">ออเดอร์ทั้งหมด</h3>
            <div class="block-options">
                <a href="{{ route('admin.orders.index') }}" class="btn btn-alt-secondary">
                    <i class="fa fa-refresh me-1"></i> รีเฟรช
                </a>
            </div>
        </div>
        <div class="block-content">
            <!-- ฟิลเตอร์และการค้นหา -->
            <div class="mb-4">
                <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-body">
                                <i class="fa fa-search"></i>
                            </span>
                            <input type="text" class="form-control" name="search" placeholder="ค้นหาเลขออเดอร์"
                                value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="status">
                            <option value="">ทุกสถานะ</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>รอดำเนินการ
                            </option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>
                                กำลังดำเนินการ</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>สำเร็จ
                            </option>
                            <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>ยกเลิก</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="user_id">
                            <option value="">ทุกผู้ซื้อ</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="sort">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>ล่าสุด</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>เก่าสุด</option>
                            <option value="total_asc" {{ request('sort') == 'total_asc' ? 'selected' : '' }}>ราคาน้อย-มาก
                            </option>
                            <option value="total_desc" {{ request('sort') == 'total_desc' ? 'selected' : '' }}>ราคามาก-น้อย
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex">
                            <button type="submit" class="btn btn-alt-primary me-2">
                                <i class="fa fa-filter me-1"></i> กรอง
                            </button>
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-alt-secondary">
                                <i class="fa fa-times me-1"></i> ล้าง
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- ตารางออเดอร์ -->
            <div class="table-responsive">
                <table class="table table-striped table-vcenter">
                    <thead>
                        <tr>
                            <th>เลขออเดอร์</th>
                            <th>ผู้ซื้อ</th>
                            <th>รายการ</th>
                            <th>มูลค่ารวม</th>
                            <th>สถานะ</th>
                            <th>วันที่สั่งซื้อ</th>
                            <th class="text-center">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}">#{{ $order->order_number }}</a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.users.show', $order->user) }}">{{ $order->user->name }}</a>
                                </td>
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
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.orders.show', $order) }}"
                                            class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip"
                                            title="ดูรายละเอียด">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.orders.edit', $order) }}"
                                            class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="แก้ไข">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <!-- ปุ่มเปลี่ยนสถานะ -->
                                        <button type="button" class="btn btn-sm btn-alt-secondary"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-cog"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            @if ($order->status !== 'pending')
                                                <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="pending">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fa fa-clock-o text-warning me-1"></i> ตั้งเป็นรอดำเนินการ
                                                    </button>
                                                </form>
                                            @endif
                                            @if ($order->status !== 'processing')
                                                <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="processing">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fa fa-spinner text-info me-1"></i> ตั้งเป็นกำลังดำเนินการ
                                                    </button>
                                                </form>
                                            @endif
                                            @if ($order->status !== 'completed')
                                                <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="completed">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fa fa-check text-success me-1"></i> ตั้งเป็นสำเร็จ
                                                    </button>
                                                </form>
                                            @endif
                                            @if ($order->status !== 'canceled')
                                                <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="canceled">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fa fa-times text-danger me-1"></i> ตั้งเป็นยกเลิก
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">ไม่พบข้อมูลออเดอร์</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- การแบ่งหน้า -->
            <div class="d-flex justify-content-center mt-4">
                {{ $orders->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
@endsection
