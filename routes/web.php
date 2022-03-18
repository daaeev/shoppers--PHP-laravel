<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\SiteController;

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

Route::get('/', [SiteController::class, 'index'])->name('index');
Route::get('/about', [SiteController::class, 'about'])->name('about');
Route::get('/cart', [SiteController::class, 'cart'])->name('cart');
Route::get('/cart/buy', [SiteController::class, 'buy'])->name('cart.but');
Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
Route::get('/catalog', [SiteController::class, 'catalog'])->name('catalog');
Route::get('/catalog/SLUG', [SiteController::class, 'single'])->name('catalog.single');
Route::get('/thanks', [SiteController::class, 'thanks'])->name('thanks');
