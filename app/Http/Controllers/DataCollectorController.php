<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MonetaryFluctuation;
use App\Services\CurrencyDataCollector;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DataCollectorController extends Controller
{
    private $rules = [
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'currency_name' => 'required|exists:currencies,name'
    ];

    // Define custom error messages
    private $messages = [
        'start_date.required' => 'The start date is required.',
        'start_date.date' => 'The start date must be a valid date.',
        'end_date.required' => 'The end date is required.',
        'end_date.date' => 'The end date must be a valid date.',
        'end_date.after_or_equal' => 'The end date must be equal to or after the start date.',
        'currency_name.required' => 'The currency name is required.',
        'currency_name.exists' => 'The selected currency is invalid.',
    ];

    // Important Endpoint
    public function getCurrencyValues(Request $request, $currencyName)
    {
        try {
            // Validate the request
            $validator = Validator::make(
                array_merge($request->all(), ['currency_name' => $currencyName]),
                $this->rules,
                $this->messages
            );

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }
            // Dates are valid, continue with the rest of the logic
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $dollarValues = MonetaryFluctuation::whereBetween('date', [$startDate, $endDate])
                ->get();

            return response()->json($dollarValues, 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            // If an exception (error) occurs, catch it and return an error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public static function getAllInterestData()
    {
        try {
            $dataCollector = new CurrencyDataCollector();
            return response()->json($dataCollector->getAllInterestData(), 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            // If an exception (error) occurs, catch it and return an error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public static function saveInterestData()
    {
        try {
            $dataCollector = new CurrencyDataCollector();
            $interestData = $dataCollector->getAllInterestData();
            foreach ($interestData as $currencyData) {
                $dataCollector->saveMonetaryFluctuationBBDD($currencyData);
            }

            return response()->json(['message' => 'Data saved successfully.'], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            // If an exception (error) occurs, catch it and return an error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
