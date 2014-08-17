<?php 
//page /recalc

function usectime()
{
	$t = gettimeofday();
	return $t['sec'] + ($t['usec'] / 1000000);
}

function fix($what, $query)
{
	echo $what, '... ';
	$start = usectime();
	$affected = Sql::queryAffected($query);
	$len = usectime() - $start;

	echo sprintf('%1.3f', $len), 's ';
	if($affected)
		echo $affected, ' rows affected. ';
	echo "\n";
}

function request()
{
	// User stuff
	fix('User postcount', 
		'UPDATE {users} u 
		SET posts = (SELECT COUNT(*) FROM {posts} p WHERE p.user = u.id)');

	// Post stuff

	// post num (This is not working, no idea how to do it easily)
	//UPDATE posts p SET num = (SELECT COUNT(*) FROM posts p2 WHERE p2.user = p.user AND p2.id <= p.id)

	fix('Post currentrevision', 
		'UPDATE posts p
		SET currentrevision=(
			SELECT MAX(revision) 
			FROM posts_text pt 
			WHERE pt.pid = p.id)');

	fix('Post editdate', 
		'UPDATE posts p
		SET editdate=GREATEST(p.date, (
			SELECT pt.date 
			FROM posts_text pt 
			WHERE pt.pid = p.id AND pt.revision = p.currentrevision))');

	// Thread stuff
	fix('Thread replies', 
		'UPDATE {threads} t
		SET replies = (SELECT COUNT(*) FROM {posts} p WHERE p.thread = t.id) - 1');

	fix('Thread first post id, date, user', 
		'UPDATE threads t 
		LEFT JOIN (
			SELECT thread, MIN(id) as minid
			FROM posts
			GROUP BY thread) AS tmp ON tmp.thread = t.id
		LEFT JOIN {posts} p ON tmp.minid=p.id
		SET t.firstpostid=p.id, t.date=p.date, t.user=p.user');

	fix('Thread last post id,user,date', 
		'UPDATE threads t 
		LEFT JOIN (
			SELECT thread, MAX(id) as maxid
			FROM posts
			GROUP BY thread) AS tmp ON tmp.thread = t.id
		LEFT JOIN posts p ON p.id=tmp.maxid
		SET lastpostid=p.id, lastpostuser=p.user, lastpostdate=p.editdate');

	// Forum stuff
	fix('Forum threads', 
		'UPDATE {forums} f
		SET numthreads = (SELECT COUNT(*) FROM {threads} t WHERE t.forum = f.id)');

	fix('Forum posts', 
		'UPDATE {forums} f 
		SET numposts = (SELECT SUM(replies+1) FROM {threads} t WHERE t.forum = f.id)');

	fix('Forum last post id,user,date', 
		'UPDATE forums f 
		LEFT JOIN (
			SELECT forum, MAX(lastpostdate) as maxdate
			FROM threads
			GROUP BY forum) AS tmp ON tmp.forum = f.id
		LEFT JOIN threads t ON t.lastpostdate=tmp.maxdate
		SET f.lastpostid=t.lastpostid, f.lastpostuser=t.lastpostuser, f.lastpostdate=t.lastpostdate');


}