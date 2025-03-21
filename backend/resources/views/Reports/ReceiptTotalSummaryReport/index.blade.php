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

    <table style="width: 100%">
        <thead>
            <tr>
                <th>Receipt Type</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($receiptsWithTotal as $receiptHead => $receiptHeadData)
                <!-- Display Receipt Head Title -->
                {{-- <tr>
                    <td colspan="2" style="font-weight: bold;">{{ $receiptHead }}</td>
                </tr> --}}

                {{-- Loop through each receiptType within the current receiptHead --}}
                @foreach($receiptHeadData as $receiptTypeId => $data)
                    <tr>
                        <td>{{ $data['receipts']->first()->receiptType->receipt_type ?? 'N/A' }}</td>
                        <td style="text-align:right">{{ number_format($data['total_amount'], 2) }}</td>
                    </tr>
                    {{-- <tr>
                        <td colspan="2" style="font-weight: bold;"></td>
                    </tr> --}}
                @endforeach

                <!-- Display calculated totals for the entire receiptHead -->
                {{-- <tr>
                    <td style="font-weight: bold; text-align:right">Receipt Head Total:</td>
                    <td style="font-weight: bold; text-align:right">{{ number_format($receiptHeadData->sum(function($data) { return $data['total_amount']; }), 2) }}</td>
                </tr> --}}

            @endforeach
            {{-- <tr>
                <td style="font-weight: bold; text-align:right">Cash Total:</td>
                <td style="font-weight: bold;  text-align: right;" >{{$cashTotal}}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; text-align:right">UPI Total:</td>
                <td style="font-weight: bold;  text-align: right;" >{{$upiTotal}}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; text-align:right">Cheque Total:</td>
                <td style="font-weight: bold; text-align: right;" >{{$chequeTotal}}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; text-align:right">Card Total:</td>
                <td style="font-weight: bold; text-align: right;" >{{$cardTotal}}</td>
            </tr> --}}
            <tr>
                <td style="font-weight: bold; text-align:right">TOTAL:</td>
                <td style="font-weight: bold; text-align: right;" >{{$Total}}</td>
            </tr> 
        </tbody>
    </table>

</body>

</html>

