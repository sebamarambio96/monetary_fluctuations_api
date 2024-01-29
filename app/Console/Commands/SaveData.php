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
    protected $signature = 'app:savedata';

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
            // It collects all the relevant information and stores it in an organized manner in the database.
            foreach ($interestData as $currencyData) {
                $dataCollector->saveMonetaryFluctuationBBDD($currencyData);
            }
            Log::info('Save data of DataCollector in BBDD (CRON):  Success');
            exit(0);
        } catch (\Exception $e) {
            Log::error('Save data of DataCollector in BBDD (CRON): ' . $e->getMessage());
            exit(1);
        }
    }
}
