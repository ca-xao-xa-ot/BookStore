<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>@yield('title', 'Home')</title>
    <meta name="description" content="@yield('description', 'IchorShop - Your one-stop shop for all things Ichor')">
    <meta name="keywords" content="@yield('keywords', 'Ichor, Shop, Online Store')">

    <link rel="icon" href="" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="" type="image/x-icon">
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inria+Sans:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&display=swap"
        rel="stylesheet">
    <!-- Bootstrap CSS -->

    {{-- styles --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/styles_shop.css') }}">

    @stack('styles')

    {{-- end styles --}}
</head>

<body>
    @props([
        'type' => 'default',
        'showPromo' => true,
        'showNavigation' => true,
        'showSearch' => true,
        'logoSrc' => null,
    ])

    @php
        $headerClasses = match ($type) {
            'minimal' => 'shop-header minimal-header',
            'checkout' => 'shop-header checkout-header',
            'success' => 'shop-header success-header',
            default => 'shop-header',
        };
    @endphp

    <header class="{{ $headerClasses }}">

        <!-- Main Header -->
        <div class="main-header">
            <div class="container">
                <div class="d-flex align-items-center justify-content-between">
                    <!-- Mobile Menu Button -->
                    @if ($showNavigation)
                        <button class="mobile-menu-btn d-lg-none" onclick="toggleMobileNav()">
                            <i class="fas fa-bars"></i>
                        </button>
                    @endif

                    <!-- Logo -->
                    <a class="navbar-brand p-0" href="{{ route('home') }}">
                        <h1 class="text-2xl font-bold color-primary">BookStore</h1>
                    </a>

                    <!-- Search -->
                    @if ($showSearch && $type !== 'checkout' && $type !== 'success')
                        <div class="search-container-custom d-none d-md-block">
                            <form action="{{ route('books.index') }}">
                                <input type="text" class="search-input-custom" placeholder="Search for products..."
                                    name="search" value="{{ request()->input('search', '') }}">
                                <button type="submit" class="search-btn-custom">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="header-actions">
                        @if ($showSearch && ($type === 'checkout' || $type === 'success'))
                            <!-- Minimal actions for checkout/success -->
                        @else
                            <button class="action-btn d-md-none mt-1">
                                <i class="fas fa-search"></i>
                            </button>
                        @endif

                        <a class="action-btn" href="{{ route('user.cart.index') }}">
                            <img src="{{ asset('assets/images/svg/cart.svg') }}" alt="Cart">
                            <span
                                class="cart-badge">{{ Auth::check() ? App\Models\Cart::where('user_id', Auth::id())->sum('quantity') : 0 }}</span>
                        </a>

                        @if (auth()->check())
                            <div class="dropdown">
                                <a href="#"
                                    class="action-btn mt-0 mt-md-1 text-decoration-none d-flex align-items-baseline dropdown-toggle"
                                    id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="{{ auth()->user()->avatar ? Storage::url(auth()->user()->avatar) : asset('assets/images/avatar_default.jpg') }}"
                                        alt="User" class="user-icon rounded-circle me-0 me-md-2" height="40"
                                        width="40">
                                    <span class="d-none d-md-inline text-sm">{{ auth()->user()->name }}</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end user-dropdown"
                                    aria-labelledby="userDropdown">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                            <img src="{{ asset('assets/images/svg/dashboard.svg') }}" alt=""
                                                class="me-2">
                                            Admin Dashboard
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('user.my.account') }}">
                                            <i class="fa-regular fa-user me-2"></i>
                                            My Account
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="">
                                            <img src="{{ asset('assets/images/svg/orders.svg') }}" alt="Orders"
                                                style="margin:0 3px 0 -3px">
                                            My Orders
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('user.wishlist.index') }}">
                                            <i class="fa-regular fa-heart me-2"></i>
                                            Wishlist
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="{{ route('logout') }}">
                                            <i class="fas fa-sign-out-alt me-2"></i>
                                            Logout
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="action-btn mt-0 mt-md-1">
                                <i class="fa-regular fa-circle-user"></i>
                            </a>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Navigation -->
    @if ($showNavigation && $type !== 'minimal')
        <div class="mobile-nav-overlay" onclick="closeMobileNav()"></div>
        <div class="mobile-nav">
            <div class="mobile-nav-header">
                <button class="mobile-nav-close" onclick="closeMobileNav()">×</button>
            </div>

            @if ($showSearch)
                <div class="mobile-search">
                    <form class="position-relative">
                        <input type="text" class="search-input-custom" placeholder="Search for products...">
                        <button type="submit" class="search-btn-custom">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            @endif

            <nav>
                <a href="#" class="mobile-nav-item">Shop</a>
                <a href="#" class="mobile-nav-item">On Sale</a>
                <a href="#" class="mobile-nav-item">New Arrivals</a>
                <a href="#" class="mobile-nav-item">Brands</a>
            </nav>
        </div>

        @once
            <script>
                function toggleMobileNav() {
                    document.querySelector('.mobile-nav-overlay').classList.add('show');
                    document.querySelector('.mobile-nav').classList.add('show');
                    document.body.style.overflow = 'hidden';
                }

                function closeMobileNav() {
                    document.querySelector('.mobile-nav-overlay').classList.remove('show');
                    document.querySelector('.mobile-nav').classList.remove('show');
                    document.body.style.overflow = '';
                }

                function closePromoBanner() {
                    const promoBanner = document.getElementById('promoBanner');
                    if (promoBanner) {
                        promoBanner.remove();
                        localStorage.setItem('promoBannerClosed', 'true');
                    }
                }

                document.addEventListener('DOMContentLoaded', function() {
                    const promoBanner = document.getElementById('promoBanner');

                    if (promoBanner) {
                        if (localStorage.getItem('promoBannerClosed') === 'true') {
                            promoBanner.remove();
                        } else {
                            promoBanner.classList.remove('d-none');
                        }
                    }
                });
            </script>
        @endonce
    @endif
