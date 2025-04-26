@extends('layouts.app')

@section('title', 'เติมเงิน ChillPay')
@section('subtitle', 'เพิ่มยอดเงินผ่านระบบชำระเงิน ChillPay')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">หน้าหลัก</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('topup') }}">เติมเงิน</a>
      </li>
    <li class="breadcrumb-item active" aria-current="page">เติมเงิน ChillPay</li>
@endsection

@section('content')
    <!-- นโยบายการเติมเงิน -->
   
        {{-- <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">นโยบายการเติมเงิน</h3>
            </div>
            <div class="block-content">
                <div class="alert alert-warning">
                    <h4 class="alert-heading">โปรดอ่านและยอมรับนโยบายการเติมเงิน</h4>
                    <p>ก่อนทำการเติมเงิน คุณจำเป็นต้องอ่านและยอมรับนโยบายการเติมเงินของเรา</p>
                </div>
                
                <div class="mb-4 policy-content p-3 bg-body-light rounded">
                   
                </div>
                
                <form action="" method="POST">
                    @csrf
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="accept-policy" required>
                        <label class="form-check-label" for="accept-policy">
                            ฉันได้อ่านและยอมรับนโยบายการเติมเงิน
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" id="accept-button" disabled>
                        <i class="fa fa-check me-1"></i> ยอมรับนโยบายและดำเนินการต่อ
                    </button>
                </form>
            </div>
        </div> --}}
   
        <div class="row">
            <div class="col-md-7">
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">เติมเงินผ่านระบบ ChillPay</h3>
                    </div>
                    <div class="block-content">
                        <div class="alert alert-info">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fa fa-info-circle fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5>เติมเงินได้หลากหลายช่องทาง</h5>
                                    <p class="mb-0">ระบบ ChillPay รองรับการชำระเงินผ่านบัตรเครดิต/เดบิต, QR Payment, โอนผ่านธนาคาร และอื่นๆ</p>
                                    <p class="mb-0">ค่าธรรมเนียม 2.9% ขั้นต่ำ 15 บาท</p>
                                </div>
                            </div>
                        </div>
                        
                        <form action="{{ route('topup.chillpay.process') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label" for="amount">จำนวนเงินที่ต้องการเติม (บาท)</label>
                                <div class="input-group">
                                    <span class="input-group-text">฿</span>
                                    <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" placeholder="ระบุจำนวนเงิน" value="{{ old('amount', 100) }}" min="20" step="1">
                                </div>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="fa fa-info-circle me-1"></i> จำนวนเงินขั้นต่ำ 20 บาท | ค่าธรรมเนียม 2.9% ขั้นต่ำ 15 บาท
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-12">
                                    <label class="form-label d-block">ตัวเลือกการเติมเงินด่วน</label>
                                    <div class="btn-group w-100" role="group">
                                        <button type="button" class="btn btn-alt-secondary quick-amount" data-amount="50">฿50</button>
                                        <button type="button" class="btn btn-alt-secondary quick-amount" data-amount="100">฿100</button>
                                        <button type="button" class="btn btn-alt-secondary quick-amount" data-amount="200">฿200</button>
                                        <button type="button" class="btn btn-alt-secondary quick-amount" data-amount="500">฿500</button>
                                        <button type="button" class="btn btn-alt-secondary quick-amount" data-amount="1000">฿1,000</button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="h-captcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                                @error('g-recaptcha-response')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-credit-card me-1"></i> ดำเนินการเติมเงิน
                                </button>
                                <a href="{{ route('toupTruemoney') }}" class="btn btn-alt-secondary">
                                    <i class="fa fa-wallet me-1"></i> เติมด้วย TrueMoney
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-5">
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">ช่องทางการชำระเงิน</h3>
                    </div>
                    <div class="block-content">
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">บัตรเครดิต/เดบิต</h5>
                                <div class="payment-methods d-flex flex-wrap">
                                    <div class="p-2"><img src="{{ asset('assets/img/payments/visa.png') }}" alt="Visa" height="30"></div>
                                    <div class="p-2"><img src="{{ asset('assets/img/payments/mastercard.png') }}" alt="Mastercard" height="30"></div>
                                    <div class="p-2"><img src="{{ asset('assets/img/payments/jcb.png') }}" alt="JCB" height="30"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">Mobile Banking / ธนาคาร</h5>
                                <div class="payment-methods d-flex flex-wrap">
                                    <div class="p-2"><img src="{{ asset('assets/img/payments/kbank.png') }}" alt="KBank" height="30"></div>
                                    <div class="p-2"><img src="{{ asset('assets/img/payments/scb.png') }}" alt="SCB" height="30"></div>
                                    <div class="p-2"><img src="{{ asset('assets/img/payments/bbl.png') }}" alt="Bangkok Bank" height="30"></div>
                                    <div class="p-2"><img src="{{ asset('assets/img/payments/ktb.png') }}" alt="Krung Thai Bank" height="30"></div>
                                    <div class="p-2"><img src="{{ asset('assets/img/payments/bay.png') }}" alt="Bank of Ayudhya" height="30"></div>
                                    <div class="p-2"><img src="{{ asset('assets/img/payments/ttb.png') }}" alt="TTB" height="30"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">QR Payment / E-Wallet</h5>
                                <div class="payment-methods d-flex flex-wrap">
                                    <div class="p-2"><img src="{{ asset('assets/img/payments/promptpay.png') }}" alt="PromptPay" height="30"></div>
                                    <div class="p-2"><img src="{{ asset('assets/img/payments/truemoney.png') }}" alt="TrueMoney" height="30"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-warning">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fa fa-exclamation-triangle fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5>ข้อควรทราบ</h5>
                                    <ul class="mb-0">
                                        <li>เงินจะถูกเติมเข้าระบบทันทีหลังจากที่การชำระเงินเสร็จสมบูรณ์</li>
                                        <li>กรณีเกิดปัญหาในการเติมเงิน โปรดติดต่อทีมงานผ่านช่องทางการติดต่อ</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
   
@endsection

@push('scripts')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // สำหรับปุ่มยอมรับนโยบาย
        const acceptCheckbox = document.getElementById('accept-policy');
        const acceptButton = document.getElementById('accept-button');
        
        if (acceptCheckbox && acceptButton) {
            acceptCheckbox.addEventListener('change', function() {
                acceptButton.disabled = !this.checked;
            });
        }
        
        // สำหรับปุ่มเลือกจำนวนเงินด่วน
        const quickButtons = document.querySelectorAll('.quick-amount');
        const amountInput = document.getElementById('amount');
        
        if (quickButtons.length > 0 && amountInput) {
            quickButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const amount = this.getAttribute('data-amount');
                    amountInput.value = amount;
                    
                    // ลบคลาส active จากทุกปุ่ม
                    quickButtons.forEach(btn => {
                        btn.classList.remove('active');
                        btn.classList.remove('btn-primary');
                        btn.classList.add('btn-alt-secondary');
                    });
                    
                    // เพิ่มคลาส active ให้ปุ่มที่กด
                    this.classList.add('active');
                    this.classList.remove('btn-alt-secondary');
                    this.classList.add('btn-primary');
                });
            });
        }
    });
</script>
@endpush