

<?php $__env->startSection('content'); ?>

        <div class="content-header">
            <div>
                <h2 class="content-title card-title">Products List</h2>
                <p>latest product details.</p>
            </div>
            <div>


                <a href="#" class="btn btn-warning btn-sm rounded" data-bs-toggle="modal" data-bs-target="#zeroStockModal">Zero Stock by Barcode</a>
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Create Product')): ?>
                <a href="<?php echo e(route('products.create')); ?>" class="btn btn-primary btn-sm rounded">Create new</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="card mb-4">
            <header class="card-header">
                <div class="row align-items-center">
                    
                    <div class="col-lg-3 col-12 me-auto mb-3">

                        <input type="text" id="searchbox" placeholder="Search By Name,ID ..." class="form-control">

                    </div>

                    <div class="col-md-3 col-12 me-auto mb-md-0 mb-3">
                        <select class="form-select select2" id="category_id">
                            <option selected="" value="">All category</option>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cat->id); ?>"><?php echo e($cat->title); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3 col-12 me-auto mb-md-0 mb-3">
                        <select class="form-select select2" id="brand_id">
                            <option selected="" value="">All Brand</option>
                            <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($b->id); ?>"><?php echo e($b->title); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3 col-12 me-auto mb-md-0 mb-3">
                        <select class="form-select select2" id="status">
                            <option selected="" value="">Status</option>
                            <option value="1">Active</option>
                            <option value="0">Disabled</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-12 me-auto mb-md-0 mb-3">
                        <button type="button" class="form-control btn btn-primary search" >Search</button>
                    </div>
                </div>
            </header>
            <!-- card-header end// -->
            <div class="card-body" id="result">

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
                                <?php if($product->discount_status): ?> <strike style="color: #c4bbbb;"><?php echo e(number_format($product->price)); ?></strike><br> <?php echo e(number_format($product->price - $product->discount_amount)); ?> <?php else: ?>  <?php echo e(number_format($product->price)); ?> <?php endif; ?>
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
                        
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('View Product')): ?>
                        <div class="col-lg-2 col-sm-2 col-4 col-action text-end">

                            <a href="<?php echo e(route('products.show',$product->id)); ?>" class="btn btn-sm font-sm btn-light rounded"> <i class="material-icons md-view_carousel"></i> Detail </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <!-- row .// -->
                </article>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <!-- itemlist  .// -->
            </div>
            <!-- card-body end// -->
        </div>
        <!-- card end// -->
        <div class="pagination-area mt-30 mb-50">
            <nav aria-label="Page navigation example" id="link">
                <?php echo e($products->links()); ?>

            </nav>
        </div>

    <!-- Zero Stock Modal -->
    <div class="modal fade" id="zeroStockModal" tabindex="-1" aria-labelledby="zeroStockModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="zeroStockModalLabel">Zero Stock by Barcode</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="zeroStockBarcode" class="form-label">Scan / Enter Main Product Barcode or Code</label>
                        <input type="text" class="form-control" id="zeroStockBarcode" autofocus>
                    </div>
                    <div id="zeroStockMessage" class="alert d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btnZeroStock">Zero Out Stock</button>
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
    $('.select2').select2();
    $(document).on('click','.search',function(e) {

        category_id = $('#category_id').val();
        brand_id = $('#brand_id').val();
        status = $('#status').val();
        searchbox = $('#searchbox').val();

        if(category_id || brand_id || status || searchbox) {

                        $.ajax({
                            url: "<?php echo e(route('product.search')); ?>",
                            type: 'GET',
                            data: {category_id: category_id,brand_id:brand_id, status:status,searchbox:searchbox},
                            success: function (data) {
                                document.getElementById('result').innerHTML = data;
                                document.getElementById('link').innerHTML = '';
                            }
                        });
                  
             
        }
        else {
            toastr.warning('Please select any option');
        }
    });

    // Zero Stock Feature
    $('#zeroStockModal').on('shown.bs.modal', function () {
        $('#zeroStockBarcode').focus();
    });

    $('#btnZeroStock').click(function() {
        var barcode = $('#zeroStockBarcode').val();
        if(!barcode) return;
        
        var btn = $(this);
        btn.text('Processing...').prop('disabled', true);
        var msgDiv = $('#zeroStockMessage');
        
        $.ajax({
            url: "<?php echo e(route('product.zero-stock')); ?>",
            type: 'POST',
            data: {
                barcode: barcode,
                _token: '<?php echo e(csrf_token()); ?>'
            },
            success: function(response) {
                msgDiv.removeClass('d-none alert-danger').addClass('alert-success').html(response.message);
                $('#zeroStockBarcode').val('').focus();
                btn.text('Zero Out Stock').prop('disabled', false);
                setTimeout(function(){ msgDiv.addClass('d-none'); }, 3000);
            },
            error: function(xhr) {
                var errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Error processing barcode';
                msgDiv.removeClass('d-none alert-success').addClass('alert-danger').html(errorMsg);
                $('#zeroStockBarcode').val('').focus();
                btn.text('Zero Out Stock').prop('disabled', false);
            }
        });
    });
    $('#zeroStockBarcode').keypress(function(e){
        if(e.which == 13){
            $('#btnZeroStock').click();
        }
    });
   </script>
 <?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\EjazSportsCode\ejazsports\admin\resources\views/catalog/product/index.blade.php ENDPATH**/ ?>