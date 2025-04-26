@extends('layouts.app')

@section('title', 'ขอเป็นผู้ขาย')
@section('subtitle', 'สมัครเป็นผู้ขายบนแพลตฟอร์มของเรา')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">หน้าหลัก</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('profile.edit') }}">โปรไฟล์</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">ขอเป็นผู้ขาย</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">แบบฟอร์มขอเป็นผู้ขาย</h3>
                </div>
                <div class="block-content">
                    <div class="alert alert-info">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="fa fa-info-circle fa-2x"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5>ข้อมูลสำคัญเกี่ยวกับการเป็นผู้ขาย</h5>
                                <p>การเป็นผู้ขายบนแพลตฟอร์มของเราหมายถึงคุณสามารถลงขายสินค้าได้ แต่คุณต้องปฏิบัติตามกฎระเบียบและนโยบายของเรา</p>
                                <p class="mb-0">คำขอเป็นผู้ขายจะถูกตรวจสอบโดยทีมงานของเรา ซึ่งอาจใช้เวลา 1-3 วันทำการ</p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('seller.request.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label" for="seller_name">ชื่อร้านค้า</label>
                            <input type="text" class="form-control @error('seller_name') is-invalid @enderror" id="seller_name" name="seller_name" value="{{ old('seller_name', $user->name . ' Shop') }}" required>
                            <div class="form-text">ชื่อที่จะแสดงให้ผู้ซื้อเห็น สามารถเปลี่ยนได้ภายหลัง</div>
                            @error('seller_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label" for="seller_description">คำอธิบายร้านค้า</label>
                            <textarea class="form-control @error('seller_description') is-invalid @enderror" id="seller_description" name="seller_description" rows="5" required>{{ old('seller_description') }}</textarea>
                            <div class="form-text">แนะนำตัวเองและอธิบายว่าคุณจะขายสินค้าประเภทใด (อย่างน้อย 50 ตัวอักษร)</div>
                            @error('seller_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <div class="block block-rounded">
                                <div class="block-header block-header-default">
                                    <h3 class="block-title">ข้อกำหนดและเงื่อนไขสำหรับผู้ขาย</h3>
                                </div>
                                <div class="block-content">
                                    <div class="mb-4 policy-content p-3 bg-body-light rounded" style="max-height: 200px; overflow-y: auto;">
                                        <h5>ข้อกำหนดการใช้งานสำหรับผู้ขาย</h5>
                                        <p>1. <strong>การจดทะเบียนและการยืนยันตัวตน</strong> - ผู้ขายต้องให้ข้อมูลที่ถูกต้องและเป็นจริงในการลงทะเบียน</p>
                                        <p>2. <strong>สินค้าที่อนุญาตให้ขาย</strong> - ผู้ขายต้องขายเฉพาะสินค้าดิจิทัลที่ถูกต้องตามกฎหมายเท่านั้น</p>
                                        <p>3. <strong>ค่าธรรมเนียม</strong> - ผู้ขายต้องชำระค่าธรรมเนียม 5% ของยอดขายทั้งหมด</p>
                                        <p>4. <strong>การส่งมอบสินค้า</strong> - ผู้ขายต้องส่งมอบสินค้าให้กับผู้ซื้อภายใน 24 ชั่วโมงหลังจากการชำระเงิน</p>
                                        <p>5. <strong>การคืนเงิน</strong> - ในกรณีที่สินค้ามีปัญหา ผู้ขายต้องยินยอมให้คืนเงินตามเงื่อนไขที่กำหนด</p>
                                        <p>6. <strong>การระงับบัญชี</strong> - บัญชีผู้ขายอาจถูกระงับหากพบว่ามีการละเมิดข้อกำหนดหรือนโยบายของเรา</p>
                                        <p>7. <strong>การเปลี่ยนแปลงข้อกำหนด</strong> - เราขอสงวนสิทธิ์ในการเปลี่ยนแปลงข้อกำหนดและเงื่อนไขได้ตลอดเวลา</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input @error('accept_terms') is-invalid @enderror" type="checkbox" id="accept_terms" name="accept_terms" required>
                                <label class="form-check-label" for="accept_terms">
                                    ฉันได้อ่าน เข้าใจ และยอมรับข้อกำหนดและเงื่อนไขสำหรับผู้ขาย
                                </label>
                                @error('accept_terms')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-paper-plane me-1"></i> ส่งคำขอเป็นผู้ขาย
                            </button>
                            <a href="{{ route('profile.edit') }}" class="btn btn-alt-secondary">
                                <i class="fa fa-arrow-left me-1"></i> กลับ
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection