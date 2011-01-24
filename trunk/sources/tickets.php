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
|    | Tickets :: Sources
#======================================================
*/

class tickets {

	#=======================================
	# @ Auto Run
	# Function that is run automatically
	# when the file is required.
	#=======================================

	function auto_run()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['g_ticket_access'] || $this->ifthd->member['ban_ticket_center'] )
		{
			if ( $this->ifthd->member['id'] )
			{
				$this->ifthd->log( 'security', "Blocked Access Ticket Center" );

				$this->ifthd->skin->error('banned_ticket');
			}
			else
			{
				$this->ifthd->log( 'security', "Blocked Guest Access Ticket Center" );

				$this->ifthd->skin->error( 'must_be_user', 1 );
			}
		}

		#=============================
		# Initialize
		#=============================

		$this->ifthd->load_lang('tickets');

		switch( $this->ifthd->input['code'] )
    	{
    		case 'open':
    			$this->new_form();
    		break;
    		case 'view':
    			$this->view_ticket();
    		break;
    		case 'edit':
    			$this->edit_ticket();
    		break;
    		case 'history':
    			$this->show_history();
    		break;
    		case 'close':
    			$this->ticket_action('close');
    		break;
    		case 'escalate':
    			$this->ticket_action('escalate');
    		break;
    		case 'attachment':
    			$this->download_attachment();
    		break;
    		case 'print':
    			$this->view_ticket( 0, 'print');
    		break;

    		case 'editreply':
    			$this->edit_reply();
    		break;

    		case 'doeditreply':
    			$this->do_reply_edit();
    		break;
    		case 'dodelreply':
    			$this->do_reply_delete();
    		break;

    		case 'submit':
    			$this->do_pre_search();
    		break;
    		case 'reply':
    			$this->submit_reply();
    		break;
    		case 'doedit':
    			$this->do_edit_ticket();
    		break;
    		case 'rate':
				$this->do_rate();
    		break;

    		default:
    			$this->show_history();
    		break;
		}
	}

	#=======================================
	# @ New Form
	# Show the new ticket form.
	#=======================================

	function new_form($error="", $extra="")
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->core->cache['config']['allow_new_tickets'] )
		{
			$this->ifthd->skin->error('new_tickets_disabled');
		}

		if ( ! $this->ifthd->member['g_new_tickets'] || $this->ifthd->member['ban_ticket_open'] )
		{
			$this->ifthd->log( 'security', "Blocked New Ticket" );

			$this->ifthd->skin->error('banned_ticket_open');
		}

		if ( $this->ifthd->input['step'] == 2 || $error )
		{
			if ( ! $error ) $this->ifthd->check_token('ticket_sub_a');

			#=============================
			# Initialize
			#=============================

			if ( ! $this->ifthd->input['department'] )
			{
				$this->ifthd->core->template->set_var( 'error', $this->ifthd->lang[ 'err_no_depart' ] );
				
				$this->ifthd->core->template->set_var( 'depart_opts', $this->ifthd->build_dprt_drop( $this->ifthd->input['department'], 0, 0, 2 ) );

				$this->ifthd->core->template->set_var( 'token_sub_a', $this->ifthd->create_token('ticket_sub_a') );

				$this->ifthd->core->template->set_var( 'sub_tpl', 'tck_submit_1.tpl' );
			}
			else
			{
				$this->ifthd->core->template->set_var( 'priority_drop', $this->ifthd->build_priority_drop( $this->ifthd->input['priority'] ) );

				#=============================
				# Custom Profile Fields
				#=============================

				if ( is_array( $this->ifthd->core->cache['dfields'] ) )
				{
					$cdfields = array(); // Initialize for Security
					$row_count = 0; // Initialize for Security

					foreach( $this->ifthd->core->cache['dfields'] as $id => $f )
					{
						$f_perm = unserialize( $f['departs'] );

						if ( $f_perm[ $this->ifthd->input['department'] ] )
						{
							$row_count ++;
							
							( $row_count & 1 ) ? $f['class'] = 1 : $f['class'] = 2;
							
							if ( ! $f['required'] )
							{
								$f['optional'] = $this->ifthd->lang['optional'];
							}

							if ( $error )
							{
								$f['value'] = $this->ifthd->input[ 'cdf_'. $f['fkey'] ];
							}

							if ( $f['type'] == 'textfield' )
							{
								$cdfields[] = $f;
							}
							elseif ( $f['type'] == 'textarea' )
							{
								$cdfields[] = $f;
							}
							elseif ( $f['type'] == 'dropdown' )
							{
								$options = explode( "\n", $f['extra'] );

								while ( list( , $opt ) = each( $options ) )
								{
									$our_opt = explode( "=", $opt );

									if ( $our_opt[0] == $f['value'] )
									{
										$f['options'] .= "<option value='". $our_opt[0] ."' selected='selected'>". $our_opt[1] ."</option>";
									}
									else
									{
										$f['options'] .= "<option value='". $our_opt[0] ."'>". $our_opt[1] ."</option>";
									}
								}

								$cdfields[] = $f;
							}
							elseif ( $f['type'] == 'checkbox' )
							{
								$cdfields[] = $f;
							}
							elseif ( $f['type'] == 'radio' )
							{
								$options = explode( "\n", $f['extra'] );

								while ( list( , $opt ) = each( $options ) )
								{
									$our_opt = explode( "=", $opt );

									if ( $our_opt[0] == $f['value'] )
									{
										$f['options'] .= "<label for='cdf_". $f['fkey'] ."_". $our_opt[0] ."'><input type='radio' name='cdf_". $f['fkey'] ."' id='cdf_". $f['fkey'] ."_". $our_opt[0] ."' value='". $our_opt[0] ."' class='radio' checked='checked' /> ". $our_opt[1] ."</label>&nbsp;&nbsp;";
									}
									else
									{
										$f['options'] .= "<label for='cdf_". $f['fkey'] ."_". $our_opt[0] ."'><input type='radio' name='cdf_". $f['fkey'] ."' id='cdf_". $f['fkey'] ."_". $our_opt[0] ."' value='". $our_opt[0] ."' class='radio' /> ". $our_opt[1] ."</label>&nbsp;&nbsp;";
									}
								}

								$cdfields[] = $f;
							}
						}

						$optional = ""; // Reset
						$f['options'] = ""; // Reset
					}

					$this->ifthd->core->template->set_var( 'cdfields', $cdfields );
				}

				#=============================
				# Do Output
				#=============================
				
				$row_count ++;
				( $row_count & 1 ) ? $row_class = 1 : $row_class = 2;				
				$this->ifthd->core->template->set_var( 'class_msg', $row_class );
				
				$row_count ++;
				( $row_count & 1 ) ? $row_class = 1 : $row_class = 2;	
				$this->ifthd->core->template->set_var( 'class_attach', $row_class );
				
				$row_count ++;
				( $row_count & 1 ) ? $row_class = 1 : $row_class = 2;	
				$this->ifthd->core->template->set_var( 'class_captcha', $row_class );
				
				$row_count ++;
				( $row_count & 1 ) ? $row_class = 1 : $row_class = 2;	
				$this->ifthd->core->template->set_var( 'class_guest', $row_class );

				if ( $error )
				{
					$this->ifthd->core->template->set_var( 'error', $this->ifthd->lang[ 'err_'. $error ] );

					if ( $extra ) $this->ifthd->core->template->set_var( 'error_extra', $extra );
				}

				if ( $this->ifthd->core->cache['config']['ticket_attachments'] && $this->ifthd->member['g_ticket_attach'] && $this->ifthd->core->cache['depart'][ $this->ifthd->input['department'] ]['can_attach'] )
				{
					if ( $this->ifthd->member['g_upload_size_max'] )
					{
						$upload_info = ' ('. $this->ifthd->lang['attachment_max_size'] .': '. $this->ifthd->member['g_upload_size_max'] .' '. $this->ifthd->lang['bytes'] .')';
					}
					else
					{
						$upload_info = ' '. $this->ifthd->lang['attachment'];
					}

					$this->ifthd->core->template->set_var( 'upload_info', $upload_info );
				}

				$this->ifthd->core->template->set_var( 'token_sub_b', $this->ifthd->create_token('ticket_sub_b') );

				$this->ifthd->core->template->set_var( 'sub_tpl', 'tck_submit_2.tpl' );
			}
		}
		else
		{
			$this->ifthd->core->template->set_var( 'depart_opts', $this->ifthd->build_dprt_drop( $this->ifthd->input['department'], 0, 0, 2 ) );

			$this->ifthd->core->template->set_var( 'token_sub_a', $this->ifthd->create_token('ticket_sub_a') );

			$this->ifthd->core->template->set_var( 'sub_tpl', 'tck_submit_1.tpl' );
		}

		$this->nav = array(
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=tickets'>{$this->ifthd->lang['tickets']}</a>",
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=tickets&amp;code=open'>{$this->ifthd->lang['open_ticket']}</a>",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => $this->ifthd->lang['tickets'] .' :: '. $this->ifthd->lang['open_ticket'] ) );
	}

	#=======================================
	# @ Do Pre Search
	# Performs a search through articles to
	# find articles that may answer the
	# user's question.
	#=======================================

	function do_pre_search($bypass=0, $attach_id=0)
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $bypass )
		{
			$this->ifthd->check_token('ticket_sub_b');

			if ( ! $this->ifthd->core->cache['config']['allow_new_tickets'] )
			{
				$this->ifthd->skin->error('new_tickets_disabled');
			}

			if ( ! $this->ifthd->member['g_new_tickets'] || $this->ifthd->member['ban_ticket_open'] )
			{
				$this->ifthd->log( 'security', "Blocked New Ticket" );

				$this->ifthd->skin->error('banned_ticket_open');
			}

			if ( ! $this->ifthd->input['subject'] )
			{
				$this->new_form('no_subject');
			}

			if ( ! $this->ifthd->input['message'] )
			{
				$this->new_form('no_message');
			}

			if ( ! $this->ifthd->member['id'] )
			{
				if ( ! $this->ifthd->input['name'] )
				{
					$this->new_form('no_name');
				}

				if ( ! $this->ifthd->validate_email( $this->ifthd->input['email'] ) )
				{
					$this->new_form('no_email');
				}

				$this->ifthd->core->db->construct( array(
													  	 'select'	=> array( 'id' ),
													  	 'from'		=> 'members',
													  	 'where'	=> array( 'email|lower', '=', strtolower( $this->ifthd->input['email'] ) ),
													  	 'limit'	=> array( 0, 1 ),
											  	  ) 	);

				$this->ifthd->core->db->execute();

				if ( $this->ifthd->core->db->get_num_rows() )
				{
					$this->new_form('email_in_use');
				}

				if ( $this->ifthd->core->cache['config']['use_captcha'] )
				{
					if ( ! $this->ifthd->captcha_validate( $this->ifthd->input['captcha'] ) )
					{
						if ( intval( $this->ifthd->input['final'] ) )
						{
							$error = 'captcha_mismatch';

							$this->ifthd->input['final'] = 0;
						}
						else
						{
							$this->new_form('captcha_mismatch');
						}
					}
				}
			}

			$required = array( 'department', 'priority' );

			$this->ifthd->check_fields( $required );
		}
		else
		{
			$this->ifthd->input['final'] = 1;
		}

		$searchstring = $this->ifthd->input['message'];
		$searchtitle = $this->ifthd->input['subject'];

		#=============================
		# Custom Profile Fields
		#=============================

		if ( is_array( $this->ifthd->core->cache['dfields'] ) )
		{
			$cdfvalues = ""; // Initialize for Security
			$cdfields_html = "";  // Initialize for Security

			foreach ( $this->ifthd->core->cache['dfields'] as $id => $f )
			{
				$f_perm = unserialize( $f['departs'] );

				if ( $f_perm[ $this->ifthd->input['department'] ] )
				{
					$cdfields_html .= "<input type='hidden' name='cdf_". $f['fkey'] ."' value='". $this->ifthd->input[ 'cdf_'. $f['fkey'] ] ."' />";

					if ( $f['required'] && $f['type'] != 'checkbox' )
					{
						if ( ! $this->ifthd->input[ 'cdf_'. $f['fkey'] ] )
						{
							$this->ifthd->input['step'] = 2;
							$this->new_form( 'no_cdfield', $f['name'] );
						}
					}

					$cdfvalues[ $f['fkey'] ] = $this->ifthd->input[ 'cdf_'. $f['fkey'] ];
				}
			}
		}

		if ( $this->ifthd->input['final'] )
		{
			$this->ifthd->input['department'] = intval( $this->ifthd->input['department'] );

			#=============================
			# Get Department
			#=============================

			$this->ifthd->core->db->construct( array(
												  	 'select'	=> 'all',
												  	 'from'		=> 'departments',
								 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['department'] ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			if ( ! $this->ifthd->core->db->get_num_rows() )
			{
				$this->ifthd->skin->error('no_department');
			}

			$d = $this->ifthd->core->db->fetch_row();

			#=============================
			# Department Security
			#=============================

			$d_allow = unserialize( $this->ifthd->member['g_m_depart_perm'] );

			if ( ! $d_allow[ $d['id'] ] )
			{
				$this->ifthd->log( 'security', "New Ticket to &#039;". $d['name'] ."&#039; Denied", 1, $d['id'] );

				$this->ifthd->skin->error('no_department');
			}

			if ( $this->ifthd->input['attach_id'] )
			{
				$attach_id = intval( $this->ifthd->input['attach_id'] );
			}

			#=============================
			# Create Ticket
			#=============================

			$db_array = array(
							  'did'			=> $d['id'],
							  'dname'		=> $d['name'],
							  'mid'			=> $this->ifthd->member['id'],
							  'mname'		=> $this->ifthd->member['name'],
							  'email'		=> $this->ifthd->member['email'],
							  'subject'		=> $this->ifthd->input['subject'],
							  'priority'	=> intval( $this->ifthd->input['priority'] ),
							  'message'		=> $this->ifthd->input['message'],
							  'date'		=> time(),
							  'last_reply'	=> time(),
							  'last_mid'	=> $this->ifthd->member['id'],
							  'last_mname'	=> $this->ifthd->member['name'],
							  'ipadd'		=> $this->ifthd->input['ip_address'],
							  'status'		=> 1,
							  'attach_id'	=> $attach_id,
							  'cdfields'	=> serialize($cdfvalues),
							 );

			if ( ! $this->ifthd->member['id'] )
			{
				$db_array['tkey'] = substr( md5( 'tk'. uniqid( rand(), true ) . time() ), 0, 11 );
				$db_array['mname'] = $this->ifthd->input['name'];
				$db_array['email'] = $this->ifthd->input['email'];
				$db_array['last_mname'] = $this->ifthd->input['name'];
				$db_array['guest'] = 1;
				$db_array['guest_email'] = $this->ifthd->input['guest_email'];
			}
			
			if ( $d['auto_assign'] )
			{
				$db_array['amid'] = $d['auto_assign'];
				$db_array['amname'] = $this->ifthd->core->cache['staff'][ $d['auto_assign'] ]['name'];
			}

			$this->ifthd->core->db->construct( array(
												  	 'insert'	=> 'tickets',
												  	 'set'		=> $db_array,
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$ticket_id = $this->ifthd->core->db->get_insert_id();

			$this->ifthd->log( 'member', "Ticket Created &#039;". $this->ifthd->input['subject'] ."&#039;", 1, $ticket_id );
			$this->ifthd->log( 'ticket', "Ticket Created &#039;". $this->ifthd->input['subject'] ."&#039;", 1, $ticket_id );

			#=============================
			# Update Attachment
			#=============================

			if ( $attach_id )
			{
				$this->ifthd->core->db->construct( array(
													  	 'update'	=> 'attachments',
													  	 'set'		=> array( 'tid' => $ticket_id ),
									 				  	 'where'	=> array( 'id', '=', $attach_id ),
									 				  	 'limit'	=> array( 1 ),
									 		  	  ) 	);

				$this->ifthd->core->db->next_shutdown();
				$this->ifthd->core->db->execute();

				$this->ifthd->log( 'ticket', "Uploaded Attachment #". $attach_id, 1, $ticket_id );
			}

			#=============================
			# Update Member
			#=============================

			if ( $this->ifthd->member['id'] )
			{
				$this->ifthd->core->db->next_no_quotes('set');

				$this->ifthd->core->db->construct( array(
													  	 'update'	=> 'members',
													  	 'set'		=> array( 'open_tickets' => 'open_tickets+1', 'tickets' => 'tickets+1' ),
									 				  	 'where'	=> array( 'id', '=', $this->ifthd->member['id'] ),
									 				  	 'limit'	=> array( 1 ),
									 		  	  ) 	);

				$this->ifthd->core->db->next_shutdown();
				$this->ifthd->core->db->execute();
			}

			#=============================
			# Update Department
			#=============================

			$this->ifthd->core->db->next_no_quotes('set');

			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'departments',
												  	 'set'		=> array( 'tickets' => 'tickets+1' ),
								 				  	 'where'	=> array( 'id', '=', $d['id'] ),
								 				  	 'limit'	=> array( 1 ),
								 		  	  ) 	);

			$this->ifthd->core->db->next_shutdown();
			$this->ifthd->core->db->execute();

			#=============================
			# Send Email
			#=============================

			if ( $this->ifthd->member['email_new_ticket'] && $this->ifthd->member['email_notify'] )
			{
				$replace = array(); // Initialize for Security

				$replace['TICKET_ID'] = $ticket_id;
				$replace['SUBJECT'] = $this->ifthd->input['subject'];
				$replace['DEPARTMENT'] = $d['name'];
				$replace['PRIORITY'] = $this->ifthd->get_priority( $this->ifthd->input['priority'] );
				$replace['SUB_DATE'] = $this->ifthd->ift_date( time() );
				$replace['TICKET_LINK'] = $this->ifthd->core->cache['config']['hd_url'] ."/index.php?act=tickets&code=view&id=". $ticket_id;
				$replace['MESSAGE'] = $this->ifthd->input['message'];

				$this->ifthd->send_email( $this->ifthd->member['id'], 'new_ticket', $replace, array( 'from_email' => $d['incoming_email'] ), 1 );
			}

			#=============================
			# Send Guest Email
			#=============================

			if ( ! $this->ifthd->member['id'] )
			{
				$replace = array(); // Initialize for Security

				$replace['TICKET_ID'] = $ticket_id;
				$replace['SUBJECT'] = $this->ifthd->input['subject'];
				$replace['DEPARTMENT'] = $d['name'];
				$replace['PRIORITY'] = $this->ifthd->get_priority( $this->ifthd->input['priority'] );
				$replace['SUB_DATE'] = $this->ifthd->ift_date( time() );
				$replace['TICKET_LINK'] = $this->ifthd->core->cache['config']['hd_url'] ."/index.php?act=tickets&code=view&id=". $ticket_id ."&email=". urlencode( $this->ifthd->input['email'] ) ."&key=". $db_array['tkey'];
				$replace['MEM_NAME'] = $this->ifthd->input['name'];
				$replace['TICKET_KEY'] = $db_array['tkey'];
				$replace['MESSAGE'] = $this->ifthd->input['message'];

				$this->ifthd->send_guest_email( $this->ifthd->input['email'], 'new_guest_ticket', $replace, array( 'from_email' => $d['incoming_email'] ), 1 );
			}

			#=============================
			# Email Staff
			#=============================

			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'm' => array( 'id', 'mgroup', 'email_notify', 'email_staff_new_ticket', 'time_zone', 'dst_active' ),
												  	 					  'g' => array( 'g_depart_perm' ),
												  	 					 ),
												  	 'from'		=> array( 'm' => 'members' ),
												  	 'join'		=> array( array( 'from' => array( 'g' => 'groups' ), 'where' => array( 'g' => 'g_id', '=', 'm' => 'mgroup' ) ) ),
								 				  	 'where'	=> array( array( 'g' => 'g_acp_access' ), '=', 1 ),
								 		  	  ) 	);

			$staff_sql = $this->ifthd->core->db->execute();

			if ( $this->ifthd->core->db->get_num_rows($staff_sql) )
			{
				while( $sm = $this->ifthd->core->db->fetch_row($staff_sql) )
				{
					// Check Departments
					if ( is_array( unserialize( $sm['g_depart_perm'] ) ) )
					{
						$my_departs = "";
						$my_departs = unserialize( $sm['g_depart_perm'] );

						if ( $my_departs[ $d['id'] ] )
						{
							if ( $sm['email_staff_new_ticket'] && $sm['email_notify'] )
							{
								$s_email_staff = 1;
							}

							$do_feeds[ $sm['id'] ] = 1;
						}
					}
					else
					{
						if ( $sm['email_staff_new_ticket'] && $sm['email_notify'] )
						{
							$s_email_staff = 1;
						}

						$do_feeds[ $sm['id'] ] = 1;
					}

					if ( $s_email_staff )
					{
						$mem_offset = ( $sm['time_zone'] * 60 * 60 ) + ( $sm['dst_active'] * 60 * 60 );
						
						$replace = array(); // Initialize for Security

						$replace['TICKET_ID'] = $ticket_id;
						$replace['SUBJECT'] = $this->ifthd->input['subject'];
						$replace['DEPARTMENT'] = $d['name'];
						$replace['PRIORITY'] = $this->ifthd->get_priority( $this->ifthd->input['priority'] );
						$replace['SUB_DATE'] = $this->ifthd->ift_date( time(), '', 0, 0, 1, $mem_offset, 1 );
						$replace['MESSAGE'] = $this->ifthd->input['message'];
						$replace['TICKET_LINK'] = $this->ifthd->core->cache['config']['hd_url'] ."/admin.php?section=manage&act=tickets&code=view&id=". $ticket_id;

						if ( $this->ifthd->member['id'] )
						{
							$replace['MEMBER'] = $this->ifthd->member['name'];

							$this->ifthd->send_email( $sm['id'], 'staff_new_ticket', $replace, array( 'from_email' => $d['incoming_email'] ), 1 );
						}
						else
						{
							$replace['MEMBER'] = $this->ifthd->input['name'];

							$this->ifthd->send_email( $sm['id'], 'staff_new_guest_ticket', $replace, array( 'from_email' => $d['incoming_email'] ), 1 );
						}
					}

					$s_email_staff = 0; // Reset
				}
			}

			if ( is_array( $do_feeds ) )
			{
				require_once HD_SRC .'feed.php';

				$feed = new feed();
				$feed->ifthd =& $this->ifthd;

				while( list( $smid, ) = each( $do_feeds ) )
				{
					$feed->show_feed( 'stickets', $smid, 1 );
				}
			}
			
			#=============================
			# Auto Assign
			#=============================
			
			if ( $d['auto_assign'] )
			{
				$this->ifthd->core->db->construct( array(
													  	 'update'	=> 'members',
													  	 'set'		=> array( 'assigned' => ( $this->ifthd->core->cache['staff'][ $d['auto_assign'] ]['assigned'] + 1 ) ),
									 				  	 'where'	=> array( 'id', '=', $d['auto_assign'] ),
									 		  	  ) 	);

				$this->ifthd->core->db->execute();
				
				$this->ifthd->rebuild_staff_cache();
			}

			#=============================
			# Update Stats
			#=============================

			$this->ifthd->r_ticket_stats(1);

			#=============================
			# Redirect
			#=============================

			if ( ! $this->ifthd->member['id'] )
			{
				$this->ifthd->core->db->construct( array(
													  	 'update'	=> 'sessions',
													  	 'set'		=> array( 's_mname' => $this->ifthd->input['name'], 's_email' => $this->ifthd->input['email'], 's_tkey' => $db_array['tkey'] ),
													  	 'where'	=> array( 's_id', '=', $this->ifthd->member['s_id'] ),
													  	 'limit'	=> array( 1 ),
									 		  	  ) 	);

				$this->ifthd->core->db->execute();
			}

			$this->ifthd->skin->redirect( '?act=tickets&code=view&id='. $ticket_id, 'submit_ticket_success' );
		}
		else
		{
			#=============================
			# Attachment
			#=============================

			if ( $_FILES['attachment']['size'] )
			{
				$allowed_exts = explode( "|", $this->ifthd->core->cache['config']['upload_exts'] );
				$file_ext = strrchr( $_FILES['attachment']['name'], "." );

				if ( ! in_array( $file_ext, $allowed_exts ) )
				{
					$this->new_form('upload_bad_type');
				}

				if ( $this->ifthd->member['g_upload_size_max'] )
				{
					if ( $_FILES['attachment']['size'] > $this->ifthd->member['g_upload_size_max'] )
					{
						$this->new_form('upload_too_big');
					}
				}

				$file_safe_name = $this->sanitize_name( $_FILES['attachment']['name'] );

				$attachment_name = md5( 'a'. uniqid( rand(), true ) ) . $file_ext;

				$attachment_loc = $this->ifthd->core->cache['config']['upload_dir'] .'/'. $attachment_name;

				if ( @ ! move_uploaded_file( $_FILES['attachment']['tmp_name'], $attachment_loc ) )
				{
					$this->new_form('upload_failed');
				}

				$db_array = array(
								  'tid'				=> 0,
								  'real_name'		=> $attachment_name,
								  'original_name'	=> $file_safe_name,
								  'mid'				=> $this->ifthd->member['id'],
								  'mname'			=> $this->ifthd->member['name'],
								  'size'			=> $_FILES['attachment']['size'],
								  'mime'			=> $_FILES['attachment']['type'],
								  'ipadd'			=> $this->ifthd->input['ip_address'],
								  'date'			=> time(),
								 );

				$this->ifthd->core->db->construct( array(
													  	 'insert'	=> 'attachments',
													  	 'set'		=> $db_array,
									 		  	  ) 	);

				$this->ifthd->core->db->execute();

				$attachment_id = $this->ifthd->core->db->get_insert_id();

				$attach_field = "<input type='hidden' name='attach_id' value='". $attachment_id ."' />";

				$this->ifthd->log( 'member', "Uploaded Attachment #". $attachment_id, 1, $attachment_id );
			}

			if ( $this->ifthd->core->cache['config']['tickets_suggest'] )
			{
				#=============================
				# All Is Good... Search!
				#=============================
				
				$sql = "SELECT *, ( 1.6 * ( MATCH(keywords) AGAINST ('". mysql_real_escape_string( $searchstring ) ."' IN BOOLEAN MODE) ) + 0.9 * ( MATCH(name) AGAINST ('". mysql_real_escape_string( $searchstring ) ."' IN BOOLEAN MODE) ) + ( 0.6 * ( MATCH(article) AGAINST ('". mysql_real_escape_string( $searchstring ) ."' IN BOOLEAN MODE) ) ) ) AS score FROM ". DB_PRE ."articles WHERE MATCH(name, description, article) AGAINST ('". mysql_real_escape_string( $searchstring ) ."' IN BOOLEAN MODE) ORDER BY score DESC";

				$this->ifthd->core->db->query( $sql );

				$art = ""; // Initialize for Security

				while( $m = $this->ifthd->core->db->fetch_row() )
			    {
			    	if ( $m['score'] > $max_score )
			    	{
			    		$max_score = $m['score'];
			    	}

			    	$art[] = $m;
			    }

				$articles = array(); // Initialize for Security
				$row_count = 0; // Initialize for Security

			    if ( is_array( $art ) )
			    {
			    	while ( list( , $a ) = each( $art ) )
				    {
						#=============================
						# Fix Up Information
						#=============================
				
						$row_count ++;
						
						( $row_count & 1 ) ? $a['class'] = 1 : $a['class'] = 2;

						$a['date'] = $this->ifthd->ift_date( $a['date'] );

						$a['score'] = @ round( ( $a['score'] / $max_score ) * 100 ) ."%";

				    	$articles[] = $a;
				    }

				    $this->ifthd->core->template->set_var( 'suggestions', $articles );
			    }
			    else
			    {
			    	$this->do_pre_search( 1, $attachment_id );
			    }
			}
			else
			{
				$this->do_pre_search( 1, $attachment_id );
			}

		    #=============================
			# Do Output
			#=============================

			if ( $error )
			{
				$this->ifthd->core->template->set_var( 'error', $this->ifthd->lang[ 'err_'. $error ] );
			}

			$this->ifthd->core->template->set_var( 'token_sub_c', $this->ifthd->create_token('ticket_sub_b') );

			$this->ifthd->core->template->set_var( 'cdfields', $cdfields_html );
			$this->ifthd->core->template->set_var( 'attach_field', $attach_field );

			$this->nav = array(
							   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=tickets'>{$this->ifthd->lang['tickets']}</a>",
							   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=tickets&amp;code=open'>{$this->ifthd->lang['open_ticket']}</a>",
							   );

			$this->ifthd->core->template->set_var( 'sub_tpl', 'tck_submit_3.tpl' );

			$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => $this->ifthd->lang['tickets'] .' :: '. $this->ifthd->lang['open_ticket'] ) );
		}
	}

	#=======================================
	# @ View Ticket
	# Simply shows a ticket. :)
	#=======================================

	function view_ticket($error='', $type='')
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['id'] && ! $this->ifthd->member['s_tkey'] )
		{
			if ( $this->ifthd->core->cache['group'][2]['g_ticket_access'] )
			{
				$this->show_guest_login();
			}
			else
			{
				$this->ifthd->skin->error( 'must_be_user', 1 );
			}
		}

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['id'] && $this->ifthd->member['s_tkey'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> 'all',
												  	 'from'		=> 'tickets',
								 				  	 'where'	=> array( array( 'id', '=', $this->ifthd->input['id'] ), array( 'email', '=', $this->ifthd->member['s_email'], 'and' ), array( 'guest', '=', 1, 'and' ) ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);
		}
		else
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 't' => 'all', 'a' => array( 'original_name', 'size' ) ),
												  	 'from'		=> array( 't' => 'tickets' ),
												  	 'join'		=> array( array( 'from' => array( 'a' => 'attachments' ), 'where' => array( 't' => 'attach_id', '=', 'a' => 'id' ) ) ),
								 				  	 'where'	=> array( array( array( 't' => 'id' ), '=', $this->ifthd->input['id'] ), array( array( 't' => 'mid' ), '=', $this->ifthd->member['id'], 'and' ) ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);
		}

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "Ticket Not Found ID: ". $this->ifthd->input['id'] );

			$this->ifthd->skin->error('no_ticket');
		}

		$t = $this->ifthd->core->db->fetch_row();

		#=============================
		# Custom Profile Fields
		#=============================
		
		$row_count = 0; // Initialize for Security

		if ( is_array( $this->ifthd->core->cache['dfields'] ) )
		{
			$cdfields = array(); // Initialize for Security

			$cdfdata = unserialize( $t['cdfields'] );

			// Count
			foreach( $this->ifthd->core->cache['dfields'] as $id => $f )
			{
				$f_perm = unserialize( $f['departs'] );

				if ( $f_perm[ $t['did'] ] )
				{
					$f_count ++;
				}
			}

			$f_my_right_count = 0;

			foreach( $this->ifthd->core->cache['dfields'] as $id => $f )
			{
				$f_perm = unserialize( $f['departs'] );

				if ( $f_perm[ $t['did'] ] )
				{
					if ( $f['type'] == 'dropdown' || $f['type'] == 'radio' )
					{
						$options = explode( "\n", $f['extra'] );

						while ( list( , $opt ) = each( $options ) )
						{
							$our_opt = explode( "=", $opt );

							$soggy[ $our_opt[0] ] = $our_opt[1];
						}

						$f['value'] = $soggy[ $cdfdata[ $f['fkey'] ] ];
					}
					else
					{
						$f['value'] = $cdfdata[ $f['fkey'] ];

						if ( $f['type'] == 'checkbox' )
						{
							if ( $f['value'] )
							{
								$f['value'] = $this->ifthd->lang['yes'];
							}
							else
							{
								$f['value'] = $this->ifthd->lang['no'];
							}
						}
					}
					
					if ( ! $f['value'] ) $f['value'] = '---';

					$f_my_count ++;

					if ( $f_my_count & 1 )
					{
						$f['count'] = $f_my_right_count;
						
						$row_count ++;
							
						( $row_count & 1 ) ? $row_class = 1 : $row_class = 2;

						if ( $f_my_count == $f_count )
						{
							$f['class'] = $row_class;
							
							$f['colspan'] = 3;

							$cdfields_left[] = $f;
						}
						else
						{
							$f['class'] = $row_class;
							
							$cdfields_left[] = $f;
						}
					}
					else
					{
						$f['class'] = $row_class;
							
						$cdfields_right[] = $f;

						$f_my_right_count ++;
					}

					$soggy = ""; // Reset
				}
			}

			$this->ifthd->core->template->set_var( 'cdfields_left', $cdfields_left );
			$this->ifthd->core->template->set_var( 'cdfields_right', $cdfields_right );
		}

		#=============================
		# Fix Up Information
		#=============================

		$t['links'] = ""; // Initialize for Security

		if ( $t['status'] == 1 && $t['last_reply_staff'] && ( $t['last_reply_staff'] < ( time() - ( $this->ifthd->core->cache['depart'][ $t['did'] ]['escalate_wait'] * 60 * 60 ) ) ) && $this->ifthd->core->cache['depart'][ $t['did'] ]['can_escalate'] )
		{
			$t['links'] = "<a href='". $this->ifthd->core->cache['config']['hd_url'] ."/index.php?act=tickets&amp;code=escalate&amp;id=". $t['id'] ."' onclick='return sure_escalate()'>". $this->ifthd->lang['escalate'] ."</a> | ";
		}

		if ( $t['status'] != 6 && $this->ifthd->member['g_ticket_own_close'] && $this->ifthd->core->cache['depart'][ $t['did'] ]['ticket_own_close'] )
		{
			$t['links'] .= "<a href='". $this->ifthd->core->cache['config']['hd_url'] ."/index.php?act=tickets&amp;code=close&amp;id=". $t['id'] ."' onclick='return sure_close()'>". $this->ifthd->lang['close'] ."</a>";
		}

		if ( $this->ifthd->member['g_ticket_edit'] ) $t['edit_link'] = "<span class='date'> - <a href='". $this->ifthd->core->cache['config']['hd_url'] ."/index.php?act=tickets&amp;code=edit&amp;id=". $t['id'] ."'>". $this->ifthd->lang['edit'] ."</a></span>";

		$t['date'] = $this->ifthd->ift_date( $t['date'] );
		$t['last_reply'] = $this->ifthd->ift_date( $t['last_reply'] );

		$t['message'] = $this->ifthd->prepare_output( $t['message'], 0, 0, 1 );

		$t['priority'] = $this->ifthd->get_priority( $t['priority'] );

		#=============================
		# Grab Ratings?
		#=============================

		if ( $this->ifthd->member['id'] && $this->ifthd->core->cache['config']['allow_reply_rating'] && $this->ifthd->member['g_reply_rate'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> 'all',
												  	 'from'		=> 'reply_rate',
								 				  	 'where'	=> array( 'tid', '=', $t['id'] ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			if ( $this->ifthd->core->db->get_num_rows() )
			{
				while ( $rt = $this->ifthd->core->db->fetch_row() )
				{
					$ratings[ $rt['rid'] ] = $rt['rating'];
				}
			}
		}

		#=============================
		# Grab Replies?
		#=============================

		if ( $t['replies'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'r' => 'all', 'a' => array( 'original_name', 'size' ) ),
												  	 'from'		=> array( 'r' => 'replies' ),
												  	 'join'		=> array( array( 'from' => array( 'a' => 'attachments' ), 'where' => array( 'r' => 'attach_id', '=', 'a' => 'id' ) ) ),
								 				  	 'where'	=> array( array( array( 'r' => 'tid' ), '=', $t['id'] ), array( array( 'r' => 'secret' ), '!=', 1, 'and' ) ),
								 				  	 'order'	=> array( 'date' => array( 'r' => 'asc' ) )
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$replies = array(); // Initialize for Security
			$row_count = 0; // Initialize for Security
			$attached = 1; // Initialize for Security

			while( $r = $this->ifthd->core->db->fetch_row() )
			{
				#=============================
				# Fix Up Information
				#=============================
				
				$row_count ++;
				
				( $row_count & 1 ) ? $r['class_msg'] = 'row1' : $r['class_msg'] = 'row2';

				$r['time_ago'] = $this->ifthd->ift_date( $r['date'], '', 3 );

				$r['date'] = $this->ifthd->ift_date( $r['date'] );

				if ( $r['guest'] ) $r['mname'] .= ' ('. $this->ifthd->lang['guest'] .')';

				if ( $r['staff'] )
				{
					$r['message'] = $this->ifthd->remove_dbl_spaces( $this->ifthd->prepare_output( $r['message'], 1, 1, 1 ) );

					$r['class'] = 'redstrip';
					$r['class_strip'] = 'subboxstaff';
				}
				else
				{
					$r['message'] = $this->ifthd->prepare_output( $r['message'], 0, 0, 1 );
					$r['class'] = 'bluestrip';
					$r['class_strip'] = 'subbox';
				}

				if ( $this->ifthd->member['id'] && $this->ifthd->core->cache['config']['allow_reply_rating'] && $this->ifthd->member['g_reply_rate'] && $r['staff'] && ! $this->ifthd->member['ban_ticket_rate'] )
				{
					if ( $ratings[ $r['id'] ] )
					{
						$r['rate_imgs'] = $this->rate_thumbs_already( $ratings[ $r['id'] ] );
						$r['rate_imgs_solo'] = $this->rate_thumbs_already( $ratings[ $r['id'] ] );
					}
					else
					{
						$r['rate_imgs'] = $this->rate_thumbs( $t['id'], $r['id'] );
						$r['rate_imgs_solo'] = $this->rate_thumbs( $t['id'], $r['id'] );
					}
				}

				$reply_edit_icon = 0;
				$reply_delete_icon = 0;

				if ( $this->ifthd->member['g_reply_edit'] && $r['mid'] == $this->ifthd->member['id'] && ! $r['staff'] )
				{
					$r['rate_imgs'] = "&nbsp;&nbsp;<span class='response_imgs'><a href='". $this->ifthd->core->cache['config']['hd_url'] ."/index.php?act=tickets&amp;code=editreply&amp;id={$r['id']}'><img src='images/". $this->ifthd->skin->data['img_dir'] ."/edit_icon.gif' /></a>";

					$reply_edit_icon = 1;
				}

				if ( $this->ifthd->member['g_reply_delete'] && $r['mid'] == $this->ifthd->member['id'] && ! $r['staff'] )
				{
					if ( $reply_edit_icon )
					{
						$r['rate_imgs'] .= '&nbsp;&nbsp;&nbsp;';
					}
					else
					{
						$r['rate_imgs'] = "&nbsp;&nbsp;<span class='response_imgs'>";
					}

					$r['rate_imgs'] .= "<a href='". $this->ifthd->core->cache['config']['hd_url'] ."/index.php?act=tickets&amp;code=dodelreply&amp;id={$r['id']}' onclick='return sure_delete_reply()'><img src='images/". $this->ifthd->skin->data['img_dir'] ."/delete_icon.gif' /></a>";

					$reply_delete_icon = 1;
				}

				if ( $reply_edit_icon || $reply_delete_icon ) $r['rate_imgs'] .= '</span>';

				if ( $r['attach_id'] )
				{
					$attached ++;
					
					$r['attached'] = $attached;
					$r['size'] = $this->ifthd->format_size( $r['size'] );
				}

				$replies[] = $r;
			}

			$this->ifthd->core->template->set_var( 'replies', $replies );
		}

		#=============================
		# Do Output
		#=============================

		if ( $error )
		{
			$this->ifthd->core->template->set_var( 'error', $this->ifthd->lang[ 'err_'. $error ] );
		}

		if ( $t['status'] != 6 )
		{
			if ( $this->ifthd->member['id'] && $this->ifthd->core->cache['config']['ticket_attachments'] && $this->ifthd->member['g_ticket_attach'] && $this->ifthd->core->cache['depart'][ $t['did'] ]['can_attach'] )
			{
				if ( $this->ifthd->member['g_upload_size_max'] )
				{
					$upload_info = ' ('. $this->ifthd->lang['attachment_max_size'] .': '. $this->ifthd->member['g_upload_size_max'] .' '. $this->ifthd->lang['bytes'] .')';
				}
				else
				{
					$upload_info = ' '. $this->ifthd->lang['attachment'] .'';
				}

				$this->ifthd->core->template->set_var( 'upload_info', $upload_info );
			}

			$this->ifthd->core->template->set_var( 'token_ticket_reply', $this->ifthd->create_token('treply') );
		}

		if ( $t['attach_id'] )
		{
			$t['size'] = $this->ifthd->format_size( $t['size'] );
		}

		$t['status_human'] = $this->ifthd->get_status( $t['status'] );

		$this->ifthd->core->template->set_var( 't', $t );

		$this->nav = array(
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=tickets'>{$this->ifthd->lang['tickets']}</a>",
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=tickets&amp;code=view&amp;id={$t['id']}'>{$t['subject']}</a>",
						   );


		if ( $type == 'print' )
		{
			$this->ifthd->core->template->set_var( 'sub_tpl', 'tck_print.tpl' );

			$this->ifthd->skin->do_print( array( 'title' => $this->ifthd->lang['tickets'] .' :: '. $t['subject'] ) );
		}
		else
		{
			$this->ifthd->core->template->set_var( 'sub_tpl', 'tck_show.tpl' );

			$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => $this->ifthd->lang['tickets'] .' :: '. $t['subject'] ) );
		}
	}

	#=======================================
	# @ Submit Reply
	# Submits a new reply to a ticket.
	#=======================================

	function submit_reply()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['id'] && ! $this->ifthd->member['s_tkey'] )
		{
			if ( $this->ifthd->core->cache['group'][2]['g_ticket_access'] )
			{
				$this->show_guest_login();
			}
			else
			{
				$this->ifthd->skin->error( 'must_be_user', 1 );
			}
		}

		$this->ifthd->check_token('treply');

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->input['message'] )
		{
			$this->view_ticket('no_message');
		}

		if ( ! $this->ifthd->member['id'] && $this->ifthd->member['s_tkey'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> 'all',
												  	 'from'		=> 'tickets',
								 				  	 'where'	=> array( array( 'id', '=', $this->ifthd->input['id'] ), array( 'email', '=', $this->ifthd->member['s_email'], 'and' ), array( 'guest', '=', 1, 'and' ) ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);
		}
		else
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> 'all',
												  	 'from'		=> 'tickets',
								 				  	 'where'	=> array( array( 'id', '=', $this->ifthd->input['id'] ), array( 'mid', '=', $this->ifthd->member['id'], 'and' ) ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);
		}

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "Ticket Not Found ID: ". $this->ifthd->input['id'] );

			$this->ifthd->skin->error('no_ticket');
		}

		$t = $this->ifthd->core->db->fetch_row();

		if ( $t['status'] == 6 )
		{
			$this->ifthd->log( 'error', "Reply Rejected Ticket Closed &#039;". $t['subject'] ."&#039;", 1, $t['id'] );

			$this->ifthd->skin->error('ticket_closed_reply');
		}

		#=============================
		# Attachment
		#=============================

		if ( $this->ifthd->member['id'] )
		{
			if ( $_FILES['attachment']['size'] )
			{
				$allowed_exts = explode( "|", $this->ifthd->core->cache['config']['upload_exts'] );
				$file_ext = strrchr( $_FILES['attachment']['name'], "." );

				if ( ! in_array( $file_ext, $allowed_exts ) )
				{
					$this->view_ticket('upload_bad_type');
				}

				if ( $this->ifthd->member['g_upload_size_max'] )
				{
					if ( $_FILES['attachment']['size'] > $this->ifthd->member['g_upload_size_max'] )
					{
						$this->view_ticket('upload_too_big');
					}
				}

				$file_safe_name = $this->sanitize_name( $_FILES['attachment']['name'] );

				$attachment_name = md5( 'a' . uniqid( rand(), true ) ) . $file_ext;

				$attachment_loc = $this->ifthd->core->cache['config']['upload_dir'] .'/'. $attachment_name;

				if ( @ ! move_uploaded_file( $_FILES['attachment']['tmp_name'], $attachment_loc ) )
				{
					$this->view_ticket('upload_failed');
				}

				$db_array = array(
								  'tid'				=> $t['id'],
								  'real_name'		=> $attachment_name,
								  'original_name'	=> $file_safe_name,
								  'mid'				=> $this->ifthd->member['id'],
								  'mname'			=> $this->ifthd->member['name'],
								  'size'			=> $_FILES['attachment']['size'],
								  'mime'			=> $_FILES['attachment']['type'],
								  'ipadd'			=> $this->ifthd->input['ip_address'],
								  'date'			=> time(),
								 );

				$this->ifthd->core->db->construct( array(
													  	 'insert'	=> 'attachments',
													  	 'set'		=> $db_array,
									 		  	  ) 	);

				$this->ifthd->core->db->execute();

				$attachment_id = $this->ifthd->core->db->get_insert_id();

				$this->ifthd->log( 'ticket', "Uploaded Attachment #". $attachment_id, 1, $t['id'] );
				$this->ifthd->log( 'member', "Uploaded Attachment #". $attachment_id, 1, $attachment_id );
			}
		}

		#=============================
		# Add Reply
		#=============================

		$db_array = array(
						  'tid'			=> $t['id'],
						  'mid'			=> $this->ifthd->member['id'],
						  'mname'		=> $this->ifthd->member['name'],
						  'message'		=> $this->ifthd->input['message'],
						  'attach_id'	=> $attachment_id,
						  'date'		=> time(),
						  'ipadd'		=> $this->ifthd->input['ip_address'],
						 );

		if ( ! $this->ifthd->member['id'] )
		{
			$db_array['mname'] = $this->ifthd->member['s_mname'];
			$db_array['guest'] = 1;
		}

		$this->ifthd->core->db->construct( array(
											  	 'insert'	=> 'replies',
											  	 'set'		=> $db_array,
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$reply_id = $this->ifthd->core->db->get_insert_id();

		$this->ifthd->log( 'member', "Ticket Reply &#039;". $t['subject'] ."&#039;", 1, $reply_id );
		$this->ifthd->log( 'ticket', "Ticket Reply &#039;". $t['subject'] ."&#039;", 1, $t['id'] );

		#=============================
		# Email Staff
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'm' => array( 'id', 'mgroup', 'email_notify', 'email_staff_ticket_reply', 'time_zone', 'dst_active' ),
											  	 					  'g' => array( 'g_depart_perm' ),
											  	 					 ),
											  	 'from'		=> array( 'm' => 'members' ),
											  	 'join'		=> array( array( 'from' => array( 'g' => 'groups' ), 'where' => array( 'g' => 'g_id', '=', 'm' => 'mgroup' ) ) ),
							 				  	 'where'	=> array( array( 'g' => 'g_acp_access' ), '=', 1 ),
							 		  	  ) 	);

		$staff_sql = $this->ifthd->core->db->execute();

		if ( $this->ifthd->core->db->get_num_rows($staff_sql) )
		{
			while( $sm = $this->ifthd->core->db->fetch_row($staff_sql) )
			{
				// Check Departments
				if ( is_array( unserialize( $sm['g_depart_perm'] ) ) )
				{
					$my_departs = "";
					$my_departs = unserialize( $sm['g_depart_perm'] );

					if ( $my_departs[ $t['did'] ] )
					{
						if ( $sm['email_staff_ticket_reply'] && $sm['email_notify'] )
						{
							$s_email_staff = 1;
						}

						$do_feeds[ $sm['id'] ] = 1;
					}
				}
				else
				{
					if ( $sm['email_staff_ticket_reply'] && $sm['email_notify'] )
					{
						$s_email_staff = 1;
					}

					$do_feeds[ $sm['id'] ] = 1;
				}

				if ( $s_email_staff )
				{
					$mem_offset = ( $sm['time_zone'] * 60 * 60 ) + ( $sm['dst_active'] * 60 * 60 );
					
					$replace = array(); // Initialize for Security

					$replace['TICKET_ID'] = $t['id'];
					$replace['SUBJECT'] = $t['subject'];
					$replace['DEPARTMENT'] = $t['dname'];
					$replace['PRIORITY'] = $this->ifthd->get_priority( $t['priority'] );
					$replace['SUB_DATE'] = $this->ifthd->ift_date( $t['date'], '', 0, 0, 1, $mem_offset, 1 );
					$replace['REPLY'] = $this->ifthd->input['message'];
					$replace['TICKET_LINK'] = $this->ifthd->core->cache['config']['hd_url'] ."/admin.php?section=manage&act=tickets&code=view&id=". $t['id'];
					$replace['MESSAGE'] = $t['message'];

					if ( $this->ifthd->member['id'] )
					{
						$replace['MEMBER'] = $this->ifthd->member['name'];
					}
					else
					{
						$replace['MEMBER'] = $this->ifthd->member['s_mname'];
					}

					$this->ifthd->send_email( $sm['id'], 'staff_reply_ticket', $replace, array( 'from_email' => $this->ifthd->core->cache['depart'][ $t['did'] ]['incoming_email'] ), 1 );
				}

				$s_email_staff = 0; // Reset
			}

			if ( is_array( $do_feeds ) )
			{
				require_once HD_SRC .'feed.php';

				$feed = new feed();
				$feed->ifthd =& $this->ifthd;

				while( list( $smid, ) = each( $do_feeds ) )
				{
					$feed->show_feed( 'stickets', $smid, 1 );
				}
			}
		}

		#=============================
		# Update Ticket
		#=============================

		if ( $t['status'] == 4 )
		{
			if ( $this->ifthd->member['id'] )
			{
				$db_array = array( 'last_reply' => time(), 'last_mid' => $this->ifthd->member['id'], 'last_mname' => $this->ifthd->member['name'], 'replies' => ( $t['replies'] + 1 ), 'status' => 1 );
			}
			else
			{
				$db_array = array( 'last_reply' => time(), 'last_mid' => $this->ifthd->member['id'], 'last_mname' => $this->ifthd->member['s_mname'], 'replies' => ( $t['replies'] + 1 ), 'status' => 1 );
			}
		}
		else
		{
			if ( $this->ifthd->member['id'] )
			{
				$db_array = array( 'last_reply' => time(), 'last_mid' => $this->ifthd->member['id'], 'last_mname' => $this->ifthd->member['name'], 'replies' => ( $t['replies'] + 1 ) );
			}
			else
			{
				$db_array = array( 'last_reply' => time(), 'last_mid' => $this->ifthd->member['id'], 'last_mname' => $this->ifthd->member['s_mname'], 'replies' => ( $t['replies'] + 1 ) );
			}
		}

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'tickets',
											  	 'set'		=> $db_array,
							 				  	 'where'	=> array( 'id', '=', $t['id'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->next_shutdown();
		$this->ifthd->core->db->execute();

		#=============================
		# Update Stats
		#=============================

		$this->ifthd->r_ticket_stats(1);

		#=============================
		# Redirect
		#=============================

		$this->ifthd->skin->redirect( '?act=tickets&code=view&id='. $t['id'] .'#reply'. $reply_id, 'submit_reply_success' );
	}

	#=======================================
	# @ Show History
	# Show all tickets from user.
	#=======================================

	function show_history()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['id'] && ! $this->ifthd->member['s_tkey'] )
		{
			if ( $this->ifthd->core->cache['group'][2]['g_ticket_access'] )
			{
				$this->show_guest_login();
			}
			else
			{
				$this->ifthd->skin->error( 'must_be_user', 1 );
			}
		}

		if ( $this->ifthd->member['id'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'id', 'dname', 'subject', 'priority', 'date', 'status' ),
												  	 'from'		=> 'tickets',
								 				  	 'where'	=> array( 'mid', '=', $this->ifthd->member['id'] ),
								 				  	 'order'	=> array( 'date' => 'desc' ),
								 		  	  ) 	);
		}
		else
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'id', 'dname', 'subject', 'priority', 'date', 'status' ),
												  	 'from'		=> 'tickets',
								 				  	 'where'	=> array( array( 'email', '=', $this->ifthd->member['s_email'] ), array( 'guest', '=', 1, 'and' ) ),
								 				  	 'order'	=> array( 'date' => 'desc' ),
								 		  	  ) 	);
		}

		$this->ifthd->core->db->execute();

		$tickets = array(); // Initialize for Security
		$row_count = 0; // Initialize for Security

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			while( $t = $this->ifthd->core->db->fetch_row() )
			{
				#=============================
				# Fix Up Information
				#=============================
				
				$row_count ++;
				
				( $row_count & 1 ) ? $t['class'] = 1 : $t['class'] = 2;
				
				if ( $t['priority'] == 1 ) $p_color = 'blue';
				if ( $t['priority'] == 2 ) $p_color = 'yellow';
				if ( $t['priority'] == 3 ) $p_color = 'orange';
				if ( $t['priority'] == 4 ) $p_color = 'red';
	
				$t['p_img'] = "<img src='images/". $this->ifthd->skin->data['img_dir'] ."/sq_". $p_color .".gif' class='pip' alt='priority' />&nbsp;&nbsp;";

				$t['priority'] = $this->ifthd->get_priority( $t['priority'] );

				$t['date'] = $this->ifthd->ift_date( $t['date'] );

				$t['status'] = $this->ifthd->get_status( $t['status'] );

				$tickets[] = $t;
			}

			$this->ifthd->core->template->set_var( 'htickets', $tickets );
		}

		#=============================
		# Do Output
		#=============================

		$this->nav = array(
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=tickets'>{$this->ifthd->lang['tickets']}</a>",
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=tickets&amp;code=history'>{$this->ifthd->lang['history']}</a>",
						   );

		$this->ifthd->core->template->set_var( 'sub_tpl', 'tck_history.tpl' );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => $this->ifthd->lang['tickets'] .' :: '. $this->ifthd->lang['history'] ) );
	}

	#=======================================
	# @ Ticket Action
	# Perform a special ticket action such
	# as closing or escalating.
	#=======================================

	function ticket_action($action)
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['id'] && ! $this->ifthd->member['s_tkey'] )
		{
			if ( $this->ifthd->core->cache['group'][2]['g_ticket_access'] )
			{
				$this->show_guest_login();
			}
			else
			{
				$this->ifthd->skin->error( 'must_be_user', 1 );
			}
		}
		
		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( $this->ifthd->member['id'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> 'all',
												  	 'from'		=> 'tickets',
								 				  	 'where'	=> array( array( 'id', '=', $this->ifthd->input['id'] ), array( 'mid', '=', $this->ifthd->member['id'], 'and' ) ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);
		}
		else
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> 'all',
												  	 'from'		=> 'tickets',
								 				  	 'where'	=> array( array( 'id', '=', $this->ifthd->input['id'] ), array( 'email', '=', $this->ifthd->member['s_email'], 'and' ), array( 'guest', '=', 1, 'and' ) ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);
		}

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "Ticket Not Found ID: ". $this->ifthd->input['id'] );

			$this->ifthd->skin->error('no_ticket');
		}

		$t = $this->ifthd->core->db->fetch_row();

		if ( $action == 'close' )
		{
			if ( $t['status'] == 6 )
			{
				$this->ifthd->log( 'error', "Ticket Already Closed &#039;". $t['subject'] ."&#039;", 1, $t['id'] );

				$this->ifthd->skin->error('ticket_closed_already');
			}

			if ( ! $this->ifthd->member['g_ticket_own_close'] || ! $this->ifthd->core->cache['depart'][ $t['did'] ]['ticket_own_close'] )
			{
				$this->ifthd->log( 'security', "Close Ticket Access Denied: ". $t['subject'], 1, $t['id'] );

				$this->ifthd->skin->error('ticket_no_close_perm');
			}

			if ( $this->ifthd->core->cache['depart'][ $t['did'] ]['close_reason'] )
			{
				if ( ! $this->ifthd->input['reason'] )
				{
					if ( $this->ifthd->input['final'] )
					{
						$this->ifthd->core->template->set_var( 'error', $this->ifthd->lang[ 'err_no_reason' ] );
					}

					$this->ifthd->core->template->set_var( 'token_close_reason', $this->ifthd->create_token('tclose') );

					$this->ifthd->core->template->set_var( 't', $t );

					$this->nav = array(
									   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=tickets'>{$this->ifthd->lang['tickets']}</a>",
									   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=tickets&amp;code=view&amp;id={$t['id']}'>{$t['subject']}</a>",
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=tickets&amp;code=close&amp;id={$t['id']}'>{$this->ifthd->lang['close_ticket']}</a>",
									   );

					$this->ifthd->core->template->set_var( 'sub_tpl', 'tck_close_reason.tpl' );

					$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => $this->ifthd->lang['tickets'] .' :: '. $this->ifthd->lang['close_ticket'] ) );
				}
			}

			#=============================
			# Close Ticket
			#=============================

			$this->ifthd->check_token('tclose');

			if ( $this->ifthd->member['id'] )
			{
				$this->ifthd->core->db->construct( array(
													  	 'update'	=> 'tickets',
													  	 'set'		=> array( 'close_mid' => $this->ifthd->member['id'], 'close_mname' => $this->ifthd->member['name'], 'close_reason' => $this->ifthd->input['reason'], 'status' => 6 ),
									 				  	 'where'	=> array( 'id', '=', $t['id'] ),
									 				  	 'limit'	=> array( 1 ),
									 		  	  ) 	);

				$this->ifthd->core->db->execute();
			}
			else
			{

				$this->ifthd->core->db->construct( array(
													  	 'update'	=> 'tickets',
													  	 'set'		=> array( 'close_mid' => $this->ifthd->member['id'], 'close_mname' => $this->ifthd->member['s_mname'] .' ('. $this->ifthd->lang['guest'] .')', 'close_reason' => $this->ifthd->input['reason'], 'status' => 6 ),
									 				  	 'where'	=> array( 'id', '=', $t['id'] ),
									 				  	 'limit'	=> array( 1 ),
									 		  	  ) 	);

				$this->ifthd->core->db->execute();
			}

			$this->ifthd->log( 'member', "Ticket Closed &#039;". $t['subject'] ."&#039;", 1, $t['id'] );
			$this->ifthd->log( 'ticket', "Ticket Closed &#039;". $t['subject'] ."&#039;", 1, $t['id'] );

			#=============================
			# Update Member
			#=============================

			if ( $t['mid'] )
			{
				$this->ifthd->core->db->next_no_quotes('set');

				$this->ifthd->core->db->construct( array(
													  	 'update'	=> 'members',
													  	 'set'		=> array( 'open_tickets' => 'open_tickets-1'),
									 				  	 'where'	=> array( 'id', '=', $t['mid'] ),
									 				  	 'limit'	=> array( 1 ),
									 		  	  ) 	);

				$this->ifthd->core->db->next_shutdown();
				$this->ifthd->core->db->execute();
			}

			#=============================
			# Send Email
			#=============================

			if ( ( $this->ifthd->member['email_ticket_reply'] && $this->ifthd->member['email_notify'] ) || ( $this->ifthd->core->cache['config']['guest_ticket_emails'] && $t['guest_email'] ) )
			{
				$replace = ""; // Initialize for Security

				$replace['TICKET_ID'] = $t['id'];
				$replace['SUBJECT'] = $t['subject'];
				$replace['DEPARTMENT'] = $t['dname'];
				$replace['PRIORITY'] = $this->ifthd->get_priority( $t['priority'] );
				$replace['SUB_DATE'] = $this->ifthd->ift_date( $t['date'] );
				$replace['TICKET_LINK'] = $this->ifthd->core->cache['config']['hd_url'] ."/index.php?act=tickets&code=view&id=". $t['id'];
				$replace['MESSAGE'] = $t['message'];

				if ( $mem['email_ticket_reply'] )
				{
					$this->ifthd->send_email( $t['mid'], 'ticket_close', $replace, array( 'from_email' => $this->ifthd->core->cache['depart'][ $t['did'] ]['incoming_email'] ) );
				}
				else
				{
					$replace['MEM_NAME'] = $t['mname'];

					$this->ifthd->send_guest_email( $t['email'], 'ticket_close', $replace, array( 'from_email' => $this->ifthd->core->cache['depart'][ $t['did'] ]['incoming_email'] ) );
				}
			}

			#=============================
			# Update Stats
			#=============================

			$this->ifthd->r_ticket_stats(1);

			#=============================
			# Redirect
			#=============================

			$this->ifthd->skin->redirect( '?act=tickets&code=view&id='. $t['id'], 'ticket_close_success' );
		}
		elseif ( $action == 'escalate' )
		{
			if ( $this->ifthd->member['ban_ticket_escalate'] || ! $this->ifthd->core->cache['depart'][ $t['did'] ]['can_escalate'] )
			{
				$this->ifthd->log( 'security', "Blocked Ticket Escalation" );

				$this->ifthd->skin->error('banned_ticket_escalate');
			}

			if ( $t['status'] == 6 )
			{
				$this->ifthd->log( 'error', "Escalate Rejected Ticket Closed &#039;". $t['subject'] ."&#039;", 1, $t['id'] );

				$this->ifthd->skin->error('ticket_closed_escalate');
			}

			if ( $t['status'] == 5 )
			{
				$this->ifthd->log( 'error', "Ticket Already Escalated &#039;". $t['subject'] ."&#039;", 1, $t['id'] );

				$this->ifthd->skin->error('ticket_escalated_already');
			}

			if ( $t['status'] == 1 && $t['last_reply_staff'] && ( $t['last_reply_staff'] >= ( time() - ( $this->ifthd->core->cache['depart'][ $t['did'] ]['escalate_wait'] * 60 * 60 ) ) ) )
			{
				$this->ifthd->log( 'error', "Wait For Escalation &#039;". $t['subject'] ."&#039;", 1, $t['id'] );

				$this->ifthd->skin->error('ticket_escalate_perm');
			}

			#=============================
			# Move Ticket?
			#=============================

			if ( $this->ifthd->core->cache['depart'][ $t['did'] ]['escalate_depart'] )
			{
				// Old Department
				$this->ifthd->core->db->next_no_quotes('set');

				$this->ifthd->core->db->construct( array(
													  	 'update'	=> 'departments',
													  	 'set'		=> array( 'tickets' => 'tickets-1' ),
									 				  	 'where'	=> array( 'id', '=', $t['did'] ),
									 				  	 'limit'	=> array( 1 ),
									 		  	  ) 	);
				$this->ifthd->core->db->next_shutdown();
				$this->ifthd->core->db->execute();

				// New Department
				$this->ifthd->core->db->next_no_quotes('set');

				$this->ifthd->core->db->construct( array(
													  	 'update'	=> 'departments',
													  	 'set'		=> array( 'tickets' => 'tickets+1' ),
									 				  	 'where'	=> array( 'id', '=', $this->ifthd->core->cache['depart'][ $t['did'] ]['escalate_depart'] ),
									 				  	 'limit'	=> array( 1 ),
									 		  	  ) 	);

				$this->ifthd->core->db->next_shutdown();
				$this->ifthd->core->db->execute();
			}

			#=============================
			# Escalate Ticket
			#=============================

			if ( $this->ifthd->core->cache['depart'][ $t['did'] ]['escalate_depart'] )
			{
				$this->ifthd->core->db->construct( array(
													  	 'update'	=> 'tickets',
													  	 'set'		=> array( 'did' => $this->ifthd->core->cache['depart'][ $t['did'] ]['escalate_depart'], 'dname' => $this->ifthd->core->cache['depart'][ $this->ifthd->core->cache['depart'][ $t['did'] ]['escalate_depart'] ]['name'], 'status' => 5 ),
									 				  	 'where'	=> array( 'id', '=', $t['id'] ),
									 				  	 'limit'	=> array( 1 ),
									 		  	  ) 	);
			}
			else
			{
				$this->ifthd->core->db->construct( array(
													  	 'update'	=> 'tickets',
													  	 'set'		=> array( 'status' => 5 ),
									 				  	 'where'	=> array( 'id', '=', $t['id'] ),
									 				  	 'limit'	=> array( 1 ),
									 		  	  ) 	);
			}

			$this->ifthd->core->db->execute();

			$this->ifthd->log( 'member', "Ticket Escalated &#039;". $t['subject'] ."&#039;", 1, $t['id'] );
			$this->ifthd->log( 'ticket', "Ticket Escalated &#039;". $t['subject'] ."&#039;", 1, $t['id'] );

			#=============================
			# Send Email
			#=============================

			if ( ( $this->ifthd->member['email_ticket_reply'] && $this->ifthd->member['email_notify'] ) || ( $this->ifthd->core->cache['config']['guest_ticket_emails'] && $t['guest_email'] ) )
			{
				$replace = ""; // Initialize for Security

				$replace['TICKET_ID'] = $t['id'];
				$replace['SUBJECT'] = $t['subject'];
				$replace['DEPARTMENT'] = $t['dname'];
				$replace['PRIORITY'] = $this->ifthd->get_priority( $t['priority'] );
				$replace['SUB_DATE'] = $this->ifthd->ift_date( $t['date'] );
				$replace['TICKET_LINK'] = $this->ifthd->core->cache['config']['hd_url'] ."/index.php?act=tickets&code=view&id=". $t['id'];
				$replace['MESSAGE'] = $t['message'];

				if ( $mem['email_ticket_reply'] )
				{
					$this->ifthd->send_email( $t['mid'], 'ticket_escl', $replace, array( 'from_email' => $this->ifthd->core->cache['depart'][ $t['did'] ]['incoming_email'] ), 1 );
				}
				else
				{
					$replace['MEM_NAME'] = $t['mname'];

					$this->ifthd->send_guest_email( $t['email'], 'ticket_escl', $replace, array( 'from_email' => $this->ifthd->core->cache['depart'][ $t['did'] ]['incoming_email'] ), 1 );
				}
			}

			#=============================
			# Update Stats
			#=============================

			$this->ifthd->r_ticket_stats(1);

			#=============================
			# Redirect
			#=============================

			$this->ifthd->skin->redirect( '?act=tickets&code=view&id='. $t['id'], 'ticket_escalate_success' );
		}
	}

	#=======================================
	# @ Edit Reply
	# Display edit ticket reply form.
	#=======================================

	function edit_reply($error="")
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['id'] && ! $this->ifthd->member['s_tkey'] )
		{
			if ( $this->ifthd->core->cache['group'][2]['g_ticket_access'] )
			{
				$this->show_guest_login();
			}
			else
			{
				$this->ifthd->skin->error( 'must_be_user', 1 );
			}
		}

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['g_reply_edit'] )
		{
			$this->ifthd->log( 'security', "Blocked Ticket Reply Edit Attempt" );

			$this->ifthd->skin->error('no_perm_reply_edit');
		}

		#=============================
		# Grab Reply
		#=============================

		if ( $this->ifthd->member['id'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> 'all',
												  	 'from'		=> 'replies',
								 				  	 'where'	=> array( array( 'id', '=', $this->ifthd->input['id'] ), array( 'mid', '=', $this->ifthd->member['id'], 'and' ), array( 'staff', '!=', 1, 'and' ) ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);
		}
		else
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'r' => 'all', 't' => array( 'email', 'guest' ) ),
												  	 'from'		=> array( 'r' => 'replies' ),
											  		 'join'		=> array( array( 'from' => array( 't' => 'tickets' ), 'where' => array( 'r' => 'tid', '=', 't' => 'id' ) ) ),
								 				  	 'where'	=> array( array( array( 'r' => 'id' ), '=', $this->ifthd->input['id'] ), array( array( 't' => 'email' ), '=', $this->ifthd->member['s_email'], 'and' ), array( array( 't' => 'guest' ), '=', 1, 'and' ) ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);
		}

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "Reply Not Found ID: ". $this->ifthd->input['id'] );

			$this->ifthd->skin->error('no_reply');
		}

		$r = $this->ifthd->core->db->fetch_row();

		#=============================
		# Do Output
		#=============================

		if ( $error )
		{
			$this->ifthd->core->template->set_var( 'error', $this->ifthd->lang[ 'err_'. $error ] );
		}

		$this->ifthd->core->template->set_var( 'token_reply_edit', $this->ifthd->create_token('reply_edit') );

		$this->ifthd->core->template->set_var( 'r', $r );

		$this->nav = array(
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=tickets'>{$this->ifthd->lang['tickets']}</a>",
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=tickets&amp;code=editreply&amp;id={$r['id']}'>{$this->ifthd->lang['edit_reply']}</a>",
						   );

		$this->ifthd->core->template->set_var( 'sub_tpl', 'tck_reply_edit.tpl' );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => $this->ifthd->lang['tickets'] .' :: '. $this->ifthd->lang['edit_reply'] ) );
	}

	#=======================================
	# @ Edit Ticket
	# Show edit ticket screen.
	#=======================================

	function edit_ticket($error='')
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['id'] && ! $this->ifthd->member['s_tkey'] )
		{
			if ( $this->ifthd->core->cache['group'][2]['g_ticket_access'] )
			{
				$this->show_guest_login();
			}
			else
			{
				$this->ifthd->skin->error( 'must_be_user', 1 );
			}
		}

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['g_ticket_edit'] )
		{
			$this->ifthd->log( 'security', "Blocked Ticket Edit Attempt" );

			$this->ifthd->skin->error('no_perm_ticket_edit');
		}

		if ( ! $this->ifthd->member['id'] && $this->ifthd->member['s_tkey'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> 'all',
												  	 'from'		=> 'tickets',
								 				  	 'where'	=> array( array( 'id', '=', $this->ifthd->input['id'] ), array( 'email', '=', $this->ifthd->member['s_email'], 'and' ), array( 'guest', '=', 1, 'and' ) ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);
		}
		else
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> 'all',
												  	 'from'		=> 'tickets',
								 				  	 'where'	=> array( array( 'id', '=', $this->ifthd->input['id'] ), array( 'mid', '=', $this->ifthd->member['id'], 'and' ) ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);
		}

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "Ticket Not Found ID: ". $this->ifthd->input['id'] );

			$this->ifthd->skin->error('no_ticket');
		}

		$t = $this->ifthd->core->db->fetch_row();

		#=============================
		# Do Output
		#=============================

		if ( $error )
		{
			$this->ifthd->core->template->set_var( 'error', $this->ifthd->lang[ 'err_'. $error ] );
		}

		$this->ifthd->core->template->set_var( 't', $t );

		$this->nav = array(
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=tickets'>{$this->ifthd->lang['tickets']}</a>",
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=tickets&amp;code=view&amp;id={$t['id']}'>{$t['subject']}</a>",
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=tickets&amp;code=edit&amp;id={$t['id']}'>{$this->ifthd->lang['edit']}</a>",
						   );

		$this->ifthd->core->template->set_var( 'sub_tpl', 'tck_edit.tpl' );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => $this->ifthd->lang['tickets'] .' :: '. $t['subject'] .' :: '. $this->ifthd->lang['edit'] ) );
	}

	#=======================================
	# @ Do Edit Ticket
	# Edit the damn ticket.
	#=======================================

	function do_edit_ticket()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['id'] && ! $this->ifthd->member['s_tkey'] )
		{
			if ( $this->ifthd->core->cache['group'][2]['g_ticket_access'] )
			{
				$this->show_guest_login();
			}
			else
			{
				$this->ifthd->skin->error( 'must_be_user', 1 );
			}
		}

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->input['message'] )
		{
			$this->edit_reply('no_message');
		}

		if ( ! $this->ifthd->member['g_ticket_edit'] )
		{
			$this->ifthd->log( 'security', "Blocked Ticket Edit Attempt" );

			$this->ifthd->skin->error('no_perm_ticket_edit');
		}

		if ( ! $this->ifthd->member['id'] && $this->ifthd->member['s_tkey'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> 'all',
												  	 'from'		=> 'tickets',
								 				  	 'where'	=> array( array( 'id', '=', $this->ifthd->input['id'] ), array( 'email', '=', $this->ifthd->member['s_email'], 'and' ), array( 'guest', '=', 1, 'and' ) ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);
		}
		else
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> 'all',
												  	 'from'		=> 'tickets',
								 				  	 'where'	=> array( array( 'id', '=', $this->ifthd->input['id'] ), array( 'mid', '=', $this->ifthd->member['id'], 'and' ) ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);
		}

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "Ticket Not Found ID: ". $this->ifthd->input['id'] );

			$this->ifthd->skin->error('no_ticket');
		}

		$t = $this->ifthd->core->db->fetch_row();

		#=============================
		# Edit Ticket
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'tickets',
											  	 'set'		=> array( 'message' => $this->ifthd->input['message'] ),
							 				  	 'where'	=> array( 'id', '=', $t['id'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'member', "Edited Ticket ID #". $t['id'], 1, $t['id'] );
		$this->ifthd->log( 'ticket', "Edited Ticket ID #". $t['id'], 1, $t['tid'] );

		#=============================
		# Redirect
		#=============================

		$this->ifthd->skin->redirect( '?act=tickets&code=view&id='. $t['id'], 'ticket_edit_success' );
	}

	#=======================================
	# @ Do Edit Reply
	# Edit ticket reply.
	#=======================================

	function do_reply_edit()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['id'] && ! $this->ifthd->member['s_tkey'] )
		{
			if ( $this->ifthd->core->cache['group'][2]['g_ticket_access'] )
			{
				$this->show_guest_login();
			}
			else
			{
				$this->ifthd->skin->error( 'must_be_user', 1 );
			}
		}

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['g_reply_edit'] )
		{
			$this->ifthd->log( 'security', "Blocked Ticket Reply Edit Attempt" );

			$this->ifthd->skin->error('no_perm_reply_edit');
		}

		if ( ! $this->ifthd->input['message'] )
		{
			$this->edit_reply('no_reply');
		}

		#=============================
		# Grab Reply
		#=============================

		if ( $this->ifthd->member['id'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> 'all',
												  	 'from'		=> 'replies',
								 				  	 'where'	=> array( array( 'id', '=', $this->ifthd->input['id'] ), array( 'mid', '=', $this->ifthd->member['id'], 'and' ), array( 'staff', '!=', 1, 'and' ) ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);
		}
		else
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'r' => 'all', 't' => array( 'email', 'guest' ) ),
												  	 'from'		=> array( 'r' => 'replies' ),
											  		 'join'		=> array( array( 'from' => array( 't' => 'tickets' ), 'where' => array( 'r' => 'tid', '=', 't' => 'id' ) ) ),
								 				  	 'where'	=> array( array( array( 'r' => 'id' ), '=', $this->ifthd->input['id'] ), array( array( 't' => 'email' ), '=', $this->ifthd->member['s_email'], 'and' ), array( array( 't' => 'guest' ), '=', 1, 'and' ) ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);
		}

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "Reply Not Found ID: ". $this->ifthd->input['id'] );

			$this->ifthd->skin->error('no_reply');
		}

		$r = $this->ifthd->core->db->fetch_row();

		#=============================
		# Update Reply
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'replies',
											  	 'set'		=> array( 'message' => $this->ifthd->input['message'] ),
							 				  	 'where'	=> array( 'id', '=', $r['id'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'member', "Edited Ticket Reply ID #". $r['id'], 1, $r['id'] );
		$this->ifthd->log( 'ticket', "Edited Ticket Reply ID #". $r['id'], 1, $r['tid'] );

		#=============================
		# Redirect
		#=============================

		$this->ifthd->skin->redirect( '?act=tickets&code=view&id='. $r['tid'] .'#reply'. $r['id'], 'reply_edit_success' );
	}

	#=======================================
	# @ Do Delete Reply
	# Delete ticket reply.
	#=======================================

	function do_reply_delete()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['id'] && ! $this->ifthd->member['s_tkey'] )
		{
			if ( $this->ifthd->core->cache['group'][2]['g_ticket_access'] )
			{
				$this->show_guest_login();
			}
			else
			{
				$this->ifthd->skin->error( 'must_be_user', 1 );
			}
		}

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['g_reply_delete'] )
		{
			$this->ifthd->log( 'security', "Blocked Ticket Reply Delete Attempt" );

			$this->ifthd->skin->error('no_perm_reply_delete');
		}

		#=============================
		# Grab Reply
		#=============================

		if ( $this->ifthd->member['id'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> 'all',
												  	 'from'		=> 'replies',
								 				  	 'where'	=> array( array( 'id', '=', $this->ifthd->input['id'] ), array( 'mid', '=', $this->ifthd->member['id'], 'and' ), array( 'staff', '!=', 1, 'and' ) ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);
		}
		else
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'r' => 'all', 't' => array( 'email', 'guest' ) ),
												  	 'from'		=> array( 'r' => 'replies' ),
											  		 'join'		=> array( array( 'from' => array( 't' => 'tickets' ), 'where' => array( 'r' => 'tid', '=', 't' => 'id' ) ) ),
								 				  	 'where'	=> array( array( array( 'r' => 'id' ), '=', $this->ifthd->input['id'] ), array( array( 't' => 'email' ), '=', $this->ifthd->member['s_email'], 'and' ), array( array( 't' => 'guest' ), '=', 1, 'and' ) ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);
		}

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "Reply Not Found ID: ". $this->ifthd->input['id'] );

			$this->ifthd->skin->error('no_reply');
		}

		$r = $this->ifthd->core->db->fetch_row();

		#=============================
		# Delete Reply
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'delete'	=> 'replies',
							 				  	 'where'	=> array( 'id', '=', $r['id'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'member', "Deleted Ticket Reply ID #". $r['id'], 2, $r['id'] );
		$this->ifthd->log( 'ticket', "Deleted Ticket Reply ID #". $r['id'], 2, $r['tid'] );

		#=============================
		# Update Ticket
		#=============================

		$this->ifthd->core->db->next_no_quotes('set');

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'tickets',
											  	 'set'		=> array( 'replies' => 'replies-1' ),
							 				  	 'where'	=> array( 'id', '=', $r['tid'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->next_shutdown();
		$this->ifthd->core->db->execute();

		#=============================
		# Redirect
		#=============================

		$this->ifthd->skin->redirect( '?act=tickets&code=view&id='. $r['tid'], 'reply_delete_success' );
	}

	#=======================================
	# @ Do Rate
	# Adding rating to reply.
	#=======================================

	function do_rate()
	{
		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );
		$this->ifthd->input['rid'] = intval( $this->ifthd->input['rid'] );

		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['id'] )
		{
			$this->ifthd->skin->error( 'must_be_user', 1 );
		}

		if ( ! $this->ifthd->core->cache['config']['allow_reply_rating'] )
		{
			$this->ifthd->skin->error('reply_rating_disabled');
		}

		if ( ! $this->ifthd->member['g_reply_rate'] || $this->ifthd->member['ban_ticket_rate'] )
		{
			$this->ifthd->log( 'security', "Blocked Reply Rating" );

			$this->ifthd->skin->error('banned_ticket_rate');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'tickets',
							 				  	 'where'	=> array( array( 'id', '=', $this->ifthd->input['id'] ), array( 'mid', '=', $this->ifthd->member['id'], 'and' ) ),
							 				  	 'limit'	=> array( 0, 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "Ticket Not Found ID: ". $this->ifthd->input['id'] );

			$this->ifthd->skin->error('no_ticket');
		}

		$t = $this->ifthd->core->db->fetch_row();

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'replies',
							 				  	 'where'	=> array( array( 'id', '=', $this->ifthd->input['rid'] ), array( 'tid', '=', $this->ifthd->input['id'], 'and' ) ),
							 				  	 'limit'	=> array( 0, 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "Reply Not Found ID: ". $this->ifthd->input['rid'] );

			$this->ifthd->skin->error('no_reply');
		}

		$r = $this->ifthd->core->db->fetch_row();

		if ( ! $r['staff'] )
		{
			$this->ifthd->log( 'security', "Reply Rating Blocked Not Staff &#039;". $t['subject'] ."&#039;", 1, $r['id'] );

			$this->ifthd->skin->error('no_staff_rate_reply');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'votes', 'rating', 'rating_total' ),
											  	 'from'		=> 'members',
							 				  	 'where'	=> array( 'id', '=', $r['mid'] ),
							 				  	 'limit'	=> array( 0, 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "Member Not Found ID: ". $r['mid'] );

			$this->ifthd->skin->error('no_member');
		}

		$s = $this->ifthd->core->db->fetch_row();

		if ( ! $this->ifthd->member['id'] )
		{
			$this->ifthd->log( 'security', "Reply Rating Blocked From Guest &#039;". $t['subject'] ."&#039;", 1, $r['id'] );

			$this->ifthd->skin->error( 'must_be_user', 1 );
		}

		$allowed_ratings = array( 1, 5 );

		if ( ! in_array( $this->ifthd->input['amount'], $allowed_ratings ) )
		{
			$this->ifthd->log( 'security', "Invalid Reply Rating Amount &#039;". $t['subject'] ."&#039;", 1, $r['id'] );

			$this->ifthd->skin->error('invalid_rate_value_reply');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'reply_rate',
							 				  	 'where'	=> array( array( 'tid', '=', $this->ifthd->input['id'] ), array( 'rid', '=', $this->ifthd->input['rid'], 'and' ), array( 'mid', '=', $this->ifthd->member['id'], 'and' ) ),
							 				  	 'limit'	=> array( 0, 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'security', "Already Rated Reply By Member &#039;". $t['subject'] ."&#039;", 1, $r['id'] );

			$this->ifthd->skin->error('already_rated_reply');
		}

		#=============================
		# Add Rating
		#=============================

		$db_array = array(
						  'tid'			=> $this->ifthd->input['id'],
						  'rid'			=> $this->ifthd->input['rid'],
						  'mid'			=> $this->ifthd->member['id'],
						  'rating'		=> $this->ifthd->input['amount'],
						  'date'		=> time(),
						  'ipadd'		=> $this->ifthd->input['ip_address'],
						 );

		$this->ifthd->core->db->construct( array(
											  	 'insert'	=> 'reply_rate',
											  	 'set'		=> $db_array,
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'member', "Reply Rating Value ". $this->ifthd->input['amount'] ." Added &#039;". $t['subject'] ."&#039;", 1, $r['id'] );

		#=============================
		# Update Ticket Rating
		#=============================

		$new_ticket_rating = round( ( $t['rating_total'] + $this->ifthd->input['amount'] ) / ( $t['votes'] + 1 ), 2 );

		$this->ifthd->core->db->next_no_quotes('set');

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'tickets',
											  	 'set'		=> array( 'votes' => 'votes+1', 'rating' => $new_ticket_rating, 'rating_total' => 'rating_total+'. $this->ifthd->input['amount'] ),
							 				  	 'where'	=> array( 'id', '=', $t['id'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->next_shutdown();
		$this->ifthd->core->db->execute();

		#=============================
		# Update Staff Member
		#=============================

		$new_rating = round( ( $s['rating_total'] + $this->ifthd->input['amount'] ) / ( $s['votes'] + 1 ), 2 );

		$this->ifthd->core->db->next_no_quotes('set');

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'members',
											  	 'set'		=> array( 'votes' => 'votes+1', 'rating' => $new_rating, 'rating_total' => 'rating_total+'. $this->ifthd->input['amount'] ),
							 				  	 'where'	=> array( 'id', '=', $s['id'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->next_shutdown();
		$this->ifthd->core->db->execute();

	    #=============================
		# Do Output
		#=============================

		$this->view_ticket();
	}

	#=======================================
	# @ Show Guest Login
	# Show the guest ticket login form.
	#=======================================

	function show_guest_login()
	{
		#=============================
		# Do Output
		#=============================

		$this->ifthd->core->template->set_var( 'token_gt_login', $this->ifthd->create_token('glogin') );

		$this->ifthd->core->template->set_var( 'sub_tpl', 'tck_guest_login.tpl' );

		$this->ifthd->skin->do_output();
	}

	#=======================================
	# @ rate_thumbs_already()
	#=======================================

	function rate_thumbs_already($choice)
	{
		if ( $choice == 1 )
		{
			$ift_html = "&nbsp;&nbsp;<span class='response_imgs'><img src='images/". $this->ifthd->skin->data['img_dir'] ."/thumbs_down_hover.gif' alt='". $this->ifthd->lang['thumbs_down'] ."' style='vertical-align:middle' /></span>";
		}
		elseif ( $choice == 5 )
		{
			$ift_html = "&nbsp;&nbsp;<span class='response_imgs'><img src='images/". $this->ifthd->skin->data['img_dir'] ."/thumbs_up_hover.gif' alt='". $this->ifthd->lang['thumbs_up'] ."' style='vertical-align:middle' /></span>";
		}

		return $ift_html;
	}

	#=======================================
	# @ rate_thumbs()
	#=======================================

	function rate_thumbs($tid, $rid)
	{
		$ift_html = "&nbsp;&nbsp;<span class='response_imgs'><a href='". $this->ifthd->core->cache['config']['hd_url'] ."/index.php?act=tickets&amp;code=rate&amp;amount=5&amp;id={$tid}&amp;rid={$rid}'><img src='images/". $this->ifthd->skin->data['img_dir'] ."/thumbs_up.gif' alt='". $this->ifthd->lang['thumbs_up'] ."' id='thumbsup_{$rid}' style='vertical-align:middle' onmouseover='amithumbsup({$rid})' onmouseout='unamithumbsup({$rid})' /></a>&nbsp;&nbsp;<a href='". $this->ifthd->core->cache['config']['hd_url'] ."/index.php?act=tickets&amp;code=rate&amp;amount=1&amp;id={$tid}&amp;rid={$rid}'><img src='images/". $this->ifthd->skin->data['img_dir'] ."/thumbs_down.gif' alt='". $this->ifthd->lang['thumbs_down'] ."' id='thumbsdown_{$rid}' style='vertical-align:middle' onmouseover='amithumbsdown({$rid})' onmouseout='unamithumbsdown({$rid})' /></a></span>";

		return $ift_html;
	}

	#=======================================
	# @ Sanitize Name
	# Remove scary characters. :P
	#=======================================

	function sanitize_name($name)
	{
		$name = str_replace( " ", "_", $name );

		return ereg_replace( "[^A-Za-z0-9_\.]", "", $name );
	}

	#=======================================
	# @ Download Attachment
	# Send the attachment to the browser.
	#=======================================

	function download_attachment()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['id'] && ! $this->ifthd->member['s_tkey'] )
		{
			if ( $this->ifthd->core->cache['group'][2]['g_ticket_access'] )
			{
				$this->show_guest_login();
			}
			else
			{
				$this->ifthd->skin->error( 'must_be_user', 1 );
			}
		}

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( $this->ifthd->member['id'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'a' => 'all',
												  	 					  't' => array( 'subject' ),
												  	 					 ),
												  	 'from'		=> array( 'a' => 'attachments' ),
												  	 'join'		=> array( array( 'from' => array( 't' => 'tickets' ), 'where' => array( 'a' => 'tid', '=', 't' => 'id' ) ) ),
								 				  	 'where'	=> array( array( array( 'a' => 'id' ), '=', $this->ifthd->input['id'] ), array( array( 't' => 'mid' ), '=', $this->ifthd->member['id'], 'and' ) ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);
		}
		else
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'a' => 'all',
												  	 					  't' => array( 'subject' ),
												  	 					 ),
												  	 'from'		=> array( 'a' => 'attachments' ),
												  	 'join'		=> array( array( 'from' => array( 't' => 'tickets' ), 'where' => array( 'a' => 'tid', '=', 't' => 'id' ) ) ),
								 				  	 'where'	=> array( array( array( 'a' => 'id' ), '=', $this->ifthd->input['id'] ), array( array( 't' => 'email' ), '=', $this->ifthd->member['s_email'], 'and' ), array( array( 't' => 'guest' ), '=', 1, 'and' ) ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);
		}

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_attachment');
		}

		$a = $this->ifthd->core->db->fetch_row();

		$file_path = $this->ifthd->core->cache['config']['upload_dir'] .'/'. $a['real_name'];

		if ( ! file_exists( $file_path ) )
		{
			$this->ifthd->skin->error('no_attachment');

			$this->ifthd->log( 'error', "Attachment File Not Found ID #". $a['id'], 2, $a['id'] );
		}

		#=============================
		# Send Download
		#=============================

		if ( $a['mime'] )
		{
			header("Content-type: {$a['mime']}");
		}
		else
		{
			header("Content-type: application/force-download");
		}

		$show_types = array( 'image/gif', 'image/jpeg', 'image/jpg', 'image/png', 'text/plain', 'text/html' );

		if ( ! in_array( $a['mime'], $show_types ) )
		{
			header("Content-Disposition: attachment; filename={$a['original_name']}");
		}

		#header("Content-length: ".filesize( $file_path ));

		readfile( $file_path );
	}

}

?>