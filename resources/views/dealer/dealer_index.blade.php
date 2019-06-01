@extends('main')

@section('title','| 經銷商')

@section('stylesheets')
	{{Html::style('css/_dealer_index.css')}}
@endsection


@section('content')
<div style="margin-top: 40px;margin-bottom: 40px;min-height: 500px;" class="container">
	<div class="mb-2">
		<a style="background-color: #d9534f;" class="btn text-white" href="/dealer/create">開設新團購</a>	
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<table class="table">
			  <thead class="thead-dark">
			    <tr>
			      <th scope="col">#</th>
			      <th scope="col">團購</th>
			      <th scope="col">網址</th>
			      <th scope="col">截止日期</th>
			      <th scope="col">商品</th>
			      <th scope="col">數量</th>
			      <th scope="col">狀態</th>
			      <th scope="col">-</th>
			    </tr>
			  </thead>
			  <tbody>
			  	<?php $i=1; $success = false;$isSuccess = false?>
			  	@foreach($groups as $group)
			  	<?php $j = 0;?>
			  	@foreach($group->products as $product)
			  	<?php if ($group->productSum($product->id)>=$product->min_for_dealer) {
			  		$success = true;
			  		
			  	} ?>
			  	<tr>
			  		@if($j==0)
			  		<td rowspan="{{count($group->products)}}">{{$i}}</td>
			  		<td rowspan="{{count($group->products)}}">{{$group->title}}</td>
			  		<td rowspan="{{count($group->products)}}">
			  			<input id="group_url_{{$i}}" style="" type="text" value="{{'https://www.kingpork.com.tw'.'/dealer/'.$group->group_code}}">
			  			<button class="btn btn-sm btn-primary copy-btn" data-clipboard-target="#group_url_{{$i}}">複製</button>
			  			<a class="btn btn-sm btn-warning" target="_blank" href="/group-excel/{{$group->group_code}}">名單</a>
			  			<a class="btn btn-sm btn-success" href="{{'/dealer/'.$group->group_code}}" target="_blank">瀏覽</a>
			  		</td>
			  		<td rowspan="{{count($group->products)}}">{{$group->deadline}}</td>
			  		@endif
			  		
			  		<td>{{$product->name}}</td>
			  		<td>{{$group->productSum($product->id)}}/{{$product->min_for_dealer}}</td>
			  		<td>
			  			@if($group->deadline>=date("Y-m-d"))
			  				<font color="gray">揪團中</font>
			  			@else
			  				已截止
			  			@endif
			  			@if($group->productSum($product->id)>=$product->min_for_dealer)
							(<font color="green">成團</font>)
						@else
							(<font color="red">未成團</font>)
						@endif
					</td>
			  		@if($isSuccess == false && $success == true)
					<td rowspan="{{count($group->products)}}">

						@if(!$group->is_done)
						<div class="btn btn-sm btn-success" onclick="submit_group('{{$group->group_code}}');">送單</div>
						@else
						已送出
						@endif

					</td>

					<?php $isSuccess = true; ?>
					
			  		@endif
			  		
			  	</tr>
			  	<?php $j++; $success = false?>
			  	@endforeach
			  	
			  	<?php $i++; $isSuccess = false;?>
			  	@endforeach
			    
			    
			  </tbody>
			</table>
			
		</div>
	</div>


	<div style="display: none;">
		<form id="submit_form" action="{{route('bill.store')}}" method="POST">
			{{csrf_field()}}
			<input type="text" name="ship_name" value="{{Auth::user()->name}}">
			<input type="text" name="ship_phone" value="{{Auth::user()->phone}}">
			<input type="text" name="ship_email" value="{{Auth::user()->email}}">
			<input type="text" name="bonus" value="0">
			<input type="text" name="ship_receipt" value="2">
			<input type="text" name="ship_arrive" value="no">
			<input type="text" name="time" value="no">
			<input id="submit_address" type="text" name="ship_address" value="">
			<input type="text" name="ship_pay_by" value="ATM">
		</form>
	</div>
	

</div>
		
@endsection


@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/clipboard@2/dist/clipboard.min.js"></script>
<script src="/js/_dealer_index.js"></script>
@endsection