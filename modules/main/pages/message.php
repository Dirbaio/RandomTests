<?php 
//page /u#id/messages/#mid
//page /u#id-:/messages/#mid


function request($id, $from=0, $mid)
{
	$user = Fetch::user($id);
	$message = Fetch::message($mid);

	Url::setCanonicalUrl('/u#-:/messages/#', $user['id'], $user['name'], $mid);

	Permissions::assertCanReadMessages($user);

	$breadcrumbs = array(
		array('url' => Url::format('/members'), 'title' => __("Members")),
		array('user' => $user),
		array('url' => Url::format('/u#-:/messages', $user['id'], $user['name']), 'title' => __('Messages')),
		array('url' => Url::format('/u#-:/messages/#', $user['id'], $user['name'], $message['id']), 'title' => $message['title']),
	);

	$actionlinks = array();

	$message['userposted'] = $message['ufrom'];
	unset($message['deleted']);
	
	renderPage('message.html', array(
		'user' => $user,
		'post' => $message,
		'breadcrumbs' => $breadcrumbs, 
		'actionlinks' => $actionlinks,
		'title' => 'Messages',
	));
}