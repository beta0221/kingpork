$(document).ready(function(){
	var listIndex = 0;
	var cloneTr = $('.trList').clone();

	//新增一位收件人
	$('#addListBtn').on('click',function(){
		listIndex ++;
		cloneTr.find('.delBtnTd').empty().append('<div onclick="delList('+listIndex+');" class="btn btn-block btn-sm btn-danger">刪除</div>');
		cloneTr.attr('id','trList_'+listIndex);
		// $('#sendListTable').append('<tr id="trList_'+listIndex+'" class="trList"><td><input class="form-control" type="text"></td><td><input class="form-control" type="text"></td><td><input class="form-control" type="text"></td><td><input class="form-control" type="number" value="1"></td><td><div onclick="delList('+listIndex+');" class="btn btn-block btn-sm btn-danger">刪除</div></td></tr>');
		$('#sendListTable').append(cloneTr.clone());

	});

	
	var sendListArray = [];
	var totalQuantity = 0;

	$('#startBtn').on('click',function(){
		$('.beforeDiv').css('display','block');
		
		
	});

	$('#no-thanks-btn').on('click',function(){
		$('.beforeDiv').css('display','none');
		$('#before-start-mask').css('display','none');
		$('#startBtn').css('display','none');
		$('#nextBtn').css('display','block');
	});


	//確定送出
	$('#nextBtn').on('click',function(){
		clearAlert();
		sendListArray = [];
		totalQuantity = 0;
		var allGood = true;
		$('.trList').each(function(index){
			var temp = [];
			$(this).find('.form-control').each(function(index,item){
				if ($(this).val()) {
					temp.push($(this).val());
				}else{
					showAlert('欄位不可空白，請確認是否填寫完整');
					allGood = false;
					return false;
				}
				
			})
			if (!allGood) {
				return false;
			}

			var list ={'name':temp[0],'address':temp[1],'phone':temp[2],'time':temp[3],'quantity':temp[4]};
			totalQuantity = totalQuantity+parseInt(temp[4]);
			sendListArray.push(list);
		});	

		if (allGood) {
			clearAlert();

			// lock all input
			$('#sendListTable').find('.form-control').addClass('lock-input').attr('readonly',true);
			$('#sendListTable').find('select').attr('disabled',true);


			// console.log(sendListArray);
			$('#ship_address').val(JSON.stringify(sendListArray));
			// console.log('totalQuantity='+totalQuantity);
			$('#quantity').val(totalQuantity);
			$('#price-sum').empty().append(totalQuantity * productPrice);

			// show and hide
			$(this).css('display','none');
			$('.form-outterDiv').css('display','block');
			$('#submitBtn').css('display','block');
			$('#addListBtn').css('display','none');
			$('#sendListTable').find('.btn').css('display','none');
		}
		
	});

	$('#lastBtn').on('click',function(){

		// free all input
		$('#sendListTable').find('.lock-input').removeClass('lock-input').attr('readonly',false);
		$('#sendListTable').find('select').attr('disabled',false);

		// show and hide
		$('#nextBtn').css('display','block');
		$('.form-outterDiv').css('display','none');
		$('#submitBtn').css('display','none');
		$('#sendListTable').find('.btn').css('display','block');
		
	});

	$('#ship_receipt').on('change',function(){
		if($(this).val() == 2){
			$('.ifThree').css('display','none');
			$('.ifThree').find('input').val(null);
		}else if ($(this).val() == 3) {
			$('.ifThree').css('display','block');
		}
	});

	$('#ship_ifDate').on('change',function(){
		if($(this).val() == 'no'){
			$('#ship_date_notice').css('display','none');
			$('#ship_date').css('display','none');
			$('#ship_date').val(null);
		}else if ($(this).val() == 'yes') {
			$('#ship_date').css('display','block');
			$('#ship_date_notice').css('display','block');
		}
	});

	$('#bonus-use').change(function(){	//紅利
			var maxBonus = parseInt($('#myBonus').html());
			var bonus = $('#bonus-use').val();
			var sum = parseInt($('#price-sum').html());

			if (bonus > maxBonus) {
				$('#bonus-use').val(maxBonus);
				bonus = maxBonus;
			}
			if (bonus % 50 != 0) {
				bonus = bonus - bonus%50;
				$('#bonus-use').val(bonus);
			}

			if(bonus < 0){
				$('#bonus-use').val(0);
			}

			if (bonus / 50 > sum) {
				bonus = sum * 50;
				$('#bonus-use').val(bonus);
			}

			var bonusCount = bonus/50;
			var afterDis = sum - bonusCount;
			$('#price-sum').empty().append(sum+'-$'+bonusCount+'=$'+afterDis);
		});

	$('#submitBtn').on('click',function(){
		clearAlert();
		if (!$("input[name$='ship_email']").val()) {
			showAlert('欄位不可空白<br>');
		}
		if (!$("select[name$='ship_pay_by']").val()) {
			showAlert('請選擇付款方式');
		}
		if ($("input[name$='ship_email']").val() && $("select[name$='ship_pay_by']").val()) {
			$('#billing-form').submit();
		}
	});

});

function delList(index){
	$('#trList_'+index).remove();
}


function showAlert(value){
	$('.alert-field').append(value);
	$('.alert-field').css('display','block');
}
function clearAlert(){
	$('.alert-field').empty();
	$('.alert-field').css('display','none');
}
