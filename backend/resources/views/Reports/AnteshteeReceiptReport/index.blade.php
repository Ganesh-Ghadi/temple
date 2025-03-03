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
            <th>Receipt No</th>
            <th>Receipt Date</th>
            <th>Receipt Type</th>
            <th>Guruji Name</th>
            <th>Yajman Name</th>
            <th>Day</th>
        </tr>
    </thead>
        <tbody>
            @foreach($receipts as $receipt)
            <tr>
                <td>{{$receipt->receipt_no}}</td>
                <td>{{ \Carbon\Carbon::parse($receipt->receipt_date)->format('d/m/Y') }}</td>
                <td>{{$receipt->receiptType->receipt_type}}</td>
                <td>{{$receipt->anteshteeReceipt->guruji}}</td>
                <td>{{$receipt->anteshteeReceipt->yajman}}</td>
                {{-- <td>
                    @if($receipt->anteshteeReceipt->day_9_date == $date && $receipt->anteshteeReceipt->day_9)
                        Day 9
                    @elseif($receipt->anteshteeReceipt->day_10_date == $date && $receipt->anteshteeReceipt->day_10)
                        Day 10
                    @elseif($receipt->anteshteeReceipt->day_11_date == $date && $receipt->anteshteeReceipt->day_11)
                        Day 11
                    @elseif($receipt->anteshteeReceipt->day_12_date == $date && $receipt->anteshteeReceipt->day_12)
                        Day 12
                    @elseif($receipt->anteshteeReceipt->day_13_date == $date && $receipt->anteshteeReceipt->day_13)
                        Day 13
                    @else
                        N/A
                    @endif
                </td> --}}
                <td>
                    @php
                        $days = [];
                        // Check for Day 9
                        if ($receipt->anteshteeReceipt->day_9_date == $date && $receipt->anteshteeReceipt->day_9) {
                            $days[] = '9';
                        }
                        // Check for Day 10
                        if ($receipt->anteshteeReceipt->day_10_date == $date && $receipt->anteshteeReceipt->day_10) {
                            $days[] = '10';
                        }
                        // Check for Day 11
                        if ($receipt->anteshteeReceipt->day_11_date == $date && $receipt->anteshteeReceipt->day_11) {
                            $days[] = '11';
                        }
                        // Check for Day 12
                        if ($receipt->anteshteeReceipt->day_12_date == $date && $receipt->anteshteeReceipt->day_12) {
                            $days[] = '12';
                        }
                        // Check for Day 13
                        if ($receipt->anteshteeReceipt->day_13_date == $date && $receipt->anteshteeReceipt->day_13) {
                            $days[] = '13';
                        }
                    @endphp
                
                    @if(count($days) > 0)
                        {{ implode(', ', $days) }} <!-- Display days in comma separated list -->
                    @else
                        N/A
                    @endif
                </td>
                
            </tr>
            @endforeach
            
        </tbody>

    </table>
    

    </body>



</html>