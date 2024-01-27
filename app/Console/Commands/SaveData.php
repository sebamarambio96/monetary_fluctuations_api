<?php

namespace App\Console\Commands;

use App\Services\CurrencyDataCollector;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SaveData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:save-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save data of DataCollector in BBDD';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $dataCollector = new CurrencyDataCollector();
            $interestData = $dataCollector->getAllInterestData();
            foreach ($interestData as $currencyData) {
                $dataCollector->saveMonetaryFluctuationBBDD($currencyData);
            }
            exit(0);
        } catch (\Exception $e) {
            Log::error('Save data of DataCollector in BBDD (CRON): ' . $e->getMessage());
            exit(1);
        }
    }
}
