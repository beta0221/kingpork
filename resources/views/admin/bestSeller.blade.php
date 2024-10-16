<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>銷售排行</title>
    {{Html::style('css/reset.css')}}
    {{Html::style('css/bootstrap/bootstrap.min.css')}}
    <style>
        td {
            border-bottom: 1px solid lightslategray;
        }
    </style>
</head>
<body>
    


    <div class="container mt-4 mb-4">
        <h3 class="d-block">銷售排行</h3>
        <h5 class="text-primary">廠商：{{$vendor}}</h5>
        <h5 class="text-muted">區間：{{$from}} ~ {{$to}}</h5>

        <div class="row mt-4">
            <table class="w-100">
                <tr style="background: rgb(62, 59, 59)" class="text-white">
                    <th class="p-2 text-center" style="width:80px">排名</th>
                    <th class="p-2">產品</th>
                    <th class="p-2 text-center" style="width:80px">數量</th>
                </tr>

                <?php $i = 1; ?>
                @foreach ($stats as $name => $quantity)
                    <tr style="background: lightgray" class="">
                        <td class="p-2 text-primary text-center">{{$i}}</td>
                        <td class="p-2">{{$name}}</td>
                        <td class="p-2 text-danger text-center">{{$quantity}}</td>
                    </tr>

                    <?php $i++; ?>
                @endforeach
            </table>
            
            
        </div>
    </div>
</body>

{{ Html::script('js/jquery/jquery-3.2.1.min.js') }}
{{ Html::script('js/bootstrap/bootstrap.min.js') }}
</html>