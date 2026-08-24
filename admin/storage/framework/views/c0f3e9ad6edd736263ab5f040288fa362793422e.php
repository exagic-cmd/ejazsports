<?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <article class="itemlist">
        <div class="row align-items-center">
            <div class="col col-check flex-grow-0">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox">
                </div>
            </div>
            <div class="col-lg-3 col-sm-4 col-8 flex-grow-1 col-name">
                <a class="itemside" href="<?php echo e(route('products.show',$product->id)); ?>">
                    <div class="left">
                        <?php if($product->thumbnail): ?>
                            <img src="<?php echo e(asset('storage/'.$product->thumbnail->url)); ?>" class="img-sm img-thumbnail" alt="Item">
                        <?php else: ?>
                            <img src ="<?php echo e(asset('storage/default.jpeg')); ?>" class="img-sm img-thumbnail" alt="Item">

                        <?php endif; ?>
                    </div>
                    <div class="info">
                        <h6 class="mb-0"><?php echo e($product->title); ?></h6><br>
                        <small ><?php echo e($product->available_stock); ?> (stock)</small>
                        <?php if($product->have_variants): ?>
                            <small class="right-0"> <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo e(count($product->variants)); ?></b> (variants)</small>
                        <?php endif; ?>
                    </div>
                </a>
            </div>


            <div class="col-lg-2 col-sm-2 col-4 col-status">
                <span><?php echo e($product->brand ? $product->brand->title : ''); ?></span>
            </div>
            <div class="col-lg-2 col-sm-2 col-4 col-date">
                <span><?php if($product->categories): ?>  <?php $__currentLoopData = $product->categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <?php if($loop->last): ?> <b> <?php if($category->category): ?><?php echo e($category->category->title); ?> <?php endif; ?> </b> <?php else: ?> <b>  <?php if($category->category): ?> <?php echo e($category->category->title); ?> </b> , <?php endif; ?> <?php endif; ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> <?php endif; ?></span>
            </div>
            
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Pricing')): ?>

            <div class="col-lg-1 col-sm-2 col-4 col-price"><span>
                                <?php if($product->discount_status): ?> <strike style="color: #c4bbbb;"><?php echo e($product->price); ?></strike><br> <?php echo e($product->price - $product->discount_amount); ?> <?php else: ?>  <?php echo e($product->price); ?> <?php endif; ?>
                            </span></div>
                            
                            <?php endif; ?>

            <div class="col-lg-1 col-sm-2 col-4 col-status">
                <span class="badge rounded-pill <?php echo e($product->status ?'alert-success' : 'alert-danger'); ?>"><?php if($product->status == 1): ?>
                        Published
                    <?php elseif($product->status == 2): ?>
                        Dis continue
                    <?php else: ?>
                        Un Published
                    <?php endif; ?></span>
            </div>
            <div class="col-lg-2 col-sm-2 col-4 col-action text-end">
                
                <a href="<?php echo e(route('products.show',$product->id)); ?>" class="btn btn-sm font-sm btn-light rounded"> <i class="material-icons md-view_carousel"></i> Detail </a>
            </div>
        </div>
        <!-- row .// -->
    </article>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<!-- itemlist  .// -->
</div>
<!-- card-body end// -->
<?php /**PATH C:\xampp\htdocs\EjazSportsCode\ejazsports\admin\resources\views/catalog/product/search.blade.php ENDPATH**/ ?>