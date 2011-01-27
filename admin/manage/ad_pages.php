<?php

/*
#======================================================
|    Trellis Desk
|    =====================================
|    By DJ Tarazona (dj@accord5.com)
|    (c) 2010 ACCORD5
|    http://www.trellisdesk.com/
|    =====================================
|    Email: sales@accord5.com
#======================================================
|    @ Version: v1.0.4 Final Build 10440094
|    @ Version Int: 104.4.0.094
|    @ Version Num: 10440094
|    @ Build: 0094
#======================================================
|    | Admin Custom pages
#======================================================
*/

class ad_pages {

	#=======================================
	# @ Auto Run
	# Function that is run automatically
	# when the file is required.
	#=======================================

	function auto_run()
	{
		if ( ! $this->ifthd->member['acp']['manage_pages'] )
		{
			$this->ifthd->skin->error('no_perm');
		}
		
		$this->ifthd->skin->set_section( 'Knowledge Base / Custom Pages Control' );		
		$this->ifthd->skin->set_description( 'Manage your knowledge base, categories, articles and custom pages.' );

		switch( $this->ifthd->input['code'] )
    	{
    		case 'list':
				$this->list_pages();
    		break;
    		case 'add':
    			$this->add_page();
    		break;
    		case 'edit':
    			$this->edit_page();
    		break;

    		case 'doadd':
    			$this->do_add();
    		break;
    		case 'doedit':
    			$this->do_edit();
    		break;
    		case 'dodel':
    			$this->do_delete();
    		break;

    		default:
    			$this->list_pages();
    		break;
		}
	}

	#=======================================
	# @ List Pages
	# Show a list of pages.
	#=======================================

	function list_pages($error='', $alert='')
	{
		#=============================
		# Sorting Options
		#=============================

		if ( $this->ifthd->input['sort'] )
		{
			$sort = $this->ifthd->input['sort'];
		}
		else
		{
			$sort = 'id';
		}

		$order_var = "order_". $sort;
		$img_var = "img_". $sort;

		if ( $this->ifthd->input['order'] )
		{
			$order = strtoupper( $this->ifthd->input['order'] );
		}
		elseif ( $sort == 'id' )
		{
			$order = 'ASC';
		}

		if ( $order == 'DESC' )
		{
			$$order_var = "&amp;order=asc";
			$$img_var = "&nbsp;<img src='<! IMG_DIR !>/arrow_down.gif' alt='DOWN' />";
		}
		else
		{
			$$order_var = "&amp;order=desc";
			$$img_var = "&nbsp;<img src='<! IMG_DIR !>/arrow_up.gif' alt='UP' />";
		}

		$link_id = "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=pages&amp;code=list&amp;sort=id". $order_id ."'>ID". $img_id ."</a>";
		$link_name = "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=pages&amp;code=list&amp;sort=name". $order_name ."'>Name". $img_name ."</a>";

		#=============================
		# Grab Pages
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'pages',
							 				  	 'order'	=> array( $sort => $order ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$page_rows = ""; // Initialize for Security
		$row_count = 0; // Initialize for Security

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			while( $p = $this->ifthd->core->db->fetch_row() )
			{
				$row_count ++;
				
				( $row_count & 1 ) ? $row_class = 'option1-med' : $row_class = 'option2-med';
				
				#=============================
				# Fix Up Information
				#=============================

				$page_rows .= "<tr>
									<td class='{$row_class}'>{$p['id']}</td>
									<td class='{$row_class}'><a href='<! HD_URL !>/index.php?act=pages&amp;id={$p['id']}' target='_blank'>{$p['name']}</a></td>
									<td class='{$row_class}' style='font-weight: normal'>{$p['description']}</td>
									<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=pages&amp;code=edit&amp;id={$p['id']}'><img src='<! IMG_DIR !>/button_edit.gif' alt='Edit' /></a></td>
									<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=pages&amp;code=dodel&amp;id={$p['id']}' onclick='return sure_delete()'><img src='<! IMG_DIR !>/button_delete.gif' alt='Delete' /></a></td>
								</tr>";
			}
		}
		else
		{
			$page_rows .= "<tr>
								<td class='option1' colspan='5'>There are no pages to display.</td>
							</tr>";
		}

		#=============================
		# Do Output
		#=============================

		if ( $error )
		{
			$error = "<div class='critical'>{$error}</div>";
		}
		elseif ( $alert )
		{
			$error = "<div class='alert'>{$alert}</div>";
		}

		$this->output = "<script type='text/javascript'>

							function sure_delete()
							{
								if ( confirm('Are you sure you want to delete this page?') )
								{
									return true;
								}
								else
								{
									return false;
								}
							}

						</script>
						{$error}
						<div class='groupbox'>Custom Pages List</div>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<th width='5%' align='left'>{$link_id}</th>
							<th width='24%' align='left'>{$link_name}</th>
							<th width='57%' align='left'>Description</th>
							<th width='6%'>Edit</th>
							<th width='8%'>Delete</th>
						</tr>
						". $page_rows ."
						</table>
						<div class='formtail'><div class='fb_pad'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=pages&amp;code=add' class='fake_button'>Add A New Custom Page</a></div></div>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=pages'>Custom Pages</a>",
						   "List Custom Pages",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Custom Pages' ) );
	}

	#=======================================
	# @ Add Page
	# Show add page form.
	#=======================================

	function add_page($error="")
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_pages_add'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		#=============================
		# Do Output
		#=============================

		if ( $error )
		{
			$error = "<div class='critical'>{$error}</div>";
		}

		$this->output = "<script type='text/javascript'>

						function validate_form(form)
						{
							if ( ! form.name.value )
							{
								alert('Please enter a name.');
								form.title.focus();
								return false;
							}
						}

						</script>";



		if ( $this->ifthd->member['use_rte'] )
		{
			$this->output .= "<script language='javascript' type='text/javascript' src='<! HD_URL !>/includes/tinymce/tiny_mce.js'></script>
							<script language='javascript' type='text/javascript'>
							tinyMCE.init({
								mode : 'exact',
								theme : 'advanced',
								elements : 'contentb',
								plugins : 'inlinepopups,safari,spellchecker',
								dialog_type : 'modal',
								forced_root_block : false,
								force_br_newlines : true,
								force_p_newlines : false,
								theme_advanced_toolbar_location : 'top',
								theme_advanced_toolbar_align : 'left',
								theme_advanced_path_location : 'bottom',
								theme_advanced_disable : 'styleselect,formatselect',
								theme_advanced_buttons1 : 'bold,italic,underline,strikethrough,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,bullist,numlist,separator,outdent,indent,sub,sup,separator,link,unlink,image,separator,removeformat,cleanup,code',
								theme_advanced_buttons2 : 'cut,copy,paste,separator,undo,redo,separator,forecolor,backcolor,separator,spellchecker,separator,fontsizeselect',
								theme_advanced_buttons3 : '',
								theme_advanced_resize_horizontal : false,
								theme_advanced_resizing : true
							});
							</script>";
		}

		$this->output .= "{$error}
							<form action='<! HD_URL !>/admin.php?section=manage&amp;act=pages&amp;code=doadd' method='post' onsubmit='return validate_form(this)'>
							<div class='groupbox'>Add Custom Page</div>
							<table width='100%' cellpadding='0' cellspacing='0'>
							<tr>
								<td class='option1' width='19%'><label for='name'>Name</label></td>
								<td class='option1' width='81%'><input type='text' name='name' id='name' value='{$this->ifthd->input['name']}' size='35' /></td>
							</tr>
							<tr>
								<td class='option2' valign='top'><label for='description'>Description</label></td>
								<td class='option2'><textarea name='description' id='description' cols='50' rows='2'>{$this->ifthd->input['description']}</textarea></td>
							</tr>
							<tr>
								<td class='option1'>Use For Content</td>
								<td class='option1' style='font-weight: normal'>". $this->ifthd->skin->special_radio( 'type', 'Template File', 'RTE Content', $this->ifthd->input['type'] ) ."</td>
							</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info1','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Toggle information</a>
										<div id='info1' style='display: none;'>
										<div>
											<b>Template File</b> - The below template file will be used for the custom page content.<br />
											<b>RTE Content</b> - The below content in the rich text editor will be used for the custom page content.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
								<td class='option2'><label for='template'>Template Name</label></td>
								<td class='option2' style='font-weight: normal'><input type='text' name='template' id='template' value='{$this->ifthd->input['template']}' size='30' /> .tpl</td>
							</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info2','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Toggle information</a>
										<div id='info2' style='display: none;'>
										<div>
											This is the name of the template (.tpl) file that will be used for the content of the custom page if Template File is selected in the option above.  This file must be placed in the current skin directory.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
								<td class='option1' colspan='2'><textarea name='contentb' id='contentb' rows='15' cols='120' style='width: 98%; height: 350px;'>{$this->ifthd->input['contentb']}</textarea></td>
							</tr>
							</table>
							<div class='formtail'><input type='submit' name='submit' id='add' value='Add Custom Page' class='button' /></div>
							</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=pages'>Custom Pages</a>",
						   "Add Custom Page",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Custom Pages' ) );
	}

	#=======================================
	# @ Edit Page
	# Show edit page form.
	#=======================================

	function edit_page($error="")
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_pages_edit'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'pages',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_page');
		}

		$p = $this->ifthd->core->db->fetch_row();

		#=============================
		# Do Output
		#=============================

		if ( $error )
		{
			$error = "<div class='critical'>{$error}</div>";

			$name = $this->ifthd->input['name'];
			$description = $this->ifthd->input['description'];
			$content = $this->ifthd->input['content'];
			$type = $this->ifthd->input['type'];
			$template = $this->ifthd->input['template'];
		}
		else
		{
			$name = $p['name'];
			$description = $p['description'];
			$content = $p['content'];
			$type = $p['type'];
			$template = $p['template'];
		}

		$this->output = "<script type='text/javascript'>

						function validate_form(form)
						{
							if ( ! form.name.value )
							{
								alert('Please enter a name.');
								form.name.focus();
								return false;
							}
						}

						</script>";



		if ( $this->ifthd->member['use_rte'] )
		{
			$this->output .= "<script language='javascript' type='text/javascript' src='<! HD_URL !>/includes/tinymce/tiny_mce.js'></script>
							<script language='javascript' type='text/javascript'>
							tinyMCE.init({
								mode : 'exact',
								theme : 'advanced',
								elements : 'contentb',
								plugins : 'inlinepopups,safari,spellchecker',
								dialog_type : 'modal',
								forced_root_block : false,
								force_br_newlines : true,
								force_p_newlines : false,
								theme_advanced_toolbar_location : 'top',
								theme_advanced_toolbar_align : 'left',
								theme_advanced_path_location : 'bottom',
								theme_advanced_disable : 'styleselect,formatselect',
								theme_advanced_buttons1 : 'bold,italic,underline,strikethrough,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,bullist,numlist,separator,outdent,indent,sub,sup,separator,link,unlink,image,separator,removeformat,cleanup,code',
								theme_advanced_buttons2 : 'cut,copy,paste,separator,undo,redo,separator,forecolor,backcolor,separator,spellchecker,separator,fontsizeselect',
								theme_advanced_buttons3 : '',
								theme_advanced_resize_horizontal : false,
								theme_advanced_resizing : true
							});
							</script>";
		}

		$this->output .= "{$error}
							<form action='<! HD_URL !>/admin.php?section=manage&amp;act=pages&amp;code=doedit&amp;id={$p['id']}' method='post' onsubmit='return validate_form(this)'>
							<div class='groupbox'>Editing Page: {$p['name']}</div>
							<table width='100%' cellpadding='0' cellspacing='0'>
							<tr>
								<td class='option1' width='19%'><label for='name'>Name</label></td>
								<td class='option1' width='81%'><input type='text' name='name' id='name' value='{$name}' size='35' /></td>
							</tr>
							<tr>
								<td class='option2' valign='top'><label for='description'>Description</label></td>
								<td class='option2'><textarea name='description' id='description' cols='50' rows='2'>{$description}</textarea></td>
							</tr>
							<tr>
								<td class='option1'>Use For Content</td>
								<td class='option1' style='font-weight: normal'>". $this->ifthd->skin->special_radio( 'type', 'Template File', 'RTE Content', $type ) ."</td>
							</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info1','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Toggle information</a>
										<div id='info1' style='display: none;'>
										<div>
											<b>Template File</b> - The below template file will be used for the custom page content.<br />
											<b>RTE Content</b> - The below content in the rich text editor will be used for the custom page content.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
								<td class='option2'><label for='template'>Template Name</label></td>
								<td class='option2' style='font-weight: normal'><input type='text' name='template' id='template' value='{$template}' size='30' /> .tpl</td>
							</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info2','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Toggle information</a>
										<div id='info2' style='display: none;'>
										<div>
											This is the name of the template (.tpl) file that will be used for the content of the custom page if Template File is selected in the option above.  This file must be placed in the current skin directory.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
								<td class='option1' colspan='2'><textarea name='contentb' id='contentb' rows='15' cols='120' style='width: 98%; height: 350px;'>{$content}</textarea></td>
							</tr>
							</table>
							<div class='formtail'><input type='submit' name='submit' id='edit' value='Edit Page' class='button' /></div>
							</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=pages'>Custom Pages</a>",
						   "Edit Custom Page",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Custom Pages' ) );
	}

	#=======================================
	# @ Do Add
	# Create a new page.
	#=======================================

	function do_add()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['acp']['manage_pages_add'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		if ( ! $this->ifthd->input['name'] )
		{
			$this->add_page('Please enter a name.');
		}

		if ( $this->ifthd->input['type'] == 1 && ! $this->ifthd->input['template'] )
		{
			$this->add_page('Please enter a template name.');
		}
		
		$this->ifthd->input['contentb'] = $this->ifthd->remove_extra_lbs( $this->ifthd->input['contentb'] );

		#=============================
		# Add Page
		#=============================

		$db_array = array(
						  'name'		=> $this->ifthd->input['name'],
						  'description'	=> $this->ifthd->input['description'],
						  'template'	=> $this->ifthd->input['template'],
						  'content'		=> $this->ifthd->input['contentb'],
						  'type'		=> $this->ifthd->input['type'],
						  'date'		=> time(),
						  'ipadd'		=> $this->ifthd->input['ip_address'],
						 );

		$this->ifthd->core->db->construct( array(
											  	 'insert'	=> 'pages',
											  	 'set'		=> $db_array,
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$page_id = $this->ifthd->core->db->get_insert_id();

		$this->ifthd->log( 'admin', "Custom Page Added &#039;". $this->ifthd->input['name'] ."&#039;", 1, $page_id );

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=pages&code=list', 'add_page_success' );
		$this->list_pages( '', 'The custom page has been successfully added.' );
	}

	#=======================================
	# @ Do Edit
	# Edit an page.
	#=======================================

	function do_edit()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_pages_edit'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id' ),
											  	 'from'		=> 'pages',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_page');
		}

		if ( ! $this->ifthd->input['name'] )
		{
			$this->edit_page('Please enter a name.');
		}

		if ( $this->ifthd->input['type'] == 1 && ! $this->ifthd->input['template'] )
		{
			$this->edit_page('Please enter a template name.');
		}
		
		$this->ifthd->input['contentb'] = $this->ifthd->remove_extra_lbs( $this->ifthd->input['contentb'] );

		#=============================
		# Edit Page
		#=============================

		$db_array = array(
						  'name'		=> $this->ifthd->input['name'],
						  'description'	=> $this->ifthd->input['description'],
						  'template'	=> $this->ifthd->input['template'],
						  'content'		=> $this->ifthd->input['contentb'],
						  'type'		=> $this->ifthd->input['type'],
						 );

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'pages',
											  	 'set'		=> $db_array,
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'admin', "Custom Page Edited &#039;". $this->ifthd->input['name'] ."&#039;", 1, $this->ifthd->input['id'] );

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=pages&code=list', 'edit_page_success' );
		$this->list_pages( '', 'The custom page has been successfully updated.' );
	}

	#=======================================
	# @ Do Delete
	# Delete a page.
	#=======================================

	function do_delete()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_pages_delete'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'name' ),
											  	 'from'		=> 'pages',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_page');
		}

		$p = $this->ifthd->core->db->fetch_row();

		#=============================
		# Delete Page
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'delete'	=> 'pages',
							 				  	 'where'	=> array( 'id', '=', $p['id'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'admin', "Custom Page Deleted &#039;". $p['name'] ."&#039;", 2, $p['id'] );

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=pages&code=list', 'delete_page_success' );
		$this->list_pages( 'The custom page has been successfully deleted.' );
	}

}

?>