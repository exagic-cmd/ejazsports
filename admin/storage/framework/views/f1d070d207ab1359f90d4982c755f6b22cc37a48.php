<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Admin | Ejaz Sports</title>
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:title" content="" />
    <meta property="og:type" content="" />
    <meta property="og:url" content="" />
    <meta property="og:image" content="" />
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo e(asset('imgs/theme/favicon.svg')); ?>" />
    <!-- Template CSS -->
    <link href="<?php echo e(asset('css/main.css?v=1.0.1')); ?>" rel="stylesheet" type="text/css" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />

    <link href="//cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    <?php
    function current_page($uri = "/") {
        return strstr(request()->path(), $uri);
    }

    function home_page() {
        if(request()->path()=="/")
            return true;
        else
            return false;
    }
    ?>

    <?php echo $__env->yieldContent('css'); ?>
</head>

<body>
<div class="screen-overlay"></div>

<?php echo $__env->make('layouts.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<main class="main-wrap" id="app">
    <header class="main-header navbar">
        <div class="col-search">
            <!--<form class="searchform">-->
            <!--    <div class="input-group">-->
            <!--        <input list="search_terms" type="text" class="form-control" placeholder="Search term" />-->
            <!--        <button class="btn btn-light bg" type="button"><i class="material-icons md-search"></i></button>-->
            <!--    </div>-->
            <!--    <datalist id="search_terms">-->
            <!--        <option value="Products"></option>-->
            <!--        <option value="New orders"></option>-->
            <!--        <option value="Apple iphone"></option>-->
            <!--        <option value="Ahmed Hassan"></option>-->
            <!--    </datalist>-->
            <!--</form>-->
        </div>
        <div class="col-nav">
            <button class="btn btn-icon btn-mobile me-auto" data-trigger="#offcanvas_aside"><i class="material-icons md-apps"></i></button>
            <ul class="nav">






                <li class="nav-item">
                    <a class="nav-link btn-icon darkmode" href="#"> <i class="material-icons md-nights_stay"></i> </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="requestfullscreen nav-link btn-icon"><i class="material-icons md-cast"></i></a>
                </li>

                <li class="dropdown nav-item">
                    <a class="dropdown-toggle" data-bs-toggle="dropdown" href="#" id="dropdownAccount" aria-expanded="false"> <img class="img-xs rounded-circle" src="<?php echo e(asset('imgs/people/avatar-2.png')); ?>" alt="User" /></a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownAccount">
                        <a class="dropdown-item" href="<?php echo e(route('profile.edit')); ?>"><i class="material-icons md-perm_identity"></i>Edit Profile</a>

                        <div class="dropdown-divider"></div>


                        <form method="POST" id="logout-form" action="/logout" class="text-xs font-semibold text-blue-500 ml-6">
                            <?php echo csrf_field(); ?>
                        </form>
                        <a class="dropdown-item text-danger" href="#" onclick="document.getElementById('logout-form').submit();"><i class="material-icons md-exit_to_app"></i>Logout</a>

                    </div>
                </li>
            </ul>
        </div>
    </header>
    <section class="content-main">

        <?php echo $__env->yieldContent('content'); ?>

    </section>
    <!-- content-main end// -->
    <footer class="main-footer font-xs">
        <div class="row pb-30 pt-15">
            <div class="col-sm-6">
                <script>
                    document.write(new Date().getFullYear());
                </script>
                ©, Ejaz Sports .
            </div>
            <div class="col-sm-6">
                <div class="text-sm-end">All rights reserved</div>
            </div>
        </div>
    </footer>
</main>
<script src="<?php echo e(asset('js/vendors/jquery-3.6.0.min.js')); ?>"></script>
<script src="<?php echo e(asset('js/vendors/bootstrap.bundle.min.js')); ?>"></script>
<script src="<?php echo e(asset('js/vendors/select2.min.js')); ?>"></script>
<script src="<?php echo e(asset('js/vendors/perfect-scrollbar.js')); ?>"></script>
<script src="<?php echo e(asset('js/vendors/jquery.fullscreen.min.js')); ?>"></script>
<script src="<?php echo e(asset('js/vendors/chart.js')); ?>"></script>
<!-- Main Script -->
<script src="<?php echo e(asset('js/main.js?v=1.0')); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset('js/custom-chart.js')); ?>" type="text/javascript"></script>

<script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<?php echo $__env->yieldContent('js'); ?>

<?php if(session('message')): ?>
    <script type="text/javascript">
        toastr.info("<?php echo e(session('message')); ?>");
    </script>
<?php endif; ?>

<script>
    toastr.options.progressBar = true;
    toastr.options.timeOut = '2000';

    $(document).ready(function() {
        $('.select2').select2();
    });
    $( document ).ready(function() {
        $('input').attr('autocomplete','false');
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>





<!-- Reminder Modal -->
<div class="modal fade" id="globalReminderModal" tabindex="-1" aria-labelledby="globalReminderModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="globalReminderModalLabel">Daily Reminder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="globalReminderModalBody">
                <!-- Content injected via JS -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnDismissReminder">Acknowledge / Dismiss</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let currentReminderId = null;
        let currentReminderSlot = null;

        function checkReminders() {
            $.ajax({
                url: '<?php echo e(route("reminders.check")); ?>',
                type: 'GET',
                success: function(response) {
                    if (response.due && response.due.length > 0) {
                        let reminder = response.due[0]; // Show one by one
                        currentReminderId = reminder.id;
                        currentReminderSlot = reminder.slot;

                        let html = '<h4>' + reminder.title + '</h4>';
                        if (reminder.description) {
                            html += '<p>' + reminder.description + '</p>';
                        }
                        
                        $('#globalReminderModalBody').html(html);
                        $('#globalReminderModal').modal('show');
                    }
                }
            });
        }

        // Check on load
        setTimeout(checkReminders, 2000);
        // Check every 1 minute
        setInterval(checkReminders, 60000);

        $('#btnDismissReminder').click(function() {
            if (currentReminderId && currentReminderSlot) {
                $.ajax({
                    url: '<?php echo e(route("reminders.dismiss")); ?>',
                    type: 'POST',
                    data: {
                        id: currentReminderId,
                        slot: currentReminderSlot,
                        _token: '<?php echo e(csrf_token()); ?>'
                    },
                    success: function(response) {
                        $('#globalReminderModal').modal('hide');
                        currentReminderId = null;
                        currentReminderSlot = null;
                        
                        // Check again in case there are more
                        setTimeout(checkReminders, 1000);
                    }
                });
            }
        });
    });
</script>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\EjazSportsCode\ejazsports\admin\resources\views/layouts/app.blade.php ENDPATH**/ ?>