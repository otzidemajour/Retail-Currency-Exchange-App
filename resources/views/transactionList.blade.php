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
                    <h5>Transaction List</h5>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Payment Method</th>
                            <th scope="col">Type</th>
                            <th scope="col">Base Amount</th>
                            <th scope="col">Base Currency</th>
                            <th scope="col">Target Amount</th>
                            <th scope="col">Target Currency</th>
                            <th scope="col">Rate</th>
                            <th scope="col">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($transactions as $transaction)
                            <tr>
                                <th scope="row">{{$transaction->id}}</th>
                                <td>{{$transaction->paymentMethod->name}}</td>
                                <td>
                                    @if($transaction->type == \App\Models\Transaction::TYPE_DEPOSIT)
                                        Deposit
                                    @else
                                        Withdraw
                                    @endif
                                </td>
                                <td>{{$transaction->base_amount}}</td>
                                <td>{{$transaction->baseCurrency->code}}</td>
                                <td>{{$transaction->target_amount}}</td>
                                <td>{{$transaction->targetCurrency->code}}</td>
                                <td>{{$transaction->exchange_rate}}</td>
                                <td><a href="/transaction/detail/{{$transaction->id}}"><button class="btn btn-primary">Edit</button></a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
