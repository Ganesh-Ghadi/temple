<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gotravali Summary Report</title>
    <style>
        body {
            font-family: "freeserif";
            margin-bottom: 50px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
        }
        thead {
            background-color: #f5f5f5;
        }
        .devta-header {
            background-color: #eaeaea;
            font-weight: bold;
            padding: 10px;
        }
        .total-row {
            font-weight: bold;
            text-align: right;
        }
    </style>
</head>
<body>

<h3>Gotravali Summary Report - {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</h3>

@php
    $grouped = $poojaTypeEntries->groupBy('devtaName');
    $grandTotal = 0;
@endphp

<table>
    <thead>
        <tr>
            <th style="width: 50%;">Devta Name</th>
            <th style="width: 40%;">Pooja Type</th>
            <th style="width: 10%;">Count</th>
        </tr>
    </thead>
    <tbody>
        @foreach($grouped as $devtaName => $entries)
            @php
                $poojaGroups = $entries->groupBy('poojaType');
                $devtaTotal = $entries->count();
                $grandTotal += $devtaTotal;
            @endphp

            <tr>
                <td colspan="3" class="devta-header">{{ $devtaName }}</td>
            </tr>

            @foreach($poojaGroups as $poojaType => $groupedPoojas)
                <tr>
                    <td></td>
                    <td>{{ $poojaType }}</td>
                    <td style="text-align: right;">{{ $groupedPoojas->count() }}</td>
                </tr>
            @endforeach

            <tr>
                <td colspan="2" class="total-row">Total for {{ $devtaName }}</td>
                <td class="total-row">{{ $devtaTotal }}</td>
            </tr>
        @endforeach

        <tr>
            <td colspan="2" class="total-row">Grand Total</td>
            <td class="total-row">{{ $grandTotal }}</td>
        </tr>
    </tbody>
</table>

</body>
</html>
