<?php $__env->startSection('title', 'Track Your Order — SE Shop'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-[70vh] flex items-center justify-center py-16 px-4 relative overflow-hidden">

    
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-1/3 left-1/2 -translate-x-1/2 w-[500px] h-[300px] bg-blue-600/8 rounded-full blur-[100px]"></div>
    </div>

    <div class="w-full max-w-lg relative z-10 space-y-6">

        
        <div class="text-center space-y-3">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-950 to-indigo-950 border border-blue-500/30 text-cyan-400 text-2xl shadow-xl mx-auto">
                <i class="bi bi-geo-alt-fill"></i>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-white tracking-tight">Track Your Order</h1>
                <p class="text-sm text-gray-400 mt-1.5">Enter your order details below to view real-time delivery status</p>
            </div>
        </div>

        
        <?php if($errors->any()): ?>
            <div class="alert alert-error animate-slide-in-up">
                <i class="alert-icon bi bi-exclamation-circle-fill text-lg"></i>
                <div class="space-y-1">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <p class="text-xs"><?php echo e($error); ?></p>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endif; ?>

        
        <div class="glass-panel p-8 space-y-6">

            <form action="<?php echo e(route('orders.lookup.post')); ?>" method="POST" id="lookupForm" class="space-y-5">
                <?php echo csrf_field(); ?>

                <div class="form-group">
                    <label for="order_number" class="form-label font-semibold text-gray-300">Order Number</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 text-sm">
                            <i class="bi bi-hash text-cyan-400"></i>
                        </span>
                        <input id="order_number" name="order_number" type="text"
                            value="<?php echo e(old('order_number')); ?>"
                            placeholder="SE-ORD-XXXXXX"
                            required
                            class="input-dark pl-11 font-mono tracking-wider uppercase <?php echo e($errors->has('order_number') ? 'border-rose-500/70' : ''); ?>"
                            oninput="this.value = this.value.toUpperCase()">
                    </div>
                    <p class="text-[11px] text-gray-500 mt-1.5 flex items-center gap-1">
                        <i class="bi bi-info-circle text-gray-500"></i> Format: SE-ORD-XXXXXX (from your confirmation email)
                    </p>
                </div>

                <div class="form-group">
                    <label for="customer_email" class="form-label font-semibold text-gray-300">Email Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 text-sm">
                            <i class="bi bi-envelope text-cyan-400"></i>
                        </span>
                        <input id="customer_email" name="customer_email" type="email"
                            value="<?php echo e(old('customer_email')); ?>"
                            placeholder="Email used when placing the order"
                            required
                            class="input-dark pl-11 <?php echo e($errors->has('customer_email') ? 'border-rose-500/70' : ''); ?>">
                    </div>
                </div>

                <button type="submit" id="trackBtn" class="btn btn-primary btn-full btn-lg inline-flex items-center justify-center gap-2.5 group">
                    <i class="bi bi-search text-base"></i>
                    <span id="trackBtnText" class="font-bold">Track My Order</span>
                    <i class="bi bi-arrow-right text-base group-hover:translate-x-1 transition-transform ml-1" id="trackBtnIcon"></i>
                    <span id="trackBtnSpinner" class="spinner hidden"></span>
                </button>
            </form>

            <div class="border-t border-gray-800/60 pt-5 space-y-3">
                <p class="text-xs font-semibold text-gray-400 text-center">Quick Links</p>
                <div class="grid grid-cols-2 gap-2.5">
                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-ghost btn-sm gap-1.5 justify-center">
                            <i class="bi bi-person-circle text-cyan-400"></i> My Orders
                        </a>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="btn btn-ghost btn-sm gap-1.5 justify-center">
                            <i class="bi bi-box-arrow-in-right text-cyan-400"></i> Sign In
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo e(route('shop.index')); ?>" class="btn btn-ghost btn-sm gap-1.5 justify-center">
                        <i class="bi bi-bag text-indigo-400"></i> Shop Now
                    </a>
                </div>
            </div>
        </div>

        
        <p class="text-center text-xs text-gray-500">
            Can't find your order?
            <a href="<?php echo e(route('home')); ?>" class="text-cyan-400 hover:underline font-semibold">Contact Support</a>
        </p>
    </div>
</div>

<script>
    document.getElementById('lookupForm')?.addEventListener('submit', () => {
        document.getElementById('trackBtnText').textContent = 'Looking up...';
        document.getElementById('trackBtnIcon').classList.add('hidden');
        document.getElementById('trackBtnSpinner').classList.remove('hidden');
        document.getElementById('trackBtn').disabled = true;
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\YEAR IV\SEMESTER I\WB-III\project\my-laravel-app\resources\views/checkout/lookup.blade.php ENDPATH**/ ?>