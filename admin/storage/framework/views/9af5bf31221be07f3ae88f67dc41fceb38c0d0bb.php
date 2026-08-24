

<?php $__env->startSection('css'); ?>
<style>
    .img-wrap img:hover{
        -ms-transform: scale(1.2); /* IE 9 */
        -webkit-transform: scale(1.2); /* Safari 3-8 */
        transform: scale(1.2);
    }
    .img-wrap img {
        transition: 1s;
    }
</style>


    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    
    <link href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css" rel="stylesheet">

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<div class="content-header">
    <a href="javascript:history.back()"><i class="material-icons md-arrow_back"></i> Go back </a>
</div>
<div class="card mb-4">
    <div class="card-header bg-brand-2" style="height: 150px"></div>
    <div class="card-body">
        <div class="row">
            <div class="col-xl col-lg flex-grow-0" style="flex-basis: 230px">
                <div class="img-thumbnail shadow w-100 bg-white position-relative text-center" style="height: 190px; width: 200px; margin-top: -120px">
                    <img src="<?php echo e(asset('imgs/people/avatar-4.png')); ?>" style="max-width: 80%;!important;" class="center-xy img-fluid" alt="Logo Brand">
                </div>
            </div>
            <!--  col.// -->
            <div class="col-xl col-lg">
                <h3><?php echo e($employee->name); ?></h3>
                <p><span class="badge rounded-pill <?php echo e(($employee->status) ? 'alert-success' : 'alert-danger'); ?>"><?php echo e(($employee->status) ? 'Active' : 'InActive'); ?></span>
                   
                </p>
            </div>
            <!--  col.// -->
            <div class="col-xl-4 text-md-end">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit Employee')): ?>
                <a class="dropdown-item btn btn-primary d-inline" href="<?php echo e(route('employees.edit',$employee->id)); ?>">Edit info</a>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete Employee')): ?>
                <form class="<?php echo \Illuminate\Support\Arr::toCssClasses('d-inline') ?>" onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="<?php echo e(route('employees.destroy',$employee->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>

                <button style="width: min-content;" class="dropdown-item btn btn-instagram d-inline"  type="submit">Delete</button>
                </form>
                <?php endif; ?>

            </div>
            <!--  col.// -->
        </div>
        <!-- card-body.// -->
        <hr class="my-4">
        <div class="row g-4">
            <div class="col-md-12 col-lg-4 col-xl-2">
               
            </div>
            <!--  col.// -->
            <div class="col-sm-6 col-lg-4 col-xl-3">
                <h6>Basic</h6>
                <p>
                    
                    <b>Mobile Number: </b> <?php echo e($employee->mobile_number); ?> <br>
                    <b>Commission Per Retail: </b> <?php echo e($employee->com_per_retail); ?> % <br>
                    <b>Commission Per Whole </b> <?php echo e(number_format($employee->com_per_whole)); ?> % <br>

                </p>
            </div>
            <!--  col.// -->
            <br><br>
            
            <div class="row">
        <div class="col-lg-12">
            <div class="card card-body mb-4">
                
                <div class="row mb-4">
                    <label class="col-lg-3 col-form-label">Date Range<span style="color: red;"> *</span></label>
                    <div class="col-lg-9">
                        <input type="text" class="form-control "id="daterange-btn" value='<?php echo e(old('date_range')); ?>' name='date_range'>
                        <?php $__errorArgs = ['date_range'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="alert alert-danger"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <!-- col.// -->
                </div>

                <div class="form-actions" style="text-align: right">
                    <button onclick="generateReport()" type="submit" class=" btn btn-success-light"> <i class="fa fa-check" ></i> Generate</button>

                </div>
                
            </div>
        </div>
    </div>
    
    <div id="result">

           <div class="card-body" id="update-table">
            <div class="table-responsive" >
                <h3>Retail Orders</h3>
                <table id="myTable" class="table table-hover">
                    <thead>
                    <tr>
                        <th>#Sr</th>
                        <th>Date</th>
                        <th>Order #</th>
                      
                        <th scope="col">Order Amt.</th>
                        <th scope="col">whole - Retail Margin</th>
                        <th scope="col">Com</th>

                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;$t = 0;$orderSum=0;$marginSum = 0;?>
                    <?php $__currentLoopData = $employee->orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    
                    <?php if($o->customer_id == 1 && \Carbon\Carbon::parse($o->created_at)->isToday() ): ?>
                    <?php if($o->return_amount == 0 || $o->return_amount != $o->total_amount): ?>
                    <?php
                        $netOrderAmt = $o->total_amount - $o->return_amount;
                        $proportion = $o->total_amount > 0 ? (max(0, $netOrderAmt) / $o->total_amount) : 1;
                        $netMargin = round(($o->margin - $o->discount_amount) * $proportion);
                        $com = round($netMargin * ($employee->com_per_retail / 100));
                    ?>
                            <tr>

                                <td><?php echo e($sr++); ?></td>
                                <td><?php echo e(date('d-m-Y',strtotime($o->created_at))); ?></td>
                                
                              <td><a href="<?php echo e(route('orders.show',$o->id)); ?>" target="_blank"><?php echo e($o->order_no); ?></a></td>
                              
                             
                               <td><?php echo e(number_format($netOrderAmt)); ?>

                               <?php $orderSum += $netOrderAmt;?></td>
                               <td><?php echo e(number_format($netMargin)); ?>

                               <?php $marginSum += $netMargin;?></td>
                                 <td><?php echo e(number_format($com)); ?></td>
                                
                               
                               <?php $t += $com;?>
                            </tr>
                            <?php endif; ?>
                            
                            <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><b><?php echo e(number_format($orderSum)); ?></b></td>
                                <td><b><?php echo e(number_format($marginSum)); ?></b></td>
                                <td><b><?php echo e(number_format($t)); ?></b></td>
                            </tr>
                            
                           

                    </tbody>
                </table>
            </div>
            <!-- table-responsive //end -->
        </div>
        
       <div class="card-body" >
            <div class="table-responsive" >
                <h3>whole Sale Orders</h3>
                <table id="myTable1" class="table table-hover">
                    <thead>
                    <tr>
                        <th>#Sr</th>
                        <th>Date</th>
                        <th>Order #</th>
                        
                        <th scope="col">Order Amt.</th>
                        <th scope="col">Purchase - Whole Margin</th>
                        <th scope="col">Com</th>

                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;$t = 0;$orderSum=0;$marginSum=0;?>
                    
                    <?php $__currentLoopData = $employee->orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    
                    <?php if($o->customer_id != 1 && \Carbon\Carbon::parse($o->created_at)->isToday() && ($o->paid_amount == $o->total_amount || (($o->total_amount - $o->paid_amount) < 10 ))): ?>
                    
                    
                     <?php if($o->return_amount == 0 || $o->return_amount != $o->total_amount): ?>
                     <?php
                        $netOrderAmt = $o->total_amount - $o->return_amount;
                        $proportion = $o->total_amount > 0 ? (max(0, $netOrderAmt) / $o->total_amount) : 1;
                        $netMargin = round(($o->margin - $o->discount_amount) * $proportion);
                        $com = round($netMargin * ($employee->com_per_whole / 100));
                     ?>
                            <tr>

                                <td><?php echo e($sr++); ?></td>
                                <td><?php echo e(date('d-m-Y',strtotime($o->created_at))); ?></td>
                                
                              <td><a href="<?php echo e(route('orders.show',$o->id)); ?>" target="_blank"><?php echo e($o->order_no); ?></a></td>
                              
                               <td><?php echo e(number_format($netOrderAmt)); ?>

                               <?php $orderSum += $netOrderAmt;?></td>
                               
                               <td><?php echo e(number_format($netMargin)); ?>

                               <?php $marginSum += $netMargin;?></td>
                                <td><?php echo e(number_format($com)); ?></td>
                                
                               
                               <?php $t += $com;?>
                            </tr>
                            
                            <?php endif; ?>
                            
                            <?php endif; ?>
                            
                          
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                 <td><b><?php echo e(number_format($orderSum)); ?></b></td>
                                <td><b><?php echo e(number_format($marginSum)); ?></b></td>
                                <td><b><?php echo e(number_format($t)); ?></b></td>
                            </tr>
                    

                    </tbody>
                </table>
            </div>
            <!-- table-responsive //end -->
        </div>
        
        
        
        
        <div class="card-body" >
            <div class="table-responsive" >
                <h3>Credit Orders</h3>
                <table id="myTable2" class="table table-hover">
                    <thead>
                    <tr>
                        <th>#Sr</th>
                        <th>Date</th>
                        <th>Order #</th>
                        
                        <th scope="col">Order Amt.</th>
                        <th scope="col">Purchase - Whole Margin</th>
                        <th scope="col">Com</th>

                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;$t = 0;$orderSum=0;$marginSum=0;?>
                    
                    <?php $__currentLoopData = $employee->orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    
                    <?php if($o->customer_id != 1 && \Carbon\Carbon::parse($o->created_at)->isToday() && ($o->pay_amount < $o->total_amount || $o->paid_amount < $o->total_amount)): ?>
                     <?php if($o->return_amount == 0 || $o->return_amount != $o->total_amount): ?>
                     <?php
                        $netOrderAmt = $o->total_amount - $o->return_amount;
                        $proportion = $o->total_amount > 0 ? (max(0, $netOrderAmt) / $o->total_amount) : 1;
                        $netMargin = round(($o->margin - $o->discount_amount) * $proportion);
                        $com = round($netMargin * ($employee->com_per_whole / 100));
                     ?>
                            <tr>

                                <td><?php echo e($sr++); ?></td>
                                <td><?php echo e(date('d-m-Y',strtotime($o->created_at))); ?></td>
                                
                              <td><a href="<?php echo e(route('orders.show',$o->id)); ?>" target="_blank"><?php echo e($o->order_no); ?></a></td>
                              
                               <td><?php echo e(number_format($netOrderAmt)); ?>

                               <?php $orderSum += $netOrderAmt;?></td>
                               
                               <td><?php echo e(number_format($netMargin)); ?>

                               <?php $marginSum += $netMargin;?></td>
                                 <td><?php echo e(number_format($com)); ?></td>
                                
                               
                               <?php $t += $com;?>
                            </tr>
                            
                            <?php endif; ?>
                            
                            <?php endif; ?>
                            
                          
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                 <td><b><?php echo e(number_format($orderSum)); ?></b></td>
                                <td><b><?php echo e(number_format($marginSum)); ?></b></td>
                                <td><b><?php echo e(number_format($t)); ?></b></td>
                            </tr>
                    

                    </tbody>
                </table>
            </div>
            <!-- table-responsive //end -->
        </div>
        
        
         <div class="card-body" >
            <div class="table-responsive" >
                <h3>Return Orders</h3>
                <table id="myTable4" class="table table-hover">
                    <thead>
                    <tr>
                        <th>#Sr</th>
                        <th>Date</th>
                        <th>Order #</th>
                        
                        <th scope="col">Order Amt.</th>
                        <th scope="col">Return Amt.</th>
                        <th scope="col">Com</th>

                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;$t = 0;$orderSum=0;$marginSum=0;?>
                    
                    <?php $__currentLoopData = $returnOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $rate = ($o->customer_id == 1) ? $employee->com_per_retail : $employee->com_per_whole;
                        $proportion = $o->total_amount > 0 ? ($o->return_amount / $o->total_amount) : 1;
                        $netMargin = round(($o->margin - $o->discount_amount) * $proportion);
                        $com = -round($netMargin * ($rate / 100));
                    ?>
                            <tr>

                                <td><?php echo e($sr++); ?></td>
                                <td><?php echo e(date('d-m-Y',strtotime($o->return_date ?? $o->created_at))); ?></td>
                                
                              <td><a href="<?php echo e(route('orders.show',$o->id)); ?>" target="_blank"><?php echo e($o->order_no); ?></a></td>
                              
                               <td><?php echo e(number_format(($o->total_amount ))); ?>

                               <?php $orderSum += ($o->total_amount);?></td>
                                
                               <td><?php echo e(number_format($o->return_amount)); ?>

                               <?php $marginSum += ($o->return_amount);?></td>
                                 <td><?php echo e($com); ?></td>
                                
                               <?php $t += $com;?>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                 <td><b><?php echo e(number_format($orderSum)); ?></b></td>
                                <td><b><?php echo e(number_format($marginSum)); ?></b></td>
                                <td><b><?php echo e(number_format($t)); ?></b></td>
                            </tr>
                    

                    </tbody>
                </table>
            </div>
            <!-- table-responsive //end -->
        </div>
        
        
        <div class="card-body" >
            <div class="table-responsive" >
                <h3>Customer Payments</h3>
                <table id="myTable3" class="table table-hover">
                    <thead>
                    <tr>
                        <th>#Sr</th>
                        <th scope="col">Date</th>
                        <th scope="col">Customer </th>
                        <th scope="col">Received Amount</th>
                        <th scope="col">Payment Method</th>
                        <th scope="col">Created by</th>
                        <th scope="col">Approved By</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;?>
                    <?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sP): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <tr>

                            <td><?php echo e($sr++); ?></td>
                            <td><?php echo e(date('d-m-Y',strtotime($sP->date))); ?></td>

                            <td><?php echo e($sP->customer ? $sP->customer->first_name : ''); ?></td>
                           
                            <td style="text-align: center;"><?php echo e(number_format($sP->amount)); ?></td>
                            <td>
                                <?php if($sP->payment_method == \App\Models\CustomerPayment::CASH): ?>
                                    <span class="badge rounded-pill  alert-success">
                                        CASH
                                </span>
                                <?php elseif($sP->payment_method == \App\Models\CustomerPayment::BANK_TRANSFER): ?>
                                    <span class="badge rounded-pill  alert-success">
                                        BANK TRANSFER
                                </span>
                                    <?php elseif($sP->payment_method == \App\Models\CustomerPayment::CHEQUE): ?>
                                        <span class="badge rounded-pill  alert-success">
                                        CHEQUE
                                </span>
                                    <?php endif; ?>
                            </td>
                            <td><?php echo e($sP->createdBy ? $sP->createdBy->name : ''); ?></td>
                            <td><?php echo e($sP->approvedBy ? $sP->approvedBy->name : ''); ?></td>
                            <td>

                                <?php if($sP->status == \App\Models\CustomerPayment::APPROVAL_PENDING): ?>
                                    <span class="badge rounded-pill  alert-danger">
                                        APPROVAL PENDING
                                </span>
                                <?php elseif($sP->status == \App\Models\CustomerPayment::APPROVED): ?>
                                    <span class="badge rounded-pill  alert-success">
                                        APPROVED
                                </span>
                                <?php endif; ?>
                            </td>


                            <td class="text-end">

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('View Customer Payment')): ?>
                                    <a href="<?php echo e(route('customer-payments.show',$sP->id)); ?>" class="btn btn-md rounded font-sm">Detail</a>
                                <?php endif; ?>
                                
                                    <div class="dropdown">
                                        <a href="#" data-bs-toggle="dropdown" class="btn btn-light rounded btn-sm font-sm"> <i class="material-icons md-more_horiz"></i> </a>
                                        <div class="dropdown-menu">
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit Customer Payment')): ?>
                                                <a class="dropdown-item" href="<?php echo e(route('customer-payments.edit',$sP->id)); ?>">Edit info</a>
                                            <?php endif; ?>

                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete Customer Payment')): ?>
                                                <form onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="<?php echo e(route('customer-payments.destroy',$sP->id)); ?>" method="POST">
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
        
        </div>

           

            
        </div>
        <!--  row.// -->
    </div>
    <!--  card-body.// -->
</div>
<!--  card.// -->

<!--  card.// -->

<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>

<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.print.min.js"></script>

<script>
    function initEmployeeTables() {
        ['#myTable', '#myTable1', '#myTable2', '#myTable3', '#myTable4'].forEach(function(tableId) {
            if (!$(tableId).length) {
                return;
            }

            if ($.fn.DataTable.isDataTable(tableId)) {
                $(tableId).DataTable().destroy();
            }

            $(tableId).DataTable({
                ordering: false,
                sorting: false,
                paging: true,
                pageLength: 50,
                info: false,
                searching: true,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        title: '<?php echo e($employee->name); ?> Ledger'
                    },
                    {
                        extend: 'pdf',
                        title: '<?php echo e($employee->name); ?> Ledger'
                    },
                ]
            });
        });
    }

    $(document).ready(function () {
        initEmployeeTables();
    });
</script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>



        $('#daterange-btn').daterangepicker(
            {
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                startDate: moment(),
                endDate: moment()
            },
            function (start, end) {
                $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }
        );
        
        
        function generateReport() {
             
        date_range = $('#daterange-btn').val();
        employee_id = '<?php echo e($employee->id); ?>';
        
        $.ajax({
            url: "<?php echo e(route('employee.update-report')); ?>",
            type: 'GET',
            data: {date_range: date_range,employee_id:employee_id},
            success: function (data) {
                document.getElementById('result').innerHTML = data;
                initEmployeeTables();
            }
        });
                  

        }
    </script>

    <?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\EjazSportsCode\ejazsports\admin\resources\views/employee/show.blade.php ENDPATH**/ ?>