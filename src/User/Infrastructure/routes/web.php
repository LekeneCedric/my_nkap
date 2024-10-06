<?php

use App\User\Infrastructure\Http\Controllers\LoginAction;
use App\User\Infrastructure\Http\Controllers\LogoutAction;
use App\User\Infrastructure\Http\Controllers\RecoverPasswordAction;
use App\User\Infrastructure\Http\Controllers\RegisterAction;
use App\User\Infrastructure\Http\Controllers\SendRecoverPasswordCodeAction;
use App\User\Infrastructure\Http\Controllers\VerificationAccountAction;
use Illuminate\Support\Facades\Route;

Route::post('/register', RegisterAction::class);
Route::post('/login', LoginAction::class);
Route::post('/logout', LogoutAction::class)->middleware('auth:sanctum');
Route::post('/send-recover-password-code', SendRecoverPasswordCodeAction::class);
Route::post('/recover-password', RecoverPasswordAction::class);
Route::post('/verification-account', VerificationAccountAction::class);
