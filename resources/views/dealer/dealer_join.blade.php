@extends('main')

@section('title','| 加入團購')

@section('stylesheets')
	{{Html::style('css/_dealer_join.css')}}
@endsection


@section('content')
	<div style="margin-top: 40px;margin-bottom: 40px;" class="container">
		<div class="row">
			<div class="col-md-8 offset-md-2">
				<div class="card">
					
					@if($group->image)
					<div>
						<img class="card-img-top" src="/images/groupIMG/{{$group->group_code}}/{{$group->image}}">
					</div>
					
					@endif
					
				  <div class="card-body p-4">
				    <h5 class="card-title">團購名稱：{{$group->title}}</h5>
				    <table class="table">
				    	<thead class="thead-dark">
						    <tr>
							  <th scope="col">商品</th>
							  <th scope="col">原價</th>
						      <th scope="col">團購價</th>
						    </tr>
						  </thead>
						  <tbody>
							@foreach($group->products as $product)
						    <tr>
							  	<td>{{$product->name}}</th>
								<td>{{$product->discription}}</td>
						      	<td>{{$product->price}}元</td>
						    </tr>
							@endforeach
						</tbody>
				    </table>
				    
				    <p class="card-text">截止日期：{{$group->deadline}}</p>
				    <p class="card-text">取貨地點：{{$group->address}}</p>
				    <p class="card-text">備註說明：{{$group->comment}}</p>
				    <p class="card-text"><small class="text-muted">開團日期：{{$group->created_at}}</small></p>
				  </div>
				  
				</div>


				<hr>	

				<div class="card">
					{{-- @if($group->deadline>=date("Y-m-d")) --}}
					<form class="mt-4 mb-4 pl-4 pr-4" id="join-group-form">
						
						<input style="display: none;" type="text" id="group_id" value="{{$group->id}}">
						<input style="display: none;" type="text" id="group_code" value="{{$group->group_code}}">
						

						<div class="form-group">
						    <label for="">姓名：</label>
						    <input type="text" class="form-control" placeholder="姓名" id="name">
						</div>
						<div class="form-group">
						    <label for="">聯絡電話：</label>
						    <input type="text" class="form-control" placeholder="聯絡電話" id="phone">
						</div>
						
							<div class="form-group">
								<label for="">產品：</label>

								@foreach($group->products as $product)
								<div class="form-check product-array">
								  <input class="product" type="checkbox" value="{{$product->id}}">
								  <label class="form-check-label" for="defaultCheck1">
									{{$product->name}} 價格：{{$product->price}} 
									{{-- 成團數量：<span id="max_{{$product->id}}">{{$product->min_for_dealer}}</span> --}}
								  </label>
								  <label class="form-check-label" for="defaultCheck1">
								    數量：
								  </label>
								  <input style="display: inline-block;width:56px;" type="number" min="1" value="1" class="ml-2 form-control amount">
								  {{-- <span>（剩餘：</span><span id="left_{{$product->id}}">{{$product->min_for_dealer - $group->productSum($product->id)}}</span><span>）</span> --}}
								</div>
								@endforeach
							
						</div>
						
						<div class="form-group">
						    <label for="">訂貨備註：</label>
						    <textarea class="form-control" id="comment"></textarea>
						    
						</div>	
						@if(!Session::has('success'))
							<div onclick="sendRequest();" class="mt-4 btn btn-primary btn-block">確定送出</div>
						@endif
					</form>
					<h2 id="success-alert" class="mt-4 mb-4" style="text-align: center;display: none;color: green;">訂單成功送出。</h2>
					{{-- @else
					<h2 class="mt-4 mb-4" style="text-align: center;">本團購已截止</h2>
					@endif --}}
				</div>

			</div>

		</div>
	</div>
@endsection

@section('scripts')
<script src="/js/_dealer_join.js"></script>
@endsection