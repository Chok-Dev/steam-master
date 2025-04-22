@extends('layouts.app')

@section('title', 'รายละเอียดออเดอร์')
@section('subtitle', 'ออเดอร์ #' . $order->order_number)

@section('breadcrumb')
  <li class="breadcrumb-item">
    <a href="{{ route('home') }}">หน้าหลัก</a>
  </li>
  <li class="breadcrumb-item">
    <a href="{{ route('admin.dashboard') }}">แดชบอร์ดแอดมิน</a>
  </li>
  <li class="breadcrumb-item">
    <a href="{{ route('admin.orders.index') }}">จัดการออเดอร์</a>
  </li>
  <li class="breadcrumb-item active" aria-current="page">ออเดอร์ #{{ $order->order_number }}</li>
@endsection

@section('content')
  <div class="row">
    <!-- รายละเอียดออเดอร์ -->
    <div class="col-md-8">
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">รายละเอียดออเดอร์</h3>
          <div class="block-options">
            <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-sm btn-alt-primary">
              <i class="fa fa-pencil me-1"></i> แก้ไข
            </a>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-alt-secondary">
              <i class="fa fa-arrow-left me-1"></i> กลับ
            </a>
          </div>
        </div>
        <div class="block-content">
          <div class="row mb-4">
            <div class="col-sm-6">
              <h4>ข้อมูลออเดอร์</h4>
              <div class="fs-sm">
                <div class="fw-semibold">เลขออเดอร์</div>
                <div class="text-muted">#{{ $order->order_number }}</div>
              </div>
              <div class="fs-sm">
                <div class="fw-semibold">วันที่สั่งซื้อ</div>
                <div class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</div>
              </div>
              <div class="fs-sm">
                <div class="fw-semibold">สถานะ</div>
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
              <h4>ข้อมูลผู้ซื้อ</h4>
              <div class="d-flex align-items-center mb-2">
                <div class="flex-shrink-0 me-2">
                  <img class="img-avatar img-avatar48"
                    src="{{ $order->user->avatar ? asset('storage/' . $order->user->avatar) : asset('media/avatars/avatar15.jpg') }}"
                    alt="{{ $order->user->name }}">
                </div>
                <div class="flex-grow-1">
                  <div class="fw-semibold"><a
                      href="{{ route('admin.users.show', $order->user) }}">{{ $order->user->name }}</a>
                  </div>
                  <div class="fs-sm text-muted">{{ $order->user->email }}</div>
                </div>
              </div>
              <div class="fs-sm">
                <div class="fw-semibold">วันที่สมัคร</div>
                <div class="text-muted">{{ $order->user->created_at->format('d/m/Y') }}</div>
              </div>
              <div class="fs-sm">
                <div class="fw-semibold">ออเดอร์ทั้งหมด</div>
                <div class="text-muted">{{ $order->user->orders->count() }}</div>
              </div>
              <div class="mt-3">
                <a href="{{ route('admin.users.show', $order->user) }}" class="btn btn-sm btn-alt-secondary">
                  <i class="fa fa-user me-1"></i> ดูโปรไฟล์ผู้ซื้อ
                </a>
                <a href="#" class="btn btn-sm btn-alt-primary"
                  onclick="event.preventDefault(); document.getElementById('send-message-form').submit();">
                  <i class="fa fa-envelope me-1"></i> ส่งข้อความ
                </a>
                <form id="send-message-form" action="{{ route('messages.show', $order->user) }}" method="GET"
                  style="display: none;"></form>
              </div>
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
                  <th class="text-center">จัดการ</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($order->orderItems as $item)
                  <tr>
                    <td>
                      <div class="fw-semibold">
                        <a href="{{ route('admin.products.show', $item->product) }}">{{ $item->product->name }}</a>
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
                      <a
                        href="{{ route('admin.users.show', $item->product->user) }}">{{ $item->product->user->name }}</a>
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
                      <div class="btn-group">
                        @if ($item->status === 'delivered' && $item->key_data)
                          <button type="button" class="btn btn-sm btn-alt-primary" data-bs-toggle="modal"
                            data-bs-target="#modal-view-key-{{ $item->id }}">
                            <i class="fa fa-key"></i>
                          </button>

                          <!-- Modal แสดง Key -->
                          <div class="modal fade" id="modal-view-key-{{ $item->id }}" tabindex="-1"
                            role="dialog" aria-labelledby="modal-view-key-{{ $item->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title">รหัสเกม
                                    {{ $item->product->name }}</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                  <div class="form-group">
                                    <label for="key-{{ $item->id }}">รหัสเกม</label>
                                    <div class="input-group">
                                      <input type="text" class="form-control" id="key-{{ $item->id }}"
                                        value="{{ $item->decryptedKey }}" readonly>
                                      <button type="button" class="btn btn-alt-primary"
                                        onclick="copyKey('key-{{ $item->id }}')">
                                        <i class="fa fa-copy"></i>
                                      </button>
                                    </div>
                                  </div>
                                  <div class="form-group mt-3">
                                    <div class="alert alert-info mb-0">
                                      <i class="fa fa-info-circle me-1"></i>
                                      รหัสนี้ถูกส่งมอบให้ผู้ซื้อเมื่อ
                                      {{ $item->delivered_at->format('d/m/Y H:i') }}
                                      โดย {{ $item->product->user->name }}
                                    </div>
                                  </div>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-alt-secondary"
                                    data-bs-dismiss="modal">ปิด</button>
                                </div>
                              </div>
                            </div>
                          </div>
                        @else
                          <button type="button" class="btn btn-sm btn-alt-secondary" disabled>
                            <i class="fa fa-key"></i>
                          </button>
                        @endif

                        <a href="{{ route('admin.products.show', $item->product) }}"
                          class="btn btn-sm btn-alt-secondary">
                          <i class="fa fa-eye"></i>
                        </a>
                      </div>
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

          <!-- รายการธุรกรรม -->
          <h4 class="mt-4 mb-3">ประวัติธุรกรรม</h4>
          <div class="table-responsive">
            <table class="table table-sm table-borderless table-striped table-vcenter">
              <thead>
                <tr>
                  <th>รหัสธุรกรรม</th>
                  <th>ผู้ทำรายการ</th>
                  <th>ประเภท</th>
                  <th>จำนวนเงิน</th>
                  <th>สถานะ</th>
                  <th>วันที่ทำรายการ</th>
                </tr>
              </thead>
              <tbody>
                @forelse($order->transactions as $transaction)
                  <tr>
                    <td>{{ $transaction->transaction_id }}</td>
                    <td>
                      <a href="{{ route('admin.users.show', $transaction->user) }}">{{ $transaction->user->name }}</a>
                    </td>
                    <td>
                      @if ($transaction->type === 'payment')
                        <span class="badge bg-primary">ชำระเงิน</span>
                      @elseif($transaction->type === 'payout')
                        <span class="badge bg-success">จ่ายให้ผู้ขาย</span>
                      @elseif($transaction->type === 'refund')
                        <span class="badge bg-danger">คืนเงิน</span>
                      @else
                        <span class="badge bg-secondary">{{ $transaction->type }}</span>
                      @endif
                    </td>
                    <td>{{ number_format($transaction->amount, 2) }} ฿</td>
                    <td>
                      @if ($transaction->status === 'successful')
                        <span class="badge bg-success">สำเร็จ</span>
                      @elseif($transaction->status === 'pending')
                        <span class="badge bg-warning">รอดำเนินการ</span>
                      @elseif($transaction->status === 'failed')
                        <span class="badge bg-danger">ล้มเหลว</span>
                      @else
                        <span class="badge bg-secondary">{{ $transaction->status }}</span>
                      @endif
                    </td>
                    <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center">ไม่พบข้อมูลธุรกรรม</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- การจัดการออเดอร์ -->
    <div class="col-md-4">
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">การจัดการออเดอร์</h3>
        </div>
        <div class="block-content">
          <form action="{{ route('admin.orders.update', $order) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
              <label class="form-label" for="status">เปลี่ยนสถานะออเดอร์</label>
              <select class="form-select" id="status" name="status">
                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>รอดำเนินการ
                </option>
                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>
                  กำลังดำเนินการ</option>
                <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>สำเร็จ
                </option>
                <option value="canceled" {{ $order->status === 'canceled' ? 'selected' : '' }}>ยกเลิก
                </option>
              </select>
            </div>

            <div class="mb-4">
              <label class="form-label" for="notes">หมายเหตุ</label>
              <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $order->notes) }}</textarea>
              <div class="form-text">ใส่หมายเหตุเกี่ยวกับออเดอร์นี้ (ถ้ามี)</div>
            </div>

            <div class="mb-4">
              <button type="submit" class="btn btn-alt-primary">
                <i class="fa fa-save me-1"></i> บันทึกการเปลี่ยนแปลง
              </button>
            </div>
          </form>

          <hr>

          <!-- คำเตือนเมื่อยกเลิกออเดอร์ -->
          <div class="alert alert-warning">
            <h5><i class="fa fa-exclamation-triangle me-1"></i> ข้อควรระวัง!</h5>
            <p>การยกเลิกออเดอร์จะทำให้สินค้าถูกคืนกลับไปเป็นสถานะพร้อมขาย
              และคืนเงินเข้าสู่บัญชีของผู้ซื้อโดยอัตโนมัติ</p>
            <p class="mb-0">กรุณาตรวจสอบให้แน่ใจว่าออเดอร์นี้ยังไม่มีการส่งมอบสินค้าแล้ว</p>
          </div>

          <form action="{{ route('admin.orders.update', $order) }}" method="POST"
            onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะยกเลิกออเดอร์นี้? การกระทำนี้ไม่สามารถย้อนกลับได้');">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="canceled">
            <input type="hidden" name="notes" value="{{ $order->notes }} [ยกเลิกโดยแอดมิน]">

            <div class="d-grid">
              <button type="submit" class="btn btn-danger">
                <i class="fa fa-times-circle me-1"></i> ยกเลิกออเดอร์
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- ข้อมูลเพิ่มเติม -->
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">ข้อมูลเพิ่มเติม</h3>
        </div>
        <div class="block-content">
          <!-- ประวัติการเปลี่ยนแปลงสถานะ -->

          <h5>ประวัติสถานะ</h5>
          <ul class="timeline pull-t">
            <li class="timeline-event">
              <div class="timeline-event-time">{{ $order->created_at->format('d/m/Y H:i') }}</div>
              <i class="fa fa-shopping-cart timeline-event-icon bg-info"></i>
              <div class="timeline-event-content">
                <p class="mb-0">สร้างออเดอร์</p>
              </div>
            </li>
            @if (
                $order->status !== 'pending' &&
                    $order->transactions->where('type', 'payment')->where('status', 'successful')->first())
              <li class="timeline-event">
                <div class="timeline-event-icon bg-primary">
                  <i class="fa fa-money-bill"></i>
                </div>
                <div class="timeline-event-time">
                  {{ $order->transactions->where('type', 'payment')->where('status', 'successful')->first()->created_at->format('d/m/Y H:i') }}
                </div>
                <div class="timeline-event-content">
                  <p class="mb-0">ชำระเงินแล้ว</p>
                </div>
              </li>
            @endif
            @if ($order->status === 'processing')
              <li class="timeline-event">
                <div class="timeline-event-icon bg-info">
                  <i class="fa fa-spinner"></i>
                </div>

                <div class="timeline-event-time">{{ $order->updated_at->format('d/m/Y H:i') }}
                </div>
                <div class="timeline-event-content">
                  <p class="mb-0">กำลังดำเนินการ</p>
                </div>

              </li>
            @endif
            @if ($order->status === 'completed')
              <li class="timeline-event">
                <div class="timeline-event-icon bg-success">
                  <i class="fa fa-check"></i>
                </div>

                <div class="timeline-event-time">{{ $order->updated_at->format('d/m/Y H:i') }}
                </div>
                <div class="timeline-event-content">
                  <p class="mb-0">สำเร็จ</p>
                </div>

              </li>
            @endif
            @if ($order->status === 'canceled')
              <li class="timeline-event">
                <div class="timeline-event-icon bg-danger">
                  <i class="fa fa-times"></i>
                </div>

                <div class="timeline-event-time">{{ $order->updated_at->format('d/m/Y H:i') }}
                </div>
                <div class="timeline-event-content">
                  <p class="mb-0">ยกเลิก</p>
                </div>

              </li>
            @endif
          </ul>

        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    function copyKey(elementId) {
      var copyText = document.getElementById(elementId);
      copyText.select();
      copyText.setSelectionRange(0, 99999);
      document.execCommand("copy");

      // แสดงแจ้งเตือน
      alert("คัดลอกรหัสเรียบร้อยแล้ว: " + copyText.value);
    }
  </script>
@endpush
