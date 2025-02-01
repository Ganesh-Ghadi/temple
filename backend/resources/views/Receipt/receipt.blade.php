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
        position: relative; 
        padding: 0;
    }
   
        .bottom-text {
            position: absolute;
            bottom: 1.5cm;
            width: 100%;
            margin-bottom: 0; 
        }
    </style>
</head>
<body>
    
        <h4 style="font-weight: bold; text-align:center">{{$receipt->receiptType->receipt_type}}</h4>

        <table style=" width: 100%; border-spacing: 0;">
            <tr>
                <td style=" padding: 5px;">{{$receipt->receipt_no}}</td>
                <td style="text-align:right">{{\Carbon\Carbon::parse($receipt->receipt_date)->format('d/m/Y')}}</td>
            </tr>
        </table>

        <p>{{$receipt->name}}</p>

        <table style="width: 100%; margin-top:40px; border-spacing: 0;">
            <tr>
                <td style=" padding: 5px;">{{$receipt->narration}}</td>
                <td style="text-align:right">{{$receipt->amount}}</td>
            </tr>
        </table>



        <table style="margin-top:50px; width: 100%; border-spacing: 0;">
            <tr>
                <td style=" padding: 5px;">{{$receipt->amount_in_words}}</td>
                <td style="text-align:right">{{$receipt->amount}}</td>
            </tr>
        </table>

        <p class="bottom-text">: {{$receipt->profile->profile_name}}&nbsp;&nbsp;&nbsp;{{ \Carbon\Carbon::parse($receipt->created_at)->format('d/m/Y h:i A') }}</p>


</body>
</html>