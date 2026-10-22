<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with('category');

        // Search
        if ($request->filled('q')) {
            $searchTerm = \Illuminate\Support\Str::substr(trim((string) $request->q), 0, 100);
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('tagline', 'like', "%{$searchTerm}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $catSlug = \Illuminate\Support\Str::substr(trim((string) $request->category), 0, 100);
            $query->whereHas('category', function ($q) use ($catSlug) {
                $q->where('slug', $catSlug);
            });
        }

        // Price range
        if ($request->filled('min_price')) {
            $minPrice = max(0, min(1000000, (float) $request->min_price));
            $query->where('price', '>=', $minPrice);
        }
        if ($request->filled('max_price')) {
            $maxPrice = max(0, min(1000000, (float) $request->max_price));
            $query->where('price', '<=', $maxPrice);
        }

        // Sorting
        switch ($request->get('sort', 'newest')) {
            case 'price_low':
                $query->orderByRaw('COALESCE(sale_price, price) ASC');
                break;
            case 'price_high':
                $query->orderByRaw('COALESCE(sale_price, price) DESC');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(9)->withQueryString();
        $categories = Category::withCount('products')->get();
        $activeCategory = $request->filled('category') 
            ? Category::where('slug', $request->category)->first() 
            : null;

        return view('shop.index', compact('products', 'categories', 'activeCategory'));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)->with(['category', 'reviews'])->firstOrFail();
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('shop.show', compact('product', 'relatedProducts'));
    }

    public function storeReview(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'author_name' => 'required|string|max:100',
            'rating' => 'required|integer|min:1|max:5',
            'headline' => 'required|string|max:150',
            'comment' => 'required|string|max:2000',
        ]);

        $product->reviews()->create([
            'author_name' => strip_tags(trim($validated['author_name'])),
            'rating' => $validated['rating'],
            'headline' => strip_tags(trim($validated['headline'])),
            'comment' => strip_tags(trim($validated['comment'])),
        ]);

        // Update product average rating
        $avgRating = $product->reviews()->avg('rating');
        $product->update([
            'rating' => round($avgRating, 2),
            'review_count' => $product->reviews()->count()
        ]);

        return back()->with('success', 'Thank you! Your review has been submitted.');
    }
}
