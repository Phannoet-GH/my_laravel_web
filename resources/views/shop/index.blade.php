@extends('layouts.app')

@section('title', 'SE Shop - Product Catalog')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    <!-- Header Title -->
    <div class="mb-8">
        <span class="text-xs font-bold uppercase tracking-wider text-cyan-400">HARDWARE CATALOG</span>
        <h1 class="text-3xl font-extrabold text-white mt-1">
            @if($activeCategory)
                {{ $activeCategory->name }}
            @elseif(request('q'))
                Search Results for "<span class="text-cyan-400">{{ request('q') }}</span>"
            @else
                Explore All Products
            @endif
        </h1>
        <p class="text-sm text-gray-400 mt-1">Browse {{ $products->total() }} premium tech items engineered for developer performance.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Left Filter Sidebar -->
        <aside class="space-y-6">
            
            <!-- Category Filter Card -->
            <div class="glass-panel p-6 border-gray-800 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-800 pb-3">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">Categories</h3>
                    @if(request('category'))
                        <a href="{{ route('shop.index') }}" class="text-[11px] text-rose-400 hover:underline">Reset</a>
                    @endif
                </div>

                <div class="space-y-1">
                    <a href="{{ route('shop.index', array_merge(request()->except('category', 'page'))) }}" 
                       class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-semibold transition-colors {{ !request('category') ? 'bg-blue-600 text-white font-bold' : 'text-gray-300 hover:bg-gray-800' }}">
                        <span>All Categories</span>
                        <span class="text-[10px] opacity-80">{{ $categories->sum('products_count') }}</span>
                    </a>

                    @foreach($categories as $category)
                        <a href="{{ route('shop.index', array_merge(request()->except('page'), ['category' => $category->slug])) }}" 
                           class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-semibold transition-colors {{ request('category') == $category->slug ? 'bg-blue-600 text-white font-bold' : 'text-gray-300 hover:bg-gray-800' }}">
                            <span class="flex items-center gap-2">
                                <i class="bi bi-{{ $category->icon }} text-cyan-400"></i>
                                {{ $category->name }}
                            </span>
                            <span class="text-[10px] px-2 py-0.5 rounded bg-gray-900 text-gray-400 font-mono">{{ $category->products_count }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Price Filter Card -->
            <div class="glass-panel p-6 border-gray-800 space-y-4">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider border-b border-gray-800 pb-3">Price Range</h3>
                <form action="{{ route('shop.index') }}" method="GET" class="space-y-4">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    @if(request('q'))
                        <input type="hidden" name="q" value="{{ request('q') }}">
                    @endif
                    @if(request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-[10px] text-gray-400 font-bold uppercase block mb-1">Min ($)</label>
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="0" class="input-dark w-full text-xs py-2">
                        </div>
                        <div>
                            <label class="text-[10px] text-gray-400 font-bold uppercase block mb-1">Max ($)</label>
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="5000" class="input-dark w-full text-xs py-2">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-2.5 rounded-lg bg-gray-800 hover:bg-gray-700 text-xs font-bold text-white transition-colors border border-gray-700">
                        Apply Price Filter
                    </button>
                </form>
            </div>

        </aside>

        <!-- Right Product Grid Area -->
        <main class="lg:col-span-3 space-y-6">
            
            <!-- Controls Bar: Sort Dropdown & Active Tags -->
            <div class="glass-panel p-4 border-gray-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-gray-400">
                    Showing <span class="font-bold text-white">{{ $products->firstItem() ?? 0 }}</span> to <span class="font-bold text-white">{{ $products->lastItem() ?? 0 }}</span> of <span class="font-bold text-white">{{ $products->total() }}</span> items
                </div>

                <form action="{{ route('shop.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                    @foreach(request()->except('sort', 'page') as $key => $val)
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endforeach

                    <label class="text-xs text-gray-400 whitespace-nowrap font-semibold">Sort By:</label>
                    <select name="sort" onchange="this.form.submit()" class="input-dark text-xs py-1.5 pr-8 bg-gray-900">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest Arrivals</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rating</option>
                    </select>
                </form>
            </div>

            <!-- Product Items Grid -->
            @if($products->isEmpty())
                <div class="glass-panel p-16 text-center space-y-4 border-gray-800">
                    <i class="bi bi-search text-5xl text-gray-600 block"></i>
                    <h3 class="text-lg font-bold text-white">No products found</h3>
                    <p class="text-xs text-gray-400 max-w-md mx-auto">We couldn't find any items matching your selected criteria. Try resetting your search query or filters.</p>
                    <a href="{{ route('shop.index') }}" class="inline-block px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-xs font-bold text-white transition-colors">
                        Clear All Filters
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($products as $product)
                        <div class="glass-panel p-5 glass-panel-hover flex flex-col justify-between border-gray-800 relative group">
                            
                            <!-- Badges -->
                            <div class="absolute top-4 left-4 z-10 flex flex-col gap-1">
                                @if($product->is_featured)
                                    <span class="badge-glow-cyan text-[10px] font-extrabold px-2.5 py-0.5 rounded-full">FEATURED</span>
                                @endif
                                @if($product->discount_percent > 0)
                                    <span class="bg-rose-500 text-white text-[10px] font-extrabold px-2.5 py-0.5 rounded-full">
                                        -{{ $product->discount_percent }}%
                                    </span>
                                @endif
                            </div>

                            <div>
                                <div class="relative overflow-hidden rounded-xl bg-gray-900 mb-4 h-48">
                                    <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                </div>

                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="font-bold text-cyan-400 uppercase text-[10px] tracking-wider">{{ $product->category->name ?? 'Hardware' }}</span>
                                    <div class="flex items-center gap-1 text-amber-400">
                                        <i class="bi bi-star-fill text-[10px]"></i>
                                        <span class="font-bold text-gray-300 text-[11px]">{{ number_format($product->rating, 1) }}</span>
                                    </div>
                                </div>

                                <h3 class="text-base font-bold text-white hover:text-cyan-300 transition-colors line-clamp-1">
                                    <a href="{{ route('shop.show', $product->slug) }}">{{ $product->name }}</a>
                                </h3>
                                <p class="text-xs text-gray-400 line-clamp-2 mt-1 leading-relaxed">{{ $product->tagline }}</p>
                            </div>

                            <div class="mt-6 pt-4 border-t border-gray-800 flex items-center justify-between">
                                <div>
                                    @if($product->sale_price)
                                        <span class="text-xs text-gray-500 block line-through">${{ number_format($product->price, 2) }}</span>
                                    @endif
                                    <span class="text-lg font-black text-white">${{ number_format($product->active_price, 2) }}</span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <a href="{{ route('shop.show', $product->slug) }}" class="p-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-300 transition-colors text-xs" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->check() && auth()->user()->isAdmin())
                                        <a href="{{ route('admin.dashboard') }}" class="p-2.5 rounded-xl bg-indigo-950 hover:bg-indigo-900 border border-indigo-500/30 text-indigo-300 transition-colors text-xs" title="Manage in Admin Dashboard">
                                            <i class="bi bi-speedometer2"></i>
                                        </a>
                                    @else
                                        <form action="{{ route('cart.add') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <button type="submit" class="px-3.5 py-2.5 rounded-xl cyber-glow-btn text-xs font-bold flex items-center gap-1">
                                                <i class="bi bi-cart-plus"></i> Add
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>

                <!-- Pagination Links -->
                <div class="pt-6">
                    {{ $products->links() }}
                </div>
            @endif

        </main>

    </div>
</div>
@endsection
