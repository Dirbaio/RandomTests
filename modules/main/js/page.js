"use strict";

angular.module('app')

.controller('PageCtrl', function($scope, $window, $cookies, ajax) {
	$scope.toggleMobileLayout = function(enabled) {
		$cookies.mobileversion = enabled;
		$window.location = $window.location;
	}
})