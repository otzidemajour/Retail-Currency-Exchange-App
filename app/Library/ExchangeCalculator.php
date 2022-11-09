<?php

namespace App\Library;

use App\Models\Currency;
use App\Services\ExchangeRatesIO\DataObjects\Exchange;
use App\Services\ExchangeRatesIO\ExchangeRatesIOService;
use Illuminate\Support\Facades\Cache;

class ExchangeCalculator
{

    public float $rate;
    private Currency $from;
    private Currency $to;
    private float $amount;

    public function __construct(
        private readonly ExchangeRatesIOService $api
    )
    {
    }

    /**
     * @param string $currencyCode
     * @return $this
     */
    public function from(string $currencyCode): self
    {
        $this->from = Currency::where('code', $currencyCode)->first();
        return $this;
    }

    /**
     * @param string $currencyCode
     * @return $this
     */
    public function to(string $currencyCode): self
    {
        $this->to = Currency::where('code', $currencyCode)->first();
        return $this;
    }

    /**
     * @param float $amount
     * @return $this
     */
    public function amount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return float
     */
    public function calculate(): float
    {
        $cacheKey = sprintf('%s:%s', $this->from->code, $this->to->code);
        $cachedRate = Cache::get($cacheKey);

        if ($cachedRate) {
            $this->rate = $cachedRate;
            return $this->calculateResultWithRate($this->amount, $this->rate);
        }

        $exchange = new Exchange($this->api->exchange()->get($this->from, $this->to, $this->amount)->body());
        $freshRate = $exchange->rate();
        $this->rate = $freshRate;
        Cache::put($cacheKey, $this->rate, 300);

        return $this->calculateResultWithRate($this->amount, $freshRate);
    }

    /**
     * @param float $baseAmount
     * @param float $rate
     * @return float
     */
    private function calculateResultWithRate(float $baseAmount, float $rate): float
    {
        return $baseAmount * $rate;
    }

    /**
     * @return Currency
     */
    public function getFrom(): Currency
    {
        return $this->from;
    }

    /**
     * @return Currency
     */
    public function getTo(): Currency
    {
        return $this->to;
    }
}
