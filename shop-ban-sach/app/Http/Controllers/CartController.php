<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::with('book.category')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
        
        $subtotal = $carts->sum(function($cart) {
            return $cart->book->price * $cart->quantity;
        });
        
        return view('client.pages.account.cart', compact('carts', 'subtotal'));
    }
    
    public function addToCart(Request $request, Book $book)
    {
        $request->validate([
            'quantity' => 'sometimes|required|integer|min:1|max:10',
        ]);
        
        $quantity = $request->quantity ?? 1;
        
        // Check if book exists in stock
        if ($book->stock < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Số lượng sách trong kho không đủ'
            ], 422);
        }
        
        // Check if book is already in cart
        $existingCart = Cart::where('user_id', Auth::id())
            ->where('book_id', $book->id)
            ->first();
            
        if ($existingCart) {
            // Update quantity
            $newQuantity = $existingCart->quantity + $quantity;
            
            // Check if new quantity exceeds stock
            if ($newQuantity > $book->stock) {
                $newQuantity = $book->stock;
            }
            
            $existingCart->update(['quantity' => $newQuantity]);
        } else {
            // Create new cart item
            Cart::create([
                'user_id' => Auth::id(),
                'book_id' => $book->id,
                'quantity' => $quantity
            ]);
        }
        
        $cartCount = Cart::where('user_id', Auth::id())->sum('quantity');
        
        return response()->json([
            'success' => true,
            'message' => 'Sách đã được thêm vào giỏ hàng',
            'cartCount' => $cartCount
        ]);
    }
    
    public function updateQuantity(Request $request, Cart $cart)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:10',
        ]);
        
        // Check authorization
        if ($cart->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền cập nhật giỏ hàng này'
            ], 403);
        }
        
        // Check stock
        if ($request->quantity > $cart->book->stock) {
            return response()->json([
                'success' => false,
                'message' => 'Số lượng sách trong kho không đủ'
            ], 422);
        }
        
        $cart->update(['quantity' => $request->quantity]);
        
        // Calculate new price
        $itemTotal = $cart->book->price * $cart->quantity;
        
        // Calculate cart subtotal
        $subtotal = Cart::where('user_id', Auth::id())
            ->get()
            ->sum(function($cartItem) {
                return $cartItem->book->price * $cartItem->quantity;
            });
        
        $cartCount = Cart::where('user_id', Auth::id())->sum('quantity');
        
        return response()->json([
            'success' => true,
            'itemTotal' => $itemTotal,
            'formattedItemTotal' => number_format($itemTotal, 0, ',', '.') . 'đ',
            'subtotal' => $subtotal,
            'formattedSubtotal' => number_format($subtotal, 0, ',', '.') . 'đ',
            'cartCount' => $cartCount
        ]);
    }
    
    public function removeFromCart(Cart $cart)
    {
        // Check authorization
        if ($cart->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa mục này khỏi giỏ hàng'
            ], 403);
        }
        
        $cart->delete();
        
        // Get the updated cart data
        $remainingItems = Cart::where('user_id', Auth::id())->count();
        $subtotal = Cart::where('user_id', Auth::id())
            ->get()
            ->sum(function($cartItem) {
                return $cartItem->book->price * $cartItem->quantity;
            });
        
        $cartCount = Cart::where('user_id', Auth::id())->sum('quantity');
        
        return response()->json([
            'success' => true,
            'isEmpty' => $remainingItems === 0,
            'subtotal' => $subtotal,
            'formattedSubtotal' => number_format($subtotal, 0, ',', '.') . 'đ',
            'cartCount' => $cartCount
        ]);
    }
}