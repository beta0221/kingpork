
var items = {};
$(document).ready(function(){
	$.ajaxSetup({
  		headers: {
    		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  		}
	});

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
                // alert('錯誤');
            }
		});

	//  modal hide override
	$('#orderModal').on('hidden.bs.modal', function () {
	    $('.alert-field').empty();
		$('#itemSlug').attr('value',null);
		$('#sum-overrite').empty();
	});

	$('.product-cell').on('click',function(){

		var dom = $(this);
		var name = dom.data('name');
		var price = dom.data('price');
		var slug = dom.data('slug');

		if(dom.hasClass('isSelected')){
			dom.removeClass('isSelected');
			delete items[slug];
		}else{
			dom.addClass('isSelected');
			items[slug] = {name,price};
		}
		
		updateFormItems();
	});

});

$(document).on('change','.quantity',function(){
	caculateTotalPrice();
})

function nextStep(){
	
	if(Object.keys(items).length == 0){
		alert('請選擇商品。')
		return;	
	}
	caculateTotalPrice();
	$('#orderModal').modal('show');
}

function updateFormItems(){
	$('.items-container').empty();
	
	Object.keys(items).forEach(slug => {
		var name = items[slug]['name'];
		var price = items[slug]['price'];
		$('.items-container').append("<div class='mb-2'><span>" + name + "</span><input id='itemSlug' style='display: none;' name='item[]' value='" + slug + "'><input min='1' class='quantity ml-2 mr-2' data-price='" + price + "' type='number' value='1' name='quantity[]'></div>");
	});
	caculateTotalPrice();
}

function caculateTotalPrice(){
	var totalPrice = 0;
	$('.quantity').each(function(){
		var price = $(this).data('price');
		var quantity = $(this).val();
		totalPrice += (price * quantity);
	});
	$('#sum-overrite').html(totalPrice);
}
