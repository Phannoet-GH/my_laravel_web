@extends('layouts.app')

@section('title', 'SE Shop - Next-Gen Tech & Developer Hardware')

@section('content')

<!-- Hero Section -->
<section class="relative overflow-hidden pt-12 pb-24 lg:pt-20 lg:pb-32">
    <!-- Ambient Background Glows -->
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-blue-600/15 rounded-full blur-[140px] pointer-events-none"></div>
    <div class="absolute top-1/3 right-10 w-[400px] h-[400px] bg-cyan-500/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left Hero Content -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-950/80 border border-blue-500/30 text-cyan-400 text-xs font-bold uppercase tracking-wider shadow-inner">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                    Engineered For Developers & Engineers
                </div>
                
                <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight leading-[1.1] text-white">
                    Equip Your Mind With <br>
                    <span class="cyber-gradient-text">Next-Gen Gear</span>
                </h1>

                <p class="text-base sm:text-lg text-gray-300 max-w-2xl leading-relaxed">
                    Discover precision-built workstation laptops, hot-swappable mechanical keyboards, noise-canceling studio headsets, and high-DPI ergonomic peripherals crafted for extreme productivity.
                </p>

                <!-- Action CTAs -->
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                    <a href="{{ route('shop.index') }}" class="w-full sm:w-auto px-8 py-4 rounded-xl cyber-glow-btn text-base font-bold flex items-center justify-center gap-2">
                        Explore Catalog <i class="bi bi-arrow-right"></i>
                    </a>
                    <a href="{{ route('shop.index', ['category' => 'laptops-workstations']) }}" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-gray-900/90 hover:bg-gray-800 text-gray-200 border border-gray-700/80 text-base font-semibold transition-all flex items-center justify-center gap-2">
                        <i class="bi bi-laptop text-cyan-400"></i> Browse Laptops
                    </a>
                </div>

                <!-- Trust Stats Bar -->
                <div class="pt-8 border-t border-gray-800/80 grid grid-cols-3 gap-6 max-w-lg mx-auto lg:mx-0">
                    <div>
                        <span class="block text-2xl font-black text-white">15K+</span>
                        <span class="text-xs text-gray-400">Engineers Served</span>
                    </div>
                    <div>
                        <span class="block text-2xl font-black text-cyan-400">4.9/5</span>
                        <span class="text-xs text-gray-400">Average Rating</span>
                    </div>
                    <div>
                        <span class="block text-2xl font-black text-emerald-400">24 Hours</span>
                        <span class="text-xs text-gray-400">Express Shipping</span>
                    </div>
                </div>
            </div>

            <!-- Right Hero Interactive Product Preview -->
            <div class="lg:col-span-5 relative">
                <div class="glass-panel p-6 border-gray-800 shadow-2xl relative overflow-hidden group">
                    <div class="absolute -top-12 -right-12 w-40 h-40 bg-cyan-500/20 rounded-full blur-2xl"></div>
                    
                    <span class="absolute top-4 right-4 z-10 badge-glow-cyan px-3 py-1 rounded-full text-xs font-bold">
                        FEATURED DECK
                    </span>

                    @if($featuredProducts->first())
                        @php $heroProduct = $featuredProducts->first(); @endphp
                        <img src="{{ $heroProduct->image }}" alt="{{ $heroProduct->name }}" class="w-full h-72 object-cover rounded-xl bg-gray-900 transform group-hover:scale-105 transition-transform duration-500">
                        
                        <div class="mt-6 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-cyan-400 font-bold uppercase tracking-wider">{{ $heroProduct->category->name ?? 'Hardware' }}</span>
                                <div class="flex items-center gap-1 text-amber-400 text-xs">
                                    <i class="bi bi-star-fill"></i>
                                    <span class="font-bold text-gray-200">{{ $heroProduct->rating }}</span>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-white">{{ $heroProduct->name }}</h3>
                            <p class="text-xs text-gray-400 line-clamp-2">{{ $heroProduct->tagline }}</p>

                            <div class="flex items-center justify-between pt-4 border-t border-gray-800">
                                <div>
                                    <span class="text-xs text-gray-500 block line-through">${{ number_format($heroProduct->price, 2) }}</span>
                                    <span class="text-2xl font-extrabold text-white">${{ number_format($heroProduct->active_price, 2) }}</span>
                                </div>
                                
                                @if(auth()->check() && auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-xl bg-indigo-950 hover:bg-indigo-900 border border-indigo-500/40 text-indigo-300 text-xs font-bold flex items-center gap-1.5 transition-colors">
                                        <i class="bi bi-speedometer2"></i> Admin Panel
                                    </a>
                                @else
                                    <form action="{{ route('cart.add') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $heroProduct->id }}">
                                        <button type="submit" class="px-5 py-2.5 rounded-xl cyber-glow-btn text-xs font-bold flex items-center gap-2">
                                            <i class="bi bi-cart-plus"></i> Add to Cart
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Guarantees Banner -->
<section class="border-y border-gray-800/80 bg-gray-950/60 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-950/80 border border-blue-500/30 flex items-center justify-center text-cyan-400 text-2xl">
                    <i class="bi bi-truck"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-white">Free Express Shipping</h4>
                    <p class="text-xs text-gray-400">On all qualifying orders over $500</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-950/80 border border-blue-500/30 flex items-center justify-center text-cyan-400 text-2xl">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-white">2-Year Full Warranty</h4>
                    <p class="text-xs text-gray-400">Zero hassle hardware replacement</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-950/80 border border-blue-500/30 flex items-center justify-center text-cyan-400 text-2xl">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-white">30-Day Money Back</h4>
                    <p class="text-xs text-gray-400">Full refund if you are not satisfied</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-950/80 border border-blue-500/30 flex items-center justify-center text-cyan-400 text-2xl">
                    <i class="bi bi-headset"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-white">24/7 Tech Support</h4>
                    <p class="text-xs text-gray-400">Direct line to hardware engineers</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Grid -->
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-cyan-400">CURATED CATEGORIES</span>
                <h2 class="text-3xl font-extrabold text-white mt-1">Explore By Setup Category</h2>
            </div>
            <a href="{{ route('shop.index') }}" class="text-sm font-semibold text-cyan-400 hover:text-cyan-300 mt-4 md:mt-0 flex items-center gap-1">
                View All Categories <i class="bi bi-chevron-right"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
            @foreach($categories as $category)
                <a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="glass-panel p-6 glass-panel-hover group flex flex-col justify-between h-48 border-gray-800/80">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-blue-950/80 border border-blue-500/30 flex items-center justify-center text-cyan-400 text-xl group-hover:scale-110 transition-transform">
                            <i class="bi bi-{{ $category->icon }}"></i>
                        </div>
                        <span class="text-[10px] font-extrabold px-2 py-0.5 rounded bg-gray-800 text-gray-300 uppercase">{{ $category->badge ?? 'GEAR' }}</span>
                    </div>

                    <div>
                        <h3 class="text-base font-bold text-white group-hover:text-cyan-400 transition-colors">{{ $category->name }}</h3>
                        <p class="text-xs text-gray-400 mt-1">{{ $category->products_count }} Products Available</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Trending Products Showcase -->
<section class="py-16 bg-gray-950/40 border-y border-gray-800/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">HOT DEALS</span>
                <h2 class="text-3xl font-extrabold text-white mt-1">Trending Developer Products</h2>
            </div>
            <a href="{{ route('shop.index') }}" class="text-sm font-semibold text-cyan-400 hover:text-cyan-300 mt-4 md:mt-0 flex items-center gap-1">
                Browse All Deals <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($trendingProducts as $product)
                <div class="glass-panel p-5 glass-panel-hover flex flex-col justify-between border-gray-800 relative group">
                    @if($product->discount_percent > 0)
                        <span class="absolute top-4 right-4 z-10 bg-rose-500 text-white font-extrabold text-[10px] px-2.5 py-1 rounded-full shadow-lg">
                            SAVE {{ $product->discount_percent }}%
                        </span>
                    @endif

                    <div>
                        <div class="relative overflow-hidden rounded-xl bg-gray-900 mb-4 h-48">
                            <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>

                        <span class="text-[11px] font-bold text-cyan-400 uppercase tracking-wider">{{ $product->category->name ?? 'Gear' }}</span>
                        <h3 class="text-base font-bold text-white mt-1 hover:text-cyan-300 transition-colors">
                            <a href="{{ route('shop.show', $product->slug) }}">{{ $product->name }}</a>
                        </h3>
                        <p class="text-xs text-gray-400 line-clamp-2 mt-1">{{ $product->tagline }}</p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-800 flex items-center justify-between">
                        <div>
                            @if($product->sale_price)
                                <span class="text-xs text-gray-500 block line-through">${{ number_format($product->price, 2) }}</span>
                            @endif
                            <span class="text-lg font-black text-white">${{ number_format($product->active_price, 2) }}</span>
                        </div>

                        @if(auth()->check() && auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="w-10 h-10 rounded-xl bg-indigo-950 hover:bg-indigo-900 border border-indigo-500/30 text-indigo-300 flex items-center justify-center transition-colors" title="Manage in Admin Dashboard">
                                <i class="bi bi-speedometer2"></i>
                            </a>
                        @else
                            <form action="{{ route('cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <button type="submit" class="w-10 h-10 rounded-xl bg-blue-600 hover:bg-blue-500 text-white flex items-center justify-center transition-colors shadow-md">
                                    <i class="bi bi-plus-lg text-lg"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="text-xs font-bold uppercase tracking-wider text-cyan-400">COMMUNITY REVIEWS</span>
            <h2 class="text-3xl font-extrabold text-white mt-1">Loved By Software Engineers World-Wide</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="glass-panel p-6 border-gray-800 space-y-4">
                <div class="flex items-center gap-1 text-amber-400 text-sm">
                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <p class="text-xs text-gray-300 leading-relaxed italic">
                    "The SE ProBook Cyber X laptop completely changed my compilation speeds. My Docker containers launch in seconds and the battery easily lasts through 12 hours of uninterrupted coding."
                </p>
                <div class="flex items-center gap-3 pt-2">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-cyan-500 to-blue-600 flex items-center justify-center font-bold text-white text-xs">
                        AR
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-white">Alex Rivera</h4>
                        <span class="text-[10px] text-gray-400">Staff Architect @ TechCorp</span>
                    </div>
                </div>
            </div>

            <div class="glass-panel p-6 border-gray-800 space-y-4">
                <div class="flex items-center gap-1 text-amber-400 text-sm">
                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <p class="text-xs text-gray-300 leading-relaxed italic">
                    "The CyberTactile 75% wireless mechanical keyboard is the best typing experience I have ever had. The lube job on the switches is buttery smooth right out of the box."
                </p>
                <div class="flex items-center gap-3 pt-2">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-purple-500 to-indigo-600 flex items-center justify-center font-bold text-white text-xs">
                        SL
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-white">Samantha Lin</h4>
                        <span class="text-[10px] text-gray-400">Full-Stack Developer</span>
                    </div>
                </div>
            </div>

            <div class="glass-panel p-6 border-gray-800 space-y-4">
                <div class="flex items-center gap-1 text-amber-400 text-sm">
                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <p class="text-xs text-gray-300 leading-relaxed italic">
                    "Fast shipping, premium packaging, and zero hassle support. SE Shop is now my official go-to store whenever I need to upgrade my home office workstation setup."
                </p>
                <div class="flex items-center gap-3 pt-2">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-emerald-500 to-teal-600 flex items-center justify-center font-bold text-white text-xs">
                        DK
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-white">David Kim</h4>
                        <span class="text-[10px] text-gray-400">DevOps Engineer</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
