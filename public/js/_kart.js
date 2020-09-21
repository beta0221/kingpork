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
			var slug = $(this).attr('id');
			var q = $(this).val();
			var pp = q * parseInt($(this).attr('price'));
			$('#priceTag' + slug).empty().append(pp);
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
			switch($(this).val()){
				case '基隆市':
					$('.ship_district').empty().append('<option value="仁愛區">仁愛區</option><option value="信義區">信義區</option><option value="中正區">中正區</option><option value="中山區">中山區</option><option value="安樂區">安樂區</option><option value="暖暖區">暖暖區</option><option value="七堵區">七堵區</option>');
					break;
				case '台北市':
					$('.ship_district').empty().append('<option value="中正區">中正區</option><option value="大同區">大同區</option><option value="中山區">中山區</option><option value="松山區">松山區</option><option value="大安區">大安區</option><option value="萬華區">萬華區</option><option value="信義區">信義區</option><option value="士林區">士林區</option><option value="北投區">北投區</option><option value="內湖區">內湖區</option><option value="南港區">南港區</option><option value="文山區">文山區</option>');
					break;
				case '新北市':
					$('.ship_district').empty().append('<option value="板橋區">板橋區</option><option value="新莊區">新莊區</option><option value="中和區">中和區</option><option value="永和區">永和區</option><option value="土城區">土城區</option><option value="樹林區">樹林區</option><option value="三峽區">三峽區</option><option value="鶯歌區">鶯歌區</option><option value="三重區">三重區</option><option value="蘆洲區">蘆洲區</option><option value="五股區">五股區</option><option value="泰山區">泰山區</option><option value="林口區">林口區</option><option value="八里區">八里區</option><option value="淡水區">淡水區</option><option value="三芝區">三芝區</option><option value="石門區">石門區</option><option value="金山區">金山區</option><option value="萬里區">萬里區</option><option value="汐止區">汐止區</option><option value="瑞芳區">瑞芳區</option><option value="貢寮區">貢寮區</option><option value="平溪區">平溪區</option><option value="雙溪區">雙溪區</option><option value="新店區">新店區</option><option value="深坑區">深坑區</option><option value="石碇區">石碇區</option><option value="坪林區">坪林區</option><option value="烏來區">烏來區</option>');
					break;
				case '桃園市':
					$('.ship_district').empty().append('<option value="桃園區">桃園區</option><option value="中壢區">中壢區</option><option value="平鎮區">平鎮區</option><option value="八德區">八德區</option><option value="楊梅區">楊梅區</option><option value="蘆竹區">蘆竹區</option><option value="大溪區">大溪區</option><option value="龍潭區">龍潭區</option><option value="龜山區">龜山區</option><option value="大園區">大園區</option><option value="觀音區">觀音區</option><option value="新屋區">新屋區</option><option value="復興區">復興區</option>');
					break;
				case '新竹市':
					$('.ship_district').empty().append('<option value="東區">東區</option><option value="北區">北區</option><option value="香山區">香山區</option>');
					break;
				case '新竹縣':
					$('.ship_district').empty().append('<option value="竹北市">竹北市</option><option value="竹東鎮">竹東鎮</option><option value="新埔鎮">新埔鎮</option><option value="關西鎮">關西鎮</option><option value="湖口鄉">湖口鄉</option><option value="新豐鄉">新豐鄉</option><option value="峨眉鄉">峨眉鄉</option><option value="寶山鄉">寶山鄉</option><option value="北埔鄉">北埔鄉</option><option value="芎林鄉">芎林鄉</option><option value="橫山鄉">橫山鄉</option><option value="尖石鄉">尖石鄉</option><option value="五峰鄉">五峰鄉</option>');
					break;
				case '苗栗縣':
					$('.ship_district').empty().append('<option value="苗栗市">苗栗市</option><option value="頭份市">頭份市</option><option value="竹南鎮">竹南鎮</option><option value="後龍鎮">後龍鎮</option><option value="通霄鎮">通霄鎮</option><option value="苑裡鎮">苑裡鎮</option><option value="卓蘭鎮">卓蘭鎮</option><option value="造橋鄉">造橋鄉</option><option value="西湖鄉">西湖鄉</option><option value="頭屋鄉">頭屋鄉</option><option value="公館鄉">公館鄉</option><option value="銅鑼鄉">銅鑼鄉</option><option value="三義鄉">三義鄉</option><option value="大湖鄉">大湖鄉</option><option value="獅潭鄉">獅潭鄉</option><option value="三灣鄉">三灣鄉</option><option value="南庄鄉">南庄鄉</option><option value="泰安鄉">泰安鄉</option>');
					break;
				case '台中市':
					$('.ship_district').empty().append('<option value="中區">中區</option><option value="東區">東區</option><option value="南區">南區</option><option value="西區">西區</option><option value="北區">北區</option><option value="北屯區">北屯區</option><option value="西屯區">西屯區</option><option value="南屯區">南屯區</option><option value="太平區">太平區</option><option value="大里區">大里區</option><option value="霧峰區">霧峰區</option><option value="烏日區">烏日區</option><option value="豐原區">豐原區</option><option value="后里區">后里區</option><option value="石岡區">石岡區</option><option value="東勢區">東勢區</option><option value="新社區">新社區</option><option value="潭子區">潭子區</option><option value="大雅區">大雅區</option><option value="神岡區">神岡區</option><option value="大肚區">大肚區</option><option value="沙鹿區">沙鹿區</option><option value="龍井區">龍井區</option><option value="梧棲區">梧棲區</option><option value="清水區">清水區</option><option value="大甲區">大甲區</option><option value="外埔區">外埔區</option><option value="大安區">大安區</option><option value="和平區">和平區</option>');
					break;
				case '彰化縣':
					$('.ship_district').empty().append('<option value="彰化市">彰化市</option><option value="員林市">員林市</option><option value="和美鎮">和美鎮</option><option value="鹿港鎮">鹿港鎮</option><option value="溪湖鎮">溪湖鎮</option><option value="二林鎮">二林鎮</option><option value="田中鎮">田中鎮</option><option value="北斗鎮">北斗鎮</option><option value="花壇鄉">花壇鄉</option><option value="芬園鄉">芬園鄉</option><option value="大村鄉">大村鄉</option><option value="永靖鄉">永靖鄉</option><option value="伸港鄉">伸港鄉</option><option value="線西鄉">線西鄉</option><option value="福興鄉">福興鄉</option><option value="秀水鄉">秀水鄉</option><option value="埔心鄉">埔心鄉</option><option value="埔鹽鄉">埔鹽鄉</option><option value="大城鄉">大城鄉</option><option value="芳苑鄉">芳苑鄉</option><option value="竹塘鄉">竹塘鄉</option><option value="社頭鄉">社頭鄉</option><option value="二水鄉">二水鄉</option><option value="田尾鄉">田尾鄉</option><option value="埤頭鄉">埤頭鄉</option><option value="溪州鄉">溪州鄉</option>');
					break;
				case '南投縣':
					$('.ship_district').empty().append('<option value="南投市">南投市</option><option value="埔里鎮">埔里鎮</option><option value="草屯鎮">草屯鎮</option><option value="竹山鎮">竹山鎮</option><option value="集集鎮">集集鎮</option><option value="名間鄉">名間鄉</option><option value="鹿谷鄉">鹿谷鄉</option><option value="中寮鄉">中寮鄉</option><option value="魚池鄉">魚池鄉</option><option value="國姓鄉">國姓鄉</option><option value="水里鄉">水里鄉</option><option value="信義鄉">信義鄉</option><option value="仁愛鄉">仁愛鄉</option>');
					break;
				case '雲林縣':
					$('.ship_district').empty().append('<option value="斗六市">斗六市</option><option value="斗南鎮">斗南鎮</option><option value="虎尾鎮">虎尾鎮</option><option value="西螺鎮">西螺鎮</option><option value="土庫鎮">土庫鎮</option><option value="北港鎮">北港鎮</option><option value="林內鄉">林內鄉</option><option value="古坑鄉">古坑鄉</option><option value="大埤鄉">大埤鄉</option><option value="莿桐鄉">莿桐鄉</option><option value="褒忠鄉">褒忠鄉</option><option value="二崙鄉">二崙鄉</option><option value="崙背鄉">崙背鄉</option><option value="麥寮鄉">麥寮鄉</option><option value="臺西鄉">臺西鄉</option><option value="東勢鄉">東勢鄉</option><option value="元長鄉">元長鄉</option><option value="四湖鄉">四湖鄉</option><option value="口湖鄉">口湖鄉</option><option value="水林鄉">水林鄉</option>');
					break;
				case '嘉義市':
					$('.ship_district').empty().append('<option value="東區">東區</option><option value="西區">西區</option>');
					break;
				case '嘉義縣':
					$('.ship_district').empty().append('<option value="太保市">太保市</option><option value="朴子市">朴子市</option><option value="布袋鎮">布袋鎮</option><option value="大林鎮">大林鎮</option><option value="民雄鄉">民雄鄉</option><option value="溪口鄉">溪口鄉</option><option value="新港鄉">新港鄉</option><option value="六腳鄉">六腳鄉</option><option value="東石鄉">東石鄉</option><option value="義竹鄉">義竹鄉</option><option value="鹿草鄉">鹿草鄉</option><option value="水上鄉">水上鄉</option><option value="中埔鄉">中埔鄉</option><option value="竹崎鄉">竹崎鄉</option><option value="梅山鄉">梅山鄉</option><option value="番路鄉">番路鄉</option><option value="大埔鄉">大埔鄉</option><option value="阿里山鄉">阿里山鄉</option>');
					break;
				case '台南市':
					$('.ship_district').empty().append('<option value="中西區">中西區</option><option value="東區">東區</option><option value="南區">南區</option><option value="北區">北區</option><option value="安平區">安平區</option><option value="安南區">安南區</option><option value="永康區">永康區</option><option value="歸仁區">歸仁區</option><option value="新化區">新化區</option><option value="左鎮區">左鎮區</option><option value="玉井區">玉井區</option><option value="楠西區">楠西區</option><option value="南化區">南化區</option><option value="仁德區">仁德區</option><option value="關廟區">關廟區</option><option value="龍崎區">龍崎區</option><option value="官田區">官田區</option><option value="麻豆區">麻豆區</option><option value="佳里區">佳里區</option><option value="西港區">西港區</option><option value="七股區">七股區</option><option value="將軍區">將軍區</option><option value="學甲區">學甲區</option><option value="北門區">北門區</option><option value="新營區">新營區</option><option value="後壁區">後壁區</option><option value="白河區">白河區</option><option value="東山區">東山區</option><option value="六甲區">六甲區</option><option value="下營區">下營區</option><option value="柳營區">柳營區</option><option value="鹽水區">鹽水區</option><option value="善化區">善化區</option><option value="大內區">大內區</option><option value="山上區">山上區</option><option value="新市區">新市區</option><option value="安定區">安定區</option>');
					break;
				case '高雄市':
					$('.ship_district').empty().append('<option value="楠梓區">楠梓區</option><option value="左營區">左營區</option><option value="鼓山區">鼓山區</option><option value="三民區">三民區</option><option value="鹽埕區">鹽埕區</option><option value="前金區">前金區</option><option value="新興區">新興區</option><option value="苓雅區">苓雅區</option><option value="前鎮區">前鎮區</option><option value="旗津區">旗津區</option><option value="小港區">小港區</option><option value="鳳山區">鳳山區</option><option value="大寮區">大寮區</option><option value="鳥松區">鳥松區</option><option value="林園區">林園區</option><option value="仁武區">仁武區</option><option value="大樹區">大樹區</option><option value="大社區">大社區</option><option value="岡山區">岡山區</option><option value="路竹區">路竹區</option><option value="橋頭區">橋頭區</option><option value="梓官區">梓官區</option><option value="彌陀區">彌陀區</option><option value="永安區">永安區</option><option value="燕巢區">燕巢區</option><option value="田寮區">田寮區</option><option value="阿蓮區">阿蓮區</option><option value="茄萣區">茄萣區</option><option value="湖內區">湖內區</option><option value="旗山區">旗山區</option><option value="美濃區">美濃區</option><option value="內門區">內門區</option><option value="杉林區">杉林區</option><option value="甲仙區">甲仙區</option><option value="六龜區">六龜區</option><option value="茂林區">茂林區</option><option value="桃源區">桃源區</option><option value="那瑪夏區">那瑪夏區</option>');
					break;
				case '屏東縣':
					$('.ship_district').empty().append('<option value="屏東市">屏東市</option><option value="潮州鎮">潮州鎮</option><option value="東港鎮">東港鎮</option><option value="恆春鎮">恆春鎮</option><option value="萬丹鄉">萬丹鄉</option><option value="長治鄉">長治鄉</option><option value="麟洛鄉">麟洛鄉</option><option value="九如鄉">九如鄉</option><option value="里港鄉">里港鄉</option><option value="鹽埔鄉">鹽埔鄉</option><option value="高樹鄉">高樹鄉</option><option value="萬巒鄉">萬巒鄉</option><option value="內埔鄉">內埔鄉</option><option value="竹田鄉">竹田鄉</option><option value="新埤鄉">新埤鄉</option><option value="枋寮鄉">枋寮鄉</option><option value="新園鄉">新園鄉</option><option value="崁頂鄉">崁頂鄉</option><option value="林邊鄉">林邊鄉</option><option value="南州鄉">南州鄉</option><option value="佳冬鄉">佳冬鄉</option><option value="琉球鄉">琉球鄉</option><option value="車城鄉">車城鄉</option><option value="滿州鄉">滿州鄉</option><option value="枋山鄉">枋山鄉</option><option value="霧臺鄉">霧臺鄉</option><option value="瑪家鄉">瑪家鄉</option><option value="泰武鄉">泰武鄉</option><option value="來義鄉">來義鄉</option><option value="春日鄉">春日鄉</option><option value="獅子鄉">獅子鄉</option><option value="牡丹鄉">牡丹鄉</option><option value="三地門鄉">三地門鄉</option>');
					break;
				case '台東縣':
					$('.ship_district').empty().append('<option value="臺東市">臺東市</option><option value="成功鎮">成功鎮</option><option value="關山鎮">關山鎮</option><option value="長濱鄉">長濱鄉</option><option value="池上鄉">池上鄉</option><option value="東河鄉">東河鄉</option><option value="鹿野鄉">鹿野鄉</option><option value="卑南鄉">卑南鄉</option><option value="大武鄉">大武鄉</option><option value="綠島鄉">綠島鄉</option><option value="太麻里鄉">太麻里鄉</option><option value="海端鄉">海端鄉</option><option value="延平鄉">延平鄉</option><option value="金峰鄉">金峰鄉</option><option value="達仁鄉">達仁鄉</option><option value="蘭嶼鄉">蘭嶼鄉</option>');
					break;
				case '花蓮縣':
					$('.ship_district').empty().append('<option value="花蓮市">花蓮市</option><option value="鳳林鎮">鳳林鎮</option><option value="玉里鎮">玉里鎮</option><option value="新城鄉">新城鄉</option><option value="吉安鄉">吉安鄉</option><option value="壽豐鄉">壽豐鄉</option><option value="光復鄉">光復鄉</option><option value="豐濱鄉">豐濱鄉</option><option value="瑞穗鄉">瑞穗鄉</option><option value="富里鄉">富里鄉</option><option value="秀林鄉">秀林鄉</option><option value="萬榮鄉">萬榮鄉</option><option value="卓溪鄉">卓溪鄉</option>');
					break;
				case '宜蘭縣':
					$('.ship_district').empty().append('<option value="宜蘭市">宜蘭市</option><option value="頭城鎮">頭城鎮</option><option value="羅東鎮">羅東鎮</option><option value="蘇澳鎮">蘇澳鎮</option><option value="礁溪鄉">礁溪鄉</option><option value="壯圍鄉">壯圍鄉</option><option value="員山鄉">員山鄉</option><option value="冬山鄉">冬山鄉</option><option value="五結鄉">五結鄉</option><option value="三星鄉">三星鄉</option><option value="大同鄉">大同鄉</option><option value="南澳鄉">南澳鄉</option>');
					break;
			}
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
			var sum = parseInt($('#sum').html());
			if (bonus > maxBonus) {
				$('#bonus').val(maxBonus);
				bonus = maxBonus;
			}
			if (bonus % 50 != 0) {
				bonus = bonus - bonus%50;
				$('#bonus').val(bonus);
			}
			if (bonus / 50 > sum) {
				bonus = sum * 50;
				$('#bonus').val(bonus);
			}
			var bonusCount = bonus/50;
			var afterDis = sum - bonus / 50;
			$('#sum').empty().append(sum+'-'+bonusCount+'='+afterDis);
		});
	});
	function checkForm(){
		var unFinished = 0;
		$('.alert-field').empty()

		if(!$('#pay_by_credit').is(':checked') && !$('#pay_by_atm').is(':checked') && !$('#pay_by_cod').is(':checked')){
			$('.alert-field').append('<strong>請選擇付款方式</strong><br>');
		}
		if ($('#ship_name').val() == '') {
			$('#ship_name').addClass('alerting');
			
			$('.alert-field').append('<strong><font color="red">＊</font>號處不可空白</strong><br>');
			unFinished = 1;
		}
		if ($('#ship_email').val() == '') {
			$('#ship_email').addClass('alerting');
			if (unFinished == 0) {
				$('.alert-field').append('<strong><font color="red">＊</font>號處不可空白</strong><br>');
				unFinished = 1;
			}
		}
		if ($('#ship_phone').val() == '') {
			$('#ship_phone').addClass('alerting');
			if (unFinished == 0) {
				$('.alert-field').append('<strong><font color="red">＊</font>號處不可空白</strong><br>');
				unFinished = 1;
			}
		}
		if ($('#ship_county').val() == '') {
			$('#ship_county').addClass('alerting');
			if (unFinished == 0) {
				$('.alert-field').append('<strong><font color="red">＊</font>號處不可空白</strong><br>');
				unFinished = 1;
			}
		}
		if ($('#ship_address').val() == '') {
			$('#ship_address').addClass('alerting');
			if (unFinished == 0) {
				$('.alert-field').append('<strong><font color="red">＊</font>號處不可空白</strong><br>');
				unFinished = 1;
			}
		}

		if ($('.two-three').val() == '3') {
			// if ($('#ship_three_name').val()=='') {
			// 	$('#ship_three_name').addClass('alerting');
			// 	if (unFinished == 0) {
			// 		$('.alert-field').append('<strong><font color="red">＊</font>號處不可空白</strong><br>');
			// 		unFinished = 1;
			// 	}
			// }
			if ($('#ship_three_id').val()=='') {
				$('#ship_three_id').addClass('alerting');
				if (unFinished == 0) {
					$('.alert-field').append('<strong><font color="red">＊</font>號處不可空白</strong><br>');
					unFinished = 1;
				}
			}
			if ($('#ship_three_company').val()=='') {
				$('#ship_three_company').addClass('alerting');
				if (unFinished == 0) {
					$('.alert-field').append('<strong><font color="red">＊</font>號處不可空白</strong><br>');
					unFinished = 1;
				}
			}
		}

		if(!$('#pay_by_credit').is(':checked') && !$('#pay_by_atm').is(':checked') && !$('#pay_by_cod').is(':checked')){
			$('.pay_by').addClass('alerting');
		}

		if ($('#ship_name').val()!=''&&$('#ship_email').val()!=''&&$('#ship_phone').val()!=''&&$('#ship_county').val()!=''&&$('#ship_address').val()!='') {
			if ($('.two-three').val()=='3'&&$('#ship_three_id').val()!=''&&$('#ship_three_company').val()!='') {
				if($('#pay_by_credit').is(':checked') || $('#pay_by_atm').is(':checked') || $('#pay_by_cod').is(':checked')){
					$('body').append('<div class="loader-bg"></div>');
					$('body').append('<div class="loader-box"><div class="loader"></div><strong>請稍候...</strong></div>');
					$('.kartForm').submit();
				}
			}else if($('.two-three').val()=='2'){
				if($('#pay_by_credit').is(':checked') || $('#pay_by_atm').is(':checked') || $('#pay_by_cod').is(':checked')){
					$('body').append('<div class="loader-bg"></div>');
					$('body').append('<div class="loader-box"><div class="loader"></div><strong>請稍候...</strong></div>');
					$('.kartForm').submit();
				}
			}
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

				if(response.msg=='403'){
					window.location.reload();
					return false;
				}
                // alert(response.msg);
                $('#item'+id).remove();
                // navbar cart 減一
                var inKart = parseInt($('#inKart').html()) - 1;
                $('#inKart').empty().append(inKart);

                uploadSum();
            },
            error: function () {
                alert('無法從購物車中刪除');
            }
		});
	}

	function uploadSum(){
		var price = 0;
			$('.priceTag').each(function(){
				price =  price + parseInt($(this).html());
			});

			if (price > 499) {
				$('#transport-item').attr('name','');
				$('#transport-quantity').attr('name','');
				$('#transport-fee').css('display','none');	
			}else{
				price = price + 150;
				$('#transport-item').attr('name','item[]');
				$('#transport-quantity').attr('name','quantity[]');
				$('#transport-fee').css('display','table-row');	
			}

			//全館95折
			price = Math.floor(price * 0.95);

			$('#sum').empty().append(price);

			if (price == 0) {
				$('.sureToBuy').css('display','none');
			}else{
				$('.sureToBuy').css('display','inline-block');
			}
	}
	function sureToBuy(){
		$('.kartTable').css('display','none');
		$('.sureToBuy').css('display','none');
		$('.shipping').css('display','table');
		$('#back-kart').css('display','inline-block');
		$('#h-title').html('填寫寄送資料');
		$('#payBtn').css('display','inline-block');
		$('.processing').removeClass('processing');
		$('.process-2').addClass('processing');

		$.ajax({
			type:'GET',
			url:'findMemory',
			dataType:'json',
			success: function (response) {

				if(response.ifMemory != '0'){
					if (response.ship_gender == 2) {
						$('#radio2').prop('checked','checked');
					}
	                $('#ship_name').val(response.ship_name);
	                $('#ship_email').val(response.ship_email);
	                $('#ship_phone').val(response.ship_phone);
	                $('#ship_county').val(response.ship_county);
	                $('.ship_district').empty().append('<option value="' + response.ship_district +'">' + response.ship_district + '</option>')
	                $('#ship_address').val(response.ship_address);
	                if(response.ship_receipt == '3'){
	                	$('.two-three').val(response.ship_receipt);
	                	$('.ifThree').css('display','inline-block');
	                	// $('#ship_three_name').val(response.ship_three_name);
	                	$('#ship_three_id').val(response.ship_three_id);
	                	$('#ship_three_company').val(response.ship_three_company);
	                }
	                $('#myBonus span').empty();
	                $('#myBonus span').append(response.bonus);
	                $('#bonus').attr('max',response.bonus);
              	}else{
              		$('#myBonus span').empty();
              		$('#myBonus span').append(response.bonus);
              		$('#bonus').attr('max',response.bonus);
              	}
            },
            error: function () {
                alert('錯誤');
            }
		});
	}

	function back_kart(){
		$('.kartTable').css('display','table');
		$('.sureToBuy').css('display','inline-block');
		$('.shipping').css('display','none');
		$('#back-kart').css('display','none');
		$('#h-title').html('我的購物車');
		$('#payBtn').css('display','none');
		$('.processing').removeClass('processing');
		$('.process-1').addClass('processing');
		$('.alert-field').empty();
	}
