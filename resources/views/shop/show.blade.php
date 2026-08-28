@extends('layouts.app')

@section('title', $product->name . ' - SE Shop')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    <!-- Breadcrumb Nav -->
    <nav class="flex items-center gap-2 text-xs text-gray-400 mb-8">
        <a href="{{ route('home') }}" class="hover:text-white">Home</a>
        <i class="bi bi-chevron-right text-[10px]"></i>
        <a href="{{ route('shop.index') }}" class="hover:text-white">Catalog</a>
        <i class="bi bi-chevron-right text-[10px]"></i>
        <a href="{{ route('shop.index', ['category' => $product->category->slug ?? '']) }}" class="hover:text-white">{{ $product->category->name ?? 'Category' }}</a>
        <i class="bi bi-chevron-right text-[10px]"></i>
        <span class="text-gray-200 font-bold truncate max-w-xs">{{ $product->name }}</span>
    </nav>

    <!-- Main Product Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 mb-16">
        
        <!-- Left Image Gallery Column (5 cols) -->
        <div class="lg:col-span-6 space-y-4">
            <div class="glass-panel p-4 border-gray-800 relative overflow-hidden group rounded-2xl">
                <img id="main-product-img" src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-[420px] object-cover rounded-xl bg-gray-900 transition-all duration-300">
                @if($product->discount_percent > 0)
                    <span class="absolute top-6 right-6 bg-rose-500 text-white font-extrabold text-xs px-3 py-1 rounded-full shadow-lg">
                        SAVE {{ $product->discount_percent }}%
                    </span>
                @endif
            </div>

            <!-- Thumbnail Gallery Bar -->
            @if($product->gallery && count($product->gallery) > 0)
                <div class="flex items-center gap-3 overflow-x-auto pb-2">
                    <button type="button" onclick="swapMainImage('{{ $product->image }}')" class="w-20 h-20 rounded-xl overflow-hidden border-2 border-cyan-500 focus:outline-none flex-shrink-0 bg-gray-900">
                        <img src="{{ $product->image }}" class="w-full h-full object-cover">
                    </button>
                    @foreach($product->gallery as $imgUrl)
                        <button type="button" onclick="swapMainImage('{{ $imgUrl }}')" class="w-20 h-20 rounded-xl overflow-hidden border border-gray-800 hover:border-cyan-500 focus:outline-none flex-shrink-0 bg-gray-900 transition-colors">
                            <img src="{{ $imgUrl }}" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Right Specs & Buy Action Column (6 cols) -->
        <div class="lg:col-span-6 space-y-6">
            <div>
                <span class="text-xs font-bold text-cyan-400 uppercase tracking-widest">{{ $product->category->name ?? 'Hardware' }}</span>
                <h1 class="text-3xl font-extrabold text-white mt-1">{{ $product->name }}</h1>
                <p class="text-sm text-gray-300 mt-2 leading-relaxed">{{ $product->tagline }}</p>
            </div>

            <!-- Rating & Stock Status Bar -->
            <div class="flex items-center gap-6 py-3 border-y border-gray-800/80 text-xs">
                <div class="flex items-center gap-2">
                    <div class="flex items-center text-amber-400">
                        @for($i=1; $i<=5; $i++)
                            <i class="bi bi-star{{ $i <= round($product->rating) ? '-fill' : '' }}"></i>
                        @endfor
                    </div>
                    <span class="font-bold text-white">{{ number_format($product->rating, 2) }}</span>
                    <span class="text-gray-400">({{ $product->review_count }} reviews)</span>
                </div>

                <div class="flex items-center gap-2">
                    @if($product->stock > 0)
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        <span class="text-emerald-400 font-bold">In Stock ({{ $product->stock }} units ready)</span>
                    @else
                        <span class="text-rose-400 font-bold">Out of Stock</span>
                    @endif
                </div>
            </div>

            <!-- Pricing Card -->
            <div class="glass-panel p-6 border-gray-800 space-y-4">
                <div class="flex items-baseline gap-3">
                    <span class="text-3xl font-black text-white">${{ number_format($product->active_price, 2) }}</span>
                    @if($product->sale_price)
                        <span class="text-sm text-gray-500 line-through">${{ number_format($product->price, 2) }}</span>
                        <span class="text-xs font-bold text-emerald-400">Save ${{ number_format($product->price - $product->sale_price, 2) }}</span>
                    @endif
                </div>

                @if(auth()->check() && auth()->user()->isAdmin())
                    <div class="p-4 rounded-xl bg-indigo-950/80 border border-indigo-500/40 text-indigo-300 text-xs space-y-2">
                        <div class="flex items-center gap-2 font-bold">
                            <i class="bi bi-shield-lock-fill text-indigo-400"></i> Admin View Mode
                        </div>
                        <p class="text-gray-300">Admin accounts manage inventory and fulfillment. To purchase products, please sign in with a Customer account.</p>
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition-colors shadow-md">
                            <i class="bi bi-speedometer2"></i> Manage in Admin Dashboard
                        </a>
                    </div>
                @else
                    <!-- Add to Cart Form -->
                    <form action="{{ route('cart.add') }}" method="POST" class="space-y-4 pt-2">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        
                        <div class="flex items-center gap-4">
                            <label class="text-xs text-gray-300 font-bold uppercase">Quantity:</label>
                            <div class="flex items-center bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
                                <button type="button" onclick="decrementQty()" class="px-3 py-2 text-gray-300 hover:bg-gray-800 text-sm font-bold">-</button>
                                <input type="number" id="qty-input" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="w-12 bg-transparent text-center text-xs font-bold text-white focus:outline-none">
                                <button type="button" onclick="incrementQty({{ $product->stock }})" class="px-3 py-2 text-gray-300 hover:bg-gray-800 text-sm font-bold">+</button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                            <button type="submit" class="w-full py-3.5 rounded-xl cyber-glow-btn text-sm font-bold flex items-center justify-center gap-2">
                                <i class="bi bi-bag-plus-fill"></i> Add to Cart
                            </button>
                            <a href="{{ route('checkout.index') }}" class="w-full py-3.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-gray-950 font-extrabold text-sm flex items-center justify-center gap-2 transition-colors">
                                Instant Checkout
                            </a>
                        </div>
                    </form>
                @endif
            </div>

            <!-- Product Overview Description -->
            <div class="space-y-2">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Product Overview</h3>
                <p class="text-xs text-gray-300 leading-relaxed">{{ $product->description }}</p>
            </div>

        </div>
    </div>

    <!-- Technical Specs Table & Reviews Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 mb-16">
        
        <!-- Tech Specs Column (6 cols) -->
        <div class="lg:col-span-6 space-y-6">
            <div class="glass-panel p-6 border-gray-800 space-y-4">
                <div class="flex items-center gap-2 border-b border-gray-800 pb-3">
                    <i class="bi bi-cpu text-cyan-400 text-lg"></i>
                    <h3 class="text-base font-bold text-white">Technical Specifications</h3>
                </div>

                @if($product->specs && count($product->specs) > 0)
                    <div class="divide-y divide-gray-800/80 text-xs">
                        @foreach($product->specs as $key => $val)
                            <div class="py-3 flex justify-between gap-4">
                                <span class="text-gray-400 font-semibold">{{ $key }}</span>
                                <span class="text-gray-200 font-bold text-right">{{ $val }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-gray-400">Standard enterprise grade hardware specifications apply.</p>
                @endif
            </div>
        </div>

        <!-- Customer Reviews Column (6 cols) -->
        <div class="lg:col-span-6 space-y-6">
            <div class="glass-panel p-6 border-gray-800 space-y-6">
                <div class="flex items-center justify-between border-b border-gray-800 pb-3">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-chat-left-quote text-cyan-400 text-lg"></i>
                        <h3 class="text-base font-bold text-white">Customer Reviews</h3>
                    </div>
                    <span class="text-xs font-bold text-gray-400">{{ $product->reviews->count() }} Reviews</span>
                </div>

                <!-- Review Submission Form -->
                <form action="{{ route('products.review', $product->id) }}" method="POST" class="bg-gray-900/80 p-4 rounded-xl border border-gray-800 space-y-3">
                    @csrf
                    <h4 class="text-xs font-bold text-white">Write a Review</h4>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" name="author_name" placeholder="Your Name or Handle" required class="input-dark text-xs py-2">
                        <select name="rating" required class="input-dark text-xs py-2 bg-gray-900">
                            <option value="5">⭐⭐⭐⭐⭐ (5/5)</option>
                            <option value="4">⭐⭐⭐⭐ (4/5)</option>
                            <option value="3">⭐⭐⭐ (3/5)</option>
                            <option value="2">⭐⭐ (2/5)</option>
                            <option value="1">⭐ (1/5)</option>
                        </select>
                    </div>
                    <input type="text" name="headline" placeholder="Headline / Summary" required class="input-dark text-xs py-2 w-full">
                    <textarea name="comment" rows="2" placeholder="Your feedback..." required class="input-dark text-xs w-full"></textarea>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition-colors">
                        Submit Review
                    </button>
                </form>

                <!-- Reviews List -->
                <div class="space-y-4 max-h-80 overflow-y-auto pr-2">
                    @forelse($product->reviews as $review)
                        <div class="p-4 rounded-xl bg-gray-900/50 border border-gray-800/80 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-white">{{ $review->author_name }}</span>
                                <div class="flex text-amber-400 text-[10px]">
                                    @for($r=1; $r<=5; $r++)
                                        <i class="bi bi-star{{ $r <= $review->rating ? '-fill' : '' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <h5 class="text-xs font-semibold text-cyan-300">{{ $review->headline }}</h5>
                            <p class="text-[11px] text-gray-300 leading-relaxed">{{ $review->comment }}</p>
                            <span class="text-[9px] text-gray-500 block">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500 text-center py-4">No reviews submitted yet. Be the first!</p>
                    @endforelse
                </div>

            </div>
        </div>

    </div>

    <!-- Related Products -->
    @if(count($relatedProducts) > 0)
        <div class="pt-8 border-t border-gray-800">
            <h2 class="text-xl font-bold text-white mb-6">You Might Also Like</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $rel)
                    <div class="glass-panel p-4 glass-panel-hover border-gray-800 flex flex-col justify-between">
                        <div>
                            <img src="{{ $rel->image }}" alt="{{ $rel->name }}" class="w-full h-40 object-cover rounded-xl bg-gray-900 mb-3">
                            <h4 class="text-xs font-bold text-white line-clamp-1 hover:text-cyan-400">
                                <a href="{{ route('shop.show', $rel->slug) }}">{{ $rel->name }}</a>
                            </h4>
                            <span class="text-xs font-extrabold text-cyan-400 mt-1 block">${{ number_format($rel->active_price, 2) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>

<script>
    function swapMainImage(url) {
        document.getElementById('main-product-img').src = url;
    }
    function incrementQty(max) {
        const input = document.getElementById('qty-input');
        if (parseInt(input.value) < max) input.value = parseInt(input.value) + 1;
    }
    function decrementQty() {
        const input = document.getElementById('qty-input');
        if (parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;
    }
</script>
@endsection
