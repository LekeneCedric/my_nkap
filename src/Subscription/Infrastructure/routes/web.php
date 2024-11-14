<?php

use App\Subscription\Infrastructure\Http\Controllers\GetAllSubscriptionAction;
use App\Subscription\Infrastructure\Http\Controllers\SubscriptionAction;
use Illuminate\Support\Facades\Route;

Route::post('/subscribe', SubscriptionAction::class);
Route::post('/all', GetAllSubscriptionAction::class);
