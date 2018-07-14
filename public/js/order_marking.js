function mark(id){


	$('#markingTextarea').val('');
	$('#markingID').empty();

	$.ajax({
		type:'GET',
		url:'/order/'+id,
		dataType:'json',
		success: function (response) {


			$('#markingTextarea').val(response.mark);
			$('#markingID').append(id);
		},
		error: function () {
	         alert('錯誤');
	     },
	});

}

function markingDown(){


	var mark = $('#markingTextarea').val();
	var id = $('#markingID').html();

	$.ajax({
		type:'POST',
		url:'/order/marking/'+id,
		dataType:'json',
		data:{
			_method:'patch',
			mark:mark,
		},
		success: function (response) {
			if (response==1) {
				var src = $('#mark_'+id).attr('src');
				var from = src.substr(-8,8);
				if (from == 'gray.png') {
					var to = src.replace(from,'red.png');
					$('#mark_'+id).attr('src',to);	
				}
			}else if (response == 0) {
				var src = $('#mark_'+id).attr('src');
				var from = src.substr(-8,8);
				if (from != 'gray.png') {
					var to = src.replace(from,'kgray.png');
					$('#mark_'+id).attr('src',to);	
				}
			}
			
		},
		error: function () {
	         alert('錯誤');
	     },
	});


}

function cancelBill(id){
		var r = confirm('是否確定取消訂單？');
		if (r==true) {
			$.ajax({
				type:'POST',
				url:'/bill/cancel/'+id,
				dataType:'json',
				data: {
					_method: 'delete',
				},
				success: function (response) {
						
					location.reload();
					
				},
				error: function () {
			        alert('錯誤');
			    },
			});
		}
		

	}