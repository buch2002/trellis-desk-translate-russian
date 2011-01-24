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
|    | Session Class :: Session Handler
#======================================================
*/

class session {

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

		$cookie_sid = $this->ifthd->get_cookie('hdsid');
		$cookie_mid = intval( $this->ifthd->get_cookie('hdmid') );
		$cookie_hash = $this->ifthd->get_cookie('hdphash');

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
												  	 					  'm' => array( 'id', 'name', 'email', 'login_key', 'mgroup', 'title', 'joined', 'ipadd', 'open_tickets', 'tickets', 'email_notify', 'email_html', 'email_new_ticket', 'email_ticket_reply', 'email_announce', 'email_staff_ticket_reply', 'email_staff_new_ticket', 'ban_ticket_center', 'ban_ticket_open', 'ban_ticket_escalate', 'ban_ticket_rate', 'ban_kb', 'ban_kb_comment', 'ban_kb_rate', 'time_zone', 'dst_active', 'lang', 'skin', 'use_rte', 'cpfields' ),
												  	 					  'g' => 'all',
												  	 					 ),
												  	 'from'		=> array( 's' => 'sessions' ),
												  	 'join'		=> array( array( 'from' => array( 'm' => 'members' ), 'where' => array( 's' => 's_mid', '=', 'm' => 'id' ) ), array( 'from' => array( 'g' => 'groups' ), 'where' => array( 'g' => 'g_id', '=', 'm' => 'mgroup' ) ) ),
								 				  	 'where'	=> array( array( 's' => 's_id' ), '=', $cookie_sid ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			if ( $this->ifthd->core->db->get_num_rows() == 1 )
			{
				$this->member = $this->ifthd->core->db->fetch_row();

				#=============================
				# Update Session
				#=============================

				$this->ifthd->core->db->construct( array(
													  	 'update'	=> 'sessions',
													  	 'set'		=> array( 's_location' => $this->ifthd->input['act'], 's_time'	=> time() ),
									 				  	 'where'	=> array( 's_id', '=', $cookie_sid ),
									 				  	 'limit'	=> array( 1 ),
									 		  	  ) 	);

				$this->ifthd->core->db->next_shutdown();
				$this->ifthd->core->db->execute();

				$this->ifthd->set_cookie( 'hdsid', $cookie_sid, time() + ( $this->ifthd->core->cache['config']['session_timeout'] * 60 ) );

				if ( $this->member['s_guest'] )
				{
					$this->member['id']	= 0;
					$this->member['name'] = 'Guest';
					$this->member['mgroup'] = 2;

					$this->member = array_merge( $this->member, $this->ifthd->core->cache['group'][2] );
				}

				$authorized = 1;
			}
		}

		#=============================
		# If We Have A Remember Cookie
		#=============================

		if ( $cookie_mid && $cookie_hash && ! $authorized )
		{
			#=============================
			# Load Member
			#=============================

			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'm' => array( 'id', 'name', 'email', 'login_key', 'mgroup', 'title', 'joined', 'ipadd', 'open_tickets', 'tickets', 'email_notify', 'email_html', 'email_new_ticket', 'email_ticket_reply', 'email_announce', 'email_staff_new_ticket', 'email_staff_ticket_reply', 'ban_ticket_center', 'ban_ticket_open', 'ban_ticket_escalate', 'ban_ticket_rate', 'ban_kb', 'ban_kb_comment', 'ban_kb_rate', 'time_zone', 'dst_active', 'lang', 'skin', 'use_rte', 'cpfields' ),
												  	 					  'g' => 'all',
												  	 					 ),
												  	 'from'		=> array( 'm' => 'members' ),
												  	 'join'		=> array( array( 'from' => array( 'g' => 'groups' ), 'where' => array( 'g' => 'g_id', '=', 'm' => 'mgroup' ) ) ),
								 				  	 'where'	=> array( array( 'm' => 'id' ), '=', $cookie_mid ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$this->member = $this->ifthd->core->db->fetch_row();

			#=============================
			# Checkie Checkie
			#=============================

			if ( $this->member['login_key'] == $cookie_hash )
			{
				#=============================
				# Create Session
				#=============================

				$new_session = md5( 's' . time() . $this->member['id'] . uniqid( rand(), true ) );

				$db_array = array(
								  's_id'			=> $new_session,
								  's_mid'			=> $this->member['id'],
								  's_mname'			=> $this->member['name'],
								  's_ipadd'			=> $this->ifthd->input['ip_address'],
								  's_location'		=> $this->ifthd->input['act'],
								  's_time'			=> time(),
								  );

				$this->ifthd->core->db->construct( array(
													  	 'insert'	=> 'sessions',
													  	 'set'		=> $db_array,
									 		  	  ) 	);

				$this->ifthd->core->db->execute();

				$this->ifthd->set_cookie( 'hdsid', $new_session, time() + ( $this->ifthd->core->cache['config']['session_timeout'] * 60 ) );

				$authorized = 1;
			}
			else
			{
				$this->ifthd->delete_cookie('hdmid');
				$this->ifthd->delete_cookie('hdphash');
			}
		}

		#=============================
		# If We Are Not Authorized
		#=============================

		if ( ! $authorized )
		{
			$this->member['id']	= 0;
			$this->member['name'] = 'Guest';
			$this->member['mgroup'] = 2;

			$this->member['guest'] = 1;

			#=============================
			# Create Session
			#=============================

			$new_session = md5( 's' . time() . $this->member['id'] . uniqid( rand(), true ) );

			$db_array = array(
							  's_id'			=> $new_session,
							  's_mid'			=> $this->member['id'],
							  's_mname'			=> $this->member['name'],
							  's_ipadd'			=> $this->ifthd->input['ip_address'],
							  's_location'		=> $this->ifthd->input['act'],
							  's_time'			=> time(),
							  's_guest'			=> 1,
							  );

			$this->ifthd->core->db->construct( array(
												  	 'insert'	=> 'sessions',
												  	 'set'		=> $db_array,
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$this->ifthd->set_cookie( 'hdsid', $new_session, time() + ( $this->ifthd->core->cache['config']['session_timeout'] * 60 ) );

			$this->member['s_id'] = $new_session;

			$this->member = array_merge( $this->member, $this->ifthd->core->cache['group'][2] );
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

		$this->ifthd->check_token('login');

		if ( ! $this->ifthd->input['username'] || ! $this->ifthd->input['password'] )
		{
			$this->ifthd->skin->error('fill_form_completely', 1);
		}

		#=============================
		# Select Member
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'name', 'email', 'password', 'pass_salt', 'login_key', 'email_val', 'admin_val' ),
											  	 'from'		=> 'members',
							 				  	 'where'	=> array( 'name|lower', '=', strtolower( $this->ifthd->input['username'] ) ),
							 				  	 'limit'	=> array( 0, 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('login_no_user', 1);
		}

		$mem = $this->ifthd->core->db->fetch_row();

		#=============================
		# Compare Password
		#=============================

		if ( sha1( md5( $this->ifthd->input['password'] . $mem['pass_salt'] ) ) == $mem['password'] )
		{			
			#=============================
			# Validation Check
			#=============================
			
			if ( ! $mem['email_val'] )
			{
				$this->ifthd->skin->error('login_must_val');
			}
			if ( ! $mem['admin_val'] )
			{
				$this->ifthd->skin->error('login_must_val_admin');
			}
			
			#=============================
			# Delete Old Sessoin
			#=============================

			if ( $this->member['s_id'] )
			{
				$this->ifthd->core->db->construct( array(
													  	 'delete'	=> 'sessions',
									 				  	 'where'	=> array( 's_id', '=', $this->member['s_id'] ),
									 				  	 'limit'	=> array( 1 ),
									 		  	  ) 	);

				$this->ifthd->core->db->execute();
			}

			#=============================
			# Create Session
			#=============================

			$new_session = md5( time() . $mem['id'] . uniqid( rand(), true ) );

			$db_array = array(
							  's_id'			=> $new_session,
							  's_mid'			=> $mem['id'],
							  's_mname'			=> $mem['name'],
							  's_email'			=> $mem['email'],
							  's_ipadd'			=> $this->ifthd->input['ip_address'],
							  's_location'		=> $this->ifthd->input['act'],
							  's_time'			=> time(),
							  );

			$this->ifthd->core->db->construct( array(
												  	 'insert'	=> 'sessions',
												  	 'set'		=> $db_array,
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$this->ifthd->set_cookie( 'hdsid', $new_session, time() + ( $this->ifthd->core->cache['config']['session_timeout'] * 60 ) );

			#=============================
			# Remember Me?
			#=============================

			if ( $this->ifthd->input['remember'] )
			{
				$this->ifthd->set_cookie( 'hdmid', $mem['id'] );
				$this->ifthd->set_cookie( 'hdphash', $mem['login_key'] );
			}

			#=============================
			# Redirect
			#=============================

			if ( $this->ifthd->input['extra_l'] )
			{
				$this->ifthd->skin->redirect( '?'. str_replace( "&amp;", "&", $this->ifthd->input['extra_l'] ), 'login_success' );
			}
			else
			{
				$this->ifthd->skin->redirect( '?act=portal', 'login_success' );
			}
		}
		else
		{
			$this->ifthd->skin->error('login_no_pass', 1);
		}
	}

	#=======================================
	# @ Do Guest Login
	# Attempt to login a guest.
	#=======================================

	function do_guest_login($onthefly=0)
	{
		#=============================
		# Security Checks
		#=============================

		if ( $onthefly )
		{
			$this->ifthd->input['email_address'] = $this->ifthd->input['email'];
			$this->ifthd->input['ticket_key'] = $this->ifthd->input['key'];
		}
		else
		{
			$this->ifthd->check_token('glogin');
		}

		if ( ! $this->ifthd->validate_email( $this->ifthd->input['email_address'] ) )
		{
			$this->ifthd->skin->error('no_valid_email');
		}

		if ( strlen( $this->ifthd->input['ticket_key'] ) != 11 )
		{
			$this->ifthd->skin->error('no_valid_tkey');
		}

		#=============================
		# Select Ticket
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'mname', 'email' ),
											  	 'from'		=> 'tickets',
							 				  	 'where'	=> array( array( 'tkey', '=', $this->ifthd->input['ticket_key'] ), array( 'email', '=', $this->ifthd->input['email_address'], 'and' ), array( 'guest', '=', 1, 'and' ) ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( $this->ifthd->core->db->get_num_rows() != 1 )
		{
			$this->ifthd->skin->error('no_ticket_guest');
		}

		$ticket = $this->ifthd->core->db->fetch_row();

		#=============================
		# Update Session
		#=============================

		$new_session = md5( time() . $mem['id'] . uniqid( rand(), true ) );

		$db_array = array( 's_mname' => $ticket['mname'], 's_email' => $ticket['email'], 's_tkey' => $this->ifthd->input['ticket_key'] );

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'sessions',
											  	 'set'		=> $db_array,
											  	 'where'	=> array( 's_id', '=', $this->member['s_id'] ),
											  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->member = array_merge( $this->ifthd->member, $db_array );

		if ( ! $onthefly ) $this->ifthd->skin->redirect( '?act=tickets&code=view&id='. $ticket['id'], 'login_success' );
	}

	#=======================================
	# @ Do Logout
	# Attempt to logout.
	#=======================================

	function do_logout()
	{
		if ( $this->ifthd->member['id'] )
		{
			#=============================
			# Security Checks
			#=============================

			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'id' ),
												  	 'from'		=> 'members',
								 				  	 'where'	=> array( 'login_key', '=', $this->ifthd->input['key'] ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			if ( ! $this->ifthd->core->db->get_num_rows() )
			{
				$this->ifthd->skin->error('logout_no_key');
			}

			$lk = $this->ifthd->core->db->fetch_row();

			if ( $this->ifthd->member['id'] != $lk['id'] )
			{
				$this->ifthd->skin->error('logout_no_key');
			}
		}

		#=============================
		# Delete Cookies
		#=============================

		$this->ifthd->delete_cookie('hdsid');
		$this->ifthd->delete_cookie('hdmid');
		$this->ifthd->delete_cookie('hdphash');

		#=============================
		# Delete Session
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'delete'	=> 'sessions',
							 				  	 'where'	=> array( 's_id', '=', $this->member['s_id'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		#=============================
		# Redirect
		#=============================

		$this->ifthd->skin->redirect( '?act=portal', 'logout_success' );
	}

	#=======================================
	# @ Kill Old Sessions
	# Kills sessions older than the session
	# timeout (defined in ACP).
	#=======================================

	function kill_old_sessions()
	{
		$timeout = time() - ( $this->ifthd->core->cache['config']['session_timeout'] * 60 );

		$this->ifthd->core->db->construct( array(
											  	 'delete'	=> 'sessions',
							 				  	 'where'	=> array( 's_time', '<=', $timeout ),
							 		  	  ) 	);

		$this->ifthd->core->db->next_shutdown();
		$this->ifthd->core->db->execute();

		$num_killed = $this->ifthd->core->db->get_num_rows();

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