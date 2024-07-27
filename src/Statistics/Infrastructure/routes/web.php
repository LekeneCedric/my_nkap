<?php

use App\Statistics\Infrastructure\Http\Controllers\GetAllMonthlyCategoryStatisticsAction;
use App\Statistics\Infrastructure\Http\Controllers\GetAllMonthlyStatisticsAction;
use Illuminate\Support\Facades\Route;

Route::get('monthly-statistics/all', GetAllMonthlyStatisticsAction::class);
Route::get('monthly-category-statistics/all', GetAllMonthlyCategoryStatisticsAction::class);
