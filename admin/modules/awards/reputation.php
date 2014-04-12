<?php

/***************************************************************************
 *
 *  Awards (admin/modules/awards/reputation.php)
 *  Author: Dark Neo
 *  Copyright: © 2014 DNT
 *  Website: http://darkneo.skn1.com
 */
 
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$lang->load('awards');

$page->add_breadcrumb_item($lang->reputation_tab, 'index.php?module=awards-reputation');

$page->output_header($lang->reputation_tab);

$tabs["reputation"] = array(
	'title' => $lang->reputation_tab,
	'link' => "index.php?module=awards-reputation",
	'description' => $lang->reputation_tab_desc
);
$tabs["new"] = array(
	'title' => $lang->reputation_new_award,
	'link' => "index.php?module=awards-reputation&amp;award=new",
	'description' => $lang->reputation_new_award_desc
);
if($mybb->input['award'] == "edit" && $mybb->input['raid'] > 0){
$tabs["edit"] = array(
	'title' => $lang->reputation_edit_award,
	'link' => "index.php?module=awards-reputation&amp;award=edit&amp;raid=".$mybb->input['raid'],
	'description' => $lang->reputation_edit_award_desc
);
}

$plugins->run_hooks_by_ref("admin_reputation_awards_tabs", $tabs);

switch ($mybb->input['award'])
{
	case 'reputation':
		$page->output_nav_tabs($tabs, 'reputation');
	break;
	case 'new':
		$page->output_nav_tabs($tabs, 'new');
	break;
	case 'edit':
		$page->output_nav_tabs($tabs, 'edit');
	break;
	default:
		$page->output_nav_tabs($tabs, 'reputation');
}
if(!$mybb->input['award']) 
{
	$plugins->run_hooks("admin_reputation_awards_start");
	if($mybb->settings['awards_enabled'] == 0){
	return false;
	}
	$query = $db->simple_select('awards_reputation', 'COUNT(raid) AS raids', '', array('limit' => 1));
	$quantity = $db->fetch_field($query, "raids");

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
	
	$pageurl = "index.php?module=awards-reputation";
	$table = new Table;
	$table->construct_header($lang->title, array("width" => "30%"));
	$table->construct_header($lang->description, array("width" => "40%"));
	$table->construct_header($lang->reputation, array("width" => "10%","class" => "align_center"));
	$table->construct_header($lang->image, array("width" => "10%","class" => "align_center"));
	$table->construct_header($lang->options, array("width" => "10%","class" => "align_center"));
	$table->construct_row();
	
	$query = $db->query('SELECT * FROM '.TABLE_PREFIX.'awards_reputation ORDER BY reputation ASC LIMIT '.$start.', '.$perpage);
	while($award = $db->fetch_array($query))
	{
		$lang->confirmdeleteachivement = $lang->sprintf($lang->confirmdeleteachivement, $award['name']);
		$table->construct_cell($award[name]);
		$table->construct_cell($award[description]);
		$table->construct_cell($award[reputation],array("class" => "align_center"));
		$table->construct_cell("<img src='../{$award[image]}' >",array("class" => "align_center"));
		$popup = new PopupMenu("raid_{$award['raid']}", $lang->options);
		$popup->add_item($lang->edit, "index.php?module=awards-reputation&amp;award=edit&amp;raid=".$award['raid']);
		$popup->add_item($lang->delete, "index.php?module=awards-reputation&amp;award=delete&amp;raid={$award['raid']}&my_post_key={$mybb->post_code}\" target=\"_self\" onclick=\"return AdminCP.deleteConfirmation(this, '{$lang->threads_award_delete_confirm}')");
		$Popuss = $popup->fetch();
		$table->construct_cell($Popuss, array('class' => 'align_center'));
		$table->construct_row();
	}

	if($table->num_rows() == 1)
	{
		$table->construct_cell($lang->emptyreputation_modules, array('colspan' => 7, 'class' => 'align_center'));
		$table->construct_row();
	}
	$table->output($lang->reputation_modules);
	$plugins->run_hooks("admin_reputation_awards_end");
	
	echo multipage($quantity, (int)$perpage, (int)$pag, $pageurl);
	
}

else if($mybb->input['award'] == "do_new" && $mybb->request_method == "post")
{
	if (empty($mybb->input['name']))
	{
		flash_message($lang->reputation_award_name_empty, 'error');
		admin_redirect("index.php?module=awards-reputation&amp;award=new&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;reputation={$mybb->input['reputation']}&amp;image={$mybb->input['image']}");
	}
	if (empty($mybb->input['description']))
	{
		flash_message($lang->reputation_award_descrip_empty, 'error');
		admin_redirect("index.php?module=awards-reputation&amp;award=new&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;reputation={$mybb->input['reputation']}&amp;image={$mybb->input['image']}");
	}
	if (empty($mybb->input['reputation']))
	{
		flash_message($lang->reputation_award_quantity_empty, 'error');
		admin_redirect("index.php?module=awards-reputation&amp;award=new&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;reputation={$mybb->input['reputation']}&amp;image={$mybb->input['image']}");
	}
	if(!$imagen['name'] || !$imagen['tmp_name'])
	{
		$imagen = $_FILES['image'];
	}
	if(!is_uploaded_file($imagen['tmp_name']))
	{
		flash_message($lang->reputation_award_image_upload_fail, 'error');
		admin_redirect("index.php?module=awards-reputation&amp;award=new&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;reputation={$mybb->input['reputation']}&amp;image={$mybb->input['image']}");
	}
	$ext = get_extension(my_strtolower($imagen['name']));
	if(!preg_match("#^(gif|jpg|jpeg|jpe|bmp|png)$#i", $ext)) 
	{
		flash_message($lang->reputation_award_image_ext_error, 'error');
		admin_redirect("index.php?module=awards-reputation&amp;award=new&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;reputation={$mybb->input['reputation']}&amp;image={$mybb->input['image']}");
	}
	$path = MYBB_ROOT."uploads/awards";
	$filename = "reputation_".date('d_m_y_g_i_s').'.'.$ext; 
	$moved = @move_uploaded_file($imagen['tmp_name'], $path."/".$filename);
	if(!$moved)
	{
		flash_message($lang->reputation_award_image_copy_fail, 'error');
		admin_redirect("index.php?module=awards-reputation&amp;award=new&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;reputation={$mybb->input['reputation']}&amp;image={$mybb->input['image']}");
	}
	@my_chmod($path."/".$filename, '0644');
	if($imagen['error'])
	{
		@unlink($path."/".$filename);		
		flash_message($lang->reputation_award_image_load_fail, 'error');
		admin_redirect("index.php?module=awards-reputation&amp;award=edit&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;reputation={$mybb->input['reputation']}&amp;image={$mybb->input['image']}&amp;raid={$mybb->input['raid']}");
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
		flash_message($lang->reputation_award_image_ext_error, 'error');
		admin_redirect("index.php?module=awards-reputation&amp;award=edit&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;reputation={$mybb->input['reputation']}&amp;image={$mybb->input['image']}&amp;raid={$mybb->input['raid']}");
	}
	$update = array( 
		"raid"  => $raid,
		"name" => $mybb->input['name'],
		"description" => $mybb->input['description'],
		"reputation" => intval($mybb->input['reputation']),
		"image" => "uploads/awards/".$filename
	); 
	$raid = $db->insert_id();
	$plugins->run_hooks_by_ref("admin_reputation_do_new_award_save", $update);
	$db->insert_query("awards_reputation", $update);
	flash_message($lang->reputation_award_success, 'success');
	admin_redirect("index.php?module=awards-reputation");
}
elseif($mybb->input['award'] == "do_edit" && $mybb->request_method == "post")
{
	if (empty($mybb->input['name']))
	{
		flash_message($lang->reputation_award_name_empty, 'error');
		admin_redirect("index.php?module=awards-reputation&amp;award=edit&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;reputation={$mybb->input['reputation']}&amp;image={$mybb->input['image']}&amp;raid={$mybb->input['raid']}");
	}
	if (empty($mybb->input['description']))
	{
		flash_message($lang->reputation_award_descrip_empty, 'error');
		admin_redirect("index.php?module=awards-reputation&amp;award=edit&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;reputation={$mybb->input['reputation']}&amp;image={$mybb->input['image']}&amp;raid={$mybb->input['raid']}");
	}
	if (empty($mybb->input['reputation']))
	{
		flash_message($lang->reputation_award_quantity_empty, 'error');
		admin_redirect("index.php?module=awards-reputation&amp;award=edit&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;reputation={$mybb->input['reputation']}&amp;image={$mybb->input['image']}&amp;raid={$mybb->input['raid']}");
	}
	$plugins->run_hooks("admin_reputation_do_edit_award");
	if($_FILES['image']['error'] > 0)
	{
		$editupdate = array( 
			"name" => $mybb->input['name'],
			"description" => $mybb->input['description'],
			"reputation" => intval($mybb->input['reputation'])
		);

		$plugins->run_hooks_by_ref("admin_reputation_do_edit_award_update",$editupdate);
		
		$db->update_query("awards_reputation", $editupdate,"raid=".$mybb->input['raid']);
		$query = $db->simple_select('awards_reputation', '*', 'raid='.$mybb->input['raid']);
		$award = $db->fetch_array($query);
		$lang->reputation_award_edited = $lang->sprintf($lang->reputation_award_edited, $award['name']);
		flash_message($lang->reputation_award_edited, 'success');
		admin_redirect("index.php?module=awards-reputation");
	}
	else
	{
		if(!$imagen['name'] || !$imagen['tmp_name'])
		{
			$imagen = $_FILES['image'];
		}
		if(!is_uploaded_file($imagen['tmp_name']))
		{
			flash_message($lang->reputation_award_image_copy_fail, 'error');
			admin_redirect("index.php?module=awards-reputation&amp;award=edit&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;reputation={$mybb->input['reputation']}&amp;image={$mybb->input['image']}&amp;raid={$mybb->input['raid']}");
		}
		$ext = get_extension(my_strtolower($imagen['name']));
		if(!preg_match("#^(gif|jpg|jpeg|jpe|bmp|png)$#i", $ext)) 
		{
			flash_message($lang->reputation_award_image_ext_error, 'error');
			admin_redirect("index.php?module=awards-reputation&amp;award=edit&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;reputation={$mybb->input['reputation']}&amp;image={$mybb->input['image']}&amp;raid={$mybb->input['raid']}");
		}
		$path = MYBB_ROOT."uploads/awards";
		$filename = "reputation_".date('d_m_y_g_i_s').'.'.$ext; 
		$moved = @move_uploaded_file($imagen['tmp_name'], $path."/".$filename);
		if(!$moved)
		{
			flash_message($lang->reputation_award_image_copy_fail, 'error');
			admin_redirect("index.php?module=awards-reputation&amp;award=edit&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;reputation={$mybb->input['reputation']}&amp;image={$mybb->input['image']}&amp;raid={$mybb->input['raid']}");
		}
		@my_chmod($path."/".$filename, '0644');
		if($imagen['error'])
		{
			@unlink($path."/".$filename);		
			flash_message($lang->reputation_award_image_load_fail, 'error');
			admin_redirect("index.php?module=awards-reputation&amp;award=edit&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;reputation={$mybb->input['reputation']}&amp;image={$mybb->input['image']}&amp;raid={$mybb->input['raid']}");
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
			flash_message($lang->reputation_award_image_ext_error, 'error');
			admin_redirect("index.php?module=awards-reputation&amp;award=edit&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;reputation={$mybb->input['reputation']}&amp;image={$mybb->input['image']}&amp;raid={$mybb->input['raid']}");
		}
		$query = $db->simple_select('awards_reputation', 'image', 'raid='.$mybb->input['raid']);
		$award = $db->fetch_array($query);
		@unlink(MYBB_ROOT.$award['image']);
		$editupdate = array( 
			"name" => $mybb->input['name'],
			"description" => $mybb->input['description'],
			"reputation" => intval($mybb->input['reputation']),
			"image" => "uploads/awards/".$filename
		);
		$db->update_query("awards_reputation", $editupdate,"raid=".$mybb->input['raid']);
		flash_message($lang->reputation_award_success, 'success');
		admin_redirect("index.php?module=awards-reputation");
	}
}
elseif($mybb->input['award'] == "new")
{
	$plugins->run_hooks("admin_reputation_new_award_start");
	$form = new Form("index.php?module=awards-reputation&amp;award=do_new", "post", "save",1);
	$form_container = new FormContainer($lang->newachivementsbyreputation);
	$form_container->output_row($lang->reputation_award_name,$lang->reputation_award_name_desc, $form->generate_text_box('name',$mybb->input['name'], array('id' => 'name')), 'name');
	$form_container->output_row($lang->reputation_award_descrip,$lang->reputation_award_descrip_desc, $form->generate_text_area('description',$mybb->input['description'], array('id' => 'description')), 'description');
	$form_container->output_row($lang->reputation_award_quantity,$lang->reputation_award_quantity_desc, $form->generate_text_box('reputation',$mybb->input['reputation'], array('id' => 'reputation')), 'reputation');
	$form_container->output_row($lang->reputation_award_image,$lang->reputation_award_image_desc, $form->generate_file_upload_box("image", array('style' => 'width: 310px;')), 'file');
	$form_container->end();

	$plugins->run_hooks("admin_reputation_new_award_end");
	$buttons[] = $form->generate_submit_button($lang->reputation_award_save);
	$form->output_submit_wrapper($buttons);
	$form->end();
}
elseif($mybb->input['award'] == "edit")
{
	if(!$mybb->input['raid'])
	{
		flash_message($lang->reputation_edit_none, 'error');
		admin_redirect("index.php?module=awards-reputation");
	}
	$query = $db->simple_select('awards_reputation', '*', 'raid='.$mybb->input['raid']);
	$awards = $db->fetch_array($query);
	$plugins->run_hooks("admin_reputation_edit_award_start");
	
	$form = new Form("index.php?module=awards-reputation&amp;award=do_edit", "post", "save",1);
	echo $form->generate_hidden_field("raid", $achivement[raid]);
	$form_container = new FormContainer($lang->reputation_award_new);
	$form_container->output_row($lang->reputation_award_name,$lang->reputation_award_name_desc, $form->generate_text_box('name',$awards['name'], array('id' => 'name')), 'name');
	$form_container->output_row($lang->reputation_award_descrip,$lang->reputation_award_descrip_desc, $form->generate_text_area('description',$awards['description'], array('id' => 'description')), 'description');
	$form_container->output_row($lang->reputation_award_quantity,$lang->reputation_award_quantity_desc, $form->generate_text_box('reputation',$awards['reputation'], array('id' => 'reputation')), 'reputation');
	$form_container->output_row($lang->reputation_award_image_actual,$lang->reputation_award_image_actual_desc, "<img src='../{$awards['image']}' />", 'usedimg');
	$form_container->output_row($lang->reputation_award_image_new,$lang->reputation_award_image_new_desc, $form->generate_file_upload_box("image", array('style' => 'width: 310px;')), 'file');
	$form_container->end();

	$plugins->run_hooks("admin_reputation_edit_award_end");
	$buttons[] = $form->generate_submit_button($lang->reputation_award_save);
	$form->output_submit_wrapper($buttons);
	$form->end();
}
elseif($mybb->input['award'] == "delete")
{
	$query = $db->simple_select("awards_reputation", "*", "raid=".$mybb->input['raid']);
	$achivement = $db->fetch_array($query);
	$plugins->run_hooks("admin_reputation_delete_award");
	@unlink(MYBB_ROOT.$achivement['image']);
	$db->query("DELETE FROM ".TABLE_PREFIX."awards_reputation WHERE raid='".intval($mybb->input['raid'])."'");
	flash_message($lang->reputation_award_deleted, 'success');
	admin_redirect("index.php?module=awards-reputation");
}

$page->output_footer();
exit;

?>