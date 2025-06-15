<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    
    public function store(Request $request, Book $book)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);
        
        // Check if user has purchased the book
        $hasPurchased = Order::where('user_id', auth()->id())
            ->whereHas('items', function ($query) use ($book) {
                $query->where('book_id', $book->id);
            })
            ->where('status', 'completed')
            ->exists();
            
        if (!$hasPurchased) {
            return back()->with('error', 'You can only review books you have purchased');
        }
        
        // Check if user has already reviewed this book
        $existingReview = Review::where('user_id', auth()->id())
            ->where('book_id', $book->id)
            ->first();
            
        if ($existingReview) {
            $existingReview->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);
            
            return back()->with('success', 'Review updated successfully');
        }
        
        Review::create([
            'user_id' => auth()->id(),
            'book_id' => $book->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);
        
        return back()->with('success', 'Review added successfully');
    }
}