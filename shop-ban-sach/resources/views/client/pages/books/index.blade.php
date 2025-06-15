{{-- filepath: d:\shop-ban-sach\resources\views\client\pages\books\index.blade.php --}}
@extends('client.layouts.app')
@section('title', 'Danh sách sách - Book Store')
@section('description', 'Khám phá bộ sưu tập sách đa dạng của chúng tôi với nhiều thể loại, tác giả và chủ đề khác
    nhau.')
@section('keywords', 'sách, thể loại sách, sách mới, sách bán chạy, mua sách trực tuyến')

@section('content')
    <div class="books-header">
        <div class="container">
            <div class="books-header-content">
                <h1 class="books-title">Danh sách sách</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Sách</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="books-container">
        <div class="container">
            <div class="row">
                <!-- Sidebar Categories -->
                <div class="col-lg-3">
                    <div class="books-sidebar">
                        <div class="filter-card">
                            <h3 class="filter-title">Danh mục</h3>
                            <ul class="category-list">
                                <li
                                    class="category-item {{ !request('category') && !request('is_bestseller') && !request('newest') ? 'active' : '' }}">
                                    <a href="{{ route('books.index') }}">Tất cả sách</a>
                                    <span class="count">{{ \App\Models\Book::count() }}</span>
                                </li>
                                

                                @foreach ($categories as $category)
                                    <li class="category-item {{ request('category') == $category->slug ? 'active' : '' }}">
                                        <a
                                            href="{{ route('books.index', ['category' => $category->slug]) }}">{{ $category->name }}</a>
                                        <span class="count">{{ $category->books_count }}</span>
                                    </li>
                                @endforeach

                               

                                <h3 class="filter-title">Loại sách</h3>

                                <li class="category-item {{ request('is_bestseller') ? 'active' : '' }}">
                                    <a href="{{ route('books.index', ['is_bestseller' => 1]) }}">Sách bán chạy</a>
                                    <span class="count">{{ \App\Models\Book::where('is_bestseller', 1)->count() }}</span>
                                </li>
                                <li class="category-item {{ request('newest') ? 'active' : '' }}">
                                    <a href="{{ route('books.index', ['newest' => 1]) }}">Sách mới</a>
                                    <span class="count">{{ \App\Models\Book::latest()->take(20)->count() }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-lg-9">
                    <div class="books-content">
                        <!-- Search and Results Info -->
                        <div class="books-header-actions">
                            <div class="books-search">
                                <form action="{{ route('books.index') }}" method="GET">
                                    @if (request('category'))
                                        <input type="hidden" name="category" value="{{ request('category') }}">
                                    @endif
                                    @if (request('is_bestseller'))
                                        <input type="hidden" name="is_bestseller" value="{{ request('is_bestseller') }}">
                                    @endif
                                    @if (request('newest'))
                                        <input type="hidden" name="newest" value="{{ request('newest') }}">
                                    @endif
                                    <div class="search-input-wrapper">
                                        <input type="text" name="search" placeholder="Tìm kiếm sách..."
                                            value="{{ request('search') }}">
                                        <button type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div class="books-found">
                                <p>Tìm thấy <span>{{ $books->total() }}</span> kết quả</p>
                            </div>
                        </div>

                        <!-- Status Messages -->
                        @if (request('category') || request('search') || request('is_bestseller') || request('newest'))
                            <div class="search-filters">
                                @if (request('category'))
                                    @php $categoryName = \App\Models\Category::where('slug', request('category'))->first()->name ?? ''; @endphp
                                    <div class="filter-status">
                                        <span class="filter-status-label">Danh mục:</span>
                                        <span class="filter-badge">{{ $categoryName }} <a
                                                href="{{ route('books.index', request()->except('category')) }}"
                                                class="remove-filter">&times;</a></span>
                                    </div>
                                @endif

                                @if (request('is_bestseller'))
                                    <div class="filter-status">
                                        <span class="filter-status-label">Loại sách:</span>
                                        <span class="filter-badge">Sách bán chạy <a
                                                href="{{ route('books.index', request()->except('is_bestseller')) }}"
                                                class="remove-filter">&times;</a></span>
                                    </div>
                                @endif

                                @if (request('newest'))
                                    <div class="filter-status">
                                        <span class="filter-status-label">Loại sách:</span>
                                        <span class="filter-badge">Sách mới <a
                                                href="{{ route('books.index', request()->except('newest')) }}"
                                                class="remove-filter">&times;</a></span>
                                    </div>
                                @endif

                                @if (request('search'))
                                    <div class="filter-status">
                                        <span class="filter-status-label">Tìm kiếm:</span>
                                        <span class="filter-badge">"{{ request('search') }}" <a
                                                href="{{ route('books.index', request()->except('search')) }}"
                                                class="remove-filter">&times;</a></span>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Book Grid -->
                        <div class="books-grid">
                            @if ($books->isEmpty())
                                <div class="no-results">
                                    <div class="no-results-icon">
                                        <i class="far fa-sad-tear"></i>
                                    </div>
                                    <h3>Không tìm thấy kết quả</h3>
                                    <p>Rất tiếc, chúng tôi không thể tìm thấy bất kỳ sách nào phù hợp với tiêu chí tìm kiếm
                                        của bạn.</p>
                                    <a href="{{ route('books.index') }}" class="btn btn-primary">Xem tất cả sách</a>
                                </div>
                            @else
                                <div class="row">
                                    @foreach ($books as $book)
                                        <div class="col-md-4 col-sm-6 col-6 book-grid-item">
                                            @include('components.item-book', ['book' => $book])
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Pagination -->
                        <div class="books-pagination">
                            {{ $books->appends(request()->query())->links('components.paginate') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Books Header */
        .books-header {
            background-color: #f8f9fa;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-bottom: 1px solid #eaecef;
        }

        .books-header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .books-title {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            color: #333;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
        }

        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: #6c757d;
        }

        /* Books Container */
        .books-container {
            margin-bottom: 3rem;
        }

        /* Sidebar Categories */
        .books-sidebar {
            margin-bottom: 2rem;
        }

        .filter-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .filter-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin: 0 0 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #eaecef;
        }

        .category-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .category-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #eaecef;
        }

        .category-item:last-child {
            border-bottom: none;
        }

        .category-item a {
            color: #495057;
            text-decoration: none;
            transition: color 0.2s;
        }

        .category-item a:hover {
            color: var(--primary-color);
        }

        .category-item.active a {
            color: var(--primary-color);
            font-weight: 600;
        }

        .count {
            background: #f1f3f5;
            color: #6c757d;
            border-radius: 20px;
            padding: 0.1rem 0.5rem;
            font-size: 0.8rem;
        }

        /* Books Content */
        .books-content {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
        }

        .books-header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .books-search {
            flex: 1;
            min-width: 250px;
        }

        .search-input-wrapper {
            display: flex;
            border: 1px solid #ced4da;
            border-radius: 5px;
            overflow: hidden;
        }

        .search-input-wrapper input {
            flex: 1;
            border: none;
            padding: 0.5rem 0.75rem;
            outline: none;
        }

        .search-input-wrapper button {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            cursor: pointer;
        }

        .books-found {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .books-found span {
            color: var(--primary-color);
            font-weight: 600;
        }

        /* Search Filters */
        .search-filters {
            margin-bottom: 1.5rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .filter-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filter-status-label {
            font-weight: 600;
            color: #495057;
        }

        .filter-badge {
            background: var(--primary-color-light);
            color: var(--primary-color-dark);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }

        .remove-filter {
            color: var(--primary-color-dark);
            font-size: 1.2rem;
            line-height: 1;
            text-decoration: none;
        }

        .remove-filter:hover {
            color: #dc3545;
        }

        /* Book Grid */
        .books-grid {
            margin-bottom: 2rem;
        }

        .book-grid-item {
            margin-bottom: 2rem;
        }

        /* No Results */
        .no-results {
            text-align: center;
            padding: 3rem 1rem;
        }

        .no-results-icon {
            font-size: 3rem;
            color: #ced4da;
            margin-bottom: 1rem;
        }

        .no-results h3 {
            margin-bottom: 0.5rem;
        }

        .no-results p {
            color: #6c757d;
            margin-bottom: 1.5rem;
        }

        /* Book Card Styles */
        .book-card {
            height: 100%;
        }

        .book-card-inner {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .book-card-inner:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
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

        .book-card-inner:hover .book-thumbnail img {
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

        .book-card-inner:hover .book-actions {
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

        /* Pagination */
        .books-pagination {
            display: flex;
            justify-content: center;
        }

        .books-pagination .pagination {
            margin: 0;
        }

        .books-pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .books-pagination .page-link {
            color: var(--primary-color);
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .books-sidebar {
                margin-bottom: 2rem;
            }
        }

        @media (max-width: 767.98px) {
            .books-header-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .books-search {
                width: 100%;
            }

            .books-header-content {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
        }

        @media (max-width: 575.98px) {
            .book-grid-item {
                padding: 0 0.5rem;
            }

            .book-info {
                padding: 1rem;
            }

            .book-title {
                font-size: 0.9rem;
            }

            .book-price {
                font-size: 1rem;
            }
        }

        .category-divider {
            border-bottom: 2px dashed #eaecef;
            margin: 0.75rem 0;
            list-style: none;
        }
    </style>
@endpush

@push('scripts')

@endpush
