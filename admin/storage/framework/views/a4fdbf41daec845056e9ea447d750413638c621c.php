


<?php $__env->startSection('content'); ?>


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">All Employees</h2>
            <p>employees information.</p>
        </div>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Create Employee')): ?>
        <div>
            <a  href="<?php echo e(route('employees.create')); ?>" class="btn btn-primary"><i class="text-muted material-icons md-post_add"></i>Add New</a>
        </div>
            <?php endif; ?>

    </div>

    <?php if(session()->has('message')): ?>
        <div class="alert alert-success text-center">
            <?php echo e(session()->get('message')); ?>

        </div>
    <?php endif; ?>

    <div class="alert alert-success alert-div text-center" style="display: none;">

    </div>

    <div class="card mb-4">

        <!-- card-header end// -->
        <div class="card-body" id="update-table">
            <div class="table-responsive" >
                <table id="myTable" class="table table-hover">
                    <thead>
                    <tr>
                        <th>#Sr</th>
                        <th>Full Name</th>
                        <th scope="col">Mobile Number</th>
                        <th scope="col">Com Per Retail</th>
                        <th scope="col">Com Per Whole</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;?>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            <tr>

                                <td><?php echo e($sr++); ?></td>
                                
                                <td><b><?php echo e($employee->name); ?></b></td>
                                <td><?php echo e($employee->mobile_number); ?></td>
                                <td><?php echo e($employee->com_per_retail); ?> %</td>
                                <td><?php echo e($employee->com_per_whole); ?> %</td>
                                <td><span class="badge rounded-pill <?php echo e(($employee->status) ? 'alert-success' : 'alert-danger'); ?>"><?php echo e(($employee->status) ? 'Active' : 'InActive'); ?></span></td>



                                <td class="text-end">
                                    
                                     <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('View Employee')): ?>
                                    <a href="<?php echo e(route('employees.show',$employee->id)); ?>" class="btn btn-md rounded font-sm">Detail</a>
                                <?php endif; ?>

                                        <div class="dropdown">
                                        <a href="#" data-bs-toggle="dropdown" class="btn btn-light rounded btn-sm font-sm"> <i class="material-icons md-more_horiz"></i> </a>
                                        <div class="dropdown-menu">
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit Employee')): ?>
                                            <a class="dropdown-item" href="<?php echo e(route('employees.edit',$employee->id)); ?>">Edit info</a>
                                            <?php endif; ?>

                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete Employee')): ?>
                                            <form onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="<?php echo e(route('employees.destroy',$employee->id)); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>

                                                <button class="dropdown-item text-danger" onclick="return confirm('Are you sure?')"  type="submit">Delete</button>
                                            </form>
                                                <?php endif; ?>
                                        </div>
                                    </div>
                                    <!-- dropdown //end -->
                                </td>

                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </tbody>
                </table>
            </div>
            <!-- table-responsive //end -->
        </div>
        <!-- card-body end// -->
    </div>





<?php $__env->stopSection(); ?>


<?php $__env->startSection('js'); ?>
    <script>
        $(document).ready( function () {
            $('#myTable').DataTable({
                'ordering': false, 'sorting' : false, 'paging' : true,'pageLength' : 50, 'info' : false, 'searching':true
            });
        } );

        setTimeout(function() {
            $('.alert').fadeOut('fast');
        }, 1000);
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\EjazSportsCode\ejazsports\admin\resources\views/employee/index.blade.php ENDPATH**/ ?>