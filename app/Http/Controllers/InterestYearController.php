<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CurrencyDataCollector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InterestYearController extends Controller
{
    public static function getActiveYears()
    {
        try {
            // Attempt to get active years using the CurrencyDataCollector class
            return response()->json(CurrencyDataCollector::getActiveYears(), 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            // If an exception (error) occurs, catch it and return an error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
