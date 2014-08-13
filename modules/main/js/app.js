"use strict";

angular.module('app', [
	'monospaced.elastic',
	'ngCookies'
])

.config(function($interpolateProvider) {
	$interpolateProvider.startSymbol('{[').endSymbol(']}');
})

.factory('ajax', function($http) {
	return function (path, param, callback, error) {
		$http({
			method: 'POST',
			url: path,
			data: angular.toJson(param),
			transformResponse: []
		})
		.success(function(data, status, headers, config) {
			callback(angular.fromJson(data));
		})
		.error(function(data, status, headers, config) {
			if(error)
				error(data);
			else
				alert(data);
		});
	};
})
