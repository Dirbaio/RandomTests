<?php
//page /api/newthread

function request($fid, $title='', $text='')
{
	$forum = Fetch::forum($fid);

	Permissions::assertCanViewForum($forum);
	Permissions::assertCanCreateThread($forum);

	$title = trim($title);

	if(!$text)
		fail(__("Your post is empty. Enter a message and try again."));
	if(!$title)
		fail(__("Your thread is unnamed. Enter a thread title and try again."));

	$lastPost = time() - Session::get('lastposttime');
	if($lastPost < 10)//Settings::get("floodProtectionInterval"))
	{
		//Check for last post the user posted.
		$lastPost = Sql::querySingle("SELECT * FROM {posts} WHERE user=? ORDER BY date DESC LIMIT 1", Session::id());

		//If it looks similar to this one, assume the user has double-clicked the button.
		//if($lastPost["thread"] == $tid)
		//	json(Url::format('/post/#', $lastPost['id']));

		fail(__("You're going too damn fast! Slow down a little."));
	}

	$now = time();

	Sql::query('UPDATE {users} set posts=posts+1, lastposttime=? where id=?',
		time(), Session::id());

	// Create the thread
	Sql::query("INSERT into {threads} (forum, user, title, lastpostdate, lastposter) values (?,?,?,?,?)",
		$fid, Session::id(), $title, $now, Session::id());

	$tid = Sql::insertId();

	// Put the first post
	Sql::query("INSERT into {posts} (thread, user, date, ip, num) values (?,?,?,?,?)",
		$tid, Session::id(), $now, $_SERVER['REMOTE_ADDR'], Session::get('posts')+1);

	$pid = Sql::insertId();

	Sql::Query("INSERT into {posts_text} (pid,text,revision,user,date) values (?,?,?,?,?)", 
		$pid, $text, 0, Session::id(), $now);

	Sql::query("UPDATE {forums} set numposts=numposts+1, numthreads=numthreads+1, lastpostdate=?, lastpostuser=?, lastpostid=? where id=?",
		$now, Session::id(), $pid, $fid);

	//Erase the draft
	Sql::query('DELETE FROM {drafts} WHERE user=? AND type=? AND target=?', Session::id(), 1, $fid);

	json(Url::format('/#-#/#-#', $forum['id'], $forum['title'], $tid, $title));
}