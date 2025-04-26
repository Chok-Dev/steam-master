<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>กำลังนำคุณไปยังหน้าชำระเงิน ChillPay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@100;200;300;400;500;600;700&display=swap');

        body {
            font-family: 'IBM Plex Sans Thai', sans-serif;
        }
    </style>
</head>

<body>

    @php
        $amountBaht = $amount / 100;
        $fee = max($amountBaht * 0.029, 15); // ค่าธรรมเนียม 2.9% หรือขั้นต่ำ 15 บาท
        $totalAmount = $amountBaht + $fee;
    @endphp

    <div class="d-flex justify-content-center mt-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-bold">รายละเอียดการชำระเงิน</h5>
                <p class="card-text fw-bold fs-6">เลขที่รายการ: {{ $orderNo }}</p>
                <p class="card-text fw-bold fs-6">จำนวนเงิน: ฿{{ number_format($amountBaht, 2) }}</p>
                <p class="card-text fw-bold fs-6 text-danger">ค่าธรรมเนียม: ฿{{ number_format($fee, 2) }}</p>
                <hr>
                <p class="card-text fw-bold fs-5">ยอดที่ต้องชำระทั้งหมด: ฿{{ number_format($totalAmount, 2) }}</p>
            </div>
        </div>
    </div>



    <form id="payment-form" action="https://cdn.chillpay.co/Payment/" method="post" role="form"
        class="form-horizontal">
        <modernpay:widget id="modernpay-widget-container" data-merchantid="{{ $merchantCode }}"
            data-amount="{{ $amount }}" data-orderno="{{ $orderNo }}" data-customerid="{{ $customerId }}"
            data-clientip="{{ $ipAddress }}" data-routeno="{{ $routeNo }}" data-currency="{{ $currency }}"
            data-apikey="{{ $apiKey }}" data-checksum="{{ $checksum }}"
            data-description="{{ $description }}" data-lang="{{ $langCode }}">
        </modernpay:widget>
        <button type="submit" id="btnSubmit" value="Submit" class="btn">Payment</button>
    </form>

    <script async src="https://cdn.chillpay.co/js/widgets.js?v=1.00" charset="utf-8"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>
