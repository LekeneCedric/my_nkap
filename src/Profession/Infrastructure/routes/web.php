<?php

use App\Profession\Infrastructure\Http\Controllers\GetAllProfessionAction;
use Illuminate\Support\Facades\Route;

Route::get('/all', GetAllProfessionAction::class);
