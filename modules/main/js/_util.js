"use strict";

function ajax(path, params, success, error) {
	$.ajax({
		url: path,
		data: JSON.stringify(parmas),
		dataType: 'json',
		contentType: "application/json; charset=utf-8",
		type: 'POST',
		success: success,
		error: error
	});	
}
