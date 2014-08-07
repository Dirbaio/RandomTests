<?php
//page /api/editpost

function request($text='', $pid=0)
{
	$post = Fetch::post($pid);
	$tid = $post['thread'];
	$thread = Fetch::thread($tid);
	$fid = $thread['forum'];
	$forum = Fetch::forum($fid);
	
	Permissions::assertCanViewForum($forum);
	Permissions::assertCanEditPost($post, $thread, $forum);
	
	if(!$text)
		fail(__("Your post is empty. Enter a message and try again."));


	$now = time();
	$rev = $post['currentrevision'] + 1;

	// Edit the post
	Sql::Query("INSERT INTO {posts_text} (pid,text,revision,user,date) VALUES (?,?,?,?,?)", 
		$pid, $text, $rev, Session::id(), $now);

	Sql::query("UPDATE {posts} SET currentrevision=? WHERE id=?",
		$rev, $pid);

	// Update thread lastpostdate if we edited the last post
	if($thread['lastpostid'] == $pid)
		Sql::query("UPDATE {threads} SET lastpostdate=? WHERE id=?",
			Session::id(), $now, $pid, $tid);

	// Erase the draft
	Sql::query('DELETE FROM {drafts} WHERE user=? AND type=? AND target=?', Session::id(), 2, $pid);

	json(Url::format('/post/#', $pid));
}