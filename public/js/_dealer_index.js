new ClipboardJS('.copy-btn');

$(document).ready(function(){
	$.ajaxSetup({
  		headers: {
    		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  		}
	});



	$('#ship_receipt').change(function(){
		if($(this).val() == 2){
			$('#receipt_three_group').hide();
		}else{
			$('#receipt_three_group').show();
		}
	});

});

var groupCode = '';

function show_submit_form(group_code,group_title){
	$('#model-title').html(group_title);
	groupCode = group_code;
}

function submit_group(){
	$('#form-submit-button').hide();
	
	$.ajax({
			type:'POST',
			url:'/group-detail/' + groupCode,
			dataType:'json',
			data:{
				_method:'patch',
			},
			success:function(res){
				console.log(res);
				$('.submit_item').remove();
				$('.submit_quantity').remove();
				$.each(res.itemArray,function(key,value){
					$('#submit_form').append('<input class="submit_item" type="hidden" name="item[]" value="'+key+'">');
					$('#submit_form').append('<input class="submit_quantity" type="hidden" name="quantity[]" value="'+value+'">');
				});
				$('#submit_address').val(res.address);
			},
			error:function(error){
				console.log(error);	
			},
			complete:function(){
				$('#submit_form').submit();
			}
		});
}

