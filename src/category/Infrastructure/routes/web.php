<?php

use App\category\Infrastructure\Http\Controllers\SaveCategoryAction;
use Illuminate\Support\Facades\Route;

Route::post('/save', SaveCategoryAction::class);
