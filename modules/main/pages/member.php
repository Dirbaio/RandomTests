<?php 
//page /u#id
//page /u#id-:

//ABXD LEGACY
//page /profile/#id
//page /profile/#id-:
//page /profile.php

function request($id)
{
	$user = Fetch::user($id);

	Url::setCanonicalUrl('/u#-:', $user['id'], $user['name']);

	$breadcrumbs = array(
		array('url' => Url::format('/members'), 'title' => __("Members")),
		array('user' => $user),
	);

	$actionlinks = array();

	if(Permissions::canEditUser($user))
		$actionlinks[] = array('url' => Url::format('/u#-:/edit', $user['id'], $user['name']), 'title' => __('Edit profile'));
	if(Permissions::canReadMessages($user))
		$actionlinks[] = array('url' => Url::format('/u#-:/messages', $user['id'], $user['name']), 'title' => __('Messages'));

	$actionlinks[] = array('url' => Url::format('/u#-:/threads', $user['id'], $user['name']), 'title' => __('Threads'));
	$actionlinks[] = array('url' => Url::format('/u#-:/posts', $user['id'], $user['name']), 'title' => __('Posts'));

	renderPage('member.html', array(
		'user' => $user,
		'breadcrumbs' => $breadcrumbs, 
		'actionlinks' => $actionlinks,
		'title' => $forum['title'],
	));
}