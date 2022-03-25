<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\admin\ProductController;

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

Route::get('/', [SiteController::class, 'index'])->name('home');
Route::get('/about', [SiteController::class, 'about'])->name('about');
Route::get('/cart', [SiteController::class, 'cart'])->name('cart');
Route::get('/cart/buy', [SiteController::class, 'buy'])->name('cart.but');
Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
Route::get('/catalog', [SiteController::class, 'catalog'])->name('catalog');
Route::get('/catalog/SLUG', [SiteController::class, 'single'])->name('catalog.single');
Route::get('/thanks', [SiteController::class, 'thanks'])->name('thanks');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [SiteController::class, 'profile'])->name('profile');
});

// ---ADMIN ROUTES---

Route::middleware(['auth', 'admin'])->group(function () {
    // ---CRUD ROUTES---

    Route::post('/admin/user/role', [UserController::class, 'setRole'])->name('admin.users.role');

    Route::post('/admin/product/create', [ProductController::class, 'createProduct'])->name('admin.product.create');
    Route::post('/admin/product/delete', [ProductController::class, 'deleteProduct'])->name('admin.product.delete');

    // !!!CRUD ROUTES!!!

    Route::get('/admin/users', [AdminController::class, 'usersList'])->name('admin.users');
    Route::get('/admin/products', [AdminController::class, 'productsList'])->name('admin.products');
    Route::get('/admin/products/create/form', [AdminController::class, 'productCreateForm'])->name('admin.products.create.form');
    Route::get('/admin/categories', [AdminController::class, 'categoriesList'])->name('admin.categories');
    Route::get('/admin/colors', [AdminController::class, 'colorsList'])->name('admin.colors');
    Route::get('/admin/sizes', [AdminController::class, 'sizesList'])->name('admin.sizes');
});

// !!!ADMIN ROUTES!!!

Auth::routes(['verify' => true]);
