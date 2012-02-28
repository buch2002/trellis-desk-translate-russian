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
|    | Admin Tickets
#======================================================
*/

class ad_tickets {

	#=======================================
	# @ Auto Run
	# Function that is run automatically
	# when the file is required.
	#=======================================

	function auto_run()
	{
		if ( ! $this->ifthd->member['acp']['manage_ticket'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->skin->set_section( 'Управление тикетами' );
		$this->ifthd->skin->set_description( 'Управление тикетами, отделами, настраиваемые поля отдела и шаблоны ответов.' );

		if ( $this->ifthd->input['act'] == 'reply' )
		{
			switch( $this->ifthd->input['code'] )
	    	{
	    		case 'edit':
					$this->edit_reply();
	    		break;

	    		case 'doedit':
	    			$this->do_reply_edit();
	    		break;
	    		case 'dodelete':
	    			$this->do_reply_delete();
	    		break;

	    		default:
	    			$this->list_tickets();
	    		break;
			}
		}
		else
		{
			switch( $this->ifthd->input['code'] )
	    	{
	    		case 'list':
					$this->list_tickets();
	    		break;
	    		case 'view':
					$this->view_ticket();
	    		break;
	    		case 'add':
	    			$this->add_ticket();
	    		break;
	    		case 'edit':
	    			$this->edit_ticket();
	    		break;
	    		case 'assign':
					$this->assign_ticket();
	    		break;
	    		case 'attachment':
					$this->download_attachment();
	    		break;

	    		case 'close':
	    			$this->ticket_action('close');
	    		break;
	    		case 'hold':
	    			$this->ticket_action('hold');
	    		break;
	    		case 'escalate':
	    			$this->ticket_action('escalate');
	    		break;
	    		case 'delete':
	    			$this->ticket_action('delete');
	    		break;
	    		case 'reopen':
	    			$this->ticket_action('reopen');
	    		break;
	    		case 'move':
	    			$this->ticket_action('move');
	    		break;
	    		case 'reply':
	    			$this->submit_reply();
	    		break;
	    		case 'notes':
	    			$this->notes_save();
	    		break;
	    		case 'multi':
	    			$this->do_multi();
	    		break;
	    		case 'doedit':
	    			$this->do_edit_ticket();
	    		break;

	    		default:
	    			$this->list_tickets();
	    		break;
			}
		}
	}

	#=======================================
	# @ Generate Link
	# Generates link for list tickets.
	#=======================================

	function generate_link( $key='', $value='', $append='' )
	{
		$link = '<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=list';

		if ( $this->ifthd->input['status'] ) $status = $this->ifthd->input['status'];
		if ( $this->ifthd->input['depart'] ) $depart = $this->ifthd->input['depart'];
		if ( $this->ifthd->input['sort'] ) $sort = $this->ifthd->input['sort'];
		if ( $this->ifthd->input['order'] ) $order = $this->ifthd->input['order'];
		if ( $this->ifthd->input['mid'] ) $mid = $this->ifthd->input['mid'];
		if ( $this->ifthd->input['search'] ) $search = $this->ifthd->input['search'];
		if ( $this->ifthd->input['field'] ) $field = $this->ifthd->input['field'];
		if ( $this->ifthd->input['my'] ) $my = $this->ifthd->input['my'];

		if( $key ) $$key = $value;

		if ( $status ) $link .= '&amp;status='. $status;
		if ( $depart ) $link .= '&amp;depart='. $depart;
		if ( $sort ) $link .= '&amp;sort='. $sort;
		if ( $order ) $link .= '&amp;order='. $order;
		if ( $mid ) $link .= '&amp;mid='. $mid;
		if ( $search ) $link .= '&amp;search='. $search;
		if ( $field ) $link .= '&amp;field='. $field;
		if ( $my ) $link .= '&amp;my='. $my;

		if ( $append ) $link .= $append;

		return $link;
	}

	#=======================================
	# @ List Tickets
	# Show a list of tickets. :)
	#=======================================

	function list_tickets($error='')
	{
		#=============================
		# Filter Options
		#=============================

		$sql_where = array();
		$filters = array();

		if ( $this->ifthd->input['status'] && $this->ifthd->input['status'] != 'all' && $this->ifthd->input['status'] != 'nclosed' )
		{
			$filters[] = array( 'status', '=', $this->ifthd->input['status'] );
		}
		elseif( ! $this->ifthd->input['status'] || $this->ifthd->input['status'] == 'nclosed' )
		{
			#$filters[] = array( 'status', '!=', 4 );
			#$filters[] = array( 'status', '!=', 6, 'and' );
			$filters[] = array( 'status', '!=', 6 );
		}

		if ( $this->ifthd->input['depart'] )
		{
			$filters[] = array( 'did', '=', $this->ifthd->input['depart'] );
		}

		if ( $this->ifthd->input['mid'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'id', 'name' ),
												  	 'from'		=> 'members',
								 				  	 'where'	=> array( 'id', '=', intval( $this->ifthd->input['mid'] ) ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			if ( ! $this->ifthd->core->db->get_num_rows() )
			{
				$this->ifthd->skin->error('no_member');
			}

			$midm = $this->ifthd->core->db->fetch_row();
			
			$filters[] = array( 'mid', '=', $this->ifthd->input['mid'] );
			
			$mid_box = "<div class='option1'>Showing Tickets Submitted By: <a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=view&amp;id=". $midm['id'] ."'>". $midm['name'] ."</a> | <a href='". $this->generate_link( 'mid', 0 ) ."'>Show Tickets From All Members</a></div>";
		}

		if ( $this->ifthd->input['search'] )
		{
			if ( ! $this->ifthd->input['field'] ) $this->ifthd->input['field'] = 'subject';
			
			$filters[] = array( $this->ifthd->input['field'], 'like', $this->ifthd->input['search'] );
		}

		if ( $this->ifthd->input['my'] )
		{
			$filters[] = array( 'amid', '=', $this->ifthd->member['id'] );
		}

		#=============================
		# Department Security
		#=============================

		if ( is_array( unserialize( $this->ifthd->member['g_depart_perm'] ) ) )
		{
			$rev_perms = array(); // Initialize for Security

			foreach( unserialize( $this->ifthd->member['g_depart_perm'] ) as $did => $access )
			{
				if ( $access == 1 ) $rev_perms[] = $did;
			}

			$filters[] = array( 'did', 'in', $rev_perms );
		}

		#=============================
		# Sorting Options
		#=============================

		if ( $this->ifthd->input['sort'] )
		{
			$sort = $this->ifthd->input['sort'];
		}
		else
		{
			$sort = 'date';
		}

		$order_var = "order_". $sort;
		$img_var = "img_". $sort;

		if ( $this->ifthd->input['order'] )
		{
			$order = strtoupper( $this->ifthd->input['order'] );
		}
		elseif ( $sort == 'date' )
		{
			$order = 'DESC';
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

		( $sort == 'id' ) ? ( $order == 'DESC' ) ? $raw_link_id = $this->generate_link( 'order', 'asc' ) : $raw_link_id = $this->generate_link( 'order', 'desc' ) : $raw_link_id = $this->generate_link( 'sort', 'id' );
		( $sort == 'subject' ) ? ( $order == 'DESC' ) ? $raw_link_subject = $this->generate_link( 'order', 'asc' ) : $raw_link_subject = $this->generate_link( 'order', 'desc' ) : $raw_link_subject = $this->generate_link( 'sort', 'subject' );
		( $sort == 'priority' ) ? ( $order == 'DESC' ) ? $raw_link_priority = $this->generate_link( 'order', 'asc' ) : $raw_link_priority = $this->generate_link( 'order', 'desc' ) : $raw_link_priority = $this->generate_link( 'sort', 'priority' );
		( $sort == 'dname' ) ? ( $order == 'DESC' ) ? $raw_link_dname = $this->generate_link( 'order', 'asc' ) : $raw_link_dname = $this->generate_link( 'order', 'desc' ) : $raw_link_dname = $this->generate_link( 'sort', 'dname' );
		( $sort == 'date' ) ? ( $order == 'DESC' ) ? $raw_link_date = $this->generate_link( 'order', 'asc' ) : $raw_link_date = $this->generate_link( 'order', 'desc' ) : $raw_link_date = $this->generate_link( 'sort', 'date' );
		( $sort == 'status' ) ? ( $order == 'DESC' ) ? $raw_link_status = $this->generate_link( 'order', 'asc' ) : $raw_link_status = $this->generate_link( 'order', 'desc' ) : $raw_link_status = $this->generate_link( 'sort', 'status' );

		$link_id = "<a href='". $raw_link_id ."'>ID". $img_id ."</a>";
		$link_subject = "<a href='". $raw_link_subject ."'>Subject". $img_subject ."</a>";
		$link_priority = "<a href='". $raw_link_priority ."'>Priority". $img_priority ."</a>";
		$link_dname = "<a href='". $raw_link_dname ."'>Department". $img_dname ."</a>";
		$link_date = "<a href='". $raw_link_date ."'>Submitted". $img_date ."</a>";
		$link_status = "<a href='". $raw_link_status ."'>Status". $img_status ."</a>";

		#=============================
		# Grab Tickets
		#=============================

		// Combine Filters
		$filter_count = 0;

		while( list( , $fdata ) = each( $filters ) )
		{
			if ( $filter_count )
			{
				$fdata[] = 'and';
			}

			$sql_where[] = $fdata;

			$filter_count ++;
		}

		if ( $this->ifthd->input['st'] )
		{
			$start = $this->ifthd->input['st'];
		}
		else
		{
			$start = 0;
		}

		$db_array = array(
					  	 'select'	=> array( 'id' ),
					  	 'from'		=> 'tickets',
					  	 'order'	=> array( $sort => $order ),
				  	  );

		if ( $sql_where )
		{
			$db_array['where'] = $sql_where;
		}

		$this->ifthd->core->db->construct( $db_array );

		$this->ifthd->core->db->execute();

		$ticket_count = $this->ifthd->core->db->get_num_rows();

		$db_array = array(
					  	 'select'	=> 'all',
					  	 'from'		=> 'tickets',
					  	 'order'	=> array( $sort => $order ),
					  	 'limit'	=> array( $start, 20 ),
				  	  );

		if ( $sql_where )
		{
			$db_array['where'] = $sql_where;
		}

		$this->ifthd->core->db->construct( $db_array );

		$this->ifthd->core->db->execute();

		$ticket_rows = ""; // Initialize for Security
		$row_count = 0; // Initialize for Security

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			$tickets = array(); // Initialize for Security

			while( $t = $this->ifthd->core->db->fetch_row() )
			{
				$row_count ++;

				#=============================
				# Color Code
				#=============================

				if ( $t['priority'] == 1 ) $p_color = 'blue';
				if ( $t['priority'] == 2 ) $p_color = 'yellow';
				if ( $t['priority'] == 3 ) $p_color = 'orange';
				if ( $t['priority'] == 4 ) $p_color = 'red';

				if ( $t['amid'] == $this->ifthd->member['id'] ) $p_color .= '_dot';

				$t['p_img'] = "<img src='<! IMG_DIR !>/sq_". $p_color .".gif' class='pip' alt='priority' />&nbsp;&nbsp;";

				( $row_count & 1 ) ? $row_class = 'option1-mini' : $row_class = 'option2-mini';

				#=============================
				# Fix Up Information
				#=============================

				$t['priority'] = $this->ifthd->get_priority( $t['priority'] );

				$t['date'] = $this->ifthd->ift_date( $t['date'], "n/j/y g:i A" );

				$t['status'] = $this->ifthd->get_status( $t['status'], 1 );

				$ticket_rows .= "<tr>
									<td class='{$row_class}'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=view&amp;id={$t['id']}'>{$t['id']}</a></td>
									<td class='{$row_class}'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=view&amp;id={$t['id']}'>{$t['subject']}</a></td>
									<td class='{$row_class}'>{$t['p_img']}{$t['priority']}</td>
									<td class='{$row_class}' style='font-weight: normal'>{$t['dname']}</td>
									<td class='{$row_class}' style='font-weight: normal'>{$t['date']}</td>
									<td class='{$row_class}'>{$t['status']}</td>
									<td class='{$row_class}' align='center'><input type='checkbox' name='tcb_{$t['id']}' id='tcb_{$t['id']}' value='1' /></td>
								</tr>";

				$tickets[] = $t['id'];
			}
		}
		else
		{
			$ticket_rows .= "<tr>
								<td class='option1' colspan='7' align='center'>There are no tickets to display.</td>
							</tr>";
		}

		#=============================
		# Do Output
		#=============================

		if ( $error )
		{
			$error = "<div class='critical'>{$error}</div>";
		}

		$depart_links = ""; // Initialize for Security

		if ( is_array( unserialize( $this->ifthd->member['g_depart_perm'] ) ) )
		{
			$g_depart_perms = unserialize( $this->ifthd->member['g_depart_perm'] );
		}

		while ( list( $id, $d ) = each( $this->ifthd->core->cache['depart'] ) )
    	{
    		if ( $g_depart_perms && $g_depart_perms[ $id ] )
    		{
    			$depart_links .= "<a href='". $this->generate_link( 'depart', $id ) ."'>{$d['name']}</a> | ";
    		}
    		elseif ( ! $g_depart_perms )
    		{
    			$depart_links .= "<a href='". $this->generate_link( 'depart', $id ) ."'>{$d['name']}</a> | ";
    		}
    	}

    	$depart_links .= "<a href='". $this->generate_link( 'depart', 0 ) ."'>All</a>";

		$page_links = $this->ifthd->page_links( substr( $this->generate_link(), strpos( $this->generate_link(), '?' ) ), $ticket_count, 20, $start, 1 );

		( $this->ifthd->input['my'] ) ? $my_link = $this->generate_link( 'my', 0 ) : $my_link = $this->generate_link( 'my', 1 );
		( $this->ifthd->input['my'] ) ? $my_link_text = 'All Tickets' : $my_link_text = 'My Assigned Tickets';

		if ( ! $this->ifthd->sanitize_data( $_COOKIE['hdsh_filterbox'] ) ) $filterbox_hide = " style='display:none'";

		$this->output = "{$error}
						<div class='groupbox'><div style='float: right'><a onclick=\"javascript:Effect.toggle('filterbox','blind',{duration: 0.5});set_hide('filterbox');\" class='fake_link' title='Filter ticket display'><img src='<! IMG_DIR !>/button_mini_filters.gif' alt='Filters' /></a></div>Ticket List</div>
						<div id='filterbox'{$filterbox_hide}>
						<div class='option1' style='font-weight: normal'>
							<strong>Ticket Status:</strong> <a href='". $this->generate_link( 'status', 1 ) ."'>Open</a> | <a href='". $this->generate_link( 'status', 2 ) ."'>In Progress</a> | <a href='". $this->generate_link( 'status', 3 ) ."'>On Hold</a> | <a href='". $this->generate_link( 'status', 4 ) ."'>ACA</a> | <a href='". $this->generate_link( 'status', 5 ) ."'>Escalated</a> | <a href='". $this->generate_link( 'status', 6 ) ."'>Closed</a> | <a href='". $this->generate_link( 'status', 'nclosed' ) ."'>All But Closed</a> | <a href='". $this->generate_link( 'status', 'all' ) ."'>All</a><br />
							<strong>Department:</strong> {$depart_links}
							<form action='". $this->generate_link() ."' name='tsearch' method='post'>
							<div style='padding-top:8px'><strong>Search:</strong> <input type='text' name='search' id='search' value='{$this->ifthd->input['search']}' size='25' /> ". $this->build_search_drop( $this->ifthd->input['field'] ) ." <input type='submit' name='go' id='search_go' value='Search' /></div>
							</form>
						</div>
						</div>
						{$mid_box}
						<form action='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=multi&amp;start={$start}' name='tlist' method='post'>
						<input type='hidden' name='ticket_ids' value='". serialize( $tickets ) ."' />
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<th width='4%' align='left'>{$link_id}</th>
							<th width='31%' align='left'>{$link_subject}</th>
							<th width='13%' align='left'>{$link_priority}</th>
							<th width='19%' align='left'>{$link_dname}</th>
							<th width='20%' align='left'>{$link_date}</th>
							<th width='10%' align='left'>{$link_status}</th>
							<th width='3%' style='padding-left:8px'><input type='checkbox' name='check_all' value='Check All' onclick='tdcheck_all(this.form);' /></th>
						</tr>
						". $ticket_rows ."
						</table>

						<div class='formtail'>
							<div style='float: left;padding-top: 3px'><a href='{$my_link}'>{$my_link_text}</a> &nbsp;|&nbsp; <a href='<! HD_URL !>/index.php?act=feed&amp;code=stickets&amp;id=". $this->ifthd->member['id'] ."&amp;key=". $this->ifthd->member['rss_key'] ."'>RSS Feed</a> &nbsp;|&nbsp; <a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=sig&amp;id=". $this->ifthd->member['id'] ."'>Edit My Signature</a></div>
							<div align='right'>
								<span style='font-size: 11px; vertical-align: middle'>With Selected:</span>
								<select name='multi_action' id='multi_action' style='vertical-align:bottom'>
									<option value='hold'>Hold</option>
									<option value='move'>Move</option>
									<option value='close'>Close</option>
									<option value='delete'>Delete</option>
									<option value='reopen'>Reopen</option>
								</select>
								<input type='submit' name='submit' id='go' value='Go' />
							</div>
						</div>
						</form>
						<br />{$page_links}";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets'>Tickets</a>",
						   "List Tickets",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Tickets' ) );
	}

	#=======================================
	# @ View Ticket
	# Simply shows a ticket. :)
	#=======================================

	function view_ticket($error='', $top_error='', $top_alert='', $notes='', $nreply_id=0)
	{
		#=============================
		# Security Checks
		#=============================

		$attached = 0; // Initialize for Security

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( is_array( unserialize( $this->ifthd->member['g_depart_perm'] ) ) )
		{
			$rev_perms = array(); // Initialize for Security

			foreach( unserialize( $this->ifthd->member['g_depart_perm'] ) as $did => $access )
			{
				if ( $access == 1 ) $rev_perms[] = $did;
			}

			$sql_where = array( array( array( 't' => 'id' ), '=', $this->ifthd->input['id'] ), array( array( 't' => 'did' ), 'in', $rev_perms, 'and' ) );
		}
		else
		{
			$sql_where = array( array( 't' => 'id' ), '=', $this->ifthd->input['id'] );
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 't' => 'all',
												  	 				  'm' => array( 'mgroup', 'cpfields' ),
												  	 				  'a' => array( 'original_name', 'size' ),
												  	 				 ),
											  	 'from'		=> array( 't' => 'tickets' ),
											  	 'join'		=> array( array( 'from' => array( 'm' => 'members' ), 'where' => array( 't' => 'mid', '=', 'm' => 'id' ) ), array( 'from' => array( 'a' => 'attachments' ), 'where' => array( 't' => 'attach_id', '=', 'a' => 'id' ) ) ),
							 				  	 'where'	=> $sql_where,
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_ticket');
		}

		$t = $this->ifthd->core->db->fetch_row();

		#=============================
		# Custom Department Fields
		#=============================

		$row_count = 0; // Initialize for Security

		if ( is_array( $this->ifthd->core->cache['dfields'] ) )
		{
			$cdfields = ""; // Initialize for Security

			$cdfdata = unserialize( $t['cdfields'] );

			// Count
			foreach( $this->ifthd->core->cache['dfields'] as $id => $f )
			{
				$f_perm = unserialize( $f['departs'] );

				if ( $f_perm[ $t['did'] ] )
				{
					$f_count ++;
					$df_count ++;
				}
			}

			// Count
			foreach( $this->ifthd->core->cache['pfields'] as $id => $f )
			{
				$f_perm = unserialize( $f['perms'] );

				if ( $f_perm[ $t['mgroup'] ] )
				{
					$f_count ++;
					$pf_count ++;
				}
			}

			foreach( $this->ifthd->core->cache['dfields'] as $id => $f )
			{
				$f_perm = unserialize( $f['departs'] );

				if ( $f_perm[ $t['did'] ] )
				{
					if ( $f['type'] == 'dropdown' || $f['type'] == 'radio' )
					{
						$options = explode( "\r\n", $f['extra'] );

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
								$f['value'] = 'Yes';
							}
							else
							{
								$f['value'] = 'No';
							}
						}
						else
						{
							if ( ! $f['value'] ) $f['value'] = '---';
						}
					}

					$df_my_count ++;

					if ( $df_my_count & 1 )
					{
						$row_count ++;

						( $row_count & 1 ) ? $row_class = '1-med' : $row_class = '2-med';

						if ( $df_my_count == $df_count && ! $pf_count )
						{
							$field_rows .= "<tr>
												<td class='option{$row_class}'>{$f['name']}</td>
												<td class='row{$row_class}' colspan='3'>{$f['value']}</td>
											</tr>";
						}
						else
						{
							$field_rows .= "<tr>
												<td class='option{$row_class}'>{$f['name']}</td>
												<td class='row{$row_class}'>{$f['value']}</td>";
						}
					}
					else
					{
						$field_rows .= "<td class='option{$row_class}'>{$f['name']}</td>
										<td class='row{$row_class}'>{$f['value']}</td>
									</tr>";
					}

					$soggy = ""; // Reset
				}
			}
		}

		#=============================
		# Custom Profile Fields
		#=============================

		if ( is_array( $this->ifthd->core->cache['dfields'] ) )
		{
			$cpfields = ""; // Initialize for Security

			$cpfdata = unserialize( $t['cpfields'] );

			foreach( $this->ifthd->core->cache['pfields'] as $id => $f )
			{
				$f_perm = unserialize( $f['perms'] );

				if ( $f_perm[ $t['mgroup'] ] && $f['ticket'] )
				{
					if ( $f['type'] == 'dropdown' || $f['type'] == 'radio' )
					{
						$options = explode( "\r\n", $f['extra'] );

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
								$f['value'] = 'Yes';
							}
							else
							{
								$f['value'] = 'No';
							}
						}
						else
						{
							if ( ! $f['value'] ) $f['value'] = '---';
						}
					}

					$pf_my_count ++;

					if ( $pf_my_count & 1 && ( ! ( $df_my_count & 1 ) || ! $df_my_count ) )
					{
						$row_count ++;

						( $row_count & 1 ) ? $row_class = '1-med' : $row_class = '2-med';

						if ( $pf_my_count == $f_count )
						{
							$field_rows .= "<tr>
												<td class='option{$row_class}'>{$f['name']}</td>
												<td class='row{$row_class}' colspan='3'>{$f['value']}</td>
											</tr>";
						}
						else
						{
							$field_rows .= "<tr>
												<td class='option{$row_class}'>{$f['name']}</td>
												<td class='row{$row_class}'>{$f['value']}</td>";
						}
					}
					else
					{
						$field_rows .= "<td class='option{$row_class}'>{$f['name']}</td>
										<td class='row{$row_class}'>{$f['value']}</td>
									</tr>";

						if ( $df_my_count & 1 )
						{
							$pf_my_count --;
							$df_my_count = 0;
						}
					}

					$soggy = ""; // Reset
				}
			}
		}

		#=============================
		# Fix Up Information
		#=============================

		$t['links'] = "<a onclick=\"javascript:Effect.toggle('notesbox','blind',{duration: 0.5});\" class='fake_link' title='Expand/collapse notes'><img src='<! IMG_DIR !>/button_mini_notes.gif' alt='Notes' /></a> "; // Initialize for Security

		if ( $this->ifthd->member['acp']['manage_ticket_escalate'] && $this->ifthd->core->cache['depart'][ $t['did'] ]['can_escalate'] && $t['status'] != 6 )
		{
			$t['links'] .= "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=escalate&amp;id=". $t['id'] ."' onclick='return sure_escalate()' title='Escalate this ticket'><img src='<! IMG_DIR !>/button_mini_escalate.gif' alt='Escalate' /></a> ";
		}

		if ( $t['status'] != 3 && $t['status'] != 6 )
		{
			$t['links'] .= "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=hold&amp;id=". $t['id'] ."' title='Put this ticket on hold'><img src='<! IMG_DIR !>/button_mini_hold.gif' alt='Hold' /></a> ";
		}

		$t['links'] .= "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=move&amp;id=". $t['id'] ."' title='Move this ticket'><img src='<! IMG_DIR !>/button_mini_move.gif' alt='Move' /></a> ";

		if ( $t['status'] == 6 )
		{
			$t['links'] .= "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=reopen&amp;id=". $t['id'] ."' title='Reopen this ticket'><img src='<! IMG_DIR !>/button_mini_reopen.gif' alt='Reopen' /></a> ";
		}
		else
		{
			$t['links'] .= "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=close&amp;id=". $t['id'] ."' onclick='return sure_close()' title='Close this ticket'><img src='<! IMG_DIR !>/button_mini_close.gif' alt='Close' /></a> ";
		}

		$t['links'] .= "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=delete&amp;id=". $t['id'] ."' onclick='return sure_delete()' title='Delete this ticket'><img src='<! IMG_DIR !>/button_mini_delete.gif' alt='Delete' /></a>";

		$t['date'] = $this->ifthd->ift_date( $t['date'] );
		$t['last_reply'] = $this->ifthd->ift_date( $t['last_reply'] );

		$t['message'] = $this->ifthd->prepare_output( $t['message'], 0, 0, 1 );

		if ( $t['priority'] == 1 ) $p_color = 'blue';
		if ( $t['priority'] == 2 ) $p_color = 'yellow';
		if ( $t['priority'] == 3 ) $p_color = 'orange';
		if ( $t['priority'] == 4 ) $p_color = 'red';

		if ( $t['amid'] == $this->ifthd->member['id'] ) $p_color .= '_dot';

		$t['p_img'] = "<img src='<! IMG_DIR !>/sq_". $p_color .".gif' class='pip' alt='priority' />&nbsp;&nbsp;";

		$t['priority'] = $this->ifthd->get_priority( $t['priority'] );

		if ( $t['guest'] )
		{
			$t['mname_link'] = "{$t['mname']} (Guest)";
		}
		else
		{
			$t['mname_link'] = "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=view&amp;id={$t['mid']}'>{$t['mname']}</a>";
		}

		if ( $notes )
		{
			$t['notes'] = $notes;

			$notes_display = '';
			$notes_msg = "<div class='alert'>The notes have been successfully updated.</div>";
		}
		elseif ( $t['notes'] )
		{
			$notes_display = '';
		}
		else
		{
			$notes_display = " style='display:none'";
		}

		if ( $t['rating'] )
		{
			$t['human_rating'] = $t['rating'];
		}
		else
		{
			$t['human_rating'] = 'N/A';
		}

		$t['rating'] = $this->get_stars( $t['rating'] );

		$t['assigned'] = ""; // Initialize for Security

		if ( $this->ifthd->member['acp']['manage_ticket_assign_self'] || $this->ifthd->member['acp']['manage_ticket_assign_any'] )
		{
			$can_assign = 1;
		}

		if ( $this->ifthd->member['acp']['manage_ticket_assign_any'] )
		{
			$t['assigned'] = "<span onclick=\"show_hide('assign_drop', 1)\" style='color:#175174;cursor:pointer'>";
		}
		else
		{
			$t['assigned'] = "<span style='color:#175174;'>";
		}

		if ( $t['amid'] )
		{
			$t['assigned'] .= $t['amname'] ." (". $this->ifthd->core->cache['staff'][ $t['amid'] ]['assigned'] .")";
		}
		else
		{
			$t['assigned'] .= "<b>Not Assigned</b>";
		}

		if ( $this->ifthd->member['acp']['manage_ticket_assign_any'] )
		{
			$t['assigned'] .= " <img src='<! IMG_DIR !>/arrow_down.gif' alt='Down' style='margin-bottom:1px' /></span>";
		}
		elseif ( $this->ifthd->member['acp']['manage_ticket_assign_self'] && $t['amid'] != $this->ifthd->member['id'] )
		{
			if ( is_array( $this->ifthd->member['g_depart_perm'] ) )
			{
				if ( $this->ifthd->member['g_depart_perm'][ $t['did'] ] )
				{
					$t['assigned'] .= " - </span><a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=assign&amp;tid=". $t['id']. "&amp;mid=". $this->ifthd->member['id'] ."' style='color:#175174;cursor:pointer'><b>Assign to Self</b></a>";
				}
			}
			else
			{
				$t['assigned'] .= " - </span><a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=assign&amp;tid=". $t['id']. "&amp;mid=". $this->ifthd->member['id'] ."' style='color:#175174;cursor:pointer'><b>Assign to Self</b></a>";
			}
		}
		else
		{
			$t['assigned'] .= "</span>";
		}

		$assign_drop = ""; // Initialize for Security

		if ( $can_assign )
		{
			while ( list( , $staff) = each( $this->ifthd->core->cache['staff'] ) )
			{
				if ( is_array( unserialize( $staff['g_depart_perm'] ) ) )
				{
					$staff_perms = unserialize( $staff['g_depart_perm'] );

					if ( $staff_perms[ $t['did'] ] )
					{
						if ( $t['amid'] != $staff['id'] ) $assign_drop .= "<div><a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=assign&amp;tid=". $t['id']. "&amp;mid=". $staff['id'] ."'>". $staff['name'] ." (". $staff['assigned'] .")</a></div>";
					}
				}
				else
				{
					if ( $t['amid'] != $staff['id'] ) $assign_drop .= "<div><a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=assign&amp;tid=". $t['id']. "&amp;mid=". $staff['id'] ."'>". $staff['name'] ." (". $staff['assigned'] .")</a></div>";
				}
			}

			if ( $t['amid'] ) $assign_drop .= "<div><a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=assign&amp;tid=". $t['id']. "&amp;mid=0'>Unassign</a></div>";
		}

		#=============================
		# Grab Ratings?
		#=============================

		if ( $this->ifthd->core->cache['config']['allow_reply_rating'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> 'all',
												  	 'from'		=> 'reply_rate',
								 				  	 'where'	=> array( 'tid', '=', $this->ifthd->input['id'] ),
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

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'r' => 'all', 'a' => array( 'original_name', 'size' ) ),
											  	 'from'		=> array( 'r' => 'replies' ),
											  	 'join'		=> array( array( 'from' => array( 'a' => 'attachments' ), 'where' => array( 'r' => 'attach_id', '=', 'a' => 'id' ) ) ),
							 				  	 'where'	=> array( array( 'r' => 'tid' ), '=', $t['id'] ),
							 				  	 'order'	=> array( 'date' => array( 'r' => 'asc' ) ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$replies = ""; // Initialize for Security
		$row_count = 0; // Initialize for Security

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			while( $r = $this->ifthd->core->db->fetch_row() )
			{
				$row_count ++;

				( $row_count & 1 ) ? $reply_class = 'row1' : $reply_class = 'row2';

				#=============================
				# Fix Up Information
				#=============================

				$r['time_ago'] = $this->ifthd->ift_date( $r['date'], '', 3 );

				$r['date'] = $this->ifthd->ift_date( $r['date'] );

				if ( $r['guest'] ) $r['mname'] .= ' ('. $this->ifthd->lang['guest'] .')';

				if ( $r['staff'] )
				{
					$r['message'] = $this->ifthd->remove_dbl_spaces( $this->ifthd->prepare_output( $r['message'], 1, 1, 1 ) );

					if ( $r['secret'] )
					{
						$reply_strip = 'subboxsecret';
						$r['r_img'] = "<img src='<! IMG_DIR !>/sq_grey.gif' class='pip' alt='reply' />&nbsp;&nbsp;";
					}
					else
					{
						$reply_strip = 'subboxstaff';
						$r['r_img'] = "<img src='<! IMG_DIR !>/sq_red.gif' class='pip' alt='reply' />&nbsp;&nbsp;";
					}
				}
				else
				{
					$r['message'] = $this->ifthd->prepare_output( $r['message'], 0, 0, 1 );
					$reply_strip = 'subbox';
					$r['r_img'] = "<img src='<! IMG_DIR !>/sq_blue.gif' class='pip' alt='reply' />&nbsp;&nbsp;";
				}

				$r['mod_links'] = "&nbsp;<a href='<! HD_URL !>/admin.php?section=manage&amp;act=reply&amp;code=edit&amp;id={$r['id']}' title='Edit this reply'><img src='<! IMG_DIR !>/page_edit.png' style='vertical-align: middle' alt='Edit this reply' /></a>&nbsp;&nbsp;<a href='<! HD_URL !>/admin.php?section=manage&amp;act=reply&amp;code=dodelete&amp;id={$r['id']}' onclick='return sure_delete_reply()' title='Delete this reply'><img src='<! IMG_DIR !>/page_delete.png' style='vertical-align: middle' alt='Delete this reply' /></a>&nbsp;";

				if ( $this->ifthd->core->cache['config']['allow_reply_rating'] && $this->ifthd->member['g_reply_rate'] && $r['staff'] )
				{
					if ( $ratings[ $r['id'] ] == 1 )
					{
						$r['rate_imgs'] = "&nbsp;&nbsp;<span class='response_imgs'><img src='<! IMG_DIR !>/emoticon_unhappy.png' alt='Thumbs down' style='vertical-align: middle' />&nbsp;&nbsp;{$r['mod_links']}</span>";
					}
					elseif ( $ratings[ $r['id'] ] == 5 )
					{
						$r['rate_imgs'] = "&nbsp;&nbsp;<span class='response_imgs'><img src='<! IMG_DIR !>/emoticon_smile.png' alt='Thumbs up' style='vertical-align: middle' />&nbsp;&nbsp;{$r['mod_links']}</span>";
					}
					else
					{
						$r['rate_imgs'] = "&nbsp;&nbsp;<span class='response_imgs'>{$r['mod_links']}</span>";
					}
				}
				else
				{
					$r['rate_imgs'] = "&nbsp;&nbsp;<span class='response_imgs'>{$r['mod_links']}</span>";
				}

				if ( $nreply_id == $r['id'] )
				{
					$replies .= "<a name='newreply'></a>";
				}
				else
				{
					$replies .= "<a name='reply{$r['id']}'></a>";
				}

				$replies .= "<div class='{$reply_strip}'><div style='float: right; font-size: 11px; vertical-align: middle'>{$r['time_ago']} {$r['rate_imgs']}</div>{$r['mname']} -- {$r['date']}</div>
							<div class='{$reply_class}'>
								{$r['message']}
							</div>";

				if ( $r['attach_id'] )
				{
					$attached ++;

					$replies .= "<div class='infopop'><a onclick=\"javascript:Effect.toggle('info{$attached}','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Attachment</a><div id='info{$attached}' style='display: none;'><div>Download attachment: <a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=attachment&amp;id=". $r['attach_id'] ."'>". $r['original_name'] ."</a> (". $this->ifthd->format_size( $r['size'] ) .")</div></div></div>";
				}
			}
		}
		else
		{
			$replies = "<div class='option2-mini'>No replies have been made to this ticket.</div>";
		}

		#=============================
		# Update Ticket
		#=============================

		if ( $t['status'] == 1 )
		{
			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'tickets',
												  	 'set'		=> array( 'status' => 2 ),
								 				  	 'where'	=> array( 'id', '=', $t['id'] ),
								 		  	  ) 	);

			$this->ifthd->core->db->next_shutdown();
			$this->ifthd->core->db->execute();
		}

		#=============================
		# Grab Logs
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'logs',
											  	 'where'	=> array( array( 'type', '=', 7 ), array( 'extra', '=', $t['id'], 'and' ) ),
											  	 'order'	=> array( 'date' => 'desc' ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$log_rows = ""; // Initialize for Security
		$row_count = 0; // Initialize for Security

		while( $l = $this->ifthd->core->db->fetch_row() )
		{
			$row_count ++;

			( $row_count & 1 ) ? $row_class = 'option1-mini' : $row_class = 'option2-mini';

			$l['date'] = $this->ifthd->ift_date( $l['date'], "n/j/y g:i A" );

			if ( $l['level'] == 2 )
			{
				$l['action'] = "<font color='#790000'>". $l['action'] ."</font>";
				$l['date'] = "<font color='#790000'>". $l['date'] ."</font>";
				$l['mname'] = "<font color='#790000'>". $l['mname'] ."</font>";
				$l['ipadd'] = "<font color='#790000'>". $l['ipadd'] ."</font>";
			}

			$log_rows .= "<tr>
							<td width='45%' class='{$row_class}'>{$l['action']}</td>
							<td width='21%' class='{$row_class}'>{$l['date']}</td>
							<td width='17%' class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=view&amp;id={$l['mid']}'>{$l['mname']}</a></td>
							<td width='17%' class='{$row_class}' align='center'>{$l['ipadd']}</td>
						</tr>";
		}

		#=============================
		# Do Output
		#=============================

		if ( $error )
		{
			$error = "<div class='critical'>". $error ."</div>";
		}

		$reply_form = ""; // Initialize for Security

		if ( $t['status'] != 6 )
		{
			if ( $this->ifthd->member['use_rte'] && $this->ifthd->core->cache['config']['enable_ticket_rte'] )
			{
				$reply_form = "<script language='javascript' type='text/javascript' src='<! HD_URL !>/includes/tinymce/tiny_mce.js'></script>
								<script language='javascript' type='text/javascript'>
								tinyMCE.init({
									mode : 'exact',
									theme : 'advanced',
									elements : 'message',
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

								function canned(cid)
								{
									var xmlHttp;

									try
									{
										xmlHttp = new XMLHttpRequest();
									}
									catch (e)
									{
										try
										{
											xmlHttp = new ActiveXObject( 'Msxml2.XMLHTTP' );
										}
										catch (e)
										{
											try
											{
												xmlHttp = new ActiveXObject( 'Microsoft.XMLHTTP' );
											}
											catch (e)
											{
												alert( 'Sorry, your browser does not support AJAX.  Therefore, this feature will not work.' );
												show_hide('canned_drop');
												return false;
											}
										}
									}

									xmlHttp.onreadystatechange=function()
									{
										if( xmlHttp.readyState == 4 )
										{
											tinyMCE.execCommand( 'mceInsertContent', false, xmlHttp.responseText );
										}
									}

									xmlHttp.open( 'GET', '<! HD_URL !>/admin.php?section=manage&act=canned&code=get&id='+cid, true );
									xmlHttp.send(null);

									show_hide('canned_drop');
								}

							</script>";

				$used_rte = "<input type='hidden' name='rte' value='1' />";
			}
			else
			{
				$reply_form = "<script language='javascript' type='text/javascript'>

								function canned(cid)
								{
									var xmlHttp;

									try
									{
										xmlHttp = new XMLHttpRequest();
									}
									catch (e)
									{
										try
										{
											xmlHttp = new ActiveXObject( 'Msxml2.XMLHTTP' );
										}
										catch (e)
										{
											try
											{
												xmlHttp = new ActiveXObject( 'Microsoft.XMLHTTP' );
											}
											catch (e)
											{
												alert( 'Sorry, your browser does not support AJAX.  Therefore, this feature will not work.' );
												show_hide('canned_drop');
												return false;
											}
										}
									}

									xmlHttp.onreadystatechange=function()
									{
										if( xmlHttp.readyState == 4 )
										{
											var inst = get_by_id('message');

											inst.value = xmlHttp.responseText;
										}
									}

									xmlHttp.open( 'GET', '<! HD_URL !>/admin.php?section=manage&act=canned&code=get&id='+cid, true );
									xmlHttp.send(null);

									show_hide('canned_drop');
								}

							</script>";

				$onsubmit = " onsubmit='return validate_form(this)'";
			}

			if ( $this->ifthd->core->cache['config']['ticket_attachments'] && $this->ifthd->member['g_ticket_attach'] && $this->ifthd->core->cache['depart'][ $t['did'] ]['can_attach'] )
			{
				$form_extra = " enctype='multipart/form-data'";
				$upload_field = "<br /><input type='file' name='attachment' id='attachment' size='32' /> ";

				if ( $this->ifthd->member['g_upload_size_max'] )
				{
					$upload_field .= "(Attachment max size: ". $this->ifthd->member['g_upload_size_max'] ." Bytes)";
				}
				else
				{
					$upload_field .= "(Attachment)";
				}
			}

			$canned_drop = ""; // Initialize for Security

			if( $this->ifthd->member['acp']['manage_canned'] )
			{
				if ( is_array( $this->ifthd->core->cache['canned'] ) )
				{
					while( list( , $canned ) = each( $this->ifthd->core->cache['canned'] ) )
					{
						$canned_drop .= "<div onclick='canned(". $canned['id'] .")'>". $canned['name'] ."</div>";
					}
				}
				else
				{
					$canned_drop = "<div>No canned replies.</div>";
				}

				$canned_button = "&nbsp;&nbsp;<span class='fakebutton' onclick=\"show_hide('canned_drop', 1)\" style='cursor:pointer'>Canned Replies <img src='<! IMG_DIR !>/arrow_down.gif' alt='Down' /></span>";
			}

			if ( $this->ifthd->member['signature'] )
			{
				if ( $this->ifthd->member['auto_sig'] ) $auto_sig = " checked='checked'";

				$sig_option = "&nbsp;&nbsp;<input type='checkbox' name='use_sig' id='use_sig' value='1' style='margin-bottom:2px;'{$auto_sig} /> <label for='use_sig'>Append My Signature</label>";
			}

			if ( $nreply_id )
			{
				$reply_form .= "<div class='alert'>Your reply has been successfully added.</div>";

				$this->ifthd->input['message'] = '';
			}

			if ( $error ) $reply_form .= "<a name='newreply'></a>";

			$reply_form .= "<div class='groupbox'>Send a Reply</div>
							<form action='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=reply&amp;id={$t['id']}#newreply' method='post'{$onsubmit}{$form_extra}>
							<div class='option1'>
							{$error}{$used_rte}
							<textarea name='message' id='message' rows='10' cols='120' style='width: 98%; height: 180px;'>{$this->ifthd->input['message']}</textarea>
							{$upload_field}
							</div>
							<div class='formtail' style='text-align:left'><input type='submit' name='submit' id='send' value='Add Reply' class='button' />&nbsp; {$canned_button}&nbsp; &nbsp;<input type='checkbox' name='secret' id='secret' value='1' style='margin-bottom:2px;' /> <label for='secret'>Staff Only Reply</label>{$sig_option}</div>
							<div id='canned_drop' class='fakedropdown' style='position:absolute;margin:-1px 0 0 112px;display:none'>{$canned_drop}</div>
							</form>
							<br />";
		}

		if ( $t['close_reason'] )
		{
			$close_reason = "<div class='alert'>This ticket was closed by {$t['close_mname']} for the following reason.<br /><br />{$t['close_reason']}</div>";
		}

		$t['status'] = $this->ifthd->get_status( $t['status'], 2 );

		if ( $t['attach_id'] )
		{
			$attached ++;

			$attach_download = "<div class='infopop'><a onclick=\"javascript:Effect.toggle('info{$attached}','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Attachment</a><div id='info{$attached}' style='display: none;'><div>Download attachment: <a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=attachment&amp;id=". $t['attach_id'] ."'>". $t['original_name'] ."</a> (". $this->ifthd->format_size( $t['size'] ) .")</div></div></div>";
		}

		#=============================
		# Do Output
		#=============================

		if ( $top_error )
		{
			$top_error = "<div class='critical'>{$top_error}</div>";
		}
		elseif ( $top_alert )
		{
			$top_error = "<div class='alert'>{$top_alert}</div>";
		}

		$this->output = "<script type='text/javascript'>

							function sure_close()
							{
								if ( confirm('Are you sure you want to close this ticket?') )
								{
									return true;
								}
								else
								{
									return false;
								}
							}

							function sure_escalate()
							{
								if ( confirm('Are you sure you want to escalate this ticket?') )
								{
									return true;
								}
								else
								{
									return false;
								}
							}

							function sure_delete()
							{
								if ( confirm('Are you sure you want to delete this ticket?') )
								{
									return true;
								}
								else
								{
									return false;
								}
							}

							function sure_delete_reply()
							{
								if ( confirm('Are you sure you want to delete this reply?') )
								{
									return true;
								}
								else
								{
									return false;
								}
							}

							function validate_form(form)
							{
								if ( ! form.message.value )
								{
									alert('Please enter a message.');
									form.message.focus();
									return false;
								}
							}

						</script>
						{$top_error}
						<div class='groupbox' style='margin-bottom: 0px'><div style='float: right'>{$t['links']}</div>Viewing Ticket</div>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<td class='ticketrow1-med' width='18%'>Ticket ID</td>
							<td class='ticketrow2-med' width='32%'>{$t['id']}</td>
							<td class='ticketrow1-med' width='18%'>Replies</td>
							<td class='ticketrow2-med' width='32%'>{$t['replies']}</td>
						</tr>
						<tr>
							<td class='ticketrow1-med'>Priority</td>
							<td class='ticketrow2-med'>{$t['p_img']}{$t['priority']}</td>
							<td class='ticketrow1-med'>Last Reply</td>
							<td class='ticketrow2-med'>{$t['last_reply']}</td>
						</tr>
						<tr>
							<td class='ticketrow1-med'>Department</td>
							<td class='ticketrow2-med'>{$this->ifthd->core->cache['depart'][ $t['did'] ]['name']}</td>
							<td class='ticketrow1-med'>Last Replier</td>
							<td class='ticketrow2-med'>{$t['last_mname']}</td>
						</tr>
						<tr>
							<td class='ticketrow1-med'>Submitted On</td>
							<td class='ticketrow2-med'>{$t['date']}</td>
							<td class='ticketrow1-med'>Status</td>
							<td class='ticketrow2-med'>{$t['status']}</td>
						</tr>
						<tr>
							<td class='ticketrow1-med'>Submitted By</td>
							<td class='ticketrow2-med'>{$t['mname_link']}</td>
							<td class='ticketrow1-med'>Assigned To</td>
							<td class='ticketrow2-med'>
								{$t['assigned']}
								<div id='assign_drop' class='fakedropdown' style='position:absolute;display:none'>{$assign_drop}</div>
							</td>
						</tr>
						<tr>
							<td class='ticketrow1-med'>Email</td>
							<td class='ticketrow2-med'>{$t['email']}</td>
							<td class='ticketrow1-med'>Satisfaction</td>
							<td class='ticketrow2-med'><span title='{$t['human_rating']}'>{$t['rating']}</span></td>
						</tr>
						{$field_rows}
						</table>

						<form action='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=notes&amp;id={$t['id']}' method='post'>
						<div id='notesbox'{$notes_display}>
						<div class='row1'>
							{$notes_msg}
							<textarea name='tnotes' id='tnotes' rows='4' cols='120' style='width: 98%; height: 100px;'>{$t['notes']}</textarea><br />
							<input type='submit' style='margin-top:4px' name='save' id='save' value='Save Notes' class='button' />
						</div>
						</div>
						</form>

						{$close_reason}

						<br />
						<div class='groupbox'>&quot;{$t['subject']}&quot; <span style='font-size: 11px;'> by {$t['mname']} </span> &nbsp; <span class='date' style='font-size: 10px; font-family: Arial, Helvetica, sans-serif'> (<a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=edit&amp;id=". $t['id'] ."'>EDIT</a>)</span></div>
						<div class='row1'>
							{$t['message']}
						</div>
						{$attach_download}

						{$replies}<br />

						{$reply_form}

						<div class='groupbox'>Ticket History</div>
						<table width='100%' cellpadding='0' cellspacing='0'>
						". $log_rows ."
						</table>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=list'>Tickets</a>",
						   $t['subject'],
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Tickets' ) );
	}

	#=======================================
	# @ Add Ticket
	# Show add ticket form. :)
	#=======================================

	function add_ticket($error='')
	{
		if ( $this->ifthd->input['step'] == 3 && $this->ifthd->input['department'] && ! $error )
		{
			if ( ! $this->ifthd->input['subject'] )
			{
				$this->add_ticket('Please enter a subject.');
			}

			if ( ! $this->ifthd->input['message'] )
			{
				$this->add_ticket('Please enter a message.');
			}

			$required = array( 'department', 'priority' );

			$this->ifthd->check_fields( $required );

			if ( is_array( $this->ifthd->core->cache['dfields'] ) )
			{
				$cdfvalues = ""; // Initialize for Security
				$cdfields_html = "";  // Initialize for Security

				foreach ( $this->ifthd->core->cache['dfields'] as $id => $f )
				{
					$f_perm = unserialize( $f['departs'] );

					if ( $f_perm[ $this->ifthd->input['department'] ] )
					{
						if ( $f['required'] && $f['type'] != 'checkbox' )
						{
							if ( ! $this->ifthd->input[ 'cdf_'. $f['fkey'] ] )
							{
								$this->ifthd->input['step'] = 2;
								$this->add_ticket( 'Please enter a value for the field: '. $f['name'] );
							}
						}

						$cdfvalues[ $f['fkey'] ] = $this->ifthd->input[ 'cdf_'. $f['fkey'] ];
					}
				}
			}

			$this->ifthd->input['department'] = intval( $this->ifthd->input['department'] );

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

			$d_allow = unserialize( $this->ifthd->member['g_m_depart_perm'] );

			$this->ifthd->input['mid'] = intval( $this->ifthd->input['mid'] );

			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'id', 'name', 'email', 'email_notify', 'email_new_ticket', 'time_zone', 'dst_active' ),
												  	 'from'		=> 'members',
								 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['mid'] ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			if ( ! $this->ifthd->core->db->get_num_rows() )
			{
				$this->ifthd->skin->error('no_member');
			}

			$mem = $this->ifthd->core->db->fetch_row();

			if ( $_FILES['attachment']['size'] )
			{
				$allowed_exts = explode( "|", $this->ifthd->core->cache['config']['upload_exts'] );
				$file_ext = strrchr( $_FILES['attachment']['name'], "." );

				if ( ! in_array( $file_ext, $allowed_exts ) )
				{
					$this->ifthd->skin->error('upload_bad_type');
				}

				if ( $this->ifthd->member['g_upload_size_max'] )
				{
					if ( $_FILES['attachment']['size'] > $this->ifthd->member['g_upload_size_max'] )
					{
						$this->ifthd->skin->error('upload_too_big');
					}
				}

				$file_safe_name = $this->sanitize_name( $_FILES['attachment']['name'] );

				$attachment_name = md5( 'a' . uniqid( rand(), true ) ) . $file_ext;

				$attachment_loc = $this->ifthd->core->cache['config']['upload_dir'] .'/'. $attachment_name;

				if ( @ ! move_uploaded_file( $_FILES['attachment']['tmp_name'], $attachment_loc ) )
				{
					$this->ifthd->skin->error('upload_failed');
				}

				$db_array = array(
								  'tid'				=> 0,
								  'real_name'		=> $attachment_name,
								  'original_name'	=> $file_safe_name,
								  'mid'				=> $mem['id'],
								  'mname'			=> $mem['name'],
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

			$db_array = array(
							  'did'			=> $d['id'],
							  'dname'		=> $d['name'],
							  'mid'			=> $mem['id'],
							  'mname'		=> $mem['name'],
							  'email'		=> $mem['email'],
							  'subject'		=> $this->ifthd->input['subject'],
							  'priority'	=> intval( $this->ifthd->input['priority'] ),
							  'message'		=> $this->ifthd->input['message'],
							  'date'		=> time(),
							  'last_reply'	=> time(),
							  'last_mid'	=> $mem['id'],
							  'last_mname'	=> $mem['name'],
							  'ipadd'		=> $this->ifthd->input['ip_address'],
							  'status'		=> 1,
							  'attach_id'	=> $attachment_id,
							  'cdfields'	=> serialize($cdfvalues),
							 );
			
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

			$this->ifthd->core->db->next_no_quotes('set');

			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'members',
												  	 'set'		=> array( 'open_tickets' => 'open_tickets+1', 'tickets' => 'tickets+1' ),
								 				  	 'where'	=> array( 'id', '=', $mem['id'] ),
								 				  	 'limit'	=> array( 1 ),
								 		  	  ) 	);

			$this->ifthd->core->db->next_shutdown();
			$this->ifthd->core->db->execute();

			$this->ifthd->core->db->next_no_quotes('set');

			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'departments',
												  	 'set'		=> array( 'tickets' => 'tickets+1' ),
								 				  	 'where'	=> array( 'id', '=', $d['id'] ),
								 				  	 'limit'	=> array( 1 ),
								 		  	  ) 	);

			$this->ifthd->core->db->next_shutdown();
			$this->ifthd->core->db->execute();

			if ( $mem['email_new_ticket'] && $mem['email_notify'] )
			{
				$mem_offset = ( $mem['time_zone'] * 60 * 60 ) + ( $mem['dst_active'] * 60 * 60 );

				$replace = array(); // Initialize for Security

				$replace['TICKET_ID'] = $ticket_id;
				$replace['SUBJECT'] = $this->ifthd->input['subject'];
				$replace['DEPARTMENT'] = $d['name'];
				$replace['PRIORITY'] = $this->ifthd->get_priority( $this->ifthd->input['priority'] );
				$replace['SUB_DATE'] = $this->ifthd->ift_date( time(), '', '', 0, 1, $mem_offset, 1 );
				$replace['TICKET_LINK'] = $this->ifthd->core->cache['config']['hd_url'] ."/index.php?act=tickets&code=view&id=". $ticket_id;
				$replace['MESSAGE'] = $this->ifthd->input['message'];

				$this->ifthd->send_email( $mem['id'], 'new_ticket', $replace, array( 'from_email' => $d['incoming_email'] ), 1 );
			}

			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'm' => array( 'id', 'mgroup', 'email_staff_new_ticket', 'time_zone', 'dst_active' ),
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
							if ( $sm['email_staff_new_ticket'] )
							{
								$s_email_staff = 1;
							}

							$do_feeds[ $sm['id'] ] = 1;
						}
					}
					else
					{
						if ( $sm['email_staff_new_ticket'] )
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

						$replace['MEMBER'] = $mem['name'];

						$this->ifthd->send_email( $sm['id'], 'staff_new_ticket', $replace, array( 'from_email' => $d['incoming_email'] ), 1 );
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

			$this->ifthd->r_ticket_stats(1);

			$this->ifthd->skin->redirect( '?section=manage&act=tickets&code=view&id='. $ticket_id, 'submit_ticket_success' );
		}
		elseif ( ( $this->ifthd->input['step'] == 2 && $this->ifthd->input['department'] ) || $error )
		{
			if ( is_array( $this->ifthd->core->cache['dfields'] ) )
			{
				$cdfields = array(); // Initialize for Security

				foreach( $this->ifthd->core->cache['dfields'] as $id => $f )
				{
					$f_perm = unserialize( $f['departs'] );

					if ( $f_perm[ $this->ifthd->input['department'] ] )
					{
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
			}

			if ( $this->ifthd->core->cache['config']['ticket_attachments'] && $this->ifthd->member['g_ticket_attach'] && $this->ifthd->core->cache['depart'][ $this->ifthd->input['department'] ]['can_attach'] )
			{
				if ( $this->ifthd->member['g_upload_size_max'] )
				{
					$upload_info = ' (Attachment max size: '. $this->ifthd->member['g_upload_size_max'] .' Bytes)';
				}
				else
				{
					$upload_info = ' (Attachment)';
				}
			}

			$this->output = "<script type='text/javascript'>

							function validate_form(form)
							{
								if ( ! form.subject.value )
								{
									alert('Please enter a subject.');
									form.subject.focus();
									return false;
								}

								if ( ! form.message.value )
								{
									alert('Please enter a message.');
									form.message.focus();
									return false;
								}
							}

						</script>
						<form action='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=add&amp;step=3&amp;mid={$this->ifthd->input['mid']}' method='post' onsubmit='return validate_form(this)' enctype='multipart/form-data'>
						{$token_sub_b}
						<input type='hidden' name='department' value='{$this->ifthd->input['department']}' />";

			if ( $error )
			{
				$this->output .= "<div class='critical'>{$error} {$error_extra}</div>";
			}

			$this->output .= "<div class='groupbox'>Submit A Ticket: {$this->ifthd->core->cache['depart'][ $this->ifthd->input['department'] ]['name']}</div>
							<table width='100%' cellpadding='0' cellspacing='0'>
							<tr>
								<td class='option1' width='15%'><label for='subject'>Subject</label></td>
								<td class='option1' width='85%'><input type='text' name='subject' id='subject' value='{$input['subject']}' size='35' /></td>
							</tr>
							<tr>
								<td class='option2'><label for='priority'>Priority</label></td>
								<td class='option2'><select name='priority' id='priority'>". $this->ifthd->build_priority_drop( $this->ifthd->input['priority'] ) ."</select></td>
							</tr>";

			if ( $cdfields )
			{
				$row_count = 0;

				foreach( $cdfields as $cdf )
				{
					$row_count ++;

					( $row_count & 1 ) ? $row_class = 'option1' : $row_class = 'option2';

					if ( $cdf['type'] == 'textfield' )
					{
						$this->output .= "<tr>
								<td class='{$row_class}'><label for='cdf_{$cdf['fkey']}'>{$cdf['name']}</label></td>
								<td class='{$row_class}'><input type='text' name='cdf_{$cdf['fkey']}' id='cdf_{$cdf['fkey']}' value='{$cdf['value']}' size='45' /> {$cdf['optional']}</td>
							</tr>";
					}
					elseif ( $cdf['type'] == 'textarea' )
					{
						$this->output .= "<tr>
								<td class='{$row_class}'><label for='cdf_{$cdf['fkey']}'>{$cdf['name']}</label></td>
								<td class='{$row_class}'><textarea name='cdf_{$cdf['fkey']}' id='cdf_{$cdf['fkey']}' cols='50' rows='3'>{$cdf['value']}</textarea> {$cdf['optional']}</td>
							</tr>";
					}
					elseif ( $cdf['type'] == 'dropdown' )
					{
						$this->output .= "<tr>
								<td class='{$row_class}'><label for='cdf_{$cdf['fkey']}'>{$cdf['name']}</label></td>
								<td class='{$row_class}'><select name='cdf_{$cdf['fkey']}' id='cdf_{$cdf['fkey']}'>{$cdf['options']}</select> {$cdf['optional']}</td>
							</tr>";
					}
					elseif ( $cdf['type'] == 'checkbox' )
					{
						$this->output .= "<tr>
								<td class='{$row_class}'><label for='cdf_{$cdf['fkey']}'>{$cdf['name']}</label></td>
								<td class='{$row_class}'><input type='checkbox' name='cdf_{$cdf['fkey']}' id='cdf_{$cdf['fkey']}' value='1' class='ckbox'";

						if ( $cdf['value'] ) $this->output .= " checked='checked'";

						$this->output .= " />";

						if ( $cdf['extra'] ) $this->output .= "<label for='cdf_{$cdf['fkey']}'>{$cdf['extra']}</label>";

						$this->output .= "</td>
							</tr>";
					}
					elseif ( $cdf['type'] == 'radio' )
					{
						$this->output .= "<tr>
								<td class='{$row_class}'>{$cdf['name']}</td>
								<td class='{$row_class}'>{$cdf['options']} {$cdf['optional']}</td>
							</tr>";
					}
				}
			}

			$row_count ++;

			( $row_count & 1 ) ? $row_class = 'option1' : $row_class = 'option2';

			$this->output .= "<tr>
								<td colspan='2' class='{$row_class}'><textarea name='message' id='message' style='width: 98%; height: 180px;'>{$input['message']}</textarea></td>
							</tr>";

			$row_count ++;

			( $row_count & 1 ) ? $row_class = 'option1' : $row_class = 'option2';

			if ( $this->ifthd->core->cache['config']['ticket_attachments'] && $this->ifthd->member['g_ticket_attach'] && $this->ifthd->core->cache['depart'][ $this->ifthd->input['department'] ]['can_attach'] )
			{
				$this->output .= "<tr>
								<td colspan='2' class='{$row_class}'><input type='file' name='attachment' id='attachment' size='32' />{$upload_info}</td>
							</tr>";
			}

			$this->output .= "</table>
							<div class='formtail'><input type='submit' name='submit' id='send' value='Submit Ticket' class='button' /></div>
							</form>";

			$this->ifthd->skin->add_output( $this->output );

			$this->nav = array(
							   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
							   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=list'>Tickets</a>",
							   "Submit A Ticket",
							   );

			$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Tickets' ) );
		}
		else
		{
			$this->output = "<div class='groupbox'>Submit A Ticket</div>
							<form action='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=add&amp;step=2&amp;mid={$this->ifthd->input['mid']}' method='post' onsubmit='return validate_form(this)'>
							<div class='subbox'>Please select a department.</div>
							<div class='option1'>
								<table width='100%' cellpadding='4' cellspacing='0'>
									". $this->ifthd->build_dprt_drop( $this->ifthd->input['department'], 0, 0, 2 ) ."
								</table>
							</div>
							<div class='formtail'><input type='submit' name='submit' id='send' value='Submit Ticket' class='button' /></div>
							</form>";

			$this->ifthd->skin->add_output( $this->output );

			$this->nav = array(
							   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
							   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=list'>Tickets</a>",
							   "Submit A Ticket",
							   );

			$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Tickets' ) );
		}
	}

	#=======================================
	# @ Notes Save
	# Save notes for a ticket.
	#=======================================

	function notes_save()
	{
		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'tickets',
											  	 'set'		=> array( 'notes' => $this->ifthd->input['tnotes'] ),
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->view_ticket( '', '', '', $this->ifthd->input['tnotes'] );
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

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_ticket_reply'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->input['message'] = $this->ifthd->remove_extra_lbs( $this->ifthd->input['message'] );

		if ( ! $this->ifthd->input['message'] )
		{
			$this->view_ticket('Please enter a message.');
		}

		if ( is_array( unserialize( $this->ifthd->member['g_depart_perm'] ) ) )
		{
			$rev_perms = array(); // Initialize for Security

			foreach( unserialize( $this->ifthd->member['g_depart_perm'] ) as $did => $access )
			{
				if ( $access == 1 ) $rev_perms[] = $did;
			}

			$sql_where = array( array( 'id', '=', $this->ifthd->input['id'] ), array( 'did', 'in', $rev_perms, 'and' ) );
		}
		else
		{
			$sql_where = array( 'id', '=', $this->ifthd->input['id'] );
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'tickets',
							 				  	 'where'	=> $sql_where,
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_ticket');
		}

		$t = $this->ifthd->core->db->fetch_row();

		if ( $t['status'] == 6 )
		{
			$this->ifthd->skin->error('ticket_closed_reply');
		}

		#=============================
		# Grab Member Info
		#=============================

		if ( $t['mid'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'email_notify', 'email_ticket_reply', 'time_zone', 'dst_active' ),
												  	 'from'		=> 'members',
								 				  	 'where'	=> array( 'id', '=', $t['mid'] ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$mem = $this->ifthd->core->db->fetch_row();
		}

		#=============================
		# Attachment
		#=============================

		if ( $_FILES['attachment']['size'] )
		{
			$allowed_exts = explode( "|", $this->ifthd->core->cache['config']['upload_exts'] );
			$file_ext = strrchr( $_FILES['attachment']['name'], "." );

			if ( ! in_array( $file_ext, $allowed_exts ) )
			{
				$this->view_ticket('The file type you were trying to upload is not allowed.');
			}

			if ( $this->ifthd->member['g_upload_size_max'] )
			{
				if ( $_FILES['attachment']['size'] > $this->ifthd->member['g_upload_size_max'] )
				{
					$this->view_ticket('The file you were trying to upload exceeded the max upload size.');
				}
			}

			$file_safe_name = $this->sanitize_name( $_FILES['attachment']['name'] );

			$attachment_name = md5( 'a' . uniqid( rand(), true ) ) . $file_ext;

			$attachment_loc = $this->ifthd->core->cache['config']['upload_dir'] .'/'. $attachment_name;

			if ( @ ! move_uploaded_file( $_FILES['attachment']['tmp_name'], $attachment_loc ) )
			{
				$this->view_ticket('File upload failed.  Please try again.');
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

		#=============================
		# Add Reply
		#=============================

		if ( $this->ifthd->member['signature'] && $this->ifthd->input['use_sig'] )
		{
			$this->ifthd->input['message'] .= '&lt;br /&gt;&lt;br /&gt;'. $this->ifthd->member['signature'];
		}
		$db_array = array(
						  'tid'			=> $t['id'],
						  'mid'			=> $this->ifthd->member['id'],
						  'mname'		=> $this->ifthd->member['name'],
						  'message'		=> $this->ifthd->input['message'],
						  'staff'		=> 1,
						  'rte'			=> intval( $this->ifthd->input['rte'] ),
						  'secret'		=> intval( $this->ifthd->input['secret'] ),
						  'attach_id'	=> $attachment_id,
						  'date'		=> time(),
						  'ipadd'		=> $this->ifthd->input['ip_address'],
						 );

		$this->ifthd->core->db->construct( array(
											  	 'insert'	=> 'replies',
											  	 'set'		=> $db_array,
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$reply_id = $this->ifthd->core->db->get_insert_id();

		$this->ifthd->log( 'admin', "Ticket Reply &#039;". $t['subject'] ."&#039;", 1, $t['id'] );
		$this->ifthd->log( 'ticket', "Ticket Reply &#039;". $t['subject'] ."&#039;", 1, $t['id'] );

		#=============================
		# Update Ticket
		#=============================

		if ( ! $this->ifthd->input['secret'] )
		{
			$sql_status = ""; // Initialize for Security

			if ( $this->ifthd->core->cache['depart'][ $t['did'] ]['auto_close'] )
			{
				$auto_close = time() + ( $this->ifthd->core->cache['depart'][ $t['did'] ]['auto_close'] * 60 * 60 );
			}

			$db_array = array(
							  'last_reply'			=> time(),
							  'last_reply_staff'	=> time(),
							  'last_mid'			=> $this->ifthd->member['id'],
							  'last_mname'			=> $this->ifthd->member['name'],
							  'replies'				=> $t['replies'] + 1,
							  'auto_close'			=> $auto_close,
							 );

			if ( $t['status'] != 5 )
			{
				$db_array['status'] = 4;
			}

			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'tickets',
												  	 'set'		=> $db_array,
								 				  	 'where'	=> array( 'id', '=', $t['id'] ),
								 		  	  ) 	);

			#$this->ifthd->core->db->next_shutdown();
			$this->ifthd->core->db->execute();

			#=============================
			# Send Email
			#=============================

			if ( ( $mem['email_ticket_reply'] && $mem['email_notify'] ) || ( $this->ifthd->core->cache['config']['guest_ticket_emails'] && $t['guest_email'] ) )
			{
				$mem_offset = ( $mem['time_zone'] * 60 * 60 ) + ( $mem['dst_active'] * 60 * 60 );

				$replace = ""; // Initialize for Security

				$replace['TICKET_ID'] = $t['id'];
				$replace['SUBJECT'] = $t['subject'];
				$replace['DEPARTMENT'] = $t['dname'];
				$replace['PRIORITY'] = $this->ifthd->get_priority( $t['priority'] );
				$replace['SUB_DATE'] = $this->ifthd->ift_date( $t['date'], '', '', 0, 1, $mem_offset, 1 );
				$replace['REPLY'] = $this->ifthd->input['message'];
				$replace['MESSAGE'] = $t['message'];

				if ( $mem['email_ticket_reply'] )
				{
					$replace['TICKET_LINK'] = $this->ifthd->core->cache['config']['hd_url'] ."/index.php?act=tickets&code=view&id=". $t['id'];
					
					$this->ifthd->send_email( $t['mid'], 'ticket_reply', $replace, array( 'from_email' => $this->ifthd->core->cache['depart'][ $t['did'] ]['incoming_email'] ), 1 );
				}
				elseif ( ! $t['mid'] )
				{
					$replace['MEM_NAME'] = $t['mname'];
					$replace['TICKET_KEY'] = $t['tkey'];
					
					$replace['TICKET_LINK'] = $this->ifthd->core->cache['config']['hd_url'] ."/index.php?act=tickets&code=view&id=". $t['id'] ."&email=". urlencode( $t['email'] ) ."&key=". $t['tkey'];

					$this->ifthd->send_guest_email( $t['email'], 'ticket_reply_guest', $replace, array( 'from_email' => $this->ifthd->core->cache['depart'][ $t['did'] ]['incoming_email'] ), 1 );
				}
			}
		}

		#=============================
		# Update Stats
		#=============================

		$this->ifthd->r_ticket_stats(1);

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=tickets&code=view&id='. $t['id'] .'#reply'. $reply_id, '', 1 );
		$this->view_ticket( '', '', '', '', $reply_id );
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

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( is_array( unserialize( $this->ifthd->member['g_depart_perm'] ) ) )
		{
			$rev_perms = array(); // Initialize for Security

			foreach( unserialize( $this->ifthd->member['g_depart_perm'] ) as $did => $access )
			{
				if ( $access == 1 ) $rev_perms[] = $did;
			}

			$sql_where = array( array( 'id', '=', $this->ifthd->input['id'] ), array( 'did', 'in', $rev_perms, 'and' ) );
		}
		else
		{
			$sql_where = array( 'id', '=', $this->ifthd->input['id'] );
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'tickets',
							 				  	 'where'	=> $sql_where,
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_ticket');
		}

		$t = $this->ifthd->core->db->fetch_row();

		#=============================
		# Grab Member Info
		#=============================

		if ( $t['mid'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'email_notify', 'email_ticket_reply', 'open_tickets', 'tickets', 'time_zone', 'dst_active' ),
												  	 'from'		=> 'members',
								 				  	 'where'	=> array( 'id', '=', $t['mid'] ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$mem = $this->ifthd->core->db->fetch_row();

			$mem_offset = ( $mem['time_zone'] * 60 * 60 ) + ( $mem['dst_active'] * 60 * 60 );
		}

		if ( $action == 'close' )
		{
			if ( ! $this->ifthd->member['acp']['manage_ticket_close'] )
			{
				$this->ifthd->skin->error('no_perm');
			}

			if ( $t['status'] == 6 )
			{
				$this->ifthd->log( 'error', "Ticket Already Closed &#039;". $t['subject'] ."&#039;", 1, $t['id'] );

				$this->ifthd->skin->error('ticket_closed_already');
			}

			if ( $this->ifthd->core->cache['depart'][ $t['did'] ]['close_reason'] )
			{
				if ( ! $this->ifthd->input['reason'] )
				{
					if ( $this->ifthd->input['final'] )
					{
						$error = "<div class='critical'>Please enter a reason.</div>";
					}

					$this->output = "<script type='text/javascript'>

										function validate_form(form)
										{
											if ( ! form.reason.value )
											{
												alert('Please enter a reason.');
												form.reason.focus();
												return false;
											}
										}

									</script>
									{$error}
									<div class='groupbox'>Closing Ticket: ". $t['subject'] ."</div>
									<form action='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=close&amp;id={$t['id']}&amp;final=1' method='post'>
									<div class='option1'>
										Please enter a reason for closing this ticket.
										<textarea name='reason' id='reason' rows='4' cols='120' style='width: 98%; height: 70px; margin-top: 7px;'>{$this->ifthd->input['reason']}</textarea>
									</div>
									<div class='formtail'><input type='submit' name='submit' id='do_close' value='Close Ticket' class='button' /></div>
									</form>";

					$this->ifthd->skin->add_output( $this->output );

					$this->nav = array(
									   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
									   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets'>Tickets</a>",
									   "<a href='<! HD_URL !>/admin.php?section=manage&act=tickets&code=view&id=". $t['id'] ."'>". $t['subject'] ."</a>",
									   "Close Ticket",
									   );

					$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Tickets' ) );
				}
			}

			#=============================
			# Close Ticket
			#=============================

			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'tickets',
												  	 'set'		=> array( 'close_mid' => $this->ifthd->member['id'], 'close_mname' => $this->ifthd->member['name'], 'close_reason' => $this->ifthd->input['reason'], 'status' => 6 ),
								 				  	 'where'	=> array( 'id', '=', $t['id'] ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$this->ifthd->log( 'admin', "Ticket Closed &#039;". $t['subject'] ."&#039;", 1, $t['id'] );
			$this->ifthd->log( 'ticket', "Ticket Closed &#039;". $t['subject'] ."&#039;", 1, $t['id'] );

			#=============================
			# Update Member
			#=============================

			if ( $t['mid'] )
			{
				$this->ifthd->core->db->construct( array(
													  	 'update'	=> 'members',
													  	 'set'		=> array( 'open_tickets' => $mem['open_tickets'] - 1 ),
									 				  	 'where'	=> array( 'id', '=', $t['mid'] ),
									 		  	  ) 	);

				$this->ifthd->core->db->next_shutdown();
				$this->ifthd->core->db->execute();
			}

			if ( $t['amid'] )
			{
				$this->ifthd->core->db->next_no_quotes('set');

				$this->ifthd->core->db->construct( array(
													  	 'update'	=> 'members',
													  	 'set'		=> array( 'assigned' => 'assigned-1' ),
									 				  	 'where'	=> array( 'id', '=', $t['amid'] ),
									 		  	  ) 	);

				$this->ifthd->core->db->execute();

				$this->ifthd->rebuild_staff_cache();
			}


			#=============================
			# Send Email
			#=============================

			if ( ( $mem['email_ticket_reply'] && $mem['email_notify'] ) || ( $this->ifthd->core->cache['config']['guest_ticket_emails'] && $t['guest_email'] ) )
			{
				$replace = ""; // Initialize for Security

				$replace['TICKET_ID'] = $t['id'];
				$replace['SUBJECT'] = $t['subject'];
				$replace['DEPARTMENT'] = $t['dname'];
				$replace['PRIORITY'] = $this->ifthd->get_priority( $t['priority'] );
				$replace['SUB_DATE'] = $this->ifthd->ift_date( $t['date'], '', '', 0, 1, $mem_offset, 1 );
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

			#$this->ifthd->skin->redirect( '?section=manage&act=tickets&code=view&id='. $t['id'], 'ticket_close_success' );
			$this->view_ticket( '', 'The ticket has been successfully closed.' );
		}
		elseif ( $action == 'escalate' )
		{
			if ( ! $this->ifthd->member['acp']['manage_ticket_escalate'] || ! $this->ifthd->core->cache['depart'][ $t['did'] ]['can_escalate'] )
			{
				$this->ifthd->skin->error('no_perm');
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
									 		  	  ) 	);

				$this->ifthd->core->db->next_shutdown();
				$this->ifthd->core->db->execute();

				// New Department
				$this->ifthd->core->db->next_no_quotes('set');

				$this->ifthd->core->db->construct( array(
													  	 'update'	=> 'departments',
													  	 'set'		=> array( 'tickets' => 'tickets+1' ),
									 				  	 'where'	=> array( 'id', '=', $this->ifthd->core->cache['depart'][ $t['did'] ]['escalate_depart'] ),
									 		  	  ) 	);

				$this->ifthd->core->db->next_shutdown();
				$this->ifthd->core->db->execute();

				$db_array = array( 'did' => $this->ifthd->core->cache['depart'][ $t['did'] ]['escalate_depart'], 'dname' => $this->ifthd->core->cache['depart'][ $this->ifthd->core->cache['depart'][ $t['did'] ]['escalate_depart'] ]['name'], 'status' => 5 );
			}
			else
			{
				$db_array = array( 'status' => 5 );
			}

			#=============================
			# Escalate Ticket
			#=============================

			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'tickets',
												  	 'set'		=> $db_array,
								 				  	 'where'	=> array( 'id', '=', $t['id'] ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$this->ifthd->log( 'admin', "Ticket Escalated &#039;". $t['subject'] ."&#039;", 1, $t['id'] );
			$this->ifthd->log( 'ticket', "Ticket Escalated &#039;". $t['subject'] ."&#039;", 1, $t['id'] );

			#=============================
			# Send Email
			#=============================

			if ( ( $mem['email_ticket_reply'] && $mem['email_notify'] ) || ( $this->ifthd->core->cache['config']['guest_ticket_emails'] && $t['guest_email'] ) )
			{
				$replace = ""; // Initialize for Security

				$replace['TICKET_ID'] = $t['id'];
				$replace['SUBJECT'] = $t['subject'];
				$replace['DEPARTMENT'] = $t['dname'];
				$replace['PRIORITY'] = $this->ifthd->get_priority( $t['priority'] );
				$replace['SUB_DATE'] = $this->ifthd->ift_date( $t['date'], '', '', 0, 1, $mem_offset, 1 );
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

			#$this->ifthd->skin->redirect( '?section=manage&act=tickets&code=view&id='. $t['id'], 'ticket_escalate_success' );
			$this->view_ticket( '', '', 'The ticket has been successfully escalated.' );
		}
		elseif ( $action == 'hold' )
		{
			if ( ! $this->ifthd->member['acp']['manage_ticket_hold'] )
			{
				$this->ifthd->skin->error('no_perm');
			}

			if ( $t['status'] == 6 )
			{
				$this->ifthd->log( 'error', "Put On Hold Rejected Ticket Closed &#039;". $t['subject'] ."&#039;", 1, $t['id'] );

				$this->ifthd->skin->error('ticket_closed_hold');
			}

			if ( $t['status'] == 3 )
			{
				$this->ifthd->log( 'error', "Ticket Already On Hold &#039;". $t['subject'] ."&#039;", 1, $t['id'] );

				$this->ifthd->skin->error('ticket_hold_already');
			}

			#=============================
			# Hold Ticket
			#=============================

			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'tickets',
												  	 'set'		=> array( 'status' => 3 ),
								 				  	 'where'	=> array( 'id', '=', $t['id'] ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$this->ifthd->log( 'admin', "Ticket Put On Hold &#039;". $t['subject'] ."&#039;", 1, $t['id'] );
			$this->ifthd->log( 'ticket', "Ticket Put On Hold &#039;". $t['subject'] ."&#039;", 1, $t['id'] );

			#=============================
			# Update Stats
			#=============================

			$this->ifthd->r_ticket_stats(1);

			#=============================
			# Redirect
			#=============================

			#$this->ifthd->skin->redirect( '?section=manage&act=tickets&code=list', 'ticket_hold_success' );
			$this->view_ticket( '', '', 'The ticket has been successfully put on hold.' );
		}
		elseif ( $action == 'delete' )
		{
			if ( ! $this->ifthd->member['acp']['manage_ticket_delete'] )
			{
				$this->ifthd->skin->error('no_perm');
			}

			#=============================
			# Delete Ticket
			#=============================

			$this->ifthd->core->db->construct( array(
												  	 'delete'	=> 'tickets',
								 				  	 'where'	=> array( 'id', '=', $t['id'] ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$this->ifthd->log( 'admin', "Ticket Deleted &#039;". $t['subject'] ."&#039;", 2, $t['id'] );
			$this->ifthd->log( 'ticket', "Ticket Deleted &#039;". $t['subject'] ."&#039;", 2, $t['id'] );

			#=============================
			# Update Member
			#=============================

			if ( $t['mid'] )
			{
				if ( $t['status'] != 6 )
				{
					$db_array = array( 'open_tickets' => $mem['open_tickets'] - 1, 'tickets' => $mem['tickets'] - 1 );
				}
				else
				{
					$db_array = array( 'tickets' => $mem['tickets'] - 1 );
				}

				$this->ifthd->core->db->construct( array(
													  	 'update'	=> 'members',
													  	 'set'		=> $db_array,
									 				  	 'where'	=> array( 'id', '=', $t['mid'] ),
									 		  	  ) 	);

				$this->ifthd->core->db->next_shutdown();
				$this->ifthd->core->db->execute();
			}

			if ( $t['amid'] )
			{
				$this->ifthd->core->db->next_no_quotes('set');

				$this->ifthd->core->db->construct( array(
													  	 'update'	=> 'members',
													  	 'set'		=> array( 'assigned' => 'assigned-1' ),
									 				  	 'where'	=> array( 'id', '=', $t['amid'] ),
									 		  	  ) 	);

				$this->ifthd->core->db->execute();

				$this->ifthd->rebuild_staff_cache();
			}

			#=============================
			# Update Department
			#=============================

			$this->ifthd->core->db->next_no_quotes('set');

			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'departments',
												  	 'set'		=> array( 'tickets' => 'tickets-1' ),
								 				  	 'where'	=> array( 'id', '=', $t['did'] ),
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

			#$this->ifthd->skin->redirect( '?section=manage&act=tickets&code=list', 'ticket_delete_success' );
			$this->list_tickets( 'The ticket has been successfully deleted.' );
		}
		elseif ( $action == 'reopen' )
		{
			if ( ! $this->ifthd->member['acp']['manage_ticket_reopen'] )
			{
				$this->ifthd->skin->error('no_perm');
			}

			if ( $t['status'] != 6 )
			{
				$this->ifthd->log( 'error', "Reopen Rejected Ticket Not Closed &#039;". $t['subject'] ."&#039;", 1, $t['id'] );

				$this->ifthd->skin->error('ticket_reopen_already');
			}

			#=============================
			# Reopen Ticket
			#=============================

			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'tickets',
												  	 'set'		=> array( 'close_reason' => "", 'status' => 1 ),
								 				  	 'where'	=> array( 'id', '=', $t['id'] ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$this->ifthd->log( 'admin', "Ticket Reopened &#039;". $t['subject'] ."&#039;", 1, $t['id'] );
			$this->ifthd->log( 'ticket', "Ticket Reopened &#039;". $t['subject'] ."&#039;", 1, $t['id'] );

			#=============================
			# Update Member
			#=============================

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

			#=============================
			# Update Stats
			#=============================

			$this->ifthd->r_ticket_stats(1);

			#=============================
			# Redirect
			#=============================

			#$this->ifthd->skin->redirect( '?section=manage&act=tickets&code=view&id='. $t['id'], 'ticket_reopen_success' );
			$this->view_ticket( '', '', 'The ticket has been successfully re-opened.' );
		}
		elseif ( $action == 'move' )
		{
			if ( ! $this->ifthd->member['acp']['manage_ticket_move'] )
			{
				$this->ifthd->skin->error('no_perm');
			}

			if ( ! $this->ifthd->input['move_to'] )
			{
				if ( $this->ifthd->input['final'] )
				{
					$error = "<div class='critical'>Please select a department.</div>";
				}

				$depart_drop = $this->ifthd->build_dprt_drop( 0, $t['did'], 1 );

				$this->output = "{$error}<div class='groupbox'>Moving Ticket: ". $t['subject'] ."</div>
								<form action='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=move&amp;id={$t['id']}&amp;final=1' method='post'>
								<div class='option1'>Move to Department: <select name='move_to' id='move_to'>{$depart_drop}</select></div>
								<div class='formtail'><input type='submit' name='submit' id='move' value='Move Ticket' class='button' /></div>
								</form>";

				$this->ifthd->skin->add_output( $this->output );

				$this->nav = array(
								   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
								   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets'>Tickets</a>",
								   "<a href='<! HD_URL !>/admin.php?section=manage&act=tickets&code=view&id=". $t['id'] ."'>". $t['subject'] ."</a>",
								   "Move Ticket",
								   );

				$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Tickets' ) );
			}
		}

		#=============================
		# Move Ticket
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'tickets',
											  	 'set'		=> array( 'did' => $this->ifthd->input['move_to'], 'dname' => $this->ifthd->core->cache['depart'][ $this->ifthd->input['move_to'] ]['name'] ),
							 				  	 'where'	=> array( 'id', '=', $t['id'] ),
							 		  	  ) 	);

		#$this->ifthd->core->db->next_shutdown();
		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'admin', "Ticket Moved &#039;". $t['subject'] ."&#039;", 1, $t['id'] );
		$this->ifthd->log( 'ticket', "Ticket Moved &#039;". $t['subject'] ."&#039;", 1, $t['id'] );

		#=============================
		# Send Email
		#=============================

		if ( ( $mem['email_ticket_reply'] && $mem['email_notify'] ) || ( $this->ifthd->core->cache['config']['guest_ticket_emails'] && $t['guest_email'] ) )
		{
			$replace = ""; // Initialize for Security

			$replace['TICKET_ID'] = $t['id'];
			$replace['SUBJECT'] = $t['subject'];
			$replace['OLD_DEPARTMENT'] = $t['dname'];
			$replace['NEW_DEPARTMENT'] = $this->ifthd->core->cache['depart'][ $this->ifthd->input['move_to'] ]['name'];
			$replace['PRIORITY'] = $this->ifthd->get_priority( $t['priority'] );
			$replace['SUB_DATE'] = $this->ifthd->ift_date( $t['date'], '', '', 0, 1, $mem_offset, 1 );
			$replace['TICKET_LINK'] = $this->ifthd->core->cache['config']['hd_url'] ."/index.php?act=tickets&code=view&id=". $t['id'];
			$replace['MESSAGE'] = $t['message'];

			if ( $mem['email_ticket_reply'] )
			{
				$this->ifthd->send_email( $t['mid'], 'ticket_move', $replace, array( 'from_email' => $this->ifthd->core->cache['depart'][ $t['did'] ]['incoming_email'] ), 1 );
			}
			else
			{
				$replace['MEM_NAME'] = $t['mname'];

				$this->ifthd->send_guest_email( $t['email'], 'ticket_move', $replace, array( 'from_email' => $this->ifthd->core->cache['depart'][ $t['did'] ]['incoming_email'] ), 1 );
			}
		}

		#=============================
		# Update Departments
		#=============================

		// Old Department
		$this->ifthd->core->db->next_no_quotes('set');

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'departments',
											  	 'set'		=> array( 'tickets' => 'tickets-1' ),
							 				  	 'where'	=> array( 'id', '=', $t['did'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->next_shutdown();
		$this->ifthd->core->db->execute();

		// New Department
		$this->ifthd->core->db->next_no_quotes('set');

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'departments',
											  	 'set'		=> array( 'tickets' => 'tickets+1' ),
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['move_to'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->next_shutdown();
		$this->ifthd->core->db->execute();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=tickets&code=view&id='. $t['id'], 'ticket_move_success' );
		$this->view_ticket( '', '', 'The ticket has been successfully moved.' );
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

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_ticket_reply'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		#=============================
		# Grab Reply
		#=============================

		if ( is_array( unserialize( $this->ifthd->member['g_depart_perm'] ) ) )
		{
			$rev_perms = array(); // Initialize for Security

			foreach( unserialize( $this->ifthd->member['g_depart_perm'] ) as $did => $access )
			{
				if ( $access == 1 ) $rev_perms[] = $did;
			}

			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'r' => 'all', 't' => array( 'did', 'dname' ) ),
												  	 'from'		=> array( 'r' => 'replies' ),
												  	 'join'		=> array( array( 'from' => array( 't' => 'tickets' ), 'where' => array( 'r' => 'tid', '=', 't' => 'id' ) ) ),
								 				  	 'where'	=> array( array( array( 'r' => 'id' ), '=', $this->ifthd->input['id'] ), array( array( 't' => 'did' ), 'in', $rev_perms, 'and' ) ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);
		}
		else
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'r' => 'all', 't' => array( 'did', 'dname' ) ),
												  	 'from'		=> array( 'r' => 'replies' ),
												  	 'join'		=> array( array( 'from' => array( 't' => 'tickets' ), 'where' => array( 'r' => 'tid', '=', 't' => 'id' ) ) ),
								 				  	 'where'	=> array( array( 'r' => 'id' ), '=', $this->ifthd->input['id'] ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);
		}

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_reply');
		}

		$r = $this->ifthd->core->db->fetch_row();

		#=============================
		# Do Output
		#=============================

		if ( $error )
		{
			$error = "<div class='critical'>". $error ."</div>";

			$message = $this->ifthd->input['message'];
			if ( $this->ifthd->input['secret'] ) $secret = ' checked="checked"';
		}
		else
		{
			$message = $r['message'];
			if ( $r['secret'] ) $secret = ' checked="checked"';
		}

		if ( $this->ifthd->member['use_rte'] && $this->ifthd->core->cache['config']['enable_ticket_rte'] )
		{
			$rte_javascript = "<script language='javascript' type='text/javascript' src='<! HD_URL !>/includes/tinymce/tiny_mce.js'></script>
								<script language='javascript' type='text/javascript'>
								tinyMCE.init({
									mode : 'exact',
									theme : 'advanced',
									elements : 'message',
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

				$used_rte = "<input type='hidden' name='rte' value='1' />";
			}
			else
			{
				$onsubmit = " onsubmit='return validate_form(this)'";
			}

		$this->output = "<script type='text/javascript'>

						function validate_form(form)
						{
							if ( ! form.reply.value )
							{
								alert('Please enter a reply.');
								form.reason.focus();
								return false;
							}
						}

						</script>
						{$rte_javascript}
						{$error}
						<div class='groupbox'>Editing Reply ID: ". $r['id'] ."</div>
						<form action='<! HD_URL !>/admin.php?section=manage&amp;act=reply&amp;code=doedit&amp;id={$r['id']}' method='post'{$onsubmit}>
						{$used_rte}
						<div class='option1'><textarea name='message' id='message' rows='8' cols='120' style='width: 98%; height: 120px;'>{$message}</textarea></div>
						<div class='option2'><input type='checkbox' name='secret' id='secret' value='1'{$secret} style='margin-bottom:2px;' /> <label for='secret'>Staff Only Reply</label></div>
						<div class='formtail'><input type='submit' name='submit' id='edit' value='Edit Reply' class='button' /></div>
						</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets'>Tickets</a>",
						   "Edit Reply",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Tickets' ) );
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

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( is_array( unserialize( $this->ifthd->member['g_depart_perm'] ) ) )
		{
			$rev_perms = array(); // Initialize for Security

			foreach( unserialize( $this->ifthd->member['g_depart_perm'] ) as $did => $access )
			{
				if ( $access == 1 ) $rev_perms[] = $did;
			}

			$sql_where = array( array( 'id', '=', $this->ifthd->input['id'] ), array( 'did', 'in', $rev_perms, 'and' ) );
		}
		else
		{
			$sql_where = array( 'id', '=', $this->ifthd->input['id'] );
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'tickets',
							 				  	 'where'	=> $sql_where,
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_ticket');
		}

		$t = $this->ifthd->core->db->fetch_row();

		#=============================
		# Custom Department Fields
		#=============================

		if ( is_array( $this->ifthd->core->cache['dfields'] ) )
		{
			$cdfields = array(); // Initialize for Security

			$cdfdata = unserialize( $t['cdfields'] );

			foreach( $this->ifthd->core->cache['dfields'] as $id => $f )
			{
				$f_perm = unserialize( $f['departs'] );

				if ( $f_perm[ $t['did'] ] )
				{
					if ( ! $f['required'] )
					{
						$f['optional'] = $this->ifthd->lang['optional'];
					}

					if ( $error )
					{
						$f['value'] = $this->ifthd->input[ 'cdf_'. $f['fkey'] ];
					}
					else
					{
						$f['value'] = $cdfdata[ $f['fkey'] ];
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
		}

		#=============================
		# Do Output
		#=============================

		if ( $error )
		{
			$error = "<div class='critical'>". $error ."</div>";

			$subject = $this->ifthd->input['subject'];
			$priority = $this->ifthd->input['priority'];
			$message = $this->ifthd->input['message'];
		}
		else
		{
			$subject = $t['subject'];
			$priority = $t['priority'];
			$message = $t['message'];
		}

		$this->output = "<script type='text/javascript'>

						function validate_form(form)
						{
							if ( ! form.message.value )
							{
								alert('Please enter a message.');
								form.message.focus();
								return false;
							}
						}

						</script>
						{$error}
						<div class='groupbox'>Editing Ticket: ". $t['subject'] ."</div>
						<form action='<! HD_URL !>/admin.php?section=manage&amp;act=ticket&amp;code=doedit&amp;id={$t['id']}' method='post' onsubmit='return validate_form(this)'>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<td class='option1' width='15%'><label for='subject'>Subject</label></td>
							<td class='option1' width='85%'><input type='text' name='subject' id='subject' value='{$subject}' size='35' /></td>
						</tr>
						<tr>
							<td class='option2'><label for='priority'>Priority</label></td>
							<td class='option2'><select name='priority' id='priority'>". $this->ifthd->build_priority_drop( $priority ) ."</select></td>
						</tr>";

		if ( $cdfields )
		{
			$row_count = 0;

			foreach( $cdfields as $cdf )
			{
				$row_count ++;

				( $row_count & 1 ) ? $row_class = 'option1' : $row_class = 'option2';

				if ( $cdf['type'] == 'textfield' )
				{
					$this->output .= "<tr>
							<td class='{$row_class}'><label for='cdf_{$cdf['fkey']}'>{$cdf['name']}</label></td>
							<td class='{$row_class}'><input type='text' name='cdf_{$cdf['fkey']}' id='cdf_{$cdf['fkey']}' value='{$cdf['value']}' size='45' /> {$cdf['optional']}</td>
						</tr>";
				}
				elseif ( $cdf['type'] == 'textarea' )
				{
					$this->output .= "<tr>
							<td class='{$row_class}'><label for='cdf_{$cdf['fkey']}'>{$cdf['name']}</label></td>
							<td class='{$row_class}'><textarea name='cdf_{$cdf['fkey']}' id='cdf_{$cdf['fkey']}' cols='50' rows='3'>{$cdf['value']}</textarea> {$cdf['optional']}</td>
						</tr>";
				}
				elseif ( $cdf['type'] == 'dropdown' )
				{
					$this->output .= "<tr>
							<td class='{$row_class}'><label for='cdf_{$cdf['fkey']}'>{$cdf['name']}</label></td>
							<td class='{$row_class}'><select name='cdf_{$cdf['fkey']}' id='cdf_{$cdf['fkey']}'>{$cdf['options']}</select> {$cdf['optional']}</td>
						</tr>";
				}
				elseif ( $cdf['type'] == 'checkbox' )
				{
					$this->output .= "<tr>
							<td class='{$row_class}'><label for='cdf_{$cdf['fkey']}'>{$cdf['name']}</label></td>
							<td class='{$row_class}'><input type='checkbox' name='cdf_{$cdf['fkey']}' id='cdf_{$cdf['fkey']}' value='1' class='ckbox'";

					if ( $cdf['value'] ) $this->output .= " checked='checked'";

					$this->output .= " />";

					if ( $cdf['extra'] ) $this->output .= "<label for='cdf_{$cdf['fkey']}'>{$cdf['extra']}</label>";

					$this->output .= "</td>
						</tr>";
				}
				elseif ( $cdf['type'] == 'radio' )
				{
					$this->output .= "<tr>
							<td class='{$row_class}'>{$cdf['name']}</td>
							<td class='{$row_class}'>{$cdf['options']} {$cdf['optional']}</td>
						</tr>";
				}
			}
		}

		$row_count ++;

		( $row_count & 1 ) ? $row_class = 'option1' : $row_class = 'option2';

		$this->output .= "<tr>
							<td colspan='2' class='{$row_class}'><textarea name='message' id='message' rows='10' cols='120' style='width: 98%; height: 180px;'>{$message}</textarea></td>
						</tr>
						</table>
						<div class='formtail'><input type='submit' name='submit' id='edit' value='Edit Ticket' class='button' /></div>
						</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets'>Tickets</a>",
						   $t['subject'],
						   "Edit Ticket",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Tickets' ) );

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
						   $t['subject'],
						   $this->ifthd->lang['edit'],
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

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->input['subject'] )
		{
			$this->edit_ticket('Please enter a subject.');
		}

		if ( ! $this->ifthd->input['message'] )
		{
			$this->edit_ticket('Please enter a message.');
		}

		if ( is_array( unserialize( $this->ifthd->member['g_depart_perm'] ) ) )
		{
			$rev_perms = array(); // Initialize for Security

			foreach( unserialize( $this->ifthd->member['g_depart_perm'] ) as $did => $access )
			{
				if ( $access == 1 ) $rev_perms[] = $did;
			}

			$sql_where = array( array( 'id', '=', $this->ifthd->input['id'] ), array( 'did', 'in', $rev_perms, 'and' ) );
		}
		else
		{
			$sql_where = array( 'id', '=', $this->ifthd->input['id'] );
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'tickets',
							 				  	 'where'	=> $sql_where,
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_ticket');
		}

		$t = $this->ifthd->core->db->fetch_row();

		#=============================
		# Custom Department Fields
		#=============================

		if ( is_array( $this->ifthd->core->cache['dfields'] ) )
		{
			$cdfvalues = ""; // Initialize for Security
			$cdfields_html = "";  // Initialize for Security

			foreach ( $this->ifthd->core->cache['dfields'] as $id => $f )
			{
				$f_perm = unserialize( $f['departs'] );

				if ( $f_perm[ $t['did'] ] )
				{
					if ( $f['required'] && $f['type'] != 'checkbox' )
					{
						if ( ! $this->ifthd->input[ 'cdf_'. $f['fkey'] ] )
						{
							$this->edit_ticket( 'Please enter a value for the field: '. $f['name'] );
						}
					}

					$cdfvalues[ $f['fkey'] ] = $this->ifthd->input[ 'cdf_'. $f['fkey'] ];
				}
			}
		}

		#=============================
		# Edit Ticket
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'tickets',
											  	 'set'		=> array( 'subject' => $this->ifthd->input['subject'], 'priority' => $this->ifthd->input['priority'], 'message' => $this->ifthd->input['message'], 'cdfields' => serialize( $cdfvalues ) ),
							 				  	 'where'	=> array( 'id', '=', $t['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'admin', "Edited Ticket ID #". $t['id'], 1, $t['id'] );
		$this->ifthd->log( 'ticket', "Edited Ticket ID #". $t['id'], 1, $t['id'] );

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=tickets&code=view&id='. $t['id'], 'ticket_edit_success' );

		$this->ifthd->input['message'] = "";

		$this->view_ticket( '', '', 'The ticket has been successfully updated.' );
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

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_ticket_reply'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->input['message'] = $this->ifthd->remove_extra_lbs( $this->ifthd->input['message'] );

		if ( ! $this->ifthd->input['message'] )
		{
			$this->edit_reply('Please enter a message.');
		}

		#=============================
		# Grab Reply
		#=============================

		if ( is_array( unserialize( $this->ifthd->member['g_depart_perm'] ) ) )
		{
			$rev_perms = array(); // Initialize for Security

			foreach( unserialize( $this->ifthd->member['g_depart_perm'] ) as $did => $access )
			{
				if ( $access == 1 ) $rev_perms[] = $did;
			}

			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'r' => 'all', 't' => array( 'did', 'dname' ) ),
												  	 'from'		=> array( 'r' => 'replies' ),
												  	 'join'		=> array( array( 'from' => array( 't' => 'tickets' ), 'where' => array( 'r' => 'tid', '=', 't' => 'id' ) ) ),
								 				  	 'where'	=> array( array( array( 'r' => 'id' ), '=', $this->ifthd->input['id'] ), array( array( 't' => 'did' ), 'in', $rev_perms, 'and' ) ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);
		}
		else
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'r' => 'all', 't' => array( 'did', 'dname' ) ),
												  	 'from'		=> array( 'r' => 'replies' ),
												  	 'join'		=> array( array( 'from' => array( 't' => 'tickets' ), 'where' => array( 'r' => 'tid', '=', 't' => 'id' ) ) ),
								 				  	 'where'	=> array( array( 'r' => 'id' ), '=', $this->ifthd->input['id'] ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);
		}

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_reply');
		}

		$r = $this->ifthd->core->db->fetch_row();

		#=============================
		# Update Reply
		#=============================

		$db_array = array(
						  'message'		=> $this->ifthd->input['message'],
						  'rte'			=> intval( $this->ifthd->input['rte'] ),
						  'secret'		=> intval( $this->ifthd->input['secret'] ),
						 );

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'replies',
											  	 'set'		=> $db_array,
							 				  	 'where'	=> array( 'id', '=', $r['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'admin', "Edited Ticket Reply ID #". $r['id'], 1, $r['id'] );
		$this->ifthd->log( 'ticket', "Edited Ticket Reply ID #". $r['id'], 1, $r['tid'] );

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=tickets&code=view&id='. $r['tid'] .'#reply'. $r['id'], 'reply_edit_success' );

		$this->ifthd->input['id'] = $r['tid'];
		$this->ifthd->input['message'] = "";

		$this->view_ticket( '', '', 'The reply has been successfully updated.' );
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

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_ticket_reply'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		#=============================
		# Grab Reply
		#=============================

		if ( is_array( unserialize( $this->ifthd->member['g_depart_perm'] ) ) )
		{
			$rev_perms = array(); // Initialize for Security

			foreach( unserialize( $this->ifthd->member['g_depart_perm'] ) as $did => $access )
			{
				if ( $access == 1 ) $rev_perms[] = $did;
			}

			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'r' => 'all', 't' => array( 'did', 'dname' ) ),
												  	 'from'		=> array( 'r' => 'replies' ),
												  	 'join'		=> array( array( 'from' => array( 't' => 'tickets' ), 'where' => array( 'r' => 'tid', '=', 't' => 'id' ) ) ),
								 				  	 'where'	=> array( array( array( 'r' => 'id' ), '=', $this->ifthd->input['id'] ), array( array( 't' => 'did' ), 'in', $rev_perms, 'and' ) ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);
		}
		else
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'r' => 'all', 't' => array( 'did', 'dname' ) ),
												  	 'from'		=> array( 'r' => 'replies' ),
												  	 'join'		=> array( array( 'from' => array( 't' => 'tickets' ), 'where' => array( 'r' => 'tid', '=', 't' => 'id' ) ) ),
								 				  	 'where'	=> array( array( 'r' => 'id' ), '=', $this->ifthd->input['id'] ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);
		}

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
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

		$this->ifthd->log( 'admin', "Deleted Ticket Reply ID #". $r['id'], 2, $r['id'] );
		$this->ifthd->log( 'ticket', "Deleted Ticket Reply ID #". $r['id'], 2, $r['tid'] );

		#=============================
		# Update Ticket
		#=============================

		if ( ! $r['secret'] )
		{
			$this->ifthd->core->db->next_no_quotes('set');

			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'tickets',
												  	 'set'		=> array( 'replies' => 'replies-1' ),
								 				  	 'where'	=> array( 'id', '=', $r['tid'] ),
								 		  	  ) 	);

			$this->ifthd->core->db->next_shutdown();
			$this->ifthd->core->db->execute();
		}

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=tickets&code=view&id='. $r['tid'], 'reply_delete_success' );

		$this->ifthd->input['id'] = $r['tid'];

		$this->view_ticket( '', 'The reply has been successfully deleted.' );
	}

	#=======================================
	# @ Assign Ticket
	# Assign ticket to staff member.
	#=======================================

	function assign_ticket()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['acp']['manage_ticket_assign_self'] && ! $this->ifthd->member['acp']['manage_ticket_assign_any'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->input['tid'] = intval( $this->ifthd->input['tid'] );
		$this->ifthd->input['mid'] = intval( $this->ifthd->input['mid'] );

		if ( is_array( unserialize( $this->ifthd->member['g_depart_perm'] ) ) )
		{
			$rev_perms = array(); // Initialize for Security

			foreach( unserialize( $this->ifthd->member['g_depart_perm'] ) as $did => $access )
			{
				if ( $access == 1 ) $rev_perms[] = $did;
			}

			$sql_where = array( array( 'id', '=', $this->ifthd->input['tid'] ), array( 'did', 'in', $rev_perms, 'and' ) );
		}
		else
		{
			$sql_where = array( 'id', '=', $this->ifthd->input['tid'] );
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'tickets',
							 				  	 'where'	=> $sql_where,
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_ticket');
		}

		$t = $this->ifthd->core->db->fetch_row();

		#=============================
		# Grab Staff Member
		#=============================

		if ( $this->ifthd->input['mid'] )
		{
			if ( $this->ifthd->member['id'] == $this->ifthd->input['mid'] )
			{
				$assign_mid = $this->ifthd->member['id'];
				$assign_mname = $this->ifthd->member['name'];
				$assign_count = $this->ifthd->member['assigned'] + 1;
			}
			else
			{
				if ( ! $this->ifthd->member['acp']['manage_ticket_assign_any'] )
				{
					$this->ifthd->skin->error('no_perm');
				}

				$this->ifthd->core->db->construct( array(
													  	 'select'	=> array( 'm' => array( 'id', 'name', 'assigned' ), 'g' => array( 'g_depart_perm' ) ),
													  	 'from'		=> array( 'm' => 'members' ),
													  	 'join'		=> array( array( 'from' => array( 'g' => 'groups' ), 'where' => array( 'g' => 'g_id', '=', 'm' => 'mgroup' ) ) ),
									 				  	 'where'	=> array( array( array( 'm' => 'id' ), '=', $this->ifthd->input['mid'] ), array( array( 'g' => 'g_acp_access' ), '=', 1, 'and' ) ),
									 				  	 'limit'	=> array( 0, 1 ),
									 		  	  ) 	);

				$this->ifthd->core->db->execute();

				if ( ! $this->ifthd->core->db->get_num_rows() )
				{
					$this->ifthd->skin->error('no_member');
				}

				$m = $this->ifthd->core->db->fetch_row();

				// Check Permissions
				if ( is_array( unserialize( $m['g_depart_perm'] ) ) )
				{
					$staff_perms = unserialize( $m['g_depart_perm'] );

					if ( ! $staff_perms[ $t['did'] ] )
					{
						$this->ifthd->skin->error('no_member');
					}
				}

				$assign_mid = $m['id'];
				$assign_mname = $m['name'];
				$assign_count = $m['assigned'] + 1;
			}
		}
		else
		{
			$assign_mid = 0;
			$assign_mname = '';
		}

		#=============================
		# Update Ticket
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'tickets',
											  	 'set'		=> array( 'amid' => $assign_mid, 'amname' => $assign_mname ),
							 				  	 'where'	=> array( 'id', '=', $t['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( $assign_mid )
		{
			$this->ifthd->log( 'admin', "Assigned Ticket ID #". $t['id'] .": ". $assign_mname, 1, $t['id'] );
			$this->ifthd->log( 'ticket', "Assigned Ticket ID #". $t['id'] .": ". $assign_mname, 1, $t['tid'] );
		}
		else
		{
			$this->ifthd->log( 'admin', "Unassigned Ticket ID #". $t['id'], 1, $t['id'] );
			$this->ifthd->log( 'ticket', "Unassigned Ticket ID #". $t['id'], 1, $t['tid'] );
		}

		#=============================
		# Update Member
		#=============================

		if ( $assign_mid )
		{
			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'members',
												  	 'set'		=> array( 'assigned' => $assign_count ),
								 				  	 'where'	=> array( 'id', '=', $assign_mid ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();
		}

		#=============================
		# Update Old Member
		#=============================

		if ( $t['amid'] )
		{
			$this->ifthd->core->db->next_no_quotes('set');

			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'members',
												  	 'set'		=> array( 'assigned' => 'assigned-1' ),
								 				  	 'where'	=> array( 'id', '=', $t['amid'] ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();
		}

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_staff_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=tickets&code=view&id='. $t['id'], 'ticket_edit_success' );

		$this->ifthd->input['id'] = $this->ifthd->input['tid'];

		if ( $assign_mid )
		{
			$this->view_ticket( '', '', 'The ticket has been successfully assigned.' );
		}
		else
		{
			$this->view_ticket( '', '', 'The ticket has been successfully unassigned.' );
		}
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

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( is_array( unserialize( $this->ifthd->member['g_depart_perm'] ) ) )
		{
			$rev_perms = array(); // Initialize for Security

			foreach( unserialize( $this->ifthd->member['g_depart_perm'] ) as $did => $access )
			{
				if ( $access == 1 ) $rev_perms[] = $did;
			}

			$sql_where = array( array( array( 'a' => 'id' ), '=', $this->ifthd->input['id'] ), array( array( 't' => 'did' ), 'in', $rev_perms, 'and' ) );
		}
		else
		{
			$sql_where = array( array( 'a' => 'id' ), '=', $this->ifthd->input['id'] );
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'a' => 'all',
											  	 					  't' => array( 'subject' ),
											  	 					 ),
											  	 'from'		=> array( 'a' => 'attachments' ),
											  	 'join'		=> array( array( 'from' => array( 't' => 'tickets' ), 'where' => array( 'a' => 'tid', '=', 't' => 'id' ) ) ),
							 				  	 'where'	=> $sql_where,
							 		  	  ) 	);

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

	#=======================================
	# @ Do Multi
	# Do multiple tickets action.
	#=======================================

	function do_multi()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['acp'][ 'manage_ticket_'. $this->ifthd->input['multi_action'] ] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		#=============================
		# Department Security
		#=============================

		$db_array = array(
					  	  'select'	=> array( 'id', 'status' ),
					  	  'from'	=> 'tickets',
					  	 );

		if ( is_array( unserialize( $this->ifthd->member['g_depart_perm'] ) ) )
		{
			$rev_perms = array(); // Initialize for Security

			foreach( unserialize( $this->ifthd->member['g_depart_perm'] ) as $did => $access )
			{
				if ( $access == 1 ) $rev_perms[] = $did;
			}

			$db_array['where'] = array( array( 'did', 'in', $rev_perms ), array( 'id', 'in', unserialize( $this->ifthd->convert_html( $this->ifthd->input['ticket_ids'] ) ), 'and' ) );
		}
		else
		{
			$db_array['where'] = array( 'id', 'in', unserialize( $this->ifthd->convert_html( $this->ifthd->input['ticket_ids'] ) ) );
		}

		#=============================
		# Grab Tickets
		#=============================

		$this->ifthd->core->db->construct( $db_array );

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_ticket');
		}

		while( $t = $this->ifthd->core->db->fetch_row() )
		{
			if ( $this->ifthd->input[ 'tcb_'. $t['id'] ] )
			{
				$do_tickets[ $t['id'] ] = $t;
			}
		}

		if ( ! $do_tickets )
		{
			$this->ifthd->skin->error('no_mm_tickets');
		}

		#=============================
		# Filter Tickets
		#=============================

		foreach ( $do_tickets as $tid => $tdata )
		{
			if ( $this->ifthd->input['multi_action'] == 'hold' )
			{
				if ( $tdata['status'] != 6 && $tdata['status'] != 3 )
				{
					$final_tickets[ $tid ] = $tdata;
				}
			}
			elseif ( $this->ifthd->input['multi_action'] == 'close' )
			{
				if ( $tdata['status'] != 6 )
				{
					$final_tickets[ $tid ] = $tdata;
				}
			}
			elseif ( $this->ifthd->input['multi_action'] == 'reopen' )
			{
				if ( $tdata['status'] == 6 )
				{
					$final_tickets[ $tid ] = $tdata;
				}
			}
			else
			{
				$final_tickets[ $tid ] = $tdata;
			}
		}

		if ( ! $final_tickets )
		{
			$this->ifthd->skin->error('no_mm_valid_tickets');
		}

		#=============================
		# Add Another Step? =/
		#=============================

		if ( $this->ifthd->input['multi_action'] == 'move' )
		{
			if ( ! $this->ifthd->input['move_to'] )
			{
				if ( $this->ifthd->input['final'] )
				{
					$error = "<div class='critical'>Please select a department.</div>";
				}

				$depart_drop = $this->ifthd->build_dprt_drop( 0, 0, 1 );
				$raw_ticket_ids = array(); // Initialize for Security

				foreach ( $final_tickets as $itid => $itdata )
				{
					$ticket_ckbxes .= "<input type='hidden' name='tcb_{$itid}' value='1' />\n";
					$raw_ticket_ids[] = $itid;
				}

				$this->output = "{$error}<div class='groupbox'>Moving Tickets</div>
								<form action='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=multi&amp;multi_action=move&amp;start={$this->ifthd->input['start']}&amp;final=1' method='post'>
								<input type='hidden' name='ticket_ids' value='". serialize( $raw_ticket_ids ) ."' />
								{$ticket_ckbxes}
								<div class='option1'>Move to Department: <select name='move_to' id='move_to'>{$depart_drop}</select></div>
								<div class='formtail'><input type='submit' name='submit' id='move' value='Move Tickets' class='button' /></duv>
								</form>";

				$this->ifthd->skin->add_output( $this->output );

				$this->nav = array(
								   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
								   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets'>Tickets</a>",
								   "Move Tickets",
								   );

				$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Tickets' ) );
			}
		}

		#=============================
		# Finally, Do Action :D
		#=============================

		$implode_tickets = array(); // Initialize for Security

		foreach ( $final_tickets as $t_tid => $t_tdata )
		{
			$implode_tickets[] = $t_tid;
		}

		if ( $this->ifthd->input['multi_action'] == 'hold' )
		{

			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'tickets',
												  	 'set'		=> array( 'status' => 3 ),
								 				  	 'where'	=> array( 'id', 'in', $implode_tickets ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$this->ifthd->r_ticket_stats(1);
		}
		elseif ( $this->ifthd->input['multi_action'] == 'move' )
		{
			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'tickets',
											  	 	 'set'		=> array( 'did' => $this->ifthd->input['move_to'], 'dname' => $this->ifthd->core->cache['depart'][ $this->ifthd->input['move_to'] ]['name'] ),
								 				  	 'where'	=> array( 'id', 'in', $implode_tickets ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$this->ifthd->r_tickets_per_dept(1);
		}
		elseif ( $this->ifthd->input['multi_action'] == 'close' )
		{
			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'tickets',
												  	 'set'		=> array( 'close_mid' => $this->ifthd->member['id'], 'close_mname' => $this->ifthd->member['name'], 'status' => 6 ),
								 				  	 'where'	=> array( 'id', 'in', $implode_tickets ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$this->ifthd->r_ticket_stats(1);

			$this->ifthd->r_tickets_per_member(1);
		}
		elseif ( $this->ifthd->input['multi_action'] == 'delete' )
		{
			$this->ifthd->core->db->construct( array(
												  	 'delete'	=> 'tickets',
								 				  	 'where'	=> array( 'id', 'in', $implode_tickets ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$this->ifthd->r_ticket_stats(1);

			$this->ifthd->r_tickets_per_member(1);

			$this->ifthd->r_tickets_per_dept(1);
		}
		elseif ( $this->ifthd->input['multi_action'] == 'reopen' )
		{
			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'tickets',
												  	 'set'		=> array( 'close_reason' => "", 'status' => 1 ),
								 				  	 'where'	=> array( 'id', 'in', $implode_tickets ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$this->ifthd->r_ticket_stats(1);

			$this->ifthd->r_tickets_per_member(1);
		}

		#=============================
		# Redirect
		#=============================

		$this->ifthd->skin->redirect( '?section=manage&act=ticket', 'ticket_multi_success' );
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

	#=======================================
	# @ Build Search Drop
	# Builds a search field drop-down list.
	#=======================================

	function build_search_drop($select='')
    {
    	$fields = array( 'id' => 'Ticket ID', 'subject' => 'Subject', 'message' => 'Message', 'mname' => 'Submitted By', 'email' => 'Email' );
    	
    	$ift_html = "<select name='field' id='field'>";
    	
    	foreach( $fields as $id => $name )
    	{
    		if ( $select == $id )
    		{
    			$ift_html .= "<option value='{$id}' selected='yes'>{$name}</option>";
    		}
    		else
    		{
    			$ift_html .= "<option value='{$id}'>{$name}</option>";
    		}
    	}
	
		$ift_html .= "</select>";
		
		return $ift_html;
	}

}

?>