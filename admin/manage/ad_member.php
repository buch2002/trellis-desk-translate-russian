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
|    | Admin Members
#======================================================
*/

class ad_member {

	#=======================================
	# @ Auto Run
	# Function that is run automatically
	# when the file is required.
	#=======================================

	function auto_run()
	{
		if ( ! $this->ifthd->member['acp']['manage_member'] )
		{
			$this->ifthd->skin->error('no_perm');
		}
		
		$this->ifthd->skin->set_section( 'Member Control' );		
		$this->ifthd->skin->set_description( 'Manage your members, groups, custom profile fields and members awaiting validation.' );

		switch( $this->ifthd->input['code'] )
	    {
	    	case 'list':
				$this->list_members();
	    	break;
	    	case 'mod':
				$this->list_pending();
	    	break;
	    	case 'view':
	    		$this->view_member();
	    	break;
	    	case 'add':
	    		$this->add_member();
	    	break;
	    	case 'edit':
	    		$this->edit_member();
	    	break;
	    	case 'sig':
	    		$this->edit_signature();
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
    		case 'dosig':
    			$this->do_signature();
    		break;
    		case 'approve':
    			$this->do_approve();
    		break;
    		case 'search':
    			$this->list_members();
    		break;

    		default:
    			$this->list_members();
    		break;
		}
	}

	#=======================================
	# @ List Members
	# Show a list of members.
	#=======================================

	function list_members($error='', $alert='')
	{
		#=============================
		# Sorting Options
		#=============================

		$link_extra = ""; // Initialize for Security

		if ( $this->ifthd->input['group'] )
		{
			$link_extra = '&amp;group='. $this->ifthd->input['group'];
		}

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

		if ( $this->ifthd->input['m_search'] )
		{
			$s_extra .= "&amp;m_search=". $this->ifthd->input['m_search'];
		}

		$link_id = "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=list&amp;sort=id". $order_id . $s_extra ."'>ID". $img_id ."</a>";
		$link_name = "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=list&amp;sort=name". $order_name . $s_extra ."'>Name". $img_name ."</a>";
		$link_mgroup = "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=list&amp;sort=mgroup". $order_mgroup . $s_extra ."'>Group". $img_mgroup ."</a>";
		$link_open_tickets = "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=list&amp;sort=open_tickets". $order_open_tickets . $s_extra ."'>Open Tickets". $img_open_tickets ."</a>";
		$link_tickets = "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=list&amp;sort=tickets". $order_tickets . $s_extra ."'>Tickets". $img_tickets ."</a>";

		if ( $this->ifthd->input['sort'] )
		{
			$link_extra .= "&amp;sort=". $this->ifthd->input['sort'];
		}
		if ( $this->ifthd->input['order'] )
		{
			$link_extra .= "&amp;order=". $this->ifthd->input['order'];
		}
		if ( $this->ifthd->input['m_search'] )
		{
			$link_extra .= "&amp;m_search=". $this->ifthd->input['m_search'];
		}

		#=============================
		# Grab Members
		#=============================

		if ( $this->ifthd->input['st'] )
		{
			$start = $this->ifthd->input['st'];
		}
		else
		{
			$start = 0;
		}

		// Search?
		if ( $this->ifthd->input['m_search'] )
		{
			// Filter?
			if ( $this->ifthd->input['group'] )
			{
				$this->ifthd->core->db->query( "SELECT * FROM ". DB_PRE ."members WHERE mgroup = '". $this->ifthd->input['group'] ."' && name LIKE '%". $this->ifthd->input['m_search'] ."%' ORDER BY ". $sort ." ". $order );
			}
			else
			{
				$this->ifthd->core->db->query( "SELECT * FROM ". DB_PRE ."members WHERE name LIKE '%". $this->ifthd->input['m_search'] ."%' ORDER BY ". $sort ." ". $order );
			}
		}
		else
		{
			// Filter?
			if ( $this->ifthd->input['group'] )
			{
				$this->ifthd->core->db->construct( array(
													  	 'select'	=> 'all',
													  	 'from'		=> 'members',
													  	 'where'	=> array( 'mgroup', '=', $this->ifthd->input['group'] ),
								 				  	 	 'order'	=> array( $sort => $order ),
									 		  	  ) 	);
			}
			else
			{
				$this->ifthd->core->db->construct( array(
													  	 'select'	=> 'all',
													  	 'from'		=> 'members',
								 				  	 	 'order'	=> array( $sort => $order ),
									 		  	  ) 	);
			}

			$this->ifthd->core->db->execute();
		}

		$member_rows = ""; // Initialize for Security
		$row_count = 0; // Initialize for Security

		if ( ! $mem_count = $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_members_found');
		}

		// Search?
		if ( $this->ifthd->input['m_search'] )
		{
			// Filter?
			if ( $this->ifthd->input['group'] )
			{
				$this->ifthd->core->db->query( "SELECT * FROM ". DB_PRE ."members WHERE mgroup = '". $this->ifthd->input['group'] ."' && name LIKE '%". $this->ifthd->input['m_search'] ."%' ORDER BY ". $sort ." ". $order ." LIMIT ". $start .", 20" );
			}
			else
			{
				$this->ifthd->core->db->query( "SELECT * FROM ". DB_PRE ."members WHERE name LIKE '%". $this->ifthd->input['m_search'] ."%' ORDER BY ". $sort ." ". $order ." LIMIT ". $start .", 20" );
			}
		}
		else
		{
			// Filter?
			if ( $this->ifthd->input['group'] )
			{
				$this->ifthd->core->db->construct( array(
													  	 'select'	=> 'all',
													  	 'from'		=> 'members',
													  	 'where'	=> array( 'mgroup', '=', $this->ifthd->input['group'] ),
								 				  	 	 'order'	=> array( $sort => $order ),
								 				  	 	 'limit'	=> array( $start, 20 ),
									 		  	  ) 	);
			}
			else
			{
				$this->ifthd->core->db->construct( array(
													  	 'select'	=> 'all',
													  	 'from'		=> 'members',
								 				  	 	 'order'	=> array( $sort => $order ),
								 				  	 	 'limit'	=> array( $start, 20 ),
									 		  	  ) 	);
			}

			$this->ifthd->core->db->execute();
		}

		while( $m = $this->ifthd->core->db->fetch_row() )
		{
			$row_count ++;
				
			( $row_count & 1 ) ? $row_class = 'option1-med' : $row_class = 'option2-med';
				
			#=============================
			# Fix Up Information
			#=============================

			$mem_group = $this->ifthd->core->cache['group'][ $m['mgroup'] ]['g_name'];

			$member_rows .= "<tr>
								<td class='{$row_class}'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=view&amp;id={$m['id']}'>{$m['id']}</a></td>
								<td class='{$row_class}'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=view&amp;id={$m['id']}'>{$m['name']}</a></td>
								<td class='{$row_class}' style='font-weight: normal'>{$mem_group}</td>
								<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=list&amp;mid={$m['id']}'>{$m['open_tickets']}</a></td>
								<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=list&amp;mid={$m['id']}&amp;status=all'>{$m['tickets']}</a></td>
								<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=edit&amp;id={$m['id']}'><img src='<! IMG_DIR !>/button_edit.gif' alt='Edit' /></a></td>
								<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=dodel&amp;id={$m['id']}' onclick='return sure_delete()'><img src='<! IMG_DIR !>/button_delete.gif' alt='Delete' /></a></td>
							</tr>";
		}

		$page_links = $this->ifthd->page_links( '?section=manage&amp;act=member&amp;code=list'. $link_extra, $mem_count, 20, $start, 1 );

		if ( $page_links )
		{
			$page_links = "<br />". $page_links;
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
								if ( confirm(\"Are you sure you want to delete this member?\\n\\nAll tickets owned by this member will be deleted.\") )
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
						<div class='groupbox'><div style='float: right'><a onclick=\"javascript:Effect.toggle('msearch','blind',{duration: 0.5});\" class='fake_link' title='Search for a member'><img src='<! IMG_DIR !>/button_mini_search.gif' alt='Search' /></a></div>Members List</div>
						<div id='msearch' style='display:none'>
							<form class='lspace' action='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=search' method='post'>
							<div class='option1'><input type='text' name='m_search' id='m_search' size='30' /> <input type='submit' name='do_search' id='do_search' value='Search' /></div>
							</form>
						</div>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<th width='5%' align='left'>{$link_id}</th>
							<th width='24%' align='left'>{$link_name}</th>
							<th width='21%' align='left'>{$link_mgroup}</th>
							<th width='15%'>{$link_open_tickets}</th>
							<th width='15%'>{$link_tickets}</th>
							<th width='8%'>Edit</th>
							<th width='12%'>Delete</th>
						</tr>
						". $member_rows ."
						</table>
						<div class='formtail'><div class='fb_pad'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=add' class='fake_button'>Add A New Member</a></div></div>
						{$page_links}";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=member'>Members</a>",
						   "List Members",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Members' ) );
	}

	#=======================================
	# @ List Pending
	# Show a list of members awaiting admin
	# approval.
	#=======================================

	function list_pending()
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

		if ( $order == 'DESC' )
		{
			$$order_var = "&amp;order=asc";
			$$img_var = "&nbsp;<img src='<! IMG_DIR !>/arrow_up.gif' alt='UP' />";
		}
		else
		{
			$$order_var = "&amp;order=desc";
			$$img_var = "&nbsp;<img src='<! IMG_DIR !>/arrow_down.gif' alt='DOWN' />";
		}

		$link_id = "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=mod&amp;sort=id". $order_id ."'>ID". $img_id ."</a>";
		$link_name = "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=mod&amp;sort=name". $order_name ."'>Name". $img_name ."</a>";
		$link_email_val = "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=mod&amp;sort=email_val". $order_email_val ."'>Email Validated". $img_email_val ."</a>";

		$link_extra = ""; // Initialize for Security

		if ( $this->ifthd->input['sort'] )
		{
			$link_extra .= "&amp;sort=". $this->ifthd->input['sort'];
		}
		if ( $this->ifthd->input['order'] )
		{
			$link_extra .= "&amp;order=". $this->ifthd->input['order'];
		}

		#=============================
		# Grab Members
		#=============================

		if ( $this->ifthd->input['st'] )
		{
			$start = $this->ifthd->input['st'];
		}
		else
		{
			$start = 0;
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'members',
											  	 'where'	=> array( 'admin_val', '!=', 1 ),
										  	 	 'order'	=> array( $sort => $order ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$member_rows = ""; // Initialize for Security
		$row_count = 0; // Initialize for Security

		if ( ! $mem_count = $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_members_found');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'members',
											  	 'where'	=> array( 'admin_val', '!=', 1 ),
						 				  	 	 'order'	=> array( $sort => $order ),
						 				  	 	 'limit'	=> array( $start, 20 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		while( $m = $this->ifthd->core->db->fetch_row() )
		{
			$row_count ++;
				
			( $row_count & 1 ) ? $row_class = 'option1-med' : $row_class = 'option2-med';
			
			#=============================
			# Fix Up Information
			#=============================
			
			( $m['email_val'] ) ? $m['email_val'] = 'Yes' : $m['email_val'] = 'No';

			$member_rows .= "<tr>
								<td class='{$row_class}'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=view&amp;id={$m['id']}'>{$m['id']}</a></td>
								<td class='{$row_class}'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=view&amp;id={$m['id']}'>{$m['name']}</a></td>
								<td class='{$row_class}' align='center'>{$m['email_val']}</td>
								<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=approve&amp;id={$m['id']}'><img src='<! IMG_DIR !>/button_approve.gif' alt='Approve' /></a></td>
								<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=edit&amp;id={$m['id']}'><img src='<! IMG_DIR !>/button_edit.gif' alt='Edit' /></a></td>
								<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=dodel&amp;id={$m['id']}' onclick='return sure_delete()'><img src='<! IMG_DIR !>/button_delete.gif' alt='Delete' /></a></td>
							</tr>";
		}

		$page_links = $this->ifthd->page_links( '?section=manage&amp;act=member&amp;code=mod'. $link_extra, $mem_count, 20, $start, 1 );

		$this->output = "<script type='text/javascript'>

							function sure_delete()
							{
								if ( confirm(\"Are you sure you want to delete this member?\\n\\nAll tickets owned by this member will be deleted.\") )
								{
									return true;
								}
								else
								{
									return false;
								}
							}

						</script>

						<div class='groupbox'>Approve Members</div>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<th width='5%' align='left'>{$link_id}</th>
							<th width='42%' align='left'>{$link_name}</th>
							<th width='17%'>{$link_email_val}</th>
							<th width='16%'>Approve</th>
							<th width='8%'>Edit</th>
							<th width='12%'>Delete</th>
						</tr>
						". $member_rows ."
						</table>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=member'>Members</a>",
						   "Approve Members",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Members' ) );
	}

	#=======================================
	# @ View Member
	# Show member information.
	#=======================================

	function view_member()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'members',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_member');
		}

		$m = $this->ifthd->core->db->fetch_row();

		#=============================
		# Custom Profile Fields
		#=============================
		
		$row_count = 0; // Initialize for Security

		if ( is_array( $this->ifthd->core->cache['pfields'] ) )
		{
			$cpfields = ""; // Initialize for Security

			$cpfdata = unserialize( $m['cpfields'] );

			foreach( $this->ifthd->core->cache['pfields'] as $id => $f )
			{
				$f_perm = unserialize( $f['perms'] );

				if ( $f_perm[ $m['mgroup'] ] )
				{
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
					
					$row_count ++;
					
					( $row_count & 1 ) ? $row_class = 'option1' : $row_class = 'option2';

					$cpfields .= "<tr>
									<td class='{$row_class}'>{$f['name']}</td>
									<td class='{$row_class}'>{$f['value']}</td>
								</tr>";

					$soggy = ""; // Reset
				}
			}
		}

		#=============================
		# Do Output
		#=============================

		$mem_joined = $this->ifthd->ift_date( $m['joined'] );
		$mem_offset = ( $m['time_zone'] * 60 * 60 ) + ( $m['dst_active'] * 60 * 60 );
		$mem_time = $this->ifthd->ift_date( time(), '', 0, 0, 1, $mem_offset, 1 );

		$m['rating'] = $this->get_stars( $m['rating'] );

		if ( ! $m['admin_val'] )
		{
			$approve_link = "&nbsp;&nbsp;<a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=approve&amp;id={$m['id']}' class='fake_button'>Approve Account</a>";
		}

		$this->output = "<script type='text/javascript'>

							function sure_delete()
							{
								if ( confirm(\"Are you sure you want to delete this member?\\n\\nAll tickets owned by this member will be deleted.\") )
								{
									return true;
								}
								else
								{
									return false;
								}
							}

						</script>

						<div class='groupbox'>Viewing Member: {$m['name']}</div>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<td class='option1' width='20%'>Username</td>
							<td class='option1' width='80%' style='font-weight: normal'>{$m['name']}</td>
						</tr>
						<tr>
							<td class='option2'>Email</td>
							<td class='option2' style='font-weight: normal'>{$m['email']}</td>
						</tr>
						<tr>
							<td class='option1'>Group</td>
							<td class='option1' style='font-weight: normal'>{$this->ifthd->core->cache['group'][ $m['mgroup'] ]['g_name']}</td>
						</tr>
						<tr>
							<td class='option2'>Local Time</td>
							<td class='option2' style='font-weight: normal'>{$mem_time}</td>
						</tr>
						<tr>
							<td class='option1'>Joined</td>
							<td class='option1' style='font-weight: normal'>{$mem_joined}</td>
						</tr>
						<tr>
							<td class='option2'>Open Tickets</td>
							<td class='option2' style='font-weight: normal'>{$m['open_tickets']}</td>
						</tr>
						<tr>
							<td class='option1'>Tickets</td>
							<td class='option1' style='font-weight: normal'>{$m['tickets']}</td>
						</tr>
						<tr>
							<td class='option2'>Rating</td>
							<td class='option2'>{$m['rating']}</td>
						</tr>
						{$cpfields}
						</table>
						<div class='formtail'><div class='fb_pad'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=edit&amp;id={$m['id']}' class='fake_button'>Modify Account</a>&nbsp;&nbsp;<a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=sig&amp;id={$m['id']}' class='fake_button'>Edit Signature</a>&nbsp;&nbsp;<a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=list&amp;mid={$m['id']}' class='fake_button'>View Tickets</a>&nbsp;&nbsp;<a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=add&amp;mid={$m['id']}' class='fake_button'>Submit A Ticket</a>{$approve_link}&nbsp;&nbsp;<a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=dodel&amp;id={$m['id']}' class='fake_button' onclick='return sure_delete()'>Delete Account</a></div></div>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=member'>Members</a>",
						   "View Member",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Members' ) );
	}

	#=======================================
	# @ Add Member
	# Show add member form.
	#=======================================

	function add_member($error="")
	{
		#=============================
		# Do Output
		#=============================

		if ( ! $this->ifthd->member['acp']['manage_member_add'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		if ( $error )
		{
			$error = "<div class='critical'>{$error}</div>";
		}

		$mem_group_drop = $this->ifthd->build_group_drop( $this->ifthd->input['mgroup'] );
		$time_zone_drop = $this->ifthd->build_time_zone_drop( $this->ifthd->input['time_zone'] );
		$lang_drop = $this->ifthd->build_lang_drop( $this->ifthd->input['lang'] );
		$skin_drop = $this->ifthd->build_skin_drop( $this->ifthd->input['skin'] );

		#=============================
		# Custom Profile Fields
		#=============================
		
		$row_count = 0; // Initialize for Security

		if ( is_array( $this->ifthd->core->cache['pfields'] ) )
		{
			$cpfields = ""; // Initialize for Security

			$cpfdata = unserialize( $m['cpfields'] );

			foreach( $this->ifthd->core->cache['pfields'] as $id => $f )
			{
				if ( $f['reg'] )
				{
					$row_count ++;
					
					( $row_count & 1 ) ? $row_class = 'option1-med' : $row_class = 'option2-med';
					
					if ( ! $f['required'] )
					{
						$optional = "(Optional)";
					}

					if ( $error )
					{
						$f['value'] = $this->ifthd->input[ 'cpf_'. $f['fkey'] ];
					}

					if ( $f['type'] == 'textfield' )
					{
						$cpfields .= "<tr>
										<td class='{$row_class}'><label for='cpf_{$f['fkey']}'>{$f['name']}</label></td>
										<td class='{$row_class}' style='font-weight: normal'><input type='text' name='cpf_{$f['fkey']}' id='cpf_{$f['fkey']}' value='{$f['value']}' size='45' /> {$optional}</td>
									</tr>";
					}
					elseif ( $f['type'] == 'textarea' )
					{
						$cpfields .= "<tr>
										<td class='{$row_class}'><label for='cpf_{$f['fkey']}'>{$f['name']}</label></td>
										<td class='{$row_class}' style='font-weight: normal'><textarea name='cpf_{$f['fkey']}' id='cpf_{$f['fkey']}' cols='50' rows='3'>{$f['value']}</textarea> {$optional}</td>
									</tr>";
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

						$cpfields .= "<tr>
										<td class='{$row_class}'><label for='cpf_{$f['fkey']}'>{$f['name']}</label></td>
										<td class='{$row_class}' style='font-weight: normal'><select name='cpf_{$f['fkey']}' id='cpf_{$f['fkey']}'>{$f['options']}</select> {$optional}</td>
									</tr>";
					}
					elseif ( $f['type'] == 'checkbox' )
					{
						if ( $f['extra'] ) $f['extra'] = " <label for='cpf_{$f['fkey']}'>{$f['extra']}</label>";
						if ( $f['value'] ) $f['checked'] = " checked='checked'";

						$cpfields .= "<tr>
										<td class='{$row_class}'><label for='cpf_{$f['fkey']}'>{$f['name']}</label></td>
										<td class='{$row_class}' style='font-weight: normal'><input type='checkbox' name='cpf_{$f['fkey']}' id='cpf_{$f['fkey']}' value='1' class='ckbox'{$f['checked']} />{$f['extra']}</td>
									</tr>";
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

						$cpfields .= "<tr>
										<td class='{$row_class}'><label for='cpf_{$f['fkey']}'>{$f['name']}</label></td>
										<td class='{$row_class}' style='font-weight: normal'>{$f['options']} {$optional}</td>
									</tr>";
					}
				}

				$optional = ""; // Reset
				$f['options'] = ""; // Reset
			}
		}

		$this->output = "<script type='text/javascript'>

						function validate_form(form)
						{
							if ( ! form.username.value )
							{
								alert('Please enter a username.');
								form.username.focus();
								return false;
							}

							if ( ! form.email.value )
							{
								alert('Please enter an email address.');
								form.email.focus();
								return false;
							}

							if ( ! form.password.value )
							{
								alert('Please enter a password.');
								form.password.focus();
								return false;
							}
						}

						</script>
						{$error}
						<form action='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=doadd' method='post' onsubmit='return validate_form(this)'>
						<div class='groupbox'>Add A New Member</div>
						<div class='subbox'>General Information</div>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<td class='option1' width='22%'><label for='username'>Username</label></td>
							<td class='option1' width='78%'><input type='text' name='username' id='username' value='{$this->ifthd->input['username']}' size='35' /></td>
						</tr>
						<tr>
							<td class='option2'><label for='email'>Email</label></td>
							<td class='option2'><input type='text' name='email' id='email' value='{$this->ifthd->input['email']}' size='35' /></td>
						</tr>
						<tr>
							<td class='option1'><label for='password'>Password</label></td>
							<td class='option1'><input type='text' name='password' id='password' value='{$this->ifthd->input['password']}' size='35' /></td>
						</tr>
						<tr>
							<td class='option2'><label for='title'>Title</label></td>
							<td class='option2'><input type='text' name='title' id='title' value='{$this->ifthd->input['title']}' size='35' /></td>
						</tr>
						<tr>
							<td class='option1'><label for='mgroup'>Group</label></td>
							<td class='option1'><select name='mgroup' id='mgroup'>{$mem_group_drop}</select></td>
						</tr>
						<tr>
							<td class='option2'><label for='time_zone'>Time Zone</label></td>
							<td class='option2'><select name='time_zone' id='time_zone'>{$time_zone_drop}</select></td>
						</tr>
						<tr>
							<td class='option1'>DST Active</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'dst_active', $this->ifthd->input['dst_active'] ) ."
							</td>
						</tr>
						<tr>
							<td class='option2'><label for='lang'>Language</label></td>
							<td class='option2'><select name='lang' id='lang'>{$lang_drop}</select></td>
						</tr>
						<tr>
							<td class='option1'><label for='skin'>Skin</label></td>
							<td class='option1'><select name='skin' id='skin'>{$skin_drop}</select></td>
						</tr>
						<tr>
							<td class='option2'>Rich Text Editor</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->enabled_disabled_radio( 'use_rte', $this->ifthd->input['use_rte'] ) ."
							</td>
						</tr>
						{$cpfields}
						</table>
						<div class='subbox'>Email Preferences</div>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<td class='option2' width='22%'>Email Notifications</td>
							<td class='option2' width='78%' style='font-weight: normal'>
								". $this->ifthd->skin->enabled_disabled_radio( 'email_notify', $this->ifthd->input['email_notify'] ) ."
							</td>
						</tr>
						<tr>
							<td class='option1'>Email Type</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->special_radio( 'email_html', 'HTML', 'Plain Text', $this->ifthd->input['email_html'] ) ."
							</td>
						</tr>
						<tr>
							<td class='option2'>Notifications For</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->checkbox( 'email_new_ticket', 'New Ticket', $this->ifthd->input['email_new_ticket'] ) ."
								". $this->ifthd->skin->checkbox( 'email_ticket_reply', 'New Reply', $this->ifthd->input['email_ticket_reply'] ) ."
								". $this->ifthd->skin->checkbox( 'email_announce', 'Announcements', $this->ifthd->input['email_announce'] ) ."
							</td>
						</tr>
						</table>
						<div class='formtail'><input type='submit' name='submit' id='add' value='Add Member' class='button' /></div>
						</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=member'>Members</a>",
						   "Add Member",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Members' ) );
	}

	#=======================================
	# @ Edit Member
	# Show edit member form.
	#=======================================

	function edit_member($error='')
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_member_edit'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'members',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_member');
		}

		$m = $this->ifthd->core->db->fetch_row();

		if ( $m['id'] == 1 && $this->ifthd->member['id'] != 1 )
		{
			$this->ifthd->skin->error('root_edit_mem');
		}
		
		if ( $this->ifthd->core->cache['group'][ $m['mgroup'] ]['g_acp_access'] && ! $this->ifthd->member['acp']['manage_member_staff'] ) $this->ifthd->skin->error('no_perm');

		#=============================
		# Do Output
		#=============================

		if ( $error )
		{
			$error = "<div class='critical'>{$error}</div>";

			$mem_name = $this->ifthd->input['username'];
			$mem_email = $this->ifthd->input['email'];
			$mem_title = $this->ifthd->input['title'];
			$mem_use_rte = $this->ifthd->input['use_rte'];
			$mem_dst_active = $this->ifthd->input['dst_active'];

			$mem_group_drop = $this->ifthd->build_group_drop( $this->ifthd->input['mgroup'] );
			$mem_time_zone_drop = $this->ifthd->build_time_zone_drop( $this->ifthd->input['time_zone'] );
			$mem_lang_drop = $this->ifthd->build_lang_drop( $this->ifthd->input['lang'] );
			$mem_skin_drop = $this->ifthd->build_skin_drop( $this->ifthd->input['skin'] );

			$mem_email_notify = $this->ifthd->input['email_notify'];
			$mem_email_html = $this->ifthd->input['email_html'];
			$mem_email_new_ticket = $this->ifthd->input['email_new_ticket'];
			$mem_email_ticket_reply = $this->ifthd->input['email_ticket_reply'];
			$mem_email_staff_new_ticket = $this->ifthd->input['email_staff_new_ticket'];
			$mem_email_staff_ticket_reply = $this->ifthd->input['email_staff_ticket_reply'];
			$mem_email_announce = $this->ifthd->input['email_announce'];

			$ban_ticket_center = $this->ifthd->input['ban_ticket_center'];
			$ban_ticket_open = $this->ifthd->input['ban_ticket_open'];
			$ban_ticket_escalate = $this->ifthd->input['ban_ticket_escalate'];
			$ban_ticket_rate = $this->ifthd->input['ban_ticket_rate'];
			$ban_kb = $this->ifthd->input['ban_kb'];
			$ban_kb_comment = $this->ifthd->input['ban_kb_comment'];
			$ban_kb_rate = $this->ifthd->input['ban_kb_rate'];
		}
		else
		{
			$mem_name = $m['name'];
			$mem_email = $m['email'];
			$mem_title = $m['title'];
			$mem_use_rte = $m['use_rte'];
			$mem_dst_active = $m['dst_active'];

			$mem_group_drop = $this->ifthd->build_group_drop( $m['mgroup'] );
			$mem_time_zone_drop = $this->ifthd->build_time_zone_drop( $m['time_zone'] );
			$mem_lang_drop = $this->ifthd->build_lang_drop( $m['lang'] );
			$mem_skin_drop = $this->ifthd->build_skin_drop( $m['skin'] );

			$mem_email_notify = $m['email_notify'];
			$mem_email_html = $m['email_html'];
			$mem_email_new_ticket = $m['email_new_ticket'];
			$mem_email_ticket_reply = $m['email_ticket_reply'];
			$mem_email_staff_new_ticket = $m['email_staff_new_ticket'];
			$mem_email_staff_ticket_reply = $m['email_staff_ticket_reply'];
			$mem_email_announce = $m['email_announce'];

			$ban_ticket_center = $m['ban_ticket_center'];
			$ban_ticket_open = $m['ban_ticket_open'];
			$ban_ticket_escalate = $m['ban_ticket_escalate'];
			$ban_ticket_rate = $m['ban_ticket_rate'];
			$ban_kb = $m['ban_kb'];
			$ban_kb_comment = $m['ban_kb_comment'];
			$ban_kb_rate = $m['ban_kb_rate'];
		}

		#=============================
		# Custom Profile Fields
		#=============================
		
		$row_count = 0; // Initialize for Security

		if ( is_array( $this->ifthd->core->cache['pfields'] ) )
		{
			$cpfields = ""; // Initialize for Security

			$cpfdata = unserialize( $m['cpfields'] );

			foreach( $this->ifthd->core->cache['pfields'] as $id => $f )
			{
				$f_perm = unserialize( $f['perms'] );

				if ( $f_perm[ $m['mgroup'] ] )
				{
					$row_count ++;
						
					( $row_count & 1 ) ? $row_class = 'option1-med' : $row_class = 'option2-med';
					
					if ( ! $f['required'] )
					{
						$optional = "(Optional)";
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
						$cpfields .= "<tr>
										<td class='{$row_class}'><label for='cpf_{$f['fkey']}'>{$f['name']}</label></td>
										<td class='{$row_class}' style='font-weight: normal'><input type='text' name='cpf_{$f['fkey']}' id='cpf_{$f['fkey']}' value='{$f['value']}' size='45' /> {$optional}</td>
									</tr>";
					}
					elseif ( $f['type'] == 'textarea' )
					{
						$cpfields .= "<tr>
										<td class='{$row_class}'><label for='cpf_{$f['fkey']}'>{$f['name']}</label></td>
										<td class='{$row_class}' style='font-weight: normal'><textarea name='cpf_{$f['fkey']}' id='cpf_{$f['fkey']}' cols='50' rows='3'>{$f['value']}</textarea> {$optional}</td>
									</tr>";
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

						$cpfields .= "<tr>
										<td class='{$row_class}'><label for='cpf_{$f['fkey']}'>{$f['name']}</label></td>
										<td class='{$row_class}' style='font-weight: normal'><select name='cpf_{$f['fkey']}' id='cpf_{$f['fkey']}'>{$f['options']}</select> {$optional}</td>
									</tr>";
					}
					elseif ( $f['type'] == 'checkbox' )
					{
						if ( $f['extra'] ) $f['extra'] = " <label for='cpf_{$f['fkey']}'>{$f['extra']}</label>";
						if ( $f['value'] ) $f['checked'] = " checked='checked'";

						$cpfields .= "<tr>
										<td class='{$row_class}'><label for='cpf_{$f['fkey']}'>{$f['name']}</label></td>
										<td class='{$row_class}' style='font-weight: normal'><input type='checkbox' name='cpf_{$f['fkey']}' id='cpf_{$f['fkey']}' value='1' class='ckbox'{$f['checked']} />{$f['extra']}</td>
									</tr>";
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

						$cpfields .= "<tr>
										<td class='{$row_class}'><label for='cpf_{$f['fkey']}'>{$f['name']}</label></td>
										<td class='{$row_class}' style='font-weight: normal'>{$f['options']} {$optional}</td>
									</tr>";
					}
				}

				$optional = ""; // Reset
				$f['options'] = ""; // Reset
			}
		}

		$this->output = "<script type='text/javascript'>

						function validate_form(form)
						{
							if ( ! form.username.value )
							{
								alert('Please enter a username.');
								form.username.focus();
								return false;
							}

							if ( ! form.email.value )
							{
								alert('Please enter an email address.');
								form.email.focus();
								return false;
							}
						}

						</script>
						{$error}
						<form action='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=doedit&amp;id={$m['id']}' method='post' onsubmit='return validate_form(this)'>
						<div class='groupbox'>Editing Member: {$m['name']}</div>
						<div class='subbox'>General Information</div>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<td class='option1' width='22%'><label for='username'>Username</label></td>
							<td class='option1' width='78%'><input type='text' name='username' id='username' value='{$mem_name}' size='35' /></td>
						</tr>
						<tr>
							<td class='option2'><label for='email'>Email</label></td>
							<td class='option2'><input type='text' name='email' id='email' value='{$mem_email}' size='35' /></td>
						</tr>
						<tr>
							<td class='option1'><label for='password'>Password</label></td>
							<td class='option1' style='font-weight: normal'><input type='text' name='password' id='password' value='' size='35' /> (Leave blank to not edit)</td>
						</tr>
						<tr>
							<td class='option2'><label for='title'>Title</label></td>
							<td class='option2'><input type='text' name='title' id='title' value='{$mem_title}' size='35' /></td>
						</tr>
						<tr>
							<td class='option1'><label for='mgroup'>Group</label></td>
							<td class='option1'><select name='mgroup' id='mgroup'>{$mem_group_drop}</select></td>
						</tr>
						<tr>
							<td class='option2'><label for='time_zone'>Time Zone</label></td>
							<td class='option2'><select name='time_zone' id='time_zone'>{$mem_time_zone_drop}</select></td>
						</tr>
						<tr>
							<td class='option1'>DST Active</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'dst_active', $mem_dst_active ) ."
							</td>
						</tr>
						<tr>
							<td class='option2'><label for='lang'>Language</label></td>
							<td class='option2'><select name='lang' id='lang'>{$mem_lang_drop}</select></td>
						</tr>
						<tr>
							<td class='option1'><label for='skin'>Skin</label></td>
							<td class='option1'><select name='skin' id='skin'>{$mem_skin_drop}</select></td>
						</tr>
						<tr>
							<td class='option2'>Rich Text Editor</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->enabled_disabled_radio( 'use_rte', $mem_use_rte ) ."
							</td>
						</tr>
						{$cpfields}
						</table>
						<div class='subbox'>Email Preferences</div>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<td class='option2' width='22%'>Email Notifications</td>
							<td class='option2' width='78%' style='font-weight: normal'>
								". $this->ifthd->skin->enabled_disabled_radio( 'email_notify', $mem_email_notify ) ."
							</td>
						</tr>
						<tr>
							<td class='option1'>Email Type</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->special_radio( 'email_html', 'HTML', 'Plain Text', $mem_email_html ) ."
							</td>
						</tr>
						<tr>
							<td class='option2'>Notifications For</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->checkbox( 'email_new_ticket', 'New Ticket', $mem_email_new_ticket ) ."&nbsp;
								". $this->ifthd->skin->checkbox( 'email_ticket_reply', 'New Reply', $mem_email_ticket_reply ) ."&nbsp;
								". $this->ifthd->skin->checkbox( 'email_announce', 'Announcements', $mem_email_announce ) ."
								<div style='margin-top:3px'>". $this->ifthd->skin->checkbox( 'email_staff_new_ticket', 'New Tickets in My Departments (Applies to Staff Only)', $mem_email_staff_new_ticket ) ."</div>
								<div style='margin-top:3px'>". $this->ifthd->skin->checkbox( 'email_staff_ticket_reply', 'New Replies in My Departments (Applies to Staff Only)', $mem_email_staff_ticket_reply ) ."</div>
							</td>
						</tr>
						</table>
						<div class='subbox'>Ban Settings</div>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<td class='option1' width='22%'>Ban Ticket Center</td>
							<td class='option1' width='78%' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'ban_ticket_center', $ban_ticket_center ) ."
							</td>
						</tr>
						<tr>
							<td class='option2'>Ban Open Ticket</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'ban_ticket_open', $ban_ticket_open ) ."
							</td>
						</tr>
						<tr>
							<td class='option1'>Ban Escalate Ticket</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'ban_ticket_escalate', $ban_ticket_escalate ) ."
							</td>
						</tr>
						<tr>
							<td class='option2'>Ban Rate Replies</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'ban_ticket_rate', $ban_ticket_rate ) ."
							</td>
						</tr>
						<tr>
							<td class='option1'>Ban Knowledge Base</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'ban_kb', $ban_kb ) ."
							</td>
						</tr>
						<tr>
							<td class='option2'>Ban KB Comment</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'ban_kb_comment', $ban_kb_comment ) ."
							</td>
						</tr>
						<tr>
							<td class='option1'>Ban KB Rate</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'ban_kb_rate', $ban_kb_rate ) ."
							</td>
						</tr>
						</table>
						<div class='formtail'><input type='submit' name='submit' id='edit' value='Edit Member' class='button' /></div>
						</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=member'>Members</a>",
						   "Edit Member",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Members' ) );
	}

	#=======================================
	# @ Edit Signature
	# Show edit signature form.
	#=======================================

	function edit_signature()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_member_edit'] && ! $this->ifthd->input['id'] != $this->ifthd->member['id'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'name', 'signature', 'auto_sig' ),
											  	 'from'		=> 'members',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_member');
		}

		$m = $this->ifthd->core->db->fetch_row();

		#=============================
		# Do Output
		#=============================

		$signature = $m['signature'];
		if ( $m['auto_sig'] ) $auto_sig = ' checked="checked"';

		if ( $this->ifthd->member['use_rte'] && $this->ifthd->core->cache['config']['enable_ticket_rte'] )
		{
			$rte_javascript = "<script language='javascript' type='text/javascript' src='<! HD_URL !>/includes/tinymce/tiny_mce.js'></script>
								<script language='javascript' type='text/javascript'>
								tinyMCE.init({
									mode : 'exact',
									theme : 'advanced',
									elements : 'signature',
									plugins : 'inlinepopups,safari,spellchecker',
									dialog_type : 'modal',
									forced_root_block : false,
									force_br_newlines : true,
									force_p_newlines : false,
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

		$this->output = "{$rte_javascript}
						<form action='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=dosig&amp;id={$m['id']}' method='post' onsubmit='return validate_form(this)'>
						<div class='groupbox'>Editing Member's Signature: {$m['name']}</div>
						<div class='option1'><textarea name='signature' id='signature' rows='6' cols='120' style='width: 98%; height: 150px;'>{$signature}</textarea></div>
						<div class='option2'><input type='checkbox' name='auto_sig' id='auto_sig' value='1' class='ckbox'{$auto_sig} /> <label for='auto_sig'>Append Signature to Ticket Replies by Default</label></div>
						<div class='formtail'><input type='submit' name='submit' id='edit' value='Edit Signature' class='button' /></div>
						</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=member'>Members</a>",
						   "Edit Signature",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Members' ) );
	}

	#=======================================
	# @ Do Add
	# Create a new member.
	#=======================================

	function do_add()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['acp']['manage_member_add'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		if ( ! $this->ifthd->input['username'] )
		{
			$this->add_member('Please enter a username.');
		}

		if ( ! $this->ifthd->validate_email( $this->ifthd->input['email'] ) )
		{
			$this->add_member('Please enter a valid email address.');
		}

		if ( ! $this->ifthd->input['password'] )
		{
			$this->add_member('Please enter a password.');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id' ),
											  	 'from'		=> 'members',
											  	 'where'	=> array( 'email|lower', '=', strtolower( $this->ifthd->input['email'] ) ),
									  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			$this->add_member('Sorry, that email is already in use.  Please choose another email.');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id' ),
											  	 'from'		=> 'members',
											  	 'where'	=> array( 'name|lower', '=', strtolower( $this->ifthd->input['username'] ) ),
									  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			$this->add_member('Sorry, that username is already in use.  Please choose another username.');
		}
		
		if ( $this->ifthd->core->cache['group'][ $this->ifthd->input['mgroup'] ]['g_acp_access'] && ! $this->ifthd->member['acp']['manage_member_staff'] ) $this->ifthd->skin->error('no_perm');

		#=============================
		# Custom Profile Fields
		#=============================

		if ( is_array( $this->ifthd->core->cache['pfields'] ) )
		{
			$cpfvalues = ""; // Initialize for Security

			while ( list( $id, $f ) = each( $this->ifthd->core->cache['pfields'] ) )
			{
				if ( $f['reg'] )
				{
					if ( $f['required'] && $f['type'] != 'checkbox' )
					{
						if ( ! $this->ifthd->input[ 'cpf_'. $f['fkey'] ] )
						{
							$this->add_member( 'Please enter a value for the field: '. $f['name'] );
						}
					}

					$cpfvalues[ $f['fkey'] ] = $this->ifthd->input[ 'cpf_'. $f['fkey'] ];
				}
			}
		}

		#=============================
		# Insert Member
		#=============================

		$pass_salt = substr( md5( 'ps' . uniqid( rand(), true ) ), 0, 9 );
		$pass_hash = sha1( md5( $this->ifthd->input['password'] . $pass_salt ) );

		$db_array = array(
						  'name'						=> $this->ifthd->input['username'],
						  'email'						=> $this->ifthd->input['email'],
						  'password'					=> $pass_hash,
						  'pass_salt'					=> $pass_salt,
						  'login_key'					=> str_replace( "=", "", base64_encode( strrev( crypt( md5( 'lk'. uniqid( rand(), true ) . $this->ifthd->input['username'] ) ) ) ) ),
						  'mgroup'						=> $this->ifthd->input['mgroup'],
						  'title'						=> $this->ifthd->input['title'],
						  'joined'						=> time(),
						  'ipadd'						=> $this->ifthd->input['ip_address'],
						  'email_notify'				=> $this->ifthd->input['email_notify'],
						  'email_html'					=> $this->ifthd->input['email_html'],
						  'email_new_ticket'			=> $this->ifthd->input['email_new_ticket'],
						  'email_ticket_reply'			=> $this->ifthd->input['email_ticket_reply'],
						  'email_announce'				=> $this->ifthd->input['email_announce'],
						  'time_zone'					=> $this->ifthd->input['time_zone'],
						  'dst_active'					=> $this->ifthd->input['dst_active'],
						  'lang'						=> $this->ifthd->input['lang'],
						  'skin'						=> $this->ifthd->input['skin'],
						  'use_rte'						=> $this->ifthd->input['use_rte'],
						  'cpfields'					=> serialize($cpfvalues),
						  'email_val'					=> 1,
						  'admin_val'					=> 1,
						  'rss_key'						=> md5( 'rk' . uniqid( rand(), true ) ),
						 );

		$this->ifthd->core->db->construct( array(
											  	 'insert'	=> 'members',
											  	 'set'		=> $db_array,
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$member_id = $this->ifthd->core->db->get_insert_id();

		$this->ifthd->log( 'admin', "Member Added &#039;". $this->ifthd->input['username'] ."&#039;", 1, $member_id );

		#=============================
		# Update New Group
		#=============================

		$this->ifthd->core->db->next_no_quotes('set');

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'groups',
											  	 'set'		=> array( 'g_members' => 'g_members+1' ),
							 				  	 'where'	=> array( 'g_id', '=', $this->ifthd->input['mgroup'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		#=============================
		# Update Stats / Cache
		#=============================

		$this->ifthd->r_member_stats(1);
		
		$this->ifthd->rebuild_staff_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=member&code=view&id='. $member_id, 'add_user_success' );
		$this->list_members( '', 'The member has been successfully added.' );
	}

	#=======================================
	# @ Do Edit
	# Edit a member.
	#=======================================

	function do_edit()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_member_edit'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		if ( $this->ifthd->input['id'] == 1 && $this->ifthd->member['id'] != 1 )
		{
			$this->ifthd->skin->error('root_edit_mem');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'members',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_member');
		}

		$m = $this->ifthd->core->db->fetch_row();

		if ( ! $this->ifthd->input['username'] )
		{
			$this->edit_member('Please enter a username.');
		}

		$new_group = 0; // Initialize for Security

		if ( $m['mgroup'] != $this->ifthd->input['mgroup'] )
		{
			$new_group = 1;
		}

		if ( ! $this->ifthd->validate_email( $this->ifthd->input['email'] ) )
		{
			$this->edit_member('Please enter a valid email address.');
		}

		$new_pass = 0; // Initialize for Security

		if ( $this->ifthd->input['password'] )
		{
			$new_pass = 1;
		}
		
		if ( $this->ifthd->core->cache['group'][ $m['mgroup'] ]['g_acp_access'] && ! $this->ifthd->member['acp']['manage_member_staff'] ) $this->ifthd->skin->error('no_perm');
		if ( $this->ifthd->core->cache['group'][ $this->ifthd->input['mgroup'] ]['g_acp_access'] && ! $this->ifthd->member['acp']['manage_member_staff'] ) $this->ifthd->skin->error('no_perm');

		#=============================
		# Custom Profile Fields
		#=============================

		if ( is_array( $this->ifthd->core->cache['pfields'] ) )
		{
			$cpfvalues = ""; // Initialize for Security

			while ( list( $id, $f ) = each( $this->ifthd->core->cache['pfields'] ) )
			{
				$f_perm = unserialize( $f['perms'] );

				if ( $f_perm[ $m['mgroup'] ] )
				{
					if ( $f['required'] && $f['type'] != 'checkbox' )
					{
						if ( ! $this->ifthd->input[ 'cpf_'. $f['fkey'] ] )
						{
							$this->edit_member( 'Please enter a value for the field: '. $f['name'] );
						}
					}

					$cpfvalues[ $f['fkey'] ] = $this->ifthd->input[ 'cpf_'. $f['fkey'] ];
				}
			}
		}

		#=============================
		# Update Member
		#=============================

		$db_array = array(
						  'name'						=> $this->ifthd->input['username'],
						  'email'						=> $this->ifthd->input['email'],
						  'mgroup'						=> $this->ifthd->input['mgroup'],
						  'title'						=> $this->ifthd->input['title'],
						  'email_notify'				=> $this->ifthd->input['email_notify'],
						  'email_html'					=> $this->ifthd->input['email_html'],
						  'email_new_ticket'			=> $this->ifthd->input['email_new_ticket'],
						  'email_ticket_reply'			=> $this->ifthd->input['email_ticket_reply'],
						  'email_staff_new_ticket'		=> $this->ifthd->input['email_staff_new_ticket'],
						  'email_staff_ticket_reply'	=> $this->ifthd->input['email_staff_ticket_reply'],
						  'email_announce'				=> $this->ifthd->input['email_announce'],
						  'ban_ticket_center'			=> $this->ifthd->input['ban_ticket_center'],
						  'ban_ticket_open'				=> $this->ifthd->input['ban_ticket_open'],
						  'ban_ticket_escalate'			=> $this->ifthd->input['ban_ticket_escalate'],
						  'ban_ticket_rate'				=> $this->ifthd->input['ban_ticket_rate'],
						  'ban_kb'						=> $this->ifthd->input['ban_kb'],
						  'ban_kb_comment'				=> $this->ifthd->input['ban_kb_comment'],
						  'ban_kb_rate'					=> $this->ifthd->input['ban_kb_rate'],
						  'time_zone'					=> $this->ifthd->input['time_zone'],
						  'dst_active'					=> $this->ifthd->input['dst_active'],
						  'lang'						=> $this->ifthd->input['lang'],
						  'skin'						=> $this->ifthd->input['skin'],
						  'use_rte'						=> $this->ifthd->input['use_rte'],
						  'cpfields'					=> serialize($cpfvalues),
						 );

		if ( $new_pass )
		{
			$pass_salt = substr( md5( 'ps' . uniqid( rand(), true ) ), 0, 9 );
			$pass_hash = sha1( md5( $this->ifthd->input['password'] . $pass_salt ) );

			$db_array['password'] = $pass_hash;
			$db_array['pass_salt'] = $pass_salt;
			$db_array['login_key'] = str_replace( "=", "", base64_encode( strrev( crypt( md5( 'lk' . uniqid( rand(), true ) . $m['id'] ) ) ) ) );
		}

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'members',
											  	 'set'		=> $db_array,
							 				  	 'where'	=> array( 'id', '=', $m['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'admin', "Member Edited &#039;". $this->ifthd->input['username'] ."&#039;", 1, $m['id'] );

		if ( $new_group )
		{
			#=============================
			# Update Old Group
			#=============================

			$this->ifthd->core->db->next_no_quotes('set');

			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'groups',
												  	 'set'		=> array( 'g_members' => 'g_members-1' ),
								 				  	 'where'	=> array( 'g_id', '=', $m['mgroup'] ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			#=============================
			# Update New Group
			#=============================

			$this->ifthd->core->db->next_no_quotes('set');

			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'groups',
												  	 'set'		=> array( 'g_members' => 'g_members+1' ),
								 				  	 'where'	=> array( 'g_id', '=', $this->ifthd->input['mgroup'] ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$this->ifthd->rebuild_group_cache();
		}
		
		$this->ifthd->rebuild_staff_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=member&code=view&id='. $m['id'], 'edit_user_success' );
		$this->list_members( '', 'The member has been successfully updated.' );
	}

	#=======================================
	# @ Do Signature
	# Edit a member's signature.
	#=======================================

	function do_signature()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_member_edit'] && $this->ifthd->input['id'] != $this->ifthd->member['id'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'name' ),
											  	 'from'		=> 'members',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_member');
		}

		$m = $this->ifthd->core->db->fetch_row();

		#=============================
		# Update Member
		#=============================

		$db_array = array(
						  'signature'					=> $this->ifthd->input['signature'],
						  'auto_sig'					=> $this->ifthd->input['auto_sig'],
						 );

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'members',
											  	 'set'		=> $db_array,
							 				  	 'where'	=> array( 'id', '=', $m['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'admin', "Member Signature Edited &#039;". $m['name'] ."&#039;", 1, $m['id'] );

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=member&code=view&id='. $m['id'], 'edit_user_success' );
		$this->list_members( '', 'The member has been successfully updated.' );
	}

	#=======================================
	# @ Do Approve
	# Approve a member.
	#=======================================

	function do_approve()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_member_approve'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'members',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_member');
		}

		$m = $this->ifthd->core->db->fetch_row();

		#=============================
		# Approve Member
		#=============================

		if ( ! $m['email_val'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'members',
												  	 'set'		=> array( 'admin_val' => 1 ),
								 				  	 'where'	=> array( 'id', '=', $m['id'] ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();
		}
		else
		{
			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'members',
												  	 'set'		=> array( 'mgroup' => 1, 'admin_val' => 1 ),
								 				  	 'where'	=> array( 'id', '=', $m['id'] ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			#=============================
			# Update Old Group
			#=============================

			$this->ifthd->core->db->next_no_quotes('set');

			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'groups',
												  	 'set'		=> array( 'g_members' => 'g_members-1' ),
								 				  	 'where'	=> array( 'g_id', '=', 3 )
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
								 		  	  ) 	);

			$this->ifthd->core->db->execute();
		}

		$this->ifthd->log( 'admin', "Member Approved &#039;". $m['name'] ."&#039;", 1, $m['id'] );

		#=============================
		# Send Email
		#=============================

		$replace = ""; // Initialize for Security

		if ( ! $m['email_val'] )
		{
			$replace['USER_NAME'] = $m['name'];

			$this->ifthd->send_email( $m['id'], 'acc_almost_approved', $replace );
		}
		else
		{
			$replace['VAL_LINK'] = $m['name'];

			$this->ifthd->send_email( $m['id'], 'acc_approved', $replace );
		}

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=member&code=mod', 'approve_user_success' );
		$this->list_members( '', 'The member has been successfully approved.' );
	}

	#=======================================
	# @ Do Delete
	# Delete a member.
	#=======================================

	function do_delete()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_member_delete'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'members',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_member');
		}

		$m = $this->ifthd->core->db->fetch_row();

		if ( $m['id'] == 1 )
		{
			$this->ifthd->skin->error('no_perm');
		}

		#=============================
		# DELETE *MwhaAaAaAaAa*
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'delete'	=> 'members',
							 				  	 'where'	=> array( 'id', '=', $m['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'admin', "Member Deleted &#039;". $m['name'] ."&#039;", 2, $m['id'] );

		#=============================
		# Update Stats / Cache
		#=============================

		$this->ifthd->r_member_stats(1);
		
		$this->ifthd->rebuild_staff_cache();

		#=============================
		# DELETE Tickets
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'delete'	=> 'tickets',
							 				  	 'where'	=> array( 'mid', '=', $m['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->core->db->construct( array(
											  	 'delete'	=> 'replies',
							 				  	 'where'	=> array( 'mid', '=', $m['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		#=============================
		# Update Old Group
		#=============================

		$this->ifthd->core->db->next_no_quotes('set');

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'groups',
											  	 'set'		=> array( 'g_members' => 'g_members-1' ),
							 				  	 'where'	=> array( 'g_id', '=', $m['mgroup'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=member', 'delete_user_success' );
		$this->list_members( 'The member has been successfully deleted.' );
	}

	#=======================================
	# @ Get Stars
	# Generates HTML for star rating.
	#=======================================

	function get_stars($rating)
	{
		$half = 0; // Initialize for Security

		if ( strstr( $rating, '.' ) )
		{
			$half = 1;
		}

		$real_rating = $rating;

		$rating = round( $rating );

		for ( $x = 1; $x < $rating + 1; $x++ )
	    {
	    	if ( $half && $x == $rating )
	    	{
		    	$ift_html .= "<img src='<! IMG_DIR !>/star_half.gif' alt='X' />";
	    	}
	    	else
	    	{
		    	$ift_html .= "<img src='<! IMG_DIR !>/star_on.gif' alt='>' />";
	    	}
	    }

	    for ( $x = $x; $x <= 5; $x++ )
	    {
	    	$ift_html .= "<img src='<! IMG_DIR !>/star_off.gif' alt='-' />";
	    }

		return $ift_html;
	}

}

?>