<?php

use App\Operation\Infrastructure\Http\Controllers\MakeOperationAction;
use Illuminate\Support\Facades\Route;

Route::post('/add', MakeOperationAction::class);
