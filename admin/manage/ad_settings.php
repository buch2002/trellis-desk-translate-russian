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
|    | Admin Settings
#======================================================
*/

class ad_settings {

	#=======================================
	# @ Auto Run
	# Function that is run automatically
	# when the file is required.
	#=======================================

	function auto_run()
	{
		if ( ! $this->ifthd->member['acp']['manage_settings'] )
		{
			$this->ifthd->skin->error('no_perm');
		}
		
		$this->ifthd->skin->set_section( 'System Settings' );		
		$this->ifthd->skin->set_description( 'Modify your ticket settings and configure various system options.' );

		switch( $this->ifthd->input['code'] )
	    {
	    	case 'show':
				$this->show_settings();
	    	break;
	    	case 'find':
				$this->find_settings();
	    	break;
	    	case 'update':
				$this->update_settings();
	    	break;
	    	case 'revert':
				$this->revert_settings();
	    	break;


    		default:
    			$this->show_groups();
    		break;
		}
	}

	#=======================================
	# @ Show Groups
	# Show a list of setting groups.
	#=======================================

	function show_groups()
	{
		#=============================
		# Grab Groups
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'settings_groups',
											  	 'order'	=> array( 'cg_name' => 'asc' ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$group_rows = ""; // Initialize for Security
		$row_count = 0; // Initialize for Security

		while( $g = $this->ifthd->core->db->fetch_row() )
		{
			$row_count ++;
			
			( $row_count & 1 ) ? $row_class = 'option1' : $row_class = 'option2';
			
			$group_rows .= "<tr>
								<td class='{$row_class}'>
									<a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings&amp;code=show&amp;group={$g['cg_id']}'>{$g['cg_name']}</a>
									<div class='desc'>{$g['cg_description']}</div>
								</td>
								<td class='{$row_class}' align='center'>{$g['cg_set_count']}</td>
							</tr>";
		}

		$this->output = "<div class='groupbox'>Settings Groups</div>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<th width='80%' align='left'>Name</th>
							<th width='20%'>Settings</th>
						</tr>
						". $group_rows ."
						</table>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "Settings",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'View Settings Groups' ) );
	}

	#=======================================
	# @ Show Settings
	# Show a list of setting for group.
	#=======================================

	function show_settings($group_id=0, $updated=0)
	{
		if ( ! $group_id )
		{
			$group_id = $this->ifthd->input['group'];
		}

		#=============================
		# Grab Settings Group
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'settings_groups',
											  	 'where'	=> array( 'cg_id', '=', $group_id ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_settings_found');
		}

		$g = $this->ifthd->core->db->fetch_row();

		#=============================
		# Grab Settings
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'settings',
											  	 'where'	=> array( 'cf_group', '=', $g['cg_id'] ),
											  	 'order'	=> array( 'cf_position' => 'asc' ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_settings_found');
		}

		$setting_rows = ""; // Initialize for Security
		$row_count = 0; // Initialize for Security

		while( $s = $this->ifthd->core->db->fetch_row() )
		{
			$row_count ++;
			
			( $row_count & 1 ) ? $row_class = 'option1' : $row_class = 'option2';
			
			$setting_rows .= "<tr>
								<td width='35%' class='{$row_class}'>
									{$s['cf_title']}
								</td>
								<td width='65%' class='{$row_class}'>";

			if ( $s['cf_type'] == 'input' )
			{
				$setting_rows .= $this->ifthd->skin->input( $s['cf_key'], $s['cf_value'], $s['cf_default'], $s['cf_id'] );
			}
			elseif ( $s['cf_type'] == 'yes_no' )
			{
				$setting_rows .= $this->ifthd->skin->yes_no_radio( $s['cf_key'], $s['cf_value'], $s['cf_default'], $s['cf_id'] );
			}
			elseif ( $s['cf_type'] == 'enabled_disabled' )
			{
				$setting_rows .= $this->ifthd->skin->enabled_disabled_radio( $s['cf_key'], $s['cf_value'], $s['cf_default'], $s['cf_id'] );
			}
			elseif ( $s['cf_type'] == 'checkbox' )
			{
				$setting_rows .= $this->ifthd->skin->checkbox_radio( $s['cf_key'], $s['cf_extra'], $s['cf_value'], $s['cf_default'], $s['cf_id'] );
			}
			elseif ( $s['cf_type'] == 'textarea' )
			{
				$setting_rows .= $this->ifthd->skin->textarea( $s['cf_key'], $s['cf_value'], $s['cf_default'], $s['cf_id'] );
			}
			elseif ( $s['cf_type'] == 'dropdown' )
			{
				$raw_options = explode( "\n", $s['cf_extra'] );
				
				$drop_options = array();
				
				while ( list( , $opt ) = each( $raw_options ) )
				{
					$our_opt = explode( "=", $opt );
					
					$drop_options[ $our_opt[0] ] = $our_opt[1];
				}
				
				$setting_rows .= $this->ifthd->skin->drop_down( $s['cf_key'], $s['cf_value'], $drop_options, $s['cf_default'], $s['cf_id'] );
			}

			$setting_rows .= "</td>
							</tr>
							<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info{$row_count}','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Toggle information</a>
										<div id='info{$row_count}' style='display: none;'>
										<div>
											{$s['cf_description']}
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output = "";
		
		if ( $updated == 1 )
		{
			$this->output .= "<div class='alert'>The settings have been updated.</div>";
		}
		elseif ( $updated == 2 )
		{
			$this->output .= "<div class='alert'>The setting has been reverted to default.</div>";
		}

		$this->output .= "<div class='groupbox'>{$g['cg_name']}</div>
						<form action='<! HD_URL !>/admin.php?section=manage&amp;act=settings&amp;code=update&amp;group={$g['cg_id']}' method='post'>
						<table width='100%' cellpadding='0' cellspacing='0'>
						". $setting_rows ."
						</table>
						<div class='formtail'><input type='submit' name='submit' id='update' value='Update Settings' class='button' /></div>
						</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings'>Settings</a>",
						   $g['cg_name'],
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Modify Settings' ) );
	}

	#=======================================
	# @ Find Settings
	# Find and show a list of settings for
	# the settings group.
	#=======================================

	function find_settings()
	{
		#=============================
		# Grab Settings Group
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'cg_id' ),
											  	 'from'		=> 'settings_groups',
											  	 'where'	=> array( 'cg_key', '=', $this->ifthd->input['group'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_settings_found');
		}

		$g = $this->ifthd->core->db->fetch_row();

		#=============================
		# Do Output
		#=============================

		$this->show_settings( $g['cg_id'] );
	}

	#=======================================
	# @ Update Settings
	# Show a list of setting for group.
	#=======================================

	function update_settings()
	{
		#=============================
		# Grab Group
		#=============================

		if ( ! $this->ifthd->member['acp']['manage_settings_update'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'settings_groups',
											  	 'where'	=> array( 'cg_id', '=', $this->ifthd->input['group'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_settings_found');
		}

		$g = $this->ifthd->core->db->fetch_row();

		#=============================
		# Grab Settings
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'settings',
											  	 'where'	=> array( 'cf_group', '=', $g['cg_id'] ),
							 		  	  ) 	);

		$set_handle = $this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_settings_found');
		}

		while( $s = $this->ifthd->core->db->fetch_row($set_handle) )
		{
			if ( $s['cf_value'] != $this->ifthd->input[ $s['cf_key'] ] )
			{
				$db_array = array(
								  'cf_value'			=> $this->ifthd->input[ $s['cf_key'] ],
								 );

				$this->ifthd->core->db->construct( array(
													  	 'update'	=> 'settings',
													  	 'set'		=> $db_array,
									 				  	 'where'	=> array( 'cf_id', '=', $s['cf_id'] ),
									 		  	  ) 	);

				$this->ifthd->core->db->execute();
			}
		}

		$this->ifthd->log( 'admin', "Settings Updated &#039;". $g['cg_name'] ."&#039;", 1, $g['cg_id'] );

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_set_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=settings&code=show&group='. $g['cg_id'], 'settings_update_success' );
		$this->show_settings( $g['cg_id'], 1 );
	}

	#=======================================
	# @ Revert Setting
	# Revert a setting back to its default.
	#=======================================

	function revert_settings()
	{
		#=============================
		# Select Setting
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'cf_id', 'cf_title', 'cf_group', 'cf_default' ),
											  	 'from'		=> 'settings',
											  	 'where'	=> array( 'cf_id', '=', $this->ifthd->input['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$s = $this->ifthd->core->db->fetch_row();

		#=============================
		# Lets not be difficult,
		# just run the damn query.
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'settings',
											  	 'set'		=> array( 'cf_value' => $s['cf_default'] ),
											  	 'where'	=> array( 'cf_id', '=', $s['cf_id'] ),
							 		  	  ) 	);

		$set_handle = $this->ifthd->core->db->execute();

		$this->ifthd->log( 'admin', "Setting Reverted &#039;". $s['cf_title'] ."&#039;", 1, $s['cf_id'] );

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_set_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=settings&code=show&group='. $s['cf_group'], 'setting_revert_success' );
		$this->show_settings( $s['cf_group'], 2 );
	}

}

?>