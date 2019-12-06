<!DOCTYPE html>
<html lang="en">

<head>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer',"{{config('app.gtm_id')}}");</script>
<!-- End Google Tag Manager -->

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
  *{
      position: relative;
      box-sizing: border-box;
      font-family: Arial,Microsoft JhengHei,黑体,宋体,sans-serif;
    }
    .wrapper{
    	overflow: hidden;
    }
</style>


@yield('stylesheets')

</head>
  <body>
    <!-- Google Tag Manager (noscript) -->
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{config('app.gtm_id')}}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
  	<div class="wrapper">
@include('partials._navbar')



@yield('content')


@include('partials._footer')
	</div>
  </body>


{{ Html::script('js/jquery/jquery-3.2.1.min.js') }}
{{ Html::script('js/bootstrap/bootstrap.min.js') }}
{{ Html::script('js/prefix-free/prefixfree.dynamic-dom.min.js') }}
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
          // alert('Cart api error');
       }
  });

  $.ajax({
    type:'GET',
    url:'/getRunner',
    dataType:'json',
    success: function (response) {
          $('.runner').append(response);
       },
    error: function (data) {
    }
  });
    
});
function ajaxShowKart(){
  $('.item_class').remove();
  $.ajax({
    type:'GET',
    url:'/ajaxShowIndex',
    dataType:'json',
    success: function (response) {
      $.each(response,function(index,product){
        $('.modal-body-table').append('<tr class="item_class" id="item_'+product.id+'"><td><img src="'+ '{{asset('images/productsIMG')}}'+'/'+product.image+'"></td><td>'+product.name+'</td><td>'+product.price+'</td><td><button class="btn btn-sm btn-danger" onclick="delete_item('+product.id+');">刪除</button></td></tr>');

      });
    },
    error: function (data) {
      // alert('error');
    }
  });
}
function delete_item(id){
  $.ajax({
    type:'POST',
    url:'/kart/'+id,
    dataType:'json',
    data: {
      _method: 'delete',
    },
    success: function (response) {
              
        $('#item_'+id).remove();
        $('#item'+id).remove();
        $('#add_'+id).empty().append('加入<img src="{{asset('images/cart.png')}}">');
        $('#add_'+id).removeClass('deleteKartBtn')
        $('#add_'+id).attr('onclick','addToKart('+id+')');
        // navbar cart 減一
        var inKart = parseInt($('#inKart').html()) - 1;
        $('#inKart').empty().append(inKart);

        uploadSum();
    },
    error: function () {
        alert('無法從購物車中刪除');
    }
  });
}

function burgerUp(){
  $('.navbar-ul-left').css('display','block');
  $('.burger').css('display','none');
}
function burgerDown(){
  $('.navbar-ul-left').css('display','none');
  $('.burger').css('display','block'); 
}
</script>
<script type="text/javascript">
  $('#myModal').on('shown.bs.modal', function () {
    $('#myInput').trigger('focus');
  })
</script>
@yield('scripts')
</html>