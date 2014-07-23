<?php 
//page /

//ABXD LEGACY
//page /board
//page /board.php

function request()
{
	Url::setCanonicalUrl('/');

	$pl = 0;
	$forums = Sql::queryAll(
		"SELECT 
			f.*,
			lu.(_userfields)
		FROM {forums} f
		LEFT JOIN {users} lu ON lu.id = f.lastpostuser
		WHERE minpower <= ?
		ORDER BY forder",
		$pl);

	$rCats = Sql::query("SELECT * FROM {categories} ORDER BY corder");
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

