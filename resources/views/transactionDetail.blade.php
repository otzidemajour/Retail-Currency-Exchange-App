<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h6>Operator {{auth()->user()->name}}</h6>
                    <p>Transaction detail for <b>transaction ID: {{$transaction->id}}</b></p>
                    <form id="transactionForm" action="{{route('updateTransaction')}}" method="post">
                        @csrf
                        <input type="text" hidden name="transaction_id" value="{{$transaction->id}}">
                        <div class="mb-3">
                            <label for="base_currency" class="form-label">Base</label>
                            <select disabled name="base_currency" class="form-control" id="base_currency">
                                <option selected value="{{$transaction->baseCurrency->code}}">{{$transaction->baseCurrency->code .' - ' . $transaction->baseCurrency->name}}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="target_currency" class="form-label">Target</label>
                            <select name="target_currency" class="form-control" id="target_currency" onchange="updateRate()"></select>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Base Amount</label>
                            <input id="amount" name="amount" type="number" value="{{$transaction->base_amount}}" class="form-control" onkeyup="updateRate()">
                        </div>
                        <div class="mb-3">
                            <label for="targetAmount" class="form-label">Target Amount</label>
                            <input disabled id="targetAmount" name="targetAmount" type="number" value="{{$transaction->target_amount}}" class="form-control" onkeyup="updateRate()">
                        </div>
                        <div class="mb-3">
                            <label for="txnRate" class="form-label">Exchange Rate</label>
                            <input disabled id="txnRate" name="txnRate" type="number" value="{{$transaction->exchange_rate}}" class="form-control" onkeyup="updateRate()">
                        </div>
                        <div class="mb-3">
                            <label for="type">Type</label>
                            <select disabled name="type" id="type" class="form-control">
                                @if($transaction->type == \App\Models\Transaction::TYPE_DEPOSIT)
                                    <option value="1">Deposit</option>
                                @else
                                    <option value="2">Withdraw</option>
                                @endif


                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="method">Payment Method</label>
                            <select name="method" id="method" class="form-control" disabled>
                                    <option selected value="{{$transaction->paymentMethod->slug}}">{{$transaction->paymentMethod->name}}</option>
                            </select>
                        </div>
                        <div id="rateAlert" class="alert alert-primary" role="alert">
                            Rate for <span id="convertDetail"></span>: <b><span id="rate"></span></b> <br>
                            Updated target amount: <b><span id="result"></span></b>
                        </div>
                        <button id="approveTxnBtn" type="submit" class="btn btn-primary">Update Transaction</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>

    function updateRate() {
        var baseCurrency = $("#base_currency option:selected").val()
        var targetCurrency = $("#target_currency option:selected").val()
        var amount = $("#amount").val();
        if(amount == 0) {
            return;
        }
        $("#approveTxnBtn").attr("disabled", false);
        $("#rateAlert").show();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '{{route('getExchangeRate')}}',
            type: 'POST',
            data: {'base_currency': baseCurrency, 'target_currency': targetCurrency, 'amount': amount},
            success: function (data) {
                $("#convertDetail").html(baseCurrency + ' -> ' + targetCurrency);
                $("#rate").html(data.data.rate);
                $("#result").html(targetCurrency + ' '+ data.data.result)
            }
        });
    }

    $(document).ready(function(){
        $("#rateAlert").hide();
        $("#approveTxnBtn").attr("disabled", true);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '{{route('allCurrencies')}}',
            type: 'POST',
            data: '',
            success: function (data) {
                var targetCurrencySelect = $('#target_currency');
                $.each(data, function (i,item) {
                    if(item.code == '{{$transaction->targetCurrency->code}}') {
                        targetCurrencySelect.append( '<option selected value="'
                            + item.code
                            + '">'
                            + item.code + ' - ' +item.name
                            + '</option>' );
                    } else {
                        targetCurrencySelect.append( '<option value="'
                            + item.code
                            + '">'
                            + item.code + ' - ' +item.name
                            + '</option>' );
                    }
                });
            }
        });
    });
</script>
