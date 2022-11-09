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
                    <h6>Welcome Operator {{auth()->user()->name}}</h6>
                    <p>Please select the base & target currency the customer wants to exchange</p>
                    <form id="transactionForm" action="{{route('storeTransaction')}}">
                        <div class="mb-3">
                            <label for="base_currency" class="form-label">Base</label>
                            <select name="base_currency" class="form-control" id="base_currency"></select>
                        </div>
                        <div class="mb-3">
                            <label for="target_currency" class="form-label">Target</label>
                            <select name="target_currency" class="form-control" id="target_currency" onchange="updateRate()"></select>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input id="amount" name="amount" type="number" class="form-control" onkeyup="updateRate()">
                        </div>
                        <div class="mb-3">
                            <label for="type">Type</label>
                            <select name="type" id="type" class="form-control">
                                <option value="1">Deposit</option>
                                <option value="2">Withdraw</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="method">Payment Method</label>
                            <select name="method" id="method" class="form-control">
                                @foreach($paymentMethods as $paymentMethod)
                                    <option value="{{$paymentMethod->slug}}">{{$paymentMethod->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="rateAlert" class="alert alert-primary" role="alert">
                            Rate for <span id="convertDetail"></span>: <b><span id="rate"></span></b> <br>
                            Final amount to give to the customer: <b><span id="result"></span></b>
                        </div>
                        <div id="txnCreatedAlert" class="alert alert-success" role="alert">
                            Transaction was successfully created! <a id="txnDetailLink" href="#">Click here</a> to see transaction detail.
                        </div>

                        <button id="approveTxnBtn" type="submit" class="btn btn-primary">Confirm Transaction</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>

    $("#transactionForm").submit(function(e) {

        e.preventDefault();

        var form = $(this);
        var actionUrl = form.attr('action');

        $.ajax({
            type: "POST",
            url: actionUrl,
            data: form.serialize(),
            success: function(data)
            {
                $("#txnCreatedAlert").show();
                $("#txnCreatedAlert").html(
                    'Transaction was successfully created! <a id="txnDetailLink" href="transaction/detail/'+ data.data.transaction_id +'">Click here</a> to see transaction detail.' + data.data.transaction_id
                );
            }
        });

    });

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
        $("#txnCreatedAlert").hide();
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
                var baseCurrencySelect = $('#base_currency');
                var targetCurrencySelect = $('#target_currency');
                $.each(data, function (i,item) {
                    baseCurrencySelect.append( '<option value="'
                        + item.code
                        + '">'
                        + item.code + ' - ' +item.name
                        + '</option>' );
                    targetCurrencySelect.append( '<option value="'
                        + item.code
                        + '">'
                        + item.code + ' - ' +item.name
                        + '</option>' );
                });
            }
        });
    });
</script>
