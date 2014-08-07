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


	public static function poll($id, $fail = true)
	{
		$res = Sql::querySingle('SELECT * FROM {poll} WHERE id=?', $id);
		if($res)
			return $res;

		if($fail)
			fail(__('Unknown poll ID.'));
		else
			return null;
	}
	public static function pollChoice($id, $fail = true)
	{
		$res = Sql::querySingle('SELECT * FROM {poll_choices} WHERE id=?', $id);
		if($res)
			return $res;

		if($fail)
			fail(__('Unknown poll choice ID.'));
		else
			return null;
	}

	public static function pollComplete($id, $fail = true)
	{
		$poll = Sql::querySingle(
			"SELECT p.*,
				(SELECT COUNT(DISTINCT user) 
					FROM {pollvotes} pv 
					WHERE pv.poll = p.id) as users,
				(SELECT COUNT(*) 
					FROM {pollvotes} pv 
					WHERE pv.poll = p.id) as votes
			FROM {poll} p
			WHERE p.id=?", 
			$id);
							 
		if(!$poll)
		{
			if($fail)
				fail(__('Unknown poll ID.'));
			else
				return null;
		}

		$poll['choices'] = Sql::queryAll(
			"SELECT pc.*,
				(SELECT COUNT(*) 
					FROM {pollvotes} pv 
					WHERE pv.poll = pc.poll AND pv.choiceid = pc.id) as votes,
				(SELECT COUNT(*) 
					FROM {pollvotes} pv 
					WHERE pv.poll = pc.poll AND pv.choiceid = pc.id AND pv.user = ?) as myvote
			FROM {poll_choices} pc
			WHERE poll=?", 
			Session::id(), $id);

		return $poll;
	}

	public static function draft($type, $target)
	{
		$draft = Sql::querySingle(
			'SELECT * FROM {drafts} WHERE user=? AND type=? AND target=?', 
			Session::id(), $type, $target);

		if($draft)
			$draft = json_decode($draft['data'], true);

		if(!is_array($draft))
			$draft = array();

		return $draft;
	}

}