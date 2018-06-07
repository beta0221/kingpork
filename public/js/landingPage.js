$(document).ready(function(){

	$('#story-1-box').click(function(){
		$('#story-1').css('display','block');
		$('#story-2').css('display','none');
		$('#story-3').css('display','none');
		$('.arrow-up').css('left','16.66666%');
		$('.shop-now-img').removeClass('shop-now-img');
		$('#story-1-box img').addClass('shop-now-img');
	})
	$('#story-2-box').click(function(){
		$('#story-1').css('display','none');
		$('#story-2').css('display','block');
		$('#story-3').css('display','none');
		$('.arrow-up').css('left','50%');
		$('.shop-now-img').removeClass('shop-now-img');
		$('#story-2-box img').addClass('shop-now-img');
	})
	$('#story-3-box').click(function(){
		$('#story-1').css('display','none');
		$('#story-2').css('display','none');
		$('#story-3').css('display','block');
		$('.arrow-up').css('left','83.33333%');
		$('.shop-now-img').removeClass('shop-now-img');
		$('#story-3-box img').addClass('shop-now-img');
	})

});