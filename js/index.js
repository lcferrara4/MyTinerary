$("li a, .navbar-header a").click(function() {
    	$('html, body').animate({
        	scrollTop: $( $.attr(this, 'href') ).offset().top - $("nav").height()
    	}, 1000);
	return false;
});
