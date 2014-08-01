<?php 
//page /

//ABXD LEGACY
//page /board
//page /board.php

function request()
{
	Url::setCanonicalUrl('/');

	if(Session::isLoggedIn())
		$forums = Sql::queryAll(
			'SELECT 
				f.*,
				lu.(_userfields),
				(
					SELECT COUNT(*)
					FROM {threads} t
					LEFT JOIN {threadsread} tr ON tr.thread=t.id AND tr.id=?
					WHERE t.forum=f.id AND t.lastpostdate > IFNULL(tr.date, 0)
				) numnew
			FROM {forums} f
			LEFT JOIN {users} lu ON lu.id = f.lastpostuser
			ORDER BY forder',
			Session::id());
	else
		$forums = Sql::queryAll(
			'SELECT 
				f.*,
				0 as numnew
			FROM {forums} f
			LEFT JOIN {users} lu ON lu.id = f.lastpostuser
			ORDER BY forder');

	$rCats = Sql::query('SELECT * FROM {categories} ORDER BY corder');
	$categories = array();

	while($cat = Sql::fetch($rCats))
	{
		$cat['forums'] = array();
		foreach($forums as $forum)
			if($forum['catid'] == $cat['id'])
			{
				if(!Permissions::canViewForum($forum)) continue;
				$cat['forums'][] = $forum;
				foreach($forums as $subforum)
				{
					if(!Permissions::canViewForum($subforum)) continue;
					if($subforum['catid'] == -$forum['id'])
						$cat['forums'][] = $subforum;
				}
			}

		if($cat['forums'])
			$categories[] = $cat;
	}

	$breadcrumbs = array(
	);

	$actionlinks = array(
	);

	renderPage('components/forumList.html', array(
		'categories' => $categories,
		'breadcrumbs' => $breadcrumbs, 
		'actionlinks' => $actionlinks,
		'title' => '',
	));
}

