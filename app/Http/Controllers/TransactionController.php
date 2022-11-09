<?php

namespace App\Http\Controllers;

use App\Library\ExchangeCalculator;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Services\ExchangeRatesIO\ExchangeRatesIOService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function __construct(
        private readonly ExchangeRatesIOService $api
    )
    {}

    public function list()
    {
        $transactions = Transaction::where('user_id', auth()->user()->id)->get();
        $data = [
            'transactions' => $transactions
        ];
        return view('transactionList', $data);
    }

    public function detail(int $id)
    {
        $transaction = Transaction::whereId($id)->first();

        $data = [
            'transaction' => $transaction
        ];
        return view('transactionDetail', $data);
    }

    public function update(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'target_currency' => 'required|string',
            'amount' => 'required|numeric',
            'transaction_id' => 'required|numeric',
        ]);

        //todo add UI rendered validation
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'messages' => $validation->messages()
            ], 400);
        }

        try {
            $transactionId = $request->get('transaction_id');
            $amount = $request->get('amount');

            //todo add UI rendered errors associated with the model
            $transaction = Transaction::whereId($transactionId)->first();
            $calculator = new ExchangeCalculator($this->api);
            $calculatedResult = $calculator->from($transaction->baseCurrency->code)
                ->to($request->get('target_currency'))
                ->amount($amount)
                ->calculate();
            $rate = $calculator->rate;

            $transaction->exchange_rate = $rate;
            $transaction->target_amount = $calculatedResult;
            $transaction->target_currency_id = $calculator->getTo()->id;
            $transaction->save();

            return redirect(route('transactionDetail', ['id' => $transaction->id]));
        } catch (\Exception $e) {
            Log::error('TXN_SAVE_ERROR: '. $e->getMessage() . ' on line: ' . $e->getLine() . ' on ' . $e->getFile());
            return response()->json([
                'success' => false,
                'messages' => 'internal error'
            ],500);
        }
    }
    public function store(Request $request): JsonResponse
    {
        $validation = Validator::make($request->all(), [
            'base_currency' => 'required|string',
            'target_currency' => 'required|string',
            'amount' => 'required|numeric',
            'type' => 'required|numeric',
            'method' => 'required|string',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'messages' => $validation->messages()
            ], 400);
        }

        try {
            $amount = $request->get('amount');
            $paymentSlug = $request->get('method');
            $type = $request->get('type');
            $calculator = new ExchangeCalculator($this->api);
            $calculatedResult = $calculator->from($request->get('base_currency'))
                ->to($request->get('target_currency'))
                ->amount($amount)
                ->calculate();
            $rate = $calculator->rate;

            $transaction = new Transaction();
            $transaction->user_id = auth()->user()->id;
            $transaction->payment_method_id = PaymentMethod::where('slug', $paymentSlug)->first()->id;
            $transaction->type = $type;
            $transaction->base_amount = $amount;
            $transaction->base_currency_id = $calculator->getFrom()->id;
            $transaction->target_amount = $calculatedResult;
            $transaction->target_currency_id = $calculator->getTo()->id;
            $transaction->exchange_rate = $rate;
            $transaction->ip_address = $request->ip();
            $transaction->save();

            return response()->json([
                'success' => true,
                'data' => [
                    'transaction_id' => $transaction->id
                ],
            ],200);
        } catch (\Exception $e) {
            Log::error('TXN_SAVE_ERROR: '. $e->getMessage() . ' on line: ' . $e->getLine() . ' on ' . $e->getFile());
            return response()->json([
                'success' => false,
                'messages' => 'internal error'
            ],500);
        }
    }
}
