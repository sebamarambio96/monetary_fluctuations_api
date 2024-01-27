<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CurrencyDataCollector;
use Illuminate\Http\Request;

class DataCollectorController extends Controller
{
    public static function getAllInterestData()
    {
        try {
            $dataCollector = new CurrencyDataCollector();
            return response()->json($dataCollector->getAllInterestData(), 200);
        } catch (\Exception $e) {
            // If an exception (error) occurs, catch it and return an error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
