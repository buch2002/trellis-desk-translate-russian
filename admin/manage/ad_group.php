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
|    | Admin Groups
#======================================================
*/

class ad_group {

	#=======================================
	# @ Auto Run
	# Function that is run automatically
	# when the file is required.
	#=======================================

	function auto_run()
	{
		if ( ! $this->ifthd->member['acp']['manage_group'] )
		{
			$this->ifthd->skin->error('no_perm');
		}
		
		$this->ifthd->skin->set_section( 'Member Control' );		
		$this->ifthd->skin->set_description( 'Manage your members, groups, custom profile fields and members awaiting validation.' );

		switch( $this->ifthd->input['code'] )
	    {
	    	case 'list':
				$this->list_groups();
	    	break;
	    	case 'acpperm':
				$this->list_groups( '', '', 'acpperm' );
	    	break;
	    	case 'add':
	    		$this->add_group();
	    	break;
	    	case 'edit':
	    		$this->edit_group();
	    	break;
	    	case 'delete':
	    		$this->delete_group();
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
    			$this->list_groups();
    		break;
		}
	}

	#=======================================
	# @ List Groups
	# Show a list of members.
	#=======================================

	function list_groups($error='', $alert='', $extra='')
	{
		#=============================
		# Grab Members
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'groups',
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$group_rows = ""; // Initialize for Security
		$row_count = 0; // Initialize for Security

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_group');
		}

		while( $g = $this->ifthd->core->db->fetch_row() )
		{
			$row_count ++;
				
			( $row_count & 1 ) ? $row_class = 'option1-med' : $row_class = 'option2-med';
			
			( $g['g_id'] > 5 ) ? $delete_button = "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=group&amp;code=delete&amp;id={$g['g_id']}'><img src='<! IMG_DIR !>/button_delete.gif' alt='Delete' /></a>" : $delete_button = '';
				
			$group_rows .= "<tr>
								<td class='{$row_class}'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=list&amp;group={$g['g_id']}'>{$g['g_id']}</a></td>
								<td class='{$row_class}'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=list&amp;group={$g['g_id']}'>{$g['g_name']}</a></td>
								<td class='{$row_class}' align='center'>{$g['g_members']}</td>
								<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=group&amp;code=edit&amp;id={$g['g_id']}'><img src='<! IMG_DIR !>/button_edit.gif' alt='Edit' /></a></td>
								<td class='{$row_class}' align='center'>{$delete_button}</td>
							</tr>";
		}

		#=============================
		# Do Output
		#=============================

		if ( $extra == 'acpperm' )
		{
			$extra = "<div class='option1'>To edit ACP Permissions, simply click the 'Edit' link next to the desired group.  You will see an ACP Permissions area at the bottom of the edit group page.</div>";
		}

		if ( $error )
		{
			$error = "<div class='critical'>{$error}</div>";
		}
		elseif ( $alert )
		{
			$error = "<div class='alert'>{$alert}</div>";
		}

		$this->output = "{$error}
						<div class='groupbox'>Group List</div>
						{$extra}
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<th width='5%' align='left'>ID</th>
							<th width='60%' align='left'>Name</th>
							<th width='15%'>Members</th>
							<th width='8%'>Edit</th>
							<th width='12%'>Delete</th>
						</tr>
						". $group_rows ."
						</table>
						<div class='formtail'><div class='fb_pad'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=group&amp;code=add' class='fake_button'>Add A New Group</a></div></div>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=group'>Groups</a>",
						   "List Groups",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Groups' ) );
	}

	#=======================================
	# @ Add Group
	# Show add group form.
	#=======================================

	function add_group($error="")
	{
		#=============================
		# Do Output
		#=============================

		if ( $error )
		{
			$error = "<div class='critical'>{$error}</div>";
		}

		if ( ! $this->ifthd->member['acp']['manage_group_add'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		#=============================
		# Generate Permissions
		#=============================

		if ( is_array( $this->ifthd->input['m_depart_perm'] ) )
		{
			while ( list( , $permc ) = each( $this->ifthd->input['m_depart_perm'] ) )
			{
				$g_m_depart_perm[ $permc ] = 1;
			}
		}

		if ( $this->ifthd->member['id'] == 1 )
		{
			if ( is_array( $this->ifthd->input['acp_perm'] ) )
			{
				while ( list( , $perm ) = each( $this->ifthd->input['acp_perm'] ) )
				{
					$g_acp_perm[ $perm ] = 1;
				}
			}

			if ( is_array( $this->ifthd->input['depart_perm'] ) )
			{
				while ( list( , $permb ) = each( $this->ifthd->input['depart_perm'] ) )
				{
					$g_depart_perm[ $permb ] = 1;
				}
			}
		}

		$this->output = "<script type='text/javascript'>

						function validate_form(form)
						{
							if ( ! form.g_name.value )
							{
								alert('Please enter a name.');
								form.g_name.focus();
								return false;
							}
						}

						</script>
						{$error}
						<form action='<! HD_URL !>/admin.php?section=manage&amp;act=group&amp;code=doadd' method='post' onsubmit='return validate_form(this)'>
						<div class='groupbox'>Add A New Group</div>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<td class='option1' width='30%'><label for='g_name'>Name</label></td>
							<td class='option1' width='70%'><input type='text' name='g_name' id='g_name' value='{$this->ifthd->input['g_name']}' size='35' /></td>
						</tr>
						<tr>
							<td class='option2'>Ticket Center Access</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_ticket_access', $this->ifthd->input['g_ticket_access'] ) ."
							</td>
						</tr>
						<tr>
							<td class='option1'>Can Create New Tickets</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_new_tickets', $this->ifthd->input['g_new_tickets'] ) ."
							</td>
						</tr>
						<tr>
							<td class='option2'>Can Edit Own Tickets</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_ticket_edit', $this->ifthd->input['g_ticket_edit'] ) ."
							</td>
						</tr>
						<tr>
							<td class='option1'>Can Close Own Tickets</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_ticket_own_close', $this->ifthd->input['g_ticket_own_close'] ) ."
							</td>
						</tr>
						<tr>
							<td class='option2'>Can Edit Own Replies</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_reply_edit', $this->ifthd->input['g_reply_edit'] ) ."
							</td>
						</tr>
						<tr>
							<td class='option1'>Can Delete Own Replies</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_reply_delete', $this->ifthd->input['g_reply_delete'] ) ."
							</td>
						</tr>
						<tr>
							<td class='option2'>Knowledge Base Access</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_kb_access', $this->ifthd->input['g_kb_access'] ) ."
							</td>
						</tr>
						<tr>
							<td class='option1'>Can Rate Staff Replies</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_reply_rate', $this->ifthd->input['g_reply_rate'] ) ."
							</td>
						</tr>
						<tr>
							<td class='option2'>Can Rate Articles</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_kb_rate', $this->ifthd->input['g_kb_rate'] ) ."
							</td>
						</tr>
						<tr>
							<td class='option1'>Can Comment on Articles</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_kb_comment', $this->ifthd->input['g_kb_comment'] ) ."
							</td>
						</tr>
						<tr>
							<td class='option2'>Can Comment on News</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_news_comment', $this->ifthd->input['g_news_comment'] ) ."
							</td>
						</tr>
						<tr>
							<td class='option1'>Can Edit All Comments</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_com_edit_all', $this->ifthd->input['g_com_edit_all'] ) ."
							</td>
						</tr>
						<tr>
							<td class='option2'>Can Delete All Comments</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_com_delete_all', $this->ifthd->input['g_com_delete_all'] ) ."
							</td>
						</tr>
						<tr>
							<td class='option1'>Can Change Skin</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_change_skin', $g_change_skin ) ."
							</td>
						</tr>
						<tr>
							<td class='option2'>Can Change Lang</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_change_lang', $this->ifthd->input['g_change_lang'] ) ."
							</td>
						</tr>
						<tr>
							<td class='option1'>Allow Ticket Attachments</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_ticket_attach', $this->ifthd->input['g_ticket_attach'] ) ."
							</td>
						</tr>
						<tr>
							<td class='option2'><label for='g_upload_size_max'>Max Upload Size</label></td>
							<td class='option2' style='font-weight: normal'><input type='text' name='g_upload_size_max' id='g_upload_size_max' value='{$this->ifthd->input['g_upload_size_max']}' size='6' /> (Bytes) (Leave blank to disable limit)</td>
						</tr>
						<tr>
							<td class='option1'>ACP Access</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_acp_access', $this->ifthd->input['g_acp_perm'] ) ."
							</td>
						</tr>
						<tr>
							<td class='option2' valign='top'>Department Permissions</td>
							<td class='option2' valign='top'>
								<select name='m_depart_perm[]' id='m_depart_perm' size='5' multiple='multiple'>
								". $this->ifthd->build_dprt_drop( $g_m_depart_perm, 0, 1 ) ."
								</select>
							</td>
						</tr>";

		if ( $this->ifthd->member['id'] == 1 )
		{
			$this->output .= "<tr>
								<td class='option1' valign='top'>ACP Permissions</td>
								<td class='option1'>
									<select name='acp_perm[]' id='acp_perm' size='10' multiple='multiple'>
									". $this->acp_perm_drop($g_acp_perm) ."
									</select>
								</td>
							</tr>
							<tr>
								<td class='option2' valign='top'>ACP Department Permissions<div class='addesc'>(Leave blank to allow all)</div></td>
								<td class='option2' valign='top'>
									<select name='depart_perm[]' id='depart_perm' size='5' multiple='multiple'>
									". $this->ifthd->build_dprt_drop( $g_depart_perm, 0, 1 ) ."
									</select>
								</td>
							</tr>";
		}

		$this->output .= "</table>
						<div class='formtail'><input type='submit' name='submit' id='add' value='Add Group' class='button' /></div>
						</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=group'>Groups</a>",
						   "Add Group",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Groups' ) );
	}

	#=======================================
	# @ Edit Group
	# Show edit group form.
	#=======================================

	function edit_group($error="")
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_group_edit'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'groups',
							 				  	 'where'	=> array( 'g_id', '=', $this->ifthd->input['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_group');
		}

		$g = $this->ifthd->core->db->fetch_row();

		#=============================
		# Do Output
		#=============================

		if ( $error )
		{
			$error = "<div class='critical'>{$error}</div>";

			$g_name = $this->ifthd->input['g_name'];

			$g_ticket_access = $this->ifthd->input['g_ticket_access'];
			$g_ticket_edit = $this->ifthd->input['g_ticket_edit'];
			$g_new_tickets = $this->ifthd->input['g_new_tickets'];
			$g_ticket_own_close = $this->ifthd->input['g_ticket_own_close'];
			$g_reply_edit = $this->ifthd->input['g_reply_edit'];
			$g_reply_delete = $this->ifthd->input['g_reply_delete'];
			$g_reply_rate = $this->ifthd->input['g_reply_rate'];
			$g_kb_access = $this->ifthd->input['g_kb_access'];
			$g_kb_rate = $this->ifthd->input['g_kb_rate'];
			$g_kb_comment = $this->ifthd->input['g_kb_comment'];
			$g_news_comment = $this->ifthd->input['g_news_comment'];
			$g_change_skin = $this->ifthd->input['g_change_skin'];
			$g_change_lang = $this->ifthd->input['g_change_lang'];
			$g_com_edit_all = $this->ifthd->input['g_com_edit_all'];
			$g_com_delete_all = $this->ifthd->input['g_com_delete_all'];
			$g_ticket_attach = $this->ifthd->input['g_ticket_attach'];
			$g_upload_size_max = $this->ifthd->input['g_upload_size_max'];
			$g_acp_access = $this->ifthd->input['g_acp_access'];

			#=============================
			# Generate Permissions
			#=============================

			if ( is_array( $this->ifthd->input['m_depart_perm'] ) )
			{
				while ( list( , $permc ) = each( $this->ifthd->input['m_depart_perm'] ) )
				{
					$g_m_depart_perm[ $permc ] = 1;
				}
			}

			if ( $this->ifthd->member['id'] == 1 )
			{
				if ( is_array( $this->ifthd->input['acp_perm'] ) )
				{
					while ( list( , $perm ) = each( $this->ifthd->input['acp_perm'] ) )
					{
						$g_acp_perm[ $perm ] = 1;
					}
				}

				if ( is_array( $this->ifthd->input['depart_perm'] ) )
				{
					while ( list( , $permb ) = each( $this->ifthd->input['depart_perm'] ) )
					{
						$g_depart_perm[ $permb ] = 1;
					}
				}
			}
		}
		else
		{
			$g_name = $g['g_name'];

			$g_ticket_access = $g['g_ticket_access'];
			$g_new_tickets = $g['g_new_tickets'];
			$g_ticket_edit = $g['g_ticket_edit'];
			$g_ticket_own_close = $g['g_ticket_own_close'];
			$g_reply_edit = $g['g_reply_edit'];
			$g_reply_delete = $g['g_reply_delete'];
			$g_reply_rate = $g['g_reply_rate'];
			$g_kb_access = $g['g_kb_access'];
			$g_kb_rate = $g['g_kb_rate'];
			$g_kb_comment = $g['g_kb_comment'];
			$g_news_comment = $g['g_news_comment'];
			$g_change_skin = $g['g_change_skin'];
			$g_change_lang = $g['g_change_lang'];
			$g_com_edit_all = $g['g_com_edit_all'];
			$g_com_delete_all = $g['g_com_delete_all'];
			$g_ticket_attach = $g['g_ticket_attach'];
			$g_upload_size_max = $g['g_upload_size_max'];
			$g_acp_access = $g['g_acp_access'];
			$g_m_depart_perm = unserialize( $g['g_m_depart_perm'] );

			if ( $this->ifthd->member['id'] == 1 )
			{
				$g_acp_perm = unserialize( $g['g_acp_perm'] );
				$g_depart_perm = unserialize( $g['g_depart_perm'] );
			}
		}

		$this->output = "<script type='text/javascript'>

						function validate_form(form)
						{
							if ( ! form.g_name.value )
							{
								alert('Please enter a name.');
								form.g_name.focus();
								return false;
							}
						}

						</script>
						{$error}
						<form action='<! HD_URL !>/admin.php?section=manage&amp;act=group&amp;code=doedit&amp;id={$g['g_id']}' method='post' onsubmit='return validate_form(this)'>
						<div class='groupbox'>Editing Group: {$g['g_name']}</div>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<td class='option1' width='30%'><label for='g_name'>Name</label></td>
							<td class='option1' width='70%'><input type='text' name='g_name' id='g_name' value='{$g_name}' size='35' /></td>
						</tr>
						<tr>
							<td class='option2'>Ticket Center Access</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_ticket_access', $g_ticket_access ) ."
							</td>
						</tr>
						<tr>
							<td class='option1'>Can Create New Tickets</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_new_tickets', $g_new_tickets ) ."
							</td>
						</tr>
						<tr>
							<td class='option2'>Can Edit Own Tickets</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_ticket_edit', $g_ticket_edit ) ."
							</td>
						</tr>
						<tr>
							<td class='option1'>Can Close Own Tickets</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_ticket_own_close', $g_ticket_own_close ) ."
							</td>
						</tr>
						<tr>
							<td class='option2'>Can Edit Own Replies</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_reply_edit', $g_reply_edit ) ."
							</td>
						</tr>
						<tr>
							<td class='option1'>Can Delete Own Replies</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_reply_delete', $g_reply_delete ) ."
							</td>
						</tr>
						<tr>
							<td class='option2'>Knowledge Base Access</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_kb_access', $g_kb_access ) ."
							</td>
						</tr>";

		if ( $g['g_id'] != 2 )
		{
			$this->output .= "<tr>
							<td class='option1'>Can Rate Staff Replies</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_reply_rate', $g_reply_rate ) ."
							</td>
						</tr>
						<tr>
							<td class='option2'>Can Rate Articles</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_kb_rate', $g_kb_rate ) ."
							</td>
						</tr>
						<tr>
							<td class='option1'>Can Comment on Articles</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_kb_comment', $g_kb_comment ) ."
							</td>
						</tr>
						<tr>
							<td class='option2'>Can Comment on News</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_news_comment', $g_news_comment ) ."
							</td>
						</tr>
						<tr>
							<td class='option1'>Can Edit All Comments</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_com_edit_all', $g_com_edit_all ) ."
							</td>
						</tr>
						<tr>
							<td class='option2'>Can Delete All Comments</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_com_delete_all', $g_com_delete_all ) ."
							</td>
						</tr>
						<tr>
							<td class='option1'>Can Change Skin</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_change_skin', $g_change_skin ) ."
							</td>
						</tr>
						<tr>
							<td class='option2'>Can Change Lang</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_change_lang', $g_change_lang ) ."
							</td>
						</tr>
						<tr>
							<td class='option1'>Allow Ticket Attachments</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_ticket_attach', $g_ticket_attach ) ."
							</td>
						</tr>
						<tr>
							<td class='option2'><label for='g_upload_size_max'>Max Upload Size</label></td>
							<td class='option2' style='font-weight: normal'><input type='text' name='g_upload_size_max' id='g_upload_size_max' value='{$g_upload_size_max}' size='6' /> (Bytes) (Leave blank to disable limit)</td>
						</tr>
						<tr>
							<td class='option1'>ACP Access</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'g_acp_access', $g_acp_access ) ."
							</td>
						</tr>";
		}

		$this->output .= "<tr>
							<td class='option2' valign='top'>Department Permissions</td>
							<td class='option2' valign='top'>
								<select name='m_depart_perm[]' id='m_depart_perm' size='5' multiple='multiple'>
								". $this->ifthd->build_dprt_drop( $g_m_depart_perm, 0, 1 ) ."
								</select>
							</td>
						</tr>";

		if ( $this->ifthd->member['id'] == 1 && $g['g_id'] != 2 )
		{
			$this->output .= "<tr>
							<td class='option1' valign='top'>ACP Permissions</td>
							<td class='option1'>
								<select name='acp_perm[]' id='acp_perm' size='10' multiple='multiple'>
								". $this->acp_perm_drop($g_acp_perm) ."
								</select>
							</td>
						</tr>
						<tr>
							<td class='option2' valign='top'>ACP Department Permissions<div class='addesc'>(Leave blank to allow all)</div></td>
							<td class='option2' valign='top'>
								<select name='depart_perm[]' id='depart_perm' size='5' multiple='multiple'>
								". $this->ifthd->build_dprt_drop( $g_depart_perm, 0, 1 ) ."
								</select>
							</td>
						</tr>";
		}

		$this->output .= "</table>
						<div class='formtail'><input type='submit' name='submit' id='edit' value='Edit Group' class='button' /></div>
						</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=group'>Groups</a>",
						   "Edit Group",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Groups' ) );
	}

	#=======================================
	# @ Delete Group
	# Show delete group form.
	#=======================================

	function delete_group()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_group_delete'] )
		{
			$this->ifthd->skin->error('no_perm');
		}
		
		if ( $this->ifthd->input['id'] <= 5 )
		{
			$this->list_groups( 'This group cannot be deleted.' );
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'groups',
							 				  	 'where'	=> array( 'g_id', '=', $this->ifthd->input['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_group');
		}

		$g = $this->ifthd->core->db->fetch_row();

		#=============================
		# Do Output
		#=============================

		$group_drop = $this->ifthd->build_group_drop( 0, $g['g_id'] );

		$this->output = "<form action='<! HD_URL !>/admin.php?section=manage&amp;act=group&amp;code=dodel&amp;id={$g['g_id']}' method='post'>
						<div class='groupbox'>Deleting Group: {$g['g_name']}</div>
						<div class='subbox'>What would you like to do with the members in this group?</div>
						<div class='option1'>
							<input type='radio' name='action' id='action1' value='1' checked='checked' /> <label for='action1'>Move the members to this group:</label> <select name='moveto'>{$group_drop}</select><br />
							<input type='radio' name='action' id='action2' value='2' /> <label for='action2'>Delete the members</label>
						</div>
						<div class='formtail'><input type='submit' name='submit' id='delete' value='Delete Group' class='button' /></div>
						</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=group'>Groups</a>",
						   "Delete Group",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Groups' ) );
	}

	#=======================================
	# @ Do Add
	# Create a new group.
	#=======================================

	function do_add()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['acp']['manage_group_add'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		if ( ! $this->ifthd->input['g_name'] )
		{
			$this->add_group('Please enter a name.');
		}

		#=============================
		# Generate Permissions
		#=============================

		if ( is_array( $this->ifthd->input['m_depart_perm'] ) )
		{
			while ( list( , $mdperm ) = each( $this->ifthd->input['m_depart_perm'] ) )
			{
				$m_depart_perm[ $mdperm ] = 1;
			}
		}

		if ( $this->ifthd->member['id'] == 1 )
		{
			if ( is_array( $this->ifthd->input['acp_perm'] ) )
			{
				while ( list( , $perm ) = each( $this->ifthd->input['acp_perm'] ) )
				{
					$acp_perm[ $perm ] = 1;
				}
			}

			if ( is_array( $this->ifthd->input['depart_perm'] ) )
			{
				while ( list( , $dperm ) = each( $this->ifthd->input['depart_perm'] ) )
				{
					$depart_perm[ $dperm ] = 1;
				}
			}
		}

		#=============================
		# Insert Group
		#=============================

		$db_array = array(
						  'g_name'				=> $this->ifthd->input['g_name'],
						  'g_ticket_access'		=> $this->ifthd->input['g_ticket_access'],
						  'g_new_tickets'		=> $this->ifthd->input['g_new_tickets'],
						  'g_ticket_edit'		=> $this->ifthd->input['g_ticket_edit'],
						  'g_reply_rate'		=> $this->ifthd->input['g_reply_rate'],
						  'g_reply_edit'		=> $this->ifthd->input['g_reply_edit'],
						  'g_reply_delete'		=> $this->ifthd->input['g_reply_delete'],
						  'g_ticket_own_close'	=> $this->ifthd->input['g_ticket_own_close'],
						  'g_kb_access'			=> $this->ifthd->input['g_kb_access'],
						  'g_kb_rate'			=> $this->ifthd->input['g_kb_rate'],
						  'g_kb_comment'		=> $this->ifthd->input['g_kb_comment'],
						  'g_news_comment'		=> $this->ifthd->input['g_news_comment'],
						  'g_change_skin'		=> $this->ifthd->input['g_change_skin'],
						  'g_change_lang'		=> $this->ifthd->input['g_change_lang'],
						  'g_com_edit_all'		=> $this->ifthd->input['g_com_edit_all'],
						  'g_com_delete_all'	=> $this->ifthd->input['g_com_delete_all'],
						  'g_ticket_attach'		=> $this->ifthd->input['g_ticket_attach'],
						  'g_upload_size_max'	=> $this->ifthd->input['g_upload_size_max'],
						  'g_m_depart_perm'		=> serialize($m_depart_perm),
						  'g_acp_access'		=> $this->ifthd->input['g_acp_access'],
						 );

		if ( $this->ifthd->member['id'] == 1 )
		{
			$db_array['g_depart_perm'] = serialize($depart_perm);
			$db_array['g_acp_perm'] = serialize($acp_perm);
		}

		$this->ifthd->core->db->construct( array(
											  	 'insert'	=> 'groups',
											  	 'set'		=> $db_array,
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$group_id = $this->ifthd->core->db->get_insert_id();

		$this->ifthd->log( 'admin', "Group Added &#039;". $this->ifthd->input['g_name'] ."&#039;", 1, $group_id );

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_group_cache();
		$this->ifthd->rebuild_staff_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=group', 'add_group_success' );
		$this->list_groups( '', 'The group has been successfully added.' );
	}

	#=======================================
	# @ Do Edit
	# Edit a group.
	#=======================================

	function do_edit()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_group_edit'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'groups',
							 				  	 'where'	=> array( 'g_id', '=', $this->ifthd->input['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_group');
		}

		if ( ! $this->ifthd->input['g_name'] )
		{
			$this->edit_group('Please enter a name.');
		}

		#=============================
		# Generate Permissions
		#=============================

		if ( is_array( $this->ifthd->input['m_depart_perm'] ) )
		{
			while ( list( , $mdperm ) = each( $this->ifthd->input['m_depart_perm'] ) )
			{
				$m_depart_perm[ $mdperm ] = 1;
			}
		}

		if ( $this->ifthd->member['id'] == 1 )
		{
			if ( is_array( $this->ifthd->input['acp_perm'] ) )
			{
				while ( list( , $perm ) = each( $this->ifthd->input['acp_perm'] ) )
				{
					$acp_perm[ $perm ] = 1;
				}
			}

			if ( is_array( $this->ifthd->input['depart_perm'] ) )
			{
				while ( list( , $dperm ) = each( $this->ifthd->input['depart_perm'] ) )
				{
					$depart_perm[ $dperm ] = 1;
				}
			}
		}

		#=============================
		# Update Group
		#=============================

		$db_array = array(
						  'g_name'				=> $this->ifthd->input['g_name'],
						  'g_ticket_access'		=> $this->ifthd->input['g_ticket_access'],
						  'g_new_tickets'		=> $this->ifthd->input['g_new_tickets'],
						  'g_ticket_edit'		=> $this->ifthd->input['g_ticket_edit'],
						  'g_reply_rate'		=> $this->ifthd->input['g_reply_rate'],
						  'g_reply_edit'		=> $this->ifthd->input['g_reply_edit'],
						  'g_reply_delete'		=> $this->ifthd->input['g_reply_delete'],
						  'g_ticket_own_close'	=> $this->ifthd->input['g_ticket_own_close'],
						  'g_kb_access'			=> $this->ifthd->input['g_kb_access'],
						  'g_kb_rate'			=> $this->ifthd->input['g_kb_rate'],
						  'g_kb_comment'		=> $this->ifthd->input['g_kb_comment'],
						  'g_news_comment'		=> $this->ifthd->input['g_news_comment'],
						  'g_change_skin'		=> $this->ifthd->input['g_change_skin'],
						  'g_change_lang'		=> $this->ifthd->input['g_change_lang'],
						  'g_com_edit_all'		=> $this->ifthd->input['g_com_edit_all'],
						  'g_com_delete_all'	=> $this->ifthd->input['g_com_delete_all'],
						  'g_ticket_attach'		=> $this->ifthd->input['g_ticket_attach'],
						  'g_upload_size_max'	=> $this->ifthd->input['g_upload_size_max'],
						  'g_m_depart_perm'		=> serialize($m_depart_perm),
						  'g_acp_access'		=> $this->ifthd->input['g_acp_access'],
						 );

		if ( $this->ifthd->member['id'] == 1 )
		{
			$db_array['g_depart_perm'] = serialize($depart_perm);
			$db_array['g_acp_perm'] = serialize($acp_perm);
		}

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'groups',
											  	 'set'		=> $db_array,
							 				  	 'where'	=> array( 'g_id', '=', $this->ifthd->input['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'admin', "Group Edited &#039;". $this->ifthd->input['g_name'] ."&#039;", 1, $this->ifthd->input['id'] );

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_group_cache();
		$this->ifthd->rebuild_staff_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=group', 'edit_group_success' );
		$this->list_groups( '', 'The group has been successfully updated.' );
	}

	#=======================================
	# @ Do Delete
	# Delete a group.
	#=======================================

	function do_delete()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_group_delete'] )
		{
			$this->ifthd->skin->error('no_perm');
		}
		
		if ( $this->ifthd->input['id'] <= 5 )
		{
			$this->list_groups( 'This group cannot be deleted.' );
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'g_id', 'g_name' ),
											  	 'from'		=> 'groups',
							 				  	 'where'	=> array( 'g_id', '=', $this->ifthd->input['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_groups');
		}

		$g = $this->ifthd->core->db->fetch_row();

		#=============================
		# Perform Our Action
		#=============================

		if ( $this->ifthd->input['action'] == 1 )
		{
			#=============================
			# Update New Group
			#=============================

			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'id' ),
												  	 'from'		=> 'members',
								 				  	 'where'	=> array( 'mgroup', '=', $g['g_id'] ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$num_members = $this->ifthd->core->db->get_num_rows();

			$this->ifthd->core->db->next_no_quotes('set');

			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'groups',
												  	 'set'		=> array( 'g_members' => 'g_members+'. $num_members ),
								 				  	 'where'	=> array( 'g_id', '=', $this->ifthd->input['moveto'] ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			#=============================
			# Update Members
			#=============================

			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'members',
												  	 'set'		=> array( 'mgroup' => $this->ifthd->input['moveto'] ),
								 				  	 'where'	=> array( 'mgroup', '=', $g['g_id'] ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();
		}
		elseif ( $this->ifthd->input['action'] == 2 )
		{
			$this->ifthd->core->db->construct( array(
												  	 'delete'	=> 'members',
								 				  	 'where'	=> array( 'mgroup', '=', $g['g_id'] ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();
		}

		#=============================
		# DELETE *MwhaAaAaAaAa*
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'delete'	=> 'groups',
							 				  	 'where'	=> array( 'g_id', '=', $g['g_id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'admin', "Group Deleted &#039;". $g['g_name'] ."&#039;", 2, $g['g_id'] );

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_group_cache();
		$this->ifthd->rebuild_staff_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=group', 'delete_group_success' );
		$this->list_groups( 'The group has been successfully deleted.' );
	}

	#=======================================
	# @ ACP Permissions Drop-Down
	# Generate ACP permissions drop-down.
	#=======================================

	function acp_perm_drop($selected="")
	{
		$acp_titles = array(); // Initialize for Security
		$acp_pages = array(); // Initialize for Security
		$acp_subs = array(); // Initialize for Security

		$acp_titles['admin'] = 'Administration';
		$acp_titles['manage'] = 'Management';
		$acp_titles['look'] = 'Look &amp; Feel';
		$acp_titles['tools'] = 'Tools';

		$acp_pages['admin']['logs'] = 'Log Center';
		$acp_pages['manage']['ticket'] = 'Ticket Control';
		$acp_pages['manage']['canned'] = 'Canned Replies';
		$acp_pages['manage']['depart'] = 'Department Control';
		$acp_pages['manage']['announce'] = 'Announcement Control';
		$acp_pages['manage']['member'] = 'Member Control';
		$acp_pages['manage']['group'] = 'Group Control';
		$acp_pages['manage']['article'] = 'Article Control';
		$acp_pages['manage']['cat'] = 'Category Control';
		$acp_pages['manage']['pages'] = 'Pages Control';
		$acp_pages['manage']['settings'] = 'System Settings';
		$acp_pages['look']['skin'] = 'Skins';
		$acp_pages['look']['lang'] = 'Languages';
		$acp_pages['tools']['maint'] = 'Maintenance';
		$acp_pages['tools']['backup'] = 'Backup';

		$acp_subs['logs']['admin'] = 'Admin Logs';
		$acp_subs['logs']['member'] = 'Member Logs';
		$acp_subs['logs']['email'] = 'Email Logs';
		$acp_subs['logs']['error'] = 'Error Logs';
		$acp_subs['logs']['security'] = 'Security Logs';
		$acp_subs['logs']['ticket'] = 'Ticket Logs';
		$acp_subs['logs']['prune'] = 'Prune Logs';
		$acp_subs['ticket']['reply'] = 'Reply';
		$acp_subs['ticket']['assign_self'] = 'Assign to Self';
		$acp_subs['ticket']['assign_any'] = 'Assign to Any';
		$acp_subs['ticket']['hold'] = 'Put On Hold';
		$acp_subs['ticket']['escalate'] = 'Escalate';
		$acp_subs['ticket']['move'] = 'Move';
		$acp_subs['ticket']['close'] = 'Close';
		$acp_subs['ticket']['delete'] = 'Delete';
		$acp_subs['ticket']['reopen'] = 'Reopen';
		$acp_subs['canned']['add'] = 'Add';
		$acp_subs['canned']['edit'] = 'Edit';
		$acp_subs['canned']['delete'] = 'Delete';
		$acp_subs['depart']['add'] = 'Add';
		$acp_subs['depart']['edit'] = 'Edit';
		$acp_subs['depart']['delete'] = 'Delete';
		$acp_subs['depart']['reorder'] = 'Reorder';
		$acp_subs['depart']['cfields'] = 'Custom Fields';
		$acp_subs['announce']['add'] = 'Add';
		$acp_subs['announce']['edit'] = 'Edit';
		$acp_subs['announce']['delete'] = 'Delete';
		$acp_subs['member']['add'] = 'Add';
		$acp_subs['member']['edit'] = 'Edit';
		$acp_subs['member']['delete'] = 'Delete';
		$acp_subs['member']['approve'] = 'Approve';
		$acp_subs['member']['cfields'] = 'Custom Fields';
		$acp_subs['member']['staff'] = 'Manage Staff';
		$acp_subs['group']['add'] = 'Add';
		$acp_subs['group']['edit'] = 'Edit';
		$acp_subs['group']['delete'] = 'Delete';
		$acp_subs['article']['add'] = 'Add';
		$acp_subs['article']['edit'] = 'Edit';
		$acp_subs['article']['delete'] = 'Delete';
		$acp_subs['cat']['add'] = 'Add';
		$acp_subs['cat']['edit'] = 'Edit';
		$acp_subs['cat']['delete'] = 'Delete';
		$acp_subs['pages']['add'] = 'Add';
		$acp_subs['pages']['edit'] = 'Edit';
		$acp_subs['pages']['delete'] = 'Delete';
		$acp_subs['settings']['update'] = 'Update';
		$acp_subs['skin']['manage'] = 'Manage Sets';
		$acp_subs['skin']['tools'] = 'Tools';
		$acp_subs['skin']['import'] = 'Import';
		$acp_subs['skin']['export'] = 'Export';
		$acp_subs['lang']['manage'] = 'Manage';
		$acp_subs['lang']['tools'] = 'Tools';
		$acp_subs['lang']['import'] = 'Import';
		$acp_subs['lang']['export'] = 'Export';
		$acp_subs['maint']['recount'] = 'Recount';
		$acp_subs['maint']['clean'] = 'Clean';
		$acp_subs['maint']['optm'] = 'Optimize';
		$acp_subs['maint']['syscheck'] = 'System Check';

		$html = ""; // Initialize for Security

		while ( list( $section, $stitle ) = each( $acp_titles ) )
		{
			if ( $selected[ $section ] )
			{
				$html .= "<option value='{$section}' selected='selected'>{$stitle}</option>\n";
			}
			else
			{
				$html .= "<option value='{$section}'>{$stitle}</option>\n";
			}

			if ( is_array( $acp_pages[ $section ] ) )
			{
				while ( list( $page, $ptitle ) = each( $acp_pages[ $section ] ) )
				{
					if ( $selected[ $section .'_'. $page ] )
					{
						$html .= "<option value='{$section}_{$page}' selected='selected'>&nbsp;&nbsp;&#0124;-- {$ptitle}</option>\n";
					}
					else
					{
						$html .= "<option value='{$section}_{$page}'>&nbsp;&nbsp;&#0124;-- {$ptitle}</option>\n";
					}

					if ( is_array( $acp_subs[ $page ] ) )
					{
						while ( list( $sub, $btitle ) = each( $acp_subs[ $page ] ) )
						{
							if ( $selected[ $section .'_'. $page .'_'. $sub ] )
							{
								$html .= "<option value='{$section}_{$page}_{$sub}' selected='selected'>&nbsp;&nbsp;&#0124;---- {$btitle}</option>\n";

							}
							else
							{
								$html .= "<option value='{$section}_{$page}_{$sub}'>&nbsp;&nbsp;&#0124;---- {$btitle}</option>\n";
							}
						}
					}
				}
			}
		}

		return $html;
	}

}

?>