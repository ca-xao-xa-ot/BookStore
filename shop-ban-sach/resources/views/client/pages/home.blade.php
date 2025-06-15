@extends('client.layouts.app')
@section('title', 'Home - Book Store')
@section('description',
    'Find books that match your interests. Browse through our diverse range of meticulously crafted
    literary works.')
@section('keywords', 'books, literature, reading, shopping, ecommerce')

@section('content')
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title animate-fade-in">Khám phá thế giới qua từng trang sách</h1>
                <p class="hero-subtitle animate-fade-up">Từ những cuốn sách bán chạy nhất đến các tác phẩm kinh điển, cửa
                    hàng chúng tôi có tất cả những gì bạn cần.</p>
                <a href="{{ route('books.index') }}" class="hero-cta animate-fade-up">Khám phá ngay</a>
            </div>
        </div>
    </div>

    <!-- Categories Section -->
    <section class="categories-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title animate-fade-up">Danh mục sách</h2>
                <p class="section-subtitle animate-fade-up">Khám phá đa dạng thể loại sách tại cửa hàng của chúng tôi</p>
            </div>

            <div class="categories-grid">
                @foreach ($popularCategories as $category)
                    <a href="{{ route('books.index', ['category' => $category->slug]) }}"
                        class="category-card animate-fade-up" style="animation-delay: {{ $loop->iteration * 0.1 }}s">
                        <div class="category-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <h3 class="category-name">{{ $category->name }}</h3>
                        <p class="category-count">{{ $category->books_count }} sách</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- New Books Section -->
    <section class="books-section bg-light">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title animate-fade-up">Sách mới</h2>
                <p class="section-subtitle animate-fade-up">Những cuốn sách mới nhất vừa được cập nhật</p>
                <a href="{{ route('books.index', ['sort' => 'newest']) }}" class="view-all animate-fade-up">Xem tất cả <i
                        class="fas fa-arrow-right"></i></a>
            </div>

            <div class="books-slider">
                <div class="swiper-container new-books-swiper">
                    <div class="swiper-wrapper">
                        @foreach ($newBooks as $book)
                            <div class="swiper-slide animate-slide-up"
                                style="animation-delay: {{ $loop->iteration * 0.1 }}s">
                                @include('components.item-book', ['book' => $book])
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bestseller Books Section -->
    <section class="books-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title animate-fade-up">Sách bán chạy</h2>
                <p class="section-subtitle animate-fade-up">Những cuốn sách được yêu thích nhất</p>
                <a href="{{ route('books.index', ['is_bestseller' => 1]) }}" class="view-all animate-fade-up">Xem tất cả <i
                        class="fas fa-arrow-right"></i></a>
            </div>

            <div class="bestseller-grid">
                @foreach ($bestsellerBooks as $book)
                    <div class="animate-slide-up" style="animation-delay: {{ $loop->iteration * 0.1 }}s">
                        @include('components.item-book', ['book' => $book])
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url({{ asset('assets/images/banner.jpg') }}) center/cover no-repeat;
            height: 70vh;
            display: flex;
            align-items: center;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            max-width: 650px;
            padding: 2rem;
        }

        .hero-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .hero-cta {
            display: inline-block;
            background-color: var(--primary-color);
            color: white;
            padding: 0.875rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .hero-cta:hover {
            background-color: var(--primary-color-dark);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        /* Section Styles */
        section {
            padding: 5rem 0;
        }

        .bg-light {
            background-color: #f8f9fa;
        }

        .section-header {
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
        }

        .section-title {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 0.8rem;
            color: #333;
        }

        .section-subtitle {
            color: #6c757d;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .view-all {
            position: absolute;
            right: 0;
            top: 0.5rem;
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .view-all:hover {
            color: var(--primary-color-dark);
        }

        /* Categories Section */
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1.5rem;
        }

        .category-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-decoration: none;
            color: #333;
            display: block;
        }

        .category-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .category-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 1rem;
            background: var(--primary-color-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-size: 1.5rem;
        }

        .category-name {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .category-count {
            color: #6c757d;
            margin: 0;
        }

        /* Books Section */
        .books-slider {
            position: relative;
            padding: 10px;
        }

        .swiper-container {
            overflow: hidden;
            padding: 20px 10px;
        }

        .book-card {
            border-radius: 10px;
            transition: transform 0.3s ease;
            height: 100%;
        }

        .book-card:hover {
            transform: translateY(-10px);
        }

        .book-card-inner {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .book-thumbnail {
            position: relative;
            padding-top: 140%;
            overflow: hidden;
        }

        .book-thumbnail img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .book-card:hover .book-thumbnail img {
            transform: scale(1.05);
        }

        .book-actions {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            opacity: 0;
            transform: translateX(20px);
            transition: all 0.3s ease;
        }

        .book-card:hover .book-actions {
            opacity: 1;
            transform: translateX(0);
        }

        .action-btn-home {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: white;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #333;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
        }

        .action-btn-home:hover {
            background: var(--primary-color);
            color: white;
        }

        .book-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: var(--primary-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .book-info {
            padding: 1.25rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .book-category {
            color: var(--primary-color);
            font-size: 0.8rem;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
            text-decoration: none;
        }

        .book-title {
            font-size: 1rem;
            margin-bottom: 0.75rem;
            line-height: 1.4;
            flex-grow: 1;
        }

        .book-title a {
            color: #333;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .book-title a:hover {
            color: var(--primary-color);
        }

        .book-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: #2d418b;
        }

        /* Bestseller Grid */
        .bestseller-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1.5rem;
        }

        /* Promo Section */
        .promo-section {
            background-color: #f8f9fa;
        }

        .promo-wrapper {
            display: flex;
            align-items: center;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .promo-content {
            padding: 3rem;
            flex: 1;
        }

        .promo-content h2 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .promo-content p {
            color: #6c757d;
            margin-bottom: 1.5rem;
        }

        .promo-image {
            flex: 1;
            height: 400px;
        }

        .promo-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .subscribe-form {
            display: flex;
            max-width: 500px;
        }

        .subscribe-form input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: 1px solid #ced4da;
            border-radius: 4px 0 0 4px;
            font-size: 1rem;
        }

        .subscribe-form button {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0 1.5rem;
            border-radius: 0 4px 4px 0;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .subscribe-form button:hover {
            background: var(--primary-color-dark);
        }

        /* Animation Classes */
        .animate-fade-in {
            opacity: 0;
            animation: fadeIn 1s ease forwards;
        }

        .animate-fade-up {
            opacity: 0;
            transform: translateY(30px);
            animation: fadeUp 0.8s ease forwards;
        }

        .animate-slide-up {
            opacity: 0;
            transform: translateY(30px);
            animation: fadeUp 0.8s ease forwards;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }

        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .promo-wrapper {
                flex-direction: column;
            }

            .promo-image {
                height: 200px;
                width: 100%;
            }

            .section-header {
                padding: 0 1rem;
            }

            .view-all {
                position: static;
                display: block;
                margin-top: 1rem;
            }
        }

        @media (max-width: 768px) {
            .hero-section {
                height: 60vh;
            }

            .hero-title {
                font-size: 2rem;
            }

            .hero-subtitle {
                font-size: 1rem;
            }

            .categories-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .hero-section {
                height: 50vh;
            }

            .categories-grid {
                grid-template-columns: 1fr;
            }

            .bestseller-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .promo-content {
                padding: 2rem;
            }

            .subscribe-form {
                flex-direction: column;
            }

            .subscribe-form input {
                border-radius: 4px;
                margin-bottom: 1rem;
            }

            .subscribe-form button {
                border-radius: 4px;
                padding: 0.75rem;
                width: 100%;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Swiper
            const newBooksSwiper = new Swiper('.new-books-swiper', {
                slidesPerView: 1,
                spaceBetween: 15, // Giảm khoảng cách giữa các slide
                loop: true,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                        spaceBetween: 15,
                    },
                    768: {
                        slidesPerView: 3,
                        spaceBetween: 15,
                    },
                    992: {
                        slidesPerView: 4,
                        spaceBetween: 15,
                    },
                    1200: {
                        slidesPerView: 5, // Tăng lên 5 slides cho màn hình lớn
                        spaceBetween: 15,
                    }
                }
            });

            // Animation on scroll
            const observerOptions = {
                threshold: 0.1
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animationPlayState = 'running';
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.animate-fade-up, .animate-slide-up').forEach(el => {
                el.style.animationPlayState = 'paused';
                observer.observe(el);
            });

        });
    </script>
@endpush
