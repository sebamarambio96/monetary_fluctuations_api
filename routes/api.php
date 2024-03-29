<?php

use App\Http\Controllers\DataCollectorController;
use App\Http\Controllers\InterestCurrencyController;
use App\Http\Controllers\InterestYearController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/get-currency-values/{currencyName}/', [DataCollectorController::class, 'getCurrencyValues']);


// Endpoints Extra

Route::get('/interest-years', [InterestYearController::class, 'getActiveYears']);

Route::get('/interest-currency', [InterestCurrencyController::class, 'getCurrencyYears']);

Route::get('/all-interest-data', [DataCollectorController::class, 'getAllInterestData']);

Route::get('/save-interest-data', [DataCollectorController::class, 'saveInterestData']);

/* ---------------- */

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
