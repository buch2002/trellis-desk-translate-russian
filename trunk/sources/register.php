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
|    | Register :: Sources
#======================================================
*/

class register {

	#=======================================
	# @ Auto Run
	# Function that is run automatically
	# when the file is required.
	#=======================================

	function auto_run()
	{
		#=============================
		# Initialize
		#=============================

		$this->ifthd->load_lang('register');

		#=============================
		# Security Checks
		#=============================

		if ( $this->ifthd->member['id'] )
		{
			$this->ifthd->skin->error( 'must_be_guest' );
		}

		switch( $this->ifthd->input['code'] )
    	{
    		case 'upgrade':
				$this->register_form('upgrade');
    		break;
    		case 'new':
				$this->create_account('new');
    		break;
    		case 'doupgrade':
				$this->create_account('upgrade');
    		break;
    		case 'validate':
				$this->validate_email();
    		break;
    		case 'sendval':
				$this->resend_val_form();
    		break;
    		case 'forgot':
				$this->forgot_pass_form();
    		break;
    		case 'reset':
				$this->reset_pass_form();
    		break;

    		case 'dosendval':
				$this->resend_val();
    		break;
    		case 'doforgot':
				$this->do_forgot_pass();
    		break;
    		case 'doreset':
				$this->do_reset_pass();
    		break;

    		default:
    			$this->register_form('new');
    		break;
		}
	}

	#=======================================
	# @ Register Form
	# Show registration form.
	#=======================================

	function register_form($type='new', $error="", $extra="")
	{
		#=============================
		# Security Checks
		#=============================

		if ( $type == 'upgrade' && ! $this->ifthd->core->cache['config']['guest_upgrade'] )
		{
			$this->ifthd->skin->error('no_perm_access');
		}

		#=============================
		# Custom Profile Fields
		#=============================

		$cpfields = array(); // Initialize for Security
		$row_count = 0; // Initialize for Security

		if ( is_array( $this->ifthd->core->cache['pfields'] ) )
		{
			foreach( $this->ifthd->core->cache['pfields'] as $id => $f )
			{
				if ( $f['reg'] )
				{
					$row_count ++;
					
					( $row_count & 1 ) ? $f['class'] = 1 : $f['class'] = 2;
					
					if ( ! $f['required'] )
					{
						$f['optional'] = $this->ifthd->lang['optional'];
					}

					$f['value'] = $this->ifthd->input[ 'cpf_'. $f['fkey'] ];

					if ( $f['type'] == 'textfield' )
					{
						$cpfields[] = $f;
					}
					elseif ( $f['type'] == 'textarea' )
					{
						$cpfields[] = $f;
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

						$cpfields[] = $f;
					}
					elseif ( $f['type'] == 'checkbox' )
					{
						$cpfields[] = $f;
					}
					elseif ( $f['type'] == 'radio' )
					{
						$options = explode( "\n", $f['extra'] );

						while ( list( , $opt ) = each( $options ) )
						{
							$our_opt = explode( "=", $opt );

							if ( $our_opt[0] == $f['value'] )
							{
								$f['options'] .= "<label for='cpf_". $f['fkey'] ."_". $our_opt[0] ."'><input type='radio' name='cpf_". $f['fkey'] ."' id='cpf_". $f['fkey'] ."_". $our_opt[0] ."' value='". $our_opt[0] ."' class='radio' checked='checked' /> ". $our_opt[1] ."</label>&nbsp;&nbsp;";
							}
							else
							{
								$f['options'] .= "<label for='cpf_". $f['fkey'] ."_". $our_opt[0] ."'><input type='radio' name='cpf_". $f['fkey'] ."' id='cpf_". $f['fkey'] ."_". $our_opt[0] ."' value='". $our_opt[0] ."' class='radio' /> ". $our_opt[1] ."</label>&nbsp;&nbsp;";
							}
						}

						$cpfields[] = $f;
					}
				}

				$optional = ""; // Reset
				$f['options'] = ""; // Reset
			}

			$this->ifthd->core->template->set_var( 'cpfields', $cpfields );
		}

		#=============================
		# Do Output
		#=============================
		
		$row_count ++;		
		( $row_count & 1 ) ? $class_captcha = 1 : $class_captcha = 2;
		
		$this->ifthd->core->template->set_var( 'class_captcha', $class_captcha );

		if ( ! $this->ifthd->core->cache['config']['enable_registration'] )
		{
			$this->ifthd->skin->error('registration_disabled');
		}

		if ( $error )
		{
			$this->ifthd->core->template->set_var( 'error', $this->ifthd->lang[ 'err_'. $error ] );
		}

		if ( $type == 'upgrade' )
		{
			$this->nav = array( "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=upgrade'>{$this->ifthd->lang['register']}</a>" );
		}
		else
		{
			$this->nav = array( "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=register'>{$this->ifthd->lang['register']}</a>" );
		}

		if ( $type == 'new' )
		{
			$this->ifthd->core->template->set_var( 'token_register', $this->ifthd->create_token('register') );

			$this->ifthd->core->template->set_var( 'sub_tpl', 'register.tpl' );
		}
		elseif ( $type == 'upgrade' )
		{
			$this->ifthd->core->template->set_var( 'token_upgrade', $this->ifthd->create_token('upgrade') );

			$this->ifthd->core->template->set_var( 'sub_tpl', 'reg_upgrade.tpl' );
		}

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => $this->ifthd->lang['register'] ) );
	}

	#=======================================
	# @ Create Account
	# Creates a new account.
	#=======================================

	function create_account($type='new')
	{
		#=============================
		# Security Checks
		#=============================

		if ( $type == 'upgrade' && ! $this->ifthd->core->cache['config']['guest_upgrade'] )
		{
			$this->ifthd->skin->error('no_perm_access');
		}

		if ( ! $this->ifthd->core->cache['config']['enable_registration'] )
		{
			$this->ifthd->skin->error('registration_disabled');
		}

		if ( $type == 'new' )
		{
			$this->ifthd->check_token('register');
		}
		elseif ( $type == 'upgrade' )
		{
			$this->ifthd->check_token('upgrade');
		}

		if ( ! $this->ifthd->input['user'] )
		{
			$this->register_form( $type, 'no_user_short' );
		}

		if ( $type == 'upgrade' ) $this->ifthd->input['email'] = $this->ifthd->member['s_email'];

		if ( ! $this->ifthd->validate_email( $this->ifthd->input['email'] ) )
		{
				$this->register_form( $type, 'no_email_valid' );
		}

		if ( $this->ifthd->input['pass'] != $this->ifthd->input['passb'] )
		{
			$this->register_form( $type, 'no_pass_match' );
		}

		if ( ! $this->ifthd->input['pass'] )
		{
			$this->register_form( $type, 'no_pass_short' );
		}

		if ( $this->ifthd->core->cache['config']['use_captcha'] )
		{
			if ( ! $this->ifthd->captcha_validate( $this->ifthd->input['captcha'] ) )
			{
				$this->register_form( $type, 'captcha_mismatch' );
			}
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
			$this->register_form( $type, 'email_in_use' );
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id' ),
											  	 'from'		=> 'members',
											  	 'where'	=> array( 'name|lower', '=', strtolower( $this->ifthd->input['user'] ) ),
											  	 'limit'	=> array( 0, 1 ),
									  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			$this->register_form( $type, 'user_in_use' );
		}

		#=============================
		# Custom Profile Fields
		#=============================

		$cpfvalues = ""; // Initialize for Security

		while ( list( $id, $f ) = each( $this->ifthd->core->cache['pfields'] ) )
		{
			$f_perm = unserialize( $f['perms'] );

			if ( $f['reg'] )
			{
				if ( $f['required'] && $f['type'] != 'checkbox' )
				{
					if ( ! $this->ifthd->input[ 'cpf_'. $f['fkey'] ] )
					{
						$this->register_form(  $type, 'no_cpfield', $f['name'] );
					}
				}

				$cpfvalues[ $f['fkey'] ] = $this->ifthd->input[ 'cpf_'. $f['fkey'] ];
			}
		}

		#=============================
		# Insert Member
		#=============================

		$pass_salt = substr( md5( 'ps'. uniqid( rand(), true ) ), 0, 9 );
		$pass_hash = sha1( md5( $this->ifthd->input['pass'] . $pass_salt ) );

		$db_array = array(
						  'name'						=> $this->ifthd->input['user'],
						  'email'						=> $this->ifthd->input['email'],
						  'password'					=> $pass_hash,
						  'pass_salt'					=> $pass_salt,
						  'login_key'					=> str_replace( "=", "", base64_encode( strrev( crypt( $this->ifthd->input['pass'] ) ) ) ),
						  'joined'						=> time(),
						  'ipadd'						=> $this->ifthd->input['ip_address'],
						  'email_notify'				=> 1,
						  'email_html'					=> 1,
						  'email_new_ticket'			=> 1,
						  'email_ticket_reply'			=> 1,
						  'email_announce'				=> 1,
						  'email_staff_new_ticket'		=> 1,
						  'email_staff_ticket_reply'	=> 1,
						  'use_rte'						=> 1,
						  'cpfields'					=> serialize($cpfvalues),
						  'rss_key'						=> md5( 'rk'. uniqid( rand(), true ) ),
						 );

		if ( $this->ifthd->core->cache['config']['email_validation'] || $this->ifthd->core->cache['config']['admin_validation'] )
		{
			$db_array['mgroup'] = 3;
		}
		else
		{
			$db_array['mgroup'] = 1;
		}

		if ( ! $this->ifthd->core->cache['config']['admin_validation'] )
		{
			$db_array['admin_val'] = 1;
		}
		if ( ! $this->ifthd->core->cache['config']['email_validation'] )
		{
			$db_array['email_val'] = 1;
		}

		$this->ifthd->core->db->construct( array(
											  	 'insert'	=> 'members',
											  	 'set'		=> $db_array,
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$member_id = $this->ifthd->core->db->get_insert_id();

		#=============================
		# Upgrade Account
		#=============================

		if ( $type == 'upgrade' )
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'id' ),
												  	 'from'		=> 'tickets',
												  	 'where'	=> array( array( 'email', '=', $this->ifthd->input['email'] ), array( 'guest', '=', 1, 'and' ) ),
										  	  ) 	);

			$this->ifthd->core->db->execute();

			if ( $this->ifthd->core->db->get_num_rows() )
			{
				$tickets = array(); // Initialize for Security

				while( $t = $this->ifthd->core->db->fetch_row() )
				{
					$tickets[] = $t['id'];
				}

				// Update Tickets
				$this->ifthd->core->db->construct( array(
													  	 'update'	=> 'tickets',
													  	 'set'		=> array( 'tkey' => "", 'mid' => $member_id, 'mname' => $this->ifthd->input['user'], 'guest' => 0 ),
													  	 'where'	=> array( array( 'email', '=', $this->ifthd->input['email'] ), array( 'guest', '=', 1, 'and' ) ),
									 		  	  ) 	);

				$this->ifthd->core->db->execute();

				// Update Replies
				$this->ifthd->core->db->construct( array(
													  	 'update'	=> 'replies',
													  	 'set'		=> array( 'mid' => $member_id, 'mname' => $this->ifthd->input['user'], 'guest' => 0 ),
													  	 'where'	=> array( array( 'tid', 'in', $tickets ), array( 'guest', '=', 1, 'and' ) ),
									 		  	  ) 	);

				$this->ifthd->core->db->execute();
			}

			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'sessions',
												  	 'set'		=> array( 's_tkey' => "" ),
												  	 'where'	=> array( 's_id', '=', $this->ifthd->member['s_id'] ),
												  	 'limit'	=> array( 1 ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();
		}

		#=============================
		# Update New Group
		#=============================

		$this->ifthd->core->db->next_no_quotes('set');

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'groups',
											  	 'set'		=> array( 'g_members' => 'g_members+1' ),
							 				  	 'where'	=> array( 'g_id', '=', $db_array['mgroup'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		#=============================
		# Update Stats
		#=============================

		$this->ifthd->r_member_stats(1);

		#=============================
		# Insert Validation
		#=============================

		if ( $this->ifthd->core->cache['config']['email_validation'] )
		{
			$val_code = md5( 'vc' . $this->ifthd->input['email'] . uniqid( rand(), true ) );

			$db_array = array(
							  'id'			=> $val_code,
							  'mid'			=> $member_id,
							  'mname'		=> $this->ifthd->input['user'],
							  'date'		=> time(),
							  'type'		=> 1,
							 );

			$this->ifthd->core->db->construct( array(
												  	 'insert'	=> 'validation',
												  	 'set'		=> $db_array,
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			#=============================
			# Send Email
			#=============================

			$replace = ""; // Initialize for Security

			$replace['USER_NAME'] = $this->ifthd->input['user'];
			$replace['VAL_LINK'] = $this->ifthd->core->cache['config']['hd_url'] ."/index.php?act=register&code=validate&key=". $val_code;

			if ( $this->ifthd->core->cache['config']['admin_validation'] )
			{
				$this->ifthd->send_email( $member_id, 'new_user_val_both', $replace );
			}
			else
			{
				$this->ifthd->send_email( $member_id, 'new_user_val_email', $replace );
			}
		}
		elseif ( $this->ifthd->core->cache['config']['admin_validation'] )
		{
			#=============================
			# Send Email
			#=============================

			$replace = ""; // Initialize for Security

			$replace['USER_NAME'] = $this->ifthd->input['user'];

			$this->ifthd->send_email( $member_id, 'new_user_val_admin', $replace );
		}

		$this->ifthd->log( 'member', "New Member Registration &#039;". $this->ifthd->input['user'] ."&#039;", 1, $member_id );
		
		#=============================
		# Send Email to Admin
		#=============================

		if ( $this->ifthd->core->cache['config']['admin_validation'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'id', 'time_zone', 'dst_active' ),
												  	 'from'		=> 'members',
								 				  	 'where'	=> array( 'mgroup', '=', 4 ),
								 		  	  ) 	);
			
			$admin_sql = $this->ifthd->core->db->execute();
			
			if ( $this->ifthd->core->db->get_num_rows($admin_sql) )
			{
				while( $am = $this->ifthd->core->db->fetch_row($admin_sql) )
				{
					$mem_offset = ( $am['time_zone'] * 60 * 60 ) + ( $am['dst_active'] * 60 * 60 );
					
					$replace = ""; // Initialize for Security
		
					$replace['USER_NAME'] = $this->ifthd->input['user'];
					$replace['USER_EMAIL'] = $this->ifthd->input['email'];
					$replace['JOIN_DATE'] = $this->ifthd->ift_date( time(), '', 0, 0, 1, $mem_offset, 1 );
					$replace['APPROVE_LINK'] = $this->ifthd->core->cache['config']['hd_url'] .'/admin.php?section=manage&act=member&code=mod';
		
					$this->ifthd->send_email( $am['id'], 'new_user_admin_val', $replace );
				}
			}
		}

		#=============================
		# Redirect
		#=============================

		if ( $this->ifthd->core->cache['config']['email_validation'] && $this->ifthd->core->cache['config']['admin_validation'] )
		{
			$this->ifthd->skin->redirect( '?act=portal', 'new_user_val_both' );
		}
		elseif ( $this->ifthd->core->cache['config']['email_validation'] )
		{
			$this->ifthd->skin->redirect( '?act=portal', 'new_user_val_email' );
		}
		elseif ( $this->ifthd->core->cache['config']['admin_validation'] )
		{
			$this->ifthd->skin->redirect( '?act=portal', 'new_user_val_admin' );
		}
		else
		{
			$this->ifthd->skin->redirect( '?act=portal', 'new_user_no_val' );
		}
	}

	#=======================================
	# @ Validate Email
	# Validates the member's new email.
	#=======================================

	function validate_email()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! isset( $this->ifthd->input['key'] ) )
		{
			$this->ifthd->skin->error('no_email_val_key');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'validation',
							 				  	 'where'	=> array( array( 'id', '=', $this->ifthd->input['key'] ), array( 'type', '=', 1, 'and' ) ),
							 				  	 'limit'	=> array( 0, 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "Validation Key Not Found &#039;". $this->ifthd->input['key'] ."&#039;" );

			$this->ifthd->skin->error('no_email_val_key');
		}

		$v = $this->ifthd->core->db->fetch_row();

		if ( $v['new_email'] )
		{
			$this->ifthd->log( 'error', "Incorrect Use of Validation Key &#039;". $this->ifthd->input['key'] ."&#039;" );

			$this->ifthd->skin->error('no_email_val_key');
		}

		if ( $v['date'] < time() - ( 60 * 60 * $this->ifthd->core->cache['config']['val_hours_e'] ) )
		{
			$this->ifthd->core->db->construct( array(
												  	 'delete'	=> 'validation',
								 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['key'] ),
								 				  	 'limit'	=> array( 1 ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$this->ifthd->log( 'error', "Validation Key Expired &#039;". $this->ifthd->input['key'] ."&#039;", 1, $v['mid'] );

			$this->ifthd->skin->error('no_email_val_key');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'name', 'email_val', 'admin_val' ),
											  	 'from'		=> 'members',
							 				  	 'where'	=> array( 'id', '=', $v['mid'] ),
							 				  	 'limit'	=> array( 0, 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "Member #". $v['mid'] ." Not Found" );

			$this->ifthd->skin->error('no_member');
		}

		$m = $this->ifthd->core->db->fetch_row();
		
		if ( $m['email_val'] )
		{
			$this->ifthd->skin->error('already_validated');
		}

		#=============================
		# Check Admin Validation
		#=============================

		if ( ! $m['admin_val'] )
		{
			#=============================
			# Update Member
			#=============================

			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'members',
												  	 'set'		=> array( 'email_val' => 1 ),
								 				  	 'where'	=> array( 'id', '=', $v['mid'] ),
								 				  	 'limit'	=> array( 1 ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();
		}
		else
		{
			#=============================
			# Update Member
			#=============================

			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'members',
												  	 'set'		=> array( 'mgroup' => 1, 'email_val' => '1' ),
								 				  	 'where'	=> array( 'id', '=', $v['mid'] ),
								 				  	 'limit'	=> array( 1 ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			#=============================
			# Update Old Group
			#=============================

			$this->ifthd->core->db->next_no_quotes('set');

			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'groups',
												  	 'set'		=> array( 'g_members' => 'g_members-1' ),
								 				  	 'where'	=> array( 'g_id', '=', 3 ),
								 				  	 'limit'	=> array( 1 ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			#=============================
			# Update New Group
			#=============================

			$this->ifthd->core->db->next_no_quotes('set');

			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'groups',
												  	 'set'		=> array( 'g_members' => 'g_members+1' ),
								 				  	 'where'	=> array( 'g_id', '=', 1 ),
								 				  	 'limit'	=> array( 1 ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();
		}

		#=============================
		# Delete Validation
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'delete'	=> 'validation',
							 				  	 'where'	=> array( 'id', '=', $v['id'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		#=============================
		# Send Email
		#=============================

		$replace = ""; // Initialize for Security

		if ( ! $m['admin_val'] )
		{
			$this->ifthd->send_email( $m['id'], 'acc_almost_accivated', $replace );
		}
		else
		{
			$this->ifthd->send_email( $m['id'], 'acc_accivated', $replace );
		}

		$this->ifthd->log( 'member', "Email Validated &#039;". $m['name'] ."&#039;", 1, $m['id'] );

		#=============================
		# Redirect
		#=============================

		if ( ! $m['admin_val'] )
		{
			$this->ifthd->skin->redirect( '?act=portal', 'almost_acc_activate' );
		}
		else
		{
			$this->ifthd->skin->redirect( '?act=portal', 'success_acc_activate' );
		}
	}

	#=======================================
	# @ Resend Validation Form
	# Show resend validation form.
	#=======================================

	function resend_val_form($error='')
	{
		#=============================
		# Do Output
		#=============================

		if ( $error )
		{
			$this->ifthd->core->template->set_var( 'error', $this->ifthd->lang[ 'err_'. $error ] );
		}

		$this->ifthd->core->template->set_var( 'token_resend_val', $this->ifthd->create_token('resend_val') );

		$this->nav = array(
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=register&amp;code=sendval'>{$this->ifthd->lang['resend_val']}</a>",
						   );

		$this->ifthd->core->template->set_var( 'sub_tpl', 'reg_resend_val.tpl' );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => $this->ifthd->lang['resend_val'] ) );
	}

	#=======================================
	# @ Resend Validation
	# Resends an account validation email.
	#=======================================

	function resend_val()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->check_token('resend_val');

		if ( ! $this->ifthd->input['user'] && ! $this->ifthd->input['email'] )
		{
			$this->resend_val_form('no_user_or_email');
		}

		if ( ! $this->ifthd->validate_email( $this->ifthd->input['email'] ) && ! $this->ifthd->input['user'] )
		{
			$this->resend_val_form('no_email_valid');
		}

		$username = ""; // Initialize for Security
		$email = ""; // Initialize for Security

		if ( $this->ifthd->input['user'] )
		{
			$username = $this->ifthd->input['user'];
		}
		elseif ( $this->ifthd->input['email'] )
		{
			$email = $this->ifthd->input['email'];
		}

		if ( $username )
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'id', 'name', 'email_val', 'admin_val' ),
												  	 'from'		=> 'members',
								 				  	 'where'	=> array( 'name|lower', '=', strtolower( $username ) ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);
		}
		elseif( $email )
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'id', 'name', 'email_val', 'admin_val' ),
												  	 'from'		=> 'members',
								 				  	 'where'	=> array( 'email|lower', '=', strtolower( $email ) ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);
		}

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->resend_val_form('user_not_found');
		}

		$m = $this->ifthd->core->db->fetch_row();

		if ( $m['email_val'] )
		{
			$this->resend_val_form('user_already_active');
		}

		#=============================
		# Insert Validation
		#=============================

		$val_code = md5( 'vc' . $m['email'] . uniqid( rand(), true ) );

		$db_array = array(
						  'id'			=> $val_code,
						  'mid'			=> $m['id'],
						  'mname'		=> $m['name'],
						  'date'		=> time(),
						  'type'		=> 1,
						 );

		$this->ifthd->core->db->construct( array(
											  	 'insert'	=> 'validation',
											  	 'set'		=> $db_array,
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'security', "Email Validation Resent &#039;". strtolower( $email ) ."&#039;", 1, $m['id'] );

		#=============================
		# Send Email
		#=============================

		$replace = ""; // Initialize for Security

		$replace['USER_NAME'] = $m['name'];
		$replace['VAL_LINK'] = $this->ifthd->core->cache['config']['hd_url'] ."/index.php?act=register&code=validate&key=". $val_code;

		if ( $this->ifthd->core->cache['config']['admin_validation'] )
		{
			$this->ifthd->send_email( $m['id'], 'new_user_val_both', $replace );
		}
		else
		{
			$this->ifthd->send_email( $m['id'], 'new_user_val_email', $replace );
		}

		#=============================
		# Redirect
		#=============================

		$this->ifthd->skin->redirect( '?act=portal', 'new_user_val_resend' );
	}

	#=======================================
	# @ Forgot Password Form
	# Show forgot password form.
	#=======================================

	function forgot_pass_form($error='')
	{
		#=============================
		# Do Output
		#=============================

		if ( $error )
		{
			$this->ifthd->core->template->set_var( 'error', $this->ifthd->lang[ 'err_'. $error ] );
		}

		$this->ifthd->core->template->set_var( 'token_forgot_pass', $this->ifthd->create_token('forgot_pass') );

		$this->nav = array(
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=register&amp;code=forgot'>{$this->ifthd->lang['forgot_password']}</a>",
						   );

		$this->ifthd->core->template->set_var( 'sub_tpl', 'reg_forgot_pass.tpl' );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => $this->ifthd->lang['forgot_password'] ) );
	}

	#=======================================
	# @ Do Forgot Password
	# Sends a validation email to reset pass.
	#=======================================

	function do_forgot_pass()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->check_token('forgot_pass');

		if ( ! $this->ifthd->input['user'] && ! $this->ifthd->input['email'] )
		{
			$this->forgot_pass_form('no_user_or_email');
		}

		if ( ! $this->ifthd->validate_email( $this->ifthd->input['email'] ) && ! $this->ifthd->input['user'] )
		{
			$this->forgot_pass_form('no_email_valid');
		}

		$username = ""; // Initialize for Security
		$email = ""; // Initialize for Security

		if ( $this->ifthd->input['user'] )
		{
			$username = $this->ifthd->input['user'];
		}
		elseif ( $this->ifthd->input['email'] )
		{
			$email = $this->ifthd->input['email'];
		}

		if ( $username )
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'id', 'name', 'email_val', 'admin_val' ),
												  	 'from'		=> 'members',
								 				  	 'where'	=> array( 'name|lower', '=', strtolower( $username ) ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);

			$reference = strtolower( $username );
		}
		elseif( $email )
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'id', 'name', 'email_val', 'admin_val' ),
												  	 'from'		=> 'members',
								 				  	 'where'	=> array( 'email|lower', '=', strtolower( $email ) ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);

			$reference = strtolower( $email );
		}

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->forgot_pass_form('user_not_found');
		}

		$m = $this->ifthd->core->db->fetch_row();

		#=============================
		# Insert Validation
		#=============================

		$val_code = md5( 'vc' . $m['id'] . uniqid( rand(), true ) );

		$db_array = array(
						  'id'			=> $val_code,
						  'mid'			=> $m['id'],
						  'mname'		=> $m['name'],
						  'date'		=> time(),
						  'type'		=> 3,
						 );

		$this->ifthd->core->db->construct( array(
											  	 'insert'	=> 'validation',
											  	 'set'		=> $db_array,
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'security', "Password Reset Validation Sent &#039;". $reference ."&#039;", 1, $m['id'] );

		#=============================
		# Send Email
		#=============================

		$replace = ""; // Initialize for Security

		$replace['USER_NAME'] = $m['name'];
		$replace['VAL_LINK'] = $this->ifthd->core->cache['config']['hd_url'] ."/index.php?act=register&code=reset&key=". $val_code;

		$this->ifthd->send_email( $m['id'], 'reset_pass_val', $replace );

		#=============================
		# Redirect
		#=============================

		$this->ifthd->skin->redirect( '?act=portal', 'reset_pass_email_sent' );
	}

	#=======================================
	# @ Reset Password Form
	# Show reset password form.
	#=======================================

	function reset_pass_form($error="")
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->input['key'] )
		{
			$this->ifthd->skin->error('no_email_val_key');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'validation',
							 				  	 'where'	=> array( array( 'id', '=', $this->ifthd->input['key'] ), array( 'type', '=', 3, 'and' ) ),
							 				  	 'limit'	=> array( 0, 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "Validation Key Not Found &#039;". $this->ifthd->input['key'] ."&#039;" );

			$this->ifthd->skin->error('no_email_val_key');
		}

		$v = $this->ifthd->core->db->fetch_row();

		if ( $v['date'] < time() - ( 60 * 60 * $this->ifthd->core->cache['config']['val_hours_p'] ) )
		{
			$this->ifthd->core->db->construct( array(
												  	 'delete'	=> 'validation',
								 				  	 'where'	=> array( 'id', '=', $v['id'] ),
								 				  	 'limit'	=> array( 1 ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$this->ifthd->log( 'error', "Validation Key Expired &#039;". $v['id'] ."&#039;", 1, $v['mid'] );

			$this->ifthd->skin->error('no_email_val_key');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'name' ),
											  	 'from'		=> 'members',
							 				  	 'where'	=> array( 'id', '=', $v['mid'] ),
							 				  	 'limit'	=> array( 0, 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "Member #". $v['mid'] ." Not Found" );

			$this->ifthd->skin->error('no_member');
		}

		$m = $this->ifthd->core->db->fetch_row();

		#=============================
		# Do Output
		#=============================

		if ( $error )
		{
			$this->ifthd->core->template->set_var( 'error', $this->ifthd->lang[ 'err_'. $error ] );
		}

		$this->ifthd->core->template->set_var( 'token_reset_pass', $this->ifthd->create_token('reset_pass') );

		$this->nav = array(
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=register&amp;code=reset'>{$this->ifthd->lang['reset_password']}</a>",
						   );

		$this->ifthd->core->template->set_var( 'sub_tpl', 'reg_reset_pass.tpl' );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => $this->ifthd->lang['reset_password'] ) );
	}

	#=======================================
	# @ Do Reset Password
	# Reset password.
	#=======================================

	function do_reset_pass()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->check_token('reset_pass');

		if ( ! $this->ifthd->input['key'] )
		{
			$this->ifthd->skin->error('no_email_val_key');
		}

		if ( $this->ifthd->input['new_pass'] != $this->ifthd->input['new_pass_b'] )
		{
			$this->reset_pass_form('no_pass_match');
		}

		if ( ! $this->ifthd->input['new_pass'] )
		{
			$this->reset_pass_form('no_pass_short');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'validation',
							 				  	 'where'	=> array( array( 'id', '=', $this->ifthd->input['key'] ), array( 'type', '=', 3, 'and' ) ),
							 				  	 'limit'	=> array( 0, 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "Validation Key Not Found &#039;". $this->ifthd->input['key'] ."&#039;" );

			$this->ifthd->skin->error('no_email_val_key');
		}

		$v = $this->ifthd->core->db->fetch_row();

		if ( $v['date'] < time() - ( 60 * 60 * $this->ifthd->core->cache['config']['val_hours_p'] ) )
		{
			$this->ifthd->core->db->construct( array(
												  	 'delete'	=> 'validation',
								 				  	 'where'	=> array( 'id', '=', $v['id'] ),
								 				  	 'limit'	=> array( 1 ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$this->ifthd->log( 'error', "Validation Key Expired &#039;". $v['id'] ."&#039;", 1, $v['mid'] );

			$this->ifthd->skin->error('no_email_val_key');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'name' ),
											  	 'from'		=> 'members',
							 				  	 'where'	=> array( 'id', '=', $v['mid'] ),
							 				  	 'limit'	=> array( 0, 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "Member #". $v['mid'] ." Not Found" );

			$this->ifthd->skin->error('no_member');
		}

		$m = $this->ifthd->core->db->fetch_row();

		#=============================
		# Update Member
		#=============================

		$pass_salt = substr( md5( 'ps' . uniqid( rand(), true ) ), 0, 9 );
		$pass_hash = sha1( md5( $this->ifthd->input['new_pass'] . $pass_salt ) );

		$db_array = array(
						  'password'		=> $pass_hash,
						  'pass_salt'		=> $pass_salt,
						  'login_key'		=> str_replace( "=", "", base64_encode( strrev( crypt( $this->ifthd->input['new_pass'] ) ) ) ),
						  );

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'members',
											  	 'set'		=> $db_array,
											  	 'where'	=> array( 'id', '=', $v['mid'] ),
											  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'member', "Password Reset &#039;". $v['mname'] ."&#039;" );
		$this->ifthd->log( 'security', "Password Reset &#039;". $v['mname'] ."&#039;" );

		#=============================
		# Delete Validation
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'delete'	=> 'validation',
							 				  	 'where'	=> array( 'id', '=', $v['id'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		#=============================
		# Redirect
		#=============================

		$this->ifthd->skin->redirect( '?act=portal', 'reset_pass_success' );
	}

}

?>