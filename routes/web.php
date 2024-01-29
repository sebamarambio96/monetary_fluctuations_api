<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Example request
    return redirect('/api/get-currency-values/dolar/?start_date=2023-01-01&end_date=2024-01-29');
});
