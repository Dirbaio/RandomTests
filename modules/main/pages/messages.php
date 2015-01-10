<?php 
//page /u#id/messages
//page /u#id-:/messages
//page /u#id/messages/p#from
//page /u#id-:/messages/p#from


function request($id, $from=0)
{
	$user = Fetch::user($id);

	if($from)
		Url::setCanonicalUrl('/u#-:/messages/p#', $user['id'], $user['name'], $from);
	else
		Url::setCanonicalUrl('/u#-:/messages', $user['id'], $user['name']);

	Permissions::assertCanReadMessages($user);

	$pp = 50;

	$messages = Sql::queryAll(
		'SELECT
			m.*, t.*,
			ufrom.(_userfields)
		FROM
			{pmsgs} m
			LEFT JOIN {users} ufrom ON ufrom.id=m.userfrom
			LEFT JOIN {pmsgs_text} t ON t.pid=m.id
		WHERE userto = ?
		ORDER BY date DESC 
		LIMIT ?, ?', 
		$user['id'], $from, $pp);

	$total = Sql::queryValue('SELECT COUNT(*) FROM {pmsgs} WHERE userto=?', $user['id']);

	$breadcrumbs = array(
		array('url' => Url::format('/members'), 'title' => __("Members")),
		array('user' => $user),
		array('url' => Url::format('/u#-:/messages', $user['id'], $user['name']), 'title' => __('Messages'), 'weak' => true),
	);

	$actionlinks = array();

	renderPage('messages.html', array(
		'user' => $user,
		'messages' => $messages,
		'paging' => array(
			'perpage' => $pp,
			'from' => $from,
			'total' => $total,
			'base' => Url::format('/u#-:/messages', $user['id'], $user['name']),
		),
		'breadcrumbs' => $breadcrumbs, 
		'actionlinks' => $actionlinks,
		'title' => 'Messages',
	));
}