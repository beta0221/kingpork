@extends('main')

@section('title','| 產品')

@section('stylesheets')
<style>
.content{
	margin:60px 0 80px 0;
}
/*-----------------------------------------------*/
.productsBar{
	height: 160px;
	/*border:1pt solid #000;*/
	margin-bottom: 60px;
}
.catImg{
	overflow: hidden;
	height: 100%;
	border-radius: 0.3em;
	box-shadow: 2px 2px 16px 2px rgba(0, 0, 0, 0.15);
	filter: opacity(90%);
}
.catImg:hover{
	filter:opacity(90%);
}
.catImg>img{
	max-width: 100%;
	top: -50%;
}
#currentCat{
	filter:opacity(90%);
}
.P-pork::before{
	content: "排骨";
	color: #fff;
	font-size: 22pt;
	left: 16px;
	top: 8px;
	position: absolute;
	z-index: 1;
}
.P-both::before{
	content: "幸福雙響";
	color: #fff;
	font-size: 22pt;
	left: 16px;
	top: 8px;
	position: absolute;
	z-index: 1;
}
.P-chicken::before{
	content: "雞腿";
	color: #fff;
	font-size: 22pt;
	left: 16px;
	top: 8px;
	position: absolute;
	z-index: 1;
}
/*-----------------------------------------------*/
.aboutProduct{
	padding:  60px 0 0 0;
	height: 100vh;
	overflow: scroll;
}
</style>
@endsection

@section('content')
<div class="content">
	<div class="container">
		<div class="row productsBar">
			<div class="col-md-4">
				<div class="catImg P-pork">
					<a href="{{route('productCategory.show',3)}}">
						<img src="http://localhost:8000/images/productsIMG/both.jpg" alt="">	
					</a>
				</div>
			</div>
			<div class="col-md-4">
				<div class="catImg P-both">
					<a href="{{route('productCategory.show',4)}}">
						<img src="http://localhost:8000/images/productsIMG/both.jpg" alt="">	
					</a>
				</div>
			</div>
			<div class="col-md-4">
				<div class="catImg P-chicken">
					<a href="{{route('productCategory.show',2)}}">
						<img src="http://localhost:8000/images/productsIMG/chicken.jpg" alt="">
					</a>
				</div>
			</div>
		</div>
		<hr style="margin-top: 0;margin-bottom: 0;">
		<div class="row">
			<div class="aboutProduct col-md-10 offset-md-1">
				排骨介紹文案
				Lorem ipsum dolor sit amet, consectetur adipisicing elit. Itaque consectetur, assumenda praesentium, dolores delectus nobis voluptatum eum amet quasi cum iure, dolor nemo necessitatibus! Excepturi quia ratione quod laborum quasi fugit eum aliquam atque, explicabo odio dicta ducimus in quisquam blanditiis amet praesentium corporis aspernatur sunt totam consequuntur fuga eaque laboriosam nisi rerum! Quo voluptatem nisi omnis aliquam ut ducimus delectus ipsum totam voluptatibus aliquid magni, perferendis sit, veniam, id laboriosam ipsa earum corporis at reprehenderit commodi eum, iure. Nesciunt quam voluptates nisi itaque ea fugiat, iste modi harum omnis necessitatibus corporis dolor adipisci hic amet, quidem ratione commodi blanditiis esse quos neque, velit vitae autem recusandae! Dolorum ipsa at nam nesciunt in consectetur cum voluptate placeat aperiam earum architecto facilis recusandae dignissimos necessitatibus iure quisquam aspernatur, totam minus expedita quidem quam temporibus aut ipsum! Quasi, nisi vero voluptate aperiam architecto ratione, autem pariatur dolorum animi modi at amet libero recusandae perspiciatis quam sapiente ducimus beatae cum optio perferendis totam nulla. Omnis dolores, quos dolore illum cumque, assumenda libero ad sit rem, illo repellendus! Unde id, sit dolorem, maxime autem sunt fuga quia modi excepturi odio hic similique suscipit error sed labore temporibus necessitatibus ipsa veniam. Iusto enim quibusdam dolores soluta cupiditate, voluptatum quae recusandae laudantium sint aspernatur amet quia rem tenetur placeat sequi omnis excepturi ut unde deleniti! Eius, repellat! Similique consectetur voluptatibus voluptas velit, vitae amet ipsum laborum a. Doloremque consectetur culpa adipisci ullam autem, repellat eaque est quibusdam expedita rerum quas animi error beatae impedit pariatur delectus explicabo repudiandae iusto. Ducimus aspernatur reprehenderit, nam! Labore pariatur nihil, sequi officia repudiandae quas placeat enim nisi, similique debitis. Numquam fugit repudiandae dignissimos ipsam iure illo, inventore fugiat expedita blanditiis quisquam, sequi itaque placeat, saepe quidem, libero assumenda deserunt omnis dolore cum. Magni eligendi praesentium est dignissimos id ea odit sed vitae iste. Esse ratione atque assumenda at velit aliquid reprehenderit possimus ducimus earum ad odit modi exercitationem, aut beatae accusantium nisi, quas qui magnam commodi suscipit, culpa consequatur dolorem sit. Ea rem, excepturi explicabo exercitationem? Harum error velit nostrum provident. Ducimus aspernatur placeat veniam aliquid officia eligendi quis, fugiat corporis alias assumenda illum hic, perferendis odit cupiditate beatae modi excepturi repudiandae minus earum. Veritatis omnis eligendi, nisi! Cum, esse quod sequi. Repellat, architecto dolore, ea tempora maxime numquam aperiam enim suscipit porro quibusdam excepturi eum velit. Possimus animi, reiciendis magni cumque dignissimos explicabo neque corporis, eveniet, itaque delectus consequatur repellat ipsam dolorum! Doloribus, deserunt, similique sint, quam facere delectus id animi culpa reprehenderit nobis ex accusamus magnam. Exercitationem blanditiis sint harum laudantium consequatur, illo repudiandae totam, sapiente numquam quis perspiciatis, assumenda officiis rerum ab accusantium quas nemo repellendus dolore possimus dolorum. Eligendi minus, dolor facilis modi at? Saepe mollitia, molestiae iste dolore est vero ut suscipit obcaecati necessitatibus nostrum. Magni perspiciatis possimus totam facilis a iste nulla velit repellat modi, vitae, vero deserunt eos. Aliquam eaque debitis quos dolores sequi veniam, et, accusamus animi vel iusto nulla recusandae nemo, in ab nam earum delectus aut fugit beatae corrupti optio.
			</div>
		</div>
	</div>
</div>
		

@endsection

@section('scripts')
@endsection