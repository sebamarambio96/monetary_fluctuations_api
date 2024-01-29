<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\InterestYear;
use App\Models\MonetaryFluctuation;
use Illuminate\Support\Facades\Http;

class CurrencyDataCollector
{
    protected $activeYears;
    protected $activeCurrencies;

    public function __construct()
    {
        $this->activeYears = $this->getActiveYears();
        $this->activeCurrencies = $this->getActiveCurrencies();
    }

    public function getAllInterestData()
    {
        $data = [];

        // Iterate through each active currency
        foreach ($this->activeCurrencies as $currency) {
            $data[$currency->name] = [];

            // Store the currency ID and create a sub-array for years' data
            $data[$currency->name]['idCurrency'] = $currency['id'];
            $data[$currency->name]['yearsData'] = [];

            // Iterate through each active year
            foreach ($this->activeYears as $year) {
                // Fetch currency data for the current year and currency
                $yearData = $this->fetchCurrencyData($year, $currency->name);

                // Store the year's data in the corresponding sub-array
                $data[$currency->name]['yearsData'][$year] = $yearData['serie'];
            }
        }

        // Return the collected data
        return $data;
    }

    public function saveMonetaryFluctuationBBDD($currencyData)
    {
        // Extract currency ID and years' data from the input array
        $idCurrency = $currencyData['idCurrency'];
        $yearsData = $currencyData['yearsData'];

        // Iterate through each year in the data
        foreach ($yearsData as $year) {
            // Iterate through each data entry in the current year
            foreach ($year as $data) {
                // Use updateOrCreate to either update the existing record or create a new one
                MonetaryFluctuation::updateOrCreate(
                    // Conditions to find the existing record (based on date and currency ID)
                    ['date' => $data['fecha'], 'id_currencies' => $idCurrency],

                    [ // Data to update or insert if it doesn't exist
                        'date' => $data['fecha'],
                        'value_clp' => $data['valor'],
                        'id_currencies' => $idCurrency
                    ]
                );
            }
        }
    }


    // Static Functions

    public static function fetchCurrencyData($year, $currencyName)
    {
        // Construct the API URL
        $url = "https://mindicador.cl/api/$currencyName/$year";

        // Make an HTTP GET request and retrieve JSON data
        return Http::get($url)->json();
    }

    public static function getActiveYears()
    {
        // Only get "year" column
        return InterestYear::where('status', 1)->pluck('year');
    }

    public static function getActiveCurrencies()
    {
        // Only get "name" and "id" column
        return Currency::where('status', 1)->get(['id', 'name']);
    }
}
