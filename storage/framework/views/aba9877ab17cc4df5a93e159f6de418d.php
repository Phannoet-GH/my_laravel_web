<?php $__env->startSection('title', 'Create Account — SE Shop'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-[85vh] flex items-center justify-center py-16 px-4 relative overflow-hidden">

    
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-1/4 right-1/4 w-[500px] h-[500px] bg-cyan-600/8 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-1/3 left-1/4 w-[400px] h-[400px] bg-emerald-600/8 rounded-full blur-[100px]"></div>
    </div>

    <div class="w-full max-w-md relative z-10">

        
        <div class="auth-box space-y-7">

            
            <div class="text-center space-y-3">
                <a href="<?php echo e(route('home')); ?>" class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-cyan-400 text-white font-black text-2xl shadow-xl shadow-blue-500/30 hover:scale-105 transition-transform">
                    SE
                </a>
                <div>
                    <h1 class="text-2xl font-black text-white tracking-tight">Join SE Shop</h1>
                    <p class="text-sm text-gray-400 mt-1">Create your free developer hardware account</p>
                </div>
            </div>

            
            <div class="grid grid-cols-3 gap-3 text-center">
                <div class="p-3 rounded-xl bg-gray-950/70 border border-gray-800 flex flex-col items-center justify-center gap-1.5 transition-all hover:border-gray-700">
                    <i class="bi bi-truck text-cyan-400 text-lg leading-none"></i>
                    <span class="text-[11px] font-bold text-gray-300 leading-tight block">Free<br>Shipping</span>
                </div>
                <div class="p-3 rounded-xl bg-gray-950/70 border border-gray-800 flex flex-col items-center justify-center gap-1.5 transition-all hover:border-gray-700">
                    <i class="bi bi-arrow-repeat text-indigo-400 text-lg leading-none"></i>
                    <span class="text-[11px] font-bold text-gray-300 leading-tight block">Easy<br>Returns</span>
                </div>
                <div class="p-3 rounded-xl bg-gray-950/70 border border-gray-800 flex flex-col items-center justify-center gap-1.5 transition-all hover:border-gray-700">
                    <i class="bi bi-shield-check text-emerald-400 text-lg leading-none"></i>
                    <span class="text-[11px] font-bold text-gray-300 leading-tight block">Secure<br>Account</span>
                </div>
            </div>

            
            <?php if($errors->any()): ?>
                <div class="alert alert-error animate-slide-in-up">
                    <i class="alert-icon bi bi-exclamation-circle-fill"></i>
                    <div class="space-y-0.5">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <p><?php echo e($error); ?></p>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>

            
            <form id="registerForm" action="<?php echo e(route('register')); ?>" method="POST" class="space-y-4">
                <?php echo csrf_field(); ?>

                <div class="form-group">
                    <label for="name" class="form-label">Full Name</label>
                    <div class="input-icon-group">
                        <span class="input-icon-left">
                            <i class="bi bi-person"></i>
                        </span>
                        <input id="name" name="name" type="text" required autocomplete="name"
                            value="<?php echo e(old('name')); ?>"
                            placeholder="Alex Rivera"
                            class="input-dark <?php echo e($errors->has('name') ? 'border-rose-500/70' : ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-icon-group">
                        <span class="input-icon-left">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input id="email" name="email" type="email" required autocomplete="email"
                            value="<?php echo e(old('email')); ?>"
                            placeholder="alex@developer.io"
                            class="input-dark <?php echo e($errors->has('email') ? 'border-rose-500/70' : ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password <span class="text-gray-500 font-normal">(min 8 chars)</span></label>
                    <div class="input-icon-group">
                        <span class="input-icon-left">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input id="password" name="password" type="password" required autocomplete="new-password"
                            placeholder="••••••••"
                            class="input-dark has-right-icon <?php echo e($errors->has('password') ? 'border-rose-500/70' : ''); ?>"
                            oninput="checkStrength(this.value)">
                        <button type="button" id="togglePwd1" class="input-icon-right" title="Toggle Password Visibility">
                            <i class="bi bi-eye" id="eye1"></i>
                        </button>
                    </div>
                    
                    <div class="mt-2 space-y-1">
                        <div class="flex gap-1">
                            <div class="h-1 flex-1 rounded-full bg-gray-800 overflow-hidden"><div id="s1" class="h-full rounded-full transition-all duration-300 w-0"></div></div>
                            <div class="h-1 flex-1 rounded-full bg-gray-800 overflow-hidden"><div id="s2" class="h-full rounded-full transition-all duration-300 w-0"></div></div>
                            <div class="h-1 flex-1 rounded-full bg-gray-800 overflow-hidden"><div id="s3" class="h-full rounded-full transition-all duration-300 w-0"></div></div>
                            <div class="h-1 flex-1 rounded-full bg-gray-800 overflow-hidden"><div id="s4" class="h-full rounded-full transition-all duration-300 w-0"></div></div>
                        </div>
                        <p id="strengthLabel" class="text-[10px] text-gray-500"></p>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <div class="input-icon-group">
                        <span class="input-icon-left">
                            <i class="bi bi-shield-lock"></i>
                        </span>
                        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                            placeholder="Re-enter your password"
                            class="input-dark has-right-icon">
                        <button type="button" id="togglePwd2" class="input-icon-right" title="Toggle Password Visibility">
                            <i class="bi bi-eye" id="eye2"></i>
                        </button>
                    </div>
                </div>

                
                <label class="flex items-start gap-3 cursor-pointer group">
                    <input id="terms" type="checkbox" required class="w-4 h-4 mt-0.5 rounded border-gray-700 bg-gray-950 text-cyan-500 focus:ring-cyan-500 focus:ring-offset-0 flex-shrink-0">
                    <span class="text-xs text-gray-400 group-hover:text-gray-300 leading-relaxed">
                        I agree to the <a href="#" class="text-cyan-400 hover:underline">Terms of Service</a> and <a href="#" class="text-cyan-400 hover:underline">Privacy Policy</a>
                    </span>
                </label>

                <button type="submit" id="regBtn"
                    class="btn btn-cyan btn-full btn-lg gap-2.5 group mt-2">
                    <span id="regBtnText">Create My Account</span>
                    <i class="bi bi-arrow-right group-hover:translate-x-1 transition-transform" id="regBtnIcon"></i>
                    <span id="regBtnSpinner" class="spinner hidden"></span>
                </button>

                <p class="text-center text-xs text-gray-500">
                    Already have an account?
                    <a href="<?php echo e(route('login')); ?>" class="text-cyan-400 font-semibold hover:text-cyan-300 ml-1">Sign in →</a>
                </p>
            </form>

        </div>
    </div>
</div>

<script>
    // Password strength meter
    function checkStrength(pw) {
        const levels = [
            { test: pw.length >= 4,                   color: 'bg-rose-500',   label: 'Too short' },
            { test: pw.length >= 8,                   color: 'bg-amber-400',  label: 'Weak' },
            { test: pw.length >= 10 && /[A-Z]/.test(pw) && /[0-9]/.test(pw), color: 'bg-blue-400', label: 'Good' },
            { test: pw.length >= 12 && /[A-Z]/.test(pw) && /[0-9]/.test(pw) && /[^a-zA-Z0-9]/.test(pw), color: 'bg-emerald-400', label: 'Strong' },
        ];
        let strength = 0;
        for (let i = 0; i < levels.length; i++) {
            if (levels[i].test) strength = i + 1;
        }
        const bars   = ['s1','s2','s3','s4'];
        const colors = ['bg-rose-500','bg-amber-400','bg-blue-400','bg-emerald-400'];
        const labels = ['','Too short','Weak','Good','Strong'];
        bars.forEach((id, i) => {
            const bar = document.getElementById(id);
            bar.className = 'h-full rounded-full transition-all duration-300 ' + (i < strength ? colors[strength-1] : '');
            bar.style.width = i < strength ? '100%' : '0';
        });
        document.getElementById('strengthLabel').textContent = pw.length ? labels[strength] : '';
    }

    // Eye toggles
    function makeToggle(btnId, inputId, eyeId) {
        document.getElementById(btnId)?.addEventListener('click', () => {
            const pw = document.getElementById(inputId);
            const ic = document.getElementById(eyeId);
            pw.type = pw.type === 'password' ? 'text' : 'password';
            ic.className = pw.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
        });
    }
    makeToggle('togglePwd1', 'password', 'eye1');
    makeToggle('togglePwd2', 'password_confirmation', 'eye2');

    // Loading state
    document.getElementById('registerForm')?.addEventListener('submit', () => {
        document.getElementById('regBtnText').textContent = 'Creating account...';
        document.getElementById('regBtnIcon').classList.add('hidden');
        document.getElementById('regBtnSpinner').classList.remove('hidden');
        document.getElementById('regBtn').disabled = true;
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\YEAR IV\SEMESTER I\WB-III\project\my-laravel-app\resources\views/auth/register.blade.php ENDPATH**/ ?>