<?php
namespace App\Services\ExchangeRatesIO\Resources;

use App\Models\Currency;
use App\Services\ExchangeRatesIO\ExchangeRatesIOService;
use Illuminate\Http\Client\Response;

class ConvertResource
{
    public function __construct(
        private readonly ExchangeRatesIOService $service
    )
    {}

    public function get(Currency $baseCurrency, Currency $targetCurrency, float $amount): Response
    {
        return $this->service->get(
            request: $this->service->buildRequestWithApiKey(),
            url: "/convert",
            payload: [
                'amount' => $amount,
                'from' => $baseCurrency->code,
                'to' => $targetCurrency->code,
            ]
        );
    }
}
