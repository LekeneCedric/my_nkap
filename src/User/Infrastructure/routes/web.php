<?php

use App\User\Infrastructure\Http\Controllers\LoginAction;
use App\User\Infrastructure\Http\Controllers\LogoutAction;
use App\User\Infrastructure\Http\Controllers\RegisterUserAction;
use Illuminate\Support\Facades\Route;

Route::post('/register', RegisterUserAction::class);
Route::post('/login', LoginAction::class);
Route::post('/logout', LogoutAction::class)->middleware('auth:sanctum');
