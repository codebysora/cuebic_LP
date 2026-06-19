$(function() {

  	var topBtn = $('#page-top');   
	topBtn.hide();
    $(window).on("scroll", function() {
        if ($(this).scrollTop() > 100) {
            topBtn.fadeIn("fast");
        } else {
            topBtn.fadeOut("fast");
        }
    });
	topBtn.click(function(){
	$( 'html,body' ).animate( {scrollTop:0} , 'slow' ) ;
	});
	
	  $('a[href^="#"]').click(function(e){
			var speed = 500;
			var href= $(this).attr("href");
			var target = $(href == "#" || href == "" ? 'html' : href);
			var position = target.offset().top;
			$("html, body").animate({scrollTop:position-30}, speed, "swing");
			return false;
	  });

	if ($("#token").length) {
	  var nw = $.now();
	  $("#token").val(nw);  
	  $("form").validationEngine("attach");
	}
});