@extends('admin.layouts.app')

@section('content')
<div class="dashboard-container">
    <!-- Sidebar -->
    <div id="sidebar" class="sidebar">
        <div class="sidebar-header">
            <h1 class="text-2xl font-bold color-primary">BookStore</h1>
            <button id="close-sidebar" class="close-sidebar d-md-none">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="sidebar-menu">
            <ul>
                <li class="{{ Route::currentRouteNamed('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="{{ Route::currentRouteNamed('admin.categories.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.categories.index') }}">
                        <i class="fas fa-list"></i>
                        <span>Danh mục</span>
                    </a>
                </li>

                <li class="{{ Route::currentRouteNamed('admin.books.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.books.index') }}">
                        <i class="fas fa-book"></i>
                        <span>Sách</span>
                    </a>
                </li>


                {{-- <li class="{{ Route::currentRouteNamed('admin.users.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.users.index') }}">
                        <i class="fas fa-users"></i>
                        <span>Người dùng</span>
                    </a>
                </li> --}}
                
                <li class="">
                    <a href="">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Đơn hàng</span>
                    </a>
                </li>
               
                <li class="{{ request()->is('admin/settings*') ? 'active' : '' }}">
                    <a href="">
                        <i class="fas fa-cog"></i>
                        <span>Cài đặt</span>
                    </a>
                </li>
                <li class="mt-4">
                    <a href="{{ route('logout') }}">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Đăng xuất</span>
                    </a>
                    
                </li>
            </ul>
        </div>
    </div>

    <!-- Toggle sidebar button - half-circle attached to the sidebar edge -->
    <button id="toggle-sidebar" class="toggle-sidebar-btn">
        <i class="fas fa-chevron-left"></i>
    </button>

    <!-- Main Content -->
    <div class="main-content">
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1 class="page-title">@yield('title', 'Dashboard')</h1>
                </div>
            </div>
            <div class="content">
                <div class="container-fluid">
                    @yield('main-content')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection