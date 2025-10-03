<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Základní RESTful routy pro správu produktů.
Route::apiResource('products', ProductController::class);
// Speciální routy pro získání historie pro konkrétní produkt.
// Parametr {product} je automaticky navázán na ID produktu.
Route::get('products/{product}/price-history', [ProductController::class, 'priceHistory']);
Route::get('products/{product}/stock-history', [ProductController::class, 'stockHistory']);
