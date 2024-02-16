<?php

use App\Operation\Infrastructure\Http\Controllers\DeleteOperationAction;
use App\Operation\Infrastructure\Http\Controllers\MakeOperationAction;
use Illuminate\Support\Facades\Route;

Route::post('/add', MakeOperationAction::class);
Route::post('/delete', DeleteOperationAction::class);
