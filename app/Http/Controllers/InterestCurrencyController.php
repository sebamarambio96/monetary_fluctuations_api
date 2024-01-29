<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CurrencyDataCollector;
use Illuminate\Support\Facades\Log;

class InterestCurrencyController extends Controller
{
    public static function getCurrencyYears()
    {
        try {
            // Attempt to get active currency using the CurrencyDataCollector class
            return response()->json(CurrencyDataCollector::getActiveCurrencies(), 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            // If an exception (error) occurs, catch it and return an error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
