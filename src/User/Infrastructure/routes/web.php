<?php

use App\User\Infrastructure\Http\Controllers\RegisterUserAction;
use Illuminate\Support\Facades\Route;

Route::post('/register', RegisterUserAction::class);
