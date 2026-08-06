<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers;
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

Route::get('/qr','HomeController@qr');


Route::middleware(['auth'])->group(function () {

    //POS
    Route::get('/', 'POSController@index')->name('dashboard');

    Route::post('/getBrandData','POSController@getBrandData')->name('pos.brand.data');
    Route::post('/getCategoryData','POSController@getCategoryData')->name('pos.category.data');

    Route::post('/getSearchData','POSController@getSearchData')->name('pos.search.data');

    Route::post('/getCartData','POSController@getCartData')->name('pos.cart.data');

    Route::post('/updatePayment','POSController@updatePayment')->name('pos.update.payment');
    
    Route::post('/mannualReturnForm','POSController@mannualReturnForm')->name('pos.mannual.return.form');

    Route::post('/createSale','POSController@createSale')->name('pos.create.sale');
    
    Route::post('/createReturn','POSController@createReturn')->name('pos.create.return');

    Route::get('/print/order/{id}','POSController@printOrder')->name('pos.order.print');

    //Sales
    Route::get('/sales','SaleController@index')->name('sales.data');

    Route::post('/orderInfo','SaleController@orderInfo')->name('sales.order.info');
    
    Route::get('/hold-list','SaleController@getHold')->name('sale.hold');

    Route::post('/getHoldList','SaleController@getHoldList')->name('sale.hold.list');

    Route::get('/return-orders','SaleController@getReturnOrders')->name('sales.return.orders');

    Route::post('/search-order','SaleController@getSearchOrder')->name('sales.search.order');
 
    Route::post('/complete-return-order','SaleController@updateCompleteReturnOrder')->name('sales.complete.return.order');

    Route::post('/partially-return-order','SaleController@updatePartiallyReturnOrder')->name('sales.partially.return.order');
    //Customer
    Route::get('/customers','CustomerController@index')->name('customer.data');

    Route::post('/getCustomerSearchData','CustomerController@getCustomerSearchData')->name('customer.search.data');
    Route::post('/getCustomerSuggestions','CustomerController@getCustomerSuggestions')->name('customer.suggestions');

    
    Route::post('/getOrderSearchData','CustomerController@getOrderSearchData')->name('order.search.data');

    Route::post('/getSpecificCustomerData','CustomerController@getSpecificCustomerData')->name('customer.specific.data');

    Route::post('/customers','CustomerController@createCustomer')->name('customer.create');

    //Product
    Route::get('/products','ProductController@index')->name('product.data');
    Route::get('/out-of-stock-products','ProductController@outOfStockProducts')->name('product.out.of.stock');
    Route::post('/product-detail','ProductController@productDetail')->name('product.detail');
    Route::post('/product-variant-detail','ProductController@productVariantDetail')->name('product.variant.detail');

    //Expense
    Route::get('/expense','ExpenseController@index')->name('expense.data');
    Route::post('/expense','ExpenseController@createExpense')->name('expense.create');

    //Report Module
    Route::get('/reports','ReportController@index')->name('report.data');
    Route::post('/reports','ReportController@customReport')->name('report.custom');

    //Cashier Module
    Route::get('/cashier','CashierController@index')->name('cashier.data');
    Route::post('/closing','CashierController@createClosing')->name('closing.create');
    
    
    Route::post('/order-detail','SaleController@orderDetail')->name('order.detail');


});

require __DIR__.'/auth.php';
