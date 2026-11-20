<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Sign in — <?php echo e(config('app.name')); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="min-h-screen bg-ice-50 text-ink antialiased">
    <div class="grid min-h-screen lg:grid-cols-[1.05fr_0.95fr]">
        <section class="relative hidden overflow-hidden bg-navy-950 px-12 py-12 text-white lg:flex lg:flex-col lg:justify-between">
            <div class="absolute inset-0 opacity-70" style="background:
                radial-gradient(600px 280px at 20% 5%, rgba(41,159,242,.25), transparent 60%),
                radial-gradient(500px 300px at 95% 95%, rgba(63,180,255,.18), transparent 65%);"></div>
            <div class="relative">
                <div class="flex items-center gap-3">
                    <span class="brand-mark"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'snowflake','size' => 22]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'snowflake','size' => 22]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></span>
                    <div>
                        <p class="text-[14px] font-semibold">AC Management Portal</p>
                        <p class="text-[10px] uppercase tracking-[0.16em] text-[#8fb6d8]">Centralized HVAC operations</p>
                    </div>
                </div>

                <div class="mt-24 max-w-md">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[#7dc4f7]">Organization manager console</p>
                    <h1 class="mt-4 text-[38px] font-bold leading-[1.08] tracking-[-0.035em]">Every air conditioner.<br>One clear operating picture.</h1>
                    <p class="mt-5 text-[13.5px] leading-relaxed text-[#a9c7e1]">
                        Monitor reported AC state, device connectivity and operating duration across your portfolio — without confusing last-known state with physical confirmation.
                    </p>
                </div>
            </div>

            <div class="relative grid grid-cols-3 gap-3 text-[11px]">
                <div class="rounded-xl border border-white/10 bg-white/5 p-3.5">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'shield','size' => 18,'class' => 'text-[#7dc4f7]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'shield','size' => 18,'class' => 'text-[#7dc4f7]']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                    <p class="mt-2 font-semibold text-white">Policy-aware</p>
                    <p class="mt-0.5 text-[#8fb6d8]">Nearest explicit remote policy wins.</p>
                </div>
                <div class="rounded-xl border border-white/10 bg-white/5 p-3.5">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'activity','size' => 18,'class' => 'text-[#7dc4f7]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'activity','size' => 18,'class' => 'text-[#7dc4f7]']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                    <p class="mt-2 font-semibold text-white">Fact separation</p>
                    <p class="mt-0.5 text-[#8fb6d8]">Request, receipt, transmission, state.</p>
                </div>
                <div class="rounded-xl border border-white/10 bg-white/5 p-3.5">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'clock','size' => 18,'class' => 'text-[#7dc4f7]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'clock','size' => 18,'class' => 'text-[#7dc4f7]']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                    <p class="mt-2 font-semibold text-white">ON duration</p>
                    <p class="mt-0.5 text-[#8fb6d8]">Operating hours, never kWh.</p>
                </div>
            </div>
        </section>

        <section class="flex items-center justify-center px-5 py-10 sm:px-8">
            <div class="w-full max-w-[400px]">
                <div class="mb-8 flex items-center gap-3 lg:hidden">
                    <span class="brand-mark"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'snowflake','size' => 22]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'snowflake','size' => 22]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></span>
                    <div>
                        <p class="text-[14px] font-semibold text-ink">AC Management Portal</p>
                        <p class="text-[10px] uppercase tracking-[0.14em] text-muted">Manager console</p>
                    </div>
                </div>

                <div class="card card-pad sm:p-7">
                    <h2 class="text-[23px] font-bold tracking-[-0.025em]">Sign in</h2>
                    <p class="mt-1 text-[12.5px] text-ink-soft">Use your organization administrator account.</p>

                    <?php if($errors->any()): ?>
                        <div class="alert alert-error mt-5">
                            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'alert','size' => 16,'class' => 'mt-0.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'alert','size' => 16,'class' => 'mt-0.5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                            <p><?php echo e($errors->first()); ?></p>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo e(route('login.store')); ?>" class="mt-6 space-y-4">
                        <?php echo csrf_field(); ?>
                        <div>
                            <label for="email" class="label">Email address</label>
                            <input id="email" name="email" type="email" value="<?php echo e(old('email', 'admin@northwind.test')); ?>" class="input" autocomplete="username" required autofocus>
                        </div>
                        <div>
                            <label for="password" class="label">Password</label>
                            <input id="password" name="password" type="password" value="password" class="input" autocomplete="current-password" required>
                        </div>
                        <label class="flex items-center gap-2 text-[12px] text-ink-soft">
                            <input type="checkbox" name="remember" value="1" class="h-3.5 w-3.5 rounded border-line text-blue-600">
                            Keep me signed in
                        </label>

                        <button type="submit" class="btn btn-primary w-full">Sign in</button>
                    </form>

                    <div class="mt-6 rounded-xl border border-line bg-blue-50/60 p-3.5">
                        <p class="flex items-center gap-1.5 text-[10.5px] font-semibold uppercase tracking-[0.08em] text-blue-700">
                            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'info','size' => 13]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'info','size' => 13]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Development workspace
                        </p>
                        <p class="mt-1 text-[11.5px] leading-relaxed text-ink-soft">
                            Demo credentials are pre-filled. Seed data and simulator events are explicitly sample data and are not hardware validation.
                        </p>
                    </div>
                </div>

                <p class="mt-5 text-center text-[10.5px] text-muted">
                    <?php echo e($shellOrganization?->name ?? 'Organization-scoped access'); ?> · Tenant data is isolated server-side.
                </p>
            </div>
        </section>
    </div>
</body>
</html>
<?php /**PATH D:\PROJECTS\WebApps\ac-hvac\resources\views/auth/login.blade.php ENDPATH**/ ?>