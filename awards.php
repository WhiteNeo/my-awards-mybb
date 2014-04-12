<?php

define("IN_MYBB", 1);
define('THIS_SCRIPT', 'awards.php');

// Templates used  
$templatelist  = "awards,awards_award,awards_award_empty,awards_nav";

require_once "./global.php";

// lang load
$lang->load("awards");

$awards = '';

$plugins->run_hooks("awards_start");

	$idtypes = array('threads' => 'taid', 'posts' => 'paid', 'reputation' => 'raid', 'activity' => 'aaid');
	$types = array('threads', 'posts', 'reputation', 'activity');

if($mybb->input['uid'])
{

    if(!verify_post_check($mybb->input['my_post_key'])){
		error(not_permission);
	}
	
		$threads_awards = '';
		$posts_awards = '';
		$activity_awards = '';
		$reputation_awards = '';
		
		$uid = (int)$mybb->input['uid'];
		
		if ($uid)
		{
			$user = get_user($uid);
			$user['awards'] = unserialize($user['awards']);
			add_breadcrumb('Logros de '.$user['username']);
			$ids = array();
			
			if (!isset($user['awards']['taid']) && !isset($user['awards']['paid']) && !isset($user['awards']['raid']) && !isset($user['awards']['aaid']))
			{
				$user['awards'] = $user['awards'][0];
			}
			
			if (!empty($user['awards']))
			{
				foreach ($user['awards'] as $type => $ach_type)
				{
					if (!empty($ach_type))
					{
						foreach ($ach_type as $award)
						{
							$ids[$type][] = $award[$type];
						}
					}
				}
			}
			
			if (!empty($ids))
			{
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
					if ($ids[$idtypes[$type]] != '' || $ids[$idtypes[$type]] != 0)
					{					
					$awards_in = ' IN (\''.implode('\',\'', $ids[$idtypes[$type]]).'\')';
					}else
					{
					$awards_in = ' IN (\'-2\')';
					}
					$query = $db->simple_select('awards_'.$type, '*', $idtypes[$type].$awards_in, $order_by);
					while ($award = $db->fetch_array($query))
					{
						$alt_bg = alt_trow();
						
						$award['image'] = htmlspecialchars_uni($award['image']);
						$award['name'] = htmlspecialchars_uni($award['name']);
						$award['description'] = nl2br(htmlspecialchars_uni($award['description']));
						
						switch ($type)
						{
							case 'threads':
								eval("\$threads_awards .= \"".$templates->get("awards_award")."\";");
							break;
							
							case 'posts':
								eval("\$posts_awards .= \"".$templates->get("awards_award")."\";");
							break;

							case 'reputation':
								eval("\$reputation_awards .= \"".$templates->get("awards_award")."\";");
							break;
							
							case 'activity':
								eval("\$activity_awards .= \"".$templates->get("awards_award")."\";");
							break;
						}
					}
				}
			}
			
			if ($threads_awards == '')
			{
				$colspan = 3;
				eval("\$threads_awards.= \"".$templates->get("awards_award_empty")."\";");
			}
			if ($posts_awards == '')
			{
				$colspan = 3;
				eval("\$posts_awards.= \"".$templates->get("awards_award_empty")."\";");
			}
			if ($reputation_awards == '')
			{
				$colspan = 3;
				eval("\$reputation_awards.= \"".$templates->get("awards_award_empty")."\";");
			}			
			if ($activity_awards == '')
			{
				$colspan = 3;
				eval("\$activity_awards.= \"".$templates->get("awards_award_empty")."\";");
			}
			
			eval("\$awards = \"".$templates->get("awards")."\";");
		}
}
else {
	// query all types of achievements
			foreach ($types as $type)
			{
				switch ($type)
				{
					case 'threads':
						$order_by = array('order_by' => 'taid', 'order_dir' => 'desc');
					break;
					
					case 'posts':
						$order_by = array('order_by' => 'paid', 'order_dir' => 'desc');
					break;
						
					case 'reputation':
						$order_by = array('order_by' => 'raid', 'order_dir' => 'desc');
					break;
						
					case 'activity':
						$order_by = array('order_by' => 'aaid', 'order_dir' => 'desc');
					break;
						
				}

				$page = intval($mybb->input['page']);
				if($page < 1) $page = 1;
				$perpage = 10;
				$query = $db->simple_select('awards_'.$type, '*', 'name != ""', $order_by.'LIMIT '.(($page-1)*$perpage).', {$perpage}');
				$numann = $db->num_rows($query);
				$multipage = multipage($numann, $perpage, $page, $_SERVER['PHP_SELF']);
				
				while ($award = $db->fetch_array($query))
				{
					$alt_bg = alt_trow();
						
					$award['image'] = htmlspecialchars_uni($award['image']);
					$award['name'] = htmlspecialchars_uni($award['name']);
					$award['description'] = nl2br(htmlspecialchars_uni($award['description']));
						
					switch ($type)
					{
						case 'threads':
							eval("\$threads_awards .= \"".$templates->get("awards_award")."\";");
						break;
							
						case 'posts':
							eval("\$posts_awards .= \"".$templates->get("awards_award")."\";");
						break;

						case 'reputation':
							eval("\$reputation_awards .= \"".$templates->get("awards_award")."\";");
						break;
						
						case 'activity':
							eval("\$activity_awards .= \"".$templates->get("awards_award")."\";");
						break;
					}
				}
			}
	
		eval("\$awards_nav = \"".$templates->get("awards_nav")."\";");		
		eval("\$awards = \"".$templates->get("awards")."\";");

}

$plugins->run_hooks("awards_end");

output_page($awards);

run_shutdown();

exit;

?>