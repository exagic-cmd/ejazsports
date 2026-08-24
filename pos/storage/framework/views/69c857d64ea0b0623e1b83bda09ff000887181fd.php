<?php $products = (array) $result->data->products;?>
<?php $bundles = (array) $result->data->bundles;?>
<?php if(count($products) == 0 && count($bundles) == 0): ?>
    <span style="text-align: center;color: red"> No result found...</span>
<?php endif; ?>
<?php $__currentLoopData = $result->data->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pro): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<?php if($pro->variants): ?>
    <?php $__currentLoopData = $pro->variants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div>

                <div class="product-layout" onclick="addToCart(<?php echo e($pro->id); ?>,<?php echo e($v->id); ?>)">


                            <div class="product-thumb">

                                <div class="ribbon-wrapper">

                                    <div class="ribbon"><?php echo e($v->available_stock); ?></div>
                                </div>


                                <?php if($pro->thumbnail): ?>

                                    <img src="<?php echo e(env('BACKEND_IMAGE_URL').$pro->thumbnail->url); ?>">

                                 <?php else: ?>
                                <img src="<?php echo e(asset('images/download.webp')); ?>">

                                <?php endif; ?>

                            </div>



                            <div class="product-name"  title="<?php echo e($pro->title); ?>">
                                <?php echo e(strlen($pro->title) > 40 ? substr($pro->title, 0, 40) . '...' : $pro->title); ?><br>
                                <span style=" color: #db324d;"> <?php echo e($v->shade ? $v->shade : ''); ?>  <?php echo e($v->size ? ' - '.$v->size : ''); ?> </span>
                            </div>

                            <?php if($result->data->priceShown): ?>


                                <div class="product-price">
                                                <span class="price">
                                                       Rs.<?php echo e(number_format($v->additional_price)); ?>

                                                </span>

                                </div>


                            <?php endif; ?>

                            <!--<?php if($pro->discount_status): ?>-->
                            <!--    <div class="product-price"><span class="price price-cross">-->
                            <!--                Rs.<?php echo e(number_format($pro->price)); ?>-->
                            <!--            </span> <span class="special-price">-->
                            <!--                Rs.<?php echo e(number_format($pro->price - $pro->discount_amount)); ?>-->
                            <!--            </span></div>-->
                            <!--<?php else: ?>-->
                            <!--    <div class="product-price">-->
                            <!--                    <span class="price">-->
                            <!--                           Rs.<?php echo e(number_format($pro->price)); ?>-->
                            <!--                    </span>-->

                            <!--    </div>-->
                            <!--<?php endif; ?>-->


                        </div>
                </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php else: ?>

      <div>



                <div class="product-layout" onclick="addToCart(<?php echo e($pro->id); ?>,0)">

                            <div class="product-thumb">

                                <div class="ribbon-wrapper">

                                    <div class="ribbon"><?php echo e($pro->available_stock); ?></div>
                                </div>


                                <?php if($pro->thumbnail): ?>

                                    <img src="<?php echo e(env('BACKEND_IMAGE_URL').$pro->thumbnail->url); ?>">

                                 <?php else: ?>
                                <img src="<?php echo e(asset('images/download.webp')); ?>">

                                <?php endif; ?>
                            </div>



                            <div class="product-name"  title="<?php echo e($pro->title); ?>">
                                <?php echo e(strlen($pro->title) > 40 ? substr($pro->title, 0, 40) . '...' : $pro->title); ?><br>

                            </div>

                            <?php if($result->data->priceShown): ?>

                            <?php if($pro->discount_status): ?>
                                <div class="product-price"><span class="price price-cross">
                                            Rs.<?php echo e(number_format($pro->price)); ?>

                                        </span> <span class="special-price">
                                            Rs.<?php echo e(number_format($pro->price - $pro->discount_amount)); ?>

                                        </span></div>
                            <?php else: ?>
                                <div class="product-price">
                                                <span class="price">
                                                       Rs.<?php echo e(number_format($pro->price)); ?>

                                                </span>

                                </div>
                            <?php endif; ?>

                            <?php endif; ?>

                            <!--<?php if($pro->discount_status): ?>-->
                            <!--    <div class="product-price"><span class="price price-cross">-->
                            <!--                Rs.<?php echo e(number_format($pro->price)); ?>-->
                            <!--            </span> <span class="special-price">-->
                            <!--                Rs.<?php echo e(number_format($pro->price - $pro->discount_amount)); ?>-->
                            <!--            </span></div>-->
                            <!--<?php else: ?>-->
                            <!--    <div class="product-price">-->
                            <!--                    <span class="price">-->
                            <!--                           Rs.<?php echo e(number_format($pro->price)); ?>-->
                            <!--                    </span>-->

                            <!--    </div>-->
                            <!--<?php endif; ?>-->


                        </div>
                </div>

    <?php endif; ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<!-- Add bundle display section -->
<!-- Bundle display section - matching product layout but keeping original attributes -->
<?php $__currentLoopData = $result->data->bundles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bundle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="product-layout" onclick="addToCart(<?php echo e($bundle->id); ?>, 0, true)">
        <div class="product-thumb">
            <!-- Simple ribbon (like products have) -->
            <div class="ribbon-wrapper">
                <div class="ribbon">Bundle</div>
            </div>

            <?php
                // Original image handling - unchanged
                $bundleImage = $bundle->firstImage ?? ($bundle->images[0] ?? null);
            ?>

            <?php if($bundleImage && isset($bundleImage->path)): ?>
                <img src="<?php echo e(env('BACKEND_IMAGE_URL')); ?><?php echo e($bundleImage->path); ?>">
            <?php else: ?>
                <img src="<?php echo e(asset('images/download.webp')); ?>">
            <?php endif; ?>
        </div>

        <div class="product-name" title="<?php echo e($bundle->name ?? 'Bundle'); ?>">
            <?php echo e(strlen($bundle->name) > 40 ? substr($bundle->name, 0, 40) . '...' : $bundle->name); ?>

            <?php if(isset($bundle->short_desc)): ?>
                <br><small><?php echo e($bundle->short_desc); ?></small>
            <?php endif; ?>
        </div>

        <?php if($result->data->priceShown && isset($bundle->additional_price)): ?>
            <div class="product-price">
                <span class="price">
                    Rs.<?php echo e(number_format($bundle->additional_price)); ?>

                </span>
            </div>
        <?php endif; ?>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


<input type="hidden" id="route" name="route" value="1">
<?php /**PATH C:\xampp\htdocs\EjazSportsCode\ejazsports\pos\resources\views/pos/search-product.blade.php ENDPATH**/ ?>