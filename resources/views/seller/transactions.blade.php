@extends('layouts.app')

@section('title', 'ธุรกรรมการเงิน')
@section('subtitle', 'ประวัติการทำธุรกรรมทางการเงินของคุณ')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">หน้าหลัก</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('seller.dashboard') }}">แดชบอร์ดผู้ขาย</a>
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
                        <a class="dropdown-item {{ request('type') == '' ? 'active' : '' }}" href="{{ route('seller.transactions') }}">ทั้งหมด</a>
                        <a class="dropdown-item {{ request('type') == 'payout' ? 'active' : '' }}" href="{{ route('seller.transactions', ['type' => 'payout']) }}">รับเงิน</a>
                        <a class="dropdown-item {{ request('type') == 'refund' ? 'active' : '' }}" href="{{ route('seller.transactions', ['type' => 'refund']) }}">คืนเงิน</a>
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
                                <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($transaction->type === 'payment')
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
                                <td>
                                    @if($transaction->order)
                                        <a href="{{ route('seller.orders.show', $transaction->order) }}">#{{ $transaction->order->order_number }}</a>
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
                                <td colspan="7" class="text-center">ไม่พบข้อมูลธุรกรรม</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
    
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">สรุปบัญชี</h3>
        </div>
        <div class="block-content">
            <div class="row text-center">
                <div class="col-md-4">
                    <div class="py-3">
                        <div class="item item-circle bg-success-light mx-auto">
                            <i class="fa fa-money-bill-alt text-success"></i>
                        </div>
                        <div class="fs-1 fw-bold mt-3">{{ number_format(auth()->user()->balance, 2) }} ฿</div>
                        <div class="fs-sm fw-semibold text-uppercase text-muted">ยอดเงินคงเหลือ</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="py-3">
                        <div class="item item-circle bg-info-light mx-auto">
                            <i class="fa fa-wallet text-info"></i>
                        </div>
                        <div class="fs-1 fw-bold mt-3">{{ number_format($transactions->where('type', 'payout')->where('status', 'successful')->sum('amount'), 2) }} ฿</div>
                        <div class="fs-sm fw-semibold text-uppercase text-muted">รายได้ทั้งหมด</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="py-3">
                        <div class="item item-circle bg-warning-light mx-auto">
                            <i class="fa fa-percentage text-warning"></i>
                        </div>
                        <div class="fs-1 fw-bold mt-3">5%</div>
                        <div class="fs-sm fw-semibold text-uppercase text-muted">ค่าธรรมเนียม</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection