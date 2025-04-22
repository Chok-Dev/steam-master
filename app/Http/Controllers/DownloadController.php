<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function downloadMafile(OrderItem $orderItem)
    {
        // ตรวจสอบว่า orderItem เป็นของผู้ใช้นี้จริงๆ
        if ($orderItem->order->user_id !== auth()->id()) {
            abort(403);
        }

        // ตรวจสอบว่ามีการส่งมอบแล้ว
        if ($orderItem->status !== 'delivered') {
            return redirect()->back()->with('error', 'คุณยังไม่ได้รับมอบสินค้านี้');
        }

        // ตรวจสอบว่ามีไฟล์ .mafile
        if (!$orderItem->product->mafile_path) {
            return redirect()->back()->with('error', 'ไม่พบไฟล์ Steam Guard สำหรับสินค้านี้');
        }

        // ดาวน์โหลดไฟล์
        $filename = basename($orderItem->product->mafile_path);
        return Storage::disk('private')->download(
            $orderItem->product->mafile_path,
            'steam_guard_' . $orderItem->product->id . '.mafile'
        );
    }
}
