<?php
//page /api/savedraft

function request($type, $target, $data)
{
	Session::checkLoggedIn();

	$type = (int)$type;
	$target = (int)$target;
	$data = json_encode($data);

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

	Sql::query('INSERT INTO {drafts} (user, type, target, date, data) VALUES (?,?,?,?,?)
				ON DUPLICATE KEY UPDATE date=?, data=?',
		Session::id(), $type, $target, time(), $data, time(), $data);
	

	json('ok');
}