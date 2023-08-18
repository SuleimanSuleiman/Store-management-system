<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\Categories\MainCategoryController;
use App\Http\Controllers\Categories\SubCategoryController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Review\ReviewController;
use Illuminate\Support\Facades\Route;



Route::apiResource('branch', BranchController::class);
Route::get('branch/{branch}/restoredeleted', [BranchController::class, 'restoredeletedFun']);

Route::apiResource('main-category', MainCategoryController::class);
Route::get('/{branch}/main-category', [MainCategoryController::class, 'AllMainSameBranch']);
Route::get('main-category/{category}/restoredeleted', [MainCategoryController::class, 'restoredeletedFun']);

Route::apiResource('sub-category', SubCategoryController::class);
Route::get('/sub-category/main-category/{category}', [SubCategoryController::class, 'AllSubOnly']);


Route::apiResource('{sub_category}/products', ProductController::class);
Route::get('/main-category/{category}/products', [ProductController::class, 'allProductSameMainCategory']);
Route::get('/all-product', [ProductController::class, 'allProduct']);
Route::get('/all-product-branch/{branch}', [ProductController::class, 'allProductSameBranch']);


Route::post('review/response', [ReviewController::class, 'ResponseAdmin']);