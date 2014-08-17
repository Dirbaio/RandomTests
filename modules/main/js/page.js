"use strict";

angular.module('app')

.controller('PageCtrl', function($scope, $window, ajax) {
		
	function setCookie(sKey, sValue, vEnd, sPath, sDomain, bSecure) {  
		if (!sKey || /^(?:expires|max\-age|path|domain|secure)$/.test(sKey)) { return; }  
		var sExpires = "";  
		if (vEnd) {
			switch (typeof vEnd) {  
				case "number": sExpires = "; max-age=" + vEnd; break;  
				case "string": sExpires = "; expires=" + vEnd; break;  
				case "object": if (vEnd.hasOwnProperty("toGMTString")) { sExpires = "; expires=" + vEnd.toGMTString(); } break;  
			}  
		}  
		var lol = escape(sKey) + "=" + escape(sValue) + sExpires + (sDomain ? "; domain=" + sDomain : "") + (sPath ? "; path=" + sPath : "") + (bSecure ? "; secure" : "");
		document.cookie = lol;
	}


	$scope.toggleMobileLayout = function(enabled) {
		setCookie("mobileversion", enabled, 20*365*24*60*60, "/");
		$window.location.reload();
	}

	$scope.deletePost = function(pid, del) {
		var reason = '';
		if(del)
			reason = prompt("Enter a reason for deletion:");
		
		if(reason === null)
			return;

		ajax('/api/deletepost', {pid: pid, del:del, reason:reason}, function(redirect) {
			window.location = redirect;
		});
	}
})