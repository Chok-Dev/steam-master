<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;

class SteamGuardController extends Controller
{

    public function getCode(OrderItem $orderItem)
    {
        // ตรวจสอบสิทธิ์
        function generateSteamGuardCode($shared_secret)
        {
            $time = floor(time() / 30);

            // สำหรับ 64-bit
            if (PHP_INT_SIZE === 8) {
                $time_buffer = pack('J', $time);
            } else {
                // สำหรับ 32-bit
                $time_buffer = pack('N2', 0, $time);
            }

            $shared_secret = str_pad($shared_secret, strlen($shared_secret) + (strlen($shared_secret) % 4), '=', STR_PAD_RIGHT);

            $time_hmac = hash_hmac('sha1', $time_buffer, base64_decode($shared_secret), true);
            $begin = ord(substr($time_hmac, 19, 1)) & 0x0F;
            $fullcode = unpack('N', substr($time_hmac, $begin, 4))[1] & 0x7FFFFFFF;
            $chars = '23456789BCDFGHJKMNPQRTVWXY';
            $code = '';

            for ($i = 0; $i < 5; $i++) {
                $code .= $chars[$fullcode % strlen($chars)];
                $fullcode = floor($fullcode / strlen($chars));
            }

            return $code;
        }

        function getRemainingTime()
        {
            return 30 - (time() % 30);
        }

        if ($orderItem->order->user_id !== auth()->id()) {
            return response()->json(['error' => 'ไม่มีสิทธิ์เข้าถึง'], 403);
        }

        // ตรวจสอบว่าได้รับมอบแล้ว
        if ($orderItem->status !== 'delivered' && $orderItem->status !== 'confirmed') {
            return response()->json(['error' => 'ยังไม่ได้รับมอบสินค้า'], 400);
        }

        // ตรวจสอบว่ามีข้อมูล Steam Auth
        if (!$orderItem->product->steam_auth_data) {
            return response()->json(['error' => 'ไม่มีข้อมูล Steam Guard สำหรับสินค้านี้'], 404);
        }

        try {
            // ถอดรหัสข้อมูล Steam Auth
            $authData = json_decode(decrypt($orderItem->product->steam_auth_data), true);

            // สร้างรหัส
            $code = generateSteamGuardCode($authData['shared_secret']);
            $timeRemaining = getRemainingTime();

            return response()->json([
                'code' => $code,
                'time_remaining' => $timeRemaining,
                'account_name' => $authData['account_name'] ?? 'Steam Account'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }

    public function showSteamGuard(OrderItem $orderItem)
    {
        // ตรวจสอบสิทธิ์
        if ($orderItem->order->user_id !== auth()->id()) {
            abort(403, 'ไม่มีสิทธิ์เข้าถึง');
        }

        // ตรวจสอบว่าได้รับมอบแล้ว
        if ($orderItem->status !== 'delivered' && $orderItem->status !== 'confirmed') {
            return redirect()->back()->with('error', 'ยังไม่ได้รับมอบสินค้า');
        }

        // ตรวจสอบว่ามีข้อมูล Steam Auth
        $hasSteamAuth = !empty($orderItem->product->steam_auth_data);

        return view('steamguard.show', compact('orderItem', 'hasSteamAuth'));
    }
}
