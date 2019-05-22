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
					<img class="card-img-top" src="/images/groupIMG/{{$group->group_code}}/{{$group->image}}">
					@endif
					
				  <div class="card-body p-4">
				    <h5 class="card-title">團購名稱：{{$group->title}}</h5>
				    <table class="table">
				    	<thead class="thead-dark">
						    <tr>
						      <th scope="col">商品</th>
						      <th scope="col">團購價</th>
						      <th scope="col">成團數量</th>
						    </tr>
						  </thead>
						  <tbody>
							@foreach($group->products as $product)
						    <tr>
						      <td>{{$product->name}}</th>
						      <td>{{$product->price}}</td>
						      <td>{{$group->productSum($product->id)}}/{{$product->min_for_dealer}}</td>
						    </tr>
							@endforeach
						</tbody>
				    </table>
				    
				    <p class="card-text">截止日期：{{$group->deadline}}</p>
				    <p class="card-text">內容：{{$group->comment}}</p>
				    <p class="card-text"><small class="text-muted">開團日期：{{$group->created_at}}</small></p>
				  </div>
				  
				</div>


				<hr>	

				<div class="card">

					<form class="mt-4 mb-4 pl-4 pr-4" action="/join-group" method="POST">
						{{csrf_field()}}
						<input style="display: none;" type="text" name="group_id" value="{{$group->id}}">
						<input style="display: none;" type="text" name="group_code" value="{{$group->group_code}}">
						@if(Session::has('success'))
						<div class="mb-4 btn btn-success btn-block">
							{{Session('success')}}
						</div>
						@endif

						<div class="form-group">
						    <label for="">姓名：</label>
						    <input type="text" class="form-control" id="" placeholder="姓名" name="name">
						</div>
						<div class="form-group">
						    <label for="">聯絡電話：</label>
						    <input type="text" class="form-control" id="" placeholder="聯絡電話" name="phone">
						</div>
						
							<div class="form-group">
								<label for="">產品：</label>

								@foreach($group->products as $product)
								<div class="form-check">
								  <input class="" type="checkbox" value="{{$product->id}}" id="defaultCheck1" name="product[]">
								  <label class="form-check-label" for="defaultCheck1">
								    商品：{{$product->name}} 價格：{{$product->price}} 成團數量：{{$product->min_for_dealer}}
								  </label>
								  <label class="form-check-label" for="defaultCheck1">
								    數量：
								  </label>
								  <input style="display: inline-block;width:56px;" class="ml-2 form-control" name="amount[]">
								</div>
								@endforeach
							
						</div>
						
						<div class="form-group">
						    <label for="">送貨地址：</label>
						    <input type="text" class="form-control" id="" placeholder="送貨地址" name="address">
						</div>	
						@if(!Session::has('success'))
							<button class="mt-4 btn btn-primary btn-block">確定送出</button>
						@endif
					</form>
				</div>

			</div>

		</div>
	</div>
@endsection

@section('scripts')

@endsection