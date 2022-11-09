<?php

namespace App\Http\Controllers;

use App\Library\ExchangeCalculator;
use App\Models\PaymentMethod;
use App\Services\ExchangeRatesIO\ExchangeRatesIOService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ExchangeRateController extends Controller
{
    public function __construct(
        private readonly ExchangeRatesIOService $api
    )
    {
    }

    public function index()
    {
        $paymentMethods = PaymentMethod::all();
        $data = [
            'paymentMethods' => $paymentMethods,
        ];
        return view('exchange', $data);
    }

    public function getExchangeRate(Request $request): JsonResponse
    {
        $validation = Validator::make($request->all(), [
            'base_currency' => 'required',
            'target_currency' => 'required',
            'amount' => 'required|numeric',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'messages' => $validation->messages()
            ], 400);
        }
        try {
            $amount = $request->get('amount');
            $calculator = new ExchangeCalculator($this->api);
            $calculatedResult = $calculator->from($request->get('base_currency'))
                ->to($request->get('target_currency'))
                ->amount($amount)
                ->calculate();
            $rate = $calculator->rate;
            return response()->json([
                'success' => true,
                'data' => [
                    'rate' => $rate,
                    'result' => $calculatedResult
                ]
            ]);
        } catch (Exception $e) {
            Log::error('EXCHANGE_ERROR: ' . $e->getMessage() . ' on line: ' . $e->getLine() . ' on ' . $e->getFile());
            return response()->json([
                'success' => false,
                'messages' => 'internal error'
            ], 500);
        }
    }
}
