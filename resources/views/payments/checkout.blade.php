@extends('layouts.app')

@section('title', 'ชำระเงิน')
@section('subtitle', 'ชำระเงินสำหรับการสั่งซื้อสินค้า')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">หน้าหลัก</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('products.index') }}">สินค้า</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('products.show', $checkoutData['product']) }}">{{ $checkoutData['product']->name }}</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">ชำระเงิน</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <!-- วิธีการชำระเงิน -->
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">เลือกวิธีการชำระเงิน</h3>
                </div>
                <div class="block-content">
                    <form action="{{ route('payments.process') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <div class="alert alert-primary">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="fa fa-wallet fa-2x"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5>ชำระด้วยวอลเล็ต</h5>
                                        <p class="mb-0">ยอดเงินคงเหลือของคุณ: <strong>{{ number_format(auth()->user()->balance, 2) }} ฿</strong></p>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="payment_method" value="wallet">
                        </div>
                        
                        <div class="wallet-details">
                            <div class="alert {{ auth()->user()->balance >= $checkoutData['total'] ? 'alert-success' : 'alert-danger' }}">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="fa {{ auth()->user()->balance >= $checkoutData['total'] ? 'fa-check-circle' : 'fa-exclamation-circle' }} fa-2x"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5>การตรวจสอบวอลเล็ต</h5>
                                        <p>ยอดเงินคงเหลือ: <strong>{{ number_format(auth()->user()->balance, 2) }} ฿</strong></p>
                                        <p>ยอดที่ต้องชำระ: <strong>{{ number_format($checkoutData['total'], 2) }} ฿</strong></p>
                                        @if(auth()->user()->balance < $checkoutData['total'])
                                            <p class="mb-0 text-danger"><strong>ยอดเงินในวอลเล็ตไม่เพียงพอ</strong> กรุณา<a href="{{ route('topup') }}">เติมเงิน</a>ก่อนทำการชำระ</p>
                                        @else
                                            <p class="mb-0 text-success"><strong>ยอดเงินเพียงพอ</strong> คุณสามารถชำระเงินได้ทันที</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <button type="submit" class="btn btn-alt-primary {{ auth()->user()->balance < $checkoutData['total'] ? 'disabled' : '' }}" {{ auth()->user()->balance < $checkoutData['total'] ? 'disabled' : '' }}>
                                <i class="fa fa-check me-1"></i> ยืนยันการชำระเงิน
                            </button>
                            @if(auth()->user()->balance < $checkoutData['total'])
                                <a href="{{ route('topup') }}" class="btn btn-success">
                                    <i class="fa fa-wallet me-1"></i> เติมเงิน
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- สรุปการสั่งซื้อ -->
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">สรุปการสั่งซื้อ</h3>
                </div>
                <div class="block-content">
                    <div class="fs-4 mb-1">{{ $checkoutData['product']->name }}</div>
                    <div class="fs-sm text-muted mb-3">
                        วันที่: {{ now()->format('d/m/Y H:i') }}
                    </div>
                    
                    <table class="table table-vcenter">
                        <tbody>
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $checkoutData['product']->name }}</div>
                                    <div class="fs-sm text-muted">{{ $checkoutData['product']->category->name }}</div>
                                </td>
                                <td class="text-end">{{ number_format($checkoutData['product']->price, 2) }} ฿</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="text-end border-top pt-3 mt-3">
                        <div class="mb-2">
                            <span class="h5 fw-semibold">รวมทั้งสิ้น:</span>
                            <span class="h5 fw-semibold">{{ number_format($checkoutData['total'], 2) }} ฿</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ข้อมูลการชำระเงิน -->
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">ข้อมูลการชำระเงิน</h3>
                </div>
                <div class="block-content">
                    <div class="alert alert-info mb-0">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="fa fa-info-circle fa-2x"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5>ระบบชำระเงินปลอดภัย</h5>
                                <p class="mb-0">เงินของคุณจะถูกเก็บไว้ในระบบ Escrow จนกว่าคุณจะได้รับสินค้าและยืนยันการรับสินค้า</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // ไม่จำเป็นต้องมีการสลับแสดงผลระหว่างวิธีการชำระเงินต่างๆ เนื่องจากมีแค่วิธีเดียว
</script>
@endpush