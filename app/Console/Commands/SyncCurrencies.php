<?php

namespace App\Console\Commands;

use App\Models\Currency;
use App\Services\ExchangeRatesIO\DataObjects\Symbols;
use App\Services\ExchangeRatesIO\ExchangeRatesIOService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Command\Command as CommandAlias;

class SyncCurrencies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:sync';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes Currencies';

    public function __construct(
        private readonly ExchangeRatesIOService $api
    )
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        try {
            $newSymbols = (new Symbols($this->api->currencies()->all()->body()))->toArray();
            $existingSymbols = Currency::all()->pluck('code')->toArray();
            $notSyncedCurrencies = array_diff_key($newSymbols, array_flip($existingSymbols));

            if (count($notSyncedCurrencies) == 0) {
                $this->info('Nothing to sync.');
                return CommandAlias::SUCCESS;
            }

            $bar = $this->output->createProgressBar(count($notSyncedCurrencies));

            $bar->start();
            foreach ($notSyncedCurrencies as $notSyncedCurrencySymbol => $notSyncedCurrencyName) {
                $currency = new Currency();
                $currency->code = $notSyncedCurrencySymbol;
                $currency->name = $notSyncedCurrencyName;
                $currency->save();
                $bar->advance();
            }
            $bar->finish();

        } catch (Exception $e) {
            Log::error('SYNC_CURRENCY_ERROR: ' . $e->getMessage() . ' on line: ' . $e->getLine() . ' on ' . $e->getFile());
            $this->error('A fatal error occurred & it is logged.');
            return CommandAlias::FAILURE;
        }
        return CommandAlias::SUCCESS;
    }
}
