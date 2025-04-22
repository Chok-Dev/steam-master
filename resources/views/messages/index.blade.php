@extends('layouts.app')

@section('title', 'ข้อความของฉัน')
@section('subtitle', 'จัดการการสนทนาและข้อความของคุณ')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">หน้าหลัก</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">ข้อความของฉัน</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">การสนทนาทั้งหมดของฉัน</h3>
                </div>
                <div class="block-content">
                    @if($conversations->isEmpty())
                        <div class="py-4 text-center">
                            <div class="mb-3">
                                <i class="fa fa-envelope fa-4x text-muted"></i>
                            </div>
                            <h3 class="h4 fw-normal mb-3">คุณยังไม่มีข้อความ</h3>
                            <p class="text-muted">
                                เริ่มต้นส่งข้อความถึงผู้ขายเพื่อสอบถามเกี่ยวกับสินค้า
                            </p>
                        </div>
                    @else
                        <div class="list-group">
                            @foreach($conversations as $user)
                                @php
                                    $latestMessage = \App\Models\Message::where(function ($query) use ($user) {
                                        $query->where('sender_id', auth()->id())
                                            ->where('receiver_id', $user->id);
                                    })->orWhere(function ($query) use ($user) {
                                        $query->where('sender_id', $user->id)
                                            ->where('receiver_id', auth()->id());
                                    })
                                    ->orderBy('created_at', 'desc')
                                    ->first();
                                    
                                    $unreadCount = \App\Models\Message::where('sender_id', $user->id)
                                        ->where('receiver_id', auth()->id())
                                        ->where('is_read', false)
                                        ->count();
                                @endphp
                                
                                <a href="{{ route('messages.show', $user) }}" class="list-group-item list-group-item-action d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <img class="img-avatar img-avatar48" src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('media/avatars/avatar10.jpg') }}" alt="{{ $user->name }}">
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0 fw-semibold">{{ $user->name }}</h5>
                                            <small class="text-muted">{{ $latestMessage ? $latestMessage->created_at->diffForHumans() : '' }}</small>
                                        </div>
                                        <p class="mb-0 text-muted">
                                            {{ $latestMessage ? Str::limit($latestMessage->message, 50) : 'เริ่มการสนทนา' }}
                                        </p>
                                    </div>
                                    @if($unreadCount > 0)
                                        <div class="ms-3">
                                            <span class="badge bg-primary rounded-pill">{{ $unreadCount }}</span>
                                        </div>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection