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
            // Get statistics for the dashboard (only for the authenticated user)
            $totalProducts = Product::where('user_id', auth()->id())->count();
            $totalCategories = Category::where('user_id', auth()->id())->count();
            $activeProducts = Product::where('user_id', auth()->id())->where('status', true)->count();
            $inactiveProducts = Product::where('user_id', auth()->id())->where('status', false)->count();
            $lowStockProducts = Product::where('user_id', auth()->id())->where('current_stock', '<', 10)->count();

            // Get top categories by product count
            $topCategories = Category::where('user_id', auth()->id())
                ->withCount(['products' => function($query) {
                    $query->where('user_id', auth()->id());
                }])
                ->orderByDesc('products_count')
                ->take(5)
                ->get();

            // Get recently added products
            $recentProducts = Product::with('category')
                ->where('user_id', auth()->id())
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