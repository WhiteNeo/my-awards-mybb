<?php

/***************************************************************************
 *
 *  Awards (admin/modules/awards/activity.php)
 *  Author: Dark Neo
 *  Copyright: © 2014 DNT
 *  Website: http://darkneo.skn1.com
 */
 
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$lang->load('awards');

$page->add_breadcrumb_item($lang->activity_tab, 'index.php?module=awards-activity');

$page->output_header($lang->activity_tab);

$tabs["activity"] = array(
	'title' => $lang->activity_tab,
	'link' => "index.php?module=awards-activity",
	'description' => $lang->activity_tab_desc
);
$tabs["new"] = array(
	'title' => $lang->activity_new_award,
	'link' => "index.php?module=awards-activity&amp;award=new",
	'description' => $lang->activity_new_award_desc
);
if($mybb->input['award'] == "edit" && $mybb->input['aaid'] > 0){
$tabs["edit"] = array(
	'title' => $lang->activity_edit_award,
	'link' => "index.php?module=awards-activity&amp;award=edit&amp;aaid=".$mybb->input['aaid'],
	'description' => $lang->activity_edit_award_desc
);
}

$plugins->run_hooks_by_ref("admin_activity_awards_tabs", $tabs);

switch ($mybb->input['awards'])
{
	case 'activity':
		$page->output_nav_tabs($tabs, 'activity');
	break;
	case 'new':
		$page->output_nav_tabs($tabs, 'new');
	break;
	case 'edit':
		$page->output_nav_tabs($tabs, 'edit');
	break;
	default:
		$page->output_nav_tabs($tabs, 'activity');
}
if(!$mybb->input['award']) 
{
	$plugins->run_hooks("admin_activity_awards_start");

	if($mybb->settings['awards_enabled'] == 0){
	return false;
	}
    
	$query = $db->simple_select('awards_activity', 'COUNT(aaid) AS aaids', '', array('limit' => 1));
	$quantity = $db->fetch_field($query, "aaids");

	$pag = intval($mybb->input['page']);
	$perpage = 15;
	if($pag > 0)
	{
		$start = ($pag - 1) * $perpage;
		$pages = $quantity / $perpage;
		$pages = ceil($pages);
		if($pag > $pages || $pag <= 0)
		{
			$start = 0;
			$pag = 1;
		}
	}
	else
	{
		$start = 0;
		$pag = 1;
	}
	
	$pageurl = "index.php?module=awards-activity";
	$table = new Table;
	$table->construct_header($lang->title, array("width" => "30%"));
	$table->construct_header($lang->description, array("width" => "40%"));
	$table->construct_header($lang->activity, array("width" => "10%","class" => "align_center"));
	$table->construct_header($lang->image, array("width" => "10%","class" => "align_center"));
	$table->construct_header($lang->options, array("width" => "10%","class" => "align_center"));
	$table->construct_row();
	
	$query = $db->query('SELECT * FROM '.TABLE_PREFIX.'awards_activity ORDER BY time ASC LIMIT '.$start.', '.$perpage);
	while($award = $db->fetch_array($query))
	{
		if($award['years'] == 1)
		{
			$years = "1 ".$lang->year;
		}elseif($award['years'] > 1){
			$years = $award['years']." ".$lang->years;
		}else{
			$years = "";
		}
		if($award['months'] == 1)
		{
			if($years){
				$months = ", 1 ".$lang->month;
			}else{
				$months = "1 ".$lang->month;
			}
		}elseif($award['months'] > 1){
			if($years){
				$months = ", ".$award['months']." ".$lang->months;
			}else{
				$months = $award['months']." ".$lang->months;
			}
		}else{
			$months = "";
		}
		if($award['days'] == 1)
		{
			if($years || $months){
				$days = ", 1 ".$lang->day;
			}else{
				$days = "1 ".$lang->day;
			}
		}elseif($award['days'] > 1){
			if($years || $months)
			{
				$days = ", ".$award['days']." ".$lang->days;
			}else{
				$days = $award['days']." ".$lang->days;
			}
		}else{
			$days = "";
		}
		if($award['hours'] == 1)
		{	
			if($years || $months || $days)
			{
				$hours = ", 1 ".$lang->hour;
			}else{
				$hours = "1 ".$lang->hour;
			}
		}elseif($award['hours'] > 1){
			if($years || $months || $days)
			{
				$hours = ", ".$award['hours']." ".$lang->hours;
			}else{
				$hours = $award['hours']." ".$lang->hours;
			}
		}else{
			$hours = "";
		}

		$lang->confirmdeleteachivement = $lang->sprintf($lang->confirmdeleteachivement, $award['name']);
		$table->construct_cell($award[name]);
		$table->construct_cell($award[description]);
		$table->construct_cell($years.$months.$days.$hours,array("class" => "align_center"));
		$table->construct_cell("<img src='../{$award[image]}' >",array("class" => "align_center"));
		$popup = new PopupMenu("aaid_{$award['aaid']}", $lang->options);
		$popup->add_item($lang->edit, "index.php?module=awards-activity&amp;award=edit&amp;aaid=".$award['aaid']);
		$popup->add_item($lang->delete, "index.php?module=awards-activity&amp;award=delete&amp;aaid={$award['aaid']}&amp;my_post_key={$mybb->post_code}\" target=\"_self\" onclick=\"return AdminCP.deleteConfirmation(this, '{$lang->threads_award_delete_confirm}')");
		$Popuss = $popup->fetch();
		$table->construct_cell($Popuss, array('class' => 'align_center'));
		$table->construct_row();
	}

	if($table->num_rows() == 1)
	{
		$table->construct_cell($lang->emptyactivity_modules, array('colspan' => 7, 'class' => 'align_center'));
		$table->construct_row();
	}
	$table->output($lang->activity_modules);
	$plugins->run_hooks("admin_activity_awards_end");
	
	echo multipage($quantity, (int)$perpage, (int)$pag, $pageurl);
	
}

else if($mybb->input['award'] == "do_new" && $mybb->request_method == "post")
{
	if (empty($mybb->input['name']))
	{
		flash_message($lang->activity_award_name_empty, 'error');
		admin_redirect("index.php?module=awards-activity&amp;award=new&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;activity={$mybb->input['activity']}&amp;image={$mybb->input['image']}");
	}
	if (empty($mybb->input['description']))
	{
		flash_message($lang->activity_award_descrip_empty, 'error');
		admin_redirect("index.php?module=awards-activity&amp;award=new&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;activity={$mybb->input['activity']}&amp;image={$mybb->input['image']}");
	}
	$years = intval($mybb->input['years']);
	$months = intval($mybb->input['months']);
	$days = intval($mybb->input['days']);
	$hours = intval($mybb->input['hours']);
	$time = ($years * 31556952) + ($months * 2551443) + ($days * 86400) + ($hours * 3600);
	if($time <= 0)
	{
		flash_message($lang->activity_time_error, 'error');
		admin_redirect("index.php?module=awards-activity&amp;award=new&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;years={$mybb->input['years']}&amp;months={$mybb->input['months']}&amp;days={$mybb->input['days']}&amp;hours={$mybb->input['hours']}");
	}

	if(!$imagen['name'] || !$imagen['tmp_name'])
	{
		$imagen = $_FILES['image'];
	}
	if(!is_uploaded_file($imagen['tmp_name']))
	{
		flash_message($lang->activity_award_image_upload_fail, 'error');
		admin_redirect("index.php?module=awards-activity&amp;award=new&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;activity={$mybb->input['activity']}&amp;image={$mybb->input['image']}");
	}
	$ext = get_extension(my_strtolower($imagen['name']));
	if(!preg_match("#^(gif|jpg|jpeg|jpe|bmp|png)$#i", $ext)) 
	{
		flash_message($lang->activity_award_image_ext_error, 'error');
		admin_redirect("index.php?module=awards-activity&amp;award=new&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;activity={$mybb->input['activity']}&amp;image={$mybb->input['image']}");
	}
	$path = MYBB_ROOT."uploads/awards";
	$filename = "activity_".date('d_m_y_g_i_s').'.'.$ext; 
	$moved = @move_uploaded_file($imagen['tmp_name'], $path."/".$filename);
	if(!$moved)
	{
		flash_message($lang->activity_award_image_copy_fail, 'error');
		admin_redirect("index.php?module=awards-activity&amp;award=new&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;activity={$mybb->input['activity']}&amp;image={$mybb->input['image']}");
	}
	@my_chmod($path."/".$filename, '0644');
	if($imagen['error'])
	{
		@unlink($path."/".$filename);		
		flash_message($lang->activity_award_image_load_fail, 'error');
		admin_redirect("index.php?module=awards-activity&amp;award=edit&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;activity={$mybb->input['activity']}&amp;image={$mybb->input['image']}&amp;aaid={$mybb->input['aaid']}");
	}
	switch(my_strtolower($imagen['type']))
	{
		case "image/gif":
			$img_type =  1;
			break;
		case "image/jpeg":
		case "image/x-jpg":
		case "image/x-jpeg":
		case "image/pjpeg":
		case "image/jpg":
			$img_type = 2;
			break;
		case "image/png":
		case "image/x-png":
			$img_type = 3;
			break;
		default:
			$img_type = 0;
	}
	if($img_type == 0)
	{
		@unlink($path."/".$filename);
		flash_message($lang->activity_award_image_ext_error, 'error');
		admin_redirect("index.php?module=awards-activity&amp;award=edit&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;activity={$mybb->input['activity']}&amp;image={$mybb->input['image']}&amp;aaid={$mybb->input['aaid']}");
	}
	$update = array( 
		"aaid"  => $aaid,
		"name" => $mybb->input['name'],
		"description" => $mybb->input['description'],
		"years" => $years,
		"months" => $months,
		"days" => $days,
		"hours" => $hours,
		"time" => $time,
		"image" => "uploads/awards/".$filename
	); 
	$aaid = $db->insert_id();
	$plugins->run_hooks_by_ref("admin_activity_do_new_award_save", $update);
	$db->insert_query("awards_activity", $update);
	flash_message($lang->activity_award_success, 'success');
	admin_redirect("index.php?module=awards-activity");
}
elseif($mybb->input['award'] == "do_edit" && $mybb->request_method == "post")
{
	if (empty($mybb->input['name']))
	{
		flash_message($lang->activity_award_name_empty, 'error');
		admin_redirect("index.php?module=awards-activity&amp;award=edit&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;activity={$mybb->input['activity']}&amp;image={$mybb->input['image']}&amp;aaid={$mybb->input['aaid']}");
	}
	if (empty($mybb->input['description']))
	{
		flash_message($lang->activity_award_descrip_empty, 'error');
		admin_redirect("index.php?module=awards-activity&amp;award=edit&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;activity={$mybb->input['activity']}&amp;image={$mybb->input['image']}&amp;aaid={$mybb->input['aaid']}");
	}
	$years = intval($mybb->input['years']);
	$months = intval($mybb->input['months']);
	$days = intval($mybb->input['days']);
	$hours = intval($mybb->input['hours']);
	$time = ($years * 31556952) + ($months * 2551443) + ($days * 86400) + ($hours * 3600);
	if($time <= 0)
	{
		flash_message($lang->activity_time_error, 'error');
		admin_redirect("index.php?module=awards-activity&amp;award=new&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;years={$mybb->input['years']}&amp;months={$mybb->input['months']}&amp;days={$mybb->input['days']}&amp;hours={$mybb->input['hours']}");
	}
	$plugins->run_hooks("admin_activity_do_edit_award");
	if($_FILES['image']['error'] > 0)
	{
		$editupdate = array( 
			"name" => $mybb->input['name'],
			"description" => $mybb->input['description'],
			"years" => $years,
			"months" => $months,
			"days" => $days,
			"hours" => $hours,
			"time" => $time
		);

		$plugins->run_hooks_by_ref("admin_activity_do_edit_award_update",$editupdate);
		
		$db->update_query("awards_activity", $editupdate,"aaid=".$mybb->input['aaid']);
		$query = $db->simple_select('awards_activity', '*', 'aaid='.$mybb->input['aaid']);
		$award = $db->fetch_array($query);
		$lang->activity_award_edited = $lang->sprintf($lang->activity_award_edited, $award['name']);
		flash_message($lang->activity_award_edited, 'success');
		admin_redirect("index.php?module=awards-activity");
	}
	else
	{
		if(!$imagen['name'] || !$imagen['tmp_name'])
		{
			$imagen = $_FILES['image'];
		}
		if(!is_uploaded_file($imagen['tmp_name']))
		{
			flash_message($lang->activity_award_image_copy_fail, 'error');
			admin_redirect("index.php?module=awards-activity&amp;award=edit&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;activity={$mybb->input['activity']}&amp;image={$mybb->input['image']}&amp;aaid={$mybb->input['aaid']}");
		}
		$ext = get_extension(my_strtolower($imagen['name']));
		if(!preg_match("#^(gif|jpg|jpeg|jpe|bmp|png)$#i", $ext)) 
		{
			flash_message($lang->activity_award_image_ext_error, 'error');
			admin_redirect("index.php?module=awards-activity&amp;award=edit&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;activity={$mybb->input['activity']}&amp;image={$mybb->input['image']}&amp;aaid={$mybb->input['aaid']}");
		}
		$path = MYBB_ROOT."uploads/awards";
		$filename = "activity_".date('d_m_y_g_i_s').'.'.$ext; 
		$moved = @move_uploaded_file($imagen['tmp_name'], $path."/".$filename);
		if(!$moved)
		{
			flash_message($lang->activity_award_image_copy_fail, 'error');
			admin_redirect("index.php?module=awards-activity&amp;award=edit&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;activity={$mybb->input['activity']}&amp;image={$mybb->input['image']}&amp;aaid={$mybb->input['aaid']}");
		}
		@my_chmod($path."/".$filename, '0644');
		if($imagen['error'])
		{
			@unlink($path."/".$filename);		
			flash_message($lang->activity_award_image_load_fail, 'error');
			admin_redirect("index.php?module=awards-activity&amp;award=edit&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;activity={$mybb->input['activity']}&amp;image={$mybb->input['image']}&amp;aaid={$mybb->input['aaid']}");
		}
		switch(my_strtolower($imagen['type']))
		{
			case "image/gif":
				$img_type =  1;
				break;
			case "image/jpeg":
			case "image/x-jpg":
			case "image/x-jpeg":
			case "image/pjpeg":
			case "image/jpg":
				$img_type = 2;
				break;
			case "image/png":
			case "image/x-png":
				$img_type = 3;
				break;
			default:
				$img_type = 0;
		}
		if($img_type == 0)
		{
			@unlink($path."/".$filename);
			flash_message($lang->activity_award_image_ext_error, 'error');
			admin_redirect("index.php?module=awards-activity&amp;award=edit&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;activity={$mybb->input['activity']}&amp;image={$mybb->input['image']}&amp;aaid={$mybb->input['aaid']}");
		}
		$query = $db->simple_select('awards_activity', 'image', 'aaid='.$mybb->input['aaid']);
		$award = $db->fetch_array($query);
		@unlink(MYBB_ROOT.$award['image']);
		$editupdate = array( 
			"name" => $mybb->input['name'],
			"description" => $mybb->input['description'],
			"years" => $years,
			"months" => $months,
			"days" => $days,
			"hours" => $hours,
			"time" => $time,			
			"image" => "uploads/awards/".$filename
		);
		$db->update_query("awards_activity", $editupdate,"aaid=".$mybb->input['aaid']);
		flash_message($lang->activity_award_success, 'success');
		admin_redirect("index.php?module=awards-activity");
	}
}
elseif($mybb->input['award'] == "new")
{
	$plugins->run_hooks("admin_activity_new_award_start");
	$form = new Form("index.php?module=awards-activity&amp;award=do_new", "post", "save",1);
	$form_container = new FormContainer($lang->newachivementsbyactivity);
	$form_container->output_row($lang->activity_award_name,$lang->activity_award_name_desc, $form->generate_text_box('name',$mybb->input['name'], array('id' => 'name')), 'name');
	$form_container->output_row($lang->activity_award_descrip,$lang->activity_award_descrip_desc, $form->generate_text_area('description',$mybb->input['description'], array('id' => 'description')), 'description');
	$form_container->output_row($lang->activity_award_years,$lang->activity_award_years_desc, $form->generate_text_box('years',$mybb->input['years'], array('id' => 'years')), 'years');
	$form_container->output_row($lang->activity_award_months,$lang->activity_award_months_desc, $form->generate_text_box('months',$mybb->input['months'], array('id' => 'months')), 'months');
	$form_container->output_row($lang->activity_award_days,$lang->activity_award_days_desc, $form->generate_text_box('days',$mybb->input['days'], array('id' => 'days')), 'days');
	$form_container->output_row($lang->activity_award_hours,$lang->activity_award_hours_desc, $form->generate_text_box('hours',$mybb->input['hours'], array('id' => 'hours')), 'hours');
	$form_container->output_row($lang->activity_award_image,$lang->activity_award_image_desc, $form->generate_file_upload_box("image", array('style' => 'width: 310px;')), 'file');
	$form_container->end();

	$plugins->run_hooks("admin_activity_new_award_end");
	$buttons[] = $form->generate_submit_button($lang->activity_award_save);
	$form->output_submit_wrapper($buttons);
	$form->end();
}
elseif($mybb->input['award'] == "edit")
{
	if(!$mybb->input['aaid'])
	{
		flash_message($lang->activity_edit_none, 'error');
		admin_redirect("index.php?module=awards-activity");
	}
	$query = $db->simple_select('awards_activity', '*', 'aaid='.$mybb->input['aaid']);
	$awards = $db->fetch_array($query);
	$plugins->run_hooks("admin_activity_edit_award_start");
	
	$form = new Form("index.php?module=awards-activity&amp;award=do_edit", "post", "save",1);
	echo $form->generate_hidden_field("aaid", $awards[aaid]);
	$form_container = new FormContainer($lang->activity_award_new);
	$form_container->output_row($lang->activity_award_name,$lang->activity_award_name_desc, $form->generate_text_box('name',$awards['name'], array('id' => 'name')), 'name');
	$form_container->output_row($lang->activity_award_descrip,$lang->activity_award_descrip_desc, $form->generate_text_area('description',$awards['description'], array('id' => 'description')), 'description');
	$form_container->output_row($lang->activity_award_years,$lang->activity_award_years_desc, $form->generate_text_box('years',$awards['years'], array('id' => 'years')), 'years');
	$form_container->output_row($lang->activity_award_months,$lang->activity_award_months_desc, $form->generate_text_box('months',$awards['months'], array('id' => 'months')), 'months');
	$form_container->output_row($lang->activity_award_days,$lang->activity_award_days_desc, $form->generate_text_box('days',$awards['days'], array('id' => 'days')), 'days');
	$form_container->output_row($lang->activity_award_hours,$lang->activity_award_hours_desc, $form->generate_text_box('hours',$awards['hours'], array('id' => 'hours')), 'hours');
	$form_container->output_row($lang->activity_award_image_actual,$lang->activity_award_image_actual_desc, "<img src='../{$awards['image']}' />", 'usedimg');
	$form_container->output_row($lang->activity_award_image_new,$lang->activity_award_image_new_desc, $form->generate_file_upload_box("image", array('style' => 'width: 310px;')), 'file');
	$form_container->end();

	$plugins->run_hooks("admin_activity_edit_award_end");
	$buttons[] = $form->generate_submit_button($lang->activity_award_save);
	$form->output_submit_wrapper($buttons);
	$form->end();
}
elseif($mybb->input['award'] == "delete")
{
	$query = $db->simple_select("awards_activity", "*", "aaid=".$mybb->input['aaid']);
	$achivement = $db->fetch_array($query);
	$plugins->run_hooks("admin_activity_delete_award");
	@unlink(MYBB_ROOT.$awards['image']);
	$db->query("DELETE FROM ".TABLE_PREFIX."awards_activity WHERE aaid='".intval($mybb->input['aaid'])."'");
	flash_message($lang->activity_award_deleted, 'success');
	admin_redirect("index.php?module=awards-activity");
}

$page->output_footer();
exit;

?>
