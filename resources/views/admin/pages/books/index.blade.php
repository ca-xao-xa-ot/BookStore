@extends('admin.layouts.sidebar')

@section('title', 'Quản lý sách')

@section('main-content')
<div class="book-container">
    <div class="content-card">
        <div class="card-top">
            <div class="card-title">
                <i class="fas fa-book icon-title"></i>
                <h5>Danh sách sách</h5>
            </div>
            <a href="{{ route('admin.books.create') }}" class="action-button">
                <i class="fas fa-plus"></i> Thêm sách mới
            </a>
        </div>
        
        <div class="card-content">
            @include('components.alert', ['alertType' => 'alert'])
            
            @if($books->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h4>Chưa có sách nào</h4>
                    <p>Bắt đầu bằng cách thêm quyển sách đầu tiên.</p>
                    <a href="{{ route('admin.books.create') }}" class="action-button">
                        <i class="fas fa-plus"></i> Thêm sách mới
                    </a>
                </div>
            @else
                <div class="data-table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="column-small">STT</th>
                                <th class="column-medium">Hình ảnh</th>
                                <th class="column-large">Tiêu đề</th>
                                <th class="column-medium">Danh mục</th>
                                <th class="column-small">Giá</th>
                                <th class="column-small">Tồn kho</th>
                                <th class="column-small">Bán chạy</th>
                                <th class="column-small text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($books as $index => $book)
                                <tr>
                                    <td class="text-center">{{ ($books->currentPage() - 1) * $books->perPage() + $index + 1 }}</td>
                                    <td class="book-cover">
                                        <img src="{{ Storage::url($book->cover_image) }}" alt="{{ $book->title }}">
                                    </td>
                                    <td class="item-name">
                                        {{ $book->title }}
                                        <div class="book-slug text-muted">{{ $book->slug }}</div>
                                    </td>
                                    <td>
                                        <span class="category-badge">{{ $book->category->name }}</span>
                                    </td>
                                    <td class="text-right book-price">{{ number_format($book->price, 0, ',', '.') }}đ</td>
                                    <td class="text-center">
                                        <span class="stock-badge {{ $book->stock > 0 ? 'in-stock' : 'out-stock' }}">
                                            {{ $book->stock }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($book->is_bestseller)
                                            <span class="bestseller-badge"><i class="fas fa-star"></i></span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons-wrapper">
                                            <a href="{{ route('admin.books.edit', $book) }}" class="action-icon edit-icon text-decoration-none" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @include('components.delete-form', [
                                                'id' => $book->id,
                                                'route' => route('admin.books.destroy', $book),
                                                'message' => "Bạn có chắc chắn muốn xóa sách '{$book->title}'?"
                                            ])
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="pagination-wrapper">
                    <div class="pagination-info">
                        Hiển thị {{ $books->firstItem() ?? 0 }} đến {{ $books->lastItem() ?? 0 }} của {{ $books->total() }} sách
                    </div>
                    <div class="pagination-controls">
                        {{ $books->links('components.paginate') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .book-container {
        margin-bottom: 2rem;
    }
    
    .content-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    
    .card-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #eaecef;
    }
    
    .card-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .icon-title {
        color: var(--primary-color);
        font-size: 1.2rem;
    }
    
    .action-button {
        background-color: var(--primary-color);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .action-button:hover {
        background-color: var(--primary-color-5);
        color: white;
        transform: translateY(-2px);
    }
    
    .card-content {
        padding: 1.5rem;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
    }
    
    .empty-state-icon {
        font-size: 3rem;
        color: #cfd8dc;
        margin-bottom: 1rem;
    }
    
    .empty-state h4 {
        margin-bottom: 0.5rem;
        color: #455a64;
    }
    
    .empty-state p {
        color: #78909c;
        margin-bottom: 1.5rem;
    }
    
    .data-table-container {
        overflow-x: auto;
    }
    
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .data-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        text-align: left;
        padding: 0.75rem 1rem;
        border-bottom: 2px solid #eaecef;
    }
    
    .data-table td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #eaecef;
        vertical-align: middle;
    }
    
    .book-cover img {
        width: 60px;
        height: 80px;
        object-fit: cover;
        border-radius: 4px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    
    .book-slug {
        font-size: 0.8rem;
        margin-top: 0.25rem;
    }
    
    .category-badge {
        display: inline-block;
        background: #e3f2fd;
        color: #1976d2;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.85rem;
    }
    
    .stock-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-weight: 500;
        min-width: 40px;
    }
    
    .in-stock {
        background-color: #e8f5e9;
        color: #2e7d32;
    }
    
    .out-stock {
        background-color: #ffebee;
        color: #c62828;
    }
    
    .bestseller-badge {
        display: inline-block;
        color: #ff9800;
        font-size: 1.1rem;
    }
    
    .book-price {
        font-weight: 600;
        color: #d32f2f;
    }
    
    .action-buttons-wrapper {
        display: flex;
        justify-content: center;
        gap: 0.75rem;
    }
    
    .action-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 4px;
        color: #455a64;
        transition: all 0.2s;
    }
    
    .edit-icon:hover {
        background-color: #e3f2fd;
        color: #1976d2;
    }
    
    .delete-icon:hover {
        background-color: #ffebee;
        color: #d32f2f;
    }
    
    .pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid #eaecef;
    }
    
    .pagination-info {
        color: #78909c;
        font-size: 0.875rem;
    }
    
    .text-muted {
        color: #78909c;
    }
    
    .text-right {
        text-align: right;
    }
    
    .text-center {
        text-align: center;
    }
    
    .column-small {
        width: 80px;
    }
    
    .column-medium {
        width: 150px;
    }
    
    .column-large {
        min-width: 200px;
    }
    
    @media (max-width: 768px) {
        .card-top {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
        
        .pagination-wrapper {
            flex-direction: column;
            gap: 1rem;
        }
    }
</style>
@endpush