
var items = {};
$(document).ready(function(){
	$.ajaxSetup({
  		headers: {
    		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  		}
	});

	

	//  modal hide override
	$('#orderModal').on('hidden.bs.modal', function () {
	    $('.alert-field').empty();
		$('#sum').empty();
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

	onQuickRecipientChange();
}

function updateFormItems(){
	$('.items-container').empty();
	
	Object.keys(items).forEach(slug => {
		var name = items[slug]['name'];
		var price = items[slug]['price'];
		$('.items-container').append("<div class='mb-2'><span>" + name + "</span><input id='itemSlug' style='display: none;' name='item[]' value='" + slug + "'><input min='1' class='form-control d-inline-block w-25 quantity ml-2 mr-2' data-price='" + price + "' type='number' value='1' name='quantity[]'></div>");
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
	$('#sum').html(totalPrice);
	sumBeforeDiscount = totalPrice;
}
