@extends('main')

@section('title','| 開設團購')

@section('stylesheets')
	{{Html::style('css/_dealer_create.css')}}
@endsection


@section('content')
<div style="margin-top: 40px;margin-bottom: 40px;" class="container">
	<div class="row">
		<div class="col-md-6 offset-md-3 form-box">
			<form class="mt-4 mb-4">
					<div class="form-row">
					  <div class="form-group col-md-12">
					    <label for="exampleInputEmail1">揪團主題名稱：</label>
					    <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="揪團主題名稱">
					    {{-- <small id="emailHelp" class="form-text text-muted">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Illo, fugit maiores sapiente aliquam voluptatibus dicta nihil est voluptate optio repellat cupiditate</small> --}}
					  </div>
					  	
					</div>
					
					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="exampleFormControlFile1">封面圖片：</label>
							<input type="file" class="form-control-file" id="exampleFormControlFile1">
						</div>
					</div>
					
					
					<div class="form-row">

						  <div class="form-group col-md-12">
						  	<label for="">選擇產品：</label>
							<div class="form-check">
							  <input class="" type="checkbox" value="" id="defaultCheck1">
							  <label class="form-check-label" for="defaultCheck1">
							    商品：ＸＸＸ 價格：ＸＸＸ 最低數量：ＸＸ
							  </label>
							</div>
							<div class="form-check">
							  <input class="" type="checkbox" value="" id="defaultCheck1">
							  <label class="form-check-label" for="defaultCheck1">
							    商品：ＸＸＸ 價格：ＸＸＸ 最低數量：ＸＸ
							  </label>
							</div>
						  </div>
						
						
					</div>
					
				  	<div class="form-row">
				  		<div class="form-group col-md-12">
				  			<label for="deadline">截止日期：</label>
				  			<input type="date" class="form-control" id="deadline">
				  		</div>
				  	</div>
					
					<div class="form-row">
						<div class="form-group col-md-12">
						    <label for="">取貨地址：</label>
						    <input type="text" class="form-control" id="" placeholder="送貨地址">
						    
						</div>	
					</div>
					

					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="">備註說明：</label>
							<textarea rows="5" class="form-control">
								
							</textarea>
						</div>
					</div>

					<div class="form-row pl-3 pr-3 mt-3">
						<button type="submit" class="btn btn-primary col-md-12">確定開團</button>		
					</div>
				  
				  
				
			</form>	
		</div>
	</div>
</div>
	
@endsection


@section('scripts')
	{{ Html::script('js/_dealer_create.js') }}
@endsection