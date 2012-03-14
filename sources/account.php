<?php

/*
#======================================================
|	Актуальный русский перевод находится здесь:
|	http://code.google.com/p/trellis-desk-translate-russian/
|	Следите за обновлениями.
|	Перевод предоставлен "as is",
|	сделан для своих нужд и не притендует на
|	авторские права и права третих лиц.
|	Оригинальные права принадлежат только их владельцам.
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
|    | Account :: Sources
#======================================================
*/

class account {

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

		if ( ! $this->ifthd->member['id'] )
		{
			$this->ifthd->skin->error( 'must_be_user', 1 );
		}

		#=============================
		# Initialize
		#=============================

		$this->ifthd->load_lang('account');

		switch( $this->ifthd->input['code'] )
    	{
    		case 'edit':
				$this->modify_form();
    		break;
    		case 'pass':
				$this->pass_form();
    		break;
    		case 'email':
				$this->email_form();
    		break;

    		case 'doedit':
				$this->do_modify();
    		break;
    		case 'dopass':
				$this->do_pass();
    		break;
    		case 'doemail':
				$this->do_email();
    		break;

    		case 'valemail':
				$this->validate_email();
    		break;

    		default:
    			$this->show_overview();
    		break;
		}
	}

	#=======================================
	# @ Show Overview
	# Show overview of account information.
	#=======================================

	function show_overview()
	{
		#=============================
		# Fix Up Information
		#=============================

		$this->ifthd->core->template->set_var( 'human_joined', $this->ifthd->ift_date( $this->ifthd->member['joined'] ) );

		#=============================
		# Custom Profile Fields
		#=============================

		if ( is_array( $this->ifthd->core->cache['pfields'] ) )
		{
			$cpfields = array(); // Initialize for Security
			$row_count = 1; // Initialize for Security

			$cpfdata = unserialize( $this->ifthd->member['cpfields'] );

			foreach( $this->ifthd->core->cache['pfields'] as $id => $f )
			{				
				if ( ! $f['staff'] )
				{					
					$f_perm = unserialize( $f['perms'] );

					if ( $f_perm[ $this->ifthd->member['mgroup'] ] )
					{
						$row_count ++;
							
						( $row_count & 1 ) ? $f['class'] = 1 : $f['class'] = 2;
						
						if ( $f['type'] == 'dropdown' || $f['type'] == 'radio' )
						{
							$options = explode( "\n", $f['extra'] );

							while ( list( , $opt ) = each( $options ) )
							{
								$our_opt = explode( "=", $opt );

								$soggy[ $our_opt[0] ] = $our_opt[1];
							}

							$f['value'] = $soggy[ $cpfdata[ $f['fkey'] ] ];
						}
						else
						{
							$f['value'] = $cpfdata[ $f['fkey'] ];

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

						$cpfields[] = $f;

						$soggy = ""; // Reset
					}
				}
			}

			$this->ifthd->core->template->set_var( 'cpfields', $cpfields );
		}

		#=============================
		# Do Output
		#=============================

		$this->ifthd->core->template->set_var( 'sub_tpl', 'my_account.tpl' );

		$this->nav = array(
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=myaccount'>{$this->ifthd->lang['my_account']}</a>",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => $this->ifthd->lang['my_account'] ) );
	}

	#=======================================
	# @ Modify Form
	# Show modify account information form.
	#=======================================

	function modify_form($error="", $extra="")
	{
		#=============================
		# Pre-Selects
		#=============================

		( $this->ifthd->member['dst_active'] ? $select['dst_active_a'] = ' checked="checked"' : $select['dst_active_b'] = ' checked="checked"' );
		( $this->ifthd->member['email_notify'] ? $select['email_notify_a'] = ' checked="checked"' : $select['email_notify_b'] = ' checked="checked"' );
		( $this->ifthd->member['email_html'] ? $select['email_html_a'] = ' checked="checked"' : $select['email_html_b'] = ' checked="checked"' );
		( $this->ifthd->member['use_rte'] ? $select['use_rte_a'] = ' checked="checked"' : $select['use_rte_b'] = ' checked="checked"' );

		if ( $this->ifthd->member['email_new_ticket'] ) $select['email_new_ticket'] = ' checked="checked"';
		if ( $this->ifthd->member['email_ticket_reply'] ) $select['email_ticket_reply'] = ' checked="checked"';
		if ( $this->ifthd->member['email_announce'] ) $select['email_announce'] = ' checked="checked"';
		if ( $this->ifthd->member['email_staff_new_ticket'] ) $select['email_staff_new_ticket'] = ' checked="checked"';
		if ( $this->ifthd->member['email_staff_ticket_reply'] ) $select['email_staff_ticket_reply'] = ' checked="checked"';

		$this->ifthd->core->template->set_var( 'time_zone_drop', $this->ifthd->build_time_zone_drop( $this->ifthd->member['time_zone'] ) );
		$this->ifthd->core->template->set_var( 'lang_drop', $this->ifthd->build_lang_drop( $this->ifthd->member['lang'] ) );
		$this->ifthd->core->template->set_var( 'skin_drop', $this->ifthd->build_skin_drop( $this->ifthd->member['skin'] ) );

		#=============================
		# Custom Profile Fields
		#=============================

		if ( is_array( $this->ifthd->core->cache['pfields'] ) )
		{
			$cpfields = array(); // Initialize for Security
			$row_count = 0; // Initialize for Security

			$cpfdata = unserialize( $this->ifthd->member['cpfields'] );

			foreach( $this->ifthd->core->cache['pfields'] as $id => $f )
			{
				if ( ! $f['staff'] )
				{
					$f_perm = unserialize( $f['perms'] );

					if ( $f_perm[ $this->ifthd->member['mgroup'] ] )
					{
						$row_count ++;
							
						( $row_count & 1 ) ? $f['class'] = 2 : $f['class'] = 1;
						
						if ( ! $f['required'] )
						{
							$f['optional'] = $this->ifthd->lang['optional'];
						}

						if ( $error )
						{
							$f['value'] = $this->ifthd->input[ 'cpf_'. $f['fkey'] ];
						}
						else
						{
							$f['value'] = $cpfdata[ $f['fkey'] ];
						}

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
			}

			$this->ifthd->core->template->set_var( 'cpfields', $cpfields );
		}

		#=============================
		# Do Output
		#=============================

		if ( ! $this->ifthd->member['g_change_lang'] ) $lang_dis = ' disabled = "disabled"';
		if ( ! $this->ifthd->member['g_change_skin'] ) $skin_dis = ' disabled = "disabled"';

		if ( $error )
		{
			$error = $this->ifthd->lang[ 'err_'. $error ];
			
			if ( $extra ) $error .= ' '. $extra;
			
			$this->ifthd->core->template->set_var( 'error', $error );
		}

		$this->ifthd->core->template->set_var( 'token_account_edit', $this->ifthd->create_token('account_edit') );

		$this->ifthd->core->template->set_var( 'time_now', $this->ifthd->ift_date( time(), '', 0, 0, 1 ) );
		$this->ifthd->core->template->set_var( 'select', $select );
		$this->ifthd->core->template->set_var( 'lang_dis', $lang_dis );
		$this->ifthd->core->template->set_var( 'skin_dis', $skin_dis );

		$this->nav = array(
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=myaccount'>{$this->ifthd->lang['my_account']}</a>",
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=myaccount&amp;code=edit'>{$this->ifthd->lang['modify_account']}</a>",
						   );

		$this->ifthd->core->template->set_var( 'sub_tpl', 'acc_modify.tpl' );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => $this->ifthd->lang['modify_account'] ) );
	}

	#=======================================
	# @ Do Modify
	# Update the member information.
	#=======================================

	function do_modify()
	{
		$this->ifthd->check_token('account_edit');

		#=============================
		# Custom Profile Fields
		#=============================

		if ( is_array( $this->ifthd->core->cache['pfields'] ) )
		{
			$cpfvalues = ""; // Initialize for Security

			while ( list( $id, $f ) = each( $this->ifthd->core->cache['pfields'] ) )
			{
				if ( ! $f['staff'] )
				{
					$f_perm = unserialize( $f['perms'] );

					if ( $f_perm[ $this->ifthd->member['mgroup'] ] )
					{
						if ( $f['required'] && $f['type'] != 'checkbox' )
						{
							if ( ! $this->ifthd->input[ 'cpf_'. $f['fkey'] ] )
							{
								$this->modify_form( 'no_cpfield', $f['name'] );
							}
						}

						$cpfvalues[ $f['fkey'] ] = $this->ifthd->input[ 'cpf_'. $f['fkey'] ];
					}
				}
			}
		}

		#=============================
		# Update Member
		#=============================

		$db_array = array(
						  'email_notify'				=> intval( $this->ifthd->input['email_notify'] ),
						  'email_html'					=> intval( $this->ifthd->input['email_html'] ),
						  'email_new_ticket'			=> intval( $this->ifthd->input['email_new_ticket'] ),
						  'email_ticket_reply'			=> intval( $this->ifthd->input['email_ticket_reply'] ),
						  'email_announce'				=> intval( $this->ifthd->input['email_announce'] ),
						  'email_staff_new_ticket'		=> intval( $this->ifthd->input['email_staff_new_ticket'] ),
						  'email_staff_ticket_reply'	=> intval( $this->ifthd->input['email_staff_ticket_reply'] ),
						  'time_zone'					=> $this->ifthd->input['time_zone'],
						  'dst_active'					=> $this->ifthd->input['dst_active'],
						  'use_rte'						=> intval( $this->ifthd->input['use_rte'] ),
						  'cpfields'					=> serialize($cpfvalues),
						  );

		if ( $this->ifthd->member['g_change_lang'] ) $db_array['lang'] = $this->ifthd->input['user_lang'];
		if ( $this->ifthd->member['g_change_skin'] ) $db_array['skin'] = intval( $this->ifthd->input['user_skin'] );

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'members',
											  	 'set'		=> $db_array,
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->member['id'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'member', "Аккаунт изменён &#039;". $this->ifthd->member['name'] ."&#039;" );

		#=============================
		# Redirect
		#=============================

		$this->ifthd->skin->redirect( '?act=myaccount', 'update_my_account' );
	}

	#=======================================
	# @ Pass Form
	# Show modify password form.
	#=======================================

	function pass_form($error='')
	{
		#=============================
		# Do Output
		#=============================

		if ( $error )
		{
			$this->ifthd->core->template->set_var( 'error', $this->ifthd->lang[ 'err_'. $error ] );
		}

		$this->ifthd->core->template->set_var( 'token_account_pass', $this->ifthd->create_token('account_pass') );

		$this->nav = array(
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=myaccount'>{$this->ifthd->lang['my_account']}</a>",
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=myaccount&amp;code=pass'>{$this->ifthd->lang['change_password']}</a>",
						   );

		$this->ifthd->core->template->set_var( 'sub_tpl', 'acc_change_pass.tpl' );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => $this->ifthd->lang['change_password'] ) );
	}

	#=======================================
	# @ Do Pass
	# Updates the member password.
	#=======================================

	function do_pass()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->check_token('account_pass');

		if ( ! $this->ifthd->input['current_pass'] )
		{
			$this->pass_form('no_pass_short');
		}

		if ( $this->ifthd->input['new_pass'] != $this->ifthd->input['new_pass_b'] )
		{
			$this->pass_form('no_pass_match');
		}

		if ( ! $this->ifthd->input['new_pass'] )
		{
			$this->pass_form('no_new_pass_short');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'password', 'pass_salt' ),
											  	 'from'		=> 'members',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->member['id'] ),
							 				  	 'limit'	=> array( 0, 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$mem = $this->ifthd->core->db->fetch_row();

		if ( sha1( md5( $this->ifthd->input['current_pass'] . $mem['pass_salt'] ) ) != $mem['password'] )
		{
			$this->pass_form('login_no_pass');
		}

		$mem['password'] = ""; // Security

		#=============================
		# Update Member
		#=============================

		$pass_salt = substr( md5( 'ps'. uniqid( rand(), true ) ), 0, 9 );
		$pass_hash = sha1( md5( $this->ifthd->input['new_pass'] . $pass_salt ) );

		$db_array = array(
						  'password'		=> $pass_hash,
						  'pass_salt'		=> $pass_salt,
						  'login_key'		=> str_replace( "=", "", base64_encode( strrev( crypt( $this->ifthd->input['new_pass'] ) ) ) ),
						  );

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'members',
											  	 'set'		=> $db_array,
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->member['id'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'member', "Пароль изменен &#039;". $this->ifthd->member['name'] ."&#039;" );
		$this->ifthd->log( 'security', "Пароль изменен &#039;". $this->ifthd->member['name'] ."&#039;" );

		#=============================
		# Redirect
		#=============================

		$this->ifthd->skin->redirect( '?act=myaccount', 'update_my_pass' );
	}

	#=======================================
	# @ Email Form
	# Show change email form.
	#=======================================

	function email_form($error='')
	{
		#=============================
		# Do Output
		#=============================

		if ( $error )
		{
			$this->ifthd->core->template->set_var( 'error', $this->ifthd->lang[ 'err_'. $error ] );
		}

		$this->ifthd->core->template->set_var( 'token_account_email', $this->ifthd->create_token('account_email') );

		$this->nav = array(
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=myaccount'>{$this->ifthd->lang['my_account']}</a>",
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=myaccount&amp;code=email'>{$this->ifthd->lang['change_email']}</a>",
						   );

		$this->ifthd->core->template->set_var( 'sub_tpl', 'acc_change_email.tpl' );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => $this->ifthd->lang['change_email'] ) );
	}

	#=======================================
	# @ Do Email
	# Updates the member email address.
	#=======================================

	function do_email()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->check_token('account_email');

		if ( ! $this->ifthd->validate_email( $this->ifthd->input['new_email'] ) )
		{
			$this->email_form('no_email_valid');
		}

		if ( $this->ifthd->input['new_email'] != $this->ifthd->input['new_email_b'] )
		{
			$this->email_form('no_email_match');
		}

		if ( $this->ifthd->member['email'] == $this->ifthd->input['new_email'] )
		{
			$this->email_form('no_email_change');
		}

		#=============================
		# Insert Validation
		#=============================

		$val_code = md5 ( 'vc'. $this->ifthd->input['new_email'] . uniqid( rand(), true ) );

		$db_array = array(
						  'id'			=> $val_code,
						  'mid'			=> $this->ifthd->member['id'],
						  'mname'		=> $this->ifthd->member['name'],
						  'new_email'	=> $this->ifthd->input['new_email'],
						  'date'		=> time(),
						  'type'		=> 2,
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

		$replace['VAL_LINK'] = $this->ifthd->core->cache['config']['hd_url'] ."/index.php?act=myaccount&code=valemail&key=". $val_code;

		$this->ifthd->send_email( $this->ifthd->member['id'], 'change_email_val', $replace, array( 'over_email' => $this->ifthd->input['new_email'] ) );

		$this->ifthd->log( 'member', "Email адресс изменен &#039;". $this->ifthd->member['name'] ."&#039;" );
		$this->ifthd->log( 'security', "Email адресс изменен &#039;". $this->ifthd->member['name'] ."&#039;" );

		#=============================
		# Redirect
		#=============================

		$this->ifthd->skin->redirect( '?act=myaccount', 'change_val_email' );
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
							 				  	 'where'	=> array( array( 'id', '=', $this->ifthd->input['key'] ), array( 'type', '=', 2, 'and' ) ),
							 				  	 'limit'	=> array( 0, 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "Валидационный ключ не найден &#039;". $this->ifthd->input['key'] ."&#039;" );

			$this->ifthd->skin->error('no_email_val_key');
		}

		$v = $this->ifthd->core->db->fetch_row();

		if ( $v['date'] < time() - ( 60 * 60 * $this->ifthd->core->cache['config']['val_hours_e'] ) )
		{
			$this->ifthd->core->db->construct( array(
												  	 'delete'	=> 'validation',
								 				  	 'where'	=> array( 'id', '=', $v['id'] ),
								 				  	 'limit'	=> array( 1 ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$this->ifthd->log( 'error', "Валидационный ключ истёк &#039;". $v['id'] ."&#039;", 1, $v['mid'] );

			$this->ifthd->skin->error('no_email_val_key');
		}

		#=============================
		# Update Member
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'members',
											  	 'set'		=> array( 'email' => $v['new_email'] ),
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->member['id'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'member', "Аккаунт утверждён &#039;". $v['mname'] ."&#039;", 1, $v['mid'] );
		$this->ifthd->log( 'security', "Аккаунт утверждён &#039;". $v['mname'] ."&#039;", 1, $v['mid'] );

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

		$this->ifthd->skin->redirect( '?act=myaccount', 'update_my_email' );
	}

}

?>