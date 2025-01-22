<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
    body {
        font-family: "freeserif";
        margin-bottom: 50px;

    }
    table{
        margin-bottom: 50px;
    }

    table,
    th,
    td {
        border: 1px solid black;
    }
    th,
    td {
        padding: 5px;
        margin: 5px;
    }
    thead {
            display: table-header-group;
        }
   
    </style>
</head>

<body>
  
    {{-- <h4 style="margin:0px; padding:0px;">श्री गणेश मंदिर संस्थान - सर्व पावत्या {{ \Carbon\Carbon::parse($from_date)->format('d/m/Y') }} ते {{ \Carbon\Carbon::parse($to_date)->format('d/m/Y') }}</h4>
    <p style="border: 1px solid black; width:100%; margin:0px; padding:0px; margin-bottom:5px;"></p> --}}
    <table style="width: 100%">
        <thead>
        <tr>
            <th>Receipt Type</th>
            <th>Bank</th>
            <th>Cash</th>
            <th>Card</th>
            <th>Total</th>
        </tr>
    </thead>
        <tbody>

             <tr>
                <td colspan="5">receipt head</td>
            </tr>
            <tr>
                <td>receipt type</td>
                <td>bank55</td>
                <td>cash 3434</td>
                <td>card 22</td>
                <td>total 343</td>
            </tr>
            <tr>
                <td>H total</td>
                <td>bank55</td>
                <td>cash 3434</td>
                <td>card 22</td>
                <td>total 343</td>
            </tr>
          
        </tbody>

    </table>


    <h1>new</h1>
    @foreach($receiptsWithTotal as $receiptHead => $data)

    <table style="width: 100%">
        <thead>
        <tr>
            <th>Receipt Type</th>
            <th>Bank</th>
            <th>Cash</th>
            <th>Card</th>
            <th>Total</th>
        </tr>
    </thead>
        <tbody>

             <tr>
                <td colspan="5">{{$receiptHead}}</td>
            </tr>
            <tr>
                <td>{{ $data['receipts'][0]->receiptType->receipt_type }}</td>
                <td>bank55</td>
                <td>cash 3434</td>
                <td>card 22</td>
                <td>total 343</td>
            </tr>
            <tr>
                <td>H total</td>
                <td>bank55</td>
                <td>cash 3434</td>
                <td>card 22</td>
                <td>total 343</td>
            </tr>
          
        </tbody>

    </table>
    @endforeach




    {{--  --}}


    @foreach($receiptsWithTotal as $receiptHead => $data)
    <table>
        <thead>
            <tr>
                <th colspan="5">{{ $receiptHead }} Receipts</th>
            </tr>
            <tr>
                <th>Receipt Type</th>
                <th>Bank</th>
                <th>Cash</th>
                <th>Card</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['receipts'] as $receipt)
                <tr>
                    <td>{{ $receipt->receiptType->receipt_type ?? 'N/A' }}</td>
                    <td>{{ $receipt->bank ?? 'N/A' }}</td>
                    <td>{{ $receipt->amount ?? 'N/A' }}</td>
                    <td>{{ $receipt->card ?? 'N/A' }}</td>
                    <td>{{ $receipt->amount + $receipt->card ?? 0 }}</td>
                </tr>
            @endforeach

            <!-- Add total row -->
            <tr>
                <td colspan="4" style="text-align: right; font-weight: bold;">Total Amount</td>
                <td>{{ $data['total_amount'] }}</td>
            </tr>
        </tbody>
    </table>
 @endforeach
    

 {{-- chat --}}


 @foreach($receiptsWithTotal as $receiptHead => $data)
    <table style="width: 100%">
        <thead>
            <tr>
                <th>Receipt Type</th>
                <th>Bank</th>
                <th>Cash</th>
                <th>Card</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <!-- Display Receipt Head Title -->
            <tr>
                <td colspan="5" style="font-weight: bold;">{{ $receiptHead }} Receipts</td>
            </tr>

            {{-- Initialize totals --}}
            @php
                $totalBank = 0;
                $totalCash = 0;
                $totalCard = 0;
                $totalAmount = 0;
            @endphp

            {{-- Loop through receipts and calculate totals --}}
            @foreach($data['receipts'] as $receipt)
                @php
                    $bank = $receipt->bank ?? 0;
                    $cash = $receipt->amount ?? 0;
                    $card = $receipt->card ?? 0;
                    $totalBank += $bank;
                    $totalCash += $cash;
                    $totalCard += $card;
                    $totalAmount += ($cash + $card + $bank);
                @endphp
            @endforeach

            <!-- Display calculated totals -->
            <tr>
                <td>{{ $data['receipts'][0]->receiptType->receipt_type ?? 'N/A' }}</td>
                <td>{{ number_format($totalBank, 2) }}</td>
                <td>{{ number_format($totalCash, 2) }}</td>
                <td>{{ number_format($totalCard, 2) }}</td>
                <td>{{ number_format($totalAmount, 2) }}</td>
            </tr>

            <!-- Display the overall totals (sum of all receipt types) -->
            <tr>
                <td style="font-weight: bold;">Total Amount</td>
                <td>{{ number_format($totalBank, 2) }}</td>
                <td>{{ number_format($totalCash, 2) }}</td>
                <td>{{ number_format($totalCard, 2) }}</td>
                <td>{{ number_format($totalAmount, 2) }}</td>
            </tr>

        </tbody>
    </table>
@endforeach

<h1>fvffedf</h1>


@foreach($receiptsWithTotal as $receiptHead => $data)
    <table style="width: 100%">
        <thead>
            <tr>
                <th>Receipt Type</th>
                <th>Bank</th>
                <th>Cash</th>
                <th>Cheque</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <!-- Display Receipt Head Title -->
            <tr>
                <td colspan="5" style="font-weight: bold;">{{ $receiptHead }} Receipts</td>
            </tr>

            {{-- Loop through receipts and display them --}}
            @foreach($data['receipts'] as $receipt)
                <tr>
                    <td>{{ $receipt->receiptType->receipt_type ?? 'N/A' }}</td>
                    <td>{{ $receipt->payment_mode == 'Bank' ? number_format($receipt->amount, 2) : '0.00' }}</td>
                    <td>{{ $receipt->payment_mode == 'Cash' ? number_format($receipt->amount, 2) : '0.00' }}</td>
                    <td>{{ $receipt->payment_mode == 'Card' ? number_format($receipt->amount, 2) : '0.00' }}</td>
                    <td>{{ number_format($receipt->amount, 2) }}</td>
                </tr>
            @endforeach

            <!-- Display calculated totals -->
            <tr>
                <td style="font-weight: bold;">Total</td>
                <td>{{ number_format($data['total_bank'], 2) }}</td>
                <td>{{ number_format($data['total_cash'], 2) }}</td>
                <td>{{ number_format($data['total_cheque'], 2) }}</td>
                <td>{{ number_format($data['total_amount'], 2) }}</td>
            </tr>

        </tbody>
    </table>
@endforeach


<h1>my cod3e</h1>


@foreach($receiptsWithTotal as $receiptHead => $data)
    <table style="width: 100%">
        <thead>
            <tr>
                <th>Receipt Type</th>
                <th>Bank</th>
                <th>Cash</th>
                <th>Cheque</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <!-- Display Receipt Head Title -->
            <tr>
                <td colspan="5" style="font-weight: bold;">{{ $receiptHead }} Receipts</td>
            </tr>

            {{-- Loop through receipts and display them --}}
            {{-- @foreach($data['receipts'] as $receipt) --}}
                <tr>
                    <td>{{  $data['receipts'][0]->receiptType->receipt_type ?? 'N/A'}}</td>
                    {{-- <td>{{ $receipt->payment_mode == 'Bank' ? number_format($receipt->amount, 2) : '0.00' }}</td>
                    <td>{{ $receipt->payment_mode == 'Cash' ? number_format($receipt->amount, 2) : '0.00' }}</td>
                    <td>{{ $receipt->payment_mode == 'Card' ? number_format($receipt->amount, 2) : '0.00' }}</td>
                    <td>{{ number_format($receipt->amount, 2) }}</td> --}}
                    <td>{{ number_format($data['total_bank'], 2) }}</td>
                    <td>{{ number_format($data['total_cash'], 2) }}</td>
                    <td>{{ number_format($data['total_cheque'], 2) }}</td>
                    <td>{{ number_format($data['total_amount'], 2) }}</td>
                </tr>
            {{-- @endforeach --}}

            <!-- Display calculated totals -->
            <tr>
                <td style="font-weight: bold;">Total</td>
                <td>{{ number_format($data['total_bank'], 2) }}</td>
                <td>{{ number_format($data['total_cash'], 2) }}</td>
                <td>{{ number_format($data['total_cheque'], 2) }}</td>
                <td>{{ number_format($data['total_amount'], 2) }}</td>
            </tr>

        </tbody>
    </table>
@endforeach


    </body>



</html>


/*
<body>

    @foreach($receiptsWithTotal as $receiptHead => $data)
        <table>
            <thead>
                <tr>
                    <th colspan="5">{{ $receiptHead }} Receipts</th>
                </tr>
                <tr>
                    <th>Receipt Type</th>
                    <th>Bank</th>
                    <th>Cash</th>
                    <th>Card</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['receipts'] as $receipt)
                    <tr>
                        <td>{{ $receipt->receiptType->receipt_type ?? 'N/A' }}</td>
                        <td>{{ $receipt->bank ?? 'N/A' }}</td>
                        <td>{{ $receipt->amount ?? 'N/A' }}</td>
                        <td>{{ $receipt->card ?? 'N/A' }}</td>
                        <td>{{ $receipt->amount + $receipt->card ?? 0 }}</td>
                    </tr>
                @endforeach

                <!-- Add total row -->
                <tr>
                    <td colspan="4" style="text-align: right; font-weight: bold;">Total Amount</td>
                    <td>{{ $data['total_amount'] }}</td>
                </tr>
            </tbody>
        </table>
    @endforeach

</body>
*/