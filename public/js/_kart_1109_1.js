
const CityCountyData = {
	"基隆市": [
		"仁愛區", "信義區", "中正區", "中山區", "安樂區", "暖暖區", "七堵區"
	],
	"台北市": [
		"中正區", "大同區", "中山區", "松山區", "大安區", "萬華區", "信義區", "士林區", "北投區", "內湖區", "南港區", "文山區"
	],
	"新北市": [
		"板橋區", "新莊區", "中和區", "永和區", "土城區", "樹林區", "三峽區", "鶯歌區", "三重區", "蘆洲區", "五股區", "泰山區", "林口區", "八里區", "淡水區", "三芝區", "石門區", "金山區", "萬里區", "汐止區", "瑞芳區", "貢寮區", "平溪區", "雙溪區", "新店區", "深坑區", "石碇區", "坪林區", "烏來區", 
	],
	"桃園市": [
		"桃園區", "中壢區", "平鎮區", "八德區", "楊梅區", "蘆竹區", "大溪區", "龍潭區", "龜山區", "大園區", "觀音區", "新屋區", "復興區", 
	],
	"新竹市": [
		"東區", "北區", "香山區"
	],
	"新竹縣": [
		"竹北市", "竹東鎮", "新埔鎮", "關西鎮", "湖口鄉", "新豐鄉", "峨眉鄉", "寶山鄉", "北埔鄉", "芎林鄉", "橫山鄉", "尖石鄉", "五峰鄉"
	],
	"苗栗縣": [
		"苗栗市", "頭份市", "竹南鎮", "後龍鎮", "通霄鎮", "苑裡鎮", "卓蘭鎮", "造橋鄉", "西湖鄉", "頭屋鄉", "公館鄉", "銅鑼鄉", "三義鄉", "大湖鄉", "獅潭鄉", "三灣鄉", "南庄鄉", "泰安鄉"
	],
	"台中市": [
		"中區", "東區", "南區", "西區", "北區", "北屯區", "西屯區", "南屯區", "太平區", "大里區", "霧峰區", "烏日區", "豐原區", "后里區", "石岡區", "東勢區", "新社區", "潭子區", "大雅區", "神岡區", "大肚區", "沙鹿區", "龍井區", "梧棲區", "清水區", "大甲區", "外埔區", "大安區", "和平區"
	],
	"彰化縣": [
		"彰化市", "員林市", "和美鎮", "鹿港鎮", "溪湖鎮", "二林鎮", "田中鎮", "北斗鎮", "花壇鄉", "芬園鄉", "大村鄉", "永靖鄉", "伸港鄉", "線西鄉", "福興鄉", "秀水鄉", "埔心鄉", "埔鹽鄉", "大城鄉", "芳苑鄉", "竹塘鄉", "社頭鄉", "二水鄉", "田尾鄉", "埤頭鄉", "溪州鄉"
	],
	"南投縣": [
		"南投市", "埔里鎮", "草屯鎮", "竹山鎮", "集集鎮", "名間鄉", "鹿谷鄉", "中寮鄉", "魚池鄉", "國姓鄉", "水里鄉", "信義鄉", "仁愛鄉"
	],
	"雲林縣": [
		"斗六市", "斗南鎮", "虎尾鎮", "西螺鎮", "土庫鎮", "北港鎮", "林內鄉", "古坑鄉", "大埤鄉", "莿桐鄉", "褒忠鄉", "二崙鄉", "崙背鄉", "麥寮鄉", "臺西鄉", "東勢鄉", "元長鄉", "四湖鄉", "口湖鄉", "水林鄉"
	],
	"嘉義市": [
		"東區", "西區"
	],
	"嘉義縣": [
		"太保市", "朴子市", "布袋鎮", "大林鎮", "民雄鄉", "溪口鄉", "新港鄉", "六腳鄉", "東石鄉", "義竹鄉", "鹿草鄉", "水上鄉", "中埔鄉", "竹崎鄉", "梅山鄉", "番路鄉", "大埔鄉", "阿里山鄉"
	],
	"台南市": [
		"中西區", "東區", "南區", "北區", "安平區", "安南區", "永康區", "歸仁區", "新化區", "左鎮區", "玉井區", "楠西區", "南化區", "仁德區", "關廟區", "龍崎區", "官田區", "麻豆區", "佳里區", "西港區", "七股區", "將軍區", "學甲區", "北門區", "新營區", "後壁區", "白河區", "東山區", "六甲區", "下營區", "柳營區", "鹽水區", "善化區", "大內區", "山上區", "新市區", "安定區"
	],
	"高雄市": [
		"楠梓區", "左營區", "鼓山區", "三民區", "鹽埕區", "前金區", "新興區", "苓雅區", "前鎮區", "旗津區", "小港區", "鳳山區", "大寮區", "鳥松區", "林園區", "仁武區", "大樹區", "大社區", "岡山區", "路竹區", "橋頭區", "梓官區", "彌陀區", "永安區", "燕巢區", "田寮區", "阿蓮區", "茄萣區", "湖內區", "旗山區", "美濃區", "內門區", "杉林區", "甲仙區", "六龜區", "茂林區", "桃源區", "那瑪夏區"
	],
	"屏東縣": [
		"屏東市", "潮州鎮", "東港鎮", "恆春鎮", "萬丹鄉", "長治鄉", "麟洛鄉", "九如鄉", "里港鄉", "鹽埔鄉", "高樹鄉", "萬巒鄉", "內埔鄉", "竹田鄉", "新埤鄉", "枋寮鄉", "新園鄉", "崁頂鄉", "林邊鄉", "南州鄉", "佳冬鄉", "琉球鄉", "車城鄉", "滿州鄉", "枋山鄉", "霧臺鄉", "瑪家鄉", "泰武鄉", "來義鄉", "春日鄉", "獅子鄉", "牡丹鄉", "三地門鄉"
	],
	"台東縣": [
		"臺東市", "成功鎮", "關山鎮", "長濱鄉", "池上鄉", "東河鄉", "鹿野鄉", "卑南鄉", "大武鄉", "綠島鄉", "太麻里鄉", "海端鄉", "延平鄉", "金峰鄉", "達仁鄉"
	],
	"花蓮縣": [
		"花蓮市", "鳳林鎮", "玉里鎮", "新城鄉", "吉安鄉", "壽豐鄉", "光復鄉", "豐濱鄉", "瑞穗鄉", "富里鄉", "秀林鄉", "萬榮鄉", "卓溪鄉"
	],
	"宜蘭縣": [
		"宜蘭市", "頭城鎮", "羅東鎮", "蘇澳鎮", "礁溪鄉", "壯圍鄉", "員山鄉", "冬山鄉", "五結鄉", "三星鄉", "大同鄉", "南澳鄉"
	],
}

var sumBeforeDiscount = 0;
$(document).ready(function(){
		$.ajaxSetup({
  			headers: {
    			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  			}
		});

		uploadSum();
		
		//判斷今天日期 
		var d = new Date();
		var date = d.getDate();
		var year = d.getFullYear();
		var m = d.getMonth();
		var Month = ((m+1).toString().length === 1) ? "0" + m : m+1;
		var min = year+"-"+Month+"-"+date;
		// 指定到貨日從今天開始
		$('#arriveDate').attr('min',min);

		//------- 
		$('.quantity').change(function(){
			uploadSum();
		});
		$('#arriveYes').click(function(){
			$('#arriveDate').css('display','inline-block');
			$('.date_alert').css('display','inline-block');
			$('#date_alert_tr').css('display','table-row');
		});
		$('#arriveNo').click(function(){
			$('#arriveDate').css('display','none');
			$('#arriveDate').val(null);
			$('.date_alert').css('display','none');
			$('#date_alert_tr').css('display','none');
		});
		var date_alert = 1;
		$('.date_alert').click(function(){
			if (date_alert==0) {
				$('.date_alert').addClass('date_alert_after');
				date_alert = 1;
			}else if(date_alert==1){
				$('.date_alert').removeClass('date_alert_after');
				date_alert = 0;
			}
			
		});
		$('.two-three').change(function(){
			if ($(this).val() == '3'){
				$('.ifThree').css('display','inline-block');
			}else if($(this).val()=='2'){
				$('.ifThree').css('display','none');
				$('.ship_three').val(null);
			}
		});
		$('.ship_county').change(function(){
			updateDistrict($(this).val());
		});
		$('#ship_name').change(function(){
			if ($('#ship_name').val() != '') {
				$('#ship_name').removeClass('alerting');
			}
		});
		$('#ship_email').change(function(){
			if ($('#ship_email').val() != '') {
				$('#ship_email').removeClass('alerting');
			}
		});
		$('#ship_phone').change(function(){
			if ($('#ship_phone').val() != '') {
				$('#ship_phone').removeClass('alerting');
			}
		});
		$('#ship_county').change(function(){
			if ($('#ship_county').val() != '') {
				$('#ship_county').removeClass('alerting');
			}
		});
		$('#ship_address').change(function(){
			if ($('#ship_address').val() != '') {
				$('#ship_address').removeClass('alerting');
			}
		});
		$('.radio').change(function(){
			$('.pay_by').removeClass('alerting');
		});
		// $('#ship_three_name').change(function(){
		// 	$('#ship_three_name').removeClass('alerting');
		// });
		$('#ship_three_id').change(function(){
			$('#ship_three_id').removeClass('alerting');
		});
		$('#ship_three_company').change(function(){
			$('#ship_three_company').removeClass('alerting');
		});
		$('#bonus').change(function(){	//紅利
			var maxBonus = parseInt($('#myBonus span').html());
			var bonus = $('#bonus').val();
			if (bonus > maxBonus) {
				$('#bonus').val(maxBonus);
				bonus = maxBonus;
			}
			if (bonus % 50 != 0) {
				bonus = bonus - bonus%50;
				$('#bonus').val(bonus);
			}
			if (bonus / 50 > sumBeforeDiscount) {
				bonus = sumBeforeDiscount * 50;
				$('#bonus').val(bonus);
			}
			var bonusCount = bonus/50;
			var afterDis = sumBeforeDiscount - bonus / 50;
			$('#total-price-span').empty().append(sumBeforeDiscount + '-' + bonusCount + '=' + afterDis);
		});

		// $('#shipping-carrier').on('change',function(){
		// 	let carrier_id = $(this).val();
		// 	$('.pay_by .radio').prop('checked', false);
		// 	if(carrier_id == 1){
		// 		$('.family-column').show();
		// 		$('.blackcat-column').hide();
		// 	}else{
		// 		$('.family-column').hide();
		// 		$('.blackcat-column').show();
		// 	}
		// });

	});

	function updateDistrict(county){
		if (CityCountyData[county] == undefined) { return; }
		$('.ship_district').empty();
		let districts = CityCountyData[county];
		districts.forEach(district => {
			$('.ship_district').append(`<option value="${district}">${district}</option>`);
		});
	}

	function setDistrict(county, district) {
		if (CityCountyData[county] == undefined) { return; }
		let districts = CityCountyData[county];
		if (districts.includes(district)) {
			$('.ship_district').val(district);
		}
	}

	var unFinished = 0;
	var alertMsg = '';

	function errorMessage(selector,msg){
		$(selector).addClass('alerting');
		alertMsg = msg;
		unFinished = 1;
	}

	function checkForm(){
		unFinished = 0;
		alertMsg = '';
		$('.alert-field').empty()

		if(!$('#pay_by_credit').is(':checked') && !$('#pay_by_atm').is(':checked') && !$('#pay_by_cod').is(':checked') && !$('#pay_by_family').is(':checked')){
			errorMessage('.pay_by','請選擇付款方式');
		}
		if ($('#ship_name').val() == '') {
			errorMessage('#ship_name','＊號處不可空白');
		}
		if ($('#ship_email').val() == '') {
			errorMessage('#ship_email','＊號處不可空白');
		}

		var format = /[ `!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
		var ship_phone = $('#ship_phone').val();
		if (ship_phone == '') {
			errorMessage('#ship_phone','＊號處不可空白');
		}

		// if($('#shipping-carrier').val() == '1'){	//全家
		// 	if(ship_phone.substring(0,2) != '09'){
		// 		errorMessage('#ship_phone','請使用手機號碼以便接收到店簡訊通知');
		// 	}
		// 	else if(format.test(ship_phone)){
		// 		errorMessage('#ship_phone','格式錯誤，請勿輸入特殊符號');
		// 	}
		// 	else if(ship_phone.length != 10){
		// 		errorMessage('#ship_phone','格式錯誤');
		// 	}
		// }

		// if($('#shipping-carrier').val() == 0){
		if ($('#use_favorite_address').prop('checked') == false) {

			if ($('#ship_county').val() == '') {
				errorMessage('#ship_county','＊號處不可空白');
			}
	
			if ($('#ship_address').val() == '') {
				errorMessage('#ship_address','＊號處不可空白');
			}
			
		}
		// }else if($('#shipping-carrier').val() == 1){
		// 	$('.shipping-store').each(function(i,element){
		// 		if($(element).val() == ''){
		// 			errorMessage('.shipping-store','＊號處不可空白');
		// 		}
		// 	});
		// }

		if ($('#ship_ship_receipt').val() == '3') {
			var ship_three_id = $('#ship_three_id').val();
			if (ship_three_id=='') {
				errorMessage('#ship_three_id','請輸入統一編號');
			}else if(ship_three_id.length != 8){
				errorMessage('#ship_three_id','統編格式錯誤');
			}
			if ($('#ship_three_company').val()=='') {
				errorMessage('#ship_three_company','請輸入公司名稱');
			}
		}
		$('.alert-field').append('<strong>'+alertMsg+'</strong><br>');
		if(unFinished == 0){
			$('.kartForm').submit();
		}

	};
		
	function deleteWithAjax(id){

		
		$.ajax({
			type:'POST',
			url:'kart/'+id,
			dataType:'json',
			data: {
				_method: 'delete',
			},
			success: function (response) {
				window.location.reload();
            },
            error: function () {
                alert('無法從購物車中刪除');
            }
		});
	}

	function uploadSum(){

		// caculate quantityLimit
		let quantityLimit = {};
		Object.keys(relation).forEach((key) => {
			if(!$('.quantity-input-' + key).length) { return; }
			let quantity = parseInt($('.quantity-input-' + key).val());
			relation[key].forEach((id) => {
				if (quantityLimit[id] == undefined) {
					quantityLimit[id] = quantity;
				} else {
					quantityLimit[id] += quantity;
				}
			})
		});

		Object.keys(quantityLimit).forEach((id) => {
			let dom = $('.quantity-input-' + id);
			let limit = quantityLimit[id];
			dom.attr('max', limit);
			if(dom.val() > limit) {
				dom.val(limit);
			}
		});

		let total = 0;
		$('input.quantity').each(function() {
			let slug = $(this).attr('id');
			let quantity = $(this).val();
			let price = $(this).data('price');
			let sum = quantity * parseInt(price);
			$('#priceTag' + slug).empty().append(sum);
			total += sum;
		});

		if (total > 799) {
			$('#transport-item').attr('name','');
			$('#transport-quantity').attr('name','');
			$('#transport-fee').css('display','none');
		}else{
			total = total + 150;
			$('#transport-item').attr('name','item[]');
			$('#transport-quantity').attr('name','quantity[]');
			$('#transport-fee').css('display','table-row');
		}

		// 計算優惠折扣（前端動態計算）
		let promoDiscount = 0;
		if (typeof promotionalLink !== 'undefined' && promotionalLink !== null) {
			// 計算適用優惠的商品總額
			let applicableTotal = 0;

			$('.quantity').each(function(){
				let cat_id = $(this).data('cat_id');
				let product_id = $(this).data('id');
				let price = parseInt($(this).data('price'));
				let quantity = parseInt($(this).val());
				
				// 檢查此商品是否適用優惠
				let isApplicable = false;
				if (!promotionalLink.applicable_categories || promotionalLink.applicable_categories.length === 0) {
					// 沒有類別限制，全部商品適用
					isApplicable = true;
				} else if (promotionalLink.applicable_categories.includes(String(cat_id))) {
					// 商品類別在適用清單中
					isApplicable = true;
				}

				// // 如果適用，累加到適用總額
				if (isApplicable && product_id != 99999) { // 排除運費
					applicableTotal += price * quantity;
				}
			});

			// 計算折扣金額（適用商品總額 × 折扣百分比）
			promoDiscount = Math.round(applicableTotal * (promotionalLink.discount_percentage / 100));

			// 更新折扣顯示
			if (promoDiscount > 0) {
				$('#promo-discount-amount').text('-NT$ ' + promoDiscount.toLocaleString());
			}
		}

		// 套用優惠折扣
		if (promoDiscount > 0) {
			total = total - promoDiscount;
		}

		sumBeforeDiscount = total;
		$('#total-price-span').empty().append(total);

		if (total == 0) {
			$('.sureToBuy').css('display','none');
		}else{
			$('.sureToBuy').css('display','inline-block');
		}

	}

	function sureToBuy(){
		$('.kartTable').css('display','none');
		$('.shipping').css('display','table');
		$('#h-title').html('填寫寄送資料');
		$('.processing').removeClass('processing');
		$('.process-2').addClass('processing');

		$('.sure-to-buy-div').css('display','none');
		$('.check-out-form-div').css('display','block');

		onQuickRecipientChange();
	}

	function back_kart(){
		$('.kartTable').css('display','table');
		$('.shipping').css('display','none');
		$('#h-title').html('我的購物車');
		$('.processing').removeClass('processing');
		$('.process-1').addClass('processing');
		$('.alert-field').empty();

		$('.sure-to-buy-div').css('display','block');
		$('.check-out-form-div').css('display','none');
	}

	// 新增：快速收件人選擇功能
	function onQuickRecipientChange() {
		const selectedValue = $('#quick_recipient_select').val();
		
		if (selectedValue === '') {
			// 選擇手動輸入模式
			switchToManualMode();
			return;
		}
		
		// 選擇了已保存的收件人，進入快速選擇模式
		loadRecipientData(selectedValue);
	}

	function switchToManualMode() {
		
		// 顯示保存為常用地址選項和手動輸入提示
		$('#add_favorite_address').show();
		
		// 顯示手動地址輸入欄位
		$('#ship_county').show();
		$('#ship_district').show();
		$('#ship_address_column').show();
		
		// 重設選擇
		$('#use_favorite_address').prop('checked', false);
		
		// 重新啟用所有欄位
		enableFormFields();
		
		// 清空所有輸入欄位
		clearAllInputFields();
	}

	function clearAllInputFields() {
		// 清空收件人資訊
		$('#ship_name').val('');
		$('#ship_phone').val('');
		$('#ship_email').val('');
		
		// 重設性別選擇為預設
		$('#radio1').prop('checked', true);
		$('#radio2').prop('checked', false);
		
		// 清空地址資訊
		$('#ship_county').val('');
		$('#ship_district').empty().append('<option value="">地區</option>');
		$('#ship_address').val('');
		
		// 重設發票選項為二聯（預設）
		$('#ship_ship_receipt').val('2');
		$('.ifThree').css('display','none');
		$('#ship_three_id').val('');
		$('#ship_three_company').val('');
		
		// 清除勾選狀態
		$('input[name="add_favorite"]').prop('checked', false);
		
		// 移除任何驗證錯誤樣式
		$('.form-control').removeClass('alerting');
	}

	function disableFormFields() {
		// 停用收件人資訊欄位
		$('#ship_name').prop('readonly', true);
		$('#ship_phone').prop('readonly', true);
		$('#ship_email').prop('readonly', true);
		$('#radio1').prop('readonly', true);
		$('#radio2').prop('readonly', true);
		
		// 停用地址欄位
		$('#ship_county').prop('readonly', true);
		$('#ship_district').prop('readonly', true);
		$('#ship_address').prop('readonly', true);
		
		// 停用發票欄位
		$('#ship_ship_receipt').prop('readonly', true);
		$('#ship_three_id').prop('readonly', true);
		$('#ship_three_company').prop('readonly', true);
		
		// 添加disabled樣式類
		$('#ship_name, #ship_phone, #ship_email, #ship_county, #ship_district, #ship_address, #ship_ship_receipt, #ship_three_id, #ship_three_company').addClass('form-field-disabled');
	}

	function enableFormFields() {
		// 重新啟用收件人資訊欄位
		$('#ship_name').prop('readonly', false);
		$('#ship_phone').prop('readonly', false);
		$('#ship_email').prop('readonly', false);
		$('#radio1').prop('readonly', false);
		$('#radio2').prop('readonly', false);
		
		// 重新啟用地址欄位
		$('#ship_county').prop('readonly', false);
		$('#ship_district').prop('readonly', false);
		$('#ship_address').prop('readonly', false);
		
		// 重新啟用發票欄位
		$('#ship_ship_receipt').prop('readonly', false);
		$('#ship_three_id').prop('readonly', false);
		$('#ship_three_company').prop('readonly', false);
		
		// 移除disabled樣式類
		$('#ship_name, #ship_phone, #ship_email, #ship_county, #ship_district, #ship_address, #ship_ship_receipt, #ship_three_id, #ship_three_company').removeClass('form-field-disabled');
	}

	function loadRecipientData(recipientId) {
		const selectedOption = $('#quick_recipient_select option:selected');
		
		// 從選項中取得所有資料
		const county = selectedOption.data('county');
		const district = selectedOption.data('district');
		const address = selectedOption.data('address');
		const shipName = selectedOption.data('ship-name');
		const shipPhone = selectedOption.data('ship-phone');
		const shipEmail = selectedOption.data('ship-email');
		const shipReceipt = selectedOption.data('ship-receipt');
		const shipThreeId = selectedOption.data('ship-three-id');
		const shipThreeCompany = selectedOption.data('ship-three-company');
		const shipGender = selectedOption.data('ship-gender');
		
		// 顯示保存為常用地址選項和手動輸入提示
		$('#add_favorite_address').hide();

		
		// 填入表單資料
		if (shipName) {
			$('#ship_name').val(shipName);
		}
		if (shipPhone) {
			$('#ship_phone').val(shipPhone);
		}
		if (shipEmail) {
			$('#ship_email').val(shipEmail);
		}
		
		// 填入地址資料
		if (county) {
			$('#ship_county').val(county);
			updateDistrict(county);
			if (district) {
				setTimeout(() => {
					setDistrict(county, district);
				}, 100);
			}
		}
		if (address) {
			$('#ship_address').val(address);
		}
		
		if (shipReceipt) {
			$('#ship_ship_receipt').val(shipReceipt);
			if (shipReceipt == '3') {
				$('.ifThree').css('display','inline-block');
				if (shipThreeId) {
					$('#ship_three_id').val(shipThreeId);
				}
				if (shipThreeCompany) {
					$('#ship_three_company').val(shipThreeCompany);
				}
			} else {
				$('.ifThree').css('display','none');
			}
		}
		if (shipGender) {
			if (shipGender == '1') {
				$('#radio1').prop('checked', true);
			} else if (shipGender == '2') {
				$('#radio2').prop('checked', true);
			}
		}
		
		// 設定 favorite_address 下拉選單到對應的值
		$('#favorite_address').val(recipientId);
		
		// 填入資料後停用所有欄位
		disableFormFields();
	}
