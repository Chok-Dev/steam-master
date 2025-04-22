@extends('layouts.app')

@section('title', 'รายละเอียดออเดอร์')
@section('subtitle', 'ออเดอร์ #' . $order->order_number)

@section('breadcrumb')
  <li class="breadcrumb-item">
    <a href="{{ route('home') }}">หน้าหลัก</a>
  </li>
  <li class="breadcrumb-item">
    <a href="{{ route('orders.index') }}">รายการสั่งซื้อของฉัน</a>
  </li>
  <li class="breadcrumb-item active" aria-current="page">ออเดอร์ #{{ $order->order_number }}</li>
@endsection

@section('content')
  <div class="row">
    <div class="col-md-8">
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">รายละเอียดออเดอร์</h3>
          <div class="block-options">
            <a href="{{ route('orders.index') }}" class="btn btn-sm btn-alt-secondary">
              <i class="fa fa-arrow-left me-1"></i> กลับ
            </a>
          </div>
        </div>
        <div class="block-content">
          <div class="row mb-4">
            <div class="col-sm-6">
              <div class="fs-sm">
                <div class="fw-semibold">เลขออเดอร์</div>
                <div class="text-muted">#{{ $order->order_number }}</div>
              </div>
              <div class="fs-sm">
                <div class="fw-semibold">วันที่สั่งซื้อ</div>
                <div class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</div>
              </div>
              <div class="fs-sm">
                <div class="fw-semibold">สถานะออเดอร์</div>
                <div>
                  @if ($order->status === 'pending')
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
                </div>
              </div>
              @if ($order->notes)
                <div class="fs-sm">
                  <div class="fw-semibold">หมายเหตุ</div>
                  <div class="text-muted">{{ $order->notes }}</div>
                </div>
              @endif
            </div>
            <div class="col-sm-6">
              <h4>วิธีการชำระเงิน</h4>
              @php
                $payment = null;
                if (method_exists($order, 'transactions')) {
                    $payment = $order->transactions()->where('type', 'payment')->where('status', 'successful')->first();
                }
              @endphp
              @if ($payment)
                <div class="fs-sm">
                  <div class="fw-semibold">วิธีชำระเงิน</div>
                  <div class="text-muted">
                    @if ($payment->payment_details && isset($payment->payment_details['method']))
                      @if ($payment->payment_details['method'] === 'credit_card')
                        <i class="fa fa-credit-card me-1"></i> บัตรเครดิต/เดบิต
                      @elseif ($payment->payment_details['method'] === 'qr')
                        <i class="fa fa-qrcode me-1"></i> QR Payment
                      @elseif ($payment->payment_details['method'] === 'bank')
                        <i class="fa fa-university me-1"></i> โอนผ่านธนาคาร
                      @elseif ($payment->payment_details['method'] === 'wallet')
                        <i class="fa fa-wallet me-1"></i> วอลเล็ต
                      @else
                        {{ $payment->payment_details['method'] }}
                      @endif
                    @else
                      ไม่ระบุ
                    @endif
                  </div>
                </div>
                <div class="fs-sm">
                  <div class="fw-semibold">วันที่ชำระเงิน</div>
                  <div class="text-muted">{{ $payment->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="fs-sm">
                  <div class="fw-semibold">รหัสธุรกรรม</div>
                  <div class="text-muted">{{ $payment->transaction_id }}</div>
                </div>
              @else
                <div class="alert alert-warning">
                  <i class="fa fa-exclamation-triangle me-1"></i> ยังไม่มีการชำระเงินสำหรับออเดอร์นี้
                </div>

                @if ($order->status === 'pending')
                  <a href="{{ route('checkout', $order) }}" class="btn btn-alt-primary">
                    <i class="fa fa-credit-card me-1"></i> ชำระเงินตอนนี้
                  </a>
                @endif
              @endif
            </div>
          </div>

          <!-- รายการสินค้า -->
          <h4 class="mb-3">รายการสินค้าในออเดอร์</h4>
          <div class="table-responsive">
            <table class="table table-borderless table-striped table-vcenter">
              <thead>
                <tr>
                  <th>สินค้า</th>
                  <th>ผู้ขาย</th>
                  <th class="text-center">ราคา</th>
                  <th class="text-center">สถานะ</th>
                  <th class="text-center">รหัสเกม</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($order->orderItems as $item)
                  <tr>
                    <td>
                      <div class="fw-semibold">
                        <a href="{{ route('products.show', $item->product) }}">{{ $item->product->name }}</a>
                      </div>
                      <div class="fs-sm text-muted">
                        @if ($item->product->type === 'steam_key')
                          <span class="badge bg-primary">Steam Key</span>
                        @elseif($item->product->type === 'origin_key')
                          <span class="badge bg-info">Origin Key</span>
                        @elseif($item->product->type === 'gog_key')
                          <span class="badge bg-success">GOG Key</span>
                        @elseif($item->product->type === 'uplay_key')
                          <span class="badge bg-warning">Uplay Key</span>
                        @elseif($item->product->type === 'battlenet_key')
                          <span class="badge bg-danger">Battle.net Key</span>
                        @elseif($item->product->type === 'account')
                          <span class="badge bg-dark">Account</span>
                        @else
                          <span class="badge bg-secondary">{{ $item->product->type }}</span>
                        @endif
                        <span class="ms-1">{{ $item->product->category->name }}</span>
                      </div>
                    </td>
                    <td>
                      <a href="{{ route('messages.show', $item->product->user) }}">{{ $item->product->user->name }}</a>
                    </td>
                    <td class="text-center">
                      {{ number_format($item->price, 2) }} ฿
                    </td>
                    <td class="text-center">
                      @if ($item->status === 'pending')
                        <span class="badge bg-warning">รอส่งมอบ</span>
                      @elseif($item->status === 'delivered')
                        <span class="badge bg-success">ส่งมอบแล้ว</span>
                        <div class="fs-xs text-muted">
                          {{ $item->delivered_at->format('d/m/Y H:i') }}</div>
                      @elseif($item->status === 'canceled')
                        <span class="badge bg-danger">ยกเลิก</span>
                      @elseif ($item->status === 'confirmed')
                        <span class="badge bg-primary">ยืนยันแล้ว</span>
                        <div class="fs-xs text-muted">{{ $item->confirmed_at->format('d/m/Y H:i') }}</div>
                      @else
                        <span class="badge bg-secondary">{{ $item->status }}</span>
                      @endif
                    </td>
                    <td class="text-center">
                      @if (($item->status === 'delivered' || $item->status === 'confirmed') && $item->key_data)
                        <button type="button" class="btn btn-sm btn-alt-primary" data-bs-toggle="modal"
                          data-bs-target="#modal-view-key-{{ $item->id }}">
                          <i class="fa fa-key me-1"></i> ดูรหัสเกม
                        </button>
                        @if ($item->status === 'delivered' && !$item->is_confirmed && $order->user_id === auth()->id())
                          <form action="{{ route('payments.release', $item) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success">
                              <i class="fa fa-check me-1"></i> ยืนยันการรับรหัสเกม
                            </button>
                          </form>
                        @endif
                        <!-- Modal แสดงรหัสเกม -->
                        <div class="modal fade" id="modal-view-key-{{ $item->id }}" tabindex="-1" role="dialog"
                          aria-labelledby="modal-view-key-{{ $item->id }}" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title">รหัสเกม {{ $item->product->name }}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                  aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                <div class="form-group">
                                  <label for="key-{{ $item->id }}">รหัสเกมของคุณ</label>
                                  <div class="input-group">
                                    <textarea class="form-control" id="key-{{ $item->id }}" rows="3" readonly>{{ $item->decryptedKey }}</textarea>
                                    <button type="button" class="btn btn-alt-primary"
                                      onclick="copyToClipboard('key-{{ $item->id }}')">
                                      <i class="fa fa-copy"></i>
                                    </button>
                                  </div>
                                  <div class="form-text">
                                    <i class="fa fa-info-circle me-1"></i>
                                    คัดลอกรหัสนี้และนำไปใช้ในการเปิดใช้งานเกมของคุณ
                                  </div>
                                </div>
                                <div class="alert alert-success mt-3 mb-0">
                                  <i class="fa fa-check-circle me-1"></i>
                                  รหัสเกมถูกส่งให้คุณเมื่อ
                                  {{ $item->delivered_at->format('d/m/Y H:i') }}
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-alt-secondary"
                                  data-bs-dismiss="modal">ปิด</button>
                                @if (!$item->product->reviews->where('user_id', auth()->id())->first())
                                  <button type="button" class="btn btn-alt-success" data-bs-toggle="modal"
                                    data-bs-target="#modal-review-{{ $item->id }}" data-bs-dismiss="modal">
                                    <i class="fa fa-star me-1"></i> ให้คะแนนผู้ขาย
                                  </button>
                                @endif
                              </div>
                            </div>
                          </div>
                        </div>

                        <!-- Modal รีวิวผู้ขาย -->
                        @if (!$item->product->reviews->where('user_id', auth()->id())->first())
                          <div class="modal fade" id="modal-review-{{ $item->id }}" tabindex="-1" role="dialog"
                            aria-labelledby="modal-review-{{ $item->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                              <div class="modal-content">
                                <form action="{{ route('reviews.store', $order) }}" method="POST">
                                  @csrf
                                  <input type="hidden" name="seller_id" value="{{ $item->product->user_id }}">
                                  <div class="modal-header">
                                    <h5 class="modal-title">ให้คะแนนผู้ขาย
                                      {{ $item->product->user->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                      aria-label="Close"></button>
                                  </div>
                                  <div class="modal-body">
                                    <div class="mb-4">
                                      <label class="form-label">คะแนน</label>
                                      <div class="rating-input">
                                        <div class="rating-stars">
                                          @for ($i = 5; $i >= 1; $i--)
                                            <div class="form-check form-check-inline">
                                              <input class="form-check-input" type="radio"
                                                id="rating-{{ $item->id }}-{{ $i }}" name="rating"
                                                value="{{ $i }}" {{ $i == 5 ? 'checked' : '' }}>
                                              <label class="form-check-label"
                                                for="rating-{{ $item->id }}-{{ $i }}">
                                                <i class="fa fa-star {{ $i <= 5 ? 'text-warning' : '' }}"></i>
                                              </label>
                                            </div>
                                          @endfor
                                        </div>
                                      </div>
                                    </div>

                                    <div class="mb-4">
                                      <label class="form-label" for="comment-{{ $item->id }}">ความคิดเห็น</label>
                                      <textarea class="form-control" id="comment-{{ $item->id }}" name="comment" rows="4"
                                        placeholder="แสดงความคิดเห็นเกี่ยวกับผู้ขายและบริการที่ได้รับ"></textarea>
                                    </div>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-alt-secondary"
                                      data-bs-dismiss="modal">ยกเลิก</button>
                                    <button type="submit" class="btn btn-alt-success">
                                      <i class="fa fa-check me-1"></i> ส่งคะแนน
                                    </button>
                                  </div>
                                </form>
                              </div>
                            </div>
                          </div>
                        @endif
                      @else
                        <button type="button" class="btn btn-sm btn-alt-secondary" disabled>
                          <i class="fa fa-key me-1"></i> รอรับรหัส
                        </button>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="2" class="fs-sm text-end fw-semibold">รวมทั้งสิ้น:</td>
                  <td class="text-center fs-sm fw-semibold">{{ number_format($order->total_amount, 2) }}
                    ฿</td>
                  <td colspan="2"></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>

      <!-- ส่วนรีวิว (แสดงเฉพาะเมื่อมีรีวิวแล้ว) -->
      @php
        $reviews = $order->reviews()->with('seller')->get();
      @endphp
      @if ($reviews->isNotEmpty())
        <div class="block block-rounded">
          <div class="block-header block-header-default">
            <h3 class="block-title">รีวิวของคุณ</h3>
          </div>
          <div class="block-content">
            @foreach ($reviews as $review)
              <div class="d-flex push">
                <div class="flex-shrink-0 me-3">
                  <img class="img-avatar img-avatar48"
                    src="{{ $review->seller->avatar ? asset('storage/' . $review->seller->avatar) : asset('media/avatars/avatar10.jpg') }}"
                    alt="{{ $review->seller->name }}">
                </div>
                <div class="flex-grow-1">
                  <div class="fs-sm">
                    <a href="{{ route('messages.show', $review->seller) }}">{{ $review->seller->name }}</a>
                  </div>
                  <div class="fs-sm text-muted">
                    @for ($i = 1; $i <= 5; $i++)
                      <i class="fa fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                    @endfor
                    <span class="ms-1">{{ $review->created_at->format('d/m/Y H:i') }}</span>
                  </div>
                  <p class="mt-2 mb-0">{{ $review->comment }}</p>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @endif
    </div>

    <div class="col-md-4">
      <!-- ประวัติสถานะ -->
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">ประวัติสถานะ</h3>
        </div>

        <div class="block-content">
          <ul class="timeline pull-t">
            <li class="timeline-event">
              <div class="timeline-event-time">{{ $order->created_at->format('d/m/Y H:i') }}</div>
              <i class="fa fa-shopping-cart timeline-event-icon bg-primary"></i>
              <div class="timeline-event-content">
                <p class="mb-0">สั่งซื้อสำเร็จ</p>
              </div>
            </li>
            @php
              $payment = $order->transactions()->where('type', 'payment')->where('status', 'successful')->first();
            @endphp
            @if ($payment)
              <li class="timeline-event">
                <div class="timeline-event-time">{{ $payment->created_at->format('d/m/Y H:i') }}</div>
                <i class="fa fa-credit-card timeline-event-icon bg-info"></i>
                <div class="timeline-event-content">
                  <p class="mb-0">ชำระเงินสำเร็จ</p>
                </div>
              </li>
            @endif

            @foreach ($order->orderItems as $item)
              @if ($item->status === 'delivered')
                <li class="timeline-event">
                  <div class="timeline-event-time">{{ $item->delivered_at->format('d/m/Y H:i') }}</div>
                  <i class="fa fa-key timeline-event-icon bg-success"></i>
                  <div class="timeline-event-content">
                    <p class="mb-0">ได้รับรหัสเกม {{ $item->product->name }}</p>
                  </div>
                </li>
              @endif
            @endforeach

            @if ($order->status === 'completed')
              <li class="timeline-event">
                <div class="timeline-event-time">{{ $order->updated_at->format('d/m/Y H:i') }}</div>
                <i class="fa fa-check timeline-event-icon bg-success"></i>
                <div class="timeline-event-content">
                  <p class="mb-0">ออเดอร์เสร็จสมบูรณ์</p>
                </div>
              </li>
            @endif

            @if ($order->status === 'canceled')
              <li class="timeline-event">
                <div class="timeline-event-time">{{ $order->updated_at->format('d/m/Y H:i') }}</div>
                <i class="fa fa-times timeline-event-icon bg-danger"></i>
                <div class="timeline-event-content">
                  <p class="mb-0">ออเดอร์ถูกยกเลิก</p>
                </div>
              </li>
            @endif
          </ul>
        </div>
      </div>

      <!-- ความช่วยเหลือ -->
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">ความช่วยเหลือ</h3>
        </div>
        <div class="block-content">
          <div class="mb-4">
            <p>หากคุณมีปัญหาเกี่ยวกับออเดอร์นี้ สามารถติดต่อผู้ขายหรือทีมงานของเราได้</p>
          </div>

          <div class="d-grid gap-2">
            <a href="{{ route('messages.show', $order->orderItems->first()->product->user) }}"
              class="btn btn-alt-primary">
              <i class="fa fa-envelope me-1"></i> ส่งข้อความถึงผู้ขาย
            </a>

            <button type="button" class="btn btn-alt-info" onclick="window.open('/help/contact', '_blank')">
              <i class="fa fa-headset me-1"></i> ติดต่อฝ่ายสนับสนุน
            </button>
          </div>
        </div>
      </div>

      <!-- ตัวเลือกออเดอร์ -->
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">ตัวเลือก</h3>
        </div>
        <div class="block-content">
          <div class="d-grid gap-2">
            <a href="{{ route('orders.index') }}" class="btn btn-alt-secondary">
              <i class="fa fa-list me-1"></i> ดูรายการสั่งซื้อทั้งหมด
            </a>

            <a href="{{ route('products.index') }}" class="btn btn-alt-primary">
              <i class="fa fa-shopping-cart me-1"></i> ซื้อสินค้าเพิ่มเติม
            </a>

            @if ($order->status === 'pending' && !$payment)
              <a href="{{ route('checkout', $order) }}" class="btn btn-success">
                <i class="fa fa-credit-card me-1"></i> ชำระเงินตอนนี้
              </a>
            @endif

            @if ($order->status === 'pending' && !$payment)
              <form action="{{ route('orders.destroy', $order) }}" method="POST"
                onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะยกเลิกออเดอร์นี้?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                  <i class="fa fa-times-circle me-1"></i> ยกเลิกออเดอร์
                </button>
              </form>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    function copyToClipboard(elementId) {
      var copyText = document.getElementById(elementId);
      copyText.select();
      copyText.setSelectionRange(0, 99999);
      document.execCommand("copy");

      // แสดงแจ้งเตือน
      alert("คัดลอกรหัสเรียบร้อยแล้ว!");
    }

    // สไตล์การให้คะแนนดาว
    document.addEventListener('DOMContentLoaded', function() {
      const ratingInputs = document.querySelectorAll('.rating-stars input');
      ratingInputs.forEach(input => {
        input.addEventListener('change', function() {
          const rating = this.value;
          const starsContainer = this.closest('.rating-stars');
          const stars = starsContainer.querySelectorAll('i.fa-star');

          stars.forEach((star, index) => {
            if (index < rating) {
              star.classList.add('text-warning');
            } else {
              star.classList.remove('text-warning');
            }
          });
        });
      });
    });
  </script>
@endpush
