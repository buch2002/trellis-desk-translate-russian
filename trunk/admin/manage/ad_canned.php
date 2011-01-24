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
|    | Admin Canned Replies
#======================================================
*/

class ad_canned {

	#=======================================
	# @ Auto Run
	# Function that is run automatically
	# when the file is required.
	#=======================================

	function auto_run()
	{
		if ( ! $this->ifthd->member['acp']['manage_canned'] )
		{
			$this->ifthd->skin->error('no_perm');
		}
		
		$this->ifthd->skin->set_section( 'Ticket Control' );		
		$this->ifthd->skin->set_description( 'Manage your tickets,  departments, custom department fields and canned replies.' );

		switch( $this->ifthd->input['code'] )
	    {
	    	case 'list':
				$this->list_canned();
	    	break;
	    	case 'add':
	    		$this->add_canned();
	    	break;
	    	case 'edit':
	    		$this->edit_canned();
	    	break;
	    	case 'get':
	    		$this->get_canned();
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
	    		$this->list_canned();
	    	break;
		}
	}

	#=======================================
	# @ List Canned
	# Show a list of canned replies.
	#=======================================

	function list_canned($error='', $alert='')
	{
		#=============================
		# Grab Canned Replies
		#=============================

		// Filter?
		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'canned',
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$canned_rows = ""; // Initialize for Security
		$row_count = 0; // Initialize for Security

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			while( $c = $this->ifthd->core->db->fetch_row() )
			{
				$row_count ++;
				
				( $row_count & 1 ) ? $row_class = 'option1-med' : $row_class = 'option2-med';
				
				#=============================
				# Fix Up Information
				#=============================

				$c['description'] = $this->ifthd->shorten_str( $c['description'], 80, 1 );

				$canned_rows .= "<tr>
									<td class='{$row_class}'>{$c['id']}</td>
									<td class='{$row_class}'>{$c['name']}</td>
									<td class='{$row_class}' style='font-weight: normal'>{$c['description']}</td>
									<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=canned&amp;code=edit&amp;id={$c['id']}'><img src='<! IMG_DIR !>/button_edit.gif' alt='Edit' /></a></td>
									<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=canned&amp;code=dodel&amp;id={$c['id']}' onclick='return sure_delete()'><img src='<! IMG_DIR !>/button_delete.gif' alt='Delete' /></a></td>
								</tr>";
			}
		}
		else
		{
			$canned_rows .= "<tr>
								<td class='option1' colspan='5'>There are no canned replies to display.</td>
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
								if ( confirm('Are you sure you want to delete this canned reply?') )
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
						<div class='groupbox'>Canned Replies List</div>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<th width='5%' align='left'>ID</th>
							<th width='25%' align='left'>Name</th>
							<th width='52%' align='left'>Description</th>
							<th width='7%'>Edit</th>
							<th width='11%'>Delete</th>
						</tr>
						". $canned_rows ."
						</table>
						<div class='formtail'><div class='fb_pad'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=canned&amp;code=add' class='fake_button'>Add A New Canned Reply</a></div></div>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=canned'>Canned Replies</a>",
						   "List Canned Rplies",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Canned Replies' ) );
	}

	#=======================================
	# @ Add Canned
	# Show add canned reply form.
	#=======================================

	function add_canned($error="")
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['acp']['manage_canned_add'] )
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
								form.name.focus();
								return false;
							}

							if ( ! form.description.value )
							{
								alert('Please enter a description.');
								form.description.focus();
								return false;
							}
						}

						</script>";

		if ( $this->ifthd->member['use_rte'] && $this->ifthd->core->cache['config']['enable_ticket_rte'] )
		{
			$this->output .= "<script language='javascript' type='text/javascript' src='<! HD_URL !>/includes/tinymce/tiny_mce.js'></script>
							<script language='javascript' type='text/javascript'>
							tinyMCE.init({
								mode : 'exact',
								theme : 'advanced',
								elements : 'message',
								plugins : 'inlinepopups,safari,spellchecker',
								dialog_type : 'modal',
								theme_advanced_toolbar_location : 'top',
								theme_advanced_toolbar_align : 'left',
								theme_advanced_path_location : 'bottom',
								theme_advanced_disable : 'styleselect,formatselect',
								theme_advanced_buttons1 : 'bold,italic,underline,strikethrough,separator,forecolor,backcolor,separator,bullist,numlist,separator,outdent,indent,separator,link,unlink,image,separator,undo,redo,separator,spellchecker,separator,removeformat,cleanup,code',
								theme_advanced_buttons2 : '',
								theme_advanced_buttons3 : '',
								theme_advanced_resize_horizontal : false,
								theme_advanced_resizing : true
							});
							</script>";
		}

		$this->output .= "{$error}
							<form action='<! HD_URL !>/admin.php?section=manage&amp;act=canned&amp;code=doadd' method='post' onsubmit='return validate_form(this)'>
							<div class='groupbox'>Add A New Canned Reply</div>
							<table width='100%' cellpadding='0' cellspacing='0'>
							<tr>
								<td class='option1' width='17%'><label for='name'>Name</label></td>
								<td class='option1' width='83%'><input type='text' name='name' id='name' value='{$this->ifthd->input['name']}' size='35' /></td>
							</tr>
							<tr>
								<td class='option2' valign='top'><label for='description'>Description</label></td>
								<td class='option2'><textarea name='description' id='description' cols='50' rows='2'>{$this->ifthd->input['description']}</textarea></td>
							</tr>
							<tr>
								<td class='option1' colspan='2'><textarea name='message' id='message' rows='6' cols='120' style='width: 98%; height: 200px;'>{$this->ifthd->input['message']}</textarea></td>
							</tr>
							</table>
							<div class='formtail'><input type='submit' name='submit' id='add' value='Add Canned Reply' class='button' /></div>
							</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=canned'>Canned Replies</a>",
						   "Add Canned Reply",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Canned Replies' ) );
	}

	#=======================================
	# @ Edit Canned
	# Show edit canned reply form.
	#=======================================

	function edit_canned($error="")
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['acp']['manage_canned_edit'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'canned',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_canned');
		}

		$c = $this->ifthd->core->db->fetch_row();

		#=============================
		# Do Output
		#=============================

		if ( $error )
		{
			$error = "<div class='critical'>{$error}</div>";

			$name = $this->ifthd->input['name'];
			$description = $this->ifthd->input['description'];
			$message = $this->ifthd->input['message'];
		}
		else
		{
			$name = $c['name'];
			$description = $c['description'];
			$message = $c['content'];
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

							if ( ! form.description.value )
							{
								alert('Please enter a description.');
								form.description.focus();
								return false;
							}
						}

						</script>";

		if ( $this->ifthd->member['use_rte'] && $this->ifthd->core->cache['config']['enable_ticket_rte'] )
		{
			$this->output .= "<script language='javascript' type='text/javascript' src='<! HD_URL !>/includes/tinymce/tiny_mce.js'></script>
							<script language='javascript' type='text/javascript'>
							tinyMCE.init({
								mode : 'exact',
								theme : 'advanced',
								elements : 'message',
								plugins : 'inlinepopups,safari,spellchecker',
								dialog_type : 'modal',
								theme_advanced_toolbar_location : 'top',
								theme_advanced_toolbar_align : 'left',
								theme_advanced_path_location : 'bottom',
								theme_advanced_disable : 'styleselect,formatselect',
								theme_advanced_buttons1 : 'bold,italic,underline,strikethrough,separator,forecolor,backcolor,separator,bullist,numlist,separator,outdent,indent,separator,link,unlink,image,separator,undo,redo,separator,spellchecker,separator,removeformat,cleanup,code',
								theme_advanced_buttons2 : '',
								theme_advanced_buttons3 : '',
								theme_advanced_resize_horizontal : false,
								theme_advanced_resizing : true
							});
							</script>";
		}

		$this->output .= "{$error}
							<form action='<! HD_URL !>/admin.php?section=manage&amp;act=canned&amp;code=doedit&amp;id={$c['id']}' method='post' onsubmit='return validate_form(this)'>
							<div class='groupbox'>Editing Canned Reply: {$c['name']}</div>
							<table width='100%' cellpadding='0' cellspacing='0'>
							<tr>
								<td class='option1' width='17%'><label for='name'>Name</label></td>
								<td class='option1' width='83%'><input type='text' name='name' id='name' value='{$name}' size='35' /></td>
							</tr>
							<tr>
								<td class='option2' valign='top'><label for='description'>Description</label></td>
								<td class='option2'><textarea name='description' id='description' cols='50' rows='2'>{$description}</textarea></td>
							</tr>
							<tr>
								<td class='option1' colspan='2'><textarea name='message' id='message' rows='6' cols='120' style='width: 98%; height: 200px;'>{$message}</textarea></td>
							</tr>
							</table>
							<div class='formtail'><input type='submit' name='submit' id='edit' value='Edit Canned Reply' class='button' /></div>
							</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=canned'>Canned Replies</a>",
						   "Edit Canned Reply",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Canned Replies' ) );
	}

	#=======================================
	# @ Get Canned
	# Show get canned reply for AJAX.
	#=======================================

	function get_canned()
	{
		#=============================
		# Get Canned Reply
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'content' ),
											  	 'from'		=> 'canned',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0, 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			$c = $this->ifthd->core->db->fetch_row();
		}

		if (  $this->ifthd->core->cache['config']['enable_ticket_rte'] )
		{
			print $this->ifthd->prepare_output( $c['content'], 0, 1 );
		}
		else
		{
			print $c['content'];
		}

		exit();
	}

	#=======================================
	# @ Do Add Canned
	# Create a new canned reply.
	#=======================================

	function do_add()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['acp']['manage_canned_add'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		if ( ! $this->ifthd->input['name'] )
		{
			$this->add_canned('Please enter a name.');
		}

		if ( ! $this->ifthd->input['description'] )
		{
			$this->add_canned('Please enter a description.');
		}
		
		$this->ifthd->input['message'] = $this->ifthd->remove_extra_lbs( $this->ifthd->input['message'] );

		if ( ! $this->ifthd->input['message'] )
		{
			$this->add_canned('Please enter a message.');
		}

		#=============================
		# Add Article
		#=============================

		$db_array = array(
						  'name'		=> $this->ifthd->input['name'],
						  'description'	=> $this->ifthd->input['description'],
						  'content'		=> $this->ifthd->input['message'],
						 );

		$this->ifthd->core->db->construct( array(
											  	 'insert'	=> 'canned',
											  	 'set'		=> $db_array,
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$canned_id = $this->ifthd->core->db->get_insert_id();

		$this->ifthd->log( 'admin', "Canned Reply Added &#039;". $this->ifthd->input['name'] ."&#039;", 1, $canned_id );

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_canned_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=canned&code=list', 'add_canned_success' );
		$this->list_canned( '', 'The canned reply has been successfully added.' );
	}

	#=======================================
	# @ Do Edit Canned
	# Edit a canned reply.
	#=======================================

	function do_edit()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['acp']['manage_canned_edit'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'canned',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_canned');
		}

		$c = $this->ifthd->core->db->fetch_row();

		if ( ! $this->ifthd->input['name'] )
		{
			$this->edit_canned('Please enter a name.');
		}

		if ( ! $this->ifthd->input['description'] )
		{
			$this->edit_canned('Please enter a description.');
		}
		
		$this->ifthd->input['message'] = $this->ifthd->remove_extra_lbs( $this->ifthd->input['message'] );

		if ( ! $this->ifthd->input['message'] )
		{
			$this->edit_canned('Please enter a message.}');
		}

		#=============================
		# Edit Canned Reply
		#=============================

		$db_array = array(
						  'name'		=> $this->ifthd->input['name'],
						  'description'	=> $this->ifthd->input['description'],
						  'content'		=> $this->ifthd->input['message'],
						 );

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'canned',
											  	 'set'		=> $db_array,
							 				  	 'where'	=> array( 'id', '=', $c['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'admin', "Canned Reply Updated &#039;". $this->ifthd->input['name'] ."&#039;", 1, $c['id'] );

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_canned_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=canned&code=list', 'edit_canned_success' );
		$this->list_canned( '', 'The canned reply has been successfully updated.' );
	}

	#=======================================
	# @ Do Delete Canned
	# Delete a canned reply.
	#=======================================

	function do_delete()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['acp']['manage_canned_delete'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'canned',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_canned');
		}

		$c = $this->ifthd->core->db->fetch_row();

		#=============================
		# DELETE
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'delete'	=> 'canned',
							 				  	 'where'	=> array( 'id', '=', $c['id'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'admin', "Canned Reply Deleted &#039;". $c['name'] ."&#039;", 2, $c['id'] );

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_canned_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=canned&code=list', 'delete_canned_success' );
		$this->list_canned( 'The canned reply has been successfully deleted.' );
	}

}

?>