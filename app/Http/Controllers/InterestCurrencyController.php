<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CurrencyDataCollector;
use Illuminate\Http\Request;

class InterestCurrencyController extends Controller
{
    public static function getCurrencyYears()
    {
        try {
            // Attempt to get active currency using the CurrencyDataCollector class
            return response()->json(CurrencyDataCollector::getActiveCurrencies(), 200);
        } catch (\Exception $e) {
            // If an exception (error) occurs, catch it and return an error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
