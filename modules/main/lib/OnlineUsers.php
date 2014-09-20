<?php

class OnlineUsers
{
	private static $fid = 0;
	
	public static function update()
	{
		//delete old visitors from the guest list.
		Sql::query('DELETE FROM {guests} WHERE date < ?', time()-300);
		
		//TODO do shit
	}
}