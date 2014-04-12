<?php

/************************************************
 *
 *  Awards plugin: Automatic awards performance
 *  Author: Dark Neo
 *  Copyright: © 2014 DNT
 *  Version: 1.0
 *  Website: http://darkneo.skn1.com
 ************************************************/
 
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

//Hook utilizado para mostrar los logros en la caja del mensaje
$plugins->add_hook("postbit", "awards_postbit");
$plugins->add_hook("postbit_pm", "awards_postbit");

// Hook utilizado para mostrar los logros en el perfil de usuario
$plugins->add_hook('member_profile_end', 'awards_memprofile');

//Cargar las plantillas globalmente.
$plugins->add_hook("global_start", "awards_load_templates");

function awards_info()
{
	global $lang;
	$lang->load("awards", false, true);
	return array(
		"name"			=> $lang->awards_name,
		"description"	=> $lang->awards_descrip,
		"website"		=> "http://darkneo.skn1.com",
		"author"		=> "Dark Neo",
		"authorsite"	=> "http://darkneo.skn1.com",
		"version"		=> "1.0",
		"compatibility"   => "16*",
		"guid"			=> ""
	);
}

function awards_is_installed(){
	global $db,$mybb;
	if($db->table_exists("awards_threads"))
	{
		return true;
	}
}

function awards_install() 
{

	//awards_uninstall();
	
	global $mybb, $db, $lang, $cache;
	
	$lang->load("awards", false, true);

	if(!$db->table_exists("awards_threads"))
	{
		$db->query("CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."awards_threads` (
  `taid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(200) NOT NULL,
  `threads` int(10) NOT NULL DEFAULT '0',
  `image` varchar(250) NOT NULL,
  PRIMARY KEY (`taid`)
  ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10;");
 
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_threads (`taid`,`name`,`description`,`threads`,`image`) VALUES ('1','Logro por 10 temas','Has escrito 10 temas','10','uploads/awards/threads_01.png');");
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_threads (`taid`,`name`,`description`,`threads`,`image`) VALUES ('2','Logro por 50 temas','Has escrito 50 temas','50','uploads/awards/threads_02.png');");
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_threads (`taid`,`name`,`description`,`threads`,`image`) VALUES ('3','Logro por 100 temas','Has escrito 100 temas','100','uploads/awards/threads_03.png');");
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_threads (`taid`,`name`,`description`,`threads`,`image`) VALUES ('4','Logro por 200 temas','Has escrito 200 temas','200','uploads/awards/threads_04.png');");
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_threads (`taid`,`name`,`description`,`threads`,`image`) VALUES ('5','Logro por 350 temas','Has escrito 350 temas','350','uploads/awards/threads_05.png');");
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_threads (`taid`,`name`,`description`,`threads`,`image`) VALUES ('6','Logro por 600 temas','Has escrito 600 temas','600','uploads/awards/threads_06.png');");
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_threads (`taid`,`name`,`description`,`threads`,`image`) VALUES ('7','Logro por 900 temas','Has escrito 900 temas','900','uploads/awards/threads_07.png');");
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_threads (`taid`,`name`,`description`,`threads`,`image`) VALUES ('8','Logro por 1150 temas','Has escrito 1150 temas','1150','uploads/awards/threads_08.png');");
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_threads (`taid`,`name`,`description`,`threads`,`image`) VALUES ('9','Logro por 1500 temas','Has escrito 1500 temas','1500','uploads/awards/threads_09.png');");

	}
	
	if(!$db->table_exists("awards_posts"))
	{
		$db->query("CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."awards_posts` (
  `paid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(200) NOT NULL DEFAULT '',
  `posts` int(10) NOT NULL DEFAULT '0',
  `image` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`paid`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10;");

	$db->query("INSERT INTO ".TABLE_PREFIX."awards_posts (`paid`,`name`,`description`,`posts`,`image`) VALUES ('1','Logro por 10 mensajes','Has escrito 10 mensajes','10','uploads/awards/posts_01.gif');");
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_posts (`paid`,`name`,`description`,`posts`,`image`) VALUES ('2','Logro por 50 mensajes','Has escrito 50 mensajes','50','uploads/awards/posts_02.gif');");
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_posts (`paid`,`name`,`description`,`posts`,`image`) VALUES ('3','Logro por 100 mensajes','Has escrito 100 mensajes','100','uploads/awards/posts_03.gif');");
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_posts (`paid`,`name`,`description`,`posts`,`image`) VALUES ('4','Logro por 200 mensajes','Has escrito 200 mensajes','200','uploads/awards/posts_04.gif');");
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_posts (`paid`,`name`,`description`,`posts`,`image`) VALUES ('5','Logro por 350 mensajes','Has escrito 350 mensajes','350','uploads/awards/posts_05.gif');");
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_posts (`paid`,`name`,`description`,`posts`,`image`) VALUES ('6','Logro por 600 mensajes','Has escrito 600 mensajes','600','uploads/awards/posts_06.gif');");
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_posts (`paid`,`name`,`description`,`posts`,`image`) VALUES ('7','Logro por 900 mensajes','Has escrito 900 mensajes','900','uploads/awards/posts_07.gif');");
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_posts (`paid`,`name`,`description`,`posts`,`image`) VALUES ('8','Logro por 1150 mensajes','Has escrito 1150 mensajes','1150','uploads/awards/posts_08.gif');");
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_posts (`paid`,`name`,`description`,`posts`,`image`) VALUES ('9','Logro por 1500 mensajes','Has escrito 1500 mensajes','1500','uploads/awards/posts_09.png');");

	}

	if(!$db->table_exists("awards_reputation"))
	{
		$db->query("CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."awards_reputation` (
  `raid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(200) NOT NULL DEFAULT '',
  `reputation` int(5) NOT NULL DEFAULT '0',
  `image` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`raid`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10;");

	$db->query("INSERT INTO ".TABLE_PREFIX."awards_reputation (`raid`,`name`,`description`,`reputation`,`image`) VALUES ('1','Logro por 10 puntos de reputación','Ganaste 10 puntos de reputación','10','uploads/awards/reputation_01.gif');");	
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_reputation (`raid`,`name`,`description`,`reputation`,`image`) VALUES ('2','Logro por 50 puntos de reputación','Ganaste 50 puntos de reputación','50','uploads/awards/reputation_02.png');");
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_reputation (`raid`,`name`,`description`,`reputation`,`image`) VALUES ('3','Logro por 100 puntos de reputación','Ganaste 100 puntos de reputación','100','uploads/awards/reputation_03.gif');");
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_reputation (`raid`,`name`,`description`,`reputation`,`image`) VALUES ('4','Logro por 200 puntos de reputación','Ganaste 200 puntos de reputación','200','uploads/awards/reputation_04.gif');");
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_reputation (`raid`,`name`,`description`,`reputation`,`image`) VALUES ('5','Logro por 350 puntos de reputación','Ganaste 350 puntos de reputación','350','uploads/awards/reputation_05.gif');");
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_reputation (`raid`,`name`,`description`,`reputation`,`image`) VALUES ('6','Logro por 600 puntos de reputación','Ganaste 600 puntos de reputación','600','uploads/awards/reputation_06.gif');");
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_reputation (`raid`,`name`,`description`,`reputation`,`image`) VALUES ('7','Logro por 900 puntos de reputación','Ganaste 900 puntos de reputación','900','uploads/awards/reputation_07.gif');");
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_reputation (`raid`,`name`,`description`,`reputation`,`image`) VALUES ('8','Logro por 1150 puntos de reputación','Ganaste 1150 puntos de reputación','1150','uploads/awards/reputation_08.gif');");
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_reputation (`raid`,`name`,`description`,`reputation`,`image`) VALUES ('9','Logro por 1500 puntos de reputación','Ganaste 1500 puntos de reputación','1500','uploads/awards/reputation_09.gif');");

	}

	if(!$db->table_exists("awards_activity"))
	{
		$db->query("CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."awards_activity` (
  `aaid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(200) NOT NULL DEFAULT '',
  `years` int(5) NOT NULL DEFAULT '0',
  `months` int(5) NOT NULL DEFAULT '0',
  `days` int(5) NOT NULL DEFAULT '0',
  `time` int(10) NOT NULL DEFAULT '0',
  `image` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`aaid`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10;");

	$db->query("INSERT INTO ".TABLE_PREFIX."awards_activity (`aaid`,`name`,`description`,`years`,`months`,`days`,`time`,`image`) VALUES ('1','10 dias','logro de 10 días','0','0','10','864000','uploads/awards/activity_01.gif');");
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_activity (`aaid`,`name`,`description`,`years`,`months`,`days`,`time`,`image`) VALUES ('2','1 mes','logro de 1 mes','0','1','0','2551443','uploads/awards/activity_02.gif');");
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_activity (`aaid`,`name`,`description`,`years`,`months`,`days`,`time`,`image`) VALUES ('3','3 meses','logro de 3 meses','0','3','0','7654329','uploads/awards/activity_03.gif');");
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_activity (`aaid`,`name`,`description`,`years`,`months`,`days`,`time`,`image`) VALUES ('4','7 meses','logro de 7 meses','0','7','0','17860101','uploads/awards/activity_04.gif');");
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_activity (`aaid`,`name`,`description`,`years`,`months`,`days`,`time`,`image`) VALUES ('5','1 año','logro de 1 año','1','0','0','31556952','uploads/awards/activity_05.gif');");
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_activity (`aaid`,`name`,`description`,`years`,`months`,`days`,`time`,`image`) VALUES ('6','2 años','logro de 2 años','2','0','0','63113904','uploads/awards/activity_06.gif');");
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_activity (`aaid`,`name`,`description`,`years`,`months`,`days`,`time`,`image`) VALUES ('7','5 años','logro de 5 años','5','0','0','157784760','uploads/awards/activity_07.gif');");
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_activity (`aaid`,`name`,`description`,`years`,`months`,`days`,`time`,`image`) VALUES ('8','10 años','logro de 10 años','10','0','0','315569520','uploads/awards/activity_08.png');");
	$db->query("INSERT INTO ".TABLE_PREFIX."awards_activity (`aaid`,`name`,`description`,`years`,`months`,`days`,`time`,`image`) VALUES ('9','25 años','logro de 25 años','25','0','0','788923800','uploads/awards/activity_09.png');");
	
	}
	
	if(!$db->field_exists("awards", "users"))  
		$db->add_column("users", "awards", "TEXT NOT NULL;");
	if(!$db->field_exists("awards_threads", "users"))  
		$db->add_column("users", "awards_threads", "int(10) unsigned NOT NULL default '0'"); 
		
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

	$new_task = array(
		"title" => $lang->awards_name_task,
		"description" => $lang->awards_name_task_desc,
		"file" => "awards",
		"minute" => '0',
		"hour" => '0',
		"day" => '*',
		"month" => '*',
		"weekday" => '*',
		"nextrun" => time() + (1*24*60*60),
		"enabled" => '1',
		"logging" => '1'
	);
	$tid = $db->insert_query("tasks", $new_task);
	
	$awards_groups = array(
		"gid"			=> "NULL",
		"name"			=> "awards",
		"title" 		=> $lang->awards_name,
		"description"	=> $lang->awards_descrip,
		"disporder"		=> "0",
		"isdefault"		=> "no",
	);
	$db->insert_query("settinggroups", $awards_groups);
	$gid = $db->insert_id();
	$award = array(
		array(
			"name"			=> "awards_enabled",
			"title"			=> $lang->awards_enabled,
			"description"	=> $lang->awards_enabled_desc,
			"optionscode"	=> "yesno",
			"value"			=> 1,
			"disporder"		=> 1,
			"gid"			=> $gid,
		),	
		array(
			"name"			=> "awards_per_threads",
			"title"			=> $lang->awards_per_threads,
			"description"	=> $lang->awards_per_threads_desc,
			"optionscode"	=> "yesno",
			"value"			=> 1,
			"disporder"		=> 2,
			"gid"			=> $gid,
		),
		array(
			"name"			=> "awards_per_posts",
			"title"			=> $lang->awards_per_posts,
			"description"	=> $lang->awards_per_posts_desc,
			"optionscode"	=> "yesno",
			"value"			=> 1,
			"disporder"		=> 3,
			"gid"			=> $gid,
		),
		array(
			"name"			=> "awards_per_reputation",
			"title"			=> $lang->awards_per_reputation,
			"description"	=> $lang->awards_per_reputation,
			"optionscode"	=> "yesno",
			"value"			=> 1,
			"disporder"		=> 4,
			"gid"			=> $gid,
		),
		array(
			"name"			=> "awards_per_activity",
			"title"			=> $lang->awards_per_activity,
			"description"	=> $lang->awards_per_activity,
			"optionscode"	=> "yesno",
			"value"			=> 1,
			"disporder"		=> 5,
			"gid"			=> $gid,
		),
		array(
			"name"			=> "awards_pagination",
			"title"			=> $lang->awards_pagination,
			"description"	=> $lang->awards_pagination_desc,
			"optionscode"	=> "text",
			"value"			=> "10",
			"disporder"		=> 6,
			"gid"			=> $gid,
		),
		array(
			"name"			=> "awards_max_chars",
			"title"			=> $lang->awards_max_chars,
			"description"	=> $lang->awards_max_chars_desc,
			"optionscode"	=> "text",
			"value"			=> "150",
			"disporder"		=> 7,
			"gid"			=> $gid,
		),
		array(
			"name"			=> "awards_max_postbit",
			"title"			=> $lang->awards_max_postbit,
			"description"	=> $lang->awards_max_postbit_desc,
			"optionscode"	=> "text",
			"value"			=> "5",
			"disporder"		=> 8,
			"gid"			=> $gid,
		),		
		array(
			"name"			=> "awards_max_profile",
			"title"			=> $lang->awards_max_profile,
			"description"	=> $lang->awards_max_profile_desc,
			"optionscode"	=> "text",
			"value"			=> "5",
			"disporder"		=> 8,
			"gid"			=> $gid,
		),			
	);
	foreach($award as $award_settings)
	$db->insert_query("settings", $award_settings);
	rebuildsettings();
   	$cache->update_forums();	
}

function awards_activate(){
	global $db;

    //Adding new group
    $templategrouparray = array(
        'prefix' => 'awards',
        'title'  => 'My Awards'
    );
    $db->insert_query("templategroups", $templategrouparray);
	
	$awards = array(
		"title"		=> 'awards',
		"template"	=> $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$lang->awards}</title>
{$headerinclude}
</head>
<body>
{$header}
{$awards_error}
<table width="100%" border="0" align="center">
<tr>
{$awards_nav}
<td valign="top">
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" colspan="3">
<strong>{$lang->awards_threads}</span></td>
</tr>
<tr>
<td class="tcat" width="5%"><strong>{$lang->awards_image}</strong></td>
<td class="tcat" width="20%"><strong>{$lang->awards_name}</strong></td>
<td class="tcat"><strong>{$lang->awards_descrip}</strong></td>
</tr>
{$threads_awards}
</table>
<br />
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" colspan="3">
<strong>{$lang->awards_posts}</span></td>
</tr>
<tr>
<td class="tcat" width="5%"><strong>{$lang->awards_image}</strong></td>
<td class="tcat" width="20%"><strong>{$lang->awards_name}</strong></td>
<td class="tcat"><strong>{$lang->awards_descrip}</strong></td>
</tr>
{$posts_awards}
</table>
<br />
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" colspan="3">
<strong>{$lang->awards_reputation}</span></td>
</tr>
<tr>
<td class="tcat" width="5%"><strong>{$lang->awards_image}</strong></td>
<td class="tcat" width="20%"><strong>{$lang->awards_name}</strong></td>
<td class="tcat"><strong>{$lang->awards_descrip}</strong></td>
</tr>
{$reputation_awards}
</table>
<br />
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" colspan="3">
<strong>{$lang->awards_activity}</span></td>
</tr>
<tr>
<td class="tcat" width="5%"><strong>{$lang->awards_image}</strong></td>
<td class="tcat" width="20%"><strong>{$lang->awards_name}</strong></td>
<td class="tcat"><strong>{$lang->awards_descrip}</strong></td>
</tr>
{$activity_awards}
</table>
</td>
</tr>
</table>
{$multipage}
{$footer}
</body>
</html>'),
		"sid"		=> -2,
		"version"	=> 1600,
		"dateline"	=> time(),
	);
	
	$awards_award = array(
		"title"		=> 'awards_award',
		"template"	=> $db->escape_string('<tr>
<td class="{$alt_bg}" width="5%" align="center"><img src="{$award[\'image\']}" /></td>
<td class="{$alt_bg}" width="20%">{$award[\'name\']}</td>
<td class="{$alt_bg}">{$award[\'description\']}</td>
</tr>'),
		"sid"		=> -2,
		"version"	=> 1600,
		"dateline"	=> time(),
	);
	
	$awards_award_empty = array(
		"title"		=> 'awards_award_empty',
		"template"	=> $db->escape_string('<tr><td class="trow1" colspan="4" align="center">{$lang->awards_empty}</td></tr>'),
		"sid"		=> -2,
		"version"	=> 1600,
		"dateline"	=> time(),
	);

	$awards_memprofile = array(
		"title"		=> 'awards_memprofile',
		"template"	=> $db->escape_string('<br />
<table id="awards" border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead"><strong>{$lang->awards_profile}</strong></td>
</tr>
<tr>
<td class="trow1">
{$awards}
<br />
<a class="smalltext" href="{$mybb->settings[\'bburl\']}/awards.php?uid={$post[\'uid\']}&my_post_key={$mybb->post_code}">[ {$lang->awards_see_all} ]</a>
</td>
</tr>
</table>
<br />'),
		"sid"		=> -2,
		"version"	=> 1600,
		"dateline"	=> time(),
	);

	$awards_postbit = array(
		"title"		=> 'awards_postbit',
		"template"	=> $db->escape_string('<br />
<div class="awards">
<strong>{$lang->awards}</strong><br />
{$awards}
<br />
<a class="smalltext" href="{$mybb->settings[\'bburl\']}/awards.php?uid={$post[\'uid\']}&my_post_key={$mybb->post_code}">[ {$lang->awards_see_all} ]</a>
</div>'),
		"sid"		=> -2,
		"version"	=> 1600,
		"dateline"	=> time(),
	);
	
	$awards_nav = array(
		"title"		=> 'awards_nav',
		"template"	=> $db->escape_string('<td width="180" valign="top">
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="trow1 smalltext">
<a href="awards.php">{$lang->awards}</a>
</td>
</tr>
<td class="trow1 smalltext">
<a href="{$mybb->settings[\'bburl\']}/awards.php?uid={$post[\'uid\']}&my_post_key={$mybb->post_code}">{$lang->my_awards}</a>
</td>
</tr>
</table>
</td>'),
		"sid"		=> -2,
		"version"	=> 1600,
		"dateline"	=> time(),
	);
	
	$db->insert_query("templates", $awards);	
	$db->insert_query("templates", $awards_award);	
	$db->insert_query("templates", $awards_award_empty);		
	$db->insert_query("templates", $awards_nav);
	$db->insert_query("templates", $awards_memprofile);
	$db->insert_query("templates", $awards_postbit);
	
	require_once MYBB_ROOT."/inc/adminfunctions_templates.php";
	$find     = '#' . preg_quote('{$post[\'user_details\']}') . '#';
    $replace  = "{\$post['user_details']}{\$post['awards']}";
	find_replace_templatesets("postbit", $find, $replace);
	find_replace_templatesets("postbit_classic", $find, $replace);
	find_replace_templatesets('member_profile', '#{\$profilefields}#', '{\$profilefields}
{\$memprofile[\'awards\']}');
}

function awards_deactivate(){
	global $db;
	$db->delete_query("templates","title IN('awards','awards_award','awards_award_empty','awards_nav','awards_memprofile','awards_postbit')");
	$db->delete_query("templategroups","prefix = 'awards'");

	require_once MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets('postbit', '#'.preg_quote('{$post[\'awards\']}').'#', '', 0);
	find_replace_templatesets('postbit_classic', '#'.preg_quote('{$post[\'awards\']}').'#', '', 0);
	find_replace_templatesets('member_profile', '#'.preg_quote('{$memprofile[\'awards\']}').'#', '', 0);
}

function awards_uninstall(){
	global $mybb, $db,$cache;
	$db->query("DELETE FROM ".TABLE_PREFIX."settinggroups WHERE name='awards'");
	$db->delete_query("settings","name LIKE 'awards_%'");
		
	if($db->field_exists("awards_award", "users"))  
		$db->drop_column("users", "awards_award");	
	if($db->field_exists("awards_threads", "users"))  
		$db->drop_column("users", "awards_threads");

	if($db->table_exists("awards_threads"))
		$db->drop_table("awards_threads");
	if($db->table_exists("awards_posts"))
		$db->drop_table("awards_posts");
	if($db->table_exists("awards_reputation"))
		$db->drop_table("awards_reputation");
	if($db->table_exists("awards_activity"))
		$db->drop_table("awards_activity");

	$db->delete_query('tasks', 'file=\'awards\'');
	rebuildsettings();
   	$cache->update_forums();	
}

function awards_load_templates()
{
	global $mybb, $session;
	if (isset($GLOBALS['templatelist']))
	{
		if ($mybb->settings['awards_enabled'] && THIS_SCRIPT == 'showthread.php')
		{
			$GLOBALS['templatelist'] .= ",awards_postbit";
		}
		elseif($mybb->settings['awards_enabled'] && $current_page == 'member.php')
		{
			$GLOBALS['templatelist'] .= ',awards_profile';
		}
	}
}

function awards_postbit(&$post)
{
	global $mybb, $lang, $theme, $db, $templates, $awards;
	
	if ($mybb->settings['awards_enabled'] == 0){
	return false;
	}
	
	if ($post[awards] == 'b:0;' || $post[awards] == '')
	{
		$post['awards'] = '<br />Sin logros...';
		return;
	}
	
	if ($mybb->settings['awards_max_postbit'] == '')
	{
		$post['awards'] = '';
		return;
	}
	
	$lang->load("awards");
	
	$post['awards'] = unserialize($post['awards']);
	
	if ($mybb->settings['awards_max_postbit'] > 0 && !empty($post['awards']))
	{
		static $awards_cache;
	
		if(!isset($awards_cache) || !is_array($awards_cache))
		{
			$idtypes = array('threads' => 'taid', 'posts' => 'paid', 'reputation' => 'raid', 'activity' => 'aaid');
			$types = array('threads', 'posts', 'reputation', 'activity');
		
			foreach ($types as $type)
			{
				switch ($type)
				{
					case 'threads':
						$order_by = array('order_by' => 'threads', 'order_dir' => 'desc');
					break;
				
					case 'posts':
						$order_by = array('order_by' => 'posts', 'order_dir' => 'desc');
					break;

					case 'reputation':
						$order_by = array('order_by' => 'reputation', 'order_dir' => 'desc');
					break;
			
					case 'activity':
						$order_by = array('order_by' => 'time', 'order_dir' => 'desc');
					break;
				}
				
				$query = $db->simple_select('awards_'.$type, '*', '', $order_by);
				while ($award = $db->fetch_array($query))
				{
					$awards_cache[$idtypes[$type]][$award[$idtypes[$type]]] = $award;
				}
			}
		}
	
		$awards = array();
		
		$new = false;
		
		if (!isset($post['awards']['taid']) && !isset($post['awards']['paid']) && !isset($post['awards']['raid']) && !isset($post['awards']['aaid']))
		{
			if (!empty($post['awards'][1]))
			{
				$post['awards'] = $post['awards'][1];
				$new = true;
			}
			elseif (!empty($post['awards'][0]))
			{
				$post['awards'] = $post['awards'][0];
				$new = false;
			}
		}
		else {
			$new = false;
		}
			
		if ($new === true)
		{
			$awards = '';
				$count = 1;
			foreach($post['awards'] as $award)
			{
				$award = explode('_', $award);
				// use the cache array
				$ach = $awards_cache[$award[0]][$award[1]];
				$awards .= "<img src=\"".htmlspecialchars_uni($ach['image'])."\" title=\"".htmlspecialchars_uni($ach['name'])."\" /> ";
					
				$count++;
					
				if ($count > $mybb->settings['awards_max_postbit'])
					break;
			}
		}
		else {  // Who the hell uses the old method these days?
		
			foreach ($post['awards'] as $ach_type => $award)
			{
				if (!empty($award))
				{
					foreach ($award as $ach)
					{
						$awards[$ach_type][] = $ach[$ach_type];
					}
				}
			}

			$achs_array = $awards;
			$awards = '';
			$count = 1;
			
			foreach($achs_array as $ach_type => $award)
			{
				foreach($award as $ach)
				{
					// use the cache array
					$ach = $awards_cache[$ach_type][$ach];
					$awards .= "<img src=\"".htmlspecialchars_uni($ach['image'])."\" title=\"".htmlspecialchars_uni($ach['name'])."\" style=\"max-width: 30px;max-height:30px;\" /> ";

					$count++;
					
					if ($count > $mybb->settings['awards_max_postbit'])
						break;
						//$limit = $mybb->settings['awards_max_postbit'] - 5;
							switch($count){
							     case 6 : $awards .= "<br />";break;
								 case 11 : $awards .= "<br />";break;
								 case 16 : $awards .= "<br />";break;
								 case 21 : $awards .= "<br />";break;
								 case 26 : $awards .= "<br />";break;
								 case 31 : $awards .= "<br />";break;
								 case 36 : $awards .= "<br />";break;
								 case 41 : $awards .= "<br />";break;
								 case 46 : $awards .= "<br />";break;
								 case 51 : $awards .= "<br />";break;								 
							}
				}
				
				if ($count > $mybb->settings['awards_max_postbit'])
					break;
			}
		}
	}
	
	if (empty($awards))
		return;
	
	eval("\$post['awards'] = \"".$templates->get("awards_postbit")."\";");
}

function awards_memprofile()
{
	global $mybb, $lang, $theme, $memprofile, $awards, $db, $templates;

	if ($mybb->settings['awards_enabled'] == 0){
	return false;
	}
	
	if ($memprofile[awards] == 'b:0;' || $memprofile[awards] == '')
	{
		$memprofile['awards'] = '<br />Sin logros...';
		return;
	}
	
	if ($mybb->settings['awards_max_profile'] == '')
	{
		$memprofile['awards'] = '';
		return;
	}
	
	$lang->load("awards");
	
	$memprofile['awards'] = unserialize($memprofile['awards']);
	
	if ($mybb->settings['awards_max_profile'] > 0 && !empty($memprofile['awards']))
	{
		static $awards_cache;
	
		if(!isset($awards_cache) || !is_array($awards_cache))
		{
			$idtypes = array('threads' => 'taid', 'posts' => 'paid', 'reputation' => 'raid', 'activity' => 'aaid');
			$types = array('threads', 'posts', 'reputation', 'activity');
		
			foreach ($types as $type)
			{
				switch ($type)
				{
					case 'threads':
						$order_by = array('order_by' => 'threads', 'order_dir' => 'desc');
					break;
				
					case 'posts':
						$order_by = array('order_by' => 'posts', 'order_dir' => 'desc');
					break;

					case 'reputation':
						$order_by = array('order_by' => 'reputation', 'order_dir' => 'desc');
					break;
			
					case 'activity':
						$order_by = array('order_by' => 'time', 'order_dir' => 'desc');
					break;
				}
				
				$query = $db->simple_select('awards_'.$type, '*', '', $order_by);
				while ($award = $db->fetch_array($query))
				{
					$awards_cache[$idtypes[$type]][$award[$idtypes[$type]]] = $award;
				}
			}
		}
	
		$awards = array();
		
		$new = false;
		
		if (!isset($memprofile['awards']['taid']) && !isset($memprofile['awards']['paid']) && !isset($memprofile['awards']['raid']) && !isset($memprofile['awards']['aaid']))
		{
			if (!empty($memprofile['awards'][1]))
			{
				$memprofile['awards'] = $memprofile['awards'][1];
				$new = true;
			}
			elseif (!empty($memprofile['awards'][0]))
			{
				$memprofile['awards'] = $memprofile['awards'][0];
				$new = false;
			}
		}
		else {
			$new = false;
		}
			
		if ($new === true)
		{
			$awards = '';
				$count = 1;
			foreach($memprofile['awards'] as $award)
			{
				$award = explode('_', $award);
				// use the cache array
				$ach = $awards_cache[$award[0]][$award[1]];
				$awards .= "<img src=\"".htmlspecialchars_uni($ach['image'])."\" title=\"".htmlspecialchars_uni($ach['name'])."\" /> ";
					
				$count++;
					
				if ($count > $mybb->settings['awards_max_postbit'])
					break;
			}
		}
		else {  // Who the hell uses the old method these days?
		
			foreach ($memprofile['awards'] as $ach_type => $award)
			{
				if (!empty($award))
				{
					foreach ($award as $ach)
					{
						$awards[$ach_type][] = $ach[$ach_type];
					}
				}
			}
			
			$achs_array = $awards;
			$awards = '';
			$count = 1;
			
			foreach($achs_array as $ach_type => $award)
			{
				foreach($award as $ach)
				{
					// use the cache array
					$ach = $awards_cache[$ach_type][$ach];
					$awards .= "<img src=\"".htmlspecialchars_uni($ach['image'])."\" title=\"".htmlspecialchars_uni($ach['name'])."\" /> ";

					$count++;
					
					if ($count > $mybb->settings['awards_max_profile'])
						break;
				}
				
				if ($count > $mybb->settings['awards_max_profile'])
					break;
			}
		}
	}
	
	if (empty($awards))
		return;
	
	eval("\$memprofile['awards'] = \"".$templates->get("awards_memprofile")."\";");
}

?>