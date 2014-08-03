"use strict";


function PostBoxCtrlFactory($scope, $sce, $timeout, ajax) {
	$scope.data = window._initial_scope_data;

	if(typeof($scope.data.text) != 'string')
		$scope.data.text = '';

	$scope.dirty = false;
	$scope.saving = false;
	$scope.saved = false;

	$scope.submit = function() {
		ajax($scope.postbox.submitApi, $scope.data, function(redirect) {
			window.location = redirect;
		});
	};

	$scope.changed = function() {
		if($scope.dirty) return;

		$scope.dirty=true;
		$timeout(function() {
			$scope.save();
		}, 5000);
	};

	$scope.save = function(callback) {
		if(!$scope.dirty) return;

		$scope.saving = true;
		ajax('/api/savedraft', {type: $scope.postbox.draftType, target: $scope.postbox.draftTarget, data: $scope.data}, function() {
			$scope.saving = false;
			$scope.saved = true;
			$scope.dirty = false;

			if(callback)
				callback();
		});
	};

	$scope.preview = function() {
		ajax('/api/preview', $scope.data, function(data) {
			$scope.previewhtml = $sce.trustAsHtml(data);
		});
	};

	$scope.add = function(before, after) {
		if(after === undefined)
			after = '';

		// Totally not Angular-way, but it's the simplest way possible.
		var textEditor = document.getElementById('text');

		var oldSelS = textEditor.selectionStart;
		var oldSelE = textEditor.selectionEnd;
		if(after == '')
			oldSelS = oldSelE;
		if(!$scope.editorFocused)
			oldSelS = oldSelE = $scope.data.text.length;

		var scroll = textEditor.scrollTop;
		var selectedText = $scope.data.text.substr(oldSelS, oldSelE - oldSelS);

		$scope.data.text = $scope.data.text.substr(0, oldSelS) + before + selectedText + after + textEditor.value.substr(oldSelE);

		$timeout(function() {
			textEditor.selectionStart = oldSelS + before.length;
			textEditor.selectionEnd = oldSelS + before.length + selectedText.length;
			textEditor.scrollTop = scroll;
			textEditor.focus();

			$scope.$apply($scope.changed);
		}, 0, false);
	};

	// Just by instantiating one controller of these
	// we will get draft autosaving in the entire page. Nice, hm?
	$(document).on("click", "a", function(e) {
		if($scope.dirty) {
			$scope.$apply(function() {
				$scope.save(function() {
					window.location = e.currentTarget.href;
				});
			});

			return false;
		}
		else
			return true;
	});
}


angular.module('app')

.controller('NewReplyCtrl', function($scope, $sce, $timeout, ajax) {
	PostBoxCtrlFactory($scope, $sce, $timeout, ajax);

	if(typeof($scope.data.title) != 'string')
		$scope.data.title = '';

	$scope.postbox = {
		submitApi: '/api/newreply',
		draftType: 0,
		draftTarget: $scope.data.tid
	};

	$scope.quote = function(pid) {
		ajax('/api/getquote', {pid: pid}, $scope.add);
	};
})

.controller('NewThreadCtrl', function($scope, $sce, $timeout, ajax) {
	PostBoxCtrlFactory($scope, $sce, $timeout, ajax);

	$scope.postbox = {
		submitApi: '/api/newthread',
		draftType: 1,
		draftTarget: $scope.data.fid
	};
})
