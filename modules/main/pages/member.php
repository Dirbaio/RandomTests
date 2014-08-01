<?php 
//page /members/#id-:

//ABXD LEGACY
//page /profile.php

function request($id)
{
	$user = Sql::querySingle('SELECT * FROM {users} WHERE id=?', $id);

	if(!$user)
		fail(__('Unknown user ID.'));

	$breadcrumbs = array(
		array('url' => Url::format('/members'), 'title' => __("Members")),
		array('url' => Url::format('/members/#-#', $user['id'], $user['name']), 'title' => $user['name']),
	);

	$actionlinks = array(
		array('url' => Url::format('/members/#-#/edit', $user['id'], $user['name']), 'title' => __('Edit profile')),
		array('url' => Url::format('/members/#-#/posts', $user['id'], $user['name']), 'title' => __('Posts')),
		array('url' => Url::format('/members/#-#/threads', $user['id'], $user['name']), 'title' => __('threads')),
	);

	renderPage('profile.html', array(
		'user' => $user,
		'breadcrumbs' => $breadcrumbs, 
		'actionlinks' => $actionlinks,
		'title' => $forum['title'],
	));
}