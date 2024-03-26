<?php

use App\FinancialGoal\Infrastructure\Http\Controllers\MakeFinancialGoalAction;
use Illuminate\Support\Facades\Route;

Route::post('/save', MakeFinancialGoalAction::class);
