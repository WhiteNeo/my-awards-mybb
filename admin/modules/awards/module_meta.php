<?php
/***************************************************************************
 *
 *  Awards (admin/modules/awards/module_meta.php)
 *  Author: Dark Neo
 *  Copyright: © 2014 DNT
 *  Website: http://darkneo.skn1.com
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

function awards_meta()
{
	global $mybb, $page, $lang, $plugins;

    if($mybb->settings['awards_enabled'] == 1){
	$sub_menu = array();
	$sub_menu['10'] = array("id" => "threads", "title" => $lang->awards_threads, "link" => "index.php?module=awards-threads");
	$sub_menu['20'] = array("id" => "posts", "title" => $lang->awards_posts, "link" => "index.php?module=awards-posts");
	$sub_menu['30'] = array("id" => "reputation", "title" => $lang->awards_reputation, "link" => "index.php?module=awards-reputation");
	$sub_menu['40'] = array("id" => "activity", "title" => $lang->awards_activity, "link" => "index.php?module=awards-activity");
	$sub_menu['50'] = array("id" => "activity", "title" => $lang->awards_configure, "link" => "index.php?module=config&action=change&search=awards");

	$sub_menu = $plugins->run_hooks("admin_awards_menu", $sub_menu);

	$page->add_menu_item($lang->awards, "awards", "index.php?module=awards", 60, $sub_menu);
	return true;

}
else{
	  return false;
    }
}

function awards_action_handler($action)
{
	global $page, $lang, $plugins;
	
	$page->active_module = "awards";
	
	$actions = array(
		'threads' => array('active' => 'threads', 'file' => 'threads.php'),
		'posts' => array('active' => 'posts', 'file' => 'posts.php'),
		'reputation' => array('active' => 'reputation', 'file' => 'reputation.php'),
		'activity' => array('active' => 'activity', 'file' => 'activity.php'),
	);

	$actions = $plugins->run_hooks("admin_awards_action_handler", $actions);
	
	if(isset($actions[$action]))
	{
		$page->active_action = $actions[$action]['active'];
		return $actions[$action]['file'];
	}
	else
	{
		$page->active_action = "threads";
		return "threads.php";
	}
}

function awards_admin_permissions()
{
	global $lang, $plugins;
	
	$admin_permissions = array(
		"threads"	=> $lang->permissions_awards_threads,
		"posts"	=> $lang->permissions_awards_posts,
		"reputation"	=> $lang->permissions_awards_reputation,
		"activity"	=> $lang->permissions_awards_activity,
	);
	
	$plugins->run_hooks_by_ref("admin_awards_permissions", $admin_permissions);
	
	require_once MYBB_ROOT."inc/plugins/awards.php";
	$awards = awards_is_installed();
	if($awards)
	{
		return array("name" => "awards", "permissions" => $admin_permissions, "disporder" => 70);
	}
}
?>
