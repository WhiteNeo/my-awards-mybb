<?php

/************************************************
 *
 *  Awards plugin: Automatic awards performance
 *  Author: Dark Neo
 *  Copyright: © 2014 DNT
 *  Version: 1.0
 *  Website: http://darkneo.skn1.com
 ************************************************/
 
 //Give to users his obtained awards.
 function task_awards($task)
{
	global $db,$cache,$mybb,$lang;
	$lang->load['awards'];

	$query = $db->simple_select("users", "uid");
	while($user = $db->fetch_array($query))
	{
		$users[$user['uid']] = $user;
	}
	
	foreach($users as $user)
	{
		$query = $db->simple_select("threads", "COUNT(tid) AS threads", "uid = '".$user['uid']."'");
		$threads_count = intval($db->fetch_field($query, "threads"));
		$db->update_query("users", array("awards_threads" => $threads_count), "uid = '".$user['uid']."'");
	}
	
	$cells = array('threads', 'posts', 'reputation', 'activity');
	$awards_threads_id = array();
	$awards_posts_id = array();
	$awards_reputation_id = array();
	$awards_activity_id = array();
	foreach ($cells as $type)
	{
		$query = $db->simple_select('awards_'.$type);
		while ($awards = $db->fetch_array($query))
		{
			switch ($type)
			{
				case 'threads':
					$awards_threads_id[$awards['taid']] = $awards;
				break;
				
				case 'posts':
					$awards_posts_id[$awards['paid']] = $awards;
				break;
				
				case 'reputation':
					$awards_reputation_id[$awards['raid']] = $awards;
				break;
				
				case 'activity':
					$awards_activity_id[$awards['aaid']] = $awards;
				break;
				
			}
		}
		$db->free_result($query);
	}
	$users = array();
	$query = $db->simple_select("users", "*", "lastactive >= '{$task['lastrun']}'");
	//$query = $db->simple_select("users", "*", "");
	while($user = $db->fetch_array($query))
	{
		$users[$user['uid']] = $user;
	}
	
	foreach ($users as $uid => $user)
	{
		$my_awards = unserialize($user['awards']);

		foreach($awards_threads_id as $taid => $award)
		{
			if (!isset($my_awards['taid'][$taid]) || empty($my_awards['taid'][$taid]))
			{
				if ($award['threads'] <= intval($user['awards_threads']))
				{
					$my_awards['taid'][$taid] = array('taid' => intval($award['taid']), 'name' => $db->escape_string($award['name']));
				}
			}
		}
		foreach($awards_posts_id as $paid => $award)
		{
			if (!isset($my_awards['paid'][$paid]) || empty($my_awards['paid'][$paid]))
			{
				if ($award['posts'] <= intval($user['postnum']))
				{
					$my_awards['paid'][$paid] = array('paid' => intval($award['paid']), 'name' => $db->escape_string($award['name']));
				}
			}
		}
		foreach($awards_reputation_id as $raid => $award)
		{
			if (!isset($my_awards['raid'][$raid]) || empty($my_awards['raid'][$raid]))
			{
				if ($award['reputation'] <= intval($user['reputation']))
				{
					$my_awards['raid'][$raid] = array('raid' => intval($award['raid']), 'name' => $db->escape_string($award['name']));
				}
			}
		}
		foreach($awards_activity_id as $aaid => $award)
		{
			if (!isset($my_awards['aaid'][$aaid]) || empty($my_awards['aaid'][$aaid]))
			{
				if ($award['time'] <= intval($user['timeonline']))
				{
					$my_awards['aaid'][$apid] = array('aaid' => intval($award['aaid']), 'name' => $db->escape_string($award['name']));
				}
			}
		}
		
		$my_awards = serialize($my_awards);
		//$db->update_query('users', 'set awards = ""');	
		$db->update_query('users', array('awards' => $my_awards), 'uid=\''.$uid.'\'');	
	}
	
	add_task_log($task, $lang->awards_name_task_run);
}
?>