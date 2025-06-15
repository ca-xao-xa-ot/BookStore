<div class="book-card {{ $containerClass ?? '' }}">
    <div class="book-card-inner">
        <div class="book-thumbnail">
            <img src="{{ Storage::url($book->cover_image) }}" alt="{{ $book->title }}" loading="lazy">

            @if ($book->is_bestseller)
                <div class="book-badge">Bestseller</div>
            @endif
            <div class="book-actions">
                <button class="action-btn add-to-cart" data-id="{{ $book->id }}" title="Thêm vào giỏ">
                    <i class="fas fa-shopping-cart"></i>
                </button>
                <button class="action-btn add-to-wishlist" data-id="{{ $book->id }}" title="Yêu thích">
                    {{-- Kiểm tra nếu sách đã được yêu thích --}}
                    @if (isset($book->is_favorited) && $book->is_favorited)
                        <i class="fas fa-heart" style="color: var(--primary-color);"></i>
                    @else
                        <i class="far fa-heart"></i>
                    @endif
                </button>
            </div>
        </div>
        <div class="book-info">
            <a href="{{ route('books.index', ['category' => $book->category->slug]) }}"
                class="book-category">{{ $book->category->name }}</a>
            <h3 class="book-title">
                {{ $book->title }}
            </h3>
            <div class="book-price">{{ number_format($book->price, 0, ',', '.') }}đ</div>
        </div>
    </div>
</div>

@once
    @push('styles')
        <style>
            .book-card {
                height: 100%;
                transition: transform 0.3s ease;
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
                transition: box-shadow 0.3s ease;
            }

            .book-card-inner:hover {
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

            .action-btn-item {
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

            .action-btn-item:hover {
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

            @media (max-width: 575.98px) {
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
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Add to cart functionality
                document.querySelectorAll('.add-to-cart').forEach(button => {
                    button.addEventListener('click', function() {
                        const bookId = this.dataset.id;
                        addToCart(bookId);
                    });
                });

                // Add to wishlist functionality
                document.querySelectorAll('.add-to-wishlist').forEach(button => {
                    button.addEventListener('click', function() {
                        const bookId = this.dataset.id;
                        addToWishlist(bookId);
                    });
                });

                function addToCart(bookId) {
                    // Sử dụng URL thuần thay vì route name
                    fetch(`/user/cart/add/${bookId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: JSON.stringify({
                                quantity: 1
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                               showToast('Đã thêm sách vào giỏ hàng', 'success');

                                // Update cart count if you have one
                                if (document.querySelector('.cart-count')) {
                                    document.querySelector('.cart-count').textContent = data.cartCount;
                                }
                            } else {
                                showToast(data.message || 'Có lỗi xảy ra khi thêm sách vào giỏ hàng', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                }

                function addToWishlist(bookId) {
                    // Sử dụng URL thuần thay vì route name
                    fetch(`/user/wishlist/toggle/${bookId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Toggle heart icon
                                const heartIcons = document.querySelectorAll(
                                    `.add-to-wishlist[data-id="${bookId}"] i`);
                                heartIcons.forEach(icon => {
                                    if (data.added) {
                                        icon.classList.remove('far');
                                        icon.classList.add('fas');

                                        showToast('Đã thêm vào danh sách yêu thích', 'success');
                                    } else {
                                        icon.classList.remove('fas');
                                        icon.classList.add('far');

                                        showToast('Đã xóa khỏi danh sách yêu thích', 'success');
                                    }
                                });
                            } else {
                                showToast(data.message || 'Có lỗi xảy ra khi cập nhật danh sách yêu thích',
                                'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                }
            });
        </script>
    @endpush
@endonce
