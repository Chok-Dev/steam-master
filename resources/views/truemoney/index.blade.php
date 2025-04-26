@extends('layouts.app')
@section('title', 'เติมเงิน')
@section('subtitle', 'เลือกวิธีการเติมเงินเข้าสู่ระบบ')
@section('breadcrumb')
  <li class="breadcrumb-item">
    <a href="{{ route('home') }}">หน้าหลัก</a>
  </li>
  <li class="breadcrumb-item active" aria-current="page">เติมเงิน</li>
@endsection
@section('content')
  <div class="row">
    <div class="col-md-8 mx-auto">
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">วิธีการเติมเงิน</h3>
        </div>
        <div class="block-content">
          <div class="row mb-4">
            <div class="col-12 text-center mb-4">
              <div class="fs-1 fw-bold text-success">฿{{ number_format(auth()->user()->balance, 2) }}</div>
              <div class="fs-sm text-muted">ยอดเงินคงเหลือในระบบ</div>
            </div>
          </div>
          <div class="row">
            <!-- TrueMoney Wallet -->
            <div class="col-md-6">
              <a href="{{ route('toupTruemoney') }}" class="block block-rounded block-link-shadow h-100 mb-0">
                <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                  <div class="item item-circle bg-danger-light">
                    <i class="fa fa-wallet text-danger"></i>
                  </div>
                  <div class="ms-3 text-end">
                    <p class="text-muted mb-0">เติมด้วย</p>
                    <h5 class="fs-4 mb-0">True Money</h5>
                  </div>
                </div>
                <div class="block-content block-content-full block-content-sm bg-body-light">
                  <span class="fs-sm text-muted">เติมเงินผ่านลิงก์ซองอั่งเปา TrueMoney</span>
                  <i class="fa fa-arrow-right float-end opacity-25"></i>
                </div>
              </a>
            </div>

            <!-- ChillPay -->
            <div class="col-md-6">
              <a href="{{ route('toupChillpay') }}" class="block block-rounded block-link-shadow h-100 mb-0">
                <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                  <div class="item item-circle bg-primary-light">
                    <i class="fa fa-credit-card text-primary"></i>
                  </div>
                  <div class="ms-3 text-end">
                    <p class="text-muted mb-0">เติมด้วย</p>
                    <h5 class="fs-4 mb-0">ChillPay</h5>
                  </div>
                </div>
                <div class="block-content block-content-full block-content-sm bg-body-light">
                  <span class="fs-sm text-muted">เติมเงินผ่านบัตรเครดิต/โอนเงิน/QR Payment</span>
                  <i class="fa fa-arrow-right float-end opacity-25"></i>
                </div>
              </a>
            </div>
          </div>

          <div class="block block-rounded mt-4">
            <div class="block-header block-header-default">
              <h3 class="block-title">ข้อมูลเพิ่มเติม</h3>
            </div>
            <div class="block-content">
              <div class="row">
                <div class="col-md-6">
                  <h5>ข้อดีของการเติมเงิน</h5>
                  <ul>
                    <li>ชำระเงินได้อย่างรวดเร็ว ไม่ต้องกรอกข้อมูลทุกครั้ง</li>
                    <li>ได้รับสิทธิพิเศษสำหรับสมาชิกที่เติมเงิน</li>
                    <li>ระบบปลอดภัย มีการเข้ารหัสข้อมูลทุกขั้นตอน</li>
                    <li>มีประวัติการเติมเงินให้ตรวจสอบย้อนหลังได้</li>
                  </ul>
                </div>
                <div class="col-md-6">
                  <h5>คำถามที่พบบ่อย</h5>
                  <div class="accordion" id="accordionFAQ">
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                          data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                          เงินที่เติมมีอายุการใช้งานหรือไม่?
                        </button>
                      </h2>
                      <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                        data-bs-parent="#accordionFAQ">
                        <div class="accordion-body">
                          เงินที่เติมเข้าระบบไม่มีวันหมดอายุ สามารถใช้ได้ตลอดเวลาที่บัญชียังคงอยู่ในระบบ
                        </div>
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                          data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                          ถ้าเติมเงินแล้วเกิดปัญหาต้องทำอย่างไร?
                        </button>
                      </h2>
                      <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                        data-bs-parent="#accordionFAQ">
                        <div class="accordion-body">
                          หากเกิดปัญหาในการเติมเงิน คุณสามารถติดต่อเจ้าหน้าที่ผ่านช่องทาง <a
                            href="">ติดต่อเรา</a> พร้อมแนบหลักฐานการชำระเงิน เช่น
                          สกรีนช็อตหรือสลิปการโอนเงิน
                        </div>
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                          data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                          สามารถขอถอนเงินคืนได้หรือไม่?
                        </button>
                      </h2>
                      <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                        data-bs-parent="#accordionFAQ">
                        <div class="accordion-body">
                          โดยปกติเงินที่เติมเข้าระบบแล้วจะไม่สามารถถอนคืนได้ อย่างไรก็ตาม หากมีกรณีพิเศษ
                          โปรดติดต่อเจ้าหน้าที่เพื่อพิจารณาเป็นรายกรณี
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="text-center mt-4">
                <a href="{{ route('topup.history') }}" class="btn btn-alt-info">
                  <i class="fa fa-history me-1"></i> ดูประวัติการเติมเงิน
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
