<?php $__env->startSection('title', 'Shopping Cart — SE Shop'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    
    <nav class="breadcrumb mb-6">
        <a href="<?php echo e(route('home')); ?>">Home</a>
        <span class="breadcrumb-sep"><i class="bi bi-chevron-right text-[10px]"></i></span>
        <a href="<?php echo e(route('shop.index')); ?>">Shop</a>
        <span class="breadcrumb-sep"><i class="bi bi-chevron-right text-[10px]"></i></span>
        <span class="active">Shopping Cart</span>
    </nav>

    <div class="mb-8 flex items-center justify-between">
        <div>
            <span class="section-eyebrow"><i class="bi bi-bag-fill"></i> Your Bag</span>
            <h1 class="text-3xl font-extrabold text-white mt-2">Shopping Cart</h1>
        </div>
        <?php if(!empty($cart)): ?>
            <span class="badge badge-cyan text-xs"><?php echo e(array_sum(array_column($cart, 'quantity'))); ?> item(s)</span>
        <?php endif; ?>
    </div>

    
    <?php if(empty($cart)): ?>
        <div class="glass-panel p-20 text-center space-y-6 max-w-xl mx-auto animate-fade-in">
            <div class="empty-state-icon mx-auto">
                <i class="bi bi-cart-x"></i>
            </div>
            <div class="space-y-2">
                <h3 class="text-xl font-bold text-white">Your Cart is Empty</h3>
                <p class="text-sm text-gray-400">You haven't added any developer gear yet. Explore our catalog!</p>
            </div>
            <a href="<?php echo e(route('shop.index')); ?>" class="btn btn-primary btn-lg inline-flex gap-2">
                Browse Catalog <i class="bi bi-arrow-right"></i>
            </a>
        </div>

    
    <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            
            <div class="lg:col-span-8 space-y-4">
                
                <div class="glass-panel px-6 py-4 flex items-center justify-between">
                    <span class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="bi bi-box2 text-cyan-400"></i> Items in Your Cart (<?php echo e(count($cart)); ?>)
                    </span>
                    <a href="<?php echo e(route('cart.clear')); ?>"
                        onclick="return confirm('Remove all items from your cart?')"
                        class="text-xs font-semibold text-rose-400 hover:text-rose-300 flex items-center gap-1.5 transition-colors">
                        <i class="bi bi-trash3"></i> Clear Cart
                    </a>
                </div>

                
                <div id="cartItemsList" class="space-y-3.5">
                    <?php $__currentLoopData = $cart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $productId => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="cart-item-card cart-item-row p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center gap-4" data-id="<?php echo e($item['id']); ?>" data-price="<?php echo e($item['price']); ?>">

                        
                        <a href="<?php echo e(route('shop.show', $item['slug'])); ?>" class="flex-shrink-0">
                            <div class="w-20 h-20 rounded-xl bg-gray-950 border border-gray-800 overflow-hidden shadow-inner">
                                <img src="<?php echo e($item['image']); ?>" alt="<?php echo e($item['name']); ?>" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                            </div>
                        </a>

                        
                        <div class="flex-1 min-w-0 space-y-1">
                            <span class="text-[10px] font-bold text-cyan-400 uppercase tracking-wider"><?php echo e($item['category']); ?></span>
                            <h3 class="text-sm font-bold text-white leading-snug">
                                <a href="<?php echo e(route('shop.show', $item['slug'])); ?>" class="hover:text-cyan-300 transition-colors">
                                    <?php echo e($item['name']); ?>

                                </a>
                            </h3>
                            <p class="text-xs text-gray-400 font-medium">$<?php echo e(number_format($item['price'], 2)); ?> each</p>
                        </div>

                        
                        <div class="flex items-center justify-between sm:justify-end gap-5 flex-shrink-0">

                            
                            <div class="qty-stepper">
                                <button type="button"
                                    onclick="ajaxCartUpdate(<?php echo e($item['id']); ?>, <?php echo e($item['quantity'] - 1); ?>, this)"
                                    class="qty-btn <?php echo e($item['quantity'] <= 1 ? 'opacity-40 cursor-not-allowed' : ''); ?>"
                                    <?php if($item['quantity'] <= 1): ?> disabled <?php endif; ?>>
                                    <i class="bi bi-dash"></i>
                                </button>
                                <span class="qty-value" id="qty-<?php echo e($item['id']); ?>"><?php echo e($item['quantity']); ?></span>
                                <button type="button"
                                    onclick="ajaxCartUpdate(<?php echo e($item['id']); ?>, <?php echo e($item['quantity'] + 1); ?>, this)">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>

                            
                            <div class="text-right min-w-[80px]">
                                <span class="text-sm font-black text-white block" id="line-<?php echo e($item['id']); ?>">
                                    $<?php echo e(number_format($item['price'] * $item['quantity'], 2)); ?>

                                </span>
                                <button type="button"
                                    onclick="ajaxCartRemove(<?php echo e($item['id']); ?>, this)"
                                    class="text-[11px] text-gray-500 hover:text-rose-400 transition-colors font-semibold mt-0.5">
                                    <i class="bi bi-x"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>


                
                <div class="flex items-center justify-between text-xs text-gray-400 px-1">
                    <a href="<?php echo e(route('shop.index')); ?>" class="flex items-center gap-1.5 hover:text-cyan-400 font-semibold transition-colors">
                        <i class="bi bi-arrow-left"></i> Continue Shopping
                    </a>
                    <span class="flex items-center gap-1.5">
                        <i class="bi bi-shield-check text-emerald-400"></i>
                        256-bit SSL encrypted checkout
                    </span>
                </div>
            </div>

            
            <div class="lg:col-span-4 space-y-5">
                <div class="glass-panel p-6 space-y-5 sticky-sidebar">

                    <h3 class="text-base font-bold text-white border-b border-gray-800/80 pb-4">Order Summary</h3>

                    
                    <?php if(session('coupon')): ?>
                        <div class="flex items-center justify-between p-3 rounded-xl bg-emerald-950/60 border border-emerald-500/30 text-xs">
                            <div class="flex items-center gap-2 text-emerald-300">
                                <i class="bi bi-tag-fill text-emerald-400"></i>
                                <span><span class="font-mono font-bold"><?php echo e(session('coupon.code')); ?></span> — <?php echo e(session('coupon.name')); ?></span>
                            </div>
                            <a href="<?php echo e(route('cart.coupon.remove')); ?>" class="text-rose-400 hover:text-rose-300 font-bold text-[11px]">✕</a>
                        </div>
                    <?php else: ?>
                        <form action="<?php echo e(route('cart.coupon.apply')); ?>" method="POST" class="flex gap-2">
                            <?php echo csrf_field(); ?>
                            <input type="text" name="coupon_code" required
                                placeholder="Coupon code…"
                                class="input-dark input-sm flex-1 uppercase tracking-wider">
                            <button type="submit" class="btn btn-ghost btn-sm whitespace-nowrap">Apply</button>
                        </form>
                        <p class="text-[10px] text-gray-500 -mt-3">Try: <span class="mono-tag">SESHOP2026</span> · <span class="mono-tag">SAVE10</span> · <span class="mono-tag">DEVBUILD</span></p>
                    <?php endif; ?>

                    
                    <?php
                        $coupon     = session('coupon');
                        $discount   = 0.00;
                        if ($coupon) {
                            $discount = $coupon['type'] === 'percent'
                                ? ($subtotal * $coupon['value']) / 100
                                : min($subtotal, $coupon['value']);
                        }
                        $taxable     = max(0, $subtotal - $discount);
                        $shipping    = $taxable > 500 ? 0.00 : 15.00;
                        $finalTotal  = max(0, $taxable + $shipping);
                        $freeShipGap = 500 - $taxable;
                    ?>

                    <?php if($freeShipGap > 0 && $taxable < 500): ?>
                        <div class="p-3 rounded-xl bg-blue-950/60 border border-blue-500/25 text-[11px] text-cyan-300 flex items-center gap-2">
                            <i class="bi bi-truck text-sm flex-shrink-0"></i>
                            <span>Add <strong>$<?php echo e(number_format($freeShipGap, 2)); ?></strong> more to unlock <strong>FREE shipping!</strong></span>
                        </div>
                    <?php else: ?>
                        <div class="p-3 rounded-xl bg-emerald-950/60 border border-emerald-500/25 text-[11px] text-emerald-300 flex items-center gap-2">
                            <i class="bi bi-truck text-sm flex-shrink-0"></i>
                            <span><strong>Free shipping</strong> applied to your order!</span>
                        </div>
                    <?php endif; ?>

                    
                    <div class="space-y-2.5 text-xs">
                        <div class="flex justify-between text-gray-300">
                            <span>Subtotal</span>
                            <span id="summarySubtotal" class="font-bold text-white">$<?php echo e(number_format($subtotal, 2)); ?></span>
                        </div>
                        <?php if($discount > 0): ?>
                            <div class="flex justify-between text-emerald-400 font-semibold">
                                <span>Discount (<?php echo e(session('coupon.code')); ?>)</span>
                                <span>−$<?php echo e(number_format($discount, 2)); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="flex justify-between text-gray-300">
                            <span>Shipping</span>
                            <span class="font-bold <?php echo e($shipping == 0 ? 'text-emerald-400' : 'text-white'); ?>">
                                <?php echo e($shipping == 0 ? 'FREE' : '$'.number_format($shipping, 2)); ?>

                            </span>
                        </div>
                        <div class="flex justify-between items-baseline pt-3 border-t border-gray-800">
                            <span class="text-sm font-bold text-white">Total</span>
                            <span id="summaryTotal" class="text-2xl font-black text-cyan-400">$<?php echo e(number_format($finalTotal, 2)); ?></span>
                        </div>
                    </div>

                    
                    <?php if(auth()->guard()->check()): ?>
                        <?php if(!auth()->user()->isAdmin()): ?>
                            <a href="<?php echo e(route('checkout.index')); ?>" class="btn btn-primary btn-full btn-lg gap-2">
                                Proceed to Checkout <i class="bi bi-arrow-right"></i>
                            </a>
                        <?php else: ?>
                            <div class="p-3 rounded-xl bg-amber-950/60 border border-amber-500/30 text-xs text-amber-300 text-center">
                                <i class="bi bi-shield-lock mr-1"></i>
                                Admin accounts cannot place customer orders.
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="space-y-2">
                            <a href="<?php echo e(route('login')); ?>" class="btn btn-primary btn-full btn-lg gap-2">
                                <i class="bi bi-box-arrow-in-right"></i> Sign In to Checkout
                            </a>
                            <a href="<?php echo e(route('register')); ?>" class="btn btn-ghost btn-full btn-sm gap-1.5">
                                <i class="bi bi-person-plus"></i> Create Free Account
                            </a>
                            <p class="text-center text-[10px] text-gray-500">You must be signed in to complete an order</p>
                        </div>
                    <?php endif; ?>

                    
                    <div class="flex justify-around pt-1 border-t border-gray-800/60">
                        <div class="text-center">
                            <i class="bi bi-shield-check text-emerald-400 text-lg block mb-1"></i>
                            <span class="text-[9px] text-gray-500 uppercase tracking-wider">Secure</span>
                        </div>
                        <div class="text-center">
                            <i class="bi bi-arrow-repeat text-blue-400 text-lg block mb-1"></i>
                            <span class="text-[9px] text-gray-500 uppercase tracking-wider">Returns</span>
                        </div>
                        <div class="text-center">
                            <i class="bi bi-headset text-cyan-400 text-lg block mb-1"></i>
                            <span class="text-[9px] text-gray-500 uppercase tracking-wider">Support</span>
                        </div>
                        <div class="text-center">
                            <i class="bi bi-lock text-indigo-400 text-lg block mb-1"></i>
                            <span class="text-[9px] text-gray-500 uppercase tracking-wider">Encrypted</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    <?php endif; ?>
</div>


<div id="cartToast" class="toast hidden">
    <div class="alert text-sm" id="cartToastInner"></div>
</div>

<script>
    const CSRF_TOKEN = '<?php echo e(csrf_token()); ?>';

    function showToast(msg, type = 'success') {
        const toast = document.getElementById('cartToast');
        const inner = document.getElementById('cartToastInner');
        const iconMap = { success: 'check-circle-fill', error: 'exclamation-circle-fill', warning: 'exclamation-triangle-fill' };
        const classMap = { success: 'alert-success', error: 'alert-error', warning: 'alert-warning' };
        inner.className = 'alert text-sm ' + (classMap[type] || 'alert-success');
        inner.innerHTML = `<i class="alert-icon bi bi-${iconMap[type] || 'check-circle-fill'}"></i><p>${msg}</p>`;
        toast.classList.remove('hidden');
        clearTimeout(toast._timer);
        toast._timer = setTimeout(() => toast.classList.add('hidden'), 3500);
    }

    function ajaxCartUpdate(productId, newQty, btn) {
        if (newQty < 0) return;

        // Optimistically disable button
        const row = btn.closest('.cart-item-row');
        row.style.opacity = '0.5';
        row.style.pointerEvents = 'none';

        if (newQty === 0) {
            ajaxCartRemove(productId, btn);
            return;
        }

        fetch('<?php echo e(route("cart.update")); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({ product_id: productId, quantity: newQty })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success === false && data.message) {
                showToast(data.message, 'warning');
                row.style.opacity = '1';
                row.style.pointerEvents = '';
                return;
            }
            // Reload to re-render with updated server state
            window.location.reload();
        })
        .catch(() => {
            window.location.reload();
        });
    }

    function ajaxCartRemove(productId, btn) {
        const row = btn.closest('.cart-item-row');
        row.style.opacity = '0.4';
        row.style.transform = 'translateX(30px)';
        row.style.transition = 'all 0.3s ease';
        row.style.pointerEvents = 'none';

        fetch('<?php echo e(route("cart.remove")); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({ product_id: productId })
        })
        .then(r => r.json())
        .then(() => {
            setTimeout(() => window.location.reload(), 300);
        })
        .catch(() => window.location.reload());
    }
</script>

<style>
.qty-stepper button { width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; background: transparent; border: none; color: #94a3b8; cursor: pointer; font-size: 1rem; transition: all 0.15s; }
.qty-stepper button:hover { background: rgba(255,255,255,0.06); color: white; }
.qty-stepper { display: inline-flex; align-items: center; background: rgba(8,12,23,0.8); border: 1px solid rgba(255,255,255,0.12); border-radius: 0.75rem; overflow: hidden; }
.qty-value { min-width: 2.5rem; text-align: center; font-size: 0.825rem; font-weight: 700; color: white; }
#cartToast { position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 9999; min-width: 280px; max-width: 380px; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\YEAR IV\SEMESTER I\WB-III\project\my-laravel-app\resources\views/cart/index.blade.php ENDPATH**/ ?>