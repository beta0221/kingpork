hello world!
this is test.blade.php

@foreach($products as $product)
	{{$product->slug}}
	{{$product->price}}
	{{asset('/images/productsIMG').'/'. $product->image}}
	{{$product->name}}
	{{$product->discription}}
	{{$product->bonus}}
@endforeach