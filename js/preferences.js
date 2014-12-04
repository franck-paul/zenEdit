var updateSampleBackground = function() {
	// Change background image of #zenedit_sample
	var background = $('#zenedit_background').val();
	$('#zenedit_sample').css('background-image','url(index.php?pf=zenEdit/img/background/'+background+')');
};

$(function() {
	$('#zenedit_background').change(updateSampleBackground).keyup(updateSampleBackground);
});
