<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
       
        $newBooks = Book::with('category')->latest()->take(5)->get();
        $bestsellerBooks = Book::with('category')->where('is_bestseller', true)->take(8)->get();
        $popularCategories = Category::withCount('books')->orderBy('books_count', 'desc')->take(8)->get();
        
        // Đánh dấu sách đã yêu thích nếu người dùng đã đăng nhập
        if (Auth::check()) {
            $favoriteBookIds = Auth::user()->favorites()->pluck('book_id')->toArray();
            
            foreach ($newBooks as $book) {
                $book->is_favorited = in_array($book->id, $favoriteBookIds);
            }
            
            foreach ($bestsellerBooks as $book) {
                $book->is_favorited = in_array($book->id, $favoriteBookIds);
            }
        }
        
        return view('client.pages.home', compact('newBooks', 'bestsellerBooks', 'popularCategories'));
    }

    public function showBooks(Request $request)
    {
        $books = Book::with('category')->paginate(20);
        return view('client.pages.books.index', compact('books'));
    }
}
