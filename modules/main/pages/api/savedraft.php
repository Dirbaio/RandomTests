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
		$thread = Sql::querySingle("SELECT * FROM {threads} WHERE id=?", $tid);
		if(!$thread)
			fail(__("Unknown thread ID."));
		$fid = $thread['forum'];
	}
	else
		$fid = $target;

	$forum = Sql::querySingle("SELECT * FROM {forums} WHERE id=?", $fid);
	if(!$forum)
		fail(__("Unknown forum ID."));

	$pl = Session::powerlevel();

	if($forum['minpower'] > $pl)
		fail(__("You are not allowed to browse this forum."));

	Sql::query("INSERT into {drafts} (user, type, target, date, text) values (?,?,?,?,?)
				ON DUPLICATE KEY UPDATE date=?, text=?",
		Session::id(), $type, $target, time(), $text, time(), $text);

	json('ok');
}