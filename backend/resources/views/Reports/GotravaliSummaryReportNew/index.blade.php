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


{{-- <body>
    <table style="width: 100%">
        <thead>
            <tr>
                <th style="width: 60%;">Pooja Type</th>
                <th style="width: 20%;">Receipt Type</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach($poojaTypeCounts as $poojaType => $group)
                @foreach($group as $item)
                    <tr>
                        <td>{{ $item['pooja_type'] }}</td>
                        <td>{{ $item['receipt_type'] }}</td>
                        <td style="text-align: right;">{{ count($group) }}</td> <!-- Count for this pooja type -->
                    </tr>
                @endforeach
            @endforeach

            <tr>
                <td style="font-weight: bold; text-align: right;">TOTAL:</td>
                <td style="font-weight: bold; text-align: right;"></td>
                <td style="font-weight: bold; text-align: right;">{{ $totalCount }}</td>
            </tr>
        </tbody>
    </table>
</body> --}}
<body>
    <table style="width: 100%">
        <thead>
            <tr>
                <th style="width: 60%;">Pooja Type</th>
                <th style="width: 20%;">Receipt Type</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach($poojaTypeCounts as $poojaType => $group)
                <!-- This counts the number of items in the group -->
                @php
                    $groupCount = count($group);
                @endphp
                @foreach($group as $item)
                    <tr>
                        <td>{{ $item['pooja_type'] }}</td>
                        <td>{{ $item['receipt_type'] }}</td>
                        <td style="text-align: right;">{{ $groupCount }}</td> <!-- Count for this pooja type -->
                    </tr>
                @endforeach
            @endforeach

            <tr>
                <td style="font-weight: bold; text-align: right;">TOTAL:</td>
                <td style="font-weight: bold; text-align: right;"></td>
                <td style="font-weight: bold; text-align: right;">{{ $totalCount }}</td>
            </tr>
        </tbody>
    </table>
</body>


</html>