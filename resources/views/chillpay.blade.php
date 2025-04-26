<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>กำลังนำคุณไปยังหน้าชำระเงิน ChillPay</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Sarabun', sans-serif;
        }
        .payment-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            max-height: 60px;
        }
        .loading {
            text-align: center;
            margin: 20px 0;
        }
        .spinner-border {
            width: 3rem;
            height: 3rem;
            color: #3f6ad8;
        }
        .redirect-message {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="logo">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo">
        </div>
        
        <div class="alert alert-info">
            <p class="mb-0 text-center">กำลังนำคุณไปยังหน้าชำระเงิน กรุณารอสักครู่...</p>
        </div>
        
        <div class="loading">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        
        <div class="redirect-message">
            <p>หากไม่มีการเปลี่ยนหน้าอัตโนมัติ กรุณาคลิกปุ่มด้านล่าง</p>
            <button id="submit-form" class="btn btn-primary">ไปยังหน้าชำระเงิน</button>
        </div>
        
        <div class="payment-details mt-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">รายละเอียดการชำระเงิน</h5>
                    <p class="card-text">เลขที่รายการ: {{ $orderNo }}</p>
                    <p class="card-text">จำนวนเงิน: ฿{{ number_format($amount/100, 2) }}</p>
                </div>
            </div>
        </div>
        
        <form id="chillpay-form" action="https://api.chillpay.co/redirect/checkout" method="POST" style="display: none;">
            <input type="hidden" name="merchantCode" value="{{ $merchantCode }}">
            <input type="hidden" name="orderNo" value="{{ $orderNo }}">
            <input type="hidden" name="customerId" value="{{ $customerId }}">
            <input type="hidden" name="amount" value="{{ $amount }}">
            <input type="hidden" name="description" value="{{ $description }}">
            <input type="hidden" name="currency" value="{{ $currency }}">
            <input type="hidden" name="langCode" value="{{ $langCode }}">
            <input type="hidden" name="routeNo" value="{{ $routeNo }}">
            <input type="hidden" name="ipAddress" value="{{ $ipAddress }}">
            <input type="hidden" name="apiKey" value="{{ $apiKey }}">
            <input type="hidden" name="checksum" value="{{ $checksum }}">
        </form>
    </div>
    
    <script>
        // ส่งฟอร์มอัตโนมัติเมื่อหน้าเว็บโหลดเสร็จ
        window.onload = function() {
            setTimeout(function() {
                document.getElementById('chillpay-form').submit();
            }, 2000); // รอ 2 วินาทีแล้วส่งฟอร์ม
        };
        
        // สำหรับปุ่มส่งฟอร์มแบบแมนนวล
        document.getElementById('submit-form').addEventListener('click', function() {
            document.getElementById('chillpay-form').submit();
        });
    </script>
</body>
</html>