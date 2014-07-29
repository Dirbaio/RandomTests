"use strict";

function ajax(path, params, success, error) {

	if(success == undefined)
		success = function(data) {
			window.location = data;
		};

	if(error == undefined)
		error = function(data) {
			alert(data.responseText);
		};

	$.ajax({
		url: path,
		data: JSON.stringify(params),
		dataType: 'json',
		contentType: "application/json; charset=utf-8",
		type: 'POST',
		success: success,
		error: error
	});	
}
