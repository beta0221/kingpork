<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>金園排骨 @yield('title')</title>
{{Html::style('css/reset.css')}}
{{Html::style('css/bootstrap/bootstrap.min.css')}}
{{Html::style('css/_navbar.css')}}
{{Html::style('css/_footer.css')}}
<style>
	@import url(//fonts.googleapis.com/earlyaccess/notosanskannada.css);
	*{
            position: relative;
            box-sizing: border-box;
    }
    body{
    	/*font-family: 'Noto Sans Kannada', sans-serif;*/
    }
    .wrapper{
    	overflow: hidden;
    }
</style>


@yield('stylesheets')

</head>
  <body>
  	<div class="wrapper">
@include('partials._navbar')






@yield('content')






@include('partials._footer')
	</div>
  </body>


{{ Html::script('js/jquery/jquery-3.2.1.min.js') }}

<script>
$(document).ready(function(){
  $.ajaxSetup({
      headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.ajax({
    type:'POST',
    url:'{{route('getInKart')}}',
    dataType:'json',
    success: function (response) {
          $('#inKart').append(response.msg);
       },
       error: function (data) {
          alert('Cart api error');
       }
  });

    
});
</script>
@yield('scripts')
</html>