<?php

use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\ColorController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\SizeController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ajax\CartController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\CouponController;


Route::get('/', [SiteController::class, 'index'])->name('home');
Route::get('/about', [SiteController::class, 'about'])->name('about');
Route::get('/cart', [SiteController::class, 'cart'])->name('cart');
Route::get('/cart/buy', [SiteController::class, 'buy'])->name('cart.but');
Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
Route::get('/catalog', [SiteController::class, 'catalog'])->name('catalog');
Route::get('/catalog/{product:slug}', [SiteController::class, 'single'])->name('catalog.single');
Route::get('/thanks', [SiteController::class, 'thanks'])->name('thanks');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [SiteController::class, 'profile'])->name('profile');
});

// ---ADMIN ROUTES---

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // ---CRUD ROUTES---

    Route::post('/user/role', [UserController::class, 'setRole'])->name('admin.users.role');

    Route::post('/product/create', [ProductController::class, 'createProduct'])->name('admin.product.create');
    Route::post('/product/delete', [ProductController::class, 'deleteProduct'])->name('admin.product.delete');
    Route::post('/product/edit', [ProductController::class, 'editProduct'])->name('admin.product.edit');

    Route::post('/coupon/create', [CouponController::class, 'createCoupon'])->name('admin.coupon.create');
    Route::post('/coupon/delete', [CouponController::class, 'deleteCoupon'])->name('admin.coupon.delete');

    Route::post('/category/create', [CategoryController::class, 'createCategory'])->name('admin.category.create');
    Route::post('/category/delete', [CategoryController::class, 'deleteCategory'])->name('admin.category.delete');

    Route::post('/color/create', [ColorController::class, 'createColor'])->name('admin.color.create');
    Route::post('/color/delete', [ColorController::class, 'deleteColor'])->name('admin.color.delete');

    Route::post('/size/create', [SizeController::class, 'createSize'])->name('admin.size.create');
    Route::post('/size/delete', [SizeController::class, 'deleteSize'])->name('admin.size.delete');

    // !!!CRUD ROUTES!!!

    Route::get('/users', [AdminController::class, 'usersList'])->name('admin.users');
    Route::get('/products', [AdminController::class, 'productsList'])->name('admin.products');
    Route::get('/product/create/form', [AdminController::class, 'productCreateForm'])->name('admin.product.create.form');
    Route::get('/product/edit/form', [AdminController::class, 'productEditForm'])->name('admin.product.edit.form');
    Route::get('/coupons', [AdminController::class, 'couponsList'])->name('admin.coupons');
    Route::get('/categories', [AdminController::class, 'categoriesList'])->name('admin.categories');
    Route::get('/colors', [AdminController::class, 'colorsList'])->name('admin.colors');
    Route::get('/sizes', [AdminController::class, 'sizesList'])->name('admin.sizes');
});

// !!!ADMIN ROUTES!!!

// ---AJAX---

Route::group(['prefix' => 'ajax'], function () {
    Route::get('/cart/add', [CartController::class, 'addToCart'])->name('ajax.cart.add');
    Route::get('/cart/remove', [CartController::class, 'removeFromCart'])->name('ajax.cart.remove');
    Route::get('/cart/update', [CartController::class, 'updateCart'])->name('ajax.cart.update');

    Route::get('/cart/product/plus', [CartController::class, 'productCountPlus'])->name('ajax.cart.product.plus');
    Route::get('/cart/product/minus', [CartController::class, 'productCountMinus'])->name('ajax.cart.product.minus');
});

// !!!AJAX!!!

Auth::routes(['verify' => true]);
