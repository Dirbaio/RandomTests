<?php 
//page /#/#tid
//page /#/#tid-:
//page /#-:/#tid
//page /#-:/#tid-:

//ABXD3 LEGACY
//page /thread/#tid
//page /thread/#tid-:

function request($tid)
{
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

	var_dump($thread);
	var_dump($forum);

	Url::setCanonicalUrl('/'.$fid.'-'.Url::slugify($forum['title']).'/'.$tid.'-'.Url::slugify($thread['title']));

	echo "This is thread $tid";
}

