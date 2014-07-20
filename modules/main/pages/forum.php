<?php 
//page /#id
//page /#id-:

//ABXD LEGACY
//page /forum/#id
//page /forum/#id-:
//page /forum.php

function request($id, $from=0)
{
	$fid = $id;

	$forum = Sql::querySingle("SELECT * FROM forums WHERE id=?", $fid);
	if(!$forum)
		Kill(__("Unknown forum ID."));

	$pl = 0;

	if($forum['minpower'] > $pl)
		Kill(__("You are not allowed to browse this forum."));

	Url::setCanonicalUrl('/#-#', $forum['id'], $forum['title']);

	$user = Session::get();
	$loguserid = $user ? $user['id']:0;
	$tpp = 50;

	if($loguserid)
		$threads = Sql::queryAll("
			SELECT
				t.*,
				tr.date readdate,
				su.(_userfields),
				lu.(_userfields)
			FROM
				{threads} t
				LEFT JOIN {threadsread} tr ON tr.thread=t.id AND tr.id=?
				LEFT JOIN {users} su ON su.id=t.user
				LEFT JOIN {users} lu ON lu.id=t.lastposter
			WHERE forum=?
			ORDER BY sticky DESC, lastpostdate DESC 
			LIMIT ?, ?", 
			$loguserid, $fid, $from, $tpp);
	else
		$threads = Sql::queryAll("
			SELECT
				t.*,
				? readdate,
				su.(_userfields),
				lu.(_userfields)
			FROM
				{threads} t
				LEFT JOIN {users} su ON su.id=t.user
				LEFT JOIN {users} lu ON lu.id=t.lastposter
			WHERE forum=?
			ORDER BY sticky DESC, lastpostdate DESC 
			LIMIT ?, ?", 
			time()-600, $fid, $from, $tpp);

	$breadcrumbs = array(
		array('url' => Url::format('/#-#', $forum['id'], $forum['title']), 'title' => $forum['title'])
	);

	$actionlinks = array(
		array('url' => Url::format('/#-#/newthread', $forum['id'], $forum['title']), 'title' => __('Post thread'))
	);

	renderPage('forum.html', array(
		'forum' => $forum, 
		'threads' => $threads, 
		'hotcount' => 30, 
		'paging' => array(
			'perpage' => $tpp,
			'from' => $from,
			'total' => $forum['numthreads'],
		),
		'breadcrumbs' => $breadcrumbs, 
		'actionlinks' => $actionlinks,
		'title' => $forum['title'],
	));
}

