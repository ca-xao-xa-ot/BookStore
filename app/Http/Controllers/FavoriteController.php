<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        $wishlists = Auth::user()->favorites()->with('category')->get();
        return view('client.pages.account.wishlist', compact('wishlists'));
    }
    
    public function toggle(Book $book)
    {
        $user = Auth::user();
        
        // Check if the book is already in favorites
        $favorite = $user->favorites()->where('book_id', $book->id)->exists();
        
        if ($favorite) {
            // Remove from favorites
            $user->favorites()->detach($book->id);
            $added = false;
        } else {
            // Add to favorites
            $user->favorites()->attach($book->id);
            $added = true;
        }
        
        return response()->json([
            'success' => true,
            'added' => $added,
            'message' => $added ? 'Thêm vào danh sách yêu thích thành công' : 'Đã xóa khỏi danh sách yêu thích'
        ]);
    }
    
    public function destroy($bookId)
    {
        $user = Auth::user();
        $user->favorites()->detach($bookId);
        
        return redirect()->back()->with('success', 'Đã xóa sách khỏi danh sách yêu thích');
    }
}