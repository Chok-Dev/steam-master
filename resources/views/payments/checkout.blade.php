@extends('layouts.app')

@section('title', 'ชำระเงิน')
@section('subtitle', 'ชำระเงินสำหรับออเดอร์ #' . $order->order_number)

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">หน้าหลัก</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('orders.index') }}">รายการสั่งซื้อ</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('orders.show', $order) }}">ออเดอร์ #{{ $order->order_number }}</a>
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
                    <form action="{{ route('payments.process', $order) }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <div class="space-y-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="payment_method_credit_card" name="payment_method" value="credit_card" checked>
                                    <label class="form-check-label" for="payment_method_credit_card">
                                        <i class="fa fa-credit-card me-1"></i> บัตรเครดิต/เดบิต
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="payment_method_qr" name="payment_method" value="qr">
                                    <label class="form-check-label" for="payment_method_qr">
                                        <i class="fa fa-qrcode me-1"></i> QR Payment
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="payment_method_bank" name="payment_method" value="bank">
                                    <label class="form-check-label" for="payment_method_bank">
                                        <i class="fa fa-university me-1"></i> โอนผ่านธนาคาร
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="payment_method_wallet" name="payment_method" value="wallet">
                                    <label class="form-check-label" for="payment_method_wallet">
                                        <i class="fa fa-wallet me-1"></i> วอลเล็ต ({{ number_format(auth()->user()->balance, 2) }} ฿)
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div id="credit_card_details" class="payment-details">
                            <div class="row mb-4">
                                <div class="col-12">
                                    <label class="form-label" for="card_number">หมายเลขบัตร</label>
                                    <input type="text" class="form-control" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19">
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-6">
                                    <label class="form-label" for="expiry_date">วันหมดอายุ</label>
                                    <input type="text" class="form-control" id="expiry_date" name="expiry_date" placeholder="MM/YY" maxlength="5">
                                </div>
                                <div class="col-6">
                                    <label class="form-label" for="cvv">CVV</label>
                                    <input type="text" class="form-control" id="cvv" name="cvv" placeholder="123" maxlength="4">
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-12">
                                    <label class="form-label" for="card_name">ชื่อบนบัตร</label>
                                    <input type="text" class="form-control" id="card_name" name="card_name" placeholder="JOHN DOE">
                                </div>
                            </div>
                        </div>
                        
                        <div id="qr_details" class="payment-details d-none">
                            <div class="row mb-4 justify-content-center">
                                <div class="col-12 col-md-6 text-center">
                                    <img src="{{ asset('assets/img/qr-code-example.png') }}" alt="QR Code" class="img-fluid mb-3">
                                    <p class="fs-sm">สแกน QR Code เพื่อชำระเงิน</p>
                                </div>
                            </div>
                        </div>
                        
                        <div id="bank_details" class="payment-details d-none">
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ธนาคาร</th>
                                            <th>ชื่อบัญชี</th>
                                            <th>เลขที่บัญชี</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>ธนาคารกสิกรไทย</td>
                                            <td>บริษัท สตีม มาร์เก็ต จำกัด</td>
                                            <td>123-4-56789-0</td>
                                        </tr>
                                        <tr>
                                            <td>ธนาคารไทยพาณิชย์</td>
                                            <td>บริษัท สตีม มาร์เก็ต จำกัด</td>
                                            <td>098-7-65432-1</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label" for="bank_transfer_reference">เลขที่อ้างอิงการโอน</label>
                                <input type="text" class="form-control" id="bank_transfer_reference" name="bank_transfer_reference" placeholder="เลขที่อ้างอิงการโอน">
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label" for="bank_transfer_proof">หลักฐานการโอน</label>
                                <input type="file" class="form-control" id="bank_transfer_proof" name="bank_transfer_proof">
                                <div class="form-text">อัพโหลดสลิปหรือหลักฐานการโอนเงิน</div>
                            </div>
                        </div>
                        
                        <div id="wallet_details" class="payment-details d-none">
                            <div class="alert alert-info">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="fa fa-info-circle fa-2x"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5>ยอดเงินคงเหลือ: {{ number_format(auth()->user()->balance, 2) }} ฿</h5>
                                        @if(auth()->user()->balance < $order->total_amount)
                                            <p class="mb-0">ยอดเงินในวอลเล็ตไม่เพียงพอ กรุณาเลือกวิธีการชำระเงินอื่น หรือเติมเงินในวอลเล็ต</p>
                                        @else
                                            <p class="mb-0">เงินจะถูกหักจากวอลเล็ตของคุณทันทีหลังจากยืนยันการชำระเงิน</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <button type="submit" class="btn btn-alt-primary">
                                <i class="fa fa-check me-1"></i> ยืนยันการชำระเงิน
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- สรุปออเดอร์ -->
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">สรุปการสั่งซื้อ</h3>
                </div>
                <div class="block-content">
                    <div class="fs-4 mb-1">ออเดอร์ #{{ $order->order_number }}</div>
                    <div class="fs-sm text-muted mb-3">
                        วันที่สั่งซื้อ: {{ $order->created_at->format('d/m/Y H:i') }}
                    </div>
                    
                    <table class="table table-vcenter">
                        <tbody>
                            @foreach($order->orderItems as $item)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $item->product->name }}</div>
                                        <div class="fs-sm text-muted">{{ $item->product->category->name }}</div>
                                    </td>
                                    <td class="text-end">{{ number_format($item->price, 2) }} ฿</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <div class="text-end border-top pt-3 mt-3">
                        <div class="mb-2">
                            <span class="h5 fw-semibold">รวมทั้งสิ้น:</span>
                            <span class="h5 fw-semibold">{{ number_format($order->total_amount, 2) }} ฿</span>
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
    document.addEventListener('DOMContentLoaded', function() {
        // จัดการการแสดงรายละเอียดวิธีการชำระเงิน
        const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
        const paymentDetails = document.querySelectorAll('.payment-details');
        
        function updatePaymentDetails() {
            // ซ่อนรายละเอียดทั้งหมดก่อน
            paymentDetails.forEach(detail => {
                detail.classList.add('d-none');
            });
            
            // แสดงรายละเอียดของวิธีการชำระเงินที่เลือก
            const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
            document.getElementById(`${selectedMethod}_details`).classList.remove('d-none');
        }
        
        // เพิ่มการฟังเหตุการณ์การเปลี่ยนแปลงสำหรับวิธีการชำระเงิน
        paymentMethods.forEach(method => {
            method.addEventListener('change', updatePaymentDetails);
        });
        
        // รัน function เริ่มต้นเพื่อแสดงรายละเอียดที่ถูกต้อง
        updatePaymentDetails();
    });
</script>
@endpush