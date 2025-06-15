@extends('admin.layouts.sidebar')

@section('title', 'Thêm sách mới')

@section('main-content')
    <div class="book-form-container">
        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-plus icon-title"></i>
                    <h5>Thêm sách mới</h5>
                </div>
                <a href="{{ route('admin.books.index') }}" class="back-link">
                    <i class="fas fa-arrow-left"></i> Quay lại danh sách
                </a>
            </div>
            <div class="form-body">
                @include('components.alert', ['alertType' => 'alert'])

                <form action="{{ route('admin.books.store') }}" method="POST" class="book-form"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="form-section">
                        <h6 class="section-title">Thông tin cơ bản</h6>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="title" class="form-label required">
                                    Tiêu đề <span class="required-asterisk">*</span>
                                </label>
                                <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror"
                                    placeholder="Nhập tiêu đề sách" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text">Slug sẽ được tự động tạo từ tiêu đề</small>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="category_id" class="form-label required">Danh mục</label>
                                <select id="category_id" name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label required">Mô tả</label>
                            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="6"
                                placeholder="Nhập mô tả về sách" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-section">
                        <h6 class="section-title">Hình ảnh và giá</h6>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="cover_image" class="form-label required">Ảnh bìa</label>
                                <input type="file" id="cover_image" name="cover_image" 
                                    class="form-control @error('cover_image') is-invalid @enderror" accept="image/*" required>
                                @error('cover_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text">Kích thước tối đa: 2MB. Định dạng: JPG, PNG, GIF</small>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="image-preview-container">
                                    <div id="image-preview">
                                        <div class="placeholder">
                                            <i class="fas fa-image"></i>
                                            <p>Xem trước hình ảnh</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="price" class="form-label required">Giá bán (VNĐ)</label>
                                <input type="number" id="price" name="price" class="form-control @error('price') is-invalid @enderror"
                                    placeholder="0" min="0" step="1000" value="{{ old('price', 0) }}" required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-4">
                                <label for="stock" class="form-label required">Tồn kho</label>
                                <input type="number" id="stock" name="stock" class="form-control @error('stock') is-invalid @enderror"
                                    placeholder="0" min="0" step="1" value="{{ old('stock', 0) }}" required>
                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label">Bán chạy</label>
                                <div class="checkbox-container">
                                    <input type="checkbox" id="is_bestseller" name="is_bestseller" class="form-check-input" 
                                        {{ old('is_bestseller') ? 'checked' : '' }}>
                                    <label for="is_bestseller" class="form-check-label">Đánh dấu là sách bán chạy</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.books.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Lưu sách
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Image Preview
            $('#cover_image').change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#image-preview').html(`<img src="${e.target.result}" alt="Preview" style="max-width: 100%; max-height: 200px;">`);
                    }
                    reader.readAsDataURL(file);
                } else {
                    $('#image-preview').html(`
                        <div class="placeholder">
                            <i class="fas fa-image"></i>
                            <p>Xem trước hình ảnh</p>
                        </div>
                    `);
                }
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .book-form-container {
            margin-bottom: 2rem;
        }
        
        .form-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        
        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #eaecef;
        }
        
        .form-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .icon-title {
            color: var(--primary-color);
            font-size: 1.2rem;
        }
        
        .back-link {
            color: #6c757d;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            transition: color 0.2s;
        }
        
        .back-link:hover {
            color: var(--primary-color);
        }
        
        .form-body {
            padding: 1.5rem;
        }
        
        .form-section {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #eaecef;
        }
        
        .section-title {
            color: #495057;
            margin-bottom: 1.25rem;
            font-weight: 600;
            font-size: 1rem;
        }
        
        .form-label.required:after {
            content: "*";
            color: #dc3545;
            margin-left: 0.25rem;
        }
        
        .image-preview-container {
            width: 100%;
            height: 100%;
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        #image-preview {
            width: 100%;
            height: 100%;
            min-height: 200px;
            border: 1px dashed #ced4da;
            border-radius: 4px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        #image-preview .placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #adb5bd;
        }
        
        #image-preview .placeholder i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .checkbox-container {
            display: flex;
            align-items: center;
            padding-top: 0.5rem;
        }
        
        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .form-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .form-actions {
                flex-direction: column-reverse;
                gap: 1rem;
            }
        }
    </style>
@endpush