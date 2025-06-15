<div class="wishlist-item" id="wishlist-item-{{ $wishlistItem->id }}">
    <div class="row align-items-center">
        <div class="col-12 col-md-2">
            <div class="wishlist-image">
                <img src="{{ Storage::url($wishlistItem->cover_image) }}" alt="{{ $wishlistItem->title }}"
                    class="img-fluid rounded">
            </div>
        </div>
        <div class="col-12 col-md-7">
            <div class="wishlist-info">
                <h5 class="wishlist-title">
                    <a href="{{ route('books.show', $wishlistItem->slug) }}"
                        class="text-decoration-none">{{ $wishlistItem->title }}</a>
                </h5>
                <p class="wishlist-category">
                    <span class="text-muted">Danh mục:</span>
                    <a href="{{ route('books.index', ['category' => $wishlistItem->category->slug]) }}"
                        class="text-decoration-none">{{ $wishlistItem->category->name }}</a>
                </p>
                <p class="wishlist-price">{{ number_format($wishlistItem->price, 0, ',', '.') }}đ</p>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="wishlist-actions text-end">
                <button class="btn btn-primary add-to-cart-from-wishlist mb-2" data-id="{{ $wishlistItem->id }}">
                    <i class="fas fa-shopping-cart me-1"></i> Thêm vào giỏ
                </button>
                <form action="{{ route('user.wishlist.destroy', $wishlistItem->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger remove-wishlist">
                        <i class="fas fa-trash me-1"></i> Xóa
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@once
    @push('styles')
        <style>
            .wishlist-item {
                padding: 1.5rem 0;
            }

            .wishlist-image {
                position: relative;
                width: 100%;
                max-height: 150px;
                overflow: hidden;
            }

            .wishlist-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .wishlist-title {
                font-size: 1.1rem;
                font-weight: 600;
                margin-bottom: 0.5rem;
            }

            .wishlist-title a {
                color: #333;
            }

            .wishlist-title a:hover {
                color: var(--primary-color);
            }

            .wishlist-category {
                font-size: 0.9rem;
                margin-bottom: 0.5rem;
            }

            .wishlist-category a {
                color: var(--primary-color);
            }

            .wishlist-price {
                font-size: 1.2rem;
                font-weight: 700;
                color: var(--primary-color-dark);
                margin-bottom: 0.5rem;
            }

            .wishlist-actions {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }

            @media (max-width: 767.98px) {
                .wishlist-item {
                    padding: 1rem 0;
                }

                .wishlist-image {
                    margin-bottom: 1rem;
                }

                .wishlist-info {
                    margin-bottom: 1rem;
                }

                .wishlist-actions {
                    flex-direction: row;
                    justify-content: space-between;
                }

                .wishlist-actions .btn {
                    flex: 1;
                }
            }

            .action-btn .fas.fa-heart {
                color: var(--primary-color);
                /* Màu đỏ của heart khi đã yêu thích */
            }

            .action-btn:hover .fas.fa-heart {
                color: white;
                /* Khi hover, màu sẽ chuyển thành trắng để phù hợp với background */
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.add-to-cart-from-wishlist').forEach(button => {
                    button.addEventListener('click', function() {
                        const bookId = this.dataset.id;

                        fetch(`cart/add/${bookId}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    quantity: 1
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Show success message
                                    Swal.fire({
                                        title: 'Thành công!',
                                        text: 'Đã thêm sách vào giỏ hàng',
                                        icon: 'success',
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 3000
                                    });

                                    // Update cart count
                                    if (document.querySelector('.cart-badge')) {
                                        document.querySelector('.cart-badge').textContent = data
                                            .cartCount;
                                    }
                                } else {
                                    Swal.fire({
                                        title: 'Lỗi!',
                                        text: data.message ||
                                            'Có lỗi xảy ra khi thêm vào giỏ hàng',
                                        icon: 'error'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                            });
                    });
                });
            });

            function addToWishlist(bookId) {
                fetch(`/user/wishlist/toggle/${bookId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Toggle heart icon
                            const heartIcons = document.querySelectorAll(`.add-to-wishlist[data-id="${bookId}"] i`);
                            heartIcons.forEach(icon => {
                                if (data.added) {
                                    icon.classList.remove('far');
                                    icon.classList.add('fas');
                                    icon.style.color = 'var(--primary-color)'; // Đặt màu đỏ khi thích

                                    Swal.fire({
                                        title: 'Thành công!',
                                        text: 'Đã thêm vào danh sách yêu thích',
                                        icon: 'success',
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 3000
                                    });
                                } else {
                                    icon.classList.remove('fas');
                                    icon.classList.add('far');
                                    icon.style.color = ''; // Xóa style màu

                                    Swal.fire({
                                        title: 'Đã xóa!',
                                        text: 'Đã xóa khỏi danh sách yêu thích',
                                        icon: 'info',
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 3000
                                    });
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Lỗi!',
                                text: data.message || 'Có lỗi xảy ra',
                                icon: 'error'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }
        </script>
    @endpush
@endonce
