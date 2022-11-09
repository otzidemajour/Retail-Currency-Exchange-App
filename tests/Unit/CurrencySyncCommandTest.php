<?php

namespace Tests\Unit;
//use PHPUnit\Framework\TestCase;
use App\Models\Currency;
use App\Services\ExchangeRatesIO\DataObjects\Symbols;
use App\Services\ExchangeRatesIO\ExchangeRatesIOService;
use Tests\TestCase;

class CurrencySyncCommandTest extends TestCase
{
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_all_currencies_are_synced()
    {
        $this->artisan('currency:sync')->assertExitCode(0);

        $api = resolve(ExchangeRatesIOService::class,['baseUrl' => env('EXCHANGE_API_BASE_URL'), 'apiKey' => env('EXCHANGE_API_KEY')]);
        $newSymbols = (new Symbols($api->currencies()->all()->body()))->toArray();
        $this->assertIsArray($newSymbols);
        $this->assertDatabaseCount(Currency::class,count($newSymbols));
        foreach ($newSymbols as $k => $v) {
            $this->assertDatabaseHas('currencies', ['code' => $k]);
        }
    }
}
