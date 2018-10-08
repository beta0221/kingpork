$(document).ready(function(){
	$.ajaxSetup({
  		headers: {
    		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  		}
	});




});

var selected_id = null;
function dialogue_select(id){
	selected_id = id;
	$('.selected_stack').removeClass('selected_stack');
	$('.left-outter #'+id).addClass('selected_stack');
	$('.conversation-box').empty();
	$.ajax({
		type:'GET',
		url:'/contactManage/'+id,
		dataType:'json',
		success: function (response) {
			var scrollSpace = 0;
			var t = true;
			response.forEach(function(item,index){
				appendToView(item,index);
				
				if (item.id == id) {
					t = false;
				}
				if (t) {
					scrollSpace=scrollSpace+parseInt($('.mLeft'+index).height())+10;

					 if ($('.mRight'+index).height()!=null) {
					 	scrollSpace=scrollSpace+parseInt($('.mRight'+index).height())+10;	
					 }
				}
				 
				
			});
			$('#mes'+id).addClass('selected_message');
			$('.conversation-box').scrollTop(scrollSpace);
			
        },
        error: function () {
            alert('錯誤');
        }
	});

}

function appendToView(item,index){
	$('<div class="conversation-stack left-stack mLeft'+index+'"><div id="mes'+item.id+'" class="message"><div class="message-top">'+item.name+' '+item.email+' '+item.created_at+'</div><div class="message-middle">'+item.title+'</div><div class="message-bottom">'+item.message+'</div></div></div>').appendTo('.conversation-box');
	if (item.response!=null&&item.response_at!=null) {
		$('<div class="conversation-stack right-stack mRight'+index+'"><div class="message"><div class="message-top">'+'金園排骨 kingpork '+item.response_at+'</div><div class="message-middle">'+'客服回覆'+'</div><div class="message-bottom">'+item.response+'</div></div></div>').appendTo('.conversation-box');
	}
}

function sendMail(){
	// alert(selected_id);
	var text = $('#response-text').val();
	
	if (text.length > 20 && selected_id !=null) {
		
		$.ajax({
			type:'POST',
			url:'contactManage/'+selected_id,
			dataType:'json',
			data:{
				_method:'patch',
				text:text,
			},
			success:function(responese){
				alert(responese);
				$('#response-text').val('');
				dialogue_select(selected_id);
			},
			error:function(){
				alert('錯誤');
			}
		});
			
	}else{
		alert('請確認回覆字數20字以上，並選擇對象。');
	}
	

}