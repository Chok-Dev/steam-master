<!-- resources/views/steamguard/show.blade.php -->
@extends('layouts.app')

@section('title', 'Steam Guard Code')
@section('subtitle', 'รหัส Steam Guard สำหรับบัญชี Steam ของคุณ')

@section('content')
<div class="block block-rounded">
    <div class="block-header block-header-default">
        <h3 class="block-title">รหัส Steam Guard</h3>
        <div class="block-options">
            <a href="{{ route('orders.show', $orderItem->order) }}" class="btn btn-sm btn-alt-secondary">
                <i class="fa fa-arrow-left me-1"></i> กลับ
            </a>
        </div>
    </div>
    <div class="block-content">
        @if ($hasSteamAuth)
            <div class="text-center py-4">
                <h2 class="mb-4">รหัส Steam Guard</h2>
                
                <div class="mb-4">
                    <div class="steamguard-container">
                        <div id="steam-account-name" class="fs-sm text-muted mb-1">กำลังโหลด...</div>
                        <div id="steamguard-code" class="display-4 fw-bold mb-2">------</div>
                        <div class="progress mb-2" style="height: 5px;">
                            <div id="time-progress" class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div id="time-remaining" class="fs-sm text-muted">กำลังโหลด...</div>
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <i class="fa fa-info-circle me-1"></i> รหัสจะเปลี่ยนทุก 30 วินาที โปรดใช้รหัสให้ทันเวลา
                </div>
            </div>
        @else
            <div class="alert alert-warning">
                <i class="fa fa-exclamation-triangle me-1"></i> ไม่พบข้อมูล Steam Guard สำหรับสินค้านี้
            </div>
        @endif
    </div>
</div>
@endsection

@if ($hasSteamAuth)
@push('scripts')
<script>
    let interval;
    
    async function fetchSteamGuardCode() {
        try {
            const response = await fetch('{{ route("steam-guard.code", $orderItem) }}');
            const data = await response.json();
            
            if (data.error) {
                document.getElementById('steamguard-code').textContent = 'ERROR';
                document.getElementById('time-remaining').textContent = data.error;
                return;
            }
            
            // แสดงรหัสและข้อมูล
            document.getElementById('steamguard-code').textContent = data.code;
            document.getElementById('steam-account-name').textContent = data.account_name;
            
            // เริ่มนับถอยหลัง
            let timeLeft = data.time_remaining;
            document.getElementById('time-remaining').textContent = `รหัสจะหมดอายุใน ${timeLeft} วินาที`;
            
            // อัพเดทเวลาถอยหลัง
            clearInterval(interval);
            interval = setInterval(() => {
                timeLeft--;
                const progressPercent = (timeLeft / 30) * 100;
                document.getElementById('time-progress').style.width = `${progressPercent}%`;
                document.getElementById('time-remaining').textContent = `รหัสจะหมดอายุใน ${timeLeft} วินาที`;
                
                if (timeLeft <= 0) {
                    clearInterval(interval);
                    fetchSteamGuardCode();
                }
            }, 1000);
        } catch (error) {
            console.error('Error fetching Steam Guard code:', error);
            document.getElementById('steamguard-code').textContent = 'ERROR';
            document.getElementById('time-remaining').textContent = 'เกิดข้อผิดพลาดในการรับรหัส';
        }
    }
    
    // เริ่มทำงานตั้งแต่โหลดหน้า
    document.addEventListener('DOMContentLoaded', function() {
        fetchSteamGuardCode();
    });
    
    // ทำความสะอาดเมื่อออกจากหน้า
    window.addEventListener('beforeunload', function() {
        clearInterval(interval);
    });
</script>
@endpush
@endif