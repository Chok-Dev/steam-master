@extends('layouts.app')

@section('title', 'สนทนากับ ' . $user->name)
@section('subtitle', 'ข้อความระหว่างคุณกับ ' . $user->name)

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">หน้าหลัก</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('messages.index') }}">ข้อความของฉัน</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">สนทนากับ {{ $user->name }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">ข้อมูลผู้ใช้</h3>
                </div>
                <div class="block-content">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 me-3">
                            <img class="img-avatar img-avatar96" src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('media/avatars/avatar10.jpg') }}" alt="{{ $user->name }}">
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="mb-1">{{ $user->name }}</h4>
                            <p class="mb-0 text-muted">
                                @if($user->role === 'admin')
                                    <span class="badge bg-primary">แอดมิน</span>
                                @elseif($user->role === 'seller')
                                    <span class="badge bg-success">ผู้ขาย</span>
                                @else
                                    <span class="badge bg-info">ผู้ใช้ทั่วไป</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="fs-sm">
                        <p class="mb-1">
                            <i class="fa fa-calendar me-1"></i> สมาชิกตั้งแต่ {{ $user->created_at->format('d/m/Y') }}
                        </p>
                        @if($user->role === 'seller')
                            <p class="mb-1">
                                <i class="fa fa-star text-warning me-1"></i> คะแนน: {{ number_format($user->average_rating, 1) }}/5.0
                            </p>
                            <p class="mb-1">
                                <i class="fa fa-shopping-cart me-1"></i> จำนวนสินค้า: {{ $user->products->count() }}
                            </p>
                        @endif
                    </div>
                    
                    <div class="mt-3">
                        <a href="{{ route('profile.show', ['username' => $user->name]) }}" class="btn btn-sm btn-alt-primary w-100">
                            <i class="fa fa-user me-1"></i> ดูโปรไฟล์
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">ข้อความ</h3>
                </div>
                <div class="block-content block-content-full">
                    <div class="js-chat-messages p-3" data-chat-height="350" style="height: 350px; overflow-y: auto;">
                        <div class="chat-messages-content">
                            @forelse($messages as $message)
                                <div class="chat-message {{ $message->sender_id === auth()->id() ? 'chat-message-right' : 'chat-message-left' }} mb-3">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-2 {{ $message->sender_id === auth()->id() ? 'order-last ms-2' : '' }}">
                                            <img class="img-avatar img-avatar32" src="{{ $message->sender->avatar ? asset('storage/' . $message->sender->avatar) : asset('media/avatars/avatar10.jpg') }}" alt="{{ $message->sender->name }}">
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="py-2 px-3 {{ $message->sender_id === auth()->id() ? 'bg-body-light rounded-start rounded-bottom' : 'bg-body-light rounded-end rounded-bottom' }}">
                                                {{ $message->message }}
                                            </div>
                                            <div class="fs-sm text-muted mt-1">
                                                <span>{{ $message->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <i class="fa fa-comments fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">ยังไม่มีข้อความในการสนทนานี้ เริ่มส่งข้อความได้เลย</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    
                    <div class="border-top p-3">
                        <form action="{{ route('messages.store', $user) }}" method="POST">
                            @csrf
                            <div class="input-group">
                                <textarea class="form-control" name="message" rows="2" placeholder="พิมพ์ข้อความของคุณที่นี่..." required></textarea>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-scroll to bottom of chat container
        const chatMessages = document.querySelector('.js-chat-messages');
        chatMessages.scrollTop = chatMessages.scrollHeight;
    });
</script>
@endpush