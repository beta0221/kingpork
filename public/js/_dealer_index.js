new ClipboardJS('.copy-btn');

$(document).ready(function(){
	$.ajaxSetup({
  		headers: {
    		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  		}
	});

});

function submit_group(group_code){

	$.ajax({
			type:'POST',
			url:'/group-detail/' + group_code,
			dataType:'json',
			data:{
				_method:'patch',
			},
			success:function(res){
				console.log(res);
				$('.submit_item').remove();
				$('.submit_quantity').remove();
				$.each(res.itemArray,function(key,value){
					$('#submit_form').append('<input class="submit_item" type="text" name="item[]" value="'+key+'">');
					$('#submit_form').append('<input class="submit_quantity" type="text" name="quantity[]" value="'+value+'">');
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

