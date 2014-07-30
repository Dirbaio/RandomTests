"use strict";

function doNewReply(tid) {
	var text = $('#text').val();

	ajax('/api/newreply', {tid: tid, text: text}, function(pid) {
		window.location = '/post/'+pid;
	});
}

//==================
// DRAFTS 

var draftType, draftTarget;
var draftDirty = false;

function doSaveDraft(callback) {
	var text = $('#text').val();

	ajax('/api/savedraft', {type: draftType, target: draftTarget, text: text}, function(pid) {
		$('#saveddraft').show();
		$('#savedraft').hide();

		draftDirty = false;
		if(callback)
			callback();
	});
}

function setDraftDirty() {
	if(!draftDirty) {
		draftDirty = true;

		$('#saveddraft').hide();
		$('#savedraft').show();
		setTimeout(function() {
			doSaveDraft();
		}, 5000); //5 seconds
	}
}

function startDraftAutosave(type, target) {
	draftType = type;
	draftTarget = target;

	$('#text').bind('input propertychange', setDraftDirty);

	$(document).on( "click", "a", function(e) {
		if(draftDirty) {
			doSaveDraft(function() {
				window.location = e.currentTarget.href;
			});
			return false;
		}
		return true;
	});
}