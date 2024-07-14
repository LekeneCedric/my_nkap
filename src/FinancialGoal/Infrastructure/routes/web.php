<?php

use App\FinancialGoal\Infrastructure\Http\Controllers\DeleteFinancialGoalAction;
use App\FinancialGoal\Infrastructure\Http\Controllers\GetAllFinancialGoalAction;
use App\FinancialGoal\Infrastructure\Http\Controllers\MakeFinancialGoalAction;
use Illuminate\Support\Facades\Route;

Route::post('/save', MakeFinancialGoalAction::class);
Route::post('/delete', DeleteFinancialGoalAction::class);
Route::post('/all', GetAllFinancialGoalAction::class);
