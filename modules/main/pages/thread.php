<?php 
//page /#/#id
//page /#/#id-:
//page /#-:/#id
//page /#-:/#id-:

//ABXD LEGACY
//page /thread/#id
//page /thread/#id-:
//page /thread.php

function request($id)
{
	$tid = $id;

	$thread = Sql::querySingle("SELECT * FROM threads WHERE id=?", $tid);
	if(!$thread)
		Kill(__("Unknown thread ID."));

	$fid = $thread['forum'];
	$forum = Sql::querySingle("SELECT * FROM forums WHERE id=?", $fid);
	if(!$forum)
		Kill(__("Unknown forum ID."));

	$pl = 0;

	if($forum['minpower'] > $pl)
		Kill(__("You are not allowed to browse this forum."));

	Url::setCanonicalUrl('/'.$forum['id'].'-'.Url::slugify($forum['title']).'/'.$thread['id'].'-'.Url::slugify($thread['title']));

	renderPage('thread.html', array('thread' => $thread, 'forum' => $forum));
}

