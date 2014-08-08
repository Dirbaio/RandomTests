<?php 
//page /members/#id/threads
//page /members/#id-:/threads
//page /members/#id/threads/p#from
//page /members/#id-:/threads/p#from

//ABXD LEGACY
//page /listthreads/#id
//page /listthreads/#id-:
//page /listthreads.php

function request($id, $from=0)
{
	$user = Fetch::user($id);

	if($from)
		Url::setCanonicalUrl('/members/#-#/threads/p#', $user['id'], $user['name'], $from);
	else
		Url::setCanonicalUrl('/members/#-#/threads', $user['id'], $user['name']);


	$breadcrumbs = array(
		array('url' => Url::format('/members'), 'title' => __("Members")),
		array('user' => $user),
		array('url' => Url::format('/members/#-#/threads', $user['id'], $user['name']), 'title' => __('Threads'), 'weak' => true),
	);

	$actionlinks = array();

	renderPage('member.html', array(
		'user' => $user,
		'breadcrumbs' => $breadcrumbs, 
		'actionlinks' => $actionlinks,
		'title' => $forum['title'],
	));
}