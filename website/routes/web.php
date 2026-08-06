<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\CustomerAuthController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/categories', [HomeController::class, 'getAllCategories'])->name('categories');
Route::get('/category-product/{id}', [HomeController::class, 'getCategoryProducts'])->name('categories.products');
Route::get('/brands', [HomeController::class, 'brands'])->name('brands');
Route::get('/brand-products/{id}', [HomeController::class, 'brandsProducts'])->name('brands.products');
Route::get('/product-detail/{id}', [HomeController::class, 'productDetail'])->name('products.detail');
Route::get('/cart/checkout', [CartController::class, 'checkout'])->name('checkout');
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/cart/items', [CartController::class, 'getCartItems'])->name('cart.items');
Route::post('/cart/place-order', [CartController::class, 'placeOrder'])->name('cart.placeOrder');
Route::get('/account', [HomeController::class, 'account'])->name('account');
Route::get('/page/track-order', [AboutController::class, 'track_order'])->name('track_order');
Route::get('/page/about', [AboutController::class, 'about'])->name('about');
Route::get('/page/faq', [AboutController::class, 'faq'])->name('faq');
Route::get('/page/terms', [AboutController::class, 'terms'])->name('terms');
Route::get('/layouts/header', [AboutController::class, 'headerData'])->name('header');

Route::get('/thank-you', [HomeController::class, 'thankYou'])->name('account.thankyou');


Route::get('/product/{slugOrId}', [ProductController::class, 'productDetail'])->name('productdetail');
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/my-orders', [HomeController::class, 'myOrders'])->name('account.orders.list');
Route::get('/account/orders/{id}', [HomeController::class, 'orderDetail'])->name('account.order');
Route::post('/account/update', [HomeController::class, 'updateProfile'])->name('account.update');

// Customer Authentication Routes
Route::get('/customer/login', [CustomerAuthController::class, 'showLoginForm'])->name('customer.login');
Route::post('/customer/login', [CustomerAuthController::class, 'login'])->name('customer.login.post');
Route::get('/customer/register', [CustomerAuthController::class, 'showRegisterForm'])->name('customer.register');
Route::post('/customer/register', [CustomerAuthController::class, 'register'])->name('customer.register.post');
Route::post('/customer/logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');

// Forgot Password Routes
Route::get('forgot-password', [CustomerAuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('forgot-password', [CustomerAuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('reset-password/{token}', [CustomerAuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('reset-password', [CustomerAuthController::class, 'resetPassword'])->name('password.update');

