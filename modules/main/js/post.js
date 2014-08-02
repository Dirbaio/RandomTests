"use strict";

function doNewReply(tid) {
	var text = $('#text').val();

	ajax('/api/newreply', {tid: tid, text: text}, function(pid) {
		window.location = '/post/'+pid;
	});
}

//==================
// QUOTES

function postQuote(pid) {
	ajax('/api/getquote', {pid: pid}, postAddText);
}

function doPreview() {

	$('#postpreviewing').show();

	var text = $('#text').val();
	ajax('/api/preview', {text: text}, function(data) {
		$('#postpreview').html(data);
		$('#postpreviewing').hide();
	});
}

//==================
// POST TOOLBAR

var editorFocused = false;

function postAddTag(before, after) {
	var textEditor = document.getElementById('text');

	var oldSelS = textEditor.selectionStart;
	var oldSelE = textEditor.selectionEnd;
	if(!editorFocused)
		oldSelS = oldSelE = textEditor.value.length;

	var scroll = textEditor.scrollTop;
	var selectedText = textEditor.value.substr(oldSelS, oldSelE - oldSelS);

	textEditor.value = textEditor.value.substr(0, oldSelS) + before + selectedText + after + textEditor.value.substr(oldSelE);

	textEditor.selectionStart = oldSelS + before.length;
	textEditor.selectionEnd = oldSelS + before.length + selectedText.length;
	textEditor.scrollTop = scroll;
	textEditor.focus();

	$(textEditor).trigger('autosize.resize');
}

function postAddText(added) {
	var textEditor = document.getElementById('text');

	var oldSelE = textEditor.selectionEnd;
	if(!editorFocused)
		oldSelE = textEditor.value.length;

	var scroll = textEditor.scrollTop;

	textEditor.value = textEditor.value.substr(0, oldSelE) + added + textEditor.value.substr(oldSelE);

	textEditor.selectionStart = oldSelE + added.length;
	textEditor.selectionEnd = oldSelE + added.length;
	textEditor.scrollTop = scroll;
	textEditor.focus();

	$(textEditor).trigger('autosize.resize');
}

function postBoxStart () {
	$(document).ready(function(){
		$('#text').autosize(); 
	});
	
	$('#text').focus(function() {
		editorFocused = true;
	}); 

	var textEditor = document.getElementById('text');
	textEditor.selectionStart = textEditor.value.length;
	textEditor.selectionEnd = textEditor.value.length;
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