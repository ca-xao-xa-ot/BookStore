<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BooksController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with('category');
        
        // Lọc theo danh mục
        if ($request->has('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }
        
        // Lọc sách bán chạy
        if ($request->has('is_bestseller')) {
            $query->where('is_bestseller', true);
        }
        
        // Lọc sách mới
        if ($request->has('newest')) {
            $query->latest();
        }
        
        // Tìm kiếm
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Phân trang kết quả
        $books = $query->paginate(12);
        
        // Đánh dấu sách đã yêu thích nếu người dùng đã đăng nhập
        if (Auth::check()) {
            $favoriteBookIds = Auth::user()->favorites()->pluck('book_id')->toArray();
            
            foreach ($books as $book) {
                $book->is_favorited = in_array($book->id, $favoriteBookIds);
            }
        }
        
        // Lấy tất cả danh mục
        $categories = Category::withCount('books')->get();
        
        return view('client.pages.books.index', compact('books', 'categories'));
    }
    
    public function show($slug)
    {
        $book = Book::where('slug', $slug)->firstOrFail();
        
        // Đánh dấu nếu sách đã được yêu thích
        if (Auth::check()) {
            $book->is_favorited = Auth::user()->favorites()->where('book_id', $book->id)->exists();
        }
        
        $relatedBooks = Book::where('category_id', $book->category_id)
                           ->where('id', '!=', $book->id)
                           ->take(4)
                           ->get();
        
        // Đánh dấu sách liên quan đã yêu thích
        if (Auth::check()) {
            $favoriteBookIds = Auth::user()->favorites()->pluck('book_id')->toArray();
            
            foreach ($relatedBooks as $relatedBook) {
                $relatedBook->is_favorited = in_array($relatedBook->id, $favoriteBookIds);
            }
        }
        
        return view('client.pages.books.show', compact('book', 'relatedBooks'));
    }
}