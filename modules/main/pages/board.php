<?php 
//page /

//ABXD LEGACY
//page /board
//page /board.php

function request()
{
	Url::setCanonicalUrl('/');

	$pl = Session::powerlevel();

	if(Session::isLoggedIn())
		$forums = Sql::queryAll(
			'SELECT 
				f.*,
				lu.(_userfields),
				(
					SELECT COUNT(*)
					FROM {threads} t
					LEFT JOIN {threadsread} tr ON tr.thread=t.id AND tr.id=?
					WHERE t.forum=f.id AND t.lastpostdate > IFNULL(tr.date,0)
				) numnew
			FROM {forums} f
			LEFT JOIN {users} lu ON lu.id = f.lastpostuser
			WHERE minpower <= ?
			ORDER BY forder',
			Session::id(), $pl);
	else
		$forums = Sql::queryAll(
			'SELECT 
				f.*,
				lu.(_userfields),
				(
					SELECT COUNT(*)
					FROM {threads} t
					WHERE t.forum=f.id AND t.lastpostdate > ?
				) numnew
			FROM {forums} f
			LEFT JOIN {users} lu ON lu.id = f.lastpostuser
			WHERE minpower <= 0
			ORDER BY forder',
			time() - 900);

	$rCats = Sql::query('SELECT * FROM {categories} ORDER BY corder');
	$categories = array();

	while($cat = Sql::fetch($rCats))
	{
		$cat['forums'] = array();
		foreach($forums as $forum)
			if($forum['catid'] == $cat['id'])
			{
				$forum['subforums'] = array();
				foreach($forums as $subforum)
					if($subforum['catid'] == -$forum['id'])
						$forum['subforums'][] = $subforum;
				$cat['forums'][] = $forum;
			}

		if($cat['forums'])
			$categories[] = $cat;
	}

	$breadcrumbs = array(
	);


	$actionlinks = array(
	);

	renderPage('board.html', array(
		'categories' => $categories,
		'breadcrumbs' => $breadcrumbs, 
		'actionlinks' => $actionlinks,
		'title' => '',
	));
}

