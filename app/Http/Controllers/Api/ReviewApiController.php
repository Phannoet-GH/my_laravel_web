<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewApiController extends Controller
{
    /**
     * List reviews for a specific product.
     */
    public function index($productId): JsonResponse
    {
        $product = Product::find($productId);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        $reviews = Review::where('product_id', $productId)->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $reviews->items(),
            'pagination' => [
                'total' => $reviews->total(),
                'per_page' => $reviews->perPage(),
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
            ]
        ]);
    }

    /**
     * Store a new product review.
     */
    public function store(Request $request, $productId): JsonResponse
    {
        $product = Product::find($productId);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        $validated = $request->validate([
            'author_name' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'headline' => 'required|string|max:255',
            'comment' => 'required|string|max:2000',
        ]);

        $review = Review::create([
            'product_id' => $product->id,
            'author_name' => $validated['author_name'],
            'rating' => $validated['rating'],
            'headline' => $validated['headline'],
            'comment' => $validated['comment'],
        ]);

        // Recalculate average rating & review count on product
        $avgRating = Review::where('product_id', $product->id)->avg('rating');
        $reviewCount = Review::where('product_id', $product->id)->count();

        $product->update([
            'rating' => round($avgRating, 1),
            'review_count' => $reviewCount,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully',
            'data' => $review,
        ], 201);
    }
}
