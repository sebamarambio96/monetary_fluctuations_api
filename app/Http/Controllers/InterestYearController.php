<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CurrencyDataCollector;
use Illuminate\Http\Request;

class InterestYearController extends Controller
{
    public static function getActiveYears()
    {
        try {
            // Attempt to get active years using the CurrencyDataCollector class
            return response()->json(CurrencyDataCollector::getActiveYears(), 200);
        } catch (\Exception $e) {
            // If an exception (error) occurs, catch it and return an error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
