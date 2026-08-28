<?php $__env->startSection('title', 'Secure Checkout — SE Shop'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    
    <nav class="breadcrumb mb-6">
        <a href="<?php echo e(route('home')); ?>">Home</a>
        <span class="breadcrumb-sep"><i class="bi bi-chevron-right text-[10px]"></i></span>
        <a href="<?php echo e(route('cart.index')); ?>">Cart</a>
        <span class="breadcrumb-sep"><i class="bi bi-chevron-right text-[10px]"></i></span>
        <span class="active">Checkout</span>
    </nav>

    
    <div class="mb-10">
        <div class="flex items-center gap-0 max-w-lg">
            
            <div class="flex items-center gap-2.5 group">
                <div class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-600 to-indigo-500 text-white flex items-center justify-center font-bold text-xs shadow-lg shadow-blue-500/30 flex-shrink-0">
                    <i class="bi bi-truck"></i>
                </div>
                <span class="text-xs font-bold text-white hidden sm:block">Shipping</span>
            </div>
            <div class="flex-1 h-px mx-3" style="background: linear-gradient(90deg, #3b82f6, #6366f1)"></div>
            
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-gradient-to-r from-indigo-600 to-purple-500 text-white flex items-center justify-center font-bold text-xs shadow-lg shadow-indigo-500/30 flex-shrink-0">
                    <i class="bi bi-credit-card"></i>
                </div>
                <span class="text-xs font-bold text-white hidden sm:block">Payment</span>
            </div>
            <div class="flex-1 h-px mx-3 bg-gray-700/60"></div>
            
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-gray-800 border border-gray-700 text-gray-500 flex items-center justify-center font-bold text-xs flex-shrink-0">
                    <i class="bi bi-check2-circle"></i>
                </div>
                <span class="text-xs font-semibold text-gray-500 hidden sm:block">Confirm</span>
            </div>
        </div>
    </div>

    <form action="<?php echo e(route('checkout.process')); ?>" method="POST" id="checkoutForm">
        <?php echo csrf_field(); ?>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            
            <div class="lg:col-span-8 space-y-6">

                
                <?php if($errors->any()): ?>
                    <div class="alert alert-error animate-slide-in-up">
                        <i class="alert-icon bi bi-exclamation-circle-fill"></i>
                        <div>
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <p><?php echo e($error); ?></p>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endif; ?>

                
                <div class="glass-panel p-6 space-y-5">
                    <div class="flex items-center gap-3 pb-4 border-b border-gray-800/80">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 text-white font-bold flex items-center justify-center text-xs flex-shrink-0 shadow-lg shadow-blue-600/30">1</div>
                        <div>
                            <h3 class="text-sm font-bold text-white">Shipping Details</h3>
                            <p class="text-xs text-gray-500">Where should we deliver your order?</p>
                        </div>
                        <?php if(auth()->guard()->check()): ?>
                            <span class="ml-auto badge badge-emerald text-[10px] gap-1.5">
                                <i class="bi bi-person-check-fill"></i> Pre-filled
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2 form-group">
                            <label class="form-label">Full Name *</label>
                            <div class="input-icon-group">
                                <span class="input-icon-left">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input type="text" name="customer_name" value="<?php echo e(old('customer_name', $user->name ?? '')); ?>" required
                                    placeholder="e.g. Alex Rivera"
                                    class="input-dark <?php echo e($errors->has('customer_name') ? 'border-rose-500/70' : ''); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email Address *</label>
                            <div class="input-icon-group">
                                <span class="input-icon-left">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" name="customer_email" value="<?php echo e(old('customer_email', $user->email ?? '')); ?>" required
                                    placeholder="alex@example.com"
                                    class="input-dark <?php echo e($errors->has('customer_email') ? 'border-rose-500/70' : ''); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Phone Number *</label>
                            <div class="input-icon-group">
                                <span class="input-icon-left">
                                    <i class="bi bi-telephone"></i>
                                </span>
                                <input type="text" name="customer_phone" value="<?php echo e(old('customer_phone')); ?>" required
                                    placeholder="+1 (555) 000-0000"
                                    class="input-dark <?php echo e($errors->has('customer_phone') ? 'border-rose-500/70' : ''); ?>">
                            </div>
                        </div>

                        <div class="sm:col-span-2 form-group">
                            <label class="form-label">Street Address *</label>
                            <div class="input-icon-group">
                                <span class="input-icon-left">
                                    <i class="bi bi-geo-alt"></i>
                                </span>
                                <input type="text" name="shipping_address" value="<?php echo e(old('shipping_address')); ?>" required
                                    placeholder="742 Silicon Valley Ave, Suite 300"
                                    class="input-dark <?php echo e($errors->has('shipping_address') ? 'border-rose-500/70' : ''); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">City *</label>
                            <div class="input-icon-group">
                                <span class="input-icon-left">
                                    <i class="bi bi-building"></i>
                                </span>
                                <input type="text" name="city" value="<?php echo e(old('city')); ?>" required
                                    placeholder="San Francisco"
                                    class="input-dark <?php echo e($errors->has('city') ? 'border-rose-500/70' : ''); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Postal / ZIP Code *</label>
                            <div class="input-icon-group">
                                <span class="input-icon-left">
                                    <i class="bi bi-mailbox"></i>
                                </span>
                                <input type="text" name="postal_code" value="<?php echo e(old('postal_code')); ?>" required
                                    placeholder="94107"
                                    class="input-dark <?php echo e($errors->has('postal_code') ? 'border-rose-500/70' : ''); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="glass-panel p-6 space-y-5">
                    <div class="flex items-center gap-3 pb-4 border-b border-gray-800/80">
                        <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white font-bold flex items-center justify-center text-xs flex-shrink-0 shadow-lg shadow-indigo-600/30">2</div>
                        <div>
                            <h3 class="text-sm font-bold text-white">Payment Method</h3>
                            <p class="text-xs text-gray-500">All transactions are 256-bit SSL encrypted</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <?php $__currentLoopData = [
                            ['value' => 'card',      'icon' => 'bi-credit-card',      'label' => 'Credit Card',  'color' => 'text-cyan-400',  'checked' => true],
                            ['value' => 'paypal',    'icon' => 'bi-paypal',           'label' => 'PayPal',       'color' => 'text-blue-400',  'checked' => false],
                            ['value' => 'apple_pay', 'icon' => 'bi-apple',            'label' => 'Apple Pay',    'color' => 'text-gray-200',  'checked' => false],
                            ['value' => 'crypto',    'icon' => 'bi-currency-bitcoin', 'label' => 'Crypto',       'color' => 'text-amber-400', 'checked' => false],
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="cursor-pointer select-none">
                                <input type="radio" name="payment_method" value="<?php echo e($pm['value']); ?>"
                                    <?php echo e(old('payment_method', 'card') === $pm['value'] ? 'checked' : ''); ?>

                                    class="peer sr-only">
                                <div class="p-4 rounded-xl bg-gray-900/70 border border-gray-800 peer-checked:border-blue-500 peer-checked:bg-blue-950/40 text-center space-y-2.5 transition-all hover:border-gray-700">
                                    <i class="bi <?php echo e($pm['icon']); ?> text-2xl <?php echo e($pm['color']); ?> block transition-transform peer-checked:scale-110"></i>
                                    <span class="text-xs font-bold text-gray-200 block"><?php echo e($pm['label']); ?></span>
                                </div>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    
                    <div class="p-4 rounded-xl bg-gray-900/60 border border-gray-800 space-y-4">
                        <div class="form-group">
                            <label class="form-label text-gray-300">Card Number</label>
                            <div class="input-icon-group">
                                <span class="input-icon-left">
                                    <i class="bi bi-credit-card font-bold"></i>
                                </span>
                                <input type="text" placeholder="4532 •••• •••• ••••" class="input-dark font-mono text-sm">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="form-group">
                                <label class="form-label text-gray-300">Expiration Date</label>
                                <div class="input-icon-group">
                                    <span class="input-icon-left">
                                        <i class="bi bi-calendar-event"></i>
                                    </span>
                                    <input type="text" placeholder="MM / YY" class="input-dark font-mono text-sm">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label text-gray-300">CVV / CVC</label>
                                <div class="input-icon-group">
                                    <span class="input-icon-left">
                                        <i class="bi bi-shield-lock"></i>
                                    </span>
                                    <input type="password" maxlength="4" placeholder="•••" class="input-dark font-mono text-sm">
                                </div>
                            </div>
                        </div>
                        <p class="text-[11px] text-gray-400 flex items-center gap-1.5"><i class="bi bi-shield-check text-emerald-400"></i> Encrypted & secured with 256-bit SSL protocol.</p>
                    </div>
                </div>


            </div>

            
            <div class="lg:col-span-4">
                <div class="glass-panel p-6 space-y-5 sticky-sidebar">

                    <h3 class="text-sm font-bold text-white uppercase tracking-wider border-b border-gray-800/80 pb-3 flex items-center gap-2">
                        <i class="bi bi-receipt text-cyan-400"></i> Your Order
                    </h3>

                    
                    <div class="space-y-3 max-h-56 overflow-y-auto pr-1 divide-y divide-gray-800/50">
                        <?php $__currentLoopData = $cart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="pt-3 flex items-center gap-3 first:pt-0">
                                <div class="w-10 h-10 rounded-lg bg-gray-800 overflow-hidden flex-shrink-0">
                                    <img src="<?php echo e($item['image']); ?>" alt="<?php echo e($item['name']); ?>" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-white truncate"><?php echo e($item['name']); ?></p>
                                    <p class="text-[11px] text-gray-400">×<?php echo e($item['quantity']); ?></p>
                                </div>
                                <span class="text-xs font-bold text-white flex-shrink-0">$<?php echo e(number_format($item['price'] * $item['quantity'], 2)); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    
                    <?php if(session('coupon')): ?>
                        <div class="flex items-center gap-2 p-2.5 rounded-lg bg-emerald-950/60 border border-emerald-500/30 text-xs text-emerald-300">
                            <i class="bi bi-tag-fill text-emerald-400"></i>
                            <span><span class="font-mono font-bold"><?php echo e(session('coupon.code')); ?></span>: <?php echo e(session('coupon.name')); ?></span>
                        </div>
                    <?php endif; ?>

                    
                    <div class="space-y-2.5 text-xs border-t border-gray-800/80 pt-4">
                        <div class="flex justify-between text-gray-300">
                            <span>Subtotal</span>
                            <span class="font-bold text-white">$<?php echo e(number_format($subtotal, 2)); ?></span>
                        </div>
                        <?php if($discount > 0): ?>
                            <div class="flex justify-between text-emerald-400 font-semibold">
                                <span>Discount</span>
                                <span>−$<?php echo e(number_format($discount, 2)); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="flex justify-between text-gray-300">
                            <span>Shipping</span>
                            <span class="font-bold <?php echo e($shipping == 0 ? 'text-emerald-400' : 'text-white'); ?>">
                                <?php echo e($shipping == 0 ? 'FREE' : '$'.number_format($shipping, 2)); ?>

                            </span>
                        </div>
                        <div class="flex justify-between text-gray-300">
                            <span>Est. Tax (5%)</span>
                            <span class="font-bold text-white">$<?php echo e(number_format($tax, 2)); ?></span>
                        </div>
                        <div class="flex justify-between items-baseline pt-3 border-t border-gray-800">
                            <span class="text-sm font-bold text-white">Total Due</span>
                            <span class="text-2xl font-black text-cyan-400">$<?php echo e(number_format($total, 2)); ?></span>
                        </div>
                    </div>

                    
                    <button type="submit" id="placeOrderBtn" class="btn btn-primary btn-full btn-lg gap-2.5 group">
                        <i class="bi bi-lock-fill text-sm"></i>
                        <span id="placeOrderText">Complete Order</span>
                        <span id="placeOrderSpinner" class="spinner hidden"></span>
                    </button>

                    <div class="space-y-1.5 text-center text-[10px] text-gray-500">
                        <p class="flex items-center justify-center gap-1.5"><i class="bi bi-shield-check text-cyan-400"></i>256-bit SSL encrypted & CSRF protected</p>
                        <p>Instant order confirmation sent to your email</p>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
    document.getElementById('checkoutForm')?.addEventListener('submit', function() {
        document.getElementById('placeOrderText').textContent    = 'Processing...';
        document.getElementById('placeOrderSpinner').classList.remove('hidden');
        document.getElementById('placeOrderBtn').disabled = true;
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\YEAR IV\SEMESTER I\WB-III\project\my-laravel-app\resources\views/checkout/index.blade.php ENDPATH**/ ?>