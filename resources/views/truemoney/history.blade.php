@extends('layouts.app')
@section('title', 'ประวัติการเติมเงิน')
@section('subtitle', 'ประวัติการเติมเงินและการชำระเงินของคุณ')
@section('breadcrumb')
  <li class="breadcrumb-item">
    <a href="{{ route('home') }}">หน้าหลัก</a>
  </li>
  <li class="breadcrumb-item">
    <a href="{{ route('topup') }}">เติมเงิน</a>
  </li>
  <li class="breadcrumb-item active" aria-current="page">ประวัติการเติมเงิน</li>
@endsection
@section('content')
  <div class="block block-rounded">
    <div class="block-header block-header-default">
      <h3 class="block-title">ประวัติการเติมเงินและการชำระเงิน</h3>
    </div>
    <div class="block-content">
      <div class="table-responsive">
        <table class="table table-striped table-vcenter">
          <thead>
            <tr>
              <th>รหัสธุรกรรม</th>
              <th>วันที่</th>
              <th>ประเภท</th>
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
                  @if($transaction->type === 'topup')
                    <span class="badge bg-success">เติมเงิน</span>
                  @elseif($transaction->type === 'payment')
                    <span class="badge bg-primary">ชำระเงิน</span>
                  @else
                    <span class="badge bg-secondary">{{ $transaction->type }}</span>
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
                <td colspan="6" class="text-center">ไม่พบประวัติการทำรายการ</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      
      <div class="d-flex justify-content-center mt-4">
        {{ $transactions->links('pagination::bootstrap-4') }}
      </div>
      
      <div class="text-center mt-4">
        <a href="{{ route('topup') }}" class="btn btn-alt-primary">
          <i class="fa fa-wallet me-1"></i> กลับไปหน้าเติมเงิน
        </a>
      </div>
    </div>
  </div>
  
  <!-- สรุปการเติมเงิน -->
  <div class="block block-rounded">
    <div class="block-header block-header-default">
      <h3 class="block-title">สรุปการเติมเงิน</h3>
    </div>
    <div class="block-content">
      <div class="row text-center">
        <div class="col-md-4">
          <div class="py-3">
            <div class="item item-circle bg-success-light mx-auto">
              <i class="fa fa-wallet text-success"></i>
            </div>
            <div class="fs-1 fw-bold mt-3">{{ number_format(auth()->user()->balance, 2) }} ฿</div>
            <div class="fs-sm fw-semibold text-uppercase text-muted">ยอดเงินคงเหลือ</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="py-3">
            <div class="item item-circle bg-info-light mx-auto">
              <i class="fa fa-plus-circle text-info"></i>
            </div>
            <div class="fs-1 fw-bold mt-3">{{ number_format($transactions->where('type', 'topup')->where('status', 'successful')->sum('amount'), 2) }} ฿</div>
            <div class="fs-sm fw-semibold text-uppercase text-muted">ยอดเติมเงินรวม</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="py-3">
            <div class="item item-circle bg-warning-light mx-auto">
              <i class="fa fa-shopping-cart text-warning"></i>
            </div>
            <div class="fs-1 fw-bold mt-3">{{ number_format($transactions->where('type', 'payment')->where('status', 'successful')->sum('amount'), 2) }} ฿</div>
            <div class="fs-sm fw-semibold text-uppercase text-muted">ยอดใช้จ่ายรวม</div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection