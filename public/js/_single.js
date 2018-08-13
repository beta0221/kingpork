$(document).ready(function(){
	setInterval(counter,1000);
});
function counter(){
	var s = parseInt($('#timer_s').html());
	s = s - 1;
	
	if (s<0) {
		s = 59;
		var i = parseInt($('#timer_i').html());
		i = i - 1;

		if (i<0) {
			i = 59;
			var H = parseInt($('#timer_H').html());
			var H = H - 1;
			$('#timer_H').html(H);	
		}
		$('#timer_i').html(i);
	}
	$('#timer_s').html(s);
}
var i = 0;
function contact(){
	if (i==0) {
		$('.contact-info-bg').css('display','block');
		i = 1;
	}else if (i==1) {
		$('.contact-info-bg').css('display','none');
		i=0;
	}
}