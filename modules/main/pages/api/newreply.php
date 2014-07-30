<?php
//page /api/newreply

function request($text, $tid)
{
	Session::checkLoggedIn();
	
	$thread = Sql::querySingle("SELECT * FROM {threads} WHERE id=?", $tid);
	if(!$thread)
		fail(__("Unknown thread ID."));

	$fid = $thread['forum'];
	$forum = Sql::querySingle("SELECT * FROM {forums} WHERE id=?", $fid);
	if(!$forum)
		fail(__("Unknown forum ID."));

	$pl = Session::powerlevel();

	if($forum['minpower'] > $pl)
		fail(__("You are not allowed to browse this forum."));

	if(!$text)
		fail(__("Your post is empty. Enter a message and try again."));

	if($thread['lastposter'] == Session::id() && $thread['lastpostdate'] >= time()-86400 && Session::powerlevel()<3)
		fail(__("You can't double post until it's been at least one day."));

	$lastPost = time() - Session::get('lastposttime');
	if($lastPost < 10)//Settings::get("floodProtectionInterval"))
	{
		//Check for last post the user posted.
		$lastPost = Sql::querySingle("SELECT * FROM {posts} WHERE user=? ORDER BY date DESC LIMIT 1", Session::id());

		//If it looks similar to this one, assume the user has double-clicked the button.
		if($lastPost["thread"] == $tid)
			json(Url::format('/post/#', $lastPost['id']));

		fail(__("You're going too damn fast! Slow down a little."));
	}

	$now = time();

	Sql::query('UPDATE {users} set posts=posts+1, lastposttime=? where id=?',
		time(), Session::id());

	Sql::query("INSERT into {posts} (thread, user, date, ip, num) values (?,?,?,?,?)",
		$tid, Session::id(), $now, $_SERVER['REMOTE_ADDR'], Session::get('posts')+1);

	$pid = Sql::insertId();

	Sql::Query("INSERT into {posts_text} (pid,text,revision,user,date) values (?,?,?,?,?)", 
		$pid, $text, 0, Session::id(), $now);

	Sql::query("UPDATE {forums} set numposts=numposts+1, lastpostdate=?, lastpostuser=?, lastpostid=? where id=?",
		$now, Session::id(), $pid, $fid);

	Sql::query("UPDATE {threads} set lastposter=?, lastpostdate=?, replies=replies+1, lastpostid=? where id=?",
		Session::id(), $now, $pid, $tid);

	//Erase the draft
	Sql::query('DELETE FROM {drafts} WHERE user=? AND type=? AND target=?', Session::id(), 0, $tid);


//	logAction('newreply', array('forum' => $fid, 'thread' => $tid, 'post' => $pid));

	json($pid);
}