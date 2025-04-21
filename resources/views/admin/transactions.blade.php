@extends('layouts.app')

@section('title', 'ธุรกรรมการเงิน')
@section('subtitle', 'ประวัติธุรกรรมทางการเงินในระบบ')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">หน้าหลัก</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">แดชบอร์ดแอดมิน</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">ธุรกรรมการเงิน</li>
@endsection

@section('content')
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">ประวัติธุรกรรมทั้งหมด</h3>
            <div class="block-options">
                <div class="dropdown">
                    <button type="button" class="btn btn-sm btn-alt-secondary" id="dropdown-type-filter" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-fw fa-filter me-1"></i> กรองประเภท
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-type-filter">
                        <a class="dropdown-item {{ request('type') == '' ? 'active' : '' }}" href="{{ route('admin.transactions') }}">ทั้งหมด</a>
                        <a class="dropdown-item {{ request('type') == 'payment' ? 'active' : '' }}" href="{{ route('admin.transactions', ['type' => 'payment']) }}">ชำระเงิน</a>
                        <a class="dropdown-item {{ request('type') == 'payout' ? 'active' : '' }}" href="{{ route('admin.transactions', ['type' => 'payout']) }}">จ่ายให้ผู้ขาย</a>
                        <a class="dropdown-item {{ request('type') == 'refund' ? 'active' : '' }}" href="{{ route('admin.transactions', ['type' => 'refund']) }}">คืนเงิน</a>
                        <a class="dropdown-item {{ request('type') == 'topup' ? 'active' : '' }}" href="{{ route('admin.transactions', ['type' => 'topup']) }}">เติมเงิน</a>
                        <a class="dropdown-item {{ request('type') == 'withdrawal' ? 'active' : '' }}" href="{{ route('admin.transactions', ['type' => 'withdrawal']) }}">ถอนเงิน</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="block-content">
            <div class="table-responsive">
                <table class="table table-striped table-vcenter">
                    <thead>
                        <tr>
                            <th>รหัสธุรกรรม</th>
                            <th>ผู้ใช้</th>
                            <th>วันที่</th>
                            <th>ประเภท</th>
                            <th>ออเดอร์</th>
                            <th>จำนวนเงิน</th>
                            <th>สถานะ</th>
                            <th>หมายเหตุ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->transaction_id }}</td>
                                <td>
                                    <a href="{{ route('admin.users.show', $transaction->user) }}">{{ $transaction->user->name }}</a>
                                </td>
                                <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($transaction->type === 'payment')
                                        <span class="badge bg-primary">ชำระเงิน</span>
                                    @elseif($transaction->type === 'payout')
                                        <span class="badge bg-success">จ่ายให้ผู้ขาย</span>
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
                                <td>
                                    @if($transaction->order)
                                        <a href="{{ route('admin.orders.show', $transaction->order) }}">#{{ $transaction->order->order_number }}</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ number_format($transaction->amount, 2) }} ฿</td>
                                <td>
                                    @if($transaction->status === 'successful')
                                        <span class="badge bg-success">สำเร็จ</span>
                                    @elseif($transaction->status === 'pending')
                                        <span class="badge bg-warning">รอดำเนินการ</span>
                                    @elseif($transaction->status === 'failed')
                                        <span class="badge bg-danger">ล้มเหลว</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $transaction->status }}</span>
                                    @endif
                                </td>
                                <td>{{ $transaction->notes ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">ไม่พบข้อมูลธุรกรรม</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-2">
                {{ $transactions->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
@endsection