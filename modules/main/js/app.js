"use strict";

angular.module('app', ['monospaced.elastic'])

.config(function($interpolateProvider) {
	$interpolateProvider.startSymbol('{[').endSymbol(']}');
})

.factory('ajax', function($http) {
	return function (path, param, callback, error) {
		var onSuccess = function(data) {
			if(data.data === "true")
				callback(true);
			else if(data.data === "false")
				callback(false);
			else if(data.data === "null")
				callback(null);
			else
				callback(JSON.parse(data.data));
		};

		var onError = function(data) {
			var status = data.status; //http
			var txt = data.data;
			alert(txt);
			if(error)
				error();
		};

		if (callback == undefined) {
			callback = param;
			param = "";
		}

		param = JSON.stringify(param);

		$http.post(path, param).then(onSuccess, onError);
	};
})
