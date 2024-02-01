<?php

use App\Account\Infrastructure\Http\Controllers\SaveAccountAction;
use Illuminate\Support\Facades\Route;

Route::post('/save', SaveAccountAction::class);
