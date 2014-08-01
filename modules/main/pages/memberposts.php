<?php 
//page /members/#id/posts
//page /members/#id-:/posts

//ABXD LEGACY
//page /listposts.php

function request($id)
{
	$user = Fetch::user($id);

	Url::setCanonicalUrl('/members/#-#/posts', $user['id'], $user['name']);


	$breadcrumbs = array(
		array('url' => Url::format('/members'), 'title' => __("Members")),
		array('user' => $user),
		array('url' => Url::format('/members/#-#/posts', $user['id'], $user['name']), 'title' => __('Posts')),
	);

	$actionlinks = array();

	renderPage('profile.html', array(
		'user' => $user,
		'breadcrumbs' => $breadcrumbs, 
		'actionlinks' => $actionlinks,
		'title' => $forum['title'],
	));
}