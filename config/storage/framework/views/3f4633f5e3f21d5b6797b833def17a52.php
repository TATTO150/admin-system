<div>
    <?php if (isset($component)) { $__componentOriginal1a4a318d932e02d86670f282a316cd31 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1a4a318d932e02d86670f282a316cd31 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-section','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
         <?php $__env->slot('title', null, []); ?> 
            <?php echo e(__('Autenticación de dos factores')); ?>

         <?php $__env->endSlot(); ?>

         <?php $__env->slot('description', null, []); ?> 
            <?php echo e(__('Agregue seguridad adicional a su cuenta utilizando la autenticación de dos factores.')); ?>

         <?php $__env->endSlot(); ?>

         <?php $__env->slot('content', null, []); ?> 
            <h3 class="text-lg font-medium text-gray-100">
                <!--[if BLOCK]><![endif]--><?php if($this->enabled): ?>
                    <!--[if BLOCK]><![endif]--><?php if($showingConfirmation): ?>
                        <?php echo e(__('Termine de habilitar la autenticación de dos factores.')); ?>

                    <?php else: ?>
                        <?php echo e(__('Ha habilitado la autenticación de dos factores.')); ?>

                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <?php else: ?>
                    <?php echo e(__('No ha habilitado la autenticación de dos factores.')); ?>

                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </h3>

            <div class="mt-3 max-w-xl text-sm text-gray-300">
                <p>
                    <?php echo e(__('Cuando la autenticación de dos factores está habilitada, se le solicitará un token aleatorio seguro durante la autenticación. Puede recuperar este token desde la aplicación Google Authenticator de su teléfono.')); ?>

                </p>
            </div>

            <!--[if BLOCK]><![endif]--><?php if($this->enabled): ?>
                <!--[if BLOCK]><![endif]--><?php if($showingQrCode): ?>
                    <div class="mt-4 max-w-xl text-sm text-gray-300">
                        <p class="font-semibold">
                            <!--[if BLOCK]><![endif]--><?php if($showingConfirmation): ?>
                                <?php echo e(__('Para terminar de habilitar la autenticación de dos factores, escanee el siguiente código QR usando la aplicación de autenticación de google "Authenticator" de su teléfono o ingrese la clave de configuración y proporcione el código OTP generado.')); ?>

                            <?php else: ?>
                                <?php echo e(__('La autenticación de dos factores ahora está habilitada. Escanee el siguiente código QR usando la aplicación de autenticación de su teléfono o ingrese la clave de configuración.')); ?>

                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </p>
                    </div>

                    <div class="mt-4 p-2 inline-block bg-white">
                        <?php echo $this->user->twoFactorQrCodeSvg(); ?>

                    </div>

                    <div class="mt-4 max-w-xl text-sm text-gray-300">
                        <p class="font-semibold">
                            <?php echo e(__('Clave de configuración')); ?>: <?php echo e(decrypt($this->user->two_factor_secret)); ?>

                        </p>
                    </div>

                    <!--[if BLOCK]><![endif]--><?php if($showingConfirmation): ?>
                        <div class="mt-4">
                            <?php if (isset($component)) { $__componentOriginald8ba2b4c22a13c55321e34443c386276 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald8ba2b4c22a13c55321e34443c386276 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.label','data' => ['for' => 'code','value' => ''.e(__('Código')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'code','value' => ''.e(__('Código')).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald8ba2b4c22a13c55321e34443c386276)): ?>
<?php $attributes = $__attributesOriginald8ba2b4c22a13c55321e34443c386276; ?>
<?php unset($__attributesOriginald8ba2b4c22a13c55321e34443c386276); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald8ba2b4c22a13c55321e34443c386276)): ?>
<?php $component = $__componentOriginald8ba2b4c22a13c55321e34443c386276; ?>
<?php unset($__componentOriginald8ba2b4c22a13c55321e34443c386276); ?>
<?php endif; ?>

                            <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['id' => 'code','type' => 'text','name' => 'code','class' => 'block mt-1 w-1/2','inputmode' => 'numeric','autofocus' => true,'autocomplete' => 'one-time-code','wire:model' => 'code','wire:keydown.enter' => 'confirmTwoFactorAuthentication']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'code','type' => 'text','name' => 'code','class' => 'block mt-1 w-1/2','inputmode' => 'numeric','autofocus' => true,'autocomplete' => 'one-time-code','wire:model' => 'code','wire:keydown.enter' => 'confirmTwoFactorAuthentication']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $attributes = $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $component = $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>

                            <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['for' => 'code','class' => 'mt-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'code','class' => 'mt-2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <!--[if BLOCK]><![endif]--><?php if($showingRecoveryCodes): ?>
                    <div class="mt-4 max-w-xl text-sm text-gray-300">
                        <p class="font-semibold">
                            <?php echo e(__('Guarde estos códigos de recuperación en un administrador de contraseñas seguro. Se pueden utilizar para recuperar el acceso a su cuenta si pierde su dispositivo de autenticación de dos factores.')); ?>

                        </p>
                    </div>

                    <div class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-gray-800 text-gray-100 rounded-lg">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = json_decode(decrypt($this->user->two_factor_recovery_codes), true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div><?php echo e($code); ?></div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <div class="mt-4">
                        <!--[if BLOCK]><![endif]--><?php if(Auth::user()->Id_Rol == 3): ?>
                            <a href="<?php echo e(route('autoregistro-notificacion')); ?>" class="ms-4 inline-flex items-center px-4 py-2 bg-white border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <?php echo e(__('Finalizar')); ?>

                            </a>
                        <?php else: ?>
                            <a href="<?php echo e(route('dashboard')); ?>" 
                               class="ms-4 inline-flex items-center px-4 py-2 bg-white border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
                               onclick="event.preventDefault(); document.getElementById('success-form').submit();">
                                <?php echo e(__('Finalizar')); ?>

                            </a>
                            <form id="success-form" action="<?php echo e(route('dashboard')); ?>" method="POST" style="display: none;">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('GET'); ?>
                                <input type="hidden" name="success" value="Código OTP restablecido correctamente">
                            </form>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    
                    <!--[if BLOCK]><![endif]--><?php if(session('success')): ?>
                        <div class="mt-2 text-green-500">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <div class="mt-5">
                <!--[if BLOCK]><![endif]--><?php if(! $this->enabled): ?>
                    <?php if (isset($component)) { $__componentOriginalbec74c427ea01267d1faf57b533fd04e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbec74c427ea01267d1faf57b533fd04e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.confirms-password','data' => ['wire:then' => 'enableTwoFactorAuthentication']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('confirms-password'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:then' => 'enableTwoFactorAuthentication']); ?>
                        <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'button','wire:loading.attr' => 'disabled']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','wire:loading.attr' => 'disabled']); ?>
                            <?php echo e(__('Ver código QR')); ?>

                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbec74c427ea01267d1faf57b533fd04e)): ?>
<?php $attributes = $__attributesOriginalbec74c427ea01267d1faf57b533fd04e; ?>
<?php unset($__attributesOriginalbec74c427ea01267d1faf57b533fd04e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbec74c427ea01267d1faf57b533fd04e)): ?>
<?php $component = $__componentOriginalbec74c427ea01267d1faf57b533fd04e; ?>
<?php unset($__componentOriginalbec74c427ea01267d1faf57b533fd04e); ?>
<?php endif; ?>
                    <form method="POST" action="<?php echo e(route('logout')); ?>" class="inline">
                        <?php echo csrf_field(); ?>
            
                        <button type="submit" class="ms-4 inline-flex items-center px-4 py-2 bg-white border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <?php echo e(__('Cancelar')); ?>

                        </button>
                    </form>
                <?php else: ?>
                    <!--[if BLOCK]><![endif]--><?php if($showingRecoveryCodes): ?>
                        <?php if (isset($component)) { $__componentOriginalbec74c427ea01267d1faf57b533fd04e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbec74c427ea01267d1faf57b533fd04e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.confirms-password','data' => ['wire:then' => 'regenerateRecoveryCodes']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('confirms-password'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:then' => 'regenerateRecoveryCodes']); ?>
                            <?php if (isset($component)) { $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.secondary-button','data' => ['class' => 'me-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('secondary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'me-3']); ?>
                                <?php echo e(__('Regenerar códigos de recuperación')); ?>

                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $attributes = $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $component = $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbec74c427ea01267d1faf57b533fd04e)): ?>
<?php $attributes = $__attributesOriginalbec74c427ea01267d1faf57b533fd04e; ?>
<?php unset($__attributesOriginalbec74c427ea01267d1faf57b533fd04e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbec74c427ea01267d1faf57b533fd04e)): ?>
<?php $component = $__componentOriginalbec74c427ea01267d1faf57b533fd04e; ?>
<?php unset($__componentOriginalbec74c427ea01267d1faf57b533fd04e); ?>
<?php endif; ?>
                    <?php elseif($showingConfirmation): ?>
                        <?php if (isset($component)) { $__componentOriginalbec74c427ea01267d1faf57b533fd04e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbec74c427ea01267d1faf57b533fd04e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.confirms-password','data' => ['wire:then' => 'confirmTwoFactorAuthentication']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('confirms-password'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:then' => 'confirmTwoFactorAuthentication']); ?>
                            <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'button','class' => 'me-3','wire:loading.attr' => 'disabled']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','class' => 'me-3','wire:loading.attr' => 'disabled']); ?>
                                <?php echo e(__('Confirmar')); ?>

                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbec74c427ea01267d1faf57b533fd04e)): ?>
<?php $attributes = $__attributesOriginalbec74c427ea01267d1faf57b533fd04e; ?>
<?php unset($__attributesOriginalbec74c427ea01267d1faf57b533fd04e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbec74c427ea01267d1faf57b533fd04e)): ?>
<?php $component = $__componentOriginalbec74c427ea01267d1faf57b533fd04e; ?>
<?php unset($__componentOriginalbec74c427ea01267d1faf57b533fd04e); ?>
<?php endif; ?>
                    <?php else: ?>
                        <?php if (isset($component)) { $__componentOriginalbec74c427ea01267d1faf57b533fd04e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbec74c427ea01267d1faf57b533fd04e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.confirms-password','data' => ['wire:then' => 'showRecoveryCodes']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('confirms-password'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:then' => 'showRecoveryCodes']); ?>
                            <?php if (isset($component)) { $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.secondary-button','data' => ['class' => 'me-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('secondary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'me-3']); ?>
                                <?php echo e(__('Mostrar códigos de recuperación')); ?>

                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $attributes = $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $component = $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbec74c427ea01267d1faf57b533fd04e)): ?>
<?php $attributes = $__attributesOriginalbec74c427ea01267d1faf57b533fd04e; ?>
<?php unset($__attributesOriginalbec74c427ea01267d1faf57b533fd04e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbec74c427ea01267d1faf57b533fd04e)): ?>
<?php $component = $__componentOriginalbec74c427ea01267d1faf57b533fd04e; ?>
<?php unset($__componentOriginalbec74c427ea01267d1faf57b533fd04e); ?>
<?php endif; ?>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
         <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1a4a318d932e02d86670f282a316cd31)): ?>
<?php $attributes = $__attributesOriginal1a4a318d932e02d86670f282a316cd31; ?>
<?php unset($__attributesOriginal1a4a318d932e02d86670f282a316cd31); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1a4a318d932e02d86670f282a316cd31)): ?>
<?php $component = $__componentOriginal1a4a318d932e02d86670f282a316cd31; ?>
<?php unset($__componentOriginal1a4a318d932e02d86670f282a316cd31); ?>
<?php endif; ?>
</div>
<?php /**PATH C:\Users\carri\OneDrive\Escritorio\proyect\admin-system\resources\views/profile/two-factor-authentication-form.blade.php ENDPATH**/ ?>