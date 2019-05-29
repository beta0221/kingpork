$(document).ready(function(){
	$.ajaxSetup({
  		headers: {
    		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  		}
	});

});

function sendRequest(){

	var productArray = [];
	var amountArray = [];

	$('.product-array').each(function(index){
		if ($(this).find('.product').prop('checked')) {
			productArray.push($(this).find('.product').val());
			amountArray.push($(this).find('.amount').val());
		}
		
	})

	var group_id = $('#group_id').val();
	var group_code = $('#group_code').val();
	var name = $('#name').val();
	var phone = $('#phone').val();

	// console.log({'name':name,'phone':phone,'p':productArray,'a':amountArray});

	$.ajax({
			type:'POST',
			url:'/join-group',
			dataType:'json',
			data:{	
				group_id:group_id,
				group_code:group_code,
				name:name,
				phone:phone,
				product:productArray,
				amount:amountArray,
			},
			success:function(res){
				
				if (res == 'success') {
					
					$('#join-group-form').css('display','none');
					$('#success-alert').css('display','block');
				}else{
					alert('非常抱歉，您所訂購的數量已超過揪團上限。');

					$.each(res,function(key,value){
						$('#current_'+key).html(value);
						var max = parseInt($('#max_'+key).html());
						$('#left_'+key).html(max - value);
					});
					
					// console.log(res);
				}
				
			},
			error:function(error){
				console.log(error);	
			}
		});
}