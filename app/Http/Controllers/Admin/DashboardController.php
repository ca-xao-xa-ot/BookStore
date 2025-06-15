<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBooks = Book::count();
        $totalUsers = User::where('role', 'user')->count();
        $totalOrders = Order::count();
        $totalRevenue = Order::where('status', 'completed')->sum('total_amount');
        
        $recentOrders = Order::with('user')
            ->latest()
            ->limit(5)
            ->get();
            
        $bestSellingBooks = DB::table('order_items')
            ->select('book_id', DB::raw('SUM(quantity) as total_sold'))
            ->join('books', 'order_items.book_id', '=', 'books.id')
            ->groupBy('book_id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'book' => Book::find($item->book_id),
                    'total_sold' => $item->total_sold,
                ];
            });
            
        return view('admin.pages.dashboard', compact(
            'totalBooks', 
            'totalUsers', 
            'totalOrders', 
            'totalRevenue', 
            'recentOrders',
            'bestSellingBooks'
        ));
    }
}