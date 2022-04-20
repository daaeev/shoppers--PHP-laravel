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
use App\Http\Controllers\ajax\CouponController as AjaxCouponController;
use App\Http\Controllers\admin\TeammateController;
use App\Http\Controllers\ajax\SubscribeController;
use App\Http\Controllers\admin\NewsController;
use App\Http\Controllers\users\MessageController;
use App\Http\Controllers\admin\MessageController as AdminMessageController;
use App\Http\Controllers\admin\ExchangeController;


Route::get('/', [SiteController::class, 'index'])->name('home');
Route::get('/about', [SiteController::class, 'about'])->name('about');
Route::get('/cart', [SiteController::class, 'cart'])->name('cart');
Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
Route::get('/catalog', [SiteController::class, 'catalog'])->name('catalog');
Route::get('/catalog/{product:slug}', [SiteController::class, 'single'])->name('catalog.single');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [SiteController::class, 'profile'])->name('profile');

    Route::post('/news/subscribe', [SubscribeController::class, 'createSub'])->name('news.sub');
    Route::get('/news/unsubscribe', [SubscribeController::class, 'unsubUser'])->name('news.unsub');

    Route::post('/message/create', [MessageController::class, 'createMessage'])->name('message.create');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/cart/buy', [SiteController::class, 'buy'])->middleware('haveProductsInCart')->name('cart.buy');
    Route::get('/payment/thanks', [SiteController::class, 'payThanks'])->name('pay.thanks');
    Route::get('/payment/error', [SiteController::class, 'payError'])->name('pay.error');
});

// ---ADMIN ROUTES---

Route::middleware(['can:isAdmin'])->prefix('admin')->group(function () {
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

    Route::post('/team/create', [TeammateController::class, 'createTeammate'])->name('admin.team.create');
    Route::post('/team/delete', [TeammateController::class, 'deleteTeammate'])->name('admin.team.delete');
    Route::post('/team/edit', [TeammateController::class, 'editTeammate'])->name('admin.team.edit');

    Route::post('/news/create', [NewsController::class, 'createNews'])->name('admin.news.create');
    Route::post('/news/send', [NewsController::class, 'sendNews'])->name('admin.news.send');

    Route::post('/message/set-status/answered', [AdminMessageController::class, 'setAnsweredStatus'])->name('admin.message.status-answered');
    Route::get('/messages/clear', [AdminMessageController::class, 'deleteAnswered'])->name('admin.messages.clear');

    Route::get('/exchange/update', [ExchangeController::class, 'updateExchangeRates'])->name('admin.exchange.update');

    // !!!CRUD ROUTES!!!

    Route::get('/users', [AdminController::class, 'usersList'])->name('admin.users');
    Route::get('/products', [AdminController::class, 'productsList'])->name('admin.products');
    Route::get('/product/create/form', [AdminController::class, 'productCreateForm'])->name('admin.product.create.form');
    Route::get('/product/edit/form', [AdminController::class, 'productEditForm'])->name('admin.product.edit.form');
    Route::get('/coupons', [AdminController::class, 'couponsList'])->name('admin.coupons');
    Route::get('/categories', [AdminController::class, 'categoriesList'])->name('admin.categories');
    Route::get('/colors', [AdminController::class, 'colorsList'])->name('admin.colors');
    Route::get('/sizes', [AdminController::class, 'sizesList'])->name('admin.sizes');
    Route::get('/team', [AdminController::class, 'teamList'])->name('admin.team');
    Route::get('/team/create/form', [AdminController::class, 'teamCreateForm'])->name('admin.team.create.form');
    Route::get('/team/edit/form', [AdminController::class, 'teamEditForm'])->name('admin.team.edit.form');
    Route::get('/news', [AdminController::class, 'newsList'])->name('admin.news');
    Route::get('/news/create/form', [AdminController::class, 'newsCreateForm'])->name('admin.news.create.form');
    Route::get('/messages', [AdminController::class, 'messagesList'])->name('admin.messages');
    Route::get('/exchange', [AdminController::class, 'exchangeList'])->name('admin.exchange');

});

// !!!ADMIN ROUTES!!!

// ---AJAX---

Route::group(['prefix' => 'ajax'], function () {
    Route::get('/cart/add', [CartController::class, 'addToCart'])->name('ajax.cart.add');
    Route::get('/cart/remove', [CartController::class, 'removeFromCart'])->name('ajax.cart.remove');
    Route::get('/cart/update', [CartController::class, 'updateCart'])->name('ajax.cart.update');

    Route::get('/cart/product/plus', [CartController::class, 'productCountPlus'])->name('ajax.cart.product.plus');
    Route::get('/cart/product/minus', [CartController::class, 'productCountMinus'])->name('ajax.cart.product.minus');

    Route::post('/coupon/activate', [AjaxCouponController::class, 'activateCoupon'])
        ->name('ajax.coupon.activate')
        ->middleware(['auth']);
});

// !!!AJAX!!!

Auth::routes(['verify' => true]);
