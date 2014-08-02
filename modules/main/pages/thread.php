<?php 
//page /#/#id
//page /#/#id-:
//page /#-:/#id
//page /#-:/#id-:

//page /#/#id/p#from
//page /#/#id-:/p#from
//page /#-:/#id/p#from
//page /#-:/#id-:/p#from

//ABXD LEGACY
//page /thread/#id
//page /thread/#id-:
//page /thread.php

function request($id, $from=0)
{
	$tid = $id;
	$thread = Fetch::thread($tid);
	$fid = $thread['forum'];
	$forum = Fetch::forum($fid);

	Permissions::assertCanViewForum($forum);

	if($from == 0)
		Url::setCanonicalUrl('/#-#/#-#', $forum['id'], $forum['title'], $thread['id'], $thread['title']);
	else
		Url::setCanonicalUrl('/#-#/#-#/p#', $forum['id'], $forum['title'], $thread['id'], $thread['title'], $from);

	$ppp = 20;

	$posts = Sql::queryAll(
		'SELECT
			p.*,
			pt.text, pt.revision, pt.user AS revuser, pt.date AS revdate,
			userposted.(_userfields,rankset,title,picture,posts,postheader,signature,signsep,lastposttime,lastactivity,regdate,globalblock),
			useredited.(_userfields),
			userdeleted.(_userfields)
		FROM
			{posts} p
			LEFT JOIN {posts_text} pt ON pt.pid = p.id AND pt.revision = p.currentrevision
			LEFT JOIN {users} userposted ON userposted.id = p.user
			LEFT JOIN {users} useredited ON useredited.id = pt.user
			LEFT JOIN {users} userdeleted ON userdeleted.id = p.deletedby
		WHERE thread=?
		ORDER BY date ASC LIMIT ?, ?', $tid, $from, $ppp);

	// Set postlinks

	foreach($posts as &$post)
	{
		$links = array();
		if($post['deleted'])
		{
			if(Permissions::canDeletePost($post, $thread, $forum)){
				$links[] = array('title' => __('View'));
				$links[] = array('title' => __('Undelete'));
			}
		}
		else
		{
			$links[] = array('url' => Url::format('/post/#', $post['id']), 'title' => __('Link'));

			if(Permissions::canReply($thread, $forum))
				$links[] = array('title' => __('Quote'), 'js' => 'postQuote('.$post['id'].');');
			if(Permissions::canEditPost($post, $thread, $forum))
				$links[] = array('title' => __('Edit'));
			if(Permissions::canDeletePost($post, $thread, $forum))
				$links[] = array('title' => __('Delete'));
		}

		$post['links'] = $links;
	}

	//WTF PHP 
	unset($post);


	// Update thread views
	Sql::query('UPDATE {threads} SET views=views+1 WHERE id=?', $tid);

	// Set read date to the max date of the posts displayed in this page.
	// If the user is not viewing the last page, he will still see the unread marker.
	$readdate = 0;
	foreach($posts as $post)
		if($post['date'] > $readdate)
			$readdate = $post['date'];

	Sql::query(
		'INSERT INTO {threadsread} (id,thread,date) VALUES (?,?,?)
		ON DUPLICATE KEY UPDATE date = GREATEST(date, ?)',
		Session::id(), $tid, $readdate, $readdate);

	// Retrieve the draft.

	$posttext = '';
	$draft = Sql::querySingle('SELECT * FROM {drafts} WHERE user=? AND type=? AND target=?', Session::id(), 0, $tid);
	if($draft)
		$posttext = $draft['text'];

	$breadcrumbs = array(
		array('url' => Url::format('/#-#', $forum['id'], $forum['title']), 'title' => $forum['title']),
		array('url' => Url::format('/#-#/#-#', $forum['id'], $forum['title'], $thread['id'], $thread['title']), 'title' => $thread['title']),
	);

	$actionlinks = array(
//		array('url' => Url::format('/#-#/newthread', $forum['id'], $forum['title']), 'title' => __('Post thread'))
	);

	renderPage('thread.html', array(
		'forum' => $forum, 
		'thread' => $thread, 
		'posts' => $posts, 
		'posttext' => $posttext,
		'canreply' => Permissions::canReply($thread, $forum),
		'paging' => array(
			'perpage' => $ppp,
			'from' => $from,
			'total' => $thread['replies'] + 1, //+1 for the OP
			'base' => Url::format('/#-#/#-#', $forum['id'], $forum['title'], $thread['id'], $thread['title']),
		),
		'breadcrumbs' => $breadcrumbs, 
		'actionlinks' => $actionlinks,
		'title' => $forum['title'],
	));

}

