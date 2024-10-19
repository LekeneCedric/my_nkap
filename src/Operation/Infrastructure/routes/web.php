<?php

use App\Operation\Infrastructure\Http\Controllers\DeleteOperationAction;
use App\Operation\Infrastructure\Http\Controllers\FilterAccountOperationsAction;
use App\Operation\Infrastructure\Http\Controllers\MakeAIOperationAction;
use App\Operation\Infrastructure\Http\Controllers\MakeManyOperationsAction;
use App\Operation\Infrastructure\Http\Controllers\MakeOperationAction;
use Illuminate\Support\Facades\Route;

Route::post('/add', MakeOperationAction::class);
Route::post('/add-many', MakeManyOperationsAction::class);
Route::post('/delete', DeleteOperationAction::class);
Route::post('/filter', FilterAccountOperationsAction::class);
Route::post('/ai/add', MakeAIOperationAction::class);
