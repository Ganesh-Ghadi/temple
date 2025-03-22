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
        table {
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
{{-- working --}}
@php
$totalCount = 0; // Initialize the total count variable
@endphp
    @foreach($poojaTypeCountsByReceiptType as $receiptType => $poojas)
        <h3>{{ $receiptType }}</h3>
        <table style="width: 100%">
            <thead>
                <tr>
                    <th style="width: 80%;">Pooja Type</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                @foreach($poojas->groupBy('poojaType') as $poojaType => $groupedPoojas)
                    <tr>
                        <td>{{ $poojaType }}</td>
                        <td style="text-align: right;">{{ $groupedPoojas->count() }}</td>
                    </tr>
                @endforeach

                <tr class="">
                    <td style="font-weight: bold; text-align: right;">TOTAL:</td>
                    <td style="font-weight: bold; text-align: right;">{{ $poojas->count() }}</td>
                </tr>
            </tbody>
        </table>
        @php
        $totalCount += $poojas->count(); // Add to the total count for each receiptType
    @endphp
    @endforeach
    <h3 style="text-align: right;">Total: {{ $totalCount }}</h3>


</body>

</html>
