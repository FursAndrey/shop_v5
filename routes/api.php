<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\ImageController;
use App\Http\Controllers\Admin\OptionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\SkuController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::apiResource('categories', CategoryController::class);
Route::get('/category/all', [CategoryController::class, 'categoryAll'])->name('categoryAll');
Route::apiResource('currencies', CurrencyController::class);
Route::get('/currency/all', [CurrencyController::class, 'currencyAll'])->name('currencyAll');
Route::apiResource('products', ProductController::class);
Route::apiResource('properties', PropertyController::class);
Route::apiResource('options', OptionController::class);
Route::apiResource('skus', SkuController::class);
Route::delete('/images/{image}', [ImageController::class, 'destroyOne']);
Route::delete('/images/all/{sku}', [ImageController::class, 'destroyAll']);