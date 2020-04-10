@extends('main')

@section('title','| 團購')

@section('stylesheets')
	{{Html::style('css/_dealer_index.css')}}
@endsection


@section('content')

<?php

$groupTotal = [];
$productSum = [];
$productTotalPrice = [];

foreach ($groups as $group) {
	$productTotal = 0;
	foreach ($group->products as $product) {
		$sum = $group->productSum($product->id);
		$productSum[$group->group_code][$product->id] = $sum;
		$productTotalPrice[$group->group_code][$product->id] = ($sum * $product->price);
		$productTotal += $sum * $product->price;
	}
	$groupTotal[$group->group_code] = $productTotal;
}

?>
<div style="margin-top: 40px;margin-bottom: 40px;min-height: 500px;" class="container">
	<div class="mb-2">
		<a style="background-color: #d9534f;" class="btn text-white" href="/group/create">開設新團購</a>	
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
			      <th scope="col">目前數量</th>
				  <th scope="col">狀態</th>
				  <th scope="col">目前總金額</th>
			      <th scope="col">-</th>
			    </tr>
			  </thead>
			  <tbody>
			  	<?php $i=1; $threshold = 5000;?>
			  	@foreach($groups as $group)
				  	<?php 
					  $j = 0;
					?>
			  	@foreach($group->products as $index => $product)
				  	<?php 
					  $total = $groupTotal[$group->group_code]; 
					  $_productSum = $productSum[$group->group_code][$product->id];
					  $_productTotalPrice = $productTotalPrice[$group->group_code][$product->id];
					?>
			  	<tr>
			  		@if($j==0)
			  		<td rowspan="{{count($group->products)}}">{{$i}}</td>
			  		<td rowspan="{{count($group->products)}}">{{$group->title}}</td>
					  <td rowspan="{{count($group->products)}}">
						<div>
							<input id="group_url_{{$i}}" style="" type="text" value="{{'https://www.kingpork.com.tw'.'/group/'.$group->group_code}}">
						</div>
			  			<div>
							<button class="btn btn-sm btn-primary copy-btn" data-clipboard-target="#group_url_{{$i}}">複製</button>
							<a class="btn btn-sm btn-warning" target="_blank" href="/group-excel/{{$group->group_code}}">名單</a>
							<a class="btn btn-sm btn-success" href="{{'/group/'.$group->group_code}}" target="_blank">瀏覽</a>
						</div>
			  		</td>
			  		<td rowspan="{{count($group->products)}}">{{$group->deadline}}</td>
			  		@endif
			  		
			  		<td>{{$product->name}}</td>
			  		<td>{{$_productSum}}({{$_productTotalPrice}})</td>
			  		
					
					@if($j==0)
					<td rowspan="{{count($group->products)}}">
						@if($group->deadline>=date("Y-m-d"))
							<font color="gray">揪團中</font>
						@else
							已截止
						@endif
				  	</td>
					<td rowspan="{{count($group->products)}}">
						{{$total}}
					</td>
					<td rowspan="{{count($group->products)}}">
						@if($total >= $threshold)
							@if(!$group->is_done)
					<div class="btn btn-sm btn-success" onclick="show_submit_form('{{$group->group_code}}','{{$group->title}}');" data-toggle="modal" data-target="#submit-form-modal">送單</div>
							@else
								已送出
							@endif
						@else
							未達標
						@endif
					</td>
					@endif
			  		
			  	</tr>
			  	<?php $j++;?>
			  	@endforeach
			  	
			  	<?php $i++;?>
			  	@endforeach
			    
			    
			  </tbody>
			</table>
			
		</div>
	</div>

	<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#submit-form-modal">
		Launch demo modal
	  </button>
	<!-- Modal -->
	<div class="modal fade" id="submit-form-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
			<h5 class="modal-title" id="model-title"></h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
			</div>
			<div class="modal-body">
				<form id="submit_form" action="{{route('bill.store')}}" method="POST">
					{{csrf_field()}}
					<input type="hidden" name="ship_email" value="{{Auth::user()->email}}">

					<label>收件人</label>
					<div class="form-group">
						<input class="form-control" type="text" name="ship_name" value="{{Auth::user()->name}}">
					</div>

					<label>手機</label>
					<div class="form-group">
						<input class="form-control" type="text" name="ship_phone" value="{{Auth::user()->phone}}">
					</div>
					
					<label>使用紅利(可用:{{Auth::user()->bonus}})</label>
					<div class="form-group">
						<input class="form-control" type="number" min="0" name="bonus" value="0">
					</div>
					
					<label>發票</label>
					<div class="form-group">
						<select id="ship_receipt" name="ship_receipt" class="form-control">
							<option value="2">二連</option>
							<option value="3">三聯</option>
						</select>
					</div>

					<div id="receipt_three_group" style="display:none;">
						<label>統編號碼</label>
						<div class="form-group">
							<input class="form-control" type="text" name="ship_three_id"" value="" placeholder="統編號碼">
						</div>

						<label>公司抬頭</label>
						<div class="form-group">
							<input class="form-control" type="text" name="ship_three_company" value="" placeholder="公司抬頭">
						</div>
					</div>

					<input type="hidden" name="ship_arrive" value="no">
					<input type="hidden" name="time" value="no">
					<input id="submit_address" type="hidden" name="ship_address" value="">

					<label>付款方式</label>
					<div class="form-group">
						<select name="ship_pay_by" class="form-control">
							<option value="ATM">ATM轉帳</option>
							<option value="CREDIT">信用卡</option>
							<option value="cod">貨到付款</option>
						</select>
					</div>
					
				</form>
			</div>
			<div class="modal-footer">
				<button id="form-submit-button" type="button" class="btn btn-success" onclick="submit_group()">確認送出</button>
			</div>
		</div>
		</div>
	</div>

	

</div>
		
@endsection


@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/clipboard@2/dist/clipboard.min.js"></script>
<script src="/js/_dealer_index.js"></script>
@endsection