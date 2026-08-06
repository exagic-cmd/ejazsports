<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API;
use App\Http\Controllers\BundleController;
use App\Http\Controllers\POS_API;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('/v1')->group(function() {
    //get Master Data
    Route::get('/getHeaderFooterData',[API\HomeController::class,'getHeaderFooterData']);
    Route::post('/getMainSearchProductsAjax',[API\HomeController::class,'getSearchProductsAjax']);

    //Get Home Page Content
    Route::get('/getHomePageContent',[API\HomeController::class,'getHomePageContent']);

    //Get All Categories Page Content
    Route::get('/getAllCategoriesPageContent',[API\CategoryController::class,'getAllCategories']);
    Route::post('/getSearchCategories',[API\CategoryController::class,'searchCategories']);
    Route::post('/getCategoryProducts',[API\CategoryController::class,'getCategoryProducts']);
    Route::post('/getCategoryFilterProducts',[API\CategoryController::class,'getCategoryFilterProducts']);

    //Get All Brands Page Content
    Route::get('/getAllBrandsPageContent',[API\BrandController::class,'getAllBrands']);
    Route::post('/getSearchBrands',[API\BrandController::class,'searchBrands']);
    Route::post('/getBrandProducts',[API\BrandController::class,'getBrandProducts']);
    Route::post('/getBrandFilterProducts',[API\BrandController::class,'getBrandFilterProducts']);

    //Product Detail page
    Route::post('/getProductPageContent',[API\ProductController::class,'getProductPageContent']);
    Route::post('/getProductDetail',[API\ProductController::class,'getProductDetail']);

    //Customer Login
    Route::post('/getCustomerLogin',[API\HomeController::class,'getCustomerLogin']);
    Route::post('/getCustomerDashboardData',[API\HomeController::class,'getCustomerDashboardData']);
    Route::post('/profileUpdate',[API\HomeController::class,'profileUpdate']);
    Route::post('/trackOrder',[API\HomeController::class,'trackOrder']);

    Route::post('/addProductInWishlist',[API\HomeController::class,'addProductInWishlist']);

    Route::post('/getWishlist',[API\HomeController::class,'getWishlist']);
    Route::post('/removeProductFromWishlist',[API\HomeController::class,'removeProductFromWishlist']);

    //Order
    Route::post('/orderPlace',[API\OrderController::class,'orderPlace']);

    Route::post('/orderDetail',[API\OrderController::class,'orderDetail']);

    //Claim Form
    Route::post('/submitClaimForm',[API\HomeController::class,'submitClaimForm']);


    ///////////////////////////POS ROUTES/////////////////////////////////////
    //Agent Login
    Route::post('/getAgentLogin',[POS_API\HomeController::class,'getAgentLogin']);

    Route::post('/getPOSModuleData',[POS_API\POSController::class,'getPOSModuleData']);

    Route::post('/getBrandData',[POS_API\POSController::class,'getBrandData']);

    Route::post('/getCategoryData',[POS_API\POSController::class,'getCategoryData']);

    Route::post('/getSearchData',[POS_API\POSController::class,'getSearchData']);

    Route::post('/getCartData',[POS_API\POSController::class,'getCartData']);

    Route::post('/updatePayment',[POS_API\POSController::class,'updatePayment']);

    Route::post('/mannualReturnForm',[POS_API\POSController::class,'mannualReturnForm']);

    Route::post('/createSale',[POS_API\POSController::class,'createSale']);

    Route::post('/createReturn',[POS_API\POSController::class,'createReturn']);

    Route::post('/orderInfo',[POS_API\POSController::class,'orderInfo']);

Route::get('/bundles/{id}', [BundleController::class, 'getBundle'])->name('api.bundles.show');

    //Sales
    Route::post('/getSaleData',[POS_API\SaleController::class,'getSaleData']);

    Route::post('/getHoldList',[POS_API\SaleController::class,'getHoldList']);

    Route::post('/getReturnOrders',[POS_API\SaleController::class,'getReturnOrders']);

    Route::post('/getSearchOrder',[POS_API\SaleController::class,'getSearchOrder']);
    Route::post('/getOrderDetail',[POS_API\SaleController::class,'getOrderDetail']);

    Route::post('/updateCompleteReturnOrder',[POS_API\SaleController::class,'updateCompleteReturnOrder']);

    Route::post('/updatePartiallyReturnOrder',[POS_API\SaleController::class,'updatePartiallyReturnOrder']);

    //Customer
    Route::post('/getCustomerData',[POS_API\CustomerController::class,'getCustomerData']);

    Route::post('/getCustomerSearchData',[POS_API\CustomerController::class,'getCustomerSearchData']);
    Route::post('/getCustomerSuggestions',[POS_API\CustomerController::class,'getCustomerSuggestions']);

    Route::post('/getOrderSearchData',[POS_API\CustomerController::class,'getOrderSearchData']);

    Route::post('/getSpecificCustomerData',[POS_API\CustomerController::class,'getSpecificCustomerData']);

    Route::post('/createCustomer',[POS_API\CustomerController::class,'createCustomer']);


    //Product
    Route::post('/getProductData',[POS_API\ProductController::class,'getProductData']);
    Route::post('/getOutOfStockProductData',[POS_API\ProductController::class,'getOutOfStockProductData']);
    Route::post('/getProductDetailPOS',[POS_API\ProductController::class,'getProductDetail']);
     Route::post('/getProductVariantDetailPOS',[POS_API\ProductController::class,'getProductVariantDetail']);

    //Expense
    Route::post('/getExpenseData',[POS_API\ExpenseController::class,'getExpenseData']);
    Route::post('/createExpense',[POS_API\ExpenseController::class,'createExpense']);

    //Report
    Route::post('/getReportData',[POS_API\ReportController::class,'getReportData']);
    Route::post('/getCustomReportData',[POS_API\ReportController::class,'getCustomReportData']);

    //Cashier
    Route::post('/getCashierData',[POS_API\CashierController::class,'getCashierData']);
    Route::post('/createClosing',[POS_API\CashierController::class,'createClosing']);

    Route::get('get-product-detail',[POS_API\POSController::class,'getProductDetailByBarcode']);

});

