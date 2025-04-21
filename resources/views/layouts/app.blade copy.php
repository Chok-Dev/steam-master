<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Steam Marketplace') }} - @yield('title', 'หน้าหลัก')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Codebase CSS -->
   
    
    <!-- Page JS Plugins CSS -->
    

    <!-- Font Awesome Icons -->
    

    <!-- Custom CSS -->

    @livewireStyles
    
    <!-- Scripts -->
    @vite(['resources/sass/main.scss', 'resources/js/codebase/app.js'])
</head>
<body>
    <div id="page-container" class="sidebar-o enable-page-overlay side-scroll page-header-fixed main-content-narrow">
        <!-- Side Overlay-->
        <aside id="side-overlay">
            <!-- Side Header -->
            <div class="content-header">
                <!-- User Avatar -->
                <a class="img-link me-2" href="javascript:void(0)">
                    <img class="img-avatar img-avatar32" src="{{ Auth::user() && Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : asset('assets/codebase/media/avatars/avatar15.jpg') }}" alt="">
                </a>
                <!-- END User Avatar -->

                <!-- User Info -->
                <div class="ms-2">
                    <a class="text-dark fw-semibold fs-sm" href="javascript:void(0)">{{ Auth::user()->name ?? 'Guest' }}</a>
                </div>
                <!-- END User Info -->

                <!-- Close Side Overlay -->
                <a class="ms-auto btn btn-sm btn-alt-danger" href="javascript:void(0)" data-toggle="layout" data-action="side_overlay_close">
                    <i class="fa fa-fw fa-times"></i>
                </a>
                <!-- END Close Side Overlay -->
            </div>
            <!-- END Side Header -->

            <!-- Side Content -->
            <div class="content-side">
                <!-- Mini Stats -->
                <div class="block block-transparent">
                    <div class="block-content">
                        <div class="row text-center">
                            @auth
                                <div class="col-4">
                                    <div class="fs-sm fw-semibold text-uppercase text-muted">Balance</div>
                                    <div class="fs-4 fw-bold">{{ number_format(Auth::user()->balance, 2) }} ฿</div>
                                </div>
                                <div class="col-4">
                                    <div class="fs-sm fw-semibold text-uppercase text-muted">Orders</div>
                                    <div class="fs-4 fw-bold">{{ Auth::user()->orders->count() }}</div>
                                </div>
                                <div class="col-4">
                                    <div class="fs-sm fw-semibold text-uppercase text-muted">Messages</div>
                                    <div class="fs-4 fw-bold">{{ Auth::user()->receivedMessages()->where('is_read', false)->count() }}</div>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
                <!-- END Mini Stats -->
                
                <!-- Recent Activity -->
                @auth
                <div class="block block-transparent">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">ข้อความล่าสุด</h3>
                    </div>
                    <div class="block-content">
                        <ul class="list list-activity">
                            @php
                                $recentMessages = Auth::user()->receivedMessages()->with('sender')->latest()->take(5)->get();
                            @endphp
                            
                            @forelse($recentMessages as $message)
                                <li>
                                    <i class="list-activity-icon fa fa-envelope bg-info"></i>
                                    <div class="fs-sm fw-semibold">
                                        จาก <a href="{{ route('messages.show', $message->sender) }}">{{ $message->sender->name }}</a>
                                    </div>
                                    <div class="fs-sm">
                                        {{ Str::limit($message->message, 50) }}
                                    </div>
                                    <div class="fs-xs text-muted">{{ $message->created_at->diffForHumans() }}</div>
                                </li>
                            @empty
                                <li>ไม่มีข้อความใหม่</li>
                            @endforelse
                        </ul>
                        <div class="text-center mt-3">
                            <a href="{{ route('messages.index') }}" class="btn btn-sm btn-alt-primary">
                                <i class="fa fa-envelope me-1"></i> ดูข้อความทั้งหมด
                            </a>
                        </div>
                    </div>
                </div>
                @endauth
                <!-- END Recent Activity -->
            </div>
            <!-- END Side Content -->
        </aside>
        <!-- END Side Overlay -->

        <!-- Sidebar -->
        <nav id="sidebar">
            <!-- Sidebar Content -->
            <div class="sidebar-content">
                <!-- Side Header -->
                <div class="content-header justify-content-lg-center">
                    <!-- Logo -->
                    <div>
                        <span class="smini-visible fw-bold tracking-wide fs-lg">
                            <span class="text-primary">S</span><span class="text-dual">M</span>
                        </span>
                        <a class="h5 fw-bold tracking-wide mx-auto" href="{{ route('home') }}">
                            <span class="smini-hidden fw-bold tracking-wide fs-lg">
                                <span class="text-primary">Steam</span>
                                <span class="text-dual">Market</span>
                            </span>
                        </a>
                    </div>
                    <!-- END Logo -->

                    <!-- Options -->
                    <div>
                        <button type="button" class="btn btn-sm btn-alt-secondary d-lg-none" data-toggle="layout" data-action="sidebar_close">
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    </div>
                    <!-- END Options -->
                </div>
                <!-- END Side Header -->

                <!-- Side Navigation -->
                <div class="content-side content-side-full">
                    <ul class="nav-main">
                        <li class="nav-main-item">
                            <a class="nav-main-link{{ request()->routeIs('home') ? ' active' : '' }}" href="{{ route('home') }}">
                                <i class="nav-main-link-icon fa fa-house-user"></i>
                                <span class="nav-main-link-name">หน้าหลัก</span>
                            </a>
                        </li>
                        <li class="nav-main-item">
                            <a class="nav-main-link{{ request()->routeIs('products.*') ? ' active' : '' }}" href="{{ route('products.index') }}">
                                <i class="nav-main-link-icon fa fa-gamepad"></i>
                                <span class="nav-main-link-name">สินค้าทั้งหมด</span>
                            </a>
                        </li>
                        
                        <li class="nav-main-heading">หมวดหมู่</li>
                        
                        <li class="nav-main-item">
                            <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="false" href="#">
                                <i class="nav-main-link-icon fa fa-tags"></i>
                                <span class="nav-main-link-name">หมวดหมู่</span>
                            </a>
                            <ul class="nav-main-submenu">
                                @php
                                    $categories = \App\Models\Category::all();
                                @endphp
                                
                                @foreach($categories as $category)
                                    <li class="nav-main-item">
                                        <a class="nav-main-link" href="{{ route('categories.show', $category) }}">
                                            <span class="nav-main-link-name">{{ $category->name }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                        
                        @auth
                            <li class="nav-main-heading">บัญชีของฉัน</li>
                            
                            <li class="nav-main-item">
                                <a class="nav-main-link{{ request()->routeIs('profile.*') ? ' active' : '' }}" href="{{ route('profile.show') }}">
                                    <i class="nav-main-link-icon fa fa-user"></i>
                                    <span class="nav-main-link-name">โปรไฟล์</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link{{ request()->routeIs('orders.*') ? ' active' : '' }}" href="{{ route('orders.index') }}">
                                    <i class="nav-main-link-icon fa fa-shopping-cart"></i>
                                    <span class="nav-main-link-name">รายการสั่งซื้อ</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link{{ request()->routeIs('messages.*') ? ' active' : '' }}" href="{{ route('messages.index') }}">
                                    <i class="nav-main-link-icon fa fa-envelope"></i>
                                    <span class="nav-main-link-name">ข้อความ</span>
                                    @php
                                        $unreadCount = Auth::user()->receivedMessages()->where('is_read', false)->count();
                                    @endphp
                                    
                                    @if($unreadCount > 0)
                                        <span class="nav-main-link-badge badge rounded-pill bg-primary">{{ $unreadCount }}</span>
                                    @endif
                                </a>
                            </li>
                            
                            @if(Auth::user()->role === 'seller')
                                <li class="nav-main-heading">สำหรับผู้ขาย</li>
                                
                                <li class="nav-main-item">
                                    <a class="nav-main-link{{ request()->routeIs('seller.dashboard') ? ' active' : '' }}" href="{{ route('seller.dashboard') }}">
                                        <i class="nav-main-link-icon fa fa-tachometer-alt"></i>
                                        <span class="nav-main-link-name">แดชบอร์ด</span>
                                    </a>
                                </li>
                                <li class="nav-main-item">
                                    <a class="nav-main-link{{ request()->routeIs('seller.products.*') ? ' active' : '' }}" href="{{ route('seller.products.index') }}">
                                        <i class="nav-main-link-icon fa fa-gamepad"></i>
                                        <span class="nav-main-link-name">สินค้าของฉัน</span>
                                    </a>
                                </li>
                                <li class="nav-main-item">
                                    <a class="nav-main-link{{ request()->routeIs('seller.orders.*') ? ' active' : '' }}" href="{{ route('seller.orders.index') }}">
                                        <i class="nav-main-link-icon fa fa-shopping-cart"></i>
                                        <span class="nav-main-link-name">รายการสั่งซื้อ</span>
                                    </a>
                                </li>
                                <li class="nav-main-item">
                                    <a class="nav-main-link{{ request()->routeIs('seller.transactions') ? ' active' : '' }}" href="{{ route('seller.transactions') }}">
                                        <i class="nav-main-link-icon fa fa-money-bill"></i>
                                        <span class="nav-main-link-name">การเงิน</span>
                                    </a>
                                </li>
                            @endif
                            
                            @if(Auth::user()->role === 'admin')
                                <li class="nav-main-heading">สำหรับแอดมิน</li>
                                
                                <li class="nav-main-item">
                                    <a class="nav-main-link{{ request()->routeIs('admin.dashboard') ? ' active' : '' }}" href="{{ route('admin.dashboard') }}">
                                        <i class="nav-main-link-icon fa fa-tachometer-alt"></i>
                                        <span class="nav-main-link-name">แดชบอร์ด</span>
                                    </a>
                                </li>
                                
                                <!-- ผู้ใช้ -->
                                <li class="nav-main-item{{ request()->routeIs('admin.users.*') ? ' open' : '' }}">
                                    <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="{{ request()->routeIs('admin.users.*') ? 'true' : 'false' }}" href="#">
                                        <i class="nav-main-link-icon fa fa-users"></i>
                                        <span class="nav-main-link-name">จัดการผู้ใช้</span>
                                    </a>
                                    <ul class="nav-main-submenu">
                                        <li class="nav-main-item">
                                            <a class="nav-main-link{{ request()->routeIs('admin.users.index') && !request('role') ? ' active' : '' }}" href="{{ route('admin.users.index') }}">
                                                <span class="nav-main-link-name">ผู้ใช้ทั้งหมด</span>
                                            </a>
                                        </li>
                                        <li class="nav-main-item">
                                            <a class="nav-main-link{{ request()->routeIs('admin.users.index') && request('role') == 'seller' ? ' active' : '' }}" href="{{ route('admin.users.index', ['role' => 'seller']) }}">
                                                <span class="nav-main-link-name">ผู้ขาย</span>
                                            </a>
                                        </li>
                                        <li class="nav-main-item">
                                            <a class="nav-main-link{{ request()->routeIs('admin.users.index') && request('role') == 'user' ? ' active' : '' }}" href="{{ route('admin.users.index', ['role' => 'user']) }}">
                                                <span class="nav-main-link-name">ผู้ซื้อ</span>
                                            </a>
                                        </li>
                                        <li class="nav-main-item">
                                            <a class="nav-main-link{{ request()->routeIs('admin.users.index') && request('role') == 'admin' ? ' active' : '' }}" href="{{ route('admin.users.index', ['role' => 'admin']) }}">
                                                <span class="nav-main-link-name">แอดมิน</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                
                                <!-- สินค้า -->
                                <li class="nav-main-item{{ request()->routeIs('admin.products.*') || request()->routeIs('admin.categories.*') ? ' open' : '' }}">
                                    <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="{{ request()->routeIs('admin.products.*') || request()->routeIs('admin.categories.*') ? 'true' : 'false' }}" href="#">
                                        <i class="nav-main-link-icon fa fa-gamepad"></i>
                                        <span class="nav-main-link-name">จัดการสินค้า</span>
                                    </a>
                                    <ul class="nav-main-submenu">
                                        <li class="nav-main-item">
                                            <a class="nav-main-link{{ request()->routeIs('admin.products.index') ? ' active' : '' }}" href="{{ route('admin.products.index') }}">
                                                <span class="nav-main-link-name">สินค้าทั้งหมด</span>
                                            </a>
                                        </li>
                                        <li class="nav-main-item">
                                            <a class="nav-main-link{{ request()->routeIs('admin.products.create') ? ' active' : '' }}" href="{{ route('admin.products.create') }}">
                                                <span class="nav-main-link-name">เพิ่มสินค้าใหม่</span>
                                            </a>
                                        </li>
                                        <li class="nav-main-item">
                                            <a class="nav-main-link{{ request()->routeIs('admin.categories.index') ? ' active' : '' }}" href="{{ route('admin.categories.index') }}">
                                                <span class="nav-main-link-name">จัดการหมวดหมู่</span>
                                            </a>
                                        </li>
                                        <li class="nav-main-item">
                                            <a class="nav-main-link{{ request()->routeIs('admin.categories.create') ? ' active' : '' }}" href="{{ route('admin.categories.create') }}">
                                                <span class="nav-main-link-name">เพิ่มหมวดหมู่ใหม่</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                
                                <!-- ออเดอร์ -->
                                <li class="nav-main-item{{ request()->routeIs('admin.orders.*') ? ' open' : '' }}">
                                    <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="{{ request()->routeIs('admin.orders.*') ? 'true' : 'false' }}" href="#">
                                        <i class="nav-main-link-icon fa fa-shopping-cart"></i>
                                        <span class="nav-main-link-name">จัดการออเดอร์</span>
                                    </a>
                                    <ul class="nav-main-submenu">
                                        <li class="nav-main-item">
                                            <a class="nav-main-link{{ request()->routeIs('admin.orders.index') && !request('status') ? ' active' : '' }}" href="{{ route('admin.orders.index') }}">
                                                <span class="nav-main-link-name">ออเดอร์ทั้งหมด</span>
                                            </a>
                                        </li>
                                        <li class="nav-main-item">
                                            <a class="nav-main-link{{ request()->routeIs('admin.orders.index') && request('status') == 'pending' ? ' active' : '' }}" href="{{ route('admin.orders.index', ['status' => 'pending']) }}">
                                                <span class="nav-main-link-name">รอดำเนินการ</span>
                                            </a>
                                        </li>
                                        <li class="nav-main-item">
                                            <a class="nav-main-link{{ request()->routeIs('admin.orders.index') && request('status') == 'processing' ? ' active' : '' }}" href="{{ route('admin.orders.index', ['status' => 'processing']) }}">
                                                <span class="nav-main-link-name">กำลังดำเนินการ</span>
                                            </a>
                                        </li>
                                        <li class="nav-main-item">
                                            <a class="nav-main-link{{ request()->routeIs('admin.orders.index') && request('status') == 'completed' ? ' active' : '' }}" href="{{ route('admin.orders.index', ['status' => 'completed']) }}">
                                                <span class="nav-main-link-name">สำเร็จแล้ว</span>
                                            </a>
                                        </li>
                                        <li class="nav-main-item">
                                            <a class="nav-main-link{{ request()->routeIs('admin.orders.index') && request('status') == 'canceled' ? ' active' : '' }}" href="{{ route('admin.orders.index', ['status' => 'canceled']) }}">
                                                <span class="nav-main-link-name">ยกเลิกแล้ว</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                
                                <!-- การเงิน -->
                                <li class="nav-main-item">
                                    <a class="nav-main-link{{ request()->routeIs('admin.transactions') ? ' active' : '' }}" href="{{ route('admin.transactions') }}">
                                        <i class="nav-main-link-icon fa fa-money-bill"></i>
                                        <span class="nav-main-link-name">ธุรกรรมการเงิน</span>
                                    </a>
                                </li>
                                
                                <!-- รายงาน -->
                                <li class="nav-main-item{{ request()->routeIs('admin.reports.*') ? ' open' : '' }}">
                                    <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="{{ request()->routeIs('admin.reports.*') ? 'true' : 'false' }}" href="#">
                                        <i class="nav-main-link-icon fa fa-chart-bar"></i>
                                        <span class="nav-main-link-name">รายงาน</span>
                                    </a>
                                    <ul class="nav-main-submenu">
                                        <li class="nav-main-item">
                                            <a class="nav-main-link{{ request()->routeIs('admin.reports.overview') ? ' active' : '' }}" href="{{ route('admin.reports.overview') }}">
                                                <span class="nav-main-link-name">ภาพรวม</span>
                                            </a>
                                        </li>
                                        <li class="nav-main-item">
                                            <a class="nav-main-link{{ request()->routeIs('admin.reports.sales') ? ' active' : '' }}" href="{{ route('admin.reports.sales') }}">
                                                <span class="nav-main-link-name">รายงานการขาย</span>
                                            </a>
                                        </li>
                                        <li class="nav-main-item">
                                            <a class="nav-main-link{{ request()->routeIs('admin.reports.users') ? ' active' : '' }}" href="{{ route('admin.reports.users') }}">
                                                <span class="nav-main-link-name">รายงานผู้ใช้</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endif
                            
                            <li class="nav-main-item">
                                <a class="nav-main-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="nav-main-link-icon fa fa-sign-out-alt"></i>
                                    <span class="nav-main-link-name">ออกจากระบบ</span>
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </li>
                        @else
                            <li class="nav-main-heading">บัญชี</li>
                            
                            <li class="nav-main-item">
                                <a class="nav-main-link" href="{{ route('login') }}">
                                    <i class="nav-main-link-icon fa fa-sign-in-alt"></i>
                                    <span class="nav-main-link-name">เข้าสู่ระบบ</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link" href="{{ route('register') }}">
                                    <i class="nav-main-link-icon fa fa-user-plus"></i>
                                    <span class="nav-main-link-name">สมัครสมาชิก</span>
                                </a>
                            </li>
                        @endauth
                    </ul>
                </div>
                <!-- END Side Navigation -->
            </div>
            <!-- END Sidebar Content -->
        </nav>
        <!-- END Sidebar -->

        <!-- Header -->
        <header id="page-header">
            <!-- Header Content -->
            <div class="content-header">
                <!-- Left Section -->
                <div class="space-x-1">
                    <!-- Toggle Sidebar -->
                    <button type="button" class="btn btn-sm btn-alt-secondary" data-toggle="layout" data-action="sidebar_toggle">
                        <i class="fa fa-fw fa-bars"></i>
                    </button>
                    <!-- END Toggle Sidebar -->

                    <!-- Open Search Section -->
                    <div class="d-inline-block">
                        <form action="{{ route('products.search') }}" method="GET">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control form-control-alt" placeholder="ค้นหาสินค้า.." name="query" value="{{ request('query') }}">
                                <button type="submit" class="btn btn-alt-secondary">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    <!-- END Open Search Section -->
                </div>
                <!-- END Left Section -->

                <!-- Right Section -->
                <div class="space-x-1">
                    <!-- User Dropdown -->
                    @auth
                        <div class="dropdown d-inline-block">
                            <button type="button" class="btn btn-sm btn-alt-secondary" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-user d-sm-none"></i>
                                <span class="d-none d-sm-inline-block fw-semibold">{{ Auth::user()->name }}</span>
                                <i class="fa fa-angle-down opacity-50 ms-1"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-md dropdown-menu-end p-0" aria-labelledby="page-header-user-dropdown">
                                <div class="px-2 py-3 bg-body-light rounded-top">
                                    <h5 class="h6 text-center mb-0">
                                        {{ Auth::user()->name }}
                                    </h5>
                                </div>
                                <div class="p-2">
                                    <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1" href="{{ route('profile.show') }}">
                                        <span>โปรไฟล์</span>
                                        <i class="fa fa-fw fa-user opacity-25"></i>
                                    </a>
                                    <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1" href="{{ route('messages.index') }}">
                                        <span>ข้อความ</span>
                                        <i class="fa fa-fw fa-envelope opacity-25"></i>
                                    </a>
                                    <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1" href="{{ route('orders.index') }}">
                                        <span>รายการสั่งซื้อ</span>
                                        <i class="fa fa-fw fa-shopping-cart opacity-25"></i>
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    
                                    @if(Auth::user()->role === 'admin')
                                        <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1" href="{{ route('admin.dashboard') }}">
                                            <span>แดชบอร์ดแอดมิน</span>
                                            <i class="fa fa-fw fa-cog opacity-25"></i>
                                        </a>
                                        <div class="dropdown-divider"></div>
                                    @endif
                                    
                                    @if(Auth::user()->role === 'seller')
                                        <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1" href="{{ route('seller.dashboard') }}">
                                            <span>แดชบอร์ดผู้ขาย</span>
                                            <i class="fa fa-fw fa-store opacity-25"></i>
                                        </a>
                                        <div class="dropdown-divider"></div>
                                    @endif
                                    
                                    <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <span>ออกจากระบบ</span>
                                        <i class="fa fa-fw fa-sign-out-alt opacity-25"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-sm btn-alt-primary">
                            <i class="fa fa-sign-in-alt opacity-50 me-1"></i> เข้าสู่ระบบ
                        </a>
                    @endauth
                    <!-- END User Dropdown -->

                    <!-- Toggle Side Overlay -->
                    @auth
                        <button type="button" class="btn btn-sm btn-alt-secondary" data-toggle="layout" data-action="side_overlay_toggle">
                            <i class="fa fa-fw fa-list"></i>
                        </button>
                    @endauth
                    <!-- END Toggle Side Overlay -->
                </div>
                <!-- END Right Section -->
            </div>
            <!-- END Header Content -->

            <!-- Header Search -->
            <div id="page-header-search" class="overlay-header bg-body-extra-light">
                <div class="content-header">
                    <form class="w-100" action="{{ route('products.search') }}" method="GET">
                        <div class="input-group">
                            <button type="button" class="btn btn-alt-danger" data-toggle="layout" data-action="header_search_off">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                            <input type="text" class="form-control" placeholder="ค้นหาสินค้า.." name="query" value="{{ request('query') }}">
                        </div>
                    </form>
                </div>
            </div>
            <!-- END Header Search -->

            <!-- Header Loader -->
            <div id="page-header-loader" class="overlay-header bg-primary">
                <div class="content-header">
                    <div class="w-100 text-center">
                        <i class="far fa-sun fa-spin text-white"></i>
                    </div>
                </div>
            </div>
            <!-- END Header Loader -->
        </header>
        <!-- END Header -->

        <!-- Main Container -->
        <main id="main-container">
            <!-- Hero -->
            @hasSection('hero')
                @yield('hero')
            @else
                <div class="bg-body-light">
                    <div class="content content-full">
                        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
                            <div class="flex-grow-1">
                                <h1 class="h3 fw-bold mb-1">
                                    @yield('title', 'หน้าหลัก')
                                </h1>
                                <h2 class="fs-base lh-base fw-medium text-muted mb-0">
                                    @yield('subtitle', 'ร้านค้ารหัส Steam คุณภาพ')
                                </h2>
                            </div>
                            <nav class="flex-shrink-0 mt-3 mt-sm-0 ms-sm-3" aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-alt">
                                    @yield('breadcrumb')
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            @endif
            <!-- END Hero -->

            <!-- Page Content -->
            <div class="content">
                @if (session('success'))
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <div class="flex-shrink-0">
                            <i class="fa fa-fw fa-check"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="mb-0">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <div class="flex-shrink-0">
                            <i class="fa fa-fw fa-times"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="mb-0">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                @yield('content')
            </div>
            <!-- END Page Content -->
        </main>
        <!-- END Main Container -->

        <!-- Footer -->
        <footer id="page-footer" class="bg-body-light">
            <div class="content py-3">
                <div class="row fs-sm">
                    <div class="col-sm-6 order-sm-2 py-1 text-center text-sm-end">
                        <a class="fw-semibold" href="#">Steam Market</a> &copy; <span data-toggle="year-copy"></span>
                    </div>
                    <div class="col-sm-6 order-sm-1 py-1 text-center text-sm-start">
                        <a class="fw-semibold" href="#">Steam Market</a> &copy; <span data-toggle="year-copy"></span>
                    </div>
                </div>
            </div>
        </footer>
        <!-- END Footer -->
    </div>
    <!-- END Page Container -->

    <!-- Codebase JS -->

    @stack('scripts')
</body>
</html>