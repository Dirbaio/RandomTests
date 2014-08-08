<?php

//page /lastposts

function request() {
	$breadcrumbs = array(array("url" => "/lastposts", "title" => "Last posts"));
	$actionlinks = array();

	$hours = 72;
	$limit = 100;

	$lposts = Sql::query(
		"SELECT
			p.id, p.date,
			u.(_userfields),
			t.title AS t__title, t.id AS t__id,
			f.title AS f__title, f.id AS f__id, f.minpower AS f__minpower
		FROM {posts} p
			LEFT JOIN {users} u on u.id = p.user
			LEFT JOIN {threads} t on t.id = p.thread
			LEFT JOIN {forums} f on t.forum = f.id
		WHERE p.date >= ?
		ORDER BY date DESC",
		(time() - ($hours * 60*60)));

	$posts = array();

	while (count($posts) < 100)  {
		$post = Sql::fetch($lposts);

		if ($post == null) {
			// No omre posts.
			break;
		}

		if (!Permissions::canViewForum($post['f'])) {
			continue;
		}

		array_push($posts, $post);
	}

	renderPage("lastposts.html", array(
		'breadcrumbs' => $breadcrumbs,
		'actionlinks' => $actionlinks,
		'posts' => $posts
	));
}
