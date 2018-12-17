$(document).ready(function(){
	var listIndex = 0;
	//新增一位收件人
	$('#addListBtn').on('click',function(){
		listIndex ++;
		$('#sendListTable').append('<tr id="trList_'+listIndex+'" class="trList"><td><input class="form-control" type="text"></td><td><input class="form-control" type="text"></td><td><input class="form-control" type="text"></td><td><input class="form-control" type="number" value="1"></td><td><div onclick="delList('+listIndex+');" class="btn btn-sm btn-danger">刪除</div></td></tr>');

	});

	
	var sendListArray = [];
	var totalQuantity = 0;
	//確定送出
	$('#submitBtn').on('click',function(){
		sendListArray = [];
		totalQuantity = 0;
		$('.trList').each(function(index){
			var temp = [];
			$(this).find('input').each(function(index,item){
				temp.push($(this).val());
			})
			var list ={'name':temp[0],'address':temp[1],'phone':temp[2],'quantity':temp[3]};
			totalQuantity = totalQuantity+parseInt(temp[3]);
			sendListArray.push(list);
		});	
		console.log(sendListArray);
		$('#ship_address').val(JSON.stringify(sendListArray));
		console.log('totalQuantity='+totalQuantity);
		$('#quantity').val(totalQuantity);
	});

});

function delList(index){
	$('#trList_'+index).remove();
}

