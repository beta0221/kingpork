<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>訂購紀錄 | {{$user->name}}</title>

    <style>
        table{
            border-collapse: collapse;
        }
        td{
            border: 1pt solid lightgray;
				word-break: break-all;
                padding: 4px 12px;
        }
    </style>
</head>
<body>
    <h3>訂購人：{{$user->name}}</h3>
    <h3>目前紅利：{{$user->bonus}}</h3>
    <h3>正確紅利：<font color="red">{{$bonus}}</font></h3>

    @if ($user->bonus != $bonus)
        <form method="POST" action="/regulate/bonus/{{$user->id}}">
            {{ csrf_field() }}
            <input type="hidden" value="{{$bonus}}" name="bonus">
            <button type="submit">校正</button>
        </form>
    @endif

    <table style="text-align: center;margin-top:12px">
        <tr>
           <td>訂單編號</td> 
           <td>收件人</td>
           <td>折抵</td> 
           <td>總額</td> 
           <td>回饋紅利</td> 
           <td>付款</td> 
           <td>付款方式</td> 
           <td>出貨狀態</td> 
           <td>日期</td> 
        </tr>
    
        @foreach ($bills as $bill)
            <tr>
                <td>{{$bill->bill_id}}</td> 
                <td>{{$bill->ship_name}}</td>
                <td>{{$bill->bonus_use}}</td> 
                <td>{{$bill->price}}</td> 
                <td>{{$bill->get_bonus}}</td> 
                <td>{{($bill->status)?'已付':'未付款'}}</td> 
                <td>{{$bill->pay_by}}</td> 
                <td>
                    @if(($bill->pay_by == '貨到付款' AND $bill->shipment == 0) OR ($bill->status == 1 AND $bill->shipment == 0))
					可準備
					@elseif($bill->status == 1 AND $bill->shipment == 1)
					準備中
					@elseif($bill->shipment==2)
					已出貨
					@elseif($bill->shipment==3)
					＊結案＊
					@else
					未成立
					@endif	
                </td> 
                <td>{{$bill->created_at}}</td> 
            </tr>
        @endforeach
    </table>
    
</body>
</html>