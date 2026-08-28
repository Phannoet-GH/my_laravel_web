<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'tagline',
        'description',
        'price',
        'sale_price',
        'stock',
        'rating',
        'review_count',
        'is_featured',
        'is_trending',
        'image',
        'gallery',
        'specs'
    ];

    protected $casts = [
        'gallery' => 'array',
        'specs' => 'array',
        'is_featured' => 'boolean',
        'is_trending' => 'boolean',
        'price' => 'float',
        'sale_price' => 'float',
        'rating' => 'float',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function getActivePriceAttribute(): float
    {
        return $this->sale_price ?? $this->price;
    }

    public function getDiscountPercentAttribute(): int
    {
        if ($this->sale_price && $this->price > 0) {
            return (int) round((($this->price - $this->sale_price) / $this->price) * 100);
        }
        return 0;
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    public function hasSufficientStock(int $requestedQty): bool
    {
        return $this->stock >= $requestedQty;
    }

    public function isLowStock(): bool
    {
        return $this->stock > 0 && $this->stock <= 5;
    }
}
