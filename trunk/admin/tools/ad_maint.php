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
|    | Admin Maintenance
#======================================================
*/

class ad_maint {

	#=======================================
	# @ Auto Run
	# Function that is run automatically
	# when the file is required.
	#=======================================

	function auto_run()
	{
		if ( ! $this->ifthd->member['acp']['tools_maint'] )
		{
			$this->ifthd->skin->error('no_perm');
		}
		
		$this->ifthd->skin->set_section( 'Tools &amp; Maintenance' );		
		$this->ifthd->skin->set_description( 'Run maintenance utilities, recount functions, cleaning utilities and backup functions.' );

		switch( $this->ifthd->input['code'] )
    	{
    		case 'recount':
				$this->show_recount();
    		break;
    		case 'rebuild':
				$this->show_rebuild();
    		break;
    		case 'clean':
    			$this->show_clean();
    		break;
    		case 'syscheck':
				$this->syscheck();
    		break;

    		case 'dorecount':
				$this->do_recount();
    		break;
    		case 'doclean':
				$this->do_clean();
    		break;

    		default:
    			$this->show_recount();
    		break;
		}
	}

	#=======================================
	# @ Show Recount
	# Show the recount page.
	#=======================================

	function show_recount($alert='')
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['acp']['tools_maint_recount'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		#=============================
		# Do Output
		#=============================
		
		if ( $alert )
		{
			$alert = "<div class='alert'>{$alert}</div>";
		}

		$this->output = "{$alert}
						<div class='groupbox'>Recount Functions</div>
						<div class='subbox'>Please select a task below.</div>
						<div class='option1'><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=dorecount&amp;type=rticket'>Ticket Statistics</a> <span class='desc'>-- Rebuild ticket statistics such as total tickets, etc.</span></div>
						<div class='option2'><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=dorecount&amp;type=rkb'>Knowledge Base Statistics</a> <span class='desc'>-- Rebuild knowledge base statistics such as article count, etc.</span></div>
						<div class='option1'><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=dorecount&amp;type=rmember'>Member Statistics</a> <span class='desc'>-- Rebuild member statistics such as total member count, etc.</span></div>
						<div class='option2'><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=dorecount&amp;type=rreplies'>Replies Per Ticket</a> <span class='desc'>-- The number of replies for each ticket.</span></div>
						<div class='option1'><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=dorecount&amp;type=rmemtick'>Tickets Per Member</a> <span class='desc'>-- The number of tickets for each member.</span></div>
						<div class='option2'><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=dorecount&amp;type=rdeptick'>Tickets Per Department</a> <span class='desc'>-- The number of tickets for each department.</span></div>
						<div class='option1'><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=dorecount&amp;type=rtassign'>Assigned Tickets</a> <span class='desc'>-- The number of open tickets assigned to each staff member.</span></div>
						<div class='option2'><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=dorecount&amp;type=racomments'>Article Comments</a> <span class='desc'>-- The number of comments for each article.</span></div>
						<div class='option1'><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=dorecount&amp;type=rncomments'>News Comments</a> <span class='desc'>-- The number of comments for each announcement.</span></div>
						<div class='option2'><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=dorecount&amp;type=rmembers'>Members</a> <span class='desc'>-- The number of members for each group, etc.</span></div>
						<div class='option1'><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=dorecount&amp;type=rartrate'>Article Ratings</a> <span class='desc'>-- The rating value for each article.</span></div>
						<div class='option2'><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=dorecount&amp;type=rsettings'>Settings</a> <span class='desc'>-- The number of settings per settings group.</span></div>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=tools'>Tools</a>",
						   "<a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint'>Maintenance</a>",
						   "Recount Functions",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Maintenance' ) );
	}

	#=======================================
	# @ Show Rebuild
	# Show the rebuild page.
	#=======================================

	function show_rebuild($alert='')
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['acp']['tools_maint_recount'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		#=============================
		# Do Output
		#=============================
		
		if ( $alert )
		{
			$alert = "<div class='alert'>{$alert}</div>";
		}

		$this->output = "{$alert}
						<div class='groupbox'>Rebuild Functions</div>
						<div class='subbox'>Please select a function below to rebuild.</div>
						<div class='option1'><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=dorecount&amp;type=cannounce'>Announcement Cache</a></div>
						<div class='option1'><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=dorecount&amp;type=ccanned'>Canned Replies Cache</a></div>
						<div class='option1'><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=dorecount&amp;type=ckbcat'>Category Cache</a></div>
						<div class='option1'><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=dorecount&amp;type=ccdfields'>Custom Department Fields Cache</a></div>
						<div class='option1'><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=dorecount&amp;type=ccpfields'>Custom Profile Fields Cache</a></div>
						<div class='option1'><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=dorecount&amp;type=cdepart'>Department Cache</a></div>
						<div class='option1'><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=dorecount&amp;type=cgroup'>Group Cache</a></div>
						<div class='option1'><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=dorecount&amp;type=clang'>Language Cache</a></div>
						<div class='option1'><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=dorecount&amp;type=cconfig'>Settings Cache</a></div>
						<div class='option1'><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=dorecount&amp;type=cskin'>Skin Cache</a></div>
						<div class='option1'><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=dorecount&amp;type=cstaff'>Staff Cache</a></div>
						<div class='option1'><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=dorecount&amp;type=cchmod'>CHMOD Cache Files</a> <span class='desc'>-- CHMOD all cache files to 0777.</span></div>
						<div class='option1'><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=dorecount&amp;type=cloginkeys'>Login Keys</a> <span class='desc'>-- Regenerate login keys for all members.</span></div>
						<div class='option1'><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=dorecount&amp;type=crsskeys'>RSS Keys</a> <span class='desc'>-- Regenerate RSS keys for all members.</span></div>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=tools'>Tools</a>",
						   "<a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint'>Maintenance</a>",
						   "Rebuild Functions",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Maintenance' ) );
	}

	#=======================================
	# @ Show Clean
	# Show the Spring cleaning page. :D
	#=======================================

	function show_clean($alert='')
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['acp']['tools_maint_clean'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		#=============================
		# Do Output
		#=============================
		
		if ( $alert )
		{
			$alert = "<div class='alert'>{$alert}</div>";
		}

		$this->output = "{$alert}
						<div class='groupbox'>Spring Cleaning</div>
						<div class='subbox'>Please select the checkbox next to each action you wish to perform.  Then click Start Cleaning.</div>
						<form action='<! HD_URL !>/admin.php?section=tools&amp;act=clean&amp;code=doclean' method='post' onsubmit='return validate_form(this)'>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<td class='option1' width='5%' align='center'><input type='checkbox' name='del_old_tickets' value='1' class='ckbox' /></td>
							<td class='option1' width='95%'>Delete all tickets older than <input type='text' name='dot_days' id='dot_days' value='{$this->ifthd->input['dot_days']}' size='3' /> days.</td>
						</tr>
						<tr>
							<td class='option2' align='center'><input type='checkbox' name='del_old_comments' value='1' class='ckbox' /></td>
							<td class='option2' width='95%'>Delete all comments older than <input type='text' name='doc_days' id='doc_days' value='{$this->ifthd->input['doc_days']}' size='3' /> days.</td>
						</tr>
						<tr>
							<td class='option1' align='center'><input type='checkbox' name='del_unapproved_mem' value='1' class='ckbox' /></td>
							<td class='option1'>Delete all validating members who have been registered for more than <input type='text' name='dum_days' id='dum_days' value='{$this->ifthd->input['dum_days']}' size='3' /> days.</td>
						</tr>
						<tr>
							<td class='option1' align='center'><input type='checkbox' name='delete_core_logs' value='1' class='ckbox' /></td>
							<td class='option1'>Delete all A5 Core logs.</td>
						</tr>
						<tr>
							<td class='option2' align='center'><input type='checkbox' name='delete_tmp_files' value='1' class='ckbox' /></td>
							<td class='option2'>Delete all A5 Core temporary files.</td>
						</tr>
						<tr>
							<td class='option1' align='center'><input type='checkbox' name='del_logs_admin' value='1' class='ckbox' /></td>
							<td class='option1'>Delete admin logs older than <input type='text' name='dla_days' id='dla_days' value='{$this->ifthd->input['dla_days']}' size='3' /> days.</td>
						</tr>
						<tr>
							<td class='option2' align='center'><input type='checkbox' name='del_logs_mem' value='1' class='ckbox' /></td>
							<td class='option2'>Delete member logs older than <input type='text' name='dlm_days' id='dlm_days' value='{$this->ifthd->input['dlm_days']}' size='3' /> days.</td>
						</tr>
						<tr>
							<td class='option1' align='center'><input type='checkbox' name='del_logs_error' value='1' class='ckbox' /></td>
							<td class='option1'>Delete error logs older than <input type='text' name='dle_days' id='dle_days' value='{$this->ifthd->input['dle_days']}' size='3' /> days.</td>
						</tr>
						<tr>
							<td class='option2' align='center'><input type='checkbox' name='del_logs_sec' value='1' class='ckbox' /></td>
							<td class='option2'>Delete security logs older than <input type='text' name='dls_days' id='dls_days' value='{$this->ifthd->input['dls_days']}' size='3' /> days.</td>
						</tr>
						<tr>
							<td class='option1' align='center'><input type='checkbox' name='del_logs_tick' value='1' class='ckbox' /></td>
							<td class='option1'>Delete ticket logs older than <input type='text' name='dlt_days' id='dlt_days' value='{$this->ifthd->input['dlt_days']}' size='3' /> days.</td>
						</tr>
						<tr>
							<td class='option2' align='center'><input type='checkbox' name='kill_asessions' value='1' class='ckbox' /></td>
							<td class='option2'>Kill all administrative sessions (you will be logged out).</td>
						</tr>
						<tr>
							<td class='option1' align='center'><input type='checkbox' name='kill_sessions' value='1' class='ckbox' /></td>
							<td class='option1'>Kill all user sessions.</td>
						</tr>
						</table>
						<div class='formtail'><input type='submit' name='submit' id='clean' value='Start Cleaning' class='button' /></div>
						</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=tools'>Tools</a>",
						   "<a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint'>Maintenance</a>",
						   "Spring Cleaning",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Maintenance' ) );
	}

	#=======================================
	# @ Do Recount
	# Perform the appropriate task to
	# recount or rebuild.
	#=======================================

	function do_recount()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['acp']['tools_maint_recount'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		#=============================
		# Perform Our Action
		#=============================

		if ( substr( $this->ifthd->input['type'], 0, 1 ) == 'c' )
		{
			if ( $this->ifthd->input['type'] == 'cannounce' )
			{
				$this->ifthd->rebuild_announce_cache();

				$this->ifthd->log( 'admin', "Announcement Cache Rebuilt" );
			}
			elseif ( $this->ifthd->input['type'] == 'ccanned' )
			{
				$this->ifthd->rebuild_canned_cache();

				$this->ifthd->log( 'admin', "Canned Replies Cache Rebuilt" );
			}
			elseif ( $this->ifthd->input['type'] == 'ckbcat' )
			{
				$this->ifthd->rebuild_cat_cache();

				$this->ifthd->log( 'admin', "Category Cache Rebuilt" );
			}
			elseif ( $this->ifthd->input['type'] == 'cdepart' )
			{
				$this->ifthd->rebuild_dprt_cache();

				$this->ifthd->log( 'admin', "Department Cache Rebuilt" );
			}
			elseif ( $this->ifthd->input['type'] == 'cgroup' )
			{
				$this->ifthd->rebuild_group_cache();

				$this->ifthd->log( 'admin', "Group Cache Rebuilt" );
			}
			elseif ( $this->ifthd->input['type'] == 'clang' )
			{
				$this->ifthd->rebuild_lang_cache();

				$this->ifthd->log( 'admin', "Language Cache Rebuilt" );
			}
			elseif ( $this->ifthd->input['type'] == 'cskin' )
			{
				$this->ifthd->rebuild_skin_cache();

				$this->ifthd->log( 'admin', "Skin Cache Rebuilt" );
			}
			elseif ( $this->ifthd->input['type'] == 'cstaff' )
			{
				$this->ifthd->rebuild_staff_cache();

				$this->ifthd->log( 'admin', "Staff Cache Rebuilt" );
			}
			elseif ( $this->ifthd->input['type'] == 'cconfig' )
			{
				$this->ifthd->rebuild_set_cache();

				$this->ifthd->log( 'admin', "Settings Cache Rebuilt" );
			}
			elseif ( $this->ifthd->input['type'] == 'ccdfields' )
			{
				$this->ifthd->rebuild_dfields_cache();

				$this->ifthd->log( 'admin', "Custom Department Fields Cache Rebuilt" );
			}
			elseif ( $this->ifthd->input['type'] == 'ccpfields' )
			{
				$this->ifthd->rebuild_pfields_cache();

				$this->ifthd->log( 'admin', "Custom Profile Fields Cache Rebuilt" );
			}
			elseif ( $this->ifthd->input['type'] == 'cchmod' )
			{
				$this->ifthd->core->chmod_cache();

				$this->ifthd->log( 'admin', "Cache Files CHMOD to 0777" );
			}
			elseif ( $this->ifthd->input['type'] == 'crsskeys' )
			{
				$this->r_rss_keys();
			}
			elseif ( $this->ifthd->input['type'] == 'cloginkeys' )
			{
				$this->r_login_keys();
			}

			#$this->ifthd->skin->redirect( '?section=tools&act=maint&code=rebuild', 'cache_rebuilt' );
			$this->show_rebuild( 'The rebuild function has been successfully run.' );
		}
		elseif ( substr( $this->ifthd->input['type'], 0, 1 ) == 'r' )
		{

			if ( $this->ifthd->input['type'] == 'rticket' )
			{
				$this->ifthd->r_ticket_stats();

				$this->ifthd->log( 'admin', "Rebuilt Ticket Statistics" );
			}
			elseif ( $this->ifthd->input['type'] == 'rkb' )
			{
				$this->ifthd->r_kb_stats();

				$this->ifthd->log( 'admin', "Rebuilt KB Statistics" );
			}
			elseif ( $this->ifthd->input['type'] == 'rmember' )
			{
				$this->ifthd->r_member_stats();

				$this->ifthd->log( 'admin', "Rebuilt Member Statistics" );
			}
			elseif ( $this->ifthd->input['type'] == 'rreplies' )
			{
				$this->r_replies_per_ticket();
			}
			elseif ( $this->ifthd->input['type'] == 'rmemtick' )
			{
				$this->ifthd->r_tickets_per_member();
			}
			elseif ( $this->ifthd->input['type'] == 'rdeptick' )
			{
				$this->ifthd->r_tickets_per_dept();
			}
			elseif ( $this->ifthd->input['type'] == 'racomments' )
			{
				$this->r_acomments();
			}
			elseif ( $this->ifthd->input['type'] == 'rncomments' )
			{
				$this->r_ncomments();
			}
			elseif ( $this->ifthd->input['type'] == 'rmembers' )
			{
				$this->r_members();
			}
			elseif ( $this->ifthd->input['type'] == 'rartrate' )
			{
				$this->r_article_ratings();
			}
			elseif ( $this->ifthd->input['type'] == 'rsettings' )
			{
				$this->r_settings();
			}
			elseif ( $this->ifthd->input['type'] == 'rtassign' )
			{
				$this->r_tassigned();
			}

			#$this->ifthd->skin->redirect( '?section=tools&act=maint&code=recount', 'maint_recount' );
			$this->show_recount( 'The recount function has been successfully run.' );
		}
	}

	#=======================================
	# @ Recount: Replies Per Ticket
	# Recounts the number of replies per
	# ticket.
	#=======================================

	function r_replies_per_ticket()
	{
		#=============================
		# Grab Replies
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'tid' ),
											  	 'from'		=> 'replies',
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			while( $r = $this->ifthd->core->db->fetch_row() )
			{
				$replies[ $r['tid'] ] ++;
			}
		}

		#=============================
		# Grab Tickets
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id' ),
											  	 'from'		=> 'tickets',
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			while( $t = $this->ifthd->core->db->fetch_row() )
			{
				$tickets[ $t['id'] ] = 1;
			}

			#=============================
			# Update Tickets
			#=============================

			while ( list( $tid, ) = each( $tickets ) )
			{
				$this->ifthd->core->db->construct( array(
													  	 'update'	=> 'tickets',
													  	 'set'		=> array( 'replies' => $replies[ $tid ] ),
													  	 'where'	=> array( 'id', '=', $tid ),
									 		  	  ) 	);

				$this->ifthd->core->db->execute();
			}
		}

		$this->ifthd->log( 'admin', "Recounted Replies Per Ticket" );
	}

	#=======================================
	# @ Recount: Article Comments
	# Recounts the number of comments per
	# article.
	#=======================================

	function r_acomments()
	{
		#=============================
		# Grab Comments
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'aid' ),
											  	 'from'		=> 'comments',
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			while( $c = $this->ifthd->core->db->fetch_row() )
			{
				$comments[ $c['aid'] ] ++;
			}
		}

		#=============================
		# Grab Articles
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id' ),
											  	 'from'		=> 'articles',
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			while( $a = $this->ifthd->core->db->fetch_row() )
			{
				$articles[ $a['id'] ] = 1;
			}

			#=============================
			# Update Articles
			#=============================

			while ( list( $aid, ) = each( $articles ) )
			{
				$this->ifthd->core->db->construct( array(
													  	 'update'	=> 'articles',
													  	 'set'		=> array( 'comments' => $comments[ $aid ] ),
													  	 'where'	=> array( 'id', '=', $aid ),
									 		  	  ) 	);

				$this->ifthd->core->db->execute();
			}
		}

		$this->ifthd->log( 'admin', "Recounted Article Comments" );
	}

	#=======================================
	# @ Recount: News Comments
	# Recounts the number of comments per
	# announcement.
	#=======================================

	function r_ncomments()
	{
		#=============================
		# Grab Comments
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'nid' ),
											  	 'from'		=> 'news_comments',
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			while( $c = $this->ifthd->core->db->fetch_row() )
			{
				$comments[ $c['nid'] ] ++;
			}
		}

		#=============================
		# Grab Announcements
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id' ),
											  	 'from'		=> 'announcements',
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			while( $a = $this->ifthd->core->db->fetch_row() )
			{
				$news[ $a['id'] ] = 1;
			}

			#=============================
			# Update Announcements
			#=============================

			while ( list( $nid, ) = each( $news ) )
			{
				$this->ifthd->core->db->construct( array(
													  	 'update'	=> 'announcements',
													  	 'set'		=> array( 'comments' => $comments[ $nid ] ),
													  	 'where'	=> array( 'id', '=', $nid ),
									 		  	  ) 	);

				$this->ifthd->core->db->execute();
			}
		}

		$this->ifthd->log( 'admin', "Recounted News Comments" );
	}

	#=======================================
	# @ Recount: Members
	# Recounts the number of members per
	# group.
	#=======================================

	function r_members()
	{
		#=============================
		# Grab Members
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'mgroup' ),
											  	 'from'		=> 'members',
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			while( $m = $this->ifthd->core->db->fetch_row() )
			{
				$members[ $m['mgroup'] ] ++;
			}
		}

		#=============================
		# Grab Groups
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'g_id' ),
											  	 'from'		=> 'groups',
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			while( $g = $this->ifthd->core->db->fetch_row() )
			{
				$groups[ $g['g_id'] ] = 1;
			}
		}

		#=============================
		# Update Groups
		#=============================

		while ( list( $gid, ) = each( $groups ) )
		{
			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'groups',
												  	 'set'		=> array( 'g_members' => $members[ $gid ] ),
												  	 'where'	=> array( 'g_id', '=', $gid ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();
		}

		$this->ifthd->log( 'admin', "Recounted Members" );
	}

	#=======================================
	# @ Recount: Settings
	# Recounts the number of settings per
	# settings group.
	#=======================================

	function r_settings()
	{
		#=============================
		# Grab Settings
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'cf_id', 'cf_group' ),
											  	 'from'		=> 'settings',
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			while( $s = $this->ifthd->core->db->fetch_row() )
			{
				$settings[ $s['cf_group'] ] ++;
			}
		}

		#=============================
		# Grab Settings Groups
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'cg_id' ),
											  	 'from'		=> 'settings_groups',
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			while( $g = $this->ifthd->core->db->fetch_row() )
			{
				$groups[ $g['cg_id'] ] = 1;
			}
		}

		#=============================
		# Update Settings Groups
		#=============================

		while ( list( $gid, ) = each( $groups ) )
		{
			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'settings_groups',
												  	 'set'		=> array( 'cg_set_count' => $settings[ $gid ] ),
												  	 'where'	=> array( 'cg_id', '=', $gid ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();
		}

		$this->ifthd->log( 'admin', "Recounted Settings" );
	}

	#=======================================
	# @ Recount: Article Ratings
	# Recounts the article rating for each
	# article.
	#=======================================

	function r_article_ratings()
	{
		#=============================
		# Grab Ratings
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'aid', 'rating' ),
											  	 'from'		=> 'article_rate',
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			while( $r = $this->ifthd->core->db->fetch_row() )
			{
				$rates[ $r['aid'] ] += $r['rating'];
				$rate_count[ $r['aid'] ] ++;
			}

			#=============================
			# Calculate Ratings
			#=============================

			while ( list( $a_id, $t_rate ) = each( $rates ) )
			{
				$ratings[ $a_id ] = round( ( $t_rate / $rate_count[ $a_id ] ), 2 );
			}

			#=============================
			# Grab Articles
			#=============================

			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'id' ),
												  	 'from'		=> 'articles',
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			if ( $this->ifthd->core->db->get_num_rows() )
			{
				while( $a = $this->ifthd->core->db->fetch_row() )
				{
					$articles[ $a['id'] ] = 1;
				}
			}

			#=============================
			# Update Articles
			#=============================

			while ( list( $aid, ) = each( $articles ) )
			{
				$this->ifthd->core->db->construct( array(
													  	 'update'	=> 'articles',
													  	 'set'		=> array( 'votes' => $rate_count[ $aid ], 'rating' => $ratings[ $aid ] ),
													  	 'where'	=> array( 'id', '=', $aid ),
									 		  	  ) 	);

				$this->ifthd->core->db->execute();
			}
		}

		$this->ifthd->log( 'admin', "Recounted Article Ratings" );
	}

	#=======================================
	# @ Recount: RSS Keys
	# Regenerates new RSS keys for members.
	#=======================================

	function r_rss_keys()
	{
		#=============================
		# Grab Members
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id' ),
											  	 'from'		=> 'members',
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			while( $m = $this->ifthd->core->db->fetch_row() )
			{
				$members[ $m['id'] ] = 1;
			}
		}

		#=============================
		# Generate Keys and Update
		#=============================

		while ( list( $mid, ) = each( $members ) )
		{
			$rss_key = md5( 'rk' . uniqid( rand(), true ) . $mid );

			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'members',
												  	 'set'		=> array( 'rss_key' => $rss_key ),
												  	 'where'	=> array( 'id', '=', $mid ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();
		}

		$this->ifthd->log( 'admin', "RSS Keys Regenerated" );
	}

	#=======================================
	# @ Rebuild: Login Keys
	# Regenerates new login keys for members.
	#=======================================

	function r_login_keys()
	{
		#=============================
		# Grab Members
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id' ),
											  	 'from'		=> 'members',
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			while( $m = $this->ifthd->core->db->fetch_row() )
			{
				$members[ $m['id'] ] = 1;
			}
		}

		#=============================
		# Generate Keys and Update
		#=============================

		while ( list( $mid, ) = each( $members ) )
		{
			$login_key = str_replace( "=", "", base64_encode( strrev( crypt( md5( 'lk'. uniqid( rand(), true ) . $mid ) ) ) ) );

			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'members',
												  	 'set'		=> array( 'login_key' => $login_key ),
												  	 'where'	=> array( 'id', '=', $mid ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();
		}

		$this->ifthd->log( 'admin', "Login Keys Regenerated" );
	}

	#=======================================
	# @ Recount: Assigned Tickets
	# Recounts the number of assigned
	# tickets per staff member.
	#=======================================

	function r_tassigned()
	{
		#=============================
		# Grab Tickets
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'amid' ),
											  	 'from'		=> 'tickets',
											  	 'where'	=> array( array( 'amid', '!=', 0 ), array( 'status', '!=', 6, 'and' ) )
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			while( $t = $this->ifthd->core->db->fetch_row() )
			{
				$staff[ $t['amid'] ] ++;
			}
		}

		#=============================
		# Update Staff
		#=============================

		while ( list( $smid, ) = each( $staff ) )
		{
			if ( $this->ifthd->core->cache['staff'][ $smid ] )
			{
				$this->ifthd->core->db->construct( array(
													  	 'update'	=> 'members',
													  	 'set'		=> array( 'assigned' => $staff[ $smid ] ),
													  	 'where'	=> array( 'id', '=', $smid ),
									 		  	  ) 	);

				$this->ifthd->core->db->execute();
			}
		}

		$this->ifthd->log( 'admin', "Recounted Assigned Tickets" );

		$this->ifthd->rebuild_staff_cache();
	}

	#=======================================
	# @ System Check
	# Perform a self system check.
	#=======================================

	function syscheck()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['acp']['tools_maint_syscheck'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->output = "<div class='groupbox'>System Check</div>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<th width='50%' align='left'>Table</th>
							<th width='12%'>Found</th>
							<th width='38%'>Rows</th>
						</tr>";

		#=============================
		# Check Database
		#=============================

		$datab_c = array(
							'announcements',
							'articles',
							'article_rate',
							'asessions',
							'attachments',
							'canned',
							'categories',
							'comments',
							'departments',
							'depart_fields',
							'groups',
							'languages',
							'logs',
							'members',
							'news_comments',
							'pages',
							'profile_fields',
							'replies',
							'reply_rate',
							'sessions',
							'settings',
							'settings_groups',
							'skins',
							'tickets',
							'tokens',
							'upg_history',
							'validation',
						);

		$sql = $this->ifthd->core->db->get_tables();
		$num_rows = $this->ifthd->core->db->get_num_rows( $sql );
		$row_count = 0; // Initialize for Security

		for ( $i = 0; $i < $num_rows; $i++ )
		{
			$tables[] = mysql_tablename( $sql, $i );
		}

		while ( list( , $ck_table ) = each( $datab_c ) )
		{			
			$row_count ++;
					
			( $row_count & 1 ) ? $row_class = 'option1-med' : $row_class = 'option2-med';
			
			if ( ! in_array( $this->ifthd->core->db->db_prefix . $ck_table, $tables ) )
			{
				$this->output .= "<tr>
									<td class='{$row_class}'><font color='#FF0000'>". $ck_table ."</font></td>
									<td class='{$row_class}' align='center'><font color='#FF0000'>Not Found</font></td>
									<td class='{$row_class}' align='center'><font color='#FF0000'>X</font></td>
								</tr>";
			}
			else
			{
				$this->ifthd->core->db->construct( array(
													  	 'select'	=> 'all',
													  	 'from'		=> $ck_table,
									 		  	  ) 	);

				$this->ifthd->core->db->execute();

				$temp_rows = $this->ifthd->core->db->get_num_rows();

				$this->output .= "<tr>
									<td class='{$row_class}'><font color='#007900'>". $ck_table ."</font></td>
									<td class='{$row_class}' align='center'><font color='#007900'>Found</font></td>
									<td class='{$row_class}' align='center'><font color='#007900'>". $temp_rows ."</font></td>
								</tr>";
			}
		}

		$this->output .= "</table>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=tools'>Tools</a>",
						   "<a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint'>Maintenance</a>",
						   "System Check",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Maintenance' ) );
	}

	#=======================================
	# @ Do Clean
	# Perform a Spring cleaning.
	#=======================================

	function do_clean()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['acp']['tools_maint_clean'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		#=============================
		# Do Some Cleanin'
		#=============================

		if ( $this->ifthd->input['del_old_tickets'] && $this->ifthd->input['dot_days'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'delete'	=> 'tickets',
								 				  	 'where'	=> array( 'date', '<', ( time() - ( 60 * 60 * 24 * $this->ifthd->input['dot_days'] ) ) ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$rticket = 1;
			$rmemtick = 1;
			$rdeptick = 1;
		}

		if ( $this->ifthd->input['del_old_comments'] && $this->ifthd->input['doc_days'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'delete'	=> 'comments',
								 				  	 'where'	=> array( 'date', '<', ( time() - ( 60 * 60 * 24 * $this->ifthd->input['doc_days'] ) ) ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$rcomments = 1;
		}

		if ( $this->ifthd->input['del_unapproved_mem'] && $this->ifthd->input['dum_days'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'delete'	=> 'members',
								 				  	 'where'	=> array( array( 'mgroup', '=', 3, ), array( 'joined', '<', ( time() - ( 60 * 60 * 24 * $this->ifthd->input['dum_days'] ) ), 'and' ) ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$rmember = 1;
			$rmembers = 1;
		}

		if ( $this->ifthd->input['delete_core_logs'] )
		{
			if ( $handle = opendir( HD_PATH .'core/logs' ) )
			{
	     		while ( ( $file = readdir($handle) ) !== false )
				{
					if ( $file != "." && $file != ".." && $file != "index.html" )
					{
						if ( ! is_dir( HD_PATH .'core/logs/' . $file ) )
						{
							@unlink( HD_PATH .'core/logs/' . $file );
						}
					}
				}

				closedir($handle);
			}
		}

		if ( $this->ifthd->input['delete_tmp_files'] )
		{
			if ( $handle = opendir( HD_PATH .'core/tmp' ) )
			{
	     		while ( ( $file = readdir($handle) ) !== false )
				{
					if ( $file != "." && $file != ".." && $file != "index.html" )
					{
						if ( ! is_dir( HD_PATH .'core/tmp/' . $file ) )
						{
							@unlink( HD_PATH .'core/tmp/' . $file );
						}
					}
				}

				closedir($handle);
			}
		}

		if ( $this->ifthd->input['del_logs_admin'] && $this->ifthd->input['dla_days'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'delete'	=> 'logs',
												  	 'where'	=> array( array( 'type', '=', 2, ), array( 'date', '<', ( time() - ( 60 * 60 * 24 * $this->ifthd->input['dla_days'] ) ), 'and' ) ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();
		}

		if ( $this->ifthd->input['del_logs_mem'] && $this->ifthd->input['dlm_days'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'delete'	=> 'logs',
								 				  	 'where'	=> array( array( 'type', '=', 6, ), array( 'date', '<', ( time() - ( 60 * 60 * 24 * $this->ifthd->input['dlm_days'] ) ), 'and' ) ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();
		}

		if ( $this->ifthd->input['del_logs_error'] && $this->ifthd->input['dle_days'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'delete'	=> 'logs',
								 				  	 'where'	=> array( array( 'type', '=', 3, ), array( 'date', '<', ( time() - ( 60 * 60 * 24 * $this->ifthd->input['dle_days'] ) ), 'and' ) ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();
		}

		if ( $this->ifthd->input['del_logs_sec'] && $this->ifthd->input['dls_days'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'delete'	=> 'logs',
								 				  	 'where'	=> array( array( 'type', '=', 4, ), array( 'date', '<', ( time() - ( 60 * 60 * 24 * $this->ifthd->input['dls_days'] ) ), 'and' ) ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();
		}

		if ( $this->ifthd->input['del_logs_tick'] && $this->ifthd->input['dlt_days'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'delete'	=> 'logs',
								 				  	 'where'	=> array( array( 'type', '=', 7, ), array( 'date', '<', ( time() - ( 60 * 60 * 24 * $this->ifthd->input['dlt_days'] ) ), 'and' ) ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();
		}

		if ( $this->ifthd->input['kill_asessions'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'delete'	=> 'asessions',
												  	 #'where'	=> array( array( 'type', '=', 2, ), array( 'date', '<', ( time() - ( 60 * 60 * 24 * $this->ifthd->input['dla_days'] ) ), 'and' ) ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();
		}


		if ( $this->ifthd->input['kill_sessions'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'delete'	=> 'sessions',
												  	 #'where'	=> array( array( 'type', '=', 2, ), array( 'date', '<', ( time() - ( 60 * 60 * 24 * $this->ifthd->input['dla_days'] ) ), 'and' ) ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();
		}

		#=============================
		# Do We Need To Rebuild?
		#=============================

		if ( $rticket )
		{
			$this->ifthd->r_ticket_stats();
		}

		if ( $rmemtick )
		{
			$this->ifthd->r_tickets_per_member();
		}

		if ( $rdeptick )
		{
			$this->ifthd->r_tickets_per_dept();
		}

		if ( $rmember )
		{
			$this->ifthd->r_member_stats();
		}

		#=============================
		# Redirect
		#=============================

		$this->ifthd->log( 'admin', "Spring Cleaning Ran", 2 );

		#$this->ifthd->skin->redirect( '?section=tools&act=maint&code=clean', 'spring_clean_success' );
		$this->show_rebuild( 'Spring cleaning has been successfully run.' );
	}

}

?>