<?php

/***************************************************************************
 *
 *  Awards (admin/modules/awards/posts.php)
 *  Author: Dark Neo
 *  Copyright: © 2014 DNT
 *  Website: http://darkneo.skn1.com
 */
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$lang->load('awards');

$page->add_breadcrumb_item($lang->posts_tab, 'index.php?module=awards-posts');

$page->output_header($lang->posts_tab);

$tabs["posts"] = array(
	'title' => $lang->posts_tab,
	'link' => "index.php?module=awards-posts",
	'description' => $lang->posts_tab_desc
);
$tabs["new"] = array(
	'title' => $lang->posts_new_award,
	'link' => "index.php?module=awards-posts&amp;award=new",
	'description' => $lang->posts_new_award_desc
);
if($mybb->input['award'] == "edit" && $mybb->input['paid'] > 0){
$tabs["edit"] = array(
	'title' => $lang->posts_edit_award,
	'link' => "index.php?module=awards-posts&amp;award=edit&amp;paid=".$mybb->input['paid'],
	'description' => $lang->posts_edit_award_desc
);
}

$plugins->run_hooks_by_ref("admin_posts_awards_tabs", $tabs);

switch ($mybb->input['award'])
{
	case 'posts':
		$page->output_nav_tabs($tabs, 'posts');
	break;
	case 'new':
		$page->output_nav_tabs($tabs, 'new');
	break;
	case 'edit':
		$page->output_nav_tabs($tabs, 'edit');
	break;
	default:
		$page->output_nav_tabs($tabs, 'posts');
}
if(!$mybb->input['award']) 
{
	$plugins->run_hooks("admin_posts_awards_start");
	if($mybb->settings['awards_enabled'] == 0){
	return false;
	}
	$query = $db->simple_select('awards_posts', 'COUNT(paid) AS paids', '', array('limit' => 1));
	$quantity = $db->fetch_field($query, "paids");

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
	
	$pageurl = "index.php?module=awards-posts";
	$table = new Table;
	$table->construct_header($lang->title, array("width" => "30%"));
	$table->construct_header($lang->description, array("width" => "40%"));
	$table->construct_header($lang->posts, array("width" => "10%","class" => "align_center"));
	$table->construct_header($lang->image, array("width" => "10%","class" => "align_center"));
	$table->construct_header($lang->options, array("width" => "10%","class" => "align_center"));
	$table->construct_row();
	
	$query = $db->query('SELECT * FROM '.TABLE_PREFIX.'awards_posts ORDER BY posts ASC LIMIT '.$start.', '.$perpage);
	while($award = $db->fetch_array($query))
	{
		$lang->confirmdeleteachivement = $lang->sprintf($lang->confirmdeleteachivement, $award['name']);
		$table->construct_cell($award[name]);
		$table->construct_cell($award[description]);
		$table->construct_cell($award[posts],array("class" => "align_center"));
		$table->construct_cell("<img src='../{$award[image]}' >",array("class" => "align_center"));
		$popup = new PopupMenu("paid_{$award['paid']}", $lang->options);
		$popup->add_item($lang->edit, "index.php?module=awards-posts&amp;award=edit&amp;paid=".$award['paid']);
		$popup->add_item($lang->delete, "index.php?module=awards-posts&amp;award=delete&amp;paid={$award['paid']}&my_post_key={$mybb->post_code}\" target=\"_self\" onclick=\"return AdminCP.deleteConfirmation(this, '{$lang->threads_award_delete_confirm}')");
		$Popuss = $popup->fetch();
		$table->construct_cell($Popuss, array('class' => 'align_center'));
		$table->construct_row();
	}

	if($table->num_rows() == 1)
	{
		$table->construct_cell($lang->emptyposts_modules, array('colspan' => 7, 'class' => 'align_center'));
		$table->construct_row();
	}
	$table->output($lang->posts_modules);
	$plugins->run_hooks("admin_posts_awards_end");
	
	echo multipage($quantity, (int)$perpage, (int)$pag, $pageurl);
	
}

else if($mybb->input['award'] == "do_new" && $mybb->request_method == "post")
{
	if (empty($mybb->input['name']))
	{
		flash_message($lang->posts_award_name_empty, 'error');
		admin_redirect("index.php?module=awards-posts&amp;award=new&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;posts={$mybb->input['posts']}&amp;image={$mybb->input['image']}");
	}
	if (empty($mybb->input['description']))
	{
		flash_message($lang->posts_award_descrip_empty, 'error');
		admin_redirect("index.php?module=awards-posts&amp;award=new&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;posts={$mybb->input['posts']}&amp;image={$mybb->input['image']}");
	}
	if (empty($mybb->input['posts']))
	{
		flash_message($lang->posts_award_quantity_empty, 'error');
		admin_redirect("index.php?module=awards-posts&amp;award=new&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;posts={$mybb->input['posts']}&amp;image={$mybb->input['image']}");
	}
	if(!$imagen['name'] || !$imagen['tmp_name'])
	{
		$imagen = $_FILES['image'];
	}
	if(!is_uploaded_file($imagen['tmp_name']))
	{
		flash_message($lang->posts_award_image_upload_fail, 'error');
		admin_redirect("index.php?module=awards-posts&amp;award=new&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;posts={$mybb->input['posts']}&amp;image={$mybb->input['image']}");
	}
	$ext = get_extension(my_strtolower($imagen['name']));
	if(!preg_match("#^(gif|jpg|jpeg|jpe|bmp|png)$#i", $ext)) 
	{
		flash_message($lang->posts_award_image_ext_error, 'error');
		admin_redirect("index.php?module=awards-posts&amp;award=new&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;posts={$mybb->input['posts']}&amp;image={$mybb->input['image']}");
	}
	$path = MYBB_ROOT."uploads/awards";
	$filename = "posts_".date('d_m_y_g_i_s').'.'.$ext; 
	$moved = @move_uploaded_file($imagen['tmp_name'], $path."/".$filename);
	if(!$moved)
	{
		flash_message($lang->posts_award_image_copy_fail, 'error');
		admin_redirect("index.php?module=awards-posts&amp;award=new&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;posts={$mybb->input['posts']}&amp;image={$mybb->input['image']}");
	}
	@my_chmod($path."/".$filename, '0644');
	if($imagen['error'])
	{
		@unlink($path."/".$filename);		
		flash_message($lang->posts_award_image_load_fail, 'error');
		admin_redirect("index.php?module=awards-posts&amp;award=edit&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;posts={$mybb->input['posts']}&amp;image={$mybb->input['image']}&amp;paid={$mybb->input['paid']}");
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
		flash_message($lang->posts_award_image_ext_error, 'error');
		admin_redirect("index.php?module=awards-posts&amp;award=edit&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;posts={$mybb->input['posts']}&amp;image={$mybb->input['image']}&amp;paid={$mybb->input['paid']}");
	}
	$update = array( 
		"paid"  => $paid,
		"name" => $mybb->input['name'],
		"description" => $mybb->input['description'],
		"posts" => intval($mybb->input['posts']),
		"image" => "uploads/awards/".$filename
	); 
	$paid = $db->insert_id();
	$plugins->run_hooks_by_ref("admin_posts_do_new_award_save", $update);
	$db->insert_query("awards_posts", $update);
	flash_message($lang->posts_award_success, 'success');
	admin_redirect("index.php?module=awards-posts");
}
elseif($mybb->input['award'] == "do_edit" && $mybb->request_method == "post")
{
	if (empty($mybb->input['name']))
	{
		flash_message($lang->posts_award_name_empty, 'error');
		admin_redirect("index.php?module=awards-posts&amp;award=edit&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;posts={$mybb->input['posts']}&amp;image={$mybb->input['image']}&amp;paid={$mybb->input['paid']}");
	}
	if (empty($mybb->input['description']))
	{
		flash_message($lang->posts_award_descrip_empty, 'error');
		admin_redirect("index.php?module=awards-posts&amp;award=edit&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;posts={$mybb->input['posts']}&amp;image={$mybb->input['image']}&amp;paid={$mybb->input['paid']}");
	}
	if (empty($mybb->input['posts']))
	{
		flash_message($lang->posts_award_quantity_empty, 'error');
		admin_redirect("index.php?module=awards-posts&amp;award=edit&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;posts={$mybb->input['posts']}&amp;image={$mybb->input['image']}&amp;paid={$mybb->input['paid']}");
	}
	$plugins->run_hooks("admin_posts_do_edit_award");
	if($_FILES['image']['error'] > 0)
	{
		$editupdate = array( 
			"name" => $mybb->input['name'],
			"description" => $mybb->input['description'],
			"posts" => intval($mybb->input['posts'])
		);

		$plugins->run_hooks_by_ref("admin_posts_do_edit_award_update",$editupdate);
		
		$db->update_query("awards_posts", $editupdate,"paid=".$mybb->input['paid']);
		$query = $db->simple_select('awards_posts', '*', 'paid='.$mybb->input['paid']);
		$award = $db->fetch_array($query);
		$lang->posts_award_edited = $lang->sprintf($lang->posts_award_edited, $award['name']);
		flash_message($lang->posts_award_edited, 'success');
		admin_redirect("index.php?module=awards-posts");
	}
	else
	{
		if(!$imagen['name'] || !$imagen['tmp_name'])
		{
			$imagen = $_FILES['image'];
		}
		if(!is_uploaded_file($imagen['tmp_name']))
		{
			flash_message($lang->posts_award_image_copy_fail, 'error');
			admin_redirect("index.php?module=awards-posts&amp;award=edit&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;posts={$mybb->input['posts']}&amp;image={$mybb->input['image']}&amp;paid={$mybb->input['paid']}");
		}
		$ext = get_extension(my_strtolower($imagen['name']));
		if(!preg_match("#^(gif|jpg|jpeg|jpe|bmp|png)$#i", $ext)) 
		{
			flash_message($lang->posts_award_image_ext_error, 'error');
			admin_redirect("index.php?module=awards-posts&amp;award=edit&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;posts={$mybb->input['posts']}&amp;image={$mybb->input['image']}&amp;paid={$mybb->input['paid']}");
		}
		$path = MYBB_ROOT."uploads/awards";
		$filename = "posts_".date('d_m_y_g_i_s').'.'.$ext; 
		$moved = @move_uploaded_file($imagen['tmp_name'], $path."/".$filename);
		if(!$moved)
		{
			flash_message($lang->posts_award_image_copy_fail, 'error');
			admin_redirect("index.php?module=awards-posts&amp;award=edit&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;posts={$mybb->input['posts']}&amp;image={$mybb->input['image']}&amp;paid={$mybb->input['paid']}");
		}
		@my_chmod($path."/".$filename, '0644');
		if($imagen['error'])
		{
			@unlink($path."/".$filename);		
			flash_message($lang->posts_award_image_load_fail, 'error');
			admin_redirect("index.php?module=awards-posts&amp;award=edit&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;posts={$mybb->input['posts']}&amp;image={$mybb->input['image']}&amp;paid={$mybb->input['paid']}");
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
			flash_message($lang->posts_award_image_ext_error, 'error');
			admin_redirect("index.php?module=awards-posts&amp;award=edit&amp;name={$mybb->input['name']}&amp;description={$mybb->input['description']}&amp;posts={$mybb->input['posts']}&amp;image={$mybb->input['image']}&amp;paid={$mybb->input['paid']}");
		}
		$query = $db->simple_select('awards_posts', 'image', 'paid='.$mybb->input['paid']);
		$award = $db->fetch_array($query);
		@unlink(MYBB_ROOT.$award['image']);
		$editupdate = array( 
			"name" => $mybb->input['name'],
			"description" => $mybb->input['description'],
			"posts" => intval($mybb->input['posts']),
			"image" => "uploads/awards/".$filename
		);
		$db->update_query("awards_posts", $editupdate,"paid=".$mybb->input['paid']);
		flash_message($lang->posts_award_success, 'success');
		admin_redirect("index.php?module=awards-posts");
	}
}
elseif($mybb->input['award'] == "new")
{
	$plugins->run_hooks("admin_posts_new_award_start");
	$form = new Form("index.php?module=awards-posts&amp;award=do_new", "post", "save",1);
	$form_container = new FormContainer($lang->newachivementsbyposts);
	$form_container->output_row($lang->posts_award_name,$lang->posts_award_name_desc, $form->generate_text_box('name',$mybb->input['name'], array('id' => 'name')), 'name');
	$form_container->output_row($lang->posts_award_descrip,$lang->posts_award_descrip_desc, $form->generate_text_area('description',$mybb->input['description'], array('id' => 'description')), 'description');
	$form_container->output_row($lang->posts_award_quantity,$lang->posts_award_quantity_desc, $form->generate_text_box('posts',$mybb->input['posts'], array('id' => 'posts')), 'posts');
	$form_container->output_row($lang->posts_award_image,$lang->posts_award_image_desc, $form->generate_file_upload_box("image", array('style' => 'width: 310px;')), 'file');
	$form_container->end();

	$plugins->run_hooks("admin_posts_new_award_end");
	$buttons[] = $form->generate_submit_button($lang->posts_award_save);
	$form->output_submit_wrapper($buttons);
	$form->end();
}
elseif($mybb->input['award'] == "edit")
{
	if(!$mybb->input['paid'])
	{
		flash_message($lang->posts_edit_none, 'error');
		admin_redirect("index.php?module=awards-posts");
	}
	$query = $db->simple_select('awards_posts', '*', 'paid='.$mybb->input['paid']);
	$awards = $db->fetch_array($query);
	$plugins->run_hooks("admin_posts_edit_award_start");
	
	$form = new Form("index.php?module=awards-posts&amp;award=do_edit", "post", "save",1);
	echo $form->generate_hidden_field("paid", $achivement[paid]);
	$form_container = new FormContainer($lang->posts_award_new);
	$form_container->output_row($lang->posts_award_name,$lang->posts_award_name_desc, $form->generate_text_box('name',$awards['name'], array('id' => 'name')), 'name');
	$form_container->output_row($lang->posts_award_descrip,$lang->posts_award_descrip_desc, $form->generate_text_area('description',$awards['description'], array('id' => 'description')), 'description');
	$form_container->output_row($lang->posts_award_quantity,$lang->posts_award_quantity_desc, $form->generate_text_box('posts',$awards['posts'], array('id' => 'posts')), 'posts');
	$form_container->output_row($lang->posts_award_image_actual,$lang->posts_award_image_actual_desc, "<img src='../{$awards['image']}' />", 'usedimg');
	$form_container->output_row($lang->posts_award_image_new,$lang->posts_award_image_new_desc, $form->generate_file_upload_box("image", array('style' => 'width: 310px;')), 'file');
	$form_container->end();

	$plugins->run_hooks("admin_posts_edit_award_end");
	$buttons[] = $form->generate_submit_button($lang->posts_award_save);
	$form->output_submit_wrapper($buttons);
	$form->end();
}
elseif($mybb->input['award'] == "delete")
{
	$query = $db->simple_select("awards_posts", "*", "paid=".$mybb->input['paid']);
	$achivement = $db->fetch_array($query);
	$plugins->run_hooks("admin_posts_delete_award");
	@unlink(MYBB_ROOT.$achivement['image']);
	$db->query("DELETE FROM ".TABLE_PREFIX."awards_posts WHERE paid='".intval($mybb->input['paid'])."'");
	flash_message($lang->posts_award_deleted, 'success');
	admin_redirect("index.php?module=awards-posts");
}

$page->output_footer();
exit;

?>