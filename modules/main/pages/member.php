<?php 
//page /members/#id
//page /members/#id-:

//ABXD LEGACY
//page /profile/#id
//page /profile/#id-:
//page /profile.php

function request($id)
{
	$user = Fetch::user($id);

	Url::setCanonicalUrl('/members/#-#', $user['id'], $user['name']);

	$breadcrumbs = array(
		array('url' => Url::format('/members'), 'title' => __("Members")),
		array('user' => $user),
	);

	$actionlinks = array();

	if(Permissions::canEditUser($user))
		$actionlinks[] = array('url' => Url::format('/members/#-#/edit', $user['id'], $user['name']), 'title' => __('Edit profile'));

	$actionlinks[] = array('url' => Url::format('/members/#-#/threads', $user['id'], $user['name']), 'title' => __('Threads'));
	$actionlinks[] = array('url' => Url::format('/members/#-#/posts', $user['id'], $user['name']), 'title' => __('Posts'));

	renderPage('member.html', array(
		'user' => $user,
		'breadcrumbs' => $breadcrumbs, 
		'actionlinks' => $actionlinks,
		'title' => $forum['title'],
	));
}