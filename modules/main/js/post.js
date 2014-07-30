"use strict";

function doPost(tid) {
	var text = $('#text').val();

	ajax('/api/post', {tid: tid, text: text}, function(pid) {
		window.location = '/post/'+pid;
	});
}