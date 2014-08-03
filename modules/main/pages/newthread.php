<?php 
//page /#id/newthread
//page /#id-:/newthread

//ABXD LEGACY
//page /newthread/#id
//page /newthread/#id-:
//page /newthread.php

function request($id, $from=0)
{
	$fid = $id;
	$forum = Fetch::forum($fid);

	Permissions::assertCanViewForum($forum);
	Permissions::assertCanCreateThread($forum);

	Url::setCanonicalUrl('/#-#/newthread', $forum['id'], $forum['title']);

	// Retrieve the draft.
	$draft = Sql::querySingle('SELECT * FROM {drafts} WHERE user=? AND type=? AND target=?', 
		Session::id(), 1, $fid);
	$scope = array();

	if($draft)
		$scope = json_decode($draft['data'], true);

	if(!is_array($scope))
		$scope = array();

	$scope['fid'] = $fid;


	$breadcrumbs = array(
		array('url' => Url::format('/#-#', $forum['id'], $forum['title']), 'title' => $forum['title']),
		array('url' => Url::format('/#-#/newthread', $forum['id'], $forum['title']), 'title' => __('New thread'))
	);

	$actionlinks = array(
	);

	renderPage('newthread.html', array(
		'forum' => $forum, 
		'scope' => $scope,

		'breadcrumbs' => $breadcrumbs, 
		'actionlinks' => $actionlinks,
		'title' => $forum['title'],
	));
}

