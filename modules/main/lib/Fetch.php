<?php

class Fetch
{
	public static function forum($id, $fail = true)
	{
		$res = Sql::querySingle('SELECT * FROM {forums} WHERE id=?', $id);
		if($res)
			return $res;

		if($fail)
			fail(__('Unknown thread ID.'));
		else
			return null;
	}
	
	public static function thread($id, $fail = true)
	{
		$res = Sql::querySingle('SELECT * FROM {threads} WHERE id=?', $id);
		if($res)
			return $res;

		if($fail)
			fail(__('Unknown thread ID.'));
		else
			return null;
	}
	
	public static function post($id, $fail = true)
	{
		$res = Sql::querySingle(
			'SELECT p.*, pt.text
			FROM {posts} p
			LEFT JOIN {posts_text} pt on pt.pid = p.id and pt.revision = p.currentrevision
			WHERE p.id=?', 
			$id);

		if($res)
			return $res;

		if($fail)
			fail(__('Unknown thread ID.'));
		else
			return null;
	}

	public static function user($id, $fail = true)
	{
		$res = Sql::querySingle('SELECT * FROM {users} WHERE id=?', $id);
		if($res)
			return $res;

		if($fail)
			fail(__('Unknown thread ID.'));
		else
			return null;
	}
}