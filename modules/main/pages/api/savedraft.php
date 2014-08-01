<?php
//page /api/savedraft

function request($type, $target, $text)
{
	Session::checkLoggedIn();

	$type = (int)$type;
	$target = (int)$target;

	if($type == 0)
	{
		$tid = $target;
		$thread = Fetch::thread($tid);
		$fid = $thread['forum'];
	}
	else
		$fid = $target;

	$forum = Fetch::forum($fid);
	Permissions::assertCanViewForum($forum);

	if($text)
		Sql::query('INSERT INTO {drafts} (user, type, target, date, text) VALUES (?,?,?,?,?)
					ON DUPLICATE KEY UPDATE date=?, text=?',
			Session::id(), $type, $target, time(), $text, time(), $text);
	else
		Sql::query('DELETE FROM {drafts} WHERE user=? AND type=? AND target=?',
			Session::id(), $type, $target);
	

	json('ok');
}