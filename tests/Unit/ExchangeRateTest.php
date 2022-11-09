<?php

namespace Tests\Unit;

use App\Library\ExchangeCalculator;
use App\Models\Currency;
use App\Models\User;
//use PHPUnit\Framework\TestCase;
use App\Services\ExchangeRatesIO\ExchangeRatesIOService;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ExchangeRateTest extends TestCase
{
    public function test_user_can_get_exchange_rate()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        Currency::factory()->create([
            'code' => 'USD',
            'name' => 'United States Dollar',
        ]);
        Currency::factory()->create([
            'code' => 'EUR',
            'name' => 'Euro',
        ]);


        $response = $this->json('POST', '/exchange-rate', [
            'base_currency' => 'USD',
            'target_currency' => 'EUR',
            'amount' => 1
        ]);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'
            ]);
    }

    public function test_user_cannot_get_exchange_rate_without_base_currency()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->withoutExceptionHandling();
        $response = $this->json('POST', '/exchange-rate', [
            'target_currency' => 'EUR',
            'amount' => 1
        ]);
        $response->assertStatus(400)
            ->assertJsonStructure([
                'success',
                'messages'
            ])
            ->assertJson([
                'success' => false,
                'messages' => [
                    'base_currency' => [
                        'The base currency field is required.'
                    ]
                ]
            ]);
    }

    public function test_user_cannot_get_exchange_rate_without_target_currency()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->withoutExceptionHandling();
        $response = $this->json('POST', '/exchange-rate', [
            'base_currency' => 'EUR',
            'amount' => 1
        ]);
        $response->assertStatus(400)
            ->assertJsonStructure([
                'success',
                'messages'
            ])
            ->assertJson([
                'success' => false,
                'messages' => [
                    'target_currency' => [
                        'The target currency field is required.'
                    ]
                ]
            ]);
    }

    public function test_exchange_calculator_calculates_correctly()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->withoutExceptionHandling();
        $fromCurrency = Currency::factory()->create([
            'code' => 'USD',
            'name' => 'United States Dollar',
        ]);
        $toCurrency = Currency::factory()->create([
            'code' => 'EUR',
            'name' => 'Euro',
        ]);
        $mockAmount = 5;
        $mockRate = 1.5;
        $mockResult = $mockAmount * $mockRate;

        $cacheKey =  sprintf('%s:%s', $fromCurrency->code, $toCurrency->code);
        //put mock cache to redis
        Cache::put($cacheKey,$mockRate,60);

        $calculator = new ExchangeCalculator(new ExchangeRatesIOService(env('EXCHANGE_API_BASE_URL'),env('EXCHANGE_API_KEY')));
        $calculatedResult = $calculator->from('USD')
            ->to('EUR')
            ->amount($mockAmount)
            ->calculate();
        $this->assertEquals($mockResult,$calculatedResult);
    }
}
