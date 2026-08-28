<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo $__env->yieldContent('title', 'SE Shop - Next-Gen Tech & Electronics'); ?></title>
    <meta name="description" content="SE Shop is the premier hardware and software engineering store for laptops, mechanical keyboards, audio tech, and smart peripherals.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Vite Assets -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>
</head>
<body class="min-h-screen flex flex-col bg-[#0b0f19] text-gray-100 antialiased selection:bg-blue-600 selection:text-white">

    <!-- Announcement Bar -->
    <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-purple-900 text-xs font-semibold py-2 px-4 text-center border-b border-blue-500/20 text-blue-200">
        <span class="inline-flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
            🚀 SPECIAL OFFER: Free express worldwide shipping on orders over $500! Code: <span class="text-white font-mono bg-blue-950/80 px-2 py-0.5 rounded border border-blue-500/40">SESHOP2026</span>
        </span>
    </div>

    <!-- Navigation Header -->
    <header class="sticky top-0 z-40 bg-[#0b0f19]/90 backdrop-blur-md border-b border-gray-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <!-- Logo -->
                <div class="flex items-center gap-8">
                    <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-400 flex items-center justify-center text-white font-black text-xl shadow-lg shadow-blue-500/30 group-hover:scale-105 transition-transform">
                            SE
                        </div>
                        <div>
                            <span class="text-2xl font-extrabold tracking-tight text-white group-hover:text-cyan-400 transition-colors">SE <span class="cyber-gradient-text">Shop</span></span>
                            <span class="block text-[10px] tracking-widest text-cyan-400/80 font-mono uppercase">Engineered Gear</span>
                        </div>
                    </a>

                    <!-- Nav Links -->
                    <nav class="hidden md:flex items-center gap-6 text-sm font-semibold">
                        <a href="<?php echo e(route('home')); ?>" class="text-gray-300 hover:text-cyan-400 transition-colors <?php echo e(request()->routeIs('home') ? 'text-cyan-400 font-bold' : ''); ?>">Home</a>
                        <a href="<?php echo e(route('shop.index')); ?>" class="text-gray-300 hover:text-cyan-400 transition-colors <?php echo e(request()->routeIs('shop.index') ? 'text-cyan-400 font-bold' : ''); ?>">Catalog</a>
                        <a href="<?php echo e(route('shop.index', ['category' => 'laptops-workstations'])); ?>" class="text-gray-300 hover:text-cyan-400 transition-colors">Laptops</a>
                        <a href="<?php echo e(route('shop.index', ['category' => 'smart-peripherals'])); ?>" class="text-gray-300 hover:text-cyan-400 transition-colors">Keyboards & Mice</a>
                        <a href="<?php echo e(route('orders.lookup')); ?>" class="text-gray-400 hover:text-gray-200 transition-colors">Track Order</a>
                    </nav>
                </div>

                <!-- Search Bar -->
                <div class="hidden lg:flex flex-1 max-w-md mx-8">
                    <form action="<?php echo e(route('shop.index')); ?>" method="GET" class="w-full relative">
                        <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="Search products, laptops, specs..." 
                            class="w-full bg-gray-900/90 border border-gray-800 rounded-full py-2.5 pl-11 pr-4 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all">
                        <i class="bi bi-search absolute left-4 top-3 text-gray-500 text-sm"></i>
                    </form>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-3">
                    
                    <?php if(auth()->guard()->check()): ?>
                        <?php if(auth()->user()->isAdmin()): ?>
                            <a href="<?php echo e(route('admin.dashboard')); ?>" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-950/80 hover:bg-indigo-900 text-xs font-bold text-indigo-300 border border-indigo-500/40 transition-all">
                                <i class="bi bi-speedometer2 text-cyan-400"></i> Admin Panel
                            </a>
                        <?php endif; ?>

                        <a href="<?php echo e(route('dashboard')); ?>" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-gray-900 hover:bg-gray-800 text-xs font-bold text-white border border-gray-800 transition-all">
                            <i class="bi bi-person-circle text-cyan-400"></i>
                            <span class="max-w-[100px] truncate"><?php echo e(auth()->user()->name); ?></span>
                            <span class="px-1.5 py-0.5 rounded text-[9px] uppercase font-mono <?php echo e(auth()->user()->isAdmin() ? 'bg-indigo-900 text-indigo-300 border border-indigo-500/30' : 'bg-cyan-900/60 text-cyan-300 border border-cyan-500/30'); ?>">
                                <?php echo e(auth()->user()->role); ?>

                            </span>
                        </a>

                        <form action="<?php echo e(route('logout')); ?>" method="POST" class="inline">
                            <?php echo csrf_field(); ?>
                            <button type="submit" title="Logout" class="p-2 rounded-xl bg-gray-900 border border-gray-800 text-gray-400 hover:text-rose-400 hover:border-rose-500/40 transition-all text-xs">
                                <i class="bi bi-box-arrow-right"></i>
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="px-3.5 py-1.5 text-xs font-bold rounded-xl bg-gray-900 border border-gray-700/80 text-cyan-400 hover:text-white hover:bg-gray-800 hover:border-cyan-500/50 shadow-md transition-all flex items-center gap-1.5">
                            <i class="bi bi-box-arrow-in-right"></i> Sign In
                        </a>
                        <a href="<?php echo e(route('register')); ?>" class="hidden sm:inline-flex px-3.5 py-1.5 text-xs font-bold rounded-xl bg-cyan-500 hover:bg-cyan-400 text-gray-950 shadow-md shadow-cyan-500/20 transition-all">
                            Register
                        </a>
                    <?php endif; ?>

                    <!-- Cart Drawer Trigger Button -->
                    <button type="button" id="cart-drawer-toggle" class="relative p-2.5 rounded-xl bg-gray-900 border border-gray-800 text-gray-300 hover:text-white hover:border-cyan-500/50 transition-all ml-1">
                        <i class="bi bi-bag-check text-xl"></i>
                        <?php
                            $cart = session()->get('cart', []);
                            $cartCount = array_sum(array_column($cart, 'quantity'));
                        ?>
                        <span id="cart-count-badge" class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-cyan-500 text-gray-950 font-extrabold text-xs flex items-center justify-center shadow-lg shadow-cyan-500/50 <?php echo e($cartCount > 0 ? '' : 'hidden'); ?>">
                            <?php echo e($cartCount); ?>

                        </span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <?php if(session('success')): ?>
            <div class="p-4 rounded-xl bg-emerald-950/80 border border-emerald-500/40 text-emerald-300 flex items-center justify-between text-sm shadow-lg mb-4">
                <div class="flex items-center gap-3">
                    <i class="bi bi-check-circle-fill text-emerald-400 text-lg"></i>
                    <span><?php echo e(session('success')); ?></span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white"><i class="bi bi-x-lg"></i></button>
            </div>
        <?php endif; ?>

        <?php if(session('warning')): ?>
            <div class="p-4 rounded-xl bg-amber-950/80 border border-amber-500/40 text-amber-300 flex items-center justify-between text-sm shadow-lg mb-4">
                <div class="flex items-center gap-3">
                    <i class="bi bi-exclamation-triangle-fill text-amber-400 text-lg"></i>
                    <span><?php echo e(session('warning')); ?></span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-amber-400 hover:text-white"><i class="bi bi-x-lg"></i></button>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="p-4 rounded-xl bg-rose-950/80 border border-rose-500/40 text-rose-300 flex items-center justify-between text-sm shadow-lg mb-4">
                <div class="flex items-center gap-3">
                    <i class="bi bi-x-circle-fill text-rose-400 text-lg"></i>
                    <span><?php echo e(session('error')); ?></span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-400 hover:text-white"><i class="bi bi-x-lg"></i></button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Main Content Body -->
    <main class="flex-grow">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Slide-over Mini Cart Drawer Overlay -->
    <div id="cart-drawer-overlay" class="fixed inset-0 bg-black/75 backdrop-blur-sm z-50 transition-opacity duration-300 opacity-0 pointer-events-none">
        <div id="cart-drawer" class="fixed inset-y-0 right-0 max-w-full flex pl-10 transform translate-x-full transition-transform duration-300">
            <div class="w-screen max-w-md bg-[#111827] border-l border-gray-800 text-gray-100 flex flex-col shadow-2xl">
                
                <!-- Drawer Header -->
                <div class="p-6 border-b border-gray-800 flex items-center justify-between bg-gray-900/60">
                    <div class="flex items-center gap-3">
                        <i class="bi bi-bag-fill text-cyan-400 text-xl"></i>
                        <h2 class="text-lg font-bold text-white">Your Shopping Cart</h2>
                    </div>
                    <button type="button" id="cart-drawer-close" class="text-gray-400 hover:text-white p-2 rounded-lg hover:bg-gray-800">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>

                <!-- Drawer Items Container -->
                <div id="drawer-items-list" class="flex-1 overflow-y-auto p-6 space-y-4">
                    <!-- Dynamic rendering via JS -->
                </div>

                <!-- Drawer Footer -->
                <div class="p-6 border-t border-gray-800 bg-gray-900/80 space-y-4">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-400">Estimated Subtotal</span>
                        <span id="drawer-subtotal" class="text-xl font-black text-white">$0.00</span>
                    </div>
                    <p class="text-xs text-gray-500">Taxes and shipping calculated at checkout.</p>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="<?php echo e(route('cart.index')); ?>" class="w-full py-3 text-center text-sm font-semibold rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-200 border border-gray-700 transition-colors">
                            View Full Cart
                        </a>
                        <a href="<?php echo e(route('checkout.index')); ?>" class="w-full py-3 text-center text-sm font-bold rounded-xl cyber-glow-btn">
                            Checkout <i class="bi bi-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="mt-24 bg-[#080b12] border-t border-gray-800/80 text-gray-400 text-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                
                <!-- Col 1 -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-blue-600 to-cyan-400 flex items-center justify-center text-white font-bold text-base">
                            SE
                        </div>
                        <span class="text-xl font-extrabold text-white">SE Shop</span>
                    </div>
                    <p class="text-xs text-gray-400 leading-relaxed">
                        Curated hardware, high-performance laptops, custom mechanical keyboards, and precision peripherals designed for developers and tech enthusiasts.
                    </p>
                    <div class="flex items-center gap-3 text-lg text-gray-400">
                        <a href="#" class="hover:text-cyan-400"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="hover:text-cyan-400"><i class="bi bi-github"></i></a>
                        <a href="#" class="hover:text-cyan-400"><i class="bi bi-discord"></i></a>
                        <a href="#" class="hover:text-cyan-400"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>

                <!-- Col 2 -->
                <div>
                    <h3 class="text-white font-bold mb-4 uppercase text-xs tracking-wider">Storefront</h3>
                    <ul class="space-y-2 text-xs">
                        <li><a href="<?php echo e(route('shop.index')); ?>" class="hover:text-cyan-400 transition-colors">All Products</a></li>
                        <li><a href="<?php echo e(route('shop.index', ['category' => 'laptops-workstations'])); ?>" class="hover:text-cyan-400 transition-colors">Dev Laptops</a></li>
                        <li><a href="<?php echo e(route('shop.index', ['category' => 'smart-peripherals'])); ?>" class="hover:text-cyan-400 transition-colors">Mechanical Keyboards</a></li>
                        <li><a href="<?php echo e(route('shop.index', ['category' => 'audio-studio-tech'])); ?>" class="hover:text-cyan-400 transition-colors">ANC Headphones</a></li>
                    </ul>
                </div>

                <!-- Col 3 -->
                <div>
                    <h3 class="text-white font-bold mb-4 uppercase text-xs tracking-wider">Customer Care</h3>
                    <ul class="space-y-2 text-xs">
                        <li><a href="<?php echo e(route('orders.lookup')); ?>" class="hover:text-cyan-400 transition-colors flex items-center gap-1.5"><i class="bi bi-geo-alt text-cyan-400"></i> Order Status & Tracking</a></li>
                        <li><a href="<?php echo e(route('shipping')); ?>" class="hover:text-cyan-400 transition-colors flex items-center gap-1.5"><i class="bi bi-truck text-cyan-400"></i> Shipping & Delivery Policy</a></li>
                        <li><a href="<?php echo e(route('returns')); ?>" class="hover:text-cyan-400 transition-colors flex items-center gap-1.5"><i class="bi bi-shield-check text-emerald-400"></i> Returns & 2-Year Warranty</a></li>
                        <li><a href="<?php echo e(route('admin.dashboard')); ?>" class="hover:text-cyan-400 transition-colors flex items-center gap-1.5"><i class="bi bi-speedometer2 text-indigo-400"></i> Merchant Portal</a></li>
                    </ul>
                </div>

                <!-- Col 4 -->
                <div class="space-y-4">
                    <h3 class="text-white font-bold uppercase text-xs tracking-wider">Stay Connected</h3>
                    <p class="text-xs text-gray-400">Subscribe for early access product drops and developer discount codes.</p>
                    <form onsubmit="event.preventDefault(); alert('Subscribed to SE Shop newsletter!');" class="flex gap-2">
                        <input type="email" placeholder="dev@company.com" required class="w-full bg-gray-900 border border-gray-800 rounded-lg px-3 py-2 text-xs text-gray-200 focus:outline-none focus:border-cyan-500">
                        <button type="submit" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-gray-950 font-bold rounded-lg text-xs transition-colors">Join</button>
                    </form>
                </div>

            </div>

            <div class="mt-12 pt-8 border-t border-gray-800/60 flex flex-col md:flex-row items-center justify-between text-xs text-gray-500">
                <p>&copy; <?php echo e(date('Y')); ?> SE Shop Inc. All rights reserved.</p>
                <div class="flex items-center gap-6 mt-4 md:mt-0">
                    <a href="<?php echo e(route('privacy')); ?>" class="hover:text-cyan-400 transition-colors">Privacy Policy</a>
                    <a href="<?php echo e(route('terms')); ?>" class="hover:text-cyan-400 transition-colors">Terms of Service</a>
                    <a href="<?php echo e(route('security')); ?>" class="hover:text-cyan-400 transition-colors">Security</a>
                </div>

            </div>
        </div>
    </footer>

    <!-- Client-Side Drawer Script & AJAX Cart Handler -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('cart-drawer-toggle');
            const closeBtn = document.getElementById('cart-drawer-close');
            const overlay = document.getElementById('cart-drawer-overlay');
            const drawer = document.getElementById('cart-drawer');
            const drawerItemsList = document.getElementById('drawer-items-list');
            const drawerSubtotal = document.getElementById('drawer-subtotal');
            const badge = document.getElementById('cart-count-badge');

            function openDrawer() {
                fetchCartData();
                overlay.classList.remove('opacity-0', 'pointer-events-none');
                drawer.classList.remove('translate-x-full');
            }

            function closeDrawer() {
                overlay.classList.add('opacity-0', 'pointer-events-none');
                drawer.classList.add('translate-x-full');
            }

            if (toggleBtn) toggleBtn.addEventListener('click', openDrawer);
            if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
            if (overlay) {
                overlay.addEventListener('click', (e) => {
                    if (e.target === overlay) closeDrawer();
                });
            }

            function fetchCartData() {
                fetch("<?php echo e(route('cart.json')); ?>")
                    .then(res => res.json())
                    .then(data => {
                        updateDrawerUI(data);
                    });
            }

            function updateDrawerUI(data) {
                // Update Badge
                if (badge) {
                    badge.textContent = data.cart_count;
                    if (data.cart_count > 0) {
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }

                // Update Subtotal
                if (drawerSubtotal) drawerSubtotal.textContent = '$' + data.subtotal;

                // Update Items List
                if (drawerItemsList) {
                    if (!data.cart || data.cart.length === 0) {
                        drawerItemsList.innerHTML = `
                            <div class="text-center py-12 space-y-3">
                                <i class="bi bi-cart-x text-5xl text-gray-600 block"></i>
                                <p class="text-gray-400 text-sm font-semibold">Your cart is currently empty</p>
                                <a href="<?php echo e(route('shop.index')); ?>" onclick="closeDrawer()" class="inline-block px-4 py-2 text-xs font-bold rounded-lg bg-blue-600 hover:bg-blue-500 text-white transition-colors">Browse Products</a>
                            </div>
                        `;
                        return;
                    }

                    drawerItemsList.innerHTML = data.cart.map(item => `
                        <div class="flex items-center gap-4 p-3 rounded-xl bg-gray-900/60 border border-gray-800">
                            <img src="${item.image}" alt="${item.name}" class="w-16 h-16 object-cover rounded-lg bg-gray-800">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs font-bold text-white truncate">${item.name}</h4>
                                <p class="text-[11px] text-gray-400">$${parseFloat(item.price).toFixed(2)} &times; ${item.quantity}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <button type="button" onclick="updateCartQuantity(${item.id}, ${item.quantity - 1})" class="w-5 h-5 rounded bg-gray-800 hover:bg-gray-700 flex items-center justify-center text-xs text-gray-300">-</button>
                                    <span class="text-xs font-bold text-gray-200">${item.quantity}</span>
                                    <button type="button" onclick="updateCartQuantity(${item.id}, ${item.quantity + 1})" class="w-5 h-5 rounded bg-gray-800 hover:bg-gray-700 flex items-center justify-center text-xs text-gray-300">+</button>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-bold text-cyan-400 block">$${(item.price * item.quantity).toFixed(2)}</span>
                                <button type="button" onclick="removeCartItem(${item.id})" class="text-gray-500 hover:text-rose-400 text-xs mt-1">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    `).join('');
                }
            }

            window.updateCartQuantity = function(id, qty) {
                if (qty <= 0) {
                    removeCartItem(id);
                    return;
                }
                fetch("<?php echo e(route('cart.update')); ?>", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    body: JSON.stringify({ product_id: id, quantity: qty })
                }).then(res => res.json()).then(data => {
                    fetchCartData();
                });
            };

            window.removeCartItem = function(id) {
                fetch("<?php echo e(route('cart.remove')); ?>", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    body: JSON.stringify({ product_id: id })
                }).then(res => res.json()).then(data => {
                    fetchCartData();
                });
            };
        });
    </script>
</body>
</html>
<?php /**PATH D:\YEAR IV\SEMESTER I\WB-III\project\my-laravel-app\resources\views/layouts/app.blade.php ENDPATH**/ ?>