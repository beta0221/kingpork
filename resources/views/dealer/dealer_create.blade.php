@extends('main')
<?php 
	$edit = false;
	if(isset($group)){
		$edit = true;
	}
	$title = ($edit)?'編輯團購':'開設團購';
?>
@section('title',"| $title")

@section('stylesheets')
	{{Html::style('css/_dealer_create.css')}}
@endsection


@section('content')

<div style="margin-top: 40px;margin-bottom: 40px;" class="container">
	<div class="row mb-2">
		<div class="col-md-6 offset-md-3">
			<a class="btn btn-warning" href="/group">回上頁</a>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6 offset-md-3 form-box">
			@if($edit && !empty($group->image))
			<div>
				<?php $href = "/images/groupIMG/$group->group_code/$group->image" ?>
				<img style="width:100%" src="{{$href}}">
			</div>
			@endif

			<?php 
				$action = route('group.store');
				if($edit){
					$action = route('group.update',$group->id);
				}
			?>
			<form class="mt-4 mb-4" action="{{$action}}" method="POST" enctype="multipart/form-data">
				{{csrf_field()}}
				@if($edit)
					<input type="hidden" name="_method" value="PUT">
				@endif
					<div class="form-row">
					  <div class="form-group col-md-12">
					    <label for="exampleInputEmail1">揪團主題名稱：</label>
					  	<input type="text" class="form-control" id="exampleInputEmail1" placeholder="揪團主題名稱" name="title" value="{{($edit)?$group->title:''}}">
					  </div>
					  	
					</div>
					
					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="exampleFormControlFile1">封面圖片：(圖片格式：gif,jpg,png)</label>
							<input type="file" class="form-control-file" id="exampleFormControlFile1" name="image" accept="image/*">
						</div>
					</div>
					
					
					
						<div class="form-row">
							<div class="form-group col-md-12">
								<label for="">選擇產品：</label>
								@foreach($products as $product)
								@if($product->public == 1)
									<div class="form-check">
										@if(!$edit)
											<input name="products[]" type="checkbox" value="{{$product->id}}">
										@endif
										<label class="form-check-label" for="">
											{{$product->name}} 價格:{{$product->price}} (原價:{{$product->discription}})
										</label>
									</div>
								@endif
							@endforeach
							</div>
						</div>
					
					
					
				  	<div class="form-row">
				  		<div class="form-group col-md-12">
				  			<label for="deadline">截止日期：</label>
				  			<input type="date" class="form-control" id="deadline" name="deadline" value="{{($edit)?$group->deadline:''}}">
				  		</div>
				  	</div>
					
					<div class="form-row">
						<div class="form-group col-md-12">
						    <label for="">取貨地址：</label>
							<input type="text" class="form-control" id="" placeholder="送貨地址" name="address" value="{{($edit)?$group->address:''}}">
						    
						</div>	
					</div>
					

					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="">備註說明：</label>
							<textarea rows="5" class="form-control" name="comment">{{($edit)?$group->comment:''}}</textarea>
						</div>
					</div>

					<div class="form-row pl-3 pr-3 mt-3">
						<button type="submit" class="btn btn-primary col-md-12">{{($edit)?'更新':'確定開團'}}</button>		
					</div>
				  
				  
				
			</form>	
		</div>
	</div>
</div>
	
@endsection


@section('scripts')
	{{ Html::script('js/_dealer_create.js') }}
@endsection