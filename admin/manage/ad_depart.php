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
|    | Admin Departments
#======================================================
*/

class ad_depart {

	#=======================================
	# @ Auto Run
	# Function that is run automatically
	# when the file is required.
	#=======================================

	function auto_run()
	{
		if ( ! $this->ifthd->member['acp']['manage_depart'] )
		{
			$this->ifthd->skin->error('no_perm');
		}
		
		$this->ifthd->skin->set_section( 'Ticket Control' );		
		$this->ifthd->skin->set_description( 'Manage your tickets,  departments, custom department fields and canned replies.' );

		switch( $this->ifthd->input['code'] )
    	{
    		case 'list':
				$this->list_departs();
    		break;
    		case 'reorder':
				$this->reorder_departs();
    		break;
    		case 'add':
				$this->add_depart();
    		break;
    		case 'edit':
    			$this->edit_depart();
    		break;
    		case 'delete':
    			$this->delete_depart();
    		break;

    		case 'doreorder':
				$this->do_reorder();
    		break;
    		case 'doadd':
    			$this->do_create();
    		break;
    		case 'doedit':
    			$this->do_edit();
    		break;
    		case 'dodel':
    			$this->do_delete();
    		break;

    		default:
    			$this->list_departs();
    		break;
		}
	}

	#=======================================
	# @ List Departmenets
	# Show a list of departmenets.
	#=======================================

	function list_departs($error='', $alert='')
	{
		#=============================
		# Grab Departments
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'departments',
											  	 'order'	=> array( 'position' => 'asc' ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$depart_rows = ""; // Initialize for Security
		$row_count = 0; // Initialize for Security

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			while( $d = $this->ifthd->core->db->fetch_row() )
			{
				$row_count ++;
				
				( $row_count & 1 ) ? $row_class = 'option1-med' : $row_class = 'option2-med';
				
				#=============================
				# Fix Up Information
				#=============================

				$d['description'] = $this->ifthd->shorten_str( $d['description'], 80, 1 );

				$depart_rows .= "<tr>
									<td class='{$row_class}'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=list&amp;depart={$d['id']}'>{$d['id']}</a></td>
									<td class='{$row_class}'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=list&amp;depart={$d['id']}'>{$d['name']}</a></td>
									<td class='{$row_class}' style='font-weight: normal'>{$d['description']}</td>
									<td class='{$row_class}' align='center'>{$d['tickets']}</td>
									<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=depart&amp;code=edit&amp;id={$d['id']}'><img src='<! IMG_DIR !>/button_edit.gif' alt='Edit' /></a></td>
									<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=depart&amp;code=delete&amp;id={$d['id']}'><img src='<! IMG_DIR !>/button_delete.gif' alt='Delete' /></a></td>
								</tr>";
			}
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

		$this->output = "{$error}
						<div class='groupbox'><div style='float:right'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=depart&amp;code=reorder' title='Reorder departments'><img src='<! IMG_DIR !>/button_mini_reorder.gif' alt='Reorder' /></a></div>Department List</div>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<th width='4%' align='left'>ID</th>
							<th width='22%' align='left'>Name</th>
							<th width='54%' align='left'>Description</th>
							<th width='8%'>Tickets</th>
							<th width='5%'>Edit</th>
							<th width='7%'>Delete</th>
						</tr>
						". $depart_rows ."
						</table>
						<div class='formtail'><div class='fb_pad'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=depart&amp;code=add' class='fake_button'>Add A New Department</a></div></div>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=depart'>Departments</a>",
						   "List Departments",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Departments' ) );
	}

	#=======================================
	# @ Add Department
	# Show add department form.
	#=======================================

	function add_depart($error="")
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['acp']['manage_depart_add'] )
		{
			$this->ifthd->skin->error('no_perm');
		}
		
		if ( file_exists( HD_SRC .'pop3.php' ) )
		{
			$pop3 = 1;
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

						</script>

						<form action='<! HD_URL !>/admin.php?section=manage&amp;act=depart&amp;code=doadd' method='post' onsubmit='return validate_form(this)'>
						{$error}
						<div class='groupbox'>Adding Department</div>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<td class='option1' width='25%'><label for='name'>Name</label></td>
							<td class='option1' width='75%'><input type='text' name='name' id='name' value='{$this->ifthd->input['name']}' size='35' /></td>
						</tr>
						<tr>
							<td class='option2' valign='top'><label for='description'>Description</label></td>
							<td class='option2'><textarea name='description' id='description' cols='50' rows='2'>{$this->ifthd->input['description']}</textarea></td>
						</tr>
						<tr>
							<td class='option1'>Auto Assign</td>
							<td class='option1' style='font-weight: normal'>
								<select name='auto_assign' id='auto_assign'><option value='0'>No one</option>". $this->ifthd->build_staff_drop( $this->ifthd->input['auto_assign'] ) ."</select>
							</td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info10','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Toggle information</a>
										<div id='info10' style='display: none;'>
										<div>
											Tickets submitted to this department will automatically be assigned to the above user.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}

		$this->output .= "<tr>
							<td class='option1'>Allow Escalation</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'can_escalate', $this->ifthd->input['can_escalate'] ) ."
							</td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info1','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Toggle information</a>
										<div id='info1' style='display: none;'>
										<div>
											Escalation places the ticket in a higher priority status and can also be moved to another department (see more options below).
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
							<td class='option2'>Escalate To</td>
							<td class='option2' style='font-weight: normal'>
								<select name='escalate_depart' id='escalate_depart'><option value='0'>None</option>". $this->ifthd->build_dprt_drop( $this->ifthd->input['escalate_depart'], 0, 1 ) ."</select> (Optional)
							</td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info2','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Toggle information</a>
										<div id='info2' style='display: none;'>
										<div>
											When a ticket is escalated, you can also place it in another department, regardless of the department's group permissions.  Select a department to escalate ticket to, or select None to leave the ticket in its current department.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
							<td class='option1' width='25%'><label for='escalate_wait'>Escalate Wait Time</label></td>
							<td class='option1' width='75%' style='font-weight: normal'><input type='text' name='escalate_wait' id='escalate_wait' value='{$this->ifthd->input['escalate_wait']}' size='10' style='vertical-align: bottom;' /> (Hours)</td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info3','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Toggle information</a>
										<div id='info3' style='display: none;'>
										<div>
											This is the amount of time that must have passed since ticket submission before a ticket can be escalated.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
							<td class='option2' width='25%'><label for='auto_close'>Auto Close Wait Time</label></td>
							<td class='option2' width='75%' style='font-weight: normal'><input type='text' name='auto_close' id='auto_close' value='{$this->ifthd->input['auto_close']}' size='10' style='vertical-align: bottom;' /> (Hours) (Leave blank to disable)</td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info4','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Toggle information</a>
										<div id='info4' style='display: none;'>
										<div>
											Tickets can be automatically closed when in the Awaiting Client Action status.  Enter the amount of hours that must pass before a ticket is automatically closed.  Leave blank or enter 0 to disable.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
							<td class='option1'>Enable Email Piping</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'email_pipe', $this->ifthd->input['email_pipe'] ) ."
							</td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info5','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Toggle information</a>
										<div id='info5' style='display: none;'>
										<div>
											If set to yes, tickets can be submitted and replied to by sending an email to the specified email address below.  This requires additional setup with email forwarders.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
							<td class='option2' width='25%'><label for='incoming_email'>Incoming Email</label></td>
							<td class='option2' width='75%'><input type='text' name='incoming_email' id='incoming_email' value='{$this->ifthd->input['incoming_email']}' size='35' /></td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info6','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Toggle information</a>
										<div id='info6' style='display: none;'>
										<div>
											This is the email address that tickets and replies will be sent to for email piping.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		if ( $pop3 )
		{
			$this->output .= "<tr>
							<td class='option1'>Enable POP3 Checking</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'email_pop3', $this->ifthd->input['email_pop3'] ) ."
							</td>
						</tr>
						<tr>
							<td class='option2' width='25%'><label for='pop3_host'>Mail Server Address</label></td>
							<td class='option2' width='75%'><input type='text' name='pop3_host' id='pop3_host' value='{$this->ifthd->input['pop3_host']}' size='35' /> *</td>
						</tr>
						<tr>
							<td class='option1' width='25%'><label for='pop3_user'>Username</label></td>
							<td class='option1' width='75%'><input type='text' name='pop3_user' id='pop3_user' value='{$this->ifthd->input['pop3_user']}' size='35' /> *</td>
						</tr>
						<tr>
							<td class='option2' width='25%'><label for='pop3_pass'>Password</label></td>
							<td class='option2' width='75%'><input type='text' name='pop3_pass' id='pop3_pass' value='{$this->ifthd->input['pop3_pass']}' size='35' /> *</td>
						</tr>";
		}
		
		$this->output .= "<tr>
							<td class='option1'>Enable Guest Emails</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'guest_pipe', $this->ifthd->input['guest_pipe'] ) ."
							</td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info7','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Toggle information</a>
										<div id='info7' style='display: none;'>
										<div>
											If set to yes, guests (users who's email address is not registered) will be allowed to create tickets via email piping.  Guests must also have permission to this department (see below).
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
							<td class='option2'>Can Close Own Tickets</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'ticket_own_close', $this->ifthd->input['ticket_own_close'] ) ."
							</td>
						</tr>
						<tr>
							<td class='option2'>Can Reopen Own Tickets</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'ticket_own_reopen', $this->ifthd->input['ticket_own_reopen'] ) ."
							</td>
						</tr>
						<tr>
							<td class='option1'>Require Close Reason</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'close_reason', $this->ifthd->input['close_reason'] ) ."
							</td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info8','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Toggle information</a>
										<div id='info8' style='display: none;'>
										<div>
											If set to yes, a reason must be entered for the closing of each ticket.  The close reason will be displayed on the view ticket page.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
							<td class='option2'>Allow Attachments</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'can_attach', $his->ifthd->input['can_attach'] ) ."
							</td>
						</tr>
						<tr>
							<td class='option1' valign='top'>Group Permissions</td>
							<td class='option1'>
								<select name='group_perm[]' id='group_perm' size='5' multiple='multiple'>
								". $this->ifthd->build_group_drop( $this->ifthd->input['group_perm'] ) ."
								</select>
							</td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info9','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Toggle information</a>
										<div id='info9' style='display: none;'>
										<div>
											Select the groups that has permission to create tickets in this department.  This only applies for ticket creation.  If a ticket is moved to a department in which the ticket owner does not have permission to, they will still be able to access the ticket.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "</table>
						<div class='formtail'><input type='submit' name='submit' id='add' value='Add Department' class='button' /></div>";
		
		if ( $pop3 ) $this->output .= "<div class='option1'>* Only applies when POP3 is enabled.</div>";
		
		$this->output .= "</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=depart'>Departments</a>",
						   "Add Department",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Departments' ) );
	}

	#=======================================
	# @ Edit Department
	# Show edit department form.
	#=======================================

	function edit_depart($error="")
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_depart_edit'] )
		{
			$this->ifthd->skin->error('no_perm');
		}
		
		if ( file_exists( HD_SRC .'pop3.php' ) )
		{
			$pop3 = 1;
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'departments',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_department');
		}

		$d = $this->ifthd->core->db->fetch_row();

		#=============================
		# Do Output
		#=============================

		if ( $error )
		{
			$error = "<div class='critical'>{$error}</div>";

			$name = $this->ifthd->input['name'];
			$description = $this->ifthd->input['description'];
			$can_escalate = $this->ifthd->input['can_escalate'];
			$escalate_depart = $this->ifthd->input['escalate_depart'];
			$escalate_wait = $this->ifthd->input['escalate_wait'];
			$auto_close = $this->ifthd->input['auto_close'];
			$email_pipe = $this->ifthd->input['email_pipe'];
			$guest_pipe = $this->ifthd->input['guest_pipe'];
			$incoming_email = $this->ifthd->input['incoming_email'];
			$email_pop3 = $this->ifthd->input['email_pop3'];
			$pop3_host = $this->ifthd->input['pop3_host'];
			$pop3_user = $this->ifthd->input['pop3_user'];
			$pop3_pass = $this->ifthd->input['pop3_pass'];
			$ticket_own_close = $this->ifthd->input['ticket_own_close'];
			$ticket_own_reopen = $this->ifthd->input['ticket_own_reopen'];
			$close_reason = $this->ifthd->input['close_reason'];
			$can_attach = $this->ifthd->input['can_attach'];
			$auto_assign = $this->ifthd->input['auto_assign'];

			$group_perm = $this->ifthd->input['group_perm'];
		}
		else
		{
			$name = $d['name'];
			$description = $d['description'];
			$can_escalate = $d['can_escalate'];
			$escalate_depart = $d['escalate_depart'];
			$escalate_wait = $d['escalate_wait'];
			$auto_close = $d['auto_close'];
			$email_pipe = $d['email_pipe'];
			$guest_pipe = $d['guest_pipe'];
			$incoming_email = $d['incoming_email'];
			$email_pop3 = $d['email_pop3'];
			$pop3_host = $d['pop3_host'];
			$pop3_user = $d['pop3_user'];
			$pop3_pass = $d['pop3_pass'];
			$ticket_own_close = $d['ticket_own_close'];
			$ticket_own_reopen = $d['ticket_own_reopen'];
			$close_reason = $d['close_reason'];
			$can_attach = $d['can_attach'];
			$auto_assign = $d['auto_assign'];

			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'g_id', 'g_m_depart_perm' ),
												  	 'from'		=> 'groups',
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			if ( $this->ifthd->core->db->get_num_rows() )
			{
				while ( $g = $this->ifthd->core->db->fetch_row() )
				{
					$g_temp_perm = unserialize( $g['g_m_depart_perm'] );

					if ( $g_temp_perm[ $d['id'] ] )
					{
						$group_perm[] = $g['g_id'];
					}
				}
			}
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

						</script>

						<form action='<! HD_URL !>/admin.php?section=manage&amp;act=depart&amp;code=doedit&amp;id={$d['id']}' method='post' onsubmit='return validate_form(this)'>
						{$error}
						<div class='groupbox'>Editing Department: {$d['name']}</div>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<td class='option1' width='25%'><label for='name'>Name</label></td>
							<td class='option1' width='75%'><input type='text' name='name' id='name' value='{$name}' size='35' /></td>
						</tr>
						<tr>
							<td class='option2' valign='top'><label for='description'>Description</label></td>
							<td class='option2'><textarea name='description' id='description' cols='50' rows='2'>{$description}</textarea></td>
						</tr>
						<tr>
							<td class='option1'>Auto Assign</td>
							<td class='option1' style='font-weight: normal'>
								<select name='auto_assign' id='auto_assign'><option value='0'>No one</option>". $this->ifthd->build_staff_drop( $auto_assign ) ."</select>
							</td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info10','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Toggle information</a>
										<div id='info10' style='display: none;'>
										<div>
											Tickets submitted to this department will automatically be assigned to the above user.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}

		$this->output .= "<tr>
							<td class='option1'>Allow Escalation</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'can_escalate', $can_escalate ) ."
							</td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info1','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Toggle information</a>
										<div id='info1' style='display: none;'>
										<div>
											Escalation places the ticket in a higher priority status and can also be moved to another department (see more options below).
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
							<td class='option2'>Escalate To</td>
							<td class='option2' style='font-weight: normal'>
								<select name='escalate_depart' id='escalate_depart'><option value='0'>None</option>". $this->ifthd->build_dprt_drop( $escalate_depart, $d['id'], 1 ) ."</select> (Optional)
							</td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info2','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Toggle information</a>
										<div id='info2' style='display: none;'>
										<div>
											When a ticket is escalated, you can also place it in another department, regardless of the department's group permissions.  Select a department to escalate ticket to, or select None to leave the ticket in its current department.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
							<td class='option1' width='25%'><label for='escalate_wait'>Escalate Wait Time</label></td>
							<td class='option1' width='75%' style='font-weight: normal'><input type='text' name='escalate_wait' id='escalate_wait' value='{$escalate_wait}' size='10' style='vertical-align: bottom;' /> (Hours)</td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info3','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Toggle information</a>
										<div id='info3' style='display: none;'>
										<div>
											This is the amount of time that must have passed since ticket submission before a ticket can be escalated.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
							<td class='option2' width='25%'><label for='auto_close'>Auto Close Wait Time</label></td>
							<td class='option2' width='75%' style='font-weight: normal'><input type='text' name='auto_close' id='auto_close' value='{$auto_close}' size='10' style='vertical-align: bottom;' /> (Hours) (Leave blank to disable)</td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info4','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Toggle information</a>
										<div id='info4' style='display: none;'>
										<div>
											Tickets can be automatically closed when in the Awaiting Client Action status.  Enter the amount of hours that must pass before a ticket is automatically closed.  Leave blank or enter 0 to disable.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
							<td class='option1'>Enable Email Piping</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'email_pipe', $email_pipe ) ."
							</td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info5','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Toggle information</a>
										<div id='info5' style='display: none;'>
										<div>
											If set to yes, tickets can be submitted and replied to by sending an email to the specified email address below.  This requires additional setup with email forwarders.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
							<td class='option2' width='25%'><label for='incoming_email'>Incoming Email</label></td>
							<td class='option2' width='75%'><input type='text' name='incoming_email' id='incoming_email' value='{$incoming_email}' size='35' /></td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info6','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Toggle information</a>
										<div id='info6' style='display: none;'>
										<div>
											This is the email address that tickets and replies will be sent to for email piping.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		if ( $pop3 )
		{
			$this->output .= "<tr>
							<td class='option1'>Enable POP3 Checking</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'email_pop3', $email_pop3 ) ."
							</td>
						</tr>
						<tr>
							<td class='option2' width='25%'><label for='pop3_host'>Mail Server Address</label></td>
							<td class='option2' width='75%'><input type='text' name='pop3_host' id='pop3_host' value='{$pop3_host}' size='35' /> *</td>
						</tr>
						<tr>
							<td class='option1' width='25%'><label for='pop3_user'>Username</label></td>
							<td class='option1' width='75%'><input type='text' name='pop3_user' id='pop3_user' value='{$pop3_user}' size='35' /> *</td>
						</tr>
						<tr>
							<td class='option2' width='25%'><label for='pop3_pass'>Password</label></td>
							<td class='option2' width='75%'><input type='text' name='pop3_pass' id='pop3_pass' value='{$pop3_pass}' size='35' /> *</td>
						</tr>";
		}
		
		$this->output .= "<tr>
							<td class='option1'>Enable Guest Emails</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'guest_pipe', $guest_pipe ) ."
							</td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info7','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Toggle information</a>
										<div id='info7' style='display: none;'>
										<div>
											If set to yes, guests (users who's email address is not registered) will be allowed to create tickets via email piping.  Guests must also have permission to this department (see below).
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
							<td class='option2'>Can Close Own Tickets</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'ticket_own_close', $ticket_own_close ) ."
							</td>
						</tr>
						<tr>
							<td class='option2'>Can Reopen Own Tickets</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'ticket_own_reopen', $ticket_own_reopen ) ."
							</td>
						</tr>
						<tr>
							<td class='option1'>Require Close Reason</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'close_reason', $close_reason ) ."
							</td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info8','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Toggle information</a>
										<div id='info8' style='display: none;'>
										<div>
											If set to yes, a reason must be entered for the closing of each ticket.  The close reason will be displayed on the view ticket page.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
							<td class='option2'>Allow Attachments</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'can_attach', $can_attach ) ."
							</td>
						</tr>
						<tr>
							<td class='option1' valign='top'>Group Permissions</td>
							<td class='option1'>
								<select name='group_perm[]' id='group_perm' size='5' multiple='multiple'>
								". $this->ifthd->build_group_drop( $group_perm ) ."
								</select>
							</td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info9','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Toggle information</a>
										<div id='info9' style='display: none;'>
										<div>
											Select the groups that has permission to create tickets in this department.  This only applies for ticket creation.  If a ticket is moved to a department in which the ticket owner does not have permission to, they will still be able to access the ticket.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "</table>
						<div class='formtail'><input type='submit' name='submit' id='edit' value='Edit Department' class='button' /></div>";
		
		if ( $pop3 ) $this->output .= "<div class='option1'>* Only applies when POP3 is enabled.</div>";
		
		$this->output .= "</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=depart'>Departments</a>",
						   "Edit Department",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Departments' ) );
	}

	#=======================================
	# @ Delete Department
	# Show delete department form.
	#=======================================

	function delete_depart()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_depart_delete'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'departments',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_department');
		}

		$d = $this->ifthd->core->db->fetch_row();

		#=============================
		# Do Output
		#=============================

		$depart_drop = $this->ifthd->build_dprt_drop( 0, $d['id'], 1 );

		$this->output = "<form action='<! HD_URL !>/admin.php?section=manage&amp;act=depart&amp;code=dodel&amp;id={$d['id']}' method='post'>
						<div class='groupbox'>Deleting Department: {$d['name']}</div>
						<div class='subbox'>What would you like to do with the tickets in this department?</div>
						<div class='option1'><input type='radio' name='action' id='action1' value='1' checked='checked' /> <label for='action1'>Move the tickets to this department:</label> <select name='moveto'>{$depart_drop}</select></div>
						<div class='option2'><input type='radio' name='action' id='action2' value='2' /> <label for='action2'>Delete the tickets</label></div>
						<div class='formtail'><input type='submit' name='submit' id='delete' value='Delete Department' class='button' /></div>
						</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=depart'>Departments</a>",
						   "Delete Department",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Departments' ) );
	}

	#=======================================
	# @ Do Create
	# Create a new department.
	#=======================================

	function do_create()
	{
		#=============================
		# Security Checks
		#=============================
		
		if ( file_exists( HD_SRC .'pop3.php' ) )
		{
			$pop3 = 1;
		}

		if ( ! $this->ifthd->member['acp']['manage_depart_add'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		if ( ! $this->ifthd->input['name'] )
		{
			$this->add_depart('Please enter a name.');
		}

		if ( ! $this->ifthd->input['description'] )
		{
			$this->add_depart('Please enter a description.');
		}

		if ( ! is_array( $this->ifthd->input['group_perm'] ) )
		{
			$this->add_depart('Please select some group permissions.');
		}

		if ( $this->ifthd->input['email_pipe'] )
		{
			if ( ! $this->ifthd->validate_email( $this->ifthd->input['incoming_email'] ) )
			{
				$this->add_depart('Please enter a valid incoming email address.');
			}

			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'id' ),
												  	 'from'		=> 'departments',
								 				  	 'where'	=> array( 'incoming_email', '=', $this->ifthd->input['incoming_email'] ),
								 				  	 'limit'	=> array( 0,1 ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			if ( $this->ifthd->core->db->get_num_rows() )
			{
				$this->add_depart('That incoming email address is already used for another department.  Please choose a different email.');
			}
		}

		#=============================
		# Add Department
		#=============================

		$db_array = array(
						  'name'				=> $this->ifthd->input['name'],
						  'description'			=> $this->ifthd->input['description'],
						  'can_escalate'		=> $this->ifthd->input['can_escalate'],
						  'escalate_depart'		=> $this->ifthd->input['escalate_depart'],
						  'escalate_wait'		=> $this->ifthd->input['escalate_wait'],
						  'auto_close'			=> $this->ifthd->input['auto_close'],
						  'ticket_own_close'	=> $this->ifthd->input['ticket_own_close'],
						  'ticket_own_reopen'	=> $this->ifthd->input['ticket_own_reopen'],
						  'close_reason'		=> $this->ifthd->input['close_reason'],
						  'can_attach'			=> $this->ifthd->input['can_attach'],
						  'email_pipe'			=> $this->ifthd->input['email_pipe'],
						  'guest_pipe'			=> $this->ifthd->input['guest_pipe'],
						  'incoming_email'		=> $this->ifthd->input['incoming_email'],
						  'auto_assign'			=> $this->ifthd->input['auto_assign'],
						 );
						 
		if ( $pop3 )
		{
			$db_array['email_pop3'] = $this->ifthd->input['email_pop3'];
			$db_array['pop3_host'] = $this->ifthd->input['pop3_host'];
			$db_array['pop3_user'] = $this->ifthd->input['pop3_user'];
			$db_array['pop3_pass'] = $this->ifthd->input['pop3_pass'];
		}

		$this->ifthd->core->db->construct( array(
											  	 'insert'	=> 'departments',
											  	 'set'		=> $db_array,
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$depart_id = $this->ifthd->core->db->get_insert_id();

		$this->ifthd->log( 'admin', "Department Added &#039;". $this->ifthd->input['name'] ."&#039;", 1, $depart_id );

		#=============================
		# Generate Permissions
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'g_id', 'g_m_depart_perm' ),
											  	 'from'		=> 'groups',
							 				  	 'where'	=> array( 'g_id', 'in', $this->ifthd->input['group_perm'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			while ( $g = $this->ifthd->core->db->fetch_row() )
			{
				$temp_perm[ $g['g_id'] ] = unserialize( $g['g_m_depart_perm'] );

				$temp_perm[ $g['g_id'] ][ $depart_id ] = 1;
			}
		}

		while ( list( $gid, $g_perm ) = each( $temp_perm ) )
		{
			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'groups',
												  	 'set'		=> array( 'g_m_depart_perm' => serialize($g_perm) ),
								 				  	 'where'	=> array( 'g_id', '=', $gid ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();
		}

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_dprt_cache();
		$this->ifthd->rebuild_group_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=depart&code=list', 'add_depart_success' );
		$this->list_departs( '', 'The department has been successfully added.' );
	}

	#=======================================
	# @ Do Edit
	# Edit a department.
	#=======================================

	function do_edit()
	{
		#=============================
		# Security Checks
		#=============================
		
		if ( file_exists( HD_SRC .'pop3.php' ) )
		{
			$pop3 = 1;
		}

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_depart_edit'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'departments',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_department');
		}

		if ( ! $this->ifthd->input['name'] )
		{
			$this->edit_depart('Please enter a name.');
		}

		if ( ! $this->ifthd->input['description'] )
		{
			$this->edit_depart('Please enter a description.');
		}

		if ( ! is_array( $this->ifthd->input['group_perm'] ) )
		{
			$this->edit_depart('Please select some group permissions.');
		}

		if ( $this->ifthd->input['email_pipe'] )
		{
			if ( ! $this->ifthd->validate_email( $this->ifthd->input['incoming_email'] ) )
			{
				$this->edit_depart('Please enter a valid incoming email address.');
			}

			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'id' ),
												  	 'from'		=> 'departments',
								 				  	 'where'	=> array( array( 'incoming_email', '=', $this->ifthd->input['incoming_email'] ), array( 'id', '!=', $this->ifthd->input['id'], 'and' ) ),
								 				  	 'limit'	=> array( 0,1 ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			if ( $this->ifthd->core->db->get_num_rows() )
			{
				$this->edit_depart('That incoming email address is already used for another department.  Please choose a different email.');
			}
		}

		#=============================
		# Edit Department
		#=============================

		$db_array = array(
						  'name'				=> $this->ifthd->input['name'],
						  'description'			=> $this->ifthd->input['description'],
						  'can_escalate'		=> $this->ifthd->input['can_escalate'],
						  'escalate_depart'		=> $this->ifthd->input['escalate_depart'],
						  'escalate_wait'		=> $this->ifthd->input['escalate_wait'],
						  'auto_close'			=> $this->ifthd->input['auto_close'],
						  'ticket_own_close'	=> $this->ifthd->input['ticket_own_close'],
						  'ticket_own_reopen'	=> $this->ifthd->input['ticket_own_reopen'],
						  'close_reason'		=> $this->ifthd->input['close_reason'],
						  'can_attach'			=> $this->ifthd->input['can_attach'],
						  'email_pipe'			=> $this->ifthd->input['email_pipe'],
						  'guest_pipe'			=> $this->ifthd->input['guest_pipe'],
						  'incoming_email'		=> $this->ifthd->input['incoming_email'],
						  'auto_assign'			=> $this->ifthd->input['auto_assign'],
						 );
						 
		if ( $pop3 )
		{
			$db_array['email_pop3'] = $this->ifthd->input['email_pop3'];
			$db_array['pop3_host'] = $this->ifthd->input['pop3_host'];
			$db_array['pop3_user'] = $this->ifthd->input['pop3_user'];
			$db_array['pop3_pass'] = $this->ifthd->input['pop3_pass'];
		}

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'departments',
											  	 'set'		=> $db_array,
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'admin', "Department Edited &#039;". $this->ifthd->input['name'] ."&#039;", 1, $this->ifthd->input['id'] );

		#=============================
		# Generate Permissions
		#=============================

		if ( is_array( $this->ifthd->input['group_perm'] ) )
		{
			while ( list( , $mdperm ) = each( $this->ifthd->input['group_perm'] ) )
			{
				$m_depart_perm[ $mdperm ] = 1;
			}
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'g_id', 'g_m_depart_perm' ),
											  	 'from'		=> 'groups',
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			while ( $g = $this->ifthd->core->db->fetch_row() )
			{
				$temp_perm[ $g['g_id'] ] = unserialize( $g['g_m_depart_perm'] );

			}
		}

		while ( list( $gid, $g_perm ) = each( $temp_perm ) )
		{
			if ( $m_depart_perm[ $gid ] )
			{
				$g_perm[ $this->ifthd->input['id'] ] = 1;
			}
			else
			{
				$g_perm[ $this->ifthd->input['id'] ] = 0;
			}

			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'groups',
												  	 'set'		=> array( 'g_m_depart_perm' => serialize($g_perm) ),
								 				  	 'where'	=> array( 'g_id', '=', $gid ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();
		}

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_dprt_cache();
		$this->ifthd->rebuild_group_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=depart&code=list', 'edit_depart_success' );
		$this->list_departs( '', 'The department has been successfully updated.' );
	}

	#=======================================
	# @ Do Delete
	# Delete a department.
	#=======================================

	function do_delete()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_depart_delete'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'departments',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_department');
		}

		$da = $this->ifthd->core->db->fetch_row();

		#=============================
		# Perform Our Action
		#=============================

		if ( $this->ifthd->input['action'] == 1 )
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> 'all',
												  	 'from'		=> 'departments',
								 				  	 'where'	=> array( 'id', '=', $da['id'] ),
								 				  	 'limit'	=> array( 0,1 ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			if ( ! $this->ifthd->core->db->get_num_rows() )
			{
				$this->ifthd->skin->error('no_department');
			}

			$d = $this->ifthd->core->db->fetch_row();

			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'tickets',
												  	 'set'		=> array( 'did' => $d['id'], 'dname' => $d['name'] ),
								 				  	 'where'	=> array( 'did', '=', $da['id'] ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();
		}
		elseif ( $this->ifthd->input['action'] == 2 )
		{
			$this->ifthd->core->db->construct( array(
												  	 'delete'	=> 'tickets',
								 				  	 'where'	=> array( 'did', '=', $da['id'] ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();
		}

		#=============================
		# Delete Department
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'delete'	=> 'departments',
							 				  	 'where'	=> array( 'id', '=', $da['id'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'admin', "Department Deleted &#039;". $da['name'] ."&#039;", 2, $da['id'] );

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_dprt_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=depart&code=list', 'delete_depart_success' );
		$this->list_departs( 'The department has been successfully deleted.' );
	}

	#=======================================
	# @ Reorder Departmenets
	# Show reoarder departments form.
	#=======================================

	function reorder_departs()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['acp']['manage_depart_reorder'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		#=============================
		# Grab Departments
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'departments',
											  	 'order'	=> array( 'position' => 'asc' ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$depart_rows = ""; // Initialize for Security
		$row_count = 0; // Initialize for Security

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			while( $d = $this->ifthd->core->db->fetch_row() )
			{
				$row_count ++;
				
				( $row_count & 1 ) ? $row_class = 'option1-med' : $row_class = 'option2-med';
				
				#=============================
				# Fix Up Information
				#=============================

				$d['description'] = $this->ifthd->shorten_str( $d['description'], 80, 1 );

				if ( $error )
				{
					$cur_pos = $this->ifthd->input[ 'pos_'. $d['id'] ];
				}
				else
				{
					$cur_pos = $d['position'];
				}

				$depart_rows .= "<div id='d_{$d['id']}' class='{$row_class}' style='cursor:move'>{$d['name']} (<span class='desc'>{$d['description']}</span>)</div>";
			}
		}

		#=============================
		# Do Output
		#=============================

		$this->output = "<form action='<! HD_URL !>/admin.php?section=manage&amp;act=depart&amp;code=doreorder' method='post' onsubmit='get_order()'>
						<input type='hidden' name='order' id='order' value='' />
						<div class='groupbox'>Reordering Departments</div>
						<div class='subbox'>To reorder departments, simply click and drag the department to the desired position.</div>
						<div id='draggable'>
						". $depart_rows ."
						</div>
						<div class='formtail'><input type='submit' name='submit' id='reorder' value='Reorder Departments' class='button' /></div>
						</form>
						
						<script type='text/javascript' language='javascript'>
							Sortable.create( 'draggable', {tag:'div',constraint:'vertical'} )
							
							function get_order() {
								order = get_by_id('order');
								order.value = Sortable.serialize('draggable');
							}
						</script>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=depart'>Departments</a>",
						   "Reorder Departments",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Departments' ) );
	}



	#=======================================
	# @ Do Reorder
	# Reorder departments.
	#=======================================

	function do_reorder()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['acp']['manage_depart_reorder'] )
		{
			$this->ifthd->skin->error('no_perm');
		}
		
		$raw_order = str_replace( '&amp;', '&', $this->ifthd->input['order'] );
		
		parse_str( $raw_order, $order );

		$final_order = array(); // Initialize for Security
		$depart_count = 0; // Initialize for Security
		
		while( list( , $did ) = each( $order['draggable'] ) )
		{
			$depart_count ++;
			
			$final_order[ $did ] = $depart_count;
		}

		#=============================
		# Reorder Departments
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'departments',
											  	 'order'	=> array( 'position' => 'asc' ),
							 		  	  ) 	);

		$sel_dep = $this->ifthd->core->db->execute();

		$depart_rows = ""; // Initialize for Security

		if ( $this->ifthd->core->db->get_num_rows($sel_dep) )
		{
			while( $d = $this->ifthd->core->db->fetch_row($sel_dep) )
			{
				if ( $d['position'] != intval( $final_order[ $d['id'] ] ) )
				{
					$this->ifthd->core->db->construct( array(
														  	 'update'	=> 'departments',
														  	 'set'		=> array( 'position' => intval( $final_order[ $d['id'] ] ) ),
														  	 'where'	=> array( 'id', '=', $d['id'] ),
										 		  	  ) 	);

					$this->ifthd->core->db->execute();
				}
			}
		}

		$this->ifthd->log( 'admin', "Departments Reordered" );

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_dprt_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=depart&code=list', 'reorder_depart_success' );
		$this->list_departs( '', 'The departments have been successfully reordered. ');
	}

}

?>