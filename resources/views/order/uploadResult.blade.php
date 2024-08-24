<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>上傳結果</title>
</head>
<body>

    <?php
        if (isset($success)) {
            echo "<h3>" . $success . "</h3>";
        }

        if (isset($error)) {
            echo "<pre>" .  json_encode($error, JSON_UNESCAPED_UNICODE) . "</pre>";
        }
    ?>
    

    <a href="{{route('order.index')}}">回列表</a>

</body>
</html>