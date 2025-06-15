<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with('items.book')
            ->latest()
            ->paginate(10);
            
        return view('orders.index', compact('orders'));
    }
    
    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
        
        $order->load('items.book');
        
        return view('orders.show', compact('order'));
    }
    
    public function checkout()
    {
        
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string',
            'payment_method' => 'required|in:cod,bank_transfer',
        ]);
        
        $cartItems = Cart::where('user_id', auth()->id())
            ->with('book')
            ->get();
            
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }
        
        // Check stock availability
        foreach ($cartItems as $item) {
            if ($item->book->stock < $item->quantity) {
                return back()->with('error', "Not enough stock for {$item->book->title}");
            }
        }
        
        $total = $cartItems->sum(function ($item) {
            return $item->book->price * $item->quantity;
        });
        
        DB::transaction(function () use ($cartItems, $total, $request) {
            // Create order
            $order = Order::create([
                'user_id' => auth()->id(),
                'total_amount' => $total,
                'status' => 'pending',
                'shipping_address' => $request->shipping_address,
                'payment_method' => $request->payment_method,
            ]);
            
            // Create order items and update stock
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'book_id' => $item->book_id,
                    'quantity' => $item->quantity,
                    'price' => $item->book->price,
                ]);
                
                // Update stock
                $item->book->decrement('stock', $item->quantity);
            }
            
            // Clear cart
            Cart::where('user_id', auth()->id())->delete();
        });
        
        return redirect()->route('orders.index')->with('success', 'Order placed successfully');
    }
}