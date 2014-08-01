"use strict"

function doScroll() {

	var viewTop = $(window).scrollTop();

	var navigation = $("#navigation");
	var sidebarTop = $("#sidebar").offset().top;

	if(viewTop > sidebarTop) {
		if(!navigation.is(".navigation-fixed")) navigation.addClass("navigation-fixed");
	}
	else {
		if(navigation.is(".navigation-fixed")) navigation.removeClass("navigation-fixed");
	}

	var mainTop = $("#main").offset().top;
	var stickybar = $("#stickybar");
	if(viewTop > mainTop) {
		if(!stickybar.is(".stickybar-fixed")) stickybar.addClass("stickybar-fixed");
	}
	else {
		if(stickybar.is(".stickybar-fixed")) stickybar.removeClass("stickybar-fixed");
	}

	if ((viewTop > mainTop) && !navigation.is(".navigation-fixed"))
		navigation.addClass("navigation-fixed");
	else if ((viewTop <= sidebarTop - margin) && navigation.is(".navigation-fixed"))
		navigation.removeClass("navigation-fixed");
}

$(function() {
	var view = $(window);
	view.bind("scroll resize", doScroll);
	doScroll();
});
 
