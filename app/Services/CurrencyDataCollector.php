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
        foreach ($this->activeCurrencies as $currency) {
            $data[$currency->name]['idCurrency'] = $currency['id'];
            foreach ($this->activeYears as $year) {
                $yearData = $this->fetchCurrencyData($year, $currency->name);
                $data[$currency->name][$year] = $yearData['serie'];
            }
        }
        return $data;
    }

    public function saveMonetaryFluctuationBBDD($currencyData)
    {
        $idCurrency = $currencyData['id'];
        foreach ($currencyData as $yearData) {
            MonetaryFluctuation::updateOrCreate(
                // Conditions to find the existing record
                ['date' => $yearData['fecha']],

                [ // Data to update or insert if it doesn't exist
                    'date' => $yearData['fecha'],
                    'value_clp' => $yearData['valor'],
                    'id_currencies' => $idCurrency
                ]
            );
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
        // Only get "name" column
        return Currency::where('status', 1)->get(['id', 'name']);
    }
}
