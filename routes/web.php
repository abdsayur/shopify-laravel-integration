<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopifyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {

    return view('welcome');
})->name('home');

Route::get('/connect', [ShopifyController::class, 'redirectToShopify'])->name('connect');
Route::get('/authenticate', [ShopifyController::class, 'handleShopifyCallback'])->name('authenticate');



Route::post('/push-to-shopify', [ProductController::class, 'pushToShopify'])->name('products.push');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
