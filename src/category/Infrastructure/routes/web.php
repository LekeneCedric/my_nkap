<?php

use App\category\Infrastructure\Http\Controllers\DeleteCategoryAction;
use App\category\Infrastructure\Http\Controllers\GetAllCategoryAction;
use App\category\Infrastructure\Http\Controllers\SaveCategoryAction;
use Illuminate\Support\Facades\Route;

Route::post('/save', SaveCategoryAction::class);
Route::post('/delete', DeleteCategoryAction::class);
Route::get('/all/{userId}', GetAllCategoryAction::class);
