@extends('main')

@section('title','| 經銷商')

@section('stylesheets')
	{{Html::style('css/_dealer_index.css')}}
@endsection


@section('content')
<div style="margin-top: 40px;margin-bottom: 40px;" class="container">
	<div class="mb-2">
		<button style="background-color: #d9534f;" class="btn text-white">開設新團購</button>	
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<table class="table">
			  <thead class="thead-dark">
			    <tr>
			      <th scope="col">#</th>
			      <th scope="col">團購</th>
			      <th scope="col">商品</th>
			      <th scope="col">數量</th>
			      <th scope="col">截止日期</th>
			      <th scope="col">狀態</th>
			      <th scope="col">-</th>
			    </tr>
			  </thead>
			  <tbody>
			    <tr>
			      <th scope="row">1</th>
			      <td>團購1</td>
			      <td>排骨</td>
			      <td>10/20</td>
			      <td><font color="gray">2019/04/22(四)</font></td>
			      <td><font color="red">未成團</font></td>	
			      <td>-</td>
			    </tr>
			    <tr>
			      <th scope="row">1</th>
			      <td>團購2</td>
			      <td>雞腿</td>
			      <td>16/15</td>
			      <td><font color="gray">2019/05/01(五)</font></td>
			      <td><font color="green">成團</font></td>
			      <td><button class="btn btn-sm btn-primary">提交</button></td>
			    </tr>
			    <tr>
			      <th scope="row">1</th>
			      <td>團購3</td>
			      <td>排骨</td>
			      <td>2/30</td>
			      <td>2019/06/10(三)</td>
			      <td><font color="gray">揪團中</font></td>
			      <td>-</td>
			    </tr>
			  </tbody>
			</table>

		</div>
	</div>
</div>
		
@endsection


@section('scripts')

@endsection