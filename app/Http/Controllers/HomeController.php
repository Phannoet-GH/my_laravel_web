<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->get();
        $featuredProducts = Product::where('is_featured', true)->take(4)->get();
        $trendingProducts = Product::where('is_trending', true)->take(4)->get();
        $latestProducts = Product::latest()->take(6)->get();

        return view('home', compact(
            'categories',
            'featuredProducts',
            'trendingProducts',
            'latestProducts'
        ));
    }
}
