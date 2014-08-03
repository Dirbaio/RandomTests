<?php

class Schema
{
	private static function varchar($len)
	{
		return array(
			'type' => 'varchar('.$len.')',
			'notNull' => true,
			'default' => ''
		);
	}

	public static function get()
	{
		$int = array(
			'type' => 'int(11)',
			'notNull' => true,
			'default' => '0'
		);
		$float = array(
			'type' => 'float',
			'notNull' => true,
			'default' => '0'
		);
		$bigint = array(
			'type' => 'bigint(12)',
			'notNull' => true,
			'default' => '0'
		);
		$bool = array(
			'type' => 'tinyint(1)',
			'notNull' => true,
			'default' => '0'
		);
		$text = array(
			'type' => 'text',
			'notNull' => false,  //NOT NULL breaks in certain versions/settings.
			'default' => ''
		);
		$textLong = array(
			'type' => 'mediumtext',
			'notNull' => false,  //NOT NULL breaks in certain versions/settings.
			'default' => ''
		);
		$AI = array(
			'type' => 'int(11)',
			'notNull' => true,
			'autoIncrement' => true,
		);
		$ip = self::varchar(50);

		$keyID = array
		(
			'fields' => array('id'),
			'type' => 'primary',
		);



		return array
		(
			'badges' => array
			(
				'fields' => array
				(
					'owner' => $int,
					'name' => self::varchar(256),
					'color' => $int,
				),
				'keys' => array
				(
					array(
						'fields' => array('owner', 'name'),
						'type' => 'primary',
					),
				),
			),
			'settings' => array
			(
				'fields' => array
				(
					'plugin' => self::varchar(128),
					'name' => self::varchar(128),
					'value' => $text,
				),
				'keys' => array
				(
					array(
						'fields' => array('plugin', 'name'),
						'type' => 'primary',
					),
				),
			),

			//Weird column names: An entry means that 'blockee' has blocked the layout of 'user'
			'blockedlayouts' => array
			(
				'fields' => array
				(
					'user' => $int,
					'blockee' => $int,
				),
				'keys' => array
				(
					array(
						'fields' => array('blockee', 'user'),
						'type' => 'primary',
					),
				),
			),
			'categories' => array
			(
				'fields' => array
				(
					'id' => $AI,
					'name' => self::varchar(256),
					'corder' => $int,
				),
				'keys' => array
				(
					$keyID,
				),

			),
			'enabledplugins' => array
			(
				'fields' => array
				(
					'plugin' => self::varchar(256),
				),
				'keys' => array
				(
					array(
						'fields' => array('plugin'),
						'type' => 'primary',
					),
				),
			),
			'forummods' => array
			(
				'fields' => array
				(
					'forum' => $int,
					'user' => $int,			
				),
				'keys' => array
				(
					array(
						'fields' => array('forum', 'user'),
						'type' => 'primary',
					),
				),
			),
			'forums' => array
			(
				'fields' => array
				(
					'id' => $AI,
					'title' => self::varchar(256),
					'description' => $text,
					'catid' => $int,
					'minpower' => $int,
					'minpowerthread' => $int,
					'minpowerreply' => $int,
					'numthreads' => $int,
					'numposts' => $int,
					'lastpostdate' => $int,
					'lastpostuser' => $int,
					'lastpostid' => $int,
					'hidden' => $bool,
					'forder' => $int,
				),
				'keys' => array
				(
					$keyID,
					array(
						'fields' => array('catid'),
					),
				),
			),
			'guests' => array
			(
				'fields' => array
				(
					'id' => $AI,
					'ip' => $ip,
					'date' => $int,
					'lasturl' => self::varchar(128),
					'lastforum' => $int,
					'useragent' => self::varchar(128),
					'bot' => $bool,
				),
				'keys' => array
				(
					$keyID,
					array(
						'fields' => array('ip'),
					),
					array(
						'fields' => array('bot'),
					),
				),
			),
			'ignoredforums' => array
			(
				'fields' => array
				(
					'uid' => $int,
					'fid' => $int,			
				),
				'keys' => array
				(
					array(
						'fields' => array('uid', 'fid'),
						'type' => 'primary',
					),
				),
			),
			'ip2c' => array
			(
				'fields' => array
				(
					'ip_from' => $bigint,
					'ip_to' => $bigint,
					'cc' => self::varchar(2),
				),
				'keys' => array
				(
					array(
						'fields' => array('ip_from'),
					),
				),
			),
			'ipbans' => array
			(
				'fields' => array
				(
					'ip' => $ip,
					'reason' => self::varchar(128),		
					'date' => $int,			
					'whitelisted' => $bool,
				),
				'keys' => array
				(
					array(
						'fields' => array('ip'),
					),
					array(
						'fields' => array('date'),
					),
				),
			),
			'misc' => array
			(
				'fields' => array
				(
					'version' => $int,
					'views' => $int,
					'hotcount' => $int,			
					'maxusers' => $int,
					'maxusersdate' => $int,
					'maxuserstext' => $text,
					'maxpostsday' => $int,
					'maxpostsdaydate' => $int,
					'maxpostshour' => $int,
					'maxpostshourdate' => $int,
					'milestone' => $text,
				),
				'keys' => array
				(
				),
			),
			'moodavatars' => array
			(
				'fields' => array
				(
					'id' => $AI,
					'uid' => $int,			
					'mid' => $int,			
					'name' => self::varchar(256),
				),
				'keys' => array
				(
					$keyID,
					array(
						'fields' => array('uid'),
					),
				),
			),
			'pmsgs' => array
			(
				'fields' => array
				(
					'id' => $AI,
					'userto' => $int,
					'userfrom' => $int,
					'date' => $int,
					'ip' => $ip,
					'msgread' => $bool,
					'deleted' => $int,
					'drafting' => $bool,
				),
				'keys' => array
				(
					$keyID,
					array(
						'fields' => array('userto'),
					),
					array(
						'fields' => array('userfrom'),
					),
					array(
						'fields' => array('msgread'),
					),
					array(
						'fields' => array('date'),
					),
				),
			),
			'pmsgs_text' => array
			(
				'fields' => array
				(
					'pid' => $int,
					'title' => self::varchar(256),
					'text' => $textLong,
				),
				'keys' => array(
					array(
						'fields' => array('pid'),
						'type' => 'primary',
					),
				)
			),
			'poll' => array
			(
				'fields' => array
				(
					'id' => $AI,
					'question' => self::varchar(256),
					'briefing' => $text,
					'closed' => $bool,
					'doublevote' => $bool,
				),
				'keys' => array
				(
					$keyID,
				),

			),
			'pollvotes' => array
			(
				'fields' => array
				(
					'user' => $int,
					'choiceid' => $int,
					'poll' => $int,
				),
				'keys' => array
				(
					array(
						'fields' => array('poll', 'choiceid', 'user'),
						'type' => 'primary',
					),
				),
			),
			'poll_choices' => array
			(
				'fields' => array
				(
					'id' => $AI,
					'poll' => $int,
					'choice' => self::varchar(256),
					'color' => self::varchar(32),
				),
				'keys' => array
				(
					$keyID,
					array(
						'fields' => array('poll'),
					),
				),
			),
			'posts' => array
			(
				'fields' => array
				(
					'id' => $AI,
					'thread' => $int,
					'user' => $int,
					'date' => $int,
					'ip' => $ip,
					'num' => $int,
					'deleted' => $bool,
					'deletedby' => $int,
					'reason' => $text,
					'options' => $int,
					'mood' => $int,
					'currentrevision' => $int,
				),
				'keys' => array
				(
					$keyID,
					array(
						'fields' => array('date'),
					),
					array(
						'fields' => array('thread', 'date'),
					),
					array(
						'fields' => array('user', 'date'),
					),
					array(
						'fields' => array('ip'),
					),
					array(
						'fields' => array('id', 'currentrevision'),
					),
					array(
						'fields' => array('deletedby'),
					),
				),
			),
			'drafts' => array
			(
				'fields' => array
				(
					'user' => $int, //User who created the draft. Drafts are assumed to be private to that user.
					'type' => $int, //Type: 0 = thread, 1 = forum
					'target' => $int, //Target: Thread ID or Forum ID
					'date' => $int,
					'data' => $textLong, //JSON of the form data.
				),
				'keys' => array
				(
					array(
						'fields' => array('user', 'type', 'target'),
						'type' => 'primary',
					),
				),
			),
			'posts_text' => array
			(
				'fields' => array
				(
					'pid' => $int,
					'text' => $textLong,
					'revision' => $int,
					'user' => $int,
					'date' => $int,
				),
				'keys' => array
				(
					array(
						'fields' => array('pid', 'revision'),
						'type' => 'primary',
					),
				),
			),
			'queryerrors' => array
			(
				'fields' => array
				(
					'id' => $AI,		
					'user' => $int,	
					'ip' => $ip,
					'time' => $int,	
					'query' => $text,
					'get' => $text,
					'post' => $text,
					'cookie' => $text,
					'error' => $text
				),
				'keys' => array
				(
					$keyID,
				),

			),
			'log' => array
			(
				'fields' => array
				(
					'user' => $int,
					'date' => $int,
					'type' => self::varchar(32),
					'user2' => $int,
					'thread' => $int,
					'post' => $int,
					'forum' => $int,
					'forum2' => $int,
					'pm' => $int,
					'text' => self::varchar(1024),
					'ip' => $ip
				),
				'keys' => array
				(
					//TODO: WTF doesnt this have keys??
				),
			),
			'sessions' => array
			(
				'fields' => array
				(
					'id' => self::varchar(256),
					'user' => $int,
					'expiration' => $int,
					'autoexpire' => $bool,
					'iplock' => $bool,
					'iplockaddr' => self::varchar(128),
					'lastip' => self::varchar(128),
					'lasturl' => self::varchar(128),
					'lasttime' => $int,
				),
				'keys' => array
				(
					$keyID,
					array(
						'fields' => array('user'),
					),
					array(
						'fields' => array('expiration'),
					),
				),
			),
			'smilies' => array
			(
				'fields' => array
				(
					'id' => $AI,
					'code' => self::varchar(32),
					'image' => self::varchar(32),
				),
				'keys' => array
				(
					$keyID,
				),

			),
			'threads' => array
			(
				'fields' => array
				(
					'id' => $AI,
					'forum' => $int,
					'user' => $int,
					'date' => $int,
					'firstpostid' => $int,
					'views' => $int,
					'title' => self::varchar(128),
					'icon' => self::varchar(256),
					'replies' => $int,
					'lastpostdate' => $int,
					'lastposter' => $int,
					'lastpostid' => $int,
					'closed' => $bool,
					'sticky' => $bool,
					'poll' => $int,
				),
				'keys' => array
				(
					$keyID,
					array(
						'fields' => array('forum', 'lastpostdate'),
					),
					array(
						'fields' => array('forum', 'sticky', 'lastpostdate'),
					),
					array(
						'fields' => array('date'),
					),
					array(
						'fields' => array('user', 'lastpostdate'),
					),
				),
			),
			'threadsread' => array
			(
				'fields' => array
				(
					'id' => $int,
					'thread' => $int,
					'date' => $int,
				),
				'keys' => array
				(
					array(
						'fields' => array('id', 'thread'),
						'type' => 'primary',
					),
				),
			),
			// cid = user who commented
			// uid = user whose profile received the comment
			'usercomments' => array
			(
				'fields' => array
				(
					'id' => $AI,
					'uid' => $int,
					'cid' => $int,
					'text' => $text,
					'date' => $int,
				),
				'keys' => array
				(
					$keyID,
					array(
						'fields' => array('uid', 'date'),
					),
				),
			),
			/*
			'users' => array
			(
				'fields' => array
				(
					'id' => $AI,
					'name' => self::varchar(32),
					'displayname' => self::varchar(32),
					'password' => self::varchar(256),
					'pss' => self::varchar(16),
					'powerlevel' => $int,
					'posts' => $int,
					'regdate' => $int,
					'minipic' => self::varchar(128),
					'picture' => self::varchar(128),
					'title' => self::varchar(256),
					'postheader' => $text,
					'signature' => $text,
					'bio' => $text,
					'sex' => $int,
					'rankset' => self::varchar(128),
					'realname' => self::varchar(64),
					'lastknownbrowser' => $text,
					'location' => self::varchar(128),
					'birthday' => $int,
					'email' => self::varchar(64),
					'homepageurl' => self::varchar(128),
					'homepagename' => self::varchar(128),			
					'lastposttime' => $int,
					'lastactivity' => $int,
					'lastip' => self::varchar(64),
					'lasturl' => self::varchar(128),
					'lastforum' => $int,
					'postsperpage' => $int,
					'threadsperpage' => $int,
					'timezone' => $float,
					'theme' => self::varchar(64),
					'signsep' => $bool,
					'dateformat' => self::varchar(32),
					'timeformat' => self::varchar(32),
					'fontsize' => $int,
					'karma' => $int,
					'blocklayouts' => $bool,
					'globalblock' => $bool,
					'usebanners' => $bool,
					'showemail' => $bool,
					'newcomments' => $bool,
					'tempbantime' => $bigint,
					'tempbanpl' => $int,
					'forbiddens' => self::varchar(1024),
					'pluginsettings' => $text,
					'lostkey' => self::varchar(128),
					'lostkeytimer' => $int,
					'loggedin' => $bool,
					'convertpassword' => self::varchar(256),
					'convertpasswordsalt' => self::varchar(256),
					'convertpasswordtype' => self::varchar(256),
				),
				'keys' => array
				(
					$keyID,
					array(
						'fields' => array('posts'),
					),
					array(
						'fields' => array('name'),
					),
					array(
						'fields' => array('lastforum'),
					),
					array(
						'fields' => array('lastposttime'),
					),
					array(
						'fields' => array('lastactivity'),
					),
				),
			),
			*/
			'uservotes' => array
			(
				'fields' => array
				(
					'uid' => $int,
					'voter' => $int,
					'up' => $bool,
				),
				'keys' => array(
					array(
						'fields' => array('uid', 'voter'),
						'type' => 'primary',
					),
				),
			),
		);
	}
}
