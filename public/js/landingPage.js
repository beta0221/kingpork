$(document).ready(function(){
	var story2_ismap = 0;
	var story3_ismap = 0;
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
		if (story2_ismap == 0) {
			$('#story-2').append('<div class="'+'google-map">'+'<iframe src="'+'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d115684.31745512571!2d121.34034753173913!3d25.02949493864515!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x34681f1b2b4a0cfb%3A0x315594a1dcf4866c!2z6YeR5ZyS5o6S6aqo!5e0!3m2!1szh-TW!2stw!4v1528711232233" '+'frameborder="'+'0"'+' allowfullscreen></iframe></div>');
			story2_ismap = 1;		
		}
		
	})
	$('#story-3-box').click(function(){
		$('#story-1').css('display','none');
		$('#story-2').css('display','none');
		$('#story-3').css('display','block');
		$('.arrow-up').css('left','83.33333%');
		$('.shop-now-img').removeClass('shop-now-img');
		$('#story-3-box img').addClass('shop-now-img');
		if (story2_ismap == 0) {
			$('#story-3').append('<div class="'+'google-map">'+'<iframe src="'+'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3614.2203398053202!2d121.3662263148874!3d25.060519983959885!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3442a73adf8e04d1%3A0x2f3929d61cb8ab77!2zMzMz5qGD5ZyS5biC6b6c5bGx5Y2A5b6p6IiI6KGXNS036Jmf!5e0!3m2!1szh-TW!2stw!4v1528713158239" '+'frameborder="'+'0"'+' allowfullscreen></iframe></div>');
			story3_ismap = 1;
		}
	})

});