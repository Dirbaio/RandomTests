"use strict";

function doPost(tid) {
	var text = $('#text').val();

	ajax('/api/newreply', {tid: tid, text: text}, function(pid) {
		window.location = '/post/'+pid;
	});
}