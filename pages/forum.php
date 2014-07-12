<?php 
//page /#fid
//page /#fid-:

//ABXD3 LEGACY
//page /forum/#fid
//page /forum/#fid-:

function request($fid)
{
	$forum = Sql::querySingle("SELECT * FROM forums WHERE id=?", $fid);
	if(!$forum)
		Kill(__("Unknown forum ID."));

	$pl = 0;

	if($forum['minpower'] > $pl)
		Kill(__("You are not allowed to browse this forum."));

	var_dump($forum);

	Url::setCanonicalUrl('/'.$fid.'-'.Url::slugify($forum['title']));

	echo "This is forum $fid";
}

