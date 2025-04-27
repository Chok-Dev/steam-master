@extends('layouts.app')
@section('title', 'เติมเงิน TrueMoney')
@section('subtitle', 'เพิ่มยอปเงินด้วยซองอั่งเปา TrueMoney')
@section('breadcrumb')
  <li class="breadcrumb-item">
    <a href="{{ route('home') }}">หน้าหลัก</a>
  </li>
  <li class="breadcrumb-item">
    <a href="{{ route('topup') }}">เติมเงิน</a>
  </li>
  <li class="breadcrumb-item active" aria-current="page">เติมเงิน TrueMoney</li>
@endsection
@section('content')
  <!-- นโยบายการเติมเงิน -->

  <div class="row">
    <div class="col-md-6">
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">เติมเงินด้วยซองอั่งเปา TrueMoney</h3>
        </div>
        <div class="block-content">
          <div class="alert alert-info">
            <div class="d-flex">
              <div class="flex-shrink-0">
                <i class="fa fa-info-circle fa-2x"></i>
              </div>
              <div class="flex-grow-1 ms-3">
                <h5>วิธีการเติมเงิน</h5>
                <p class="mb-0">เติมเงินง่ายๆ เพียงแค่คัดลอกลิงก์ซองอั่งเปา TrueMoney และวางลงในช่องด้านล่าง</p>
              </div>
            </div>
          </div>

          <form action="{{ route('topup.truemoney.process') }}" method="POST">
            @csrf
            <div class="mb-4">
              <label class="form-label" for="voucher">ลิงก์ซองอั่งเปา TrueMoney</label>
              <input type="text" class="form-control @error('voucher') is-invalid @enderror" id="voucher"
                name="voucher" placeholder="https://gift.truemoney.com/campaign/?v=xxx" value="{{ old('voucher') }}">
              @error('voucher')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="form-text">
                <i class="fa fa-info-circle me-1"></i> กรุณาใส่ลิงก์ซองอั่งเปา TrueMoney ที่ได้รับ
              </div>
            </div>

          {{--   <div class="mb-4">
              <div class="h-captcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
              @error('g-recaptcha-response')
                <div class="text-danger mt-2">{{ $message }}</div>
              @enderror
            </div>
 --}}
            <div class="mb-4">
              <button type="submit" class="btn btn-primary">
                <i class="fa fa-money-bill-wave me-1"></i> เติมเงิน
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">คำแนะนำการเติมเงิน</h3>
        </div>
        <div class="block-content">
          <div class="mb-4">
            <h5>วิธีสร้างซองอั่งเปา TrueMoney</h5>
            <ol class="list-group list-group-numbered mb-3">
              <li class="list-group-item d-flex">
                <div class="ms-2">เปิดแอป TrueMoney Wallet บนมือถือของคุณ</div>
              </li>
              <li class="list-group-item d-flex">
                <div class="ms-2">เลือกเมนู "ส่งของขวัญ" หรือ "อั่งเปา"</div>
              </li>
              <li class="list-group-item d-flex">
                <div class="ms-2">เลือก "ส่งซองอั่งเปาให้เพื่อน"</div>
              </li>
              <li class="list-group-item d-flex">
                <div class="ms-2">กรอกจำนวนเงินที่ต้องการเติม</div>
              </li>
              <li class="list-group-item d-flex">
                <div class="ms-2">เลือก "ส่งลิงก์" และคัดลอกลิงก์มาวางในช่องด้านซ้าย</div>
              </li>
            </ol>
          </div>

          <div class="alert alert-warning">
            <div class="d-flex">
              <div class="flex-shrink-0">
                <i class="fa fa-exclamation-triangle fa-2x"></i>
              </div>
              <div class="flex-grow-1 ms-3">
                <h5>ข้อควรระวัง</h5>
                <ul class="mb-0">
                  <li>ไม่สามารถรับซองอั่งเปาของตัวเองได้</li>
                  <li>ซองอั่งเปามีอายุการใช้งาน โปรดใช้งานทันที</li>
                  <li>เงินจะถูกเติมเข้าบัญชีทันทีที่ระบบยืนยันซองอั่งเปาสำเร็จ</li>
                </ul>
              </div>
            </div>
          </div>

          <div class="text-center mt-4">
            <a href="{{ route('toupChillpay') }}" class="btn btn-alt-primary">
              <i class="fa fa-credit-card me-1"></i> เติมเงินด้วยวิธีอื่น (ChillPay)
            </a>
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
      const acceptCheckbox = document.getElementById('accept-policy');
      const acceptButton = document.getElementById('accept-button');

      if (acceptCheckbox && acceptButton) {
        acceptCheckbox.addEventListener('change', function() {
          acceptButton.disabled = !this.checked;
        });
      }
    });
  </script>
@endpush
