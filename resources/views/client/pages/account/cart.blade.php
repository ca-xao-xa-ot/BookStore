@extends('client.layouts.information')

@section('info_title', 'Giỏ hàng')
@section('info_description', 'Quản lý giỏ hàng của bạn')
@section('info_keyword', 'Giỏ hàng, Mua sắm, Sách')
@section('info_section_title', 'Giỏ hàng của tôi')
@section('info_section_desc', 'Quản lý sản phẩm trong giỏ hàng của bạn')

@section('info_content')
    <div class="cart-container">
        @if($carts->count() > 0)
            <div class="cart-items">
                @foreach($carts as $cart)
                    <div class="cart-item" id="cart-item-{{ $cart->id }}">
                        <div class="row align-items-center">
                            <div class="col-12 col-md-2">
                                <div class="cart-image">
                                    <img src="{{ Storage::url($cart->book->cover_image) }}" alt="{{ $cart->book->title }}" class="img-fluid rounded">
                                </div>
                            </div>
                            <div class="col-12 col-md-5">
                                <div class="cart-info">
                                    <h5 class="cart-title">
                                        <a href="{{ route('books.show', $cart->book->slug) }}" class="text-decoration-none">{{ $cart->book->title }}</a>
                                    </h5>
                                    <p class="cart-category">
                                        <span class="text-muted">Danh mục:</span> 
                                        <a href="{{ route('books.index', ['category' => $cart->book->category->slug]) }}" class="text-decoration-none">{{ $cart->book->category->name }}</a>
                                    </p>
                                    <p class="cart-price">{{ number_format($cart->book->price, 0, ',', '.') }}đ</p>
                                </div>
                            </div>
                            <div class="col-12 col-md-2">
                                <div class="cart-quantity">
                                    <div class="quantity-control">
                                        <button class="quantity-btn minus" data-cart-id="{{ $cart->id }}">-</button>
                                        <input type="number" min="1" max="{{ $cart->book->stock }}" value="{{ $cart->quantity }}" class="quantity-input" id="quantity-{{ $cart->id }}" data-cart-id="{{ $cart->id }}">
                                        <button class="quantity-btn plus" data-cart-id="{{ $cart->id }}">+</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-2">
                                <div class="cart-total text-end">
                                    <p class="item-total" id="total-{{ $cart->id }}">{{ number_format($cart->book->price * $cart->quantity, 0, ',', '.') }}đ</p>
                                </div>
                            </div>
                            <div class="col-12 col-md-1">
                                <div class="cart-actions text-end">
                                    <button class="btn btn-sm btn-outline-danger remove-cart-item" data-cart-id="{{ $cart->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(!$loop->last)
                        <hr class="my-3">
                    @endif
                @endforeach
            </div>

            <div class="cart-summary">
                <div class="row">
                    <div class="col-12 col-md-6 offset-md-6">
                        <div class="summary-card">
                            <h4 class="summary-title">Tổng giỏ hàng</h4>
                            <div class="summary-item">
                                <span class="summary-label">Tổng tiền:</span>
                                <span class="summary-value" id="subtotal">{{ number_format($subtotal, 0, ',', '.') }}đ</span>
                            </div>
                            <div class="summary-actions">
                                <a href="{{ route('books.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-arrow-left me-2"></i> Tiếp tục mua sắm
                                </a>
                                <a href="{{ route('user.checkout') }}" class="btn btn-primary">
                                    Thanh toán <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="empty-cart text-center py-5">
                <i class="fas fa-shopping-cart fa-3x mb-3 text-muted"></i>
                <p class="fs-4 text-muted">Giỏ hàng của bạn đang trống</p>
                <a href="{{ route('books.index') }}" class="btn btn-primary mt-3">Tiếp tục mua sắm</a>
            </div>
        @endif
    </div>
@endsection

@push('info_scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Update quantity when input changes
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', function() {
                updateQuantity(this.dataset.cartId, this.value);
            });
        });
        
        // Increment quantity
        document.querySelectorAll('.quantity-btn.plus').forEach(button => {
            button.addEventListener('click', function() {
                const cartId = this.dataset.cartId;
                const input = document.querySelector(`#quantity-${cartId}`);
                const max = parseInt(input.getAttribute('max'));
                let value = parseInt(input.value) + 1;
                
                if (value > max) value = max;
                
                input.value = value;
                updateQuantity(cartId, value);
            });
        });
        
        // Decrement quantity
        document.querySelectorAll('.quantity-btn.minus').forEach(button => {
            button.addEventListener('click', function() {
                const cartId = this.dataset.cartId;
                const input = document.querySelector(`#quantity-${cartId}`);
                let value = parseInt(input.value) - 1;
                
                if (value < 1) value = 1;
                
                input.value = value;
                updateQuantity(cartId, value);
            });
        });
        
        // Remove cart item
        document.querySelectorAll('.remove-cart-item').forEach(button => {
            button.addEventListener('click', function() {
                const cartId = this.dataset.cartId;
                
                Swal.fire({
                    title: 'Xác nhận xóa?',
                    text: 'Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        removeCartItem(cartId);
                    }
                });
            });
        });
        
        // Function to update cart item quantity
        function updateQuantity(cartId, quantity) {
            fetch(`/user/cart/update/${cartId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ 
                    quantity: quantity,
                    _method: 'POST'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update item total
                    document.querySelector(`#total-${cartId}`).textContent = data.formattedItemTotal;
                    
                    // Update subtotal
                    document.querySelector('#subtotal').textContent = data.formattedSubtotal;
                    
                    // Update cart count
                    if (document.querySelector('.cart-badge')) {
                        document.querySelector('.cart-badge').textContent = data.cartCount;
                    }
                } else {
                    showToast(data.message || 'Có lỗi xảy ra khi cập nhật số lượng sản phẩm', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
        
        // Function to remove cart item
        function removeCartItem(cartId) {
            fetch(`/user/cart/remove/${cartId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove cart item from DOM
                    const cartItem = document.querySelector(`#cart-item-${cartId}`);
                    const hr = cartItem.nextElementSibling;
                    
                    if (hr && hr.tagName === 'HR') {
                        hr.remove();
                    }
                    
                    cartItem.remove();
                    
                    // Update subtotal
                    document.querySelector('#subtotal').textContent = data.formattedSubtotal;
                    
                    // Update cart count
                    if (document.querySelector('.cart-badge')) {
                        document.querySelector('.cart-badge').textContent = data.cartCount;
                    }
                    
                    // Show empty cart if no items left
                    if (data.isEmpty) {
                        document.querySelector('.cart-container').innerHTML = `
                            <div class="empty-cart text-center py-5">
                                <i class="fas fa-shopping-cart fa-3x mb-3 text-muted"></i>
                                <p class="fs-4 text-muted">Giỏ hàng của bạn đang trống</p>
                                <a href="{{ route('books.index') }}" class="btn btn-primary mt-3">Tiếp tục mua sắm</a>
                            </div>
                        `;
                    }
                    
                    showToast('Sản phẩm đã được xóa khỏi giỏ hàng', 'success');
                } else {
                    showToast(data.message || 'Có lỗi xảy ra khi xóa sản phẩm khỏi giỏ hàng', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    });
</script>
@endpush

@push('styles')
<style>
    .cart-item {
        padding: 1rem 0;
    }
    
    .cart-image {
        position: relative;
        width: 100%;
        max-height: 150px;
        overflow: hidden;
    }
    
    .cart-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .cart-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .cart-title a {
        color: #333;
    }
    
    .cart-title a:hover {
        color: var(--primary-color);
    }
    
    .cart-category {
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }
    
    .cart-category a {
        color: var(--primary-color);
    }
    
    .cart-price {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--primary-color-dark);
        margin-bottom: 0.5rem;
    }
    
    .quantity-control {
        display: flex;
        align-items: center;
        width: 120px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        overflow: hidden;
    }
    
    .quantity-btn {
        width: 30px;
        height: 36px;
        background: #f8f9fa;
        border: none;
        font-weight: bold;
        cursor: pointer;
    }
    
    .quantity-input {
        width: 60px;
        height: 36px;
        text-align: center;
        border: none;
        border-left: 1px solid #dee2e6;
        border-right: 1px solid #dee2e6;
        border-radius: 0;
    }
    
    .quantity-input:focus {
        outline: none;
    }
    
    .quantity-input::-webkit-inner-spin-button, 
    .quantity-input::-webkit-outer-spin-button { 
        -webkit-appearance: none;
        margin: 0;
    }
    
    .item-total {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--primary-color-dark);
        margin: 0;
    }
    
    .summary-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        margin-top: 2rem;
    }
    
    .summary-title {
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #dee2e6;
    }
    
    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        font-size: 1.1rem;
    }
    
    .summary-value {
        font-weight: 700;
        color: var(--primary-color-dark);
    }
    
    .summary-actions {
        display: flex;
        justify-content: space-between;
        margin-top: 1.5rem;
    }
    
    @media (max-width: 767.98px) {
        .cart-item {
            padding-bottom: 1.5rem;
        }
        
        .cart-image {
            margin-bottom: 1rem;
        }
        
        .cart-info {
            margin-bottom: 1rem;
        }
        
        .quantity-control {
            margin: 0 auto;
            margin-bottom: 1rem;
        }
        
        .cart-total {
            text-align: center !important;
            margin-bottom: 1rem;
        }
        
        .cart-actions {
            text-align: center !important;
        }
        
        .summary-actions {
            flex-direction: column;
            gap: 1rem;
        }
        
        .summary-actions .btn {
            width: 100%;
        }
    }
</style>
@endpush