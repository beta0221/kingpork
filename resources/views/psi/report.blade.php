<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>庫存出貨報表</title>

    <style>
        table{
            border-collapse: collapse;
        }
        table td{
            border:1px solid #000;
            padding: 4px 8px;
        }
    </style>
</head>
<body>
    
    <h3>庫存出貨報表：{{$from_date}} ~ {{$to_date}}</h3>
    
    <table>


        
        @foreach ($retailers as $i =>$retailer)

            @if ($i == 0)
                <tr>
                    <td></td>
                    @foreach ($dateArray as $j => $date)
                    <td>{{$date}}</td>
                    @endforeach
                </tr>
            @endif

            <tr>
                <td>{{$retailer->name}}</td>
                @foreach ($dateArray as $j => $date)

                    @if (isset($data[$date][$retailer->id]))
                        <td>

                            <?php $total = []; ?>
                            @foreach ($data[$date][$retailer->id] as $log)                                
                                @foreach ($log->inventories()->get() as $inventory)
                                    <?php 
                                        if(!isset($total[$inventory->name])){
                                            $total[$inventory->name] = $inventory->pivot->quantity;
                                        }else{
                                            $total[$inventory->name] += $inventory->pivot->quantity;
                                        }
                                    ?>
                                @endforeach
                            @endforeach

                            @foreach ($total as $name => $quantity)
                                {{$name}}:{{$quantity}}<br>
                            @endforeach
                        </td>
                    @else
                        <td></td>
                    @endif
                    
                @endforeach
            </tr>
            
        @endforeach
    </table>


</body>
</html>