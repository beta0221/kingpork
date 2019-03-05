@extends('main')

@section('title','| 訂購相關')

@section('stylesheets')
<style>
.content{
	margin:60px 0 80px 0;
}
</style>
{{Html::style('css/_process.css')}}
@endsection

@section('content')

<div class="content">
	<div class="container">
		<div class="row">
			<div class="col-md-10 offset-md-1">
				<ul class="process">
					<li class="process-4">
						<div class="process-bg process-1 processing"></div>
						<img src="{{asset('images/step-1-1.png')}}">
					</li>
					<li class="process-g">
						<img src="{{asset('images/arrow-right.png')}}">
					</li>
					<li class="process-4">
						<div class="process-bg process-2 processing"></div>
						<img src="{{asset('images/step-1-2.png')}}">
					</li>
					<li class="process-g">
						<img src="{{asset('images/arrow-right.png')}}">
					</li>
					<li class="process-4">
						<div class="process-bg processing"></div>
						<img src="{{asset('images/step-1-3.png')}}">
					</li>
					<li class="process-g">
						<img src="{{asset('images/arrow-right.png')}}">
					</li>
					<li class="process-4">
						<div class="process-bg processing"></div>
						<img src="{{asset('images/step-1-4.png')}}">
					</li>
				</ul>
				<ul class="process">
					<il class="process-4"><p>STEP.1</p><p>放入購物車</p></il>
					<il class="process-g">　</il>
					<il class="process-4"><p>STEP.2</p><p>填寫寄送資料</p></il>
					<il class="process-g">　</il>
					<il class="process-4"><p>STEP.3</p><p>結帳付款</p></il>
					<il class="process-g">　</il>
					<il class="process-4"><p>STEP.4</p><p>完成，貨物送出</p></il>
				</ul>

				<p>　</p><hr width="100%"><p>　</p>
				
				<p>親愛的網友您好，歡迎您來到金園排骨美食世界購物網站，在您成為金園排骨美食世界購物網站的會員之後，即可盡情選購金園排骨美食世界各式美食商品。我們提供一個美味、便利、安全的線上購物平台，讓您在此一次滿足所有的購物需求。<br /><br />【付款方式】<br />▪ATM轉帳<br />▪信用卡刷卡<br />▪貨到付款<br /><br />----------------------------------------------------------------------------------------------------------------------<br /><br />【交貨方式】<br />▪黑貓宅急便<br /><br />----------------------------------------------------------------------------------------------------------------------<br /><br />網路訂購(滿$868享免運費，未滿加收運費$150)<br /><br />【訂購／到貨須知】<br />1.訂購商品於平日星期一至星期五當日12:00前，完成訂單付款確認成功者，可於次日送達，其餘將於次次日送達(以此類推)，如為週五下午及週六日訂購則到貨日最快於星期二做配送。<br />2.所有訂購細則如因資料有誤或無法收貨、或遇颱風地震不可抗拒天災等，出貨時間將順延。<br /><br /><br />

					<p><font style="background-color: purple;padding: 4px" color="white">【商品售出概不得退貨】</font></p>
					<p>金園排骨冷凍商品，在全程冷凍下可保存6個月，消費者收到商品後無法確保全程冷凍，故本公司冷凍商品售出，到貨簽收前可辦理退貨，到貨簽收後概不得退貨，若商品本身有瑕疵除外。</p>

					<p><font style="background-color: red" color="white">※行政院公布「通訊交易解除權合理例外情事適用準則」，已於 105 年 1 月 1 日起施行</font></p>
					<p>一、 不適用消費者保護法中 7 天猶豫期的例外情形，因應消費者保護法第 19 條修正，行政院於 104 年 12 月 31 日公布「通訊交易解除權合理例外情事適用準則」，並於 105 年 1 月 1 日起 施行。</p>
					<p>二、 依「通訊交易解除權合理例外情事適用準則」，於網路買賣下列商品，得不適用消保法 7 天 猶豫期之規定，<font color="red">所以如果店家有針對下列商品預先告知消費者不提供 7 天猶豫期時，消費 者就不得向店家主張 7 天猶豫期退貨。</font></p>
					<p>第一款商品有易於腐敗、保存期限較短或解約時即將逾期之性質。因其本身容易快速變質腐 壞，保存期限少於七日，或雖較七日稍長，惟解約時即將逾期，均不適宜退還後再出售。</p>

					<p><font style="background-color: purple;padding: 4px" color="white">【商品瑕疵退、換貨】</font></p>
					<p>若您要辦理退、換貨，可透過『聯絡我們』線上留言說明退、換貨或直接來電至免付費專線，我們將於接獲申請之次日起3個工作天內檢視您的退、換貨要求，檢視完畢後將以E-mail或電話回覆通知您，並將委託本公司指定之宅配公司，在5個工作天內透過電話與您連絡前往取回退、換貨商品。請您在辦理退貨時，將您願訂購時的紙本發票連同退貨商品一併寄回本公司；若您所收到的紙本發票已經遺失，就無法辦理退貨，只能辦理換貨。</p>

					<hr>

					<p>送貨範圍限台灣本島地區，外島需另補運費。(費用部份，請來電洽詢)</p>



					{{-- 【退換貨說明】<br />1.金園排骨線上購物的消費者，都可依照消費者保護法的規定，享有商品貨到日起7天猶豫期的權益。但猶豫期並非試用期，所以您所退回的商品必須是全新且完整包裝狀態。因本商品為食品類故退貨時不得拆封包裝，否則恕不接受退換貨。<br /><br />2.若您要辦理退換貨，可透過『聯絡我們』線上留言說明退換貨或直接來電至免付費專線，我們將於接獲申請之次日起3個工作天內檢視您的退換貨要求，檢視完畢後將以E-mail或電話回覆通知您，並將委託本公司指定之宅配公司，在5個工作天內透過電話與您連絡前往取回退換貨商品。<br /><br />請您保持電話暢通，並備妥原商品及所有包裝及附件，以便於交付予本公司指定之宅配公司取回（宅配公司僅負責收件，退貨商品仍由特約廠商進行驗收），宅配公司取件後會提供簽收單據給您，請注意留存。<br /><br />3.當您收到的商品，是因本公司處理訂單而發生如寄送錯誤商品、商品破損、缺少商品等狀況，請您在到貨3日內(自簽收日起算)來電通知，本公司將儘速更換新品或依消費者之要求儘速處理。<br /><br />寄送運費由本公司支付；但若為消費者不要者（超過7天猶豫期），原則上由消費者支付此筆退換貨運費，如有其它問題歡迎來電或來信詢問，以利本公司為您處理。<br /><br /><br />E-mail:may@sacred.com.tw/kingpork@sacred.com.tw<br />免付費專線:0800-552-999<br /><br /><br />【發票處理說明】<br />1.若您在購買流程中已要求本公司寄送紙本發票，請您在辦理退貨時，將您所收到的紙本發票連同退換商品一併寄回本公司；若您所收到的紙本發票已經遺失，請先來電詢問本公司處理方式。<br /><br />2.紙本發票以掛號方式寄回總公司<br />地址：(330)桃園市桃園區大有路59號3樓<br />金園排骨股份有限公司（會計部）收。<br /><br />3.本公司於收到您所退回的商品、紙本發票後，經確認無誤，將於7個工作天內為您辦理退款，退款日當天會再發送E-mail通知函或以電話通知您。<br /><br />【海內外配送運費說明】<br />送貨範圍限台灣本島地區，外島需另補運費。(費用部份，請來電洽詢)</p>				 --}}
			</div>
		</div>
	</div>


</div>



@endsection

@section('scripts')

@endsection