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

    private $messages = [
        // These messages are in Spanish because, unlike the 500 error,
        // they will be displayed directly on the front end.
        'start_date.required' => 'La fecha de inicio es obligatoria.',
        'start_date.date' => 'La fecha de inicio debe ser una fecha válida.',
        'end_date.required' => 'La fecha de fin es obligatoria.',
        'end_date.date' => 'La fecha de fin debe ser una fecha válida.',
        'end_date.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
        'currency_name.required' => 'El nombre de la moneda es obligatorio.',
        'currency_name.exists' => 'La moneda seleccionada no es válida.'
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
                return response()->json(['error' => $validator->errors()], 200);
            }
            // If the dates are valid, they are obtained from the URL parameters.
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $currencyValues = MonetaryFluctuation::with(['currency' => function ($query) {
                $query->select('id', 'name');
            }])
                ->whereBetween('date', [$startDate, $endDate])
                ->orderBy('date', 'asc')
                ->get();

            return response()->json(["data" => $currencyValues], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            // If an exception (error) occurs, catch it and return an error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public static function getAllInterestData()
    {
        try {
            // It retrieves information from the API, all active currencies in the
            // local database corresponding to all active years.
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
            // It collects all the relevant information and stores it in an organized manner in the database.
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
