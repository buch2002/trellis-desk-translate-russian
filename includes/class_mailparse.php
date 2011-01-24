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
|    | Mail Parse Class
#======================================================
*/

class mailparse {

	#=======================================
	# @ Parse Email
	#=======================================
	
	function decode($raw_email)
	{
		require_once('Mail/mimeDecode.php');

		$params['include_bodies'] = true;
		$params['decode_bodies'] = true;
		$params['decode_headers'] = true;
		$params['input'] = $raw_email;
		
		$decoded = Mail_mimeDecode::decode($params);
		
		// Find Our Content
		$email_parse = array();
		$attachment = 0;
		$charset = 'utf-8';
		
		if ( strtolower( $decoded->ctype_primary ) == 'multipart' )
		{
			foreach( $decoded->parts as $part )
			{
				if ( ( $part->disposition == 'attachment' || $part->ctype_primary == 'image' ) && ! $attachment )
				{
					if ( $part->ctype_parameters['filename'] )
					{
						$email['attachment_name'] = $this->ifthd->sanitize_data( $part->ctype_parameters['filename'] );
					}
					elseif ( $part->ctype_parameters['name'] )
					{
						$email['attachment_name'] = $this->ifthd->sanitize_data( $part->ctype_parameters['name'] );
					}
					elseif ( $part->ctype_parameters['d_parameters']['name'] )
					{
						$email['attachment_name'] = $this->ifthd->sanitize_data( $part->ctype_parameters['d_parameters']['name'] );
					}
					
					$email['attachment_type'] = $part->ctype_primary .'/'. $part->ctype_secondary;
					$email['attachment_content'] = trim( $part->body );
					
					$attachment = 1;
				}
				elseif ( $part->ctype_primary == 'text' )
				{
					if ( $part->ctype_secondary == 'plain' )
					{
						if ( $part->ctype_parameters['charset'] )
						{
							$email_parse['message']['plain'] = iconv( $part->ctype_parameters['charset'], 'utf-8', $part->body );
						}
						else
						{
							$email_parse['message']['plain'] = $part->body;
						}
					}
					elseif ( $part->ctype_secondary == 'html' )
					{
						if ( $part->ctype_parameters['charset'] )
						{
							$email_parse['message']['html'] = iconv( $part->ctype_parameters['charset'], 'utf-8', $part->body );
						}
						else
						{
							$email_parse['message']['html'] = $part->body;
						}
					}
					
					if ( $part->ctype_parameters['charset'] && strtolower( $part->ctype_parameters['charset'] ) != $charset )
					{
						$charset = strtolower( $part->ctype_parameters['charset'] );
					}
				}
				elseif ( strtolower( $part->ctype_primary ) == 'multipart' )
				{
					foreach( $part->parts as $apart )
					{
						if ( ( $apart->disposition == 'attachment' || $apart->ctype_primary == 'image' ) && ! $attachment )
						{
							if ( $apart->ctype_parameters['filename'] )
							{
								$email['attachment_name'] = $this->ifthd->sanitize_data( $apart->ctype_parameters['filename'] );
							}
							elseif ( $apart->ctype_parameters['name'] )
							{
								$email['attachment_name'] = $this->ifthd->sanitize_data( $apart->ctype_parameters['name'] );
							}
							elseif ( $apart->ctype_parameters['d_parameters']['name'] )
							{
								$email['attachment_name'] = $this->ifthd->sanitize_data( $apart->ctype_parameters['d_parameters']['name'] );
							}
							
							$email['attachment_type'] = $apart->ctype_primary .'/'. $apart->ctype_secondary;
							$email['attachment_content'] = trim( $apart->body );
							
							$attachment = 1;
						}
						elseif ( $apart->ctype_primary == 'text' )
						{
							if ( $apart->ctype_secondary == 'plain' )
							{
								if ( strtolower( $apart->ctype_parameters['charset'] ) != 'utf-8' )
								{
									$email_parse['message']['plain'] = iconv( $part->ctype_parameters['charset'], 'utf-8', $apart->body );
								}
								else
								{
									$email_parse['message']['plain'] = $apart->body;
								}
							}
							elseif ( $apart->ctype_secondary == 'html' )
							{
								if ( strtolower( $apart->ctype_parameters['charset'] ) != 'utf-8' )
								{
									$email_parse['message']['html'] = iconv( $apart->ctype_parameters['charset'], 'utf-8', $apart->body );
								}
								else
								{
									$email_parse['message']['html'] = $apart->body;
								}
							}
					
							if ( $apart->ctype_parameters['charset'] && strtolower( $apart->ctype_parameters['charset'] ) != $charset )
							{
								$charset = strtolower( $apart->ctype_parameters['charset'] );
							}
						}
					}
				}
			}
		}
		else
		{
			if ( $decoded->ctype_primary == 'text' )
			{
				if ( $decoded->ctype_secondary == 'plain' )
				{
					if ( strtolower( $decoded->ctype_parameters['charset'] ) != 'utf-8' )
					{
						$email_parse['message']['plain'] = iconv( $decoded->ctype_parameters['charset'], 'utf-8', $decoded->body );
					}
					else
					{
						$email_parse['message']['plain'] = $decoded->body;
					}
				}
				elseif ( $decoded->ctype_secondary == 'html' )
				{
					if ( strtolower( $decoded->ctype_parameters['charset'] ) != 'utf-8' )
					{
						$email_parse['message']['html'] = iconv( $decoded->ctype_parameters['charset'], 'utf-8', $decoded->body );
					}
					else
					{
						$email_parse['message']['html'] = $decoded->body;
					}
				}
			}
		}
		
		// From
		if ( strpos( $decoded->headers['from'], '<' ) !== false )
		{
			if ( preg_match( "/(.*?)<(.*)>/", $decoded->headers['from'], $matches ) )
			{
				$email['nickname'] = $matches[1];
				$email['from'] = $matches[2];
		
				if ( preg_match( "/\"([^\"]*)\"/", $email['nickname'], $matches ) )
				{
					$email['nickname'] = $matches[1];
				}
			}
			else
			{
				$email['from'] = $decoded->headers['from'];
			}
		}
		elseif ( preg_match( "/([0-9,a-z,A-Z,+]+)([0-9,a-z,A-Z,.,_,-,+]+)[@]([0-9,a-z,A-Z]+)([0-9,a-z,A-Z,.,_,-]+)[.]([0-9,a-z,A-Z]{2})([0-9,a-z,A-Z]*)[\s](.+)/", $decoded->headers['from'], $matches ) )
		{
			$email['nickname'] = $matches[7];
			$email['from'] = $matches[1] . $matches[2] . '@' . $matches[3] . $matches[4] .'.' . $matches[5] . $matches[6];
		}
		else
		{
			$email['from'] = $decoded->headers['from'];
		}
		
		// To
		if ( strpos( $decoded->headers['to'], '<' ) !== false )
		{
			if ( preg_match( "/<(.*)>/", $decoded->headers['to'], $matches ) )
			{
				$email['to'] = $matches[1];
			}
		}
		elseif ( preg_match( "/([0-9,a-z,A-Z,+]+)([0-9,a-z,A-Z,.,_,-,+]+)[@]([0-9,a-z,A-Z]+)([0-9,a-z,A-Z,.,_,-]+)[.]([0-9,a-z,A-Z]{2})([0-9,a-z,A-Z]*)[\s](.+)/", $decoded->headers['to'], $matches ) )
		{
			$email['to'] = $matches[1] . $matches[2] . '@' . $matches[3] . $matches[4] .'.' . $matches[5] . $matches[6];
		}
		else
		{
			$email['to'] = $decoded->headers['to'];
		}
		
		// Finally, Sanitize
		$email['from'] = $this->ifthd->sanitize_data( $email['from'] );
		$email['nickname'] = $this->ifthd->sanitize_data( $email['nickname'] );
		$email['to'] = $this->ifthd->sanitize_data( $email['to'] );
		$email['date'] = strtotime( $decoded->headers['date'] );
		$email['subject'] = $this->ifthd->sanitize_data( iconv( $charset, 'utf-8', $decoded->headers['subject'] ) );
		$email['message']['plain'] = $this->ifthd->sanitize_data( $email_parse['message']['plain'] );
		$email['message']['html'] = $this->ifthd->sanitize_data( $email_parse['message']['html'] );
		
		return $email;
	}

	#=======================================
	# @ Process Email
	#=======================================
	
	function process($email)
	{
		if ( ! $email['nickname'] ) $email['nickname'] = $email['from'];
		
		if ( ! $email['from'] || ! $email['to'] || ! $email['subject'] )
		{
			$this->ifthd->log( 'error', "Email Missing Information" );
		
			return false;
		}
		
		if ( ! $this->ifthd->validate_email( $email['from'] ) )
		{
			$this->ifthd->log( 'error', "Email Invalid Address" );
		
			return false;
		}
		
		#=============================
		# Flood Check
		#=============================
		
		if ( $this->ifthd->core->cache['config']['email_flood'] )
		{
			$this->ifthd->core->db->construct( array(
											   'insert'	=> 'in_email_log',
											   'set'	=> array( 'email' => $email['from'], 'date' => time() ),
							 		  	)	   );
			
			$this->ifthd->core->db->execute();
			
			$time_cutoff = 60 * 3; // 3 Min
			
			$this->ifthd->core->db->construct( array(
										  	   'select'	=> array( 'id' ),
											   'from'	=> 'in_email_log',
							 			  	   'where'	=> array( array( 'email', '=', $email['from'] ), array( 'date', '>=', ( time() - $time_cutoff ), 'and' ) ),
							 			  	   'limit'	=> array( 0, 6 ),
							 		    ) 	  );
		
			$this->ifthd->core->db->execute();
			
			if ( $this->ifthd->core->db->get_num_rows() > 5 )
			{
				$this->ifthd->log( 'security', "Flood Limit Email: ". $email['from'] );
		
				return false;
			}
		}
		
		#=============================
		# Find Department
		#=============================
		
		$this->ifthd->core->db->construct( array(
									  	   'select'	=> 'all',
										   'from'	=> 'departments',
						 			  	   'where'	=> array( 'incoming_email', '=', $email['to'], 'and' ),
						 			  	   'limit'	=> array( 0, 1 ),
						 		    ) 	  );
		
		$this->ifthd->core->db->execute();
		
		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "Department Not Found Email: ". $email['to'] );
		
			return false;
		}
		
		$d = $this->ifthd->core->db->fetch_row();
		
		#=============================
		# Find Member
		#=============================
		
		$this->ifthd->core->db->construct( array(
									  	   'select'	=> array( 'm' => array( 'id', 'name', 'time_zone', 'dst_active', 'open_tickets' ), 'g' => array( 'g_upload_size_max', 'g_m_depart_perm', 'g_depart_perm', 'g_acp_access' ) ),
										   'from'	=> array( 'm' => 'members' ),
										   'join'	=> array( array( 'from' => array( 'g' => 'groups' ), 'where' => array( 'g' => 'g_id', '=', 'm' => 'mgroup' ) ) ),
						 			  	   'where'	=> array( array( 'm' => 'email' ), '=', $email['from'] ),
						 			  	   'limit'	=> array( 0, 1 ),
						 		    ) 	  );
		
		$this->ifthd->core->db->execute();
		
		if ( $this->ifthd->core->db->get_num_rows() )
		{
			$m = $this->ifthd->core->db->fetch_row();
		}
		else
		{
			if ( ! $d['guest_pipe'] )
			{
				$replace = array(); // Initialize for Security
		
				if ( $m['id'] )
				{
					$this->ifthd->send_email( $m['id'], 'ticket_pipe_rejected', $replace, array( 'from_email' => $d['incoming_email'] ) );
				}
				else
				{
					$replace['MEM_NAME'] = $email['nickname'];
		
					$this->ifthd->send_guest_email( $email['from'], 'ticket_pipe_rejected', $replace, array( 'from_email' => $d['incoming_email'] ) );
				}
		
				$this->ifthd->log( 'security', "Guest Piping Not Allowed: ". $d['name'] );
		
				return false;
			}
		
			$this->ifthd->core->db->construct( array(
										  	   'select'	=> array( 'g_upload_size_max', 'g_m_depart_perm' ),
											   'from'	=> 'groups',
							 			  	   'where'	=> array( 'g_id', '=', 2 ),
							 			  	   'limit'	=> array( 0, 1 ),
							 		    ) 	  );
		
			$this->ifthd->core->db->execute();
		
			$m = $this->ifthd->core->db->fetch_row();
		}
		
		#=============================
		# Detect Type
		#=============================
		
		$ticket_found = 0;
		
		if ( preg_match_all( $this->ifthd->convert_html( $this->ifthd->core->cache['config']['email_subject_regex'] ), $email['subject'], $matches, PREG_PATTERN_ORDER ) )
		{
			while( list( , $ptid ) = each( $matches[1] ) )
			{
				$this->ifthd->core->db->construct( array(
											  	   'select'	=> 'all',
												   'from'	=> 'tickets',
								 			  	   'where'	=> array( 'id', '=', intval( $ptid ) ),
								 			  	   'limit'	=> array( 0, 1 ),
								 		    ) 	  );
		
				$this->ifthd->core->db->execute();
		
				if ( $this->ifthd->core->db->get_num_rows() )
				{
					$ticket_found = 1;
		
					$t = $this->ifthd->core->db->fetch_row();
		
					break;
				}
			}
		}
		
		$message_html = 0;
			
		if ( $m['g_acp_access'] && $ticket_found )
		{
			if ( $email['message']['html'] )
			{
				if ( $this->ifthd->core->cache['config']['enable_ticket_rte'] )
				{
					$message = $email['message']['html'];
					
					$message_html = 1;
				}
				else
				{
					if ( $email['message']['plain'] )
					{
						$message = $email['message']['plain'];
					}
					else
					{
						$message = $this->ifthd->remove_html_s( $email['message']['html'] );
					}
				}
			}
			elseif ( $email['message']['plain'] )
			{
				$message = $email['message']['plain'];
			}
		}
		else
		{
			if ( $email['message']['plain'] )
			{
				$message = $email['message']['plain'];
			}
			elseif ( $email['message']['html'] )
			{
				$message = $this->ifthd->remove_html_s( $email['message']['html'] );
			}
		}
		
		// Get The Good Message
		if ( $this->ifthd->core->cache['config']['email_use_rline'] )
		{
			$final_message = "";
			$message_on = "";
			$found_on = 0;
			$on_line = 0;
			$end_line = 0;
		
			$msg_lines = split( "\n", $message );
		
			foreach( $msg_lines as $num => $mline )
			{
				if ( preg_match( '/^On[\s]/i', $mline ) )
				{
					$on_line = $num;
				
					$found_on = 1;
				}
			
				if ( strpos( $mline, $this->ifthd->core->cache['config']['email_reply_line'] ) !== false )
				{
					$end_line = $num;
				
					break;
				}
			
				if ( $found_on )
				{
					$message_on .= $mline ."\n";
				}
				else
				{
					$final_message .= $mline ."\n";
				}
			}
		
			if ( ( ( $end_line - $on_line ) > 2 ) || ! $end_line ) $final_message .= $message_on;
		
			$message = trim( $final_message );
			
			if ( $message_html ) $message = $this->closetags( $message );
		}
		
		if ( ! $message )
		{
			$this->ifthd->log( 'error', "Email Missing Message" );
		
			return false;
		}
		
		#=============================
		# Process
		#=============================
		
		if ( $ticket_found )
		{
			if ( $m['g_acp_access'] )
			{
				if ( $t['mid'] )
				{
					$this->ifthd->core->db->construct( array(
														  	 'select'	=> array( 'email_notify', 'email_ticket_reply', 'time_zone', 'dst_active', 'open_tickets' ),
														  	 'from'		=> 'members',
										 				  	 'where'	=> array( 'id', '=', $t['mid'] ),
										 		  	  ) 	);
		
					$this->ifthd->core->db->execute();
	
					$mem = $this->ifthd->core->db->fetch_row();
				}
				
				if ( $perms = is_array( unserialize( $m['g_depart_perm'] ) ) )
				{
					if ( ! $perms[ $t['did'] ] )
					{
						$this->ifthd->log( 'security', "Email Staff Reply Permission Denied: ". $t['subject'] );
		
						return false;
					}
				}
			}
			else
			{
				if ( $t['mid'] )
				{
					if ( $m['id'] != $t['mid'] )
					{
						$this->ifthd->log( 'security', "Email Member Reply Permission Denied: ". $t['subject'] );
		
						return false;
					}
				}
				else
				{
					if ( $email['from'] != $t['email'] )
					{
						$this->ifthd->log( 'security', "Email Guest Reply Permission Denied: ". $t['subject'] );
		
						return false;
					}
				}
			}
			
			if ( $t['status'] == 6 )
			{
				if ( $m['g_acp_access'] || $d['ticket_own_reopen'] )
				{
					$this->ifthd->core->db->construct( array(
														  	 'update'	=> 'tickets',
														  	 'set'		=> array( 'close_reason' => "", 'status' => 1 ),
										 				  	 'where'	=> array( 'id', '=', $t['id'] ),
										 		  	  ) 	);
		
					$this->ifthd->core->db->execute();
					
					$this->ifthd->log( 'ticket', "Ticket Reopened &#039;". $t['subject'] ."&#039;", 1, $t['id'] );
					
					if ( $m['g_acp_access'] )
					{
						if ( $t['mid'] )
						{
							$this->ifthd->core->db->construct( array(
																  	 'update'	=> 'members',
																  	 'set'		=> array( 'open_tickets' => $mem['open_tickets'] + 1 ),
												 				  	 'where'	=> array( 'id', '=', $t['mid'] ),
												 		  	  ) 	);
			
							$this->ifthd->core->db->next_shutdown();
							$this->ifthd->core->db->execute();
						}
					}
					else
					{
						$this->ifthd->core->db->construct( array(
															  	 'update'	=> 'members',
															  	 'set'		=> array( 'open_tickets' => $m['open_tickets'] + 1 ),
											 				  	 'where'	=> array( 'id', '=', $t['mid'] ),
											 		  	  ) 	);
		
						$this->ifthd->core->db->next_shutdown();
						$this->ifthd->core->db->execute();
					}
				}
				else
				{
					$replace['TICKET_ID'] = $t['id'];
					$replace['SUBJECT'] = $t['subject'];
					
					if ( $m['id'] )
					{
						$this->ifthd->send_email( $m['id'], 'reply_pipe_closed', $replace, array( 'from_email' => $d['incoming_email'] ) );
					}
					else
					{
						$replace['MEM_NAME'] = $email['nickname'];
			
						$this->ifthd->send_guest_email( $email['from'], 'reply_pipe_closed', $replace, array( 'from_email' => $d['incoming_email'] ) );
					}
					
					$this->ifthd->log( 'error', "Reply Rejected Ticket Closed &#039;". $t['subject'] ."&#039;", 1, $t['id'] );
			
					return false;
				}
			}
			
			#=============================
			# Attachment
			#=============================
			
			if ( $d['can_attach'] && $email['attachment_name'] )
			{
				$allowed_exts = explode( "|", $this->ifthd->core->cache['config']['upload_exts'] );
				$file_ext = strrchr( $email['attachment_name'], "." );
		
				if ( in_array( $file_ext, $allowed_exts ) )
				{
					if ( ( strlen( $email['attachment_content'] ) / 1024 ) <= $m['g_upload_size_max'] || ! $m['g_upload_size_max'] )
					{
						$file_safe_name = $this->sanitize_name( $email['attachment_name'] );
		
						$attachment_name = md5( 'a'. uniqid( rand(), true ) ) . $file_ext;
		
						$attachment_loc = $this->ifthd->core->cache['config']['upload_dir'] .'/'. $attachment_name;
		
						if ( @ $fp = fopen( $attachment_loc, 'a' ) )
						{
							if ( @ fwrite( $fp, $email['attachment_content'] ) )
							{
								@ fclose( $fp );
		
								$db_array = array(
												  'tid'				=> $t['id'],
												  'real_name'		=> $attachment_name,
												  'original_name'	=> $file_safe_name,
												  'mid'				=> $m['id'],
												  'mname'			=> $m['name'],
												  'size'			=> strlen( $email['attachment_content'] ),
												  'mime'			=> $email['attachment_type'],
												  'date'			=> time(),
												 );
		
								$this->ifthd->core->db->construct( array(
																   'insert'	=> 'attachments',
																   'set'	=> $db_array,
															)	   );
		
								$this->ifthd->core->db->execute();
		
								$attachment_id = $this->ifthd->core->db->get_insert_id();
		
								$this->ifthd->log( 'ticket', "Uploaded Attachment #". $attachment_id, 1, $t['id'] );
								$this->ifthd->log( 'member', "Uploaded Attachment #". $attachment_id, 1, $attachment_id );
							}
						}
					}
				}
			}
		
			#=============================
			# Add Reply
			#=============================
		
			$db_array = array(
							  'tid'			=> $t['id'],
							  'mid'			=> $m['id'],
							  'mname'		=> $m['name'],
							  'attach_id'	=> $attachment_id,
							  'message'		=> $message,
							  'date'		=> time(),
							  'ipadd'		=> $this->ifthd->input['ip_address'],
							 );
		
			if ( ! $m['id'] )
			{
				$db_array['mname'] = $email['nickname'];
				$db_array['guest'] = 1;
			}
			elseif ( $m['g_acp_access'] )
			{
				$db_array['staff'] = 1;
				
				if ( $message_html ) $db_array['rte'] = 1;
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
			# Email
			#=============================
			
			if ( $m['g_acp_access'] )
			{
				if ( ( $mem['email_ticket_reply'] && $mem['email_notify'] ) || ( $this->ifthd->core->cache['config']['guest_ticket_emails'] && $t['guest_email'] ) )
				{
					$mem_offset = ( $mem['time_zone'] * 60 * 60 ) + ( $mem['dst_active'] * 60 * 60 );

					$replace = ""; // Initialize for Security
	
					$replace['TICKET_ID'] = $t['id'];
					$replace['SUBJECT'] = $t['subject'];
					$replace['DEPARTMENT'] = $t['dname'];
					$replace['PRIORITY'] = $this->ifthd->get_priority( $t['priority'] );
					$replace['SUB_DATE'] = $this->ifthd->ift_date( $t['date'], '', '', 0, 1, $mem_offset, 1 );
					$replace['REPLY'] = $message;
					$replace['MESSAGE'] = $t['message'];
	
					if ( $mem['email_ticket_reply'] )
					{
						$replace['TICKET_LINK'] = $this->ifthd->core->cache['config']['hd_url'] ."/index.php?act=tickets&code=view&id=". $t['id'];
						
						$this->ifthd->send_email( $t['mid'], 'ticket_reply', $replace, array( 'from_email' => $this->ifthd->core->cache['depart'][ $t['did'] ]['incoming_email'] ), 1 );
					}
					elseif ( ! $t['mid'] && $this->ifthd->core->cache['config']['guest_ticket_emails'] && $t['guest_email'] )
					{
						$replace['MEM_NAME'] = $t['mname'];
						$replace['TICKET_KEY'] = $t['tkey'];
						
						$replace['TICKET_LINK'] = $this->ifthd->core->cache['config']['hd_url'] ."/index.php?act=tickets&code=view&id=". $t['id'] ."&email=". urlencode( $t['email'] ) ."&key=". $t['tkey'];
	
						$this->ifthd->send_guest_email( $t['email'], 'ticket_reply_guest', $replace, array( 'from_email' => $this->ifthd->core->cache['depart'][ $t['did'] ]['incoming_email'] ), 1 );
					}
				}
			}
			else
			{
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
			
							if ( $my_departs[ $d['id'] ] )
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
							$replace['REPLY'] = $message;
							$replace['TICKET_LINK'] = $this->ifthd->core->cache['config']['hd_url'] ."/admin.php?section=manage&act=tickets&code=view&id=". $t['id'];
							$replace['MESSAGE'] = $t['message'];
			
							if ( $m['id'] )
							{
								$replace['MEMBER'] = $m['name'];
							}
							else
							{
								$replace['MEMBER'] = $email['nickname'];
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
			}
		
			#=============================
			# Update Ticket
			#=============================
			
			$db_array = array( 'last_reply' => time(), 'last_mid' => $m['id'], 'replies' => ( $t['replies'] + 1 ) );
			
			if ( $m['g_acp_access'] )
			{
				$db_array['last_reply_staff'] = time();
				
				$db_array['last_mname'] = $m['name'];
				
				if ( $t['status'] != 5 )
				{
					$db_array['status'] = 4;
				}
			}
			else
			{
				if ( $m['id'] )
				{
					$db_array['last_mname'] = $m['name'];
				}
				else
				{
					$db_array['last_mname'] = $email['nickname'];
				}
				
				if ( $t['status'] == 4 )
				{
					$db_array['status'] = 1;
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
		}
		else
		{
			#=============================
			# Department Security
			#=============================
		
			$d_allow = unserialize( $m['g_m_depart_perm'] );
		
			if ( ! $d_allow[ $d['id'] ] )
			{
				$replace = array(); // Initialize for Security
		
				if ( $m['id'] )
				{
					$this->ifthd->send_email( $m['id'], 'ticket_pipe_rejected', $replace, array( 'from_email' => $d['incoming_email'] ) );
				}
				else
				{
					$replace['MEM_NAME'] = $email['nickname'];
		
					$this->ifthd->send_guest_email( $email['from'], 'ticket_pipe_rejected', $replace, array( 'from_email' => $d['incoming_email'] ) );
				}
		
				$this->ifthd->log( 'security', "New Ticket to &#039;". $d['name'] ."&#039; Permission Denied", 1, $d['id'] );
		
				return false;
			}
			
			#=============================
			# Attachment
			#=============================
			
			if ( $d['can_attach'] && $email['attachment_name'] )
			{
				$allowed_exts = explode( "|", $this->ifthd->core->cache['config']['upload_exts'] );
				$file_ext = strrchr( $email['attachment_name'], "." );
		
				if ( in_array( $file_ext, $allowed_exts ) )
				{
					if ( ( strlen( $email['attachment_content'] ) / 1024 ) <= $m['g_upload_size_max'] || ! $m['g_upload_size_max'] )
					{
						$file_safe_name = $this->sanitize_name( $email['attachment_name'] );
		
						$attachment_name = md5( 'a'. uniqid( rand(), true ) ) . $file_ext;
		
						$attachment_loc = $this->ifthd->core->cache['config']['upload_dir'] .'/'. $attachment_name;
		
						if ( @ $fp = fopen( $attachment_loc, 'a' ) )
						{
							if ( @ fwrite( $fp, $email['attachment_content'] ) )
							{
								@ fclose( $fp );
		
								$db_array = array(
												  'tid'				=> 0,
												  'real_name'		=> $attachment_name,
												  'original_name'	=> $file_safe_name,
												  'mid'				=> $m['id'],
												  'mname'			=> $m['name'],
												  'size'			=> strlen( $email['attachment_content'] ),
												  'mime'			=> $email['attachment_type'],
												  'date'			=> time(),
												 );
		
								$this->ifthd->core->db->construct( array(
																   'insert'	=> 'attachments',
																   'set'	=> $db_array,
															)	   );
		
								$this->ifthd->core->db->execute();
		
								$attachment_id = $this->ifthd->core->db->get_insert_id();
		
								$this->ifthd->log( 'member', "Uploaded Attachment #". $attachment_id, 1, $attachment_id );
							}
						}
					}
				}
			}

			#=============================
			# Create Ticket
			#=============================
		
			$db_array = array(
							  'did'			=> $d['id'],
							  'dname'		=> $d['name'],
							  'mid'			=> $m['id'],
							  'mname'		=> $m['name'],
							  'email'		=> $email['from'],
							  'subject'		=> $email['subject'],
							  'priority'	=> 2,
							  'message'		=> $message,
							  'date'		=> time(),
							  'last_reply'	=> time(),
							  'last_mid'	=> $m['id'],
							  'last_mname'	=> $m['name'],
							  'ipadd'		=> $this->ifthd->input['ip_address'],
							  'status'		=> 1,
							  'attach_id'	=> $attachment_id,
							 );
		
			if ( ! $m['id'] )
			{
				$db_array['tkey'] = substr( md5( 'tk' . uniqid( rand(), true ) . time() ), 0, 11 );
				$db_array['mname'] = $email['nickname'];
				$db_array['last_mname'] = $email['nickname'];
				$db_array['guest'] = 1;
				$db_array['guest_email'] = 1;
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
		
			$this->ifthd->log( 'member', "Ticket Created &#039;". $email['subject'] ."&#039;", 1, $ticket_id );
			$this->ifthd->log( 'ticket', "Ticket Created &#039;". $email['subject'] ."&#039;", 1, $ticket_id );
			
			#=============================
			# Update Attachment
			#=============================
		
			if ( $attachment_id )
			{
				$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'attachments',
											  	 'set'		=> array( 'tid' => $ticket_id ),
							 				  	 'where'	=> array( 'id', '=', $attachment_id ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);
		
				$this->ifthd->core->db->next_shutdown();
				$this->ifthd->core->db->execute();
		
				$this->ifthd->log( 'ticket', "Uploaded Attachment #". $attachment_id, 1, $ticket_id );
			}
		
			#=============================
			# Update Member
			#=============================
		
			if ( $m['id'] )
			{
				$this->ifthd->core->db->next_no_quotes('set');
		
				$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'members',
												  	 'set'		=> array( 'open_tickets' => 'open_tickets+1', 'tickets' => 'tickets+1' ),
								 				  	 'where'	=> array( 'id', '=', $m['id'] ),
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
		
			if ( $m['id'] )
			{
				$mem_offset = ( $m['time_zone'] * 60 * 60 ) + ( $m['dst_active'] * 60 * 60 );
				
				$replace = array(); // Initialize for Security
		
				$replace['TICKET_ID'] = $ticket_id;
				$replace['SUBJECT'] = $email['subject'];
				$replace['DEPARTMENT'] = $d['name'];
				$replace['PRIORITY'] = $this->ifthd->get_priority( 2 );
				$replace['SUB_DATE'] = $this->ifthd->ift_date( time(), '', 0, 0, 1, $mem_offset, 1 );
				$replace['TICKET_LINK'] = $this->ifthd->core->cache['config']['hd_url'] ."/index.php?act=tickets&code=view&id=". $ticket_id;
				$replace['MESSAGE'] = $message;
		
				$this->ifthd->send_email( $m['id'], 'new_ticket', $replace, array( 'from_email' => $d['incoming_email'] ), 1 );
			}
		
			#=============================
			# Send Guest Email
			#=============================
		
			if ( ! $m['id'] && $this->ifthd->core->cache['config']['guest_ticket_emails'] )
			{
				$replace = array(); // Initialize for Security
		
				$replace['TICKET_ID'] = $ticket_id;
				$replace['SUBJECT'] = $email['subject'];
				$replace['DEPARTMENT'] = $d['name'];
				$replace['PRIORITY'] = $this->ifthd->get_priority( 2 );
				$replace['SUB_DATE'] = $this->ifthd->ift_date( time() );
				$replace['TICKET_LINK'] = $this->ifthd->core->cache['config']['hd_url'] ."/index.php?act=tickets&code=view&id=". $ticket_id ."&email=". urlencode( $db_array['email'] ) ."&key=". $db_array['tkey'];
				$replace['MEM_NAME'] = $email['nickname'];
				$replace['TICKET_KEY'] = $db_array['tkey'];
				$replace['MESSAGE'] = $message;
		
				$this->ifthd->send_guest_email( $email['from'], 'new_guest_ticket', $replace, array( 'from_email' => $d['incoming_email'] ), 1 );
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
						$replace['SUBJECT'] = $email['subject'];
						$replace['DEPARTMENT'] = $d['name'];
						$replace['PRIORITY'] = $this->ifthd->get_priority( 2 );
						$replace['SUB_DATE'] = $this->ifthd->ift_date( time(), '', 0, 0, 1, $mem_offset, 1 );
						$replace['MESSAGE'] = $message;
						$replace['TICKET_LINK'] = $this->ifthd->core->cache['config']['hd_url'] ."/admin.php?section=manage&act=tickets&code=view&id=". $ticket_id;
		
						if ( $m['id'] )
						{
							$replace['MEMBER'] = $m['name'];
		
							$this->ifthd->send_email( $sm['id'], 'staff_new_ticket', $replace, array( 'from_email' => $d['incoming_email'] ), 1 );
						}
						else
						{
							$replace['MEMBER'] = $email['nickname'];
		
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
		}

		#=============================
		# Update Stats
		#=============================
		
		$this->ifthd->r_ticket_stats(1);
	}

	#=============================
	# Sanitize Name
	#=============================
	
	function sanitize_name($name)
	{
		$name = str_replace( " ", "_", $name );
	
		return ereg_replace( "[^A-Za-z0-9_\.]", "", $name );
	}

	#=============================
	# Close Tags
	#=============================
	
	function closetags($html)
	{
		$html = $this->ifthd->convert_html( $html );
    	
	    # Strip any mangled tags off the end
    	$html=preg_replace("#]*$#", " ", $html);
    	
	    #put all opened tags into an array
	    preg_match_all ( "#<([a-z]+)( .*)?(?!/)>#iU", $html, $result );
	    $openedtags = $result[1];
	    $openedtags = array_diff($openedtags, array("img", "hr", "br"));
	    $openedtags = array_values($openedtags);
	
	    #put all closed tags into an array
	    preg_match_all ( "#</([a-z]+)>#iU", $html, $result );
	    $closedtags = $result[1];
	    $len_opened = count ( $openedtags );
	    
	    # all tags are closed
	    if( count ( $closedtags ) == $len_opened )
	    {
	        return $html;
	    }
	    $openedtags = array_reverse ( $openedtags );
	    
	    # close tags
	    for( $i = 0; $i < $len_opened; $i++ )
	    {
	        if ( !in_array ( $openedtags[$i], $closedtags ) )
	        {
	            $html .= "</" . $openedtags[$i] . ">";
	        }
	        else
	        {
	            unset ( $closedtags[array_search ( $openedtags[$i], $closedtags)] );
	        }
	    }
	    
	    return $this->ifthd->sanitize_data( $html );
	}
	
}

?>