<?php 
//page /post/#pid

function request($pid)
{
	$post = Sql::querySingle("SELECT * FROM {posts} WHERE id=?", $pid);
	if(!$post)
		fail(__("Unknown post ID."));

	$tid = $post['thread'];
	$thread = Sql::querySingle("SELECT * FROM {threads} WHERE id=?", $tid);
	if(!$thread)
		fail(__("Unknown thread ID."));

	$fid = $thread['forum'];
	$forum = Sql::querySingle("SELECT * FROM {forums} WHERE id=?", $fid);
	if(!$forum)
		fail(__("Unknown forum ID."));

	$ppp = 20;

	$count = Sql::queryValue("SELECT COUNT(*) FROM {posts} WHERE thread=? AND date<=? AND id!=?", 
								$tid, $post['date'], $pid);

	$from = (floor($count / $ppp)) * $ppp;

	if($from == 0)
		$url = Url::format('/#-#/#-#', $forum['id'], $forum['title'], $thread['id'], $thread['title']);
	else
		$url = Url::format('/#-#/#-#/p#', $forum['id'], $forum['title'], $thread['id'], $thread['title'], $from);

	$url .= '#'.$pid;

	Url::redirect($url);
}

