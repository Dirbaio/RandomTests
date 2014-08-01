<?php 
//page /members/#id/edit
//page /members/#id-:/edit

//ABXD LEGACY
//page /listposts.php

function request($id)
{
	$user = Fetch::user($id);

	Url::setCanonicalUrl('/members/#-#/edit', $user['id'], $user['name']);


	$breadcrumbs = array(
		array('url' => Url::format('/members'), 'title' => __("Members")),
		array('user' => $user),
		array('url' => Url::format('/members/#-#/edit', $user['id'], $user['name']), 'title' => __('Edit profile')),
	);

	$actionlinks = array();

	renderPage('profile.html', array(
		'user' => $user,
		'breadcrumbs' => $breadcrumbs, 
		'actionlinks' => $actionlinks,
		'title' => $forum['title'],
	));
}