<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        try {
            // Get statistics for the dashboard
            $totalProducts = Product::count();
            $totalCategories = Category::count();
            $activeProducts = Product::where('status', true)->count();
            $inactiveProducts = Product::where('status', false)->count();
            $lowStockProducts = Product::where('current_stock', '<', 10)->count();
            
            // Get top categories by product count
            $topCategories = Category::withCount('products')
                ->orderByDesc('products_count')
                ->take(5)
                ->get();
                
            // Get recently added products
            $recentProducts = Product::with('category')
                ->latest()
                ->take(5)
                ->get();
                
            return view('dashboard', compact(
                'totalProducts', 
                'totalCategories', 
                'activeProducts', 
                'inactiveProducts', 
                'lowStockProducts',
                'topCategories',
                'recentProducts'
            ));
        } catch (\Exception $e) {
            \Log::error('Error loading dashboard: ' . $e->getMessage());
            return view('dashboard', [
                'totalProducts' => 0,
                'totalCategories' => 0,
                'activeProducts' => 0,
                'inactiveProducts' => 0,
                'lowStockProducts' => 0,
                'topCategories' => collect([]),
                'recentProducts' => collect([])
            ])->with('error', 'There was an issue loading dashboard data. Some statistics may be unavailable.');
        }
    }
}