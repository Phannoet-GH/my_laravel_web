<?php $__env->startSection('title', 'SE Shop - Product Catalog'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    <!-- Header Title -->
    <div class="mb-8">
        <span class="text-xs font-bold uppercase tracking-wider text-cyan-400">HARDWARE CATALOG</span>
        <h1 class="text-3xl font-extrabold text-white mt-1">
            <?php if($activeCategory): ?>
                <?php echo e($activeCategory->name); ?>

            <?php elseif(request('q')): ?>
                Search Results for "<span class="text-cyan-400"><?php echo e(request('q')); ?></span>"
            <?php else: ?>
                Explore All Products
            <?php endif; ?>
        </h1>
        <p class="text-sm text-gray-400 mt-1">Browse <?php echo e($products->total()); ?> premium tech items engineered for developer performance.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Left Filter Sidebar -->
        <aside class="space-y-6">
            
            <!-- Category Filter Card -->
            <div class="glass-panel p-6 border-gray-800 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-800 pb-3">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">Categories</h3>
                    <?php if(request('category')): ?>
                        <a href="<?php echo e(route('shop.index')); ?>" class="text-[11px] text-rose-400 hover:underline">Reset</a>
                    <?php endif; ?>
                </div>

                <div class="space-y-1">
                    <a href="<?php echo e(route('shop.index', array_merge(request()->except('category', 'page')))); ?>" 
                       class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-semibold transition-colors <?php echo e(!request('category') ? 'bg-blue-600 text-white font-bold' : 'text-gray-300 hover:bg-gray-800'); ?>">
                        <span>All Categories</span>
                        <span class="text-[10px] opacity-80"><?php echo e($categories->sum('products_count')); ?></span>
                    </a>

                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('shop.index', array_merge(request()->except('page'), ['category' => $category->slug]))); ?>" 
                           class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-semibold transition-colors <?php echo e(request('category') == $category->slug ? 'bg-blue-600 text-white font-bold' : 'text-gray-300 hover:bg-gray-800'); ?>">
                            <span class="flex items-center gap-2">
                                <i class="bi bi-<?php echo e($category->icon); ?> text-cyan-400"></i>
                                <?php echo e($category->name); ?>

                            </span>
                            <span class="text-[10px] px-2 py-0.5 rounded bg-gray-900 text-gray-400 font-mono"><?php echo e($category->products_count); ?></span>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <!-- Price Filter Card -->
            <div class="glass-panel p-6 border-gray-800 space-y-4">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider border-b border-gray-800 pb-3">Price Range</h3>
                <form action="<?php echo e(route('shop.index')); ?>" method="GET" class="space-y-4">
                    <?php if(request('category')): ?>
                        <input type="hidden" name="category" value="<?php echo e(request('category')); ?>">
                    <?php endif; ?>
                    <?php if(request('q')): ?>
                        <input type="hidden" name="q" value="<?php echo e(request('q')); ?>">
                    <?php endif; ?>
                    <?php if(request('sort')): ?>
                        <input type="hidden" name="sort" value="<?php echo e(request('sort')); ?>">
                    <?php endif; ?>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-[10px] text-gray-400 font-bold uppercase block mb-1">Min ($)</label>
                            <input type="number" name="min_price" value="<?php echo e(request('min_price')); ?>" placeholder="0" class="input-dark w-full text-xs py-2">
                        </div>
                        <div>
                            <label class="text-[10px] text-gray-400 font-bold uppercase block mb-1">Max ($)</label>
                            <input type="number" name="max_price" value="<?php echo e(request('max_price')); ?>" placeholder="5000" class="input-dark w-full text-xs py-2">
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
                    Showing <span class="font-bold text-white"><?php echo e($products->firstItem() ?? 0); ?></span> to <span class="font-bold text-white"><?php echo e($products->lastItem() ?? 0); ?></span> of <span class="font-bold text-white"><?php echo e($products->total()); ?></span> items
                </div>

                <form action="<?php echo e(route('shop.index')); ?>" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                    <?php $__currentLoopData = request()->except('sort', 'page'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($val); ?>">
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <label class="text-xs text-gray-400 whitespace-nowrap font-semibold">Sort By:</label>
                    <select name="sort" onchange="this.form.submit()" class="input-dark text-xs py-1.5 pr-8 bg-gray-900">
                        <option value="newest" <?php echo e(request('sort') == 'newest' ? 'selected' : ''); ?>>Newest Arrivals</option>
                        <option value="price_low" <?php echo e(request('sort') == 'price_low' ? 'selected' : ''); ?>>Price: Low to High</option>
                        <option value="price_high" <?php echo e(request('sort') == 'price_high' ? 'selected' : ''); ?>>Price: High to Low</option>
                        <option value="rating" <?php echo e(request('sort') == 'rating' ? 'selected' : ''); ?>>Highest Rating</option>
                    </select>
                </form>
            </div>

            <!-- Product Items Grid -->
            <?php if($products->isEmpty()): ?>
                <div class="glass-panel p-16 text-center space-y-4 border-gray-800">
                    <i class="bi bi-search text-5xl text-gray-600 block"></i>
                    <h3 class="text-lg font-bold text-white">No products found</h3>
                    <p class="text-xs text-gray-400 max-w-md mx-auto">We couldn't find any items matching your selected criteria. Try resetting your search query or filters.</p>
                    <a href="<?php echo e(route('shop.index')); ?>" class="inline-block px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-xs font-bold text-white transition-colors">
                        Clear All Filters
                    </a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="glass-panel p-5 glass-panel-hover flex flex-col justify-between border-gray-800 relative group">
                            
                            <!-- Badges -->
                            <div class="absolute top-4 left-4 z-10 flex flex-col gap-1">
                                <?php if($product->is_featured): ?>
                                    <span class="badge-glow-cyan text-[10px] font-extrabold px-2.5 py-0.5 rounded-full">FEATURED</span>
                                <?php endif; ?>
                                <?php if($product->discount_percent > 0): ?>
                                    <span class="bg-rose-500 text-white text-[10px] font-extrabold px-2.5 py-0.5 rounded-full">
                                        -<?php echo e($product->discount_percent); ?>%
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div>
                                <div class="relative overflow-hidden rounded-xl bg-gray-900 mb-4 h-48">
                                    <img src="<?php echo e($product->image); ?>" alt="<?php echo e($product->name); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                </div>

                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="font-bold text-cyan-400 uppercase text-[10px] tracking-wider"><?php echo e($product->category->name ?? 'Hardware'); ?></span>
                                    <div class="flex items-center gap-1 text-amber-400">
                                        <i class="bi bi-star-fill text-[10px]"></i>
                                        <span class="font-bold text-gray-300 text-[11px]"><?php echo e(number_format($product->rating, 1)); ?></span>
                                    </div>
                                </div>

                                <h3 class="text-base font-bold text-white hover:text-cyan-300 transition-colors line-clamp-1">
                                    <a href="<?php echo e(route('shop.show', $product->slug)); ?>"><?php echo e($product->name); ?></a>
                                </h3>
                                <p class="text-xs text-gray-400 line-clamp-2 mt-1 leading-relaxed"><?php echo e($product->tagline); ?></p>
                            </div>

                            <div class="mt-6 pt-4 border-t border-gray-800 flex items-center justify-between">
                                <div>
                                    <?php if($product->sale_price): ?>
                                        <span class="text-xs text-gray-500 block line-through">$<?php echo e(number_format($product->price, 2)); ?></span>
                                    <?php endif; ?>
                                    <span class="text-lg font-black text-white">$<?php echo e(number_format($product->active_price, 2)); ?></span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <a href="<?php echo e(route('shop.show', $product->slug)); ?>" class="p-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-300 transition-colors text-xs" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if(auth()->check() && auth()->user()->isAdmin()): ?>
                                        <a href="<?php echo e(route('admin.dashboard')); ?>" class="p-2.5 rounded-xl bg-indigo-950 hover:bg-indigo-900 border border-indigo-500/30 text-indigo-300 transition-colors text-xs" title="Manage in Admin Dashboard">
                                            <i class="bi bi-speedometer2"></i>
                                        </a>
                                    <?php else: ?>
                                        <form action="<?php echo e(route('cart.add')); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="product_id" value="<?php echo e($product->id); ?>">
                                            <button type="submit" class="px-3.5 py-2.5 rounded-xl cyber-glow-btn text-xs font-bold flex items-center gap-1">
                                                <i class="bi bi-cart-plus"></i> Add
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <!-- Pagination Links -->
                <div class="pt-6">
                    <?php echo e($products->links()); ?>

                </div>
            <?php endif; ?>

        </main>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\YEAR IV\SEMESTER I\WB-III\project\my-laravel-app\resources\views/shop/index.blade.php ENDPATH**/ ?>