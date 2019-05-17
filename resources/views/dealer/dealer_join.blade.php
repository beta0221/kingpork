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
					<img class="card-img-top" src="/images/productsIMG/both.png">
				  <div class="card-body p-4">
				    <h5 class="card-title">團購名稱：團購名稱</h5>
				    <p class="cart-text">商品：商品</p>
				    <p class="cart-text">成團數量：20</p>
				    <p class="cart-text">目前數量：10</p>
				    <p class="card-text">截止日期：2019/06/22 (四)</p>
				    <p class="card-text">內容：Lorem ipsum dolor sit amet, consectetur adipisicing elit. Vero aliquam ipsum iusto cum delectus debitis, error ab dicta, quae tempore, non quasi id itaque corrupti voluptatibus modi accusamus aut explicabo?</p>
				    <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
				  </div>
				  
				</div>


				<hr>	

				<div class="card">
					<form class="mt-4 mb-4 pl-4 pr-4">
						<div class="form-group">
						    <label for="">姓名：</label>
						    <input type="text" class="form-control" id="" placeholder="送貨地址">
						    
						</div>	
						<div class="form-group">
						    <label for="">聯絡電話：</label>
						    <input type="text" class="form-control" id="" placeholder="聯絡電話">
						    
						</div>	
						<div class="form-group">
						    <label for="">數量：</label>
						    <input type="number" class="form-control" id="" min="1">
						    
						</div>	
						<div class="form-group">
						    <label for="">送貨地址：</label>
						    <input type="text" class="form-control" id="" placeholder="送貨地址">
						    
						</div>	
						
						<button class="mt-4 btn btn-primary btn-block">確定送出</button>
						
					</form>
				</div>

			</div>

		</div>
	</div>
@endsection

@section('scripts')

@endsection