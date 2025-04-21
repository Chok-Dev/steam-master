@extends('layouts.app')

@section('title', $user->name)
@section('subtitle', 'โปรไฟล์ผู้ใช้')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">หน้าหลัก</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">โปรไฟล์: {{ $user->name }}</li>
@endsection

@section('content')
    <div class="row">
        <!-- ข้อมูลโปรไฟล์ -->
        <div class="col-md-4">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">ข้อมูลโปรไฟล์</h3>
                    @if(Auth::id() === $user->id)
                        <div class="block-options">
                            <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-alt-primary">
                                <i class="fa fa-pencil me-1"></i> แก้ไข
                            </a>
                        </div>
                    @endif
                </div>
                <div class="block-content">
                    <div class="text-center mb-4">
                        <img class="img-avatar img-avatar96"
                            src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('media/avatars/avatar10.jpg') }}"
                            alt="{{ $user->name }}">
                        <div class="mt-2">
                            <h3 class="mb-0">{{ $user->name }}</h3>
                            @if($user->role === 'seller')
                                <span class="badge bg-success">ผู้ขาย</span>
                            @elseif($user->role === 'admin')
                                <span class="badge bg-primary">แอดมิน</span>
                            @else
                                <span class="badge bg-info">ผู้ใช้ทั่วไป</span>
                            @endif
                        </div>
                    </div>

                    <div class="row text-center">
                        @if($user->role === 'seller')
                            <div class="col-4">
                                <div class="fs-sm fw-semibold text-uppercase text-muted">สินค้า</div>
                                <div class="fs-4 fw-bold">{{ $user->products->count() }}</div>
                            </div>
                            <div class="col-4">
                                <div class="fs-sm fw-semibold text-uppercase text-muted">ขายแล้ว</div>
                                <div class="fs-4 fw-bold">{{ $user->products->where('status', 'sold')->count() }}</div>
                            </div>
                            <div class="col-4">
                                <div class="fs-sm fw-semibold text-uppercase text-muted">คะแนน</div>
                                <div class="fs-4 fw-bold">{{ number_format($user->average_rating, 1) }}</div>
                            </div>
                        @else
                            <div class="col-6">
                                <div class="fs-sm fw-semibold text-uppercase text-muted">ออเดอร์</div>
                                <div class="fs-4 fw-bold">{{ $user->orders->count() }}</div>
                            </div>
                            <div class="col-6">
                                <div class="fs-sm fw-semibold text-uppercase text-muted">สมาชิกตั้งแต่</div>
                                <div class="fs-4 fw-bold">{{ $user->created_at->diffForHumans(null, true) }}</div>
                            </div>
                        @endif
                    </div>

                    <div class="mt-4">
                        <h5>เกี่ยวกับ</h5>
                        <p>{{ $user->bio ?? 'ไม่มีข้อมูล' }}</p>
                    </div>

                    @if(Auth::id() !== $user->id)
                        <div class="d-grid gap-2 mt-4">
                            <a href="{{ route('messages.show', $user) }}" class="btn btn-alt-primary">
                                <i class="fa fa-envelope me-1"></i> ส่งข้อความ
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- ข้อมูลเพิ่มเติม -->
        <div class="col-md-8">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <ul class="nav nav-tabs nav-tabs-block" role="tablist">
                        @if($user->role === 'seller')
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="products-tab" data-bs-toggle="tab"
                                    data-bs-target="#products" type="button" role="tab" aria-controls="products"
                                    aria-selected="true">สินค้า</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="reviews-tab" data-bs-toggle="tab"
                                    data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews"
                                    aria-selected="false">รีวิว</button>
                            </li>
                        @else
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="orders-tab" data-bs-toggle="tab"
                                    data-bs-target="#orders" type="button" role="tab" aria-controls="orders"
                                    aria-selected="true">ประวัติการสั่งซื้อ</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="reviews-tab" data-bs-toggle="tab"
                                    data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews"
                                    aria-selected="false">รีวิวที่เขียน</button>
                            </li>
                        @endif
                    </ul>
                </div>
                
                <div class="block-content tab-content">
                    <!-- แท็บสินค้า (สำหรับผู้ขาย) -->
                    @if($user->role === 'seller')
                        <div class="tab-pane fade show active" id="products" role="tabpanel"
                            aria-labelledby="products-tab">
                            <h4 class="mb-3">สินค้าของผู้ขาย</h4>

                            @if($user->products->where('status', 'available')->count() > 0)
                                <div class="row">
                                    @foreach($user->products->where('status', 'available')->take(6) as $product)
                                        <div class="col-md-6 col-xl-4">
                                            <div class="block block-rounded block-link-shadow h-100 mb-2">
                                                <div
                                                    class="block-content block-content-full d-flex align-items-center justify-content-between p-3">
                                                    <div>
                                                        <div class="fs-5 fw-semibold mb-0">{{ Str::limit($product->name, 20) }}</div>
                                                        <div class="fs-sm text-muted">{{ $product->category->name }}</div>
                                                    </div>
                                                    <div class="ms-3 text-nowrap">
                                                        <span class="badge bg-primary">{{ number_format($product->price, 0) }} ฿</span>
                                                    </div>
                                                </div>
                                                <div class="block-content block-content-full block-content-sm bg-body-light">
                                                    <div class="d-flex justify-content-between">
                                                        <div class="fs-sm">
                                                            <i class="fa fa-eye text-muted me-1"></i> {{ $product->views }}
                                                        </div>
                                                        <a href="{{ route('products.show', $product) }}" class="fs-sm text-primary">
                                                            รายละเอียด <i class="fa fa-arrow-right ms-1"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="text-center mt-3">
                                    <a href="{{ route('products.index', ['seller_id' => $user->id]) }}" class="btn btn-sm btn-alt-primary">
                                        <i class="fa fa-list me-1"></i> ดูสินค้าทั้งหมด
                                    </a>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle me-1"></i> ผู้ขายยังไม่มีสินค้าที่พร้อมขาย
                                </div>
                            @endif
                        </div>

                        <!-- แท็บรีวิว (สำหรับผู้ขาย) -->
                        <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                            <h4 class="mb-3">รีวิวจากผู้ซื้อ</h4>

                            @if($user->receivedReviews->count() > 0)
                                @foreach($user->receivedReviews->take(5) as $review)
                                    <div class="mb-4">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <img class="img-avatar img-avatar48" src="{{ $review->user->avatar ? asset('storage/' . $review->user->avatar) : asset('media/avatars/avatar10.jpg') }}" alt="{{ $review->user->name }}">
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
                                @endforeach

                                <div class="text-center mt-3">
                                    <a href="#" class="btn btn-sm btn-alt-primary">
                                        <i class="fa fa-list me-1"></i> ดูรีวิวทั้งหมด
                                    </a>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle me-1"></i> ยังไม่มีรีวิวสำหรับผู้ขายรายนี้
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- แท็บประวัติการสั่งซื้อ (สำหรับผู้ซื้อ) -->
                        <div class="tab-pane fade show active" id="orders" role="tabpanel" aria-labelledby="orders-tab">
                            <h4 class="mb-3">ประวัติการสั่งซื้อ</h4>

                            @if($user->orders->count() > 0 && Auth::id() === $user->id)
                                <div class="table-responsive">
                                    <table class="table table-striped table-vcenter">
                                        <thead>
                                            <tr>
                                                <th>เลขออเดอร์</th>
                                                <th>วันที่สั่งซื้อ</th>
                                                <th>จำนวนเงิน</th>
                                                <th>สถานะ</th>
                                                <th class="text-center">ดูรายละเอียด</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($user->orders->take(5) as $order)
                                                <tr>
                                                    <td>#{{ $order->order_number }}</td>
                                                    <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                                    <td>{{ number_format($order->total_amount, 2) }} ฿</td>
                                                    <td>
                                                        @if($order->status === 'pending')
                                                            <span class="badge bg-warning">รอดำเนินการ</span>
                                                        @elseif($order->status === 'processing')
                                                            <span class="badge bg-info">กำลังดำเนินการ</span>
                                                        @elseif($order->status === 'completed')
                                                            <span class="badge bg-success">สำเร็จ</span>
                                                        @elseif($order->status === 'canceled')
                                                            <span class="badge bg-danger">ยกเลิก</span>
                                                        @else
                                                            <span class="badge bg-secondary">{{ $order->status }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-alt-primary">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="text-center mt-3">
                                    <a href="{{ route('orders.index') }}" class="btn btn-sm btn-alt-primary">
                                        <i class="fa fa-list me-1"></i> ดูออเดอร์ทั้งหมด
                                    </a>
                                </div>
                            @elseif(Auth::id() !== $user->id)
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle me-1"></i> ข้อมูลนี้ไม่สามารถเข้าถึงได้
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle me-1"></i> ยังไม่มีประวัติการสั่งซื้อ
                                </div>
                            @endif
                        </div>

                        <!-- แท็บรีวิวที่เขียน (สำหรับผู้ซื้อ) -->
                        <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                            <h4 class="mb-3">รีวิวที่เขียน</h4>

                            @if($user->reviews->count() > 0)
                                @foreach($user->reviews as $review)
                                    <div class="mb-4">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <img class="img-avatar img-avatar48" src="{{ $review->seller->avatar ? asset('storage/' . $review->seller->avatar) : asset('media/avatars/avatar10.jpg') }}" alt="{{ $review->seller->name }}">
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <span class="fw-semibold">{{ $review->seller->name }}</span>
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
                                @endforeach
                            @else
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle me-1"></i> ยังไม่มีรีวิวที่เขียน
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection