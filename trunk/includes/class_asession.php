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
|    | Admin Session Class :: Session Handler
#======================================================
*/

class asession {

	var $member	= array();

	#=======================================
	# @ Load Session
	# Loads the session.  What else? :D
	#=======================================

	function load_session()
	{
		$authorized = 0; // Initialize for Security

		#=============================
		# Kill Any Bad Sessions
		#=============================

		$this->kill_old_sessions();
		$this->kill_old_tokens();

		#=============================
		# Get Information
		#=============================

		$cookie_sid = $this->ifthd->get_cookie('hdasid');

		#=============================
		# If We Have A Session Cookie
		#=============================

		if ( $cookie_sid )
		{
			#=============================
			# Load Member
			#=============================

			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 's' => 'all',
												  	 					  'm' => array( 'id', 'name', 'email', 'login_key', 'mgroup', 'title', 'joined', 'ipadd', 'time_zone', 'dst_active', 'lang', 'skin', 'use_rte', 'cpfields', 'rss_key', 'signature', 'auto_sig', 'assigned' ),
												  	 					  'g' => 'all',
												  	 					 ),
												  	 'from'		=> array( 's' => 'asessions' ),
												  	 'join'		=> array( array( 'from' => array( 'm' => 'members' ), 'where' => array( 's' => 's_mid', '=', 'm' => 'id' ) ), array( 'from' => array( 'g' => 'groups' ), 'where' => array( 'g' => 'g_id', '=', 'm' => 'mgroup' ) ) ),
								 				  	 'where'	=> array( array( 's' => 's_id' ), '=', $cookie_sid ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			if ( $this->ifthd->core->db->get_num_rows() )
			{
				$this->member = $this->ifthd->core->db->fetch_row();

				if ( $this->member['g_acp_access'] )
				{
					#=============================
					# Update Ticket
					#=============================

					if ( $this->ifthd->input['section'] != 'manage' || $this->ifthd->input['act'] != 'tickets' || $this->ifthd->input['code'] != 'view' )
					{
						if ( $this->member['s_inticket'] )
						{
							$this->ifthd->core->db->construct( array(
																  	 'select'	=> array( 'status' ),
																  	 'from'		=> 'tickets',
												 				  	 'where'	=> array( 'id', '=', $this->member['s_inticket'] ),
												 				  	 'limit'	=> array( 0, 1 ),
												 		  	  ) 	);

							$this->ifthd->core->db->execute();

							if ( $this->ifthd->core->db->get_num_rows() )
							{
								$t = $this->ifthd->core->db->fetch_row();

								if ( $t['status'] == 2 )
								{
									$this->ifthd->core->db->construct( array(
																		  	 'update'	=> 'tickets',
																		  	 'set'		=> array( 'status' => 1 ),
												 				  	 		 'where'	=> array( 'id', '=', $this->member['s_inticket'] ),
												 				  	 		 'limit'	=> array( 1 ),
														 		  	  ) 	);

									$this->ifthd->core->db->execute();
								}
							}
						}
					}

					#=============================
					# Update Session
					#=============================

					$db_array = array(
									  's_location'	=> $this->ifthd->input['act'],
									  's_time'		=> time(),
									  );

					if ( $this->ifthd->input['section'] == 'manage' && $this->ifthd->input['act'] == 'tickets' && $this->ifthd->input['code'] == 'view' )
					{
						$db_array['s_inticket'] = $this->ifthd->input['id'];
					}
					else
					{
						$db_array['s_inticket'] = 0;
					}

					$this->ifthd->core->db->construct( array(
														  	 'update'	=> 'asessions',
														  	 'set'		=> $db_array,
										 				  	 'where'	=> array( 's_id', '=', $cookie_sid ),
										 				  	 'limit'	=> array( 1 ),
										 		  	  ) 	);

					$this->ifthd->core->db->next_shutdown();
					$this->ifthd->core->db->execute();

					$this->ifthd->set_cookie( 'hdasid', $cookie_sid, time() + ( $this->ifthd->core->cache['config']['acp_session_timeout'] * 60 * 60 ) );

					#=============================
					# ACP Permissions
					#=============================

					if ( $this->member['id'] == 1 )
					{
						$this->member['acp'] = unserialize('a:77:{s:5:"admin";i:1;s:10:"admin_logs";i:1;s:16:"admin_logs_admin";i:1;s:17:"admin_logs_member";i:1;s:16:"admin_logs_email";i:1;s:16:"admin_logs_error";i:1;s:19:"admin_logs_security";i:1;s:17:"admin_logs_ticket";i:1;s:16:"admin_logs_prune";i:1;s:6:"manage";i:1;s:13:"manage_ticket";i:1;s:19:"manage_ticket_reply";i:1;s:25:"manage_ticket_assign_self";i:1;s:24:"manage_ticket_assign_any";i:1;s:18:"manage_ticket_hold";i:1;s:22:"manage_ticket_escalate";i:1;s:18:"manage_ticket_move";i:1;s:19:"manage_ticket_close";i:1;s:20:"manage_ticket_delete";i:1;s:20:"manage_ticket_reopen";i:1;s:13:"manage_canned";i:1;s:17:"manage_canned_add";i:1;s:18:"manage_canned_edit";i:1;s:20:"manage_canned_delete";i:1;s:13:"manage_depart";i:1;s:17:"manage_depart_add";i:1;s:18:"manage_depart_edit";i:1;s:20:"manage_depart_delete";i:1;s:21:"manage_depart_reorder";i:1;s:21:"manage_depart_cfields";i:1;s:15:"manage_announce";i:1;s:19:"manage_announce_add";i:1;s:20:"manage_announce_edit";i:1;s:22:"manage_announce_delete";i:1;s:13:"manage_member";i:1;s:17:"manage_member_add";i:1;s:18:"manage_member_edit";i:1;s:20:"manage_member_delete";i:1;s:21:"manage_member_approve";i:1;s:21:"manage_member_cfields";i:1;s:19:"manage_member_staff";i:1;s:12:"manage_group";i:1;s:16:"manage_group_add";i:1;s:17:"manage_group_edit";i:1;s:19:"manage_group_delete";i:1;s:14:"manage_article";i:1;s:18:"manage_article_add";i:1;s:19:"manage_article_edit";i:1;s:21:"manage_article_delete";i:1;s:10:"manage_cat";i:1;s:14:"manage_cat_add";i:1;s:15:"manage_cat_edit";i:1;s:17:"manage_cat_delete";i:1;s:12:"manage_pages";i:1;s:16:"manage_pages_add";i:1;s:17:"manage_pages_edit";i:1;s:19:"manage_pages_delete";i:1;s:15:"manage_settings";i:1;s:22:"manage_settings_update";i:1;s:4:"look";i:1;s:9:"look_skin";i:1;s:16:"look_skin_manage";i:1;s:15:"look_skin_tools";i:1;s:16:"look_skin_import";i:1;s:16:"look_skin_export";i:1;s:9:"look_lang";i:1;s:16:"look_lang_manage";i:1;s:15:"look_lang_tools";i:1;s:16:"look_lang_import";i:1;s:16:"look_lang_export";i:1;s:5:"tools";i:1;s:11:"tools_maint";i:1;s:19:"tools_maint_recount";i:1;s:17:"tools_maint_clean";i:1;s:16:"tools_maint_optm";i:1;s:20:"tools_maint_syscheck";i:1;s:12:"tools_backup";i:1;}');
					}
					else
					{
						$this->member['acp'] = unserialize( $this->member['g_acp_perm'] );
					}

					$authorized = 1;
				}
			}
		}

		#=============================
		# If We Are Not Authorized
		#=============================

		if ( ! $authorized )
		{
			$this->member['id']	= 0;

			$this->ifthd->delete_cookie( 'hdasid' );

			$this->ifthd->skin->error( 'must_login', 1 );
		}

		return $this->member;
	}

	#=======================================
	# @ Do Login
	# Attempt to login.
	#=======================================

	function do_login()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! isset( $this->ifthd->input['username'] ) || ! isset( $this->ifthd->input['password'] ) )
		{
			$this->ifthd->skin->error( 'fill_form_completely', 1 );
		}

		#=============================
		# Select Member
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'm' => array( 'id', 'name', 'email', 'password', 'pass_salt', 'login_key', 'mgroup', 'title', 'joined', 'ipadd', 'time_zone', 'dst_active', 'lang', 'skin', 'use_rte', 'cpfields', 'rss_key', 'signature', 'auto_sig', 'assigned' ),
											  	 					  'g' => 'all',
											  	 					 ),
											  	 'from'		=> array( 'm' => 'members' ),
											  	 'join'		=> array( array( 'from' => array( 'g' => 'groups' ), 'where' => array( 'g' => 'g_id', '=', 'm' => 'mgroup' ) ) ),
							 				  	 'where'	=> array( array( 'm' => 'name|lower' ), '=', strtolower( $this->ifthd->input['username'] ) ),
							 				  	 'limit'	=> array( 0, 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'admin', "ACP Failed Login Attempt &#039;". $this->ifthd->input['username'] ."&#039;", 2 );
			$this->ifthd->log( 'security', "ACP Failed Login Attempt &#039;". $this->ifthd->input['username'] ."&#039;", 2 );

			$this->ifthd->skin->error( 'login_no_user', 1 );
		}

		$mem = $this->ifthd->core->db->fetch_row();

		#=============================
		# Compare Password
		#=============================

		if ( sha1( md5( $this->ifthd->input['password'] . $mem['pass_salt'] ) ) == $mem['password'] )
		{
			// Permission
			if ( ! $mem['g_acp_access'] )
			{
				$this->ifthd->log( 'admin', "ACP Login Blocked Access &#039;". $mem['name'] ."&#039;", 2, $mem['id'] );
				$this->ifthd->log( 'security', "ACP Login Blocked Access &#039;". $mem['name'] ."&#039;", 2, $mem['id'] );

				$this->ifthd->skin->error( 'login_no_admin', 1 );
			}

			#=============================
			# Create Session
			#=============================

			$new_session = md5( 's' . time() . $mem['id'] . uniqid( rand(), true ) );

			$db_array = array(
							  's_id'			=> $new_session,
							  's_mid'			=> $mem['id'],
							  's_mname'			=> $mem['name'],
							  's_ipadd'			=> $this->ifthd->input['ip_address'],
							  's_location'		=> $this->ifthd->input['act'],
							  's_time'			=> time(),
							  );
			
			if ( $this->ifthd->input['section'] == 'manage' && $this->ifthd->input['act'] == 'tickets' && $this->ifthd->input['code'] == 'view' )
			{
				$db_array['s_inticket'] = $this->ifthd->input['id'];
			}
			else
			{
				$db_array['s_inticket'] = 0;
			}

			$this->ifthd->core->db->construct( array(
												  	 'insert'	=> 'asessions',
												  	 'set'		=> $db_array,
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$this->ifthd->set_cookie( 'hdasid', $new_session, time() + ( $this->ifthd->core->cache['config']['acp_session_timeout'] * 60 * 60 ) );

			$this->ifthd->log( 'admin', "ACP Successful Login &#039;". $mem['name'] ."&#039;", 1, $mem['id'] );
			
			// Play It Safe
			$mem['password'] = $mem['pass_salt'] = $mem['login_key'] = "";
			
			$mem = array_merge( $mem, $db_array );
			
			$this->member = $mem;
			
			#=============================
			# ACP Permissions
			#=============================

			if ( $this->member['id'] == 1 )
			{
				$this->member['acp'] = unserialize('a:76:{s:5:"admin";i:1;s:10:"admin_logs";i:1;s:16:"admin_logs_admin";i:1;s:17:"admin_logs_member";i:1;s:16:"admin_logs_email";i:1;s:16:"admin_logs_error";i:1;s:19:"admin_logs_security";i:1;s:17:"admin_logs_ticket";i:1;s:16:"admin_logs_prune";i:1;s:6:"manage";i:1;s:13:"manage_ticket";i:1;s:19:"manage_ticket_reply";i:1;s:25:"manage_ticket_assign_self";i:1;s:24:"manage_ticket_assign_any";i:1;s:18:"manage_ticket_hold";i:1;s:22:"manage_ticket_escalate";i:1;s:18:"manage_ticket_move";i:1;s:19:"manage_ticket_close";i:1;s:20:"manage_ticket_delete";i:1;s:20:"manage_ticket_reopen";i:1;s:13:"manage_canned";i:1;s:17:"manage_canned_add";i:1;s:18:"manage_canned_edit";i:1;s:20:"manage_canned_delete";i:1;s:13:"manage_depart";i:1;s:17:"manage_depart_add";i:1;s:18:"manage_depart_edit";i:1;s:20:"manage_depart_delete";i:1;s:21:"manage_depart_reorder";i:1;s:21:"manage_depart_cfields";i:1;s:15:"manage_announce";i:1;s:19:"manage_announce_add";i:1;s:20:"manage_announce_edit";i:1;s:22:"manage_announce_delete";i:1;s:13:"manage_member";i:1;s:17:"manage_member_add";i:1;s:18:"manage_member_edit";i:1;s:20:"manage_member_delete";i:1;s:21:"manage_member_approve";i:1;s:21:"manage_member_cfields";i:1;s:12:"manage_group";i:1;s:16:"manage_group_add";i:1;s:17:"manage_group_edit";i:1;s:19:"manage_group_delete";i:1;s:14:"manage_article";i:1;s:18:"manage_article_add";i:1;s:19:"manage_article_edit";i:1;s:21:"manage_article_delete";i:1;s:10:"manage_cat";i:1;s:14:"manage_cat_add";i:1;s:15:"manage_cat_edit";i:1;s:17:"manage_cat_delete";i:1;s:12:"manage_pages";i:1;s:16:"manage_pages_add";i:1;s:17:"manage_pages_edit";i:1;s:19:"manage_pages_delete";i:1;s:15:"manage_settings";i:1;s:22:"manage_settings_update";i:1;s:4:"look";i:1;s:9:"look_skin";i:1;s:16:"look_skin_manage";i:1;s:15:"look_skin_tools";i:1;s:16:"look_skin_import";i:1;s:16:"look_skin_export";i:1;s:9:"look_lang";i:1;s:16:"look_lang_manage";i:1;s:15:"look_lang_tools";i:1;s:16:"look_lang_import";i:1;s:16:"look_lang_export";i:1;s:5:"tools";i:1;s:11:"tools_maint";i:1;s:19:"tools_maint_recount";i:1;s:17:"tools_maint_clean";i:1;s:16:"tools_maint_optm";i:1;s:20:"tools_maint_syscheck";i:1;s:12:"tools_backup";i:1;}');
			}
			else
			{
				$this->member['acp'] = unserialize( $this->member['g_acp_perm'] );
			}

			#=============================
			# Redirect
			#=============================

			/*if ( $this->ifthd->input['extra_l'] )
			{
				$this->ifthd->skin->redirect( '?'. str_replace( "&amp;", "&", $this->ifthd->input['extra_l'] ), 'login_success' );
			}
			else
			{
				$this->ifthd->skin->redirect( '?act=admin', 'login_success' );
			}*/

			return $this->member;
		}
		else
		{
			$this->ifthd->log( 'admin', "ACP Failed Login Attempt &#039;". $mem['name'] ."&#039;", 2, $mem['id'] );
			$this->ifthd->log( 'security', "ACP Failed Login Attempt &#039;". $mem['name'] ."&#039;", 2, $mem['id'] );

			$this->ifthd->skin->error( 'login_no_pass', 1 );
		}
	}

	#=======================================
	# @ Do Logout
	# Attempt to logout.
	#=======================================

	function do_logout()
	{
		#=============================
		# Delete Cookie
		#=============================

		$this->ifthd->delete_cookie('hdasid');

		#=============================
		# Update Ticket
		#=============================

		if ( $this->member['s_inticket'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'tickets',
												  	 'set'		=> array( 'status' => 1 ),
						 				  	 		 'where'	=> array( array( 'id', '=', $this->member['s_inticket'] ), array( 'status', '=', 2, 'and' ) ),
								 		  	  ) 	);
	
			$this->ifthd->core->db->next_shutdown();
			$this->ifthd->core->db->execute();
		}

		#=============================
		# Delete Session
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'delete'	=> 'asessions',
							 				  	 'where'	=> array( 's_id', '=', $this->member['s_id'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		#=============================
		# Redirect
		#=============================

		$this->ifthd->skin->redirect( '?act=home', 'logout_success' );
	}

	#=======================================
	# @ Kill Old Sessions
	# Kills sessions older than the session
	# timeout (defined in ACP).
	#=======================================

	function kill_old_sessions()
	{
		#=============================
		# Grab Sessions
		#=============================

		$timeout = time() - ( $this->ifthd->core->cache['config']['acp_session_timeout'] * 60 * 60 );

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 's_id', 's_inticket' ),
											  	 'from'		=> 'asessions',
							 				  	 'where'	=> array( 's_time' ,'<=', $timeout ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( $num_killed = $this->ifthd->core->db->get_num_rows() )
		{
			$sessions = array(); // Initialize For Security
			$tickets = array(); // Initialize For Security

			while ( $s = $this->ifthd->core->db->fetch_row() )
			{
				$sessions[] = $s['s_id'];
				$tickets[] = $s['s_inticket'];
			}

			#=============================
			# Update Tickets
			#=============================

			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'tickets',
												  	 'set'		=> array( 'status' => 1 ),
						 				  	 		 'where'	=> array( array( 'id', 'in', $tickets ), array( 'status', '=', 2, 'and' ) ),
								 		  	  ) 	);

			$this->ifthd->core->db->next_shutdown();
			$this->ifthd->core->db->execute();

			#=============================
			# Delete Sessions
			#=============================

			$this->ifthd->core->db->construct( array(
												  	 'delete'	=> 'asessions',
								 				  	 'where'	=> array( 's_id' ,'in', $sessions ),
								 		  	  ) 	);

			$this->ifthd->core->db->next_shutdown();
			$this->ifthd->core->db->execute();
		}

		return $num_killed;
	}

	#=======================================
	# @ Kill Old Tokens
	# Kills tokens older than 1 hour.
	#=======================================

	function kill_old_tokens()
	{
		if ( $this->ifthd->core->cache['config']['use_form_tokens'] )
		{
			$timeout = time() - ( 60 * 60 );

			$this->ifthd->core->db->construct( array(
												  	 'delete'	=> 'tokens',
								 				  	 'where'	=> array( 'date', '<=', $timeout ),
								 		  	  ) 	);

			$this->ifthd->core->db->next_shutdown();
			$this->ifthd->core->db->execute();

			$num_killed = $this->ifthd->core->db->get_num_rows();

			return $num_killed;
		}
	}
}

?>