@extends('layouts.app')

@section('title', $product->name)
@section('subtitle', $product->category->name)

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">หน้าหลัก</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('products.index') }}">สินค้าทั้งหมด</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('categories.show', $product->category) }}">{{ $product->category->name }}</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <!-- รายละเอียดสินค้า -->
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">รายละเอียดสินค้า</h3>
                    <div class="block-options">
                        <div class="block-options-item">
                            <span class="badge bg-primary">{{ number_format($product->price, 0) }} ฿</span>
                        </div>
                    </div>
                </div>
                <div class="block-content">
                    <h2 class="content-heading pt-0">{{ $product->name }}</h2>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="fs-sm text-muted mb-2">
                                <i class="fa fa-tags text-muted me-1"></i> หมวดหมู่: {{ $product->category->name }}
                            </div>
                            <div class="fs-sm text-muted mb-2">
                                <i class="fa fa-gamepad text-muted me-1"></i> ประเภท: {{ ucfirst($product->type) }}
                            </div>
                            <div class="fs-sm text-muted mb-2">
                                <i class="fa fa-eye text-muted me-1"></i> จำนวนผู้เข้าชม: {{ $product->views }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="fs-sm text-muted mb-2">
                                <i class="fa fa-store text-muted me-1"></i> ผู้ขาย: 
                                <a href="{{ route('profile.show', ['username' => $product->user->name]) }}">
                                    {{ $product->user->name }}
                                </a>
                            </div>
                            <div class="fs-sm text-muted mb-2">
                                <i class="fa fa-star text-warning me-1"></i> คะแนน: {{ number_format($product->user->average_rating, 1) }}/5.0
                            </div>
                            <div class="fs-sm text-muted mb-2">
                                <i class="fa fa-calendar text-muted me-1"></i> วันที่ลงขาย: {{ $product->created_at->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                    
                    <h2 class="content-heading">คำอธิบาย</h2>
                    <div class="mb-4">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                    
                    @if (!empty($product->attributes) && is_array($product->attributes))
                        <h2 class="content-heading">ข้อมูลเพิ่มเติม</h2>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-striped table-vcenter">
                                <tbody>
                                    @foreach($product->attributes as $key => $value)
                                        <tr>
                                            <td style="width: 30%"><strong>{{ ucfirst($key) }}</strong></td>
                                            <td>{{ $value }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    
                    @auth
                        <div class="mb-4">
                            @if($product->status === 'available')
                                <form action="{{ route('products.buy', $product) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fa fa-shopping-cart me-1"></i> ซื้อเลย
                                    </button>
                                </form>
                            @else
                                <button type="button" class="btn btn-alt-secondary btn-lg" disabled>
                                    <i class="fa fa-times me-1"></i> สินค้าไม่พร้อมขาย
                                </button>
                            @endif
                        </div>
                    @else
                        <div class="mb-4">
                            <a href="{{ route('login') }}" class="btn btn-alt-primary btn-lg">
                                <i class="fa fa-sign-in-alt me-1"></i> เข้าสู่ระบบเพื่อซื้อสินค้า
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- ข้อมูลผู้ขาย -->
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">ข้อมูลผู้ขาย</h3>
                </div>
                <div class="block-content">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <img class="img-avatar img-avatar48" src="{{ $product->user->avatar ? asset('storage/' . $product->user->avatar) : asset('assets/codebase/media/avatars/avatar10.jpg') }}" alt="{{ $product->user->name }}">
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="fw-semibold mb-0">{{ $product->user->name }}</p>
                            <p class="fs-sm text-muted mb-0">
                                <i class="fa fa-star text-warning"></i> {{ number_format($product->user->average_rating, 1) }}/5.0
                                ({{ $product->user->receivedReviews->count() }} รีวิว)
                            </p>
                        </div>
                    </div>
                    
                    <div class="fs-sm mb-3">
                        <p>{{ $product->user->bio ?? 'ไม่มีข้อมูล' }}</p>
                    </div>
                    
                    <div class="row text-center mb-3">
                        <div class="col-4">
                            <div class="fs-sm fw-semibold text-uppercase text-muted">สินค้า</div>
                            <div class="fs-4 fw-bold">{{ $product->user->products->count() }}</div>
                        </div>
                        <div class="col-4">
                            <div class="fs-sm fw-semibold text-uppercase text-muted">ขายแล้ว</div>
                            <div class="fs-4 fw-bold">{{ $product->user->products->where('status', 'sold')->count() }}</div>
                        </div>
                        <div class="col-4">
                            <div class="fs-sm fw-semibold text-uppercase text-muted">เข้าร่วม</div>
                            <div class="fs-4 fw-bold">{{ $product->user->created_at->diffForHumans(null, true) }}</div>
                        </div>
                    </div>
                    
                    @auth
                        @if(auth()->id() !== $product->user_id)
                            <div class="mb-3">
                                <a href="{{ route('messages.show', $product->user) }}" class="btn btn-alt-primary btn-sm w-100">
                                    <i class="fa fa-envelope me-1"></i> ส่งข้อความถึงผู้ขาย
                                </a>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
            
            <!-- สินค้าอื่นของผู้ขาย -->
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">สินค้าอื่นจากผู้ขายรายนี้</h3>
                </div>
                <div class="block-content">
                    <div class="list-group push">
                        @foreach($otherProducts as $otherProduct)
                            <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="{{ route('products.show', $otherProduct) }}">
                                <span>{{ Str::limit($otherProduct->name, 30) }}</span>
                                <span class="badge bg-primary">{{ number_format($otherProduct->price, 0) }} ฿</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- สินค้าที่คล้ายกัน -->
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">สินค้าที่คล้ายกัน</h3>
                </div>
                <div class="block-content">
                    <div class="list-group push">
                        @foreach($similarProducts as $similarProduct)
                            <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="{{ route('products.show', $similarProduct) }}">
                                <span>{{ Str::limit($similarProduct->name, 30) }}</span>
                                <span class="badge bg-primary">{{ number_format($similarProduct->price, 0) }} ฿</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- รีวิวผู้ขาย -->
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">รีวิวผู้ขาย</h3>
        </div>
        <div class="block-content">
            @forelse($reviews as $review)
                <div class="mb-4">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <img class="img-avatar img-avatar32" src="{{ $review->user->avatar ? asset('storage/' . $review->user->avatar) : asset('assets/codebase/media/avatars/avatar10.jpg') }}" alt="{{ $review->user->name }}">
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <span class="fw-semibold">{{ $review->user->name }}</span>
                                    <span class="fs-sm text-muted ms-2">
                                        {{ $review->created_at->format('d/m/Y') }}
                                    </span>
                                </div>
                                <div>
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <i class="fa fa-star text-warning"></i>
                                        @else
                                            <i class="fa fa-star text-muted"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                            <p class="mb-0">{{ $review->comment }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info">
                    ยังไม่มีรีวิวสำหรับผู้ขายรายนี้
                </div>
            @endforelse
            
            <div class="d-flex justify-content-center">
                {{ $reviews->links() }}
            </div>
        </div>
    </div>
@endsection