<?php 
//page /members/#id/threads
//page /members/#id-:/threads

//ABXD LEGACY
//page /listthreads.php

function request($id)
{
	$user = Fetch::user($id);

	Url::setCanonicalUrl('/members/#-#/threads', $user['id'], $user['name']);


	$breadcrumbs = array(
		array('url' => Url::format('/members'), 'title' => __("Members")),
		array('user' => $user),
		array('url' => Url::format('/members/#-#/threads', $user['id'], $user['name']), 'title' => __('Threads')),
	);

	$actionlinks = array();

	renderPage('profile.html', array(
		'user' => $user,
		'breadcrumbs' => $breadcrumbs, 
		'actionlinks' => $actionlinks,
		'title' => $forum['title'],
	));
}