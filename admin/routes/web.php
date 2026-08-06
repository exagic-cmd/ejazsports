<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BundleController;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/', 'HomeController@index')->name('home');

    //User Account Routes
    Route::resource('accounts','AccountController');

    Route::get('/edit-profile','HomeController@editProfileForm')->name('profile.edit');
    Route::post('/edit-profile','HomeController@profileUpdate')->name('profile.update');

    //Role Routes
    Route::resource('roles','RoleController');

    Route::get('/priceup-notifications','ProductController@getPriceUpNotification')->name('priceup-notification');

    //Permission Routes
    Route::resource('permissions','PermissionsController');

    //Activity log Routes
    Route::get('activity-log','HomeController@getActivityLog')->name('activity.index');
    Route::post('activity-log','HomeController@searchActivityLog')->name('activity.search');

    //Category Routes
    Route::resource('categories','CategoryController');
    Route::post('search-category-product','CategoryController@searchCategoryProduct')->name('categories.search-product');

    //Brand Routes
    Route::resource('brands','BrandController');
    Route::post('search-brand-product','BrandController@searchBrandProduct')->name('brands.search-product');


    //Supplier Routes
    Route::get('/supplier/name-suggestions','SupplierController@nameSuggestions')->name('supplier.name-suggestions');
    Route::resource('suppliers','SupplierController');
    Route::get('/show-supplier-brand-detail','SupplierController@getSupplierBrandDetail')->name('supplier.brand.detail');
    Route::get('/show-supplier-product-detail','SupplierController@getSupplierProductDetail')->name('supplier.product.detail');
    Route::get('/show-supplier-product-selected-detail','SupplierController@getSupplierProductSelectedDetail')->name('supplier.product.selected.detail');
    Route::get('/supplier/report-update','SupplierController@updateReport')->name('supplier.update-report');










    //Product Routes
    Route::post('/product/zero-stock-by-barcode', [ProductController::class, 'zeroStockByBarcode'])->name('product.zero-stock');
    Route::get('/product/generate-sku', [ProductController::class, 'generateAutoSku'])->name('product.generate.sku');
    Route::post('/admin/products/generate-bundles', [ProductController::class, 'generateBundles'])
    ->name('admin.products.generate-bundles');
    Route::resource('products','ProductController');
    Route::resource('colors','ColorController');
    Route::resource('sizes','SizeController');
    Route::get('check-product-barcode-availability','ProductController@checkProductBarcodeAvailability')->name('product.barcode.check');
    Route::get('product/search','ProductController@searchProduct')->name('product.search');
    Route::get('/ajax-product-search','ProductController@ajaxProductSearch')->name('product.ajax.search');
    Route::get('/add-product-order','ProductController@addProductOrder')->name('product.add.order');

    //Deal Routes
    Route::resource('deals','ProductDealController');
    Route::get('brand-products','ProductDealController@getBrandProducts')->name('brand.products');
    Route::get('product-variants','ProductDealController@getProductVariants')->name('product.variants');

    Route::post('product-addDeal','ProductDealController@getDealProduct')->name('product.addDeal');

    Route::get('/update-product-image','ProductController@updateImageModal')->name('product.image.modal');

    Route::post('/update-product-image','ProductController@updateImage')->name('product.image.update');

    Route::get('/remove-product-image','ProductController@removeImage')->name('product.image.remove');

    Route::get('/print-barcode/{id}','ProductController@printBarcode')->name('product.barcode.print');

     Route::get('/print-variant-barcode/{id}','ProductController@printVariantBarcode')->name('variant.barcode.print');

    //Manage Content
    //Manage Top Bar Content
    Route::resource('top-bar','TopBarContentController');
    //Manage Banner
    Route::resource('banners','BannerController');
    //Manage Promotion Banners
    Route::resource('promotion','PromotionController');

    //Manage Store
    Route::resource('stores','StoreController');

    //Manage Purchase Orders
    Route::resource('purchase-orders','PurchaseOrderController');
    Route::get('purchase-order/status-update','PurchaseOrderController@changePOStatus')->name('purchase-orders.change.status');

    Route::get('purchase-order/auto-brand-filter','PurchaseOrderController@getAutoBrandFilterPurchaseOrder')->name('purchase-orders.auto-brand-filter');

    Route::post('purchase-order/auto-brands','PurchaseOrderController@getAutoBrandsPurchaseOrder')->name('purchase-orders.auto-brands');

    Route::get('purchase-order/auto-brand-create-form/{id}','PurchaseOrderController@getAutoBrandPurchaseOrderForm')->name('purchase-orders.auto-brand-form');

    Route::get('purchase-order/auto-product-create-form','PurchaseOrderController@getAutoProductPurchaseOrderForm')->name('purchase-orders.auto-product-form');





    //Manage Receiving
    Route::resource('receiving','ReceivingController');
    Route::get('/show-po-detail','ReceivingController@getPurchaseOrderDetail')->name('receiving.po.detail');
    Route::get('/show-po-product-detail','ReceivingController@getPurhcaseOrderProductDetail')->name('receiving.po.product.detail');
    Route::get('receivings/status-update','ReceivingController@changeReceivingStatus')->name('receiving.change.status');
    Route::get('receivings/grn/{id}','ReceivingController@getGRN')->name('receiving.grn');
    Route::get('/receiving-add-product','ReceivingController@addNewProduct')->name('receiving.add.product');

    Route::get('receivings/in-complete','ReceivingController@getInCompleteReceivings')->name('receiving.incomplete');


    Route::get('receivings/in-complete/edit/{id}','ReceivingController@getInCompleteReceivingsEdit')->name('receiving.incomplete.edit');

     //Direct Purchasing
    Route::get('receivings/direct','ReceivingController@getDirectReceivingForm')->name('receiving.direct.form');
    Route::post('receivings/direct','ReceivingController@submitDirectReceivingForm')->name('receiving.direct.submit');
    Route::post('receivings/direct/product-search','ReceivingController@directReceivingProductSearch')->name('receiving.direct.product.search');

    Route::get('receivings/direct/product-scan','ReceivingController@directReceivingProductScan')->name('receiving.direct.product.scan');
    Route::post('receivings/direct/product-scan','ReceivingController@directReceivingProductSubmit')->name('receiving.direct.product.submit');

Route::get('/productsvarients', [ProductController::class, 'getproductswithvarients']);
//Bundles
Route::group(['middleware' => ['auth']], function() {
    Route::get('/bundles', [BundleController::class, 'index'])->name('bundles.index');
    Route::get('/bundles/search', [BundleController::class, 'search'])->name('bundles.search');
    Route::get('/bundles/create', [BundleController::class, 'create'])->name('bundles.create');
    Route::post('/bundles', [BundleController::class, 'store'])->name('bundles.store');
    Route::get('/bundles/{bundle}', [BundleController::class, 'show'])->name('bundles.show');
    Route::get('/bundles/{bundle}/edit', [BundleController::class, 'edit'])->name('bundles.edit');
    Route::put('/bundles/{bundle}', [BundleController::class, 'update'])->name('bundles.update');
      Route::post('/bundles/batch-delete', [BundleController::class, 'batchDelete'])
    ->name('bundles.batch-delete');
    Route::delete('/bundles/{bundle}', [BundleController::class, 'destroy'])->name('bundles.destroy');
Route::get('/bundle/products/{product}/variants', [BundleController::class, 'getProductVariants'])
    ->name('bundles.product-variants');

});

    //Return
    Route::resource('supplier-returns','SupplierReturnController');
    Route::get('supplier-returns/status-update','SupplierReturnController@changeReceivingStatus')->name('supplier-returns.change.status');

    Route::get('supplier-returns-incomplete','SupplierReturnController@getInComplete')->name('supplier-returns.in');

     //Direct Purchasing
    Route::get('supplier-returns/direct','SupplierReturnController@getDirectSupplierReturnForm')->name('supplier-returns.direct.form');
    Route::post('supplier-returns/direct','SupplierReturnController@submitDirectSupplierReturnForm')->name('supplier-returns.direct.submit');
    Route::post('supplier-returns/direct/product-search','SupplierReturnController@directSupplierReturnProductSearch')->name('supplier-returns.direct.product.search');

    Route::get('supplier-returns/direct/product-scan','SupplierReturnController@directSupplierReturnProductScan')->name('supplier-returns.direct.product.scan');
    Route::post('supplier-returns/direct/product-scan','SupplierReturnController@directSupplierReturnProductSubmit')->name('supplier-returns.direct.product.submit');

    //Manage Supplier Payments
    Route::resource('supplier-payments','SupplierPaymentController');
    Route::get('supplier-payment/unpaid-invoices','SupplierPaymentController@getSupplierUnPaidInvoices')->name('supplier-payments.unpaid-invoices');
    Route::get('supplier-payment/status-update','SupplierPaymentController@changePaymentStatus')->name('supplier-payments.change.status');

     Route::get('/cheaque-reminder','SupplierPaymentController@getCheaqueList')->name('supplier-payments.cheaque');


    //Manage Customer Payments
    Route::resource('customer-payments','CustomerPaymentController');
    Route::get('customer-payment/unpaid-invoices','CustomerPaymentController@getCustomerUnPaidInvoices')->name('customer-payments.unpaid-invoices');
    Route::get('customer-payment/status-update','CustomerPaymentController@changePaymentStatus')->name('customer-payments.change.status');
    Route::get('customer/search','CustomerController@searchCustomer')->name('customer.search');

    //Manage Store Supplies
    Route::resource('supplies','SupplyController');
    Route::get('/supply/add-brand','SupplyController@getBrandProduct')->name('supply.add.brand');
    Route::get('supply/status-update','SupplyController@changeSupplyStatus')->name('supply.change.status');
    Route::get('supply/receive/{id}','SupplyController@supplyReceivingForm')->name('supply.receiving.form');
    Route::post('supply/receive','SupplyController@addSupplyReceiving')->name('supply.receiving.store');

    //Manage Discounts
    Route::resource('discounts','DiscountController');

    //Manage Coupons
    Route::resource('coupons','CouponController');

    //Manage Areas
    Route::resource('areas','AreaController');

    //Manage Orders
    Route::resource('orders','OrderController');
     Route::get('search-order','OrderController@getFilterOrders')->name('orders.search');
    Route::get('/order/pending','OrderController@getPendingOrders')->name('orders.pending');
    Route::get('/new-order-booking','OrderController@newOrderBooking')->name('orders.new.booking');
    Route::get('/order/booked','OrderController@getBookedOrders')->name('orders.booked');
    Route::get('/un-booking','OrderController@unBookedOrder')->name('orders.unbooked');
    Route::get('/order/scanned','OrderController@getScannedOrders')->name('orders.scanned');
    Route::get('/order/scan/new','OrderController@addNewScan')->name('orders.scan.new');
    Route::get('/order/info','OrderController@showOrderInfo')->name('orders.info');
    Route::get('/order/product/scan','OrderController@scanProductOrder')->name('orders.product.scan');
    Route::post('/order/product/scan','OrderController@orderScanComplete')->name('orders.scan.complete');
    Route::get('/order/dispatched','OrderController@getDispatchedOrders')->name('orders.dispatched');
    Route::get('/order/dispatch/new','OrderController@addNewDispatch')->name('orders.dispatch.new');
    Route::get('/order/dispatch/scan','OrderController@scanDispatchOrder')->name('orders.dispatch.scan');
    Route::post('/order/dispatch/new','OrderController@orderDispatchComplete')->name('orders.dispatch.complete');
    Route::get('/order/cancel','OrderController@getCancelOrders')->name('orders.cancel');
    Route::get('/order/return','OrderController@getReturnOrders')->name('orders.return');
    Route::get('/order/complete','OrderController@getCompleteOrders')->name('orders.complete');

    Route::get('/return-order/{order_id}','OrderController@getreturnVoucher')->name('orders.return');
    Route::get('/return-order/{order_id}/download-pos','OrderController@downloadPosReturn')->name('orders.return.download_pos');

    Route::get('/order-print/{id}','OrderController@printOrder')->name('orders.print');

    Route::get('/order-print-a4/{id}','OrderController@printa4Order')->name('orders.a4');

     Route::get('/order-pdf/{id}','OrderController@pdfOrder')->name('orders.pdf');

Route::get('/order-view/{id}','OrderController@customerOrder')->name('orders.view.customer');


    //Manage Couriers
    Route::resource('couriers','CourierController');
    Route::get('/courier/handover-list','CourierController@getHandoverList')->name('couriers.handover');
    Route::get('/courier/handover/{handover}','CourierController@getHandoverDetail')->name('couriers.handover.detail');

    //Manage Expense
    Route::resource('expense','ExpenseController');
    Route::resource('expense-category','ExpenseCategoryController');

    //Manage Employees
    Route::resource('employees','EmployeeController');
    Route::get('/employee/report-update','EmployeeController@updateReport')->name('employee.update-report');

    //Manage Customer
    Route::get('/customer/name-suggestions','CustomerController@nameSuggestions')->name('customer.name-suggestions');
    Route::resource('customers','CustomerController');

    Route::get('/customer/report-update','CustomerController@updateReport')->name('customer.update-report');

    //Manage Complains
    Route::get('complains/pending','ComplainController@pendingComplains')->name('complain.pending');
    Route::get('complains/in-progress','ComplainController@inprogressComplains')->name('complain.inprogress');
    Route::get('complains/resolved','ComplainController@resolvedComplains')->name('complain.resolved');
    Route::get('complains/show/{id}','ComplainController@show')->name('complain.show');
    Route::get('complain/add-note','ComplainController@addNote')->name('complain.note.add');

    Route::get('complain/add-note','ComplainController@addNote')->name('complain.note.add');

    Route::get('complain/change-status','ComplainController@changeStatus')->name('complain.status.change');

    //Report Module
    Route::get('reports/graph','ReportController@getGraphReport')->name('report.graph');
     Route::get('reports/daily-graph','ReportController@getDailyGraphReport')->name('report.daily-graph');
    Route::get('reports/brands','ReportController@getBrandReportForm')->name('report.brand.form');
    Route::post('reports/brands','ReportController@getBrandReport')->name('report.brand');
    Route::get('reports/brand-daily-graph','ReportController@getBrandDailyGraphForm')->name('report.brand.graph.form');
    Route::post('reports/brand-daily-graph','ReportController@getBrandDailyGraph')->name('report.brand.graph');
    Route::get('reports/category','ReportController@getCategoryReportForm')->name('report.category.form');
    Route::post('reports/category','ReportController@getCategoryReport')->name('report.category');
    Route::get('reports/product','ReportController@getProductReportForm')->name('report.product.form');
    Route::post('reports/product','ReportController@getProductReport')->name('report.product');
    Route::get('reports/out-of-stock','ReportController@getOutOfStockProductsReportForm')->name('report.out-of-stock.products.form');
    Route::post('reports/out-of-stock','ReportController@getOutOfStockProductsReport')->name('report.out-of-stock.products');

    Route::get('reports/brand-available-inventory-form','ReportController@getBrandAvailableInventoryForm')->name('report.brand-available-inventory-form');
     Route::post('reports/brand-available-inventory','ReportController@getBrandAvailableInventory')->name('report.brand-available-inventory');

     Route::get('reports/brand-available-inventory/{brand_id}/{store_id}','ReportController@getSpecificBrandAvailableInventory')->name('report.specific-brand-available-inventory');


     Route::get('reports/stats','ReportController@getStatsReportForm')->name('report.stats.form');
    Route::post('reports/stats','ReportController@getStatsReport')->name('report.stats');

    ///////////////////////////////FOLLOW UP//////////////////////////

    Route::get('/follow-up/auto','FollowupController@getAutoFollowUp')->name('followup.auto');

    Route::get('/follow-up/upcoming','FollowupController@getUpComingFollowUp')->name('followup.upcoming');

    Route::get('/follow-up/expired','FollowupController@getExpiredFollowUp')->name('followup.expired');
     Route::get('/follow-up/complete','FollowupController@getCompleteFollowUp')->name('followup.complete');

    Route::get('/show_followup_history','FollowupController@showFollowUpHistory');

    Route::get('/delete_followup','FollowupController@deleteFollowUp');

    Route::get('/complete_followup','FollowupController@completeFollowUp');

    Route::get('/follow-up/create-followup','FollowupController@createFollowupView')->name('followup.create');

    Route::post('/follow-up/create-followup','FollowupController@createFollowup')->name('followup.store');

    Route::get('/update_followup','FollowupController@updateFollowupHistory')->name('followup.update');

    //Stock Audit
    Route::resource('stock-audits','StockAuditController');
    Route::get('stock-audit-product-scan','StockAuditController@getScanProductDetail')->name('stock-audit.product.scan');

    Route::get('stock-audit-variant-scan','StockAuditController@getScanVariantDetail')->name('stock-audit.variant.scan');
    Route::get('stock-audit/status/update','StockAuditController@approveAudit')->name('stock-audit.status.update');

});

require __DIR__.'/auth.php';
