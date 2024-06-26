<?php

use App\Account\Infrastructure\Http\Controllers\DeleteAccountAction;
use App\Account\Infrastructure\Http\Controllers\GetAllAccountAction;
use App\Account\Infrastructure\Http\Controllers\SaveAccountAction;
use Illuminate\Support\Facades\Route;

Route::post('/save', SaveAccountAction::class);
Route::post('/delete', DeleteAccountAction::class);
Route::get('/all/{userId}', GetAllAccountAction::class);
