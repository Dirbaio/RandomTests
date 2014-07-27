<?php 
//page /#/#id
//page /#/#id-:
//page /#-:/#id
//page /#-:/#id-:

//page /#/#id/p#from
//page /#/#id-:/p#from
//page /#-:/#id/p#from
//page /#-:/#id-:/p#from

//ABXD LEGACY
//page /thread/#id
//page /thread/#id-:
//page /thread.php

function request($id, $from=0)
{
	$tid = $id;

	$thread = Sql::querySingle("SELECT * FROM threads WHERE id=?", $tid);
	if(!$thread)
		fail(__("Unknown thread ID."));

	$fid = $thread['forum'];
	$forum = Sql::querySingle("SELECT * FROM forums WHERE id=?", $fid);
	if(!$forum)
		fail(__("Unknown forum ID."));

	$pl = Session::powerlevel();

	if($forum['minpower'] > $pl)
		fail(__("You are not allowed to browse this forum."));

	if($from == 0)
		Url::setCanonicalUrl('/#-#/#-#', $forum['id'], $forum['title'], $thread['id'], $thread['title']);
	else
		Url::setCanonicalUrl('/#-#/#-#/p#', $forum['id'], $forum['title'], $thread['id'], $thread['title'], $from);

	$ppp = 20;

	$posts = Sql::queryAll("
		SELECT
			p.*,
			pt.text, pt.revision, pt.user AS revuser, pt.date AS revdate,
			user.(_userfields,rankset,title,picture,posts,postheader,signature,signsep,lastposttime,lastactivity,regdate,globalblock),
			useredited.(_userfields),
			userdeleted.(_userfields)
		FROM
			{posts} p
			LEFT JOIN {posts_text} pt ON pt.pid = p.id AND pt.revision = p.currentrevision
			LEFT JOIN {users} user ON user.id = p.user
			LEFT JOIN {users} useredited ON useredited.id = pt.user
			LEFT JOIN {users} userdeleted ON userdeleted.id = p.deletedby
		WHERE thread=?
		ORDER BY date ASC LIMIT ?, ?", $tid, $from, $ppp);


	$breadcrumbs = array(
		array('url' => Url::format('/#-#', $forum['id'], $forum['title']), 'title' => $forum['title']),
		array('url' => Url::format('/#-#/#-#', $forum['id'], $forum['title'], $thread['id'], $thread['title']), 'title' => $thread['title']),
	);

	$actionlinks = array(
		array('url' => Url::format('/#-#/newthread', $forum['id'], $forum['title']), 'title' => __('Post thread'))
	);

	renderPage('thread.html', array(
		'forum' => $forum, 
		'thread' => $thread, 
		'posts' => $posts, 
		'paging' => array(
			'perpage' => $ppp,
			'from' => $from,
			'total' => $thread['replies'] + 1, //+1 for the OP
			'base' => Url::format('/#-#/#-#', $forum['id'], $forum['title'], $thread['id'], $thread['title']),
		),
		'breadcrumbs' => $breadcrumbs, 
		'actionlinks' => $actionlinks,
		'title' => $forum['title'],
	));

}

