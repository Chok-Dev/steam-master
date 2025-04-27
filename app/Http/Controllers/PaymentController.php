<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    // แสดงหน้าชำระเงิน
    public function topupHistory()
    {
        $user = auth()->user();

        // Get all topup and payment transactions for the current user
        $transactions = Transaction::where('user_id', $user->id)
            ->whereIn('type', ['topup', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('truemoney.history', compact('transactions'));
    }
    public function checkout(Request $request)
    {
        // ตรวจสอบว่ามี product_id ที่ต้องการซื้อใน session หรือไม่
        $productId = session('product_to_buy');
        if (!$productId) {
            return redirect()->route('products.index')->with('error', 'ไม่พบสินค้าที่ต้องการชำระเงิน');
        }

        // ดึงข้อมูลสินค้า
        $product = Product::findOrFail($productId);

        // ตรวจสอบสถานะสินค้าอีกครั้ง (เผื่อมีคนซื้อไปแล้ว)
        if ($product->status !== 'available') {
            return redirect()->route('products.show', $product)->with('error', 'สินค้านี้ไม่พร้อมขายในขณะนี้');
        }

        // สร้างข้อมูลสำหรับแสดงในหน้าชำระเงิน
        $checkoutData = [
            'product' => $product,
            'total' => $product->price
        ];

        return view('payments.checkout', compact('checkoutData'));
    }

    // ประมวลผลการชำระเงิน - เฉพาะวอลเล็ต
    public function process(Request $request)
    {
        // ตรวจสอบว่ามี product_id ที่ต้องการซื้อใน session หรือไม่
        $productId = session('product_to_buy');
        if (!$productId) {
            return redirect()->route('products.index')->with('error', 'ไม่พบสินค้าที่ต้องการชำระเงิน');
        }

        // ดึงข้อมูลสินค้า
        $product = Product::findOrFail($productId);

        // ตรวจสอบสถานะสินค้าอีกครั้ง (เผื่อมีคนซื้อไปแล้ว)
        if ($product->status !== 'available') {
            return redirect()->route('products.show', $product)->with('error', 'สินค้านี้ไม่พร้อมขายในขณะนี้');
        }

        // ตรวจสอบยอดเงินในวอลเล็ต
        $user = auth()->user();
        if ($user->balance < $product->price) {
            return redirect()->route('topup')->with('error', 'ยอดเงินในวอลเล็ตไม่เพียงพอ กรุณาเติมเงินก่อนทำการชำระเงิน');
        }

        // เริ่ม transaction เพื่อความปลอดภัยของข้อมูล
        DB::beginTransaction();

        try {
            // 1. หักเงินจากวอลเล็ตของผู้ซื้อ
            $user->balance -= $product->price;
            $user->save();

            // 2. สร้างออเดอร์ใหม่
            $order = new Order();
            $order->user_id = $user->id;
            $order->order_number = 'ORD-' . Str::random(10);
            $order->total_amount = $product->price;
            $order->status = 'processing'; // เปลี่ยนเป็น processing เลยเพราะชำระเงินแล้ว
            $order->save();

            // 3. สร้าง order item
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $product->id;
            $orderItem->price = $product->price;
            $orderItem->status = 'pending';
            $orderItem->save();

            // 4. อัพเดทสถานะสินค้า
            $product->status = 'pending';
            $product->save();

            // 5. บันทึกธุรกรรมการชำระเงิน
            $transaction = new Transaction();
            $transaction->order_id = $order->id;
            $transaction->user_id = $user->id;
            $transaction->transaction_id = 'TR' . time();
            $transaction->amount = $order->total_amount;
            $transaction->type = 'payment';
            $transaction->status = 'successful';
            $transaction->payment_details = [
                'method' => 'wallet',
                'time' => now()->toDateTimeString(),
            ];
            $transaction->save();

            // 6. ถ้าสินค้ามีรหัสเกมเตรียมไว้แล้ว ให้ส่งมอบทันที
            if ($product->key_data) {
                $orderItem->key_data = $product->key_data;
                $orderItem->status = 'confirmed';
                $orderItem->delivered_at = now();
                $orderItem->confirmed_at = now();
                $orderItem->save();
                $order->status = 'completed'; // เปลี่ยนเป็น processing เลยเพราะชำระเงินแล้ว
                $order->save();
                // เปลี่ยนสถานะสินค้าเป็นขายแล้ว
                $product->status = 'sold';
                $product->save();

                // จ่ายเงินให้ผู้ขาย
                $this->payToSeller($orderItem);
            }

            // 7. ลบข้อมูลใน session
            session()->forget('product_to_buy');

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'ชำระเงินสำเร็จ! เงินถูกหักจากวอลเล็ตของคุณเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('products.show', $product)
                ->with('error', 'เกิดข้อผิดพลาดในการชำระเงิน: ' . $e->getMessage());
        }
    }

    private function payToSeller(OrderItem $item)
    {
        // สร้าง transaction สำหรับการจ่ายเงินให้ผู้ขาย
        $transaction = new Transaction();
        $transaction->order_id = $item->order_id;
        $transaction->user_id = $item->product->user_id; // ผู้ขาย
        $transaction->transaction_id = 'PO' . time();
        $transaction->amount = $item->price * 0.95; // หักค่าคอมมิชชั่น 5%
        $transaction->type = 'payout';
        $transaction->status = 'successful';
        $transaction->notes = 'จ่ายเงินให้ผู้ขายอัตโนมัติ (สินค้ามีรหัสพร้อมส่ง)';
        $transaction->save();

        // เพิ่มเงินเข้าบัญชีผู้ขาย
        $seller = $item->product->user;
        $seller->increment('balance', $transaction->amount);
    }

    public function escrowRelease(OrderItem $orderItem)
    {
        // ตรวจสอบว่าผู้ใช้ปัจจุบันเป็นผู้ซื้อของ order นี้
        if ($orderItem->order->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'คุณไม่มีสิทธิ์ยืนยันรายการนี้');
        }

        // ตรวจสอบว่า order item อยู่ในสถานะที่ถูกส่งมอบแล้ว
        if ($orderItem->status !== 'delivered') {
            return redirect()->back()->with('error', 'รายการนี้ยังไม่พร้อมสำหรับการยืนยัน');
        }

        // ตรวจสอบว่ายังไม่ได้ยืนยันรับสินค้า
        if ($orderItem->is_confirmed) {
            return redirect()->back()->with('error', 'รายการนี้ได้รับการยืนยันแล้ว');
        }

        // อัพเดทสถานะ order item เป็น confirmed
        $orderItem->update([
            'status' => 'confirmed',
            'is_confirmed' => true,
            'confirmed_at' => now()
        ]);

        // ตรวจสอบว่าทุก item ใน order ถูกยืนยันแล้วหรือไม่
        $pendingItems = $orderItem->order->orderItems()->where(function ($query) {
            $query->where('status', 'pending')
                ->orWhere('status', 'delivered');
        })->count();

        if ($pendingItems === 0) {
            $orderItem->order->update(['status' => 'completed']);
        }

        // สร้าง transaction สำหรับการจ่ายเงินให้ผู้ขาย
        $transaction = new Transaction();
        $transaction->order_id = $orderItem->order_id;
        $transaction->user_id = $orderItem->product->user_id; // ผู้ขาย
        $transaction->transaction_id = 'PO' . time();
        $transaction->amount = $orderItem->price * 0.95; // หักค่าคอมมิชชั่น 5%
        $transaction->type = 'payout';
        $transaction->status = 'successful';
        $transaction->notes = 'จ่ายเงินให้ผู้ขายจากการยืนยันรับสินค้าของผู้ซื้อ';
        $transaction->save();

        // เพิ่มเงินเข้าบัญชีผู้ขาย
        $seller = $orderItem->product->user;
        $seller->increment('balance', $transaction->amount);

        return redirect()->back()->with('success', 'ยืนยันการรับรหัสเกมเรียบร้อยแล้ว เงินได้ถูกโอนไปยังผู้ขายแล้ว');
    }

    public function toupIndex()
    {
        return view('truemoney.index');
    }
    public function processTruemoneyTopup(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'voucher' => 'required|url'
        ], [
            'voucher.required' => '* กรุณาใส่ลิ้งค์ซองอั่งเปา',
            'voucher.url' => '* รูปแบบซองอั่งเปาไม่ถูกต้อง',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // ดึงรหัสซองอั่งเปาจาก URL
        preg_match("/([^?&=#]+)=([^&#]*)/", str_replace(' ', '', $request->voucher), $code);
        if (!isset($code[2])) {
            return back()->with('error', 'รูปแบบซองอั่งเปาไม่ถูกต้อง');
        }

        $code = $code[2];
        $phoneNumber = env('TRUEMONEY_PHONE', '0934278023'); // เบอร์โทรที่รับซองอั่งเปา

        // ข้อมูลสำหรับส่งไปยัง TrueMoney API
        $header = [
            "content-type:application/json"
        ];
        $data = '{"mobile":"' . $phoneNumber . '","voucher_hash":"' . $code . '"}';

        // เริ่ม transaction เพื่อความปลอดภัยของข้อมูล
        DB::beginTransaction();

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://gift.truemoney.com/campaign/vouchers/' . $code . '/redeem');
            curl_setopt($ch, CURLOPT_USERAGENT, 'okhttp/3.8.0');
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_COOKIESESSION, true);
            curl_setopt($ch, CURLOPT_COOKIEJAR, 'COOKIE.TXT');
            curl_setopt($ch, CURLOPT_COOKIEFILE, 'COOKIE.TXT');

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode != 200) {
                throw new \Exception('TrueMoney API response error: HTTP ' . $httpCode);
            }

            $result = json_decode($response);

            if (!$result || !isset($result->status) || !isset($result->status->code)) {
                throw new \Exception('Invalid response from TrueMoney API');
            }

            switch ($result->status->code) {
                case "SUCCESS":
                    // เติมเงินสำเร็จ
                    $amount = $result->data->voucher->amount_baht;
                    $user = auth()->user();

                    // เพิ่มเงินในวอลเล็ต
                    $user->increment('balance', $amount);

                    // บันทึกธุรกรรม
                    $order = new Order();
                    $order->user_id = $user->id;
                    $order->order_number = 'ORD-' . Str::random(10);
                    $order->total_amount = $amount;
                    $order->status = 'completed'; // เปลี่ยนเป็น processing เลยเพราะชำระเงินแล้ว
                    $order->save();

                    $transaction = new Transaction();
                    $transaction->order_id = $order->id;
                    $transaction->user_id = $user->id;
                    $transaction->transaction_id = 'TM' . time();
                    $transaction->amount = $amount;
                    $transaction->type = 'topup';
                    $transaction->status = 'successful';
                    $transaction->payment_details = [
                        'method' => 'truemoney_voucher',
                        'voucher_code' => $code,
                        'time' => now()->toDateTimeString(),
                    ];
                    $transaction->save();

                    DB::commit();
                    return redirect()->route('topup')->with('success', 'เติมเงินสำเร็จ! คุณได้รับเงิน ' . number_format($amount, 2) . ' บาท');

                case "CANNOT_GET_OWN_VOUCHER":
                    return back()->with('error', 'ไม่สามารถรับอั่งเปาตัวเองได้');

                case "TARGET_USER_NOT_FOUND":
                    return back()->with('error', 'เบอร์ผู้รับไม่ถูกต้อง');

                case "INTERNAL_ERROR":
                    return back()->with('error', 'URL ไม่ถูกต้อง');

                case "VOUCHER_OUT_OF_STOCK":
                    return back()->with('error', 'อั่งเปาถูกใช้งานไปแล้ว');

                case "VOUCHER_NOT_FOUND":
                    return back()->with('error', 'ไม่พบข้อมูลอั่งเปานี้');

                case "VOUCHER_EXPIRED":
                    return back()->with('error', 'อั่งเปาหมดอายุการใช้งานแล้ว');

                default:
                    return back()->with('error', 'เกิดข้อผิดพลาด: ' . ($result->status->message ?? 'ไม่ทราบสาเหตุ'));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาดในการเติมเงิน: ' . $e->getMessage());
        }
    }

    public function toupTruemoney()
    {
        return view('truemoney.topup');
    }
    public function toupChillpay()
    {
        return view('truemoney.topupchillpay');
    }


    public function processChillpay(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:20'
        ], [
            'amount.required' => '* กรุณาใส่จำนวนเงิน',
            'amount.min' => '* จำนวนเงินขั้นต่ำ 20 บาท',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // ข้อมูลสำหรับการทำธุรกรรม ChillPay
        $merchantCode = env('CHILLPAY_MERCHANT_CODE', '');
        $orderNo = 'TP' . time() . auth()->id();
        $customerId = auth()->id();
        $amount = ($request->amount * 100); // แปลงเป็นสตางค์
        $description = 'เติมเงินเข้ากระเป๋า ' . auth()->user()->name;
        $currency = '764'; // รหัสสกุลเงินบาทไทย
        $langCode = 'TH';
        $routeNo = '1';
        $ipAddress = $request->ip();
        $apiKey = env('CHILLPAY_API_KEY', '');
        $secretKey = env('CHILLPAY_SECRET_KEY', '');

        // สร้าง CheckSum
        $checksumString = 
            $merchantCode . $orderNo . $customerId . $amount .
            $description . $currency . $langCode . $routeNo .
            $ipAddress . $apiKey .
            $secretKey;

        $checksum = md5($checksumString);
        

        // ส่งข้อมูลไปยัง view
        return view('chillpay', [
            'merchantCode' => $merchantCode,
            'orderNo' => $orderNo,
            'customerId' => $customerId,
            'amount' => $amount,
            'description' => $description,
            'currency' => $currency,
            'langCode' => $langCode,
            'routeNo' => $routeNo,
            'ipAddress' => $ipAddress,
            'apiKey' => $apiKey,
            'checksum' => $checksum
        ]);
    }
    
    function webhookResult(Request $request)
    {
        /* dd($request); */
        try {
            if ($request["respCode"] == 0) {
                return redirect()->route('topup.history');
            } elseif ($request["respCode"] == 2) {
                return redirect()->route('home');
            } elseif ($request["respCode"] == 3) {
                return redirect()->route('home');
            }
        } catch (\Exception $e) {

            /* return redirect()->back(); */
        }
    }
    public function chillpayCallback(Request $request)
    {
        // ตรวจสอบความถูกต้องของข้อมูลที่ได้รับจาก ChillPay
        if ($request["PaymentStatus"] == 0) {
            DB::beginTransaction();
            try {
                
                $amountBaht = $request["Amount"] / 100;
                $fee = max($amountBaht * 0.029, 15); // ค่าธรรมเนียม 2.9% หรือขั้นต่ำ 15 บาท
                $totalAmount = $amountBaht + $fee;

                User::find($request["CustomerId"])->increment('balance', $totalAmount);

                $order = new Order();
                $order->user_id = $request["customerId"];
                $order->order_number = 'ORD-' . Str::random(10);
                $order->total_amount = $totalAmount;
                $order->status = 'completed'; // เปลี่ยนเป็น processing เลยเพราะชำระเงินแล้ว
                $order->save();
                // บันทึกรายการเติมเงินระหว่างดำเนินการ
                $transaction = new Transaction();
                $transaction->order_id = $order->id;
                $transaction->user_id = $request["customerId"];
                $transaction->transaction_id = $request["OrderNo"];
                $transaction->amount = $totalAmount; // เก็บเป็นบาท
                $transaction->type = 'topup';
                $transaction->status = 'successful';
                $transaction->payment_details = [
                    'method' => 'chillpay',
                    'request_time' => now()->toDateTimeString(),
                    'amount_requested' => $totalAmount,
                    'fee' => $fee,
                ];
                $transaction->save();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                /* return redirect()->back(); */
            }
        }
    }

}
