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
|    | Admin Languages
#======================================================
*/

class ad_lang {

	#=======================================
	# @ Auto Run
	# Function that is run automatically
	# when the file is required.
	#=======================================

	function auto_run()
	{
		if ( ! $this->ifthd->member['acp']['look_lang'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->skin->set_section( 'Look &amp; Feel' );
		$this->ifthd->skin->set_description( 'Manage the presentation of your helpdesk with skins and languages.' );

		switch( $this->ifthd->input['code'] )
	    {
	    	case 'list':
				$this->list_langs();
	    	break;
	    	case 'prop':
				$this->show_prop();
	    	break;
	    	case 'show':
				$this->show_lang();
	    	break;
	    	case 'edit':
	    		$this->edit_lang();
	    	break;
	    	case 'tools':
	    		$this->show_tools();
	    	break;
    		case 'import':
    			$this->show_import();
    		break;
    		case 'export':
    			$this->list_langs('', '', 'export');
    		break;

	    	case 'doprop':
				$this->do_prop();
	    	break;
    		case 'doadd':
    			$this->do_add();
    		break;
    		case 'doedit':
    			$this->do_edit();
    		break;
    		case 'delete':
    			$this->do_delete();
    		break;
    		case 'doimport':
    			$this->do_import();
    		break;
    		case 'dotools':
    			$this->do_tools();
    		break;
    		case 'doexport':
    			$this->do_export();
    		break;
    		case 'default':
    			$this->do_default();
    		break;

    		default:
    			$this->list_langs();
    		break;
		}
	}

	#=======================================
	# @ List Languages
	# Show a list of available languages.
	#=======================================

	function list_langs($error='', $alert='', $instr='')
	{
		#=============================
		# Grab Languages
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'languages',
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$lang_rows = ""; // Initialize for Security
		$row_count = 0; // Initialize for Security

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_lang');
		}

		while( $l = $this->ifthd->core->db->fetch_row() )
		{
			$row_count ++;

			( $row_count & 1 ) ? $row_class = 'option1-med' : $row_class = 'option2-med';

			if ( $l['default'] )
			{
				$l['default'] = "<span class='disabled'>Default</span>";
				$l['delete'] = "<span class='disabled'><img src='<! HD_URL !>/images/default/button_delete_disabled.gif' alt='Delete' /></span>";
			}
			else
			{
				$l['default'] = "<a href='<! HD_URL !>/admin.php?section=look&amp;act=lang&amp;code=default&amp;id={$l['id']}'>Make Default</a>";
				$l['delete'] = "<a href='<! HD_URL !>/admin.php?section=look&amp;act=lang&amp;code=delete&amp;id={$l['id']}' onclick='return sure_delete()'><img src='<! HD_URL !>/images/default/button_delete.gif' alt='Delete' /></a>";
			}

			$lang_rows .= "<tr>
								<td class='{$row_class}'><a href='<! HD_URL !>/admin.php?section=look&amp;act=lang&amp;code=show&amp;id={$l['id']}'>{$l['lkey']}</a></td>
								<td class='{$row_class}'><a href='<! HD_URL !>/admin.php?section=look&amp;act=lang&amp;code=show&amp;id={$l['id']}'>{$l['name']}</a></td>
								<td class='{$row_class}' align='center'>{$l['default']}</td>
								<td class='{$row_class}' align='center'>{$l['users']}</td>
								<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=look&amp;act=lang&amp;code=doexport&amp;id={$l['id']}'><img src='<! HD_URL !>/images/default/button_export.gif' alt='Export' /></a></td>
								<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=look&amp;act=lang&amp;code=show&amp;id={$l['id']}'>Language</a> | <a href='<! HD_URL !>/admin.php?section=look&amp;act=lang&amp;code=prop&amp;id={$l['id']}'>Properties</a></td>
								<td class='{$row_class}' align='center'>{$l['delete']}</td>
							</tr>";
		}

		if ( $instr == 'export' )
		{
			$add_txt = "<div class='option1'>To export a language pack, simply click the Export link next to the appropriate language pack.</div>";
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
								if ( confirm('Are you sure you want to delete this language pack?') )
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
						<div class='groupbox'>Language List</div>
						{$add_txt}
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<th width='6%' align='left'>Key</th>
							<th width='22%' align='left'>Name</th>
							<th width='15%'>Default</th>
							<th width='11%'>Members</th>
							<th width='11%'>Export</th>
							<th width='24%'>Edit</th>
							<th width='11%'>Delete</th>
						</tr>
						". $lang_rows ."
						</table>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=look'>Look &amp; Feel</a>",
						   "<a href='<! HD_URL !>/admin.php?section=look&amp;act=lang'>Languages</a>",
						   "List Languages",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Languages' ) );
	}

	#=======================================
	# @ Show Prop
	# Show edit properties form.
	#=======================================

	function show_prop($error="")
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['look_lang_manage'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'lkey', 'name' ),
											  	 'from'		=> 'languages',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_lang');
		}

		$l = $this->ifthd->core->db->fetch_row();

		#=============================
		# Output
		#=============================

		if ( $error )
		{
			$lkey = $this->ifthd->input['lkey'];
			$name = $this->ifthd->input['name'];

			$error = "<div class='critical'>{$error}</div>";
		}
		else
		{
			$lkey = $l['lkey'];
			$name = $l['name'];
		}

		$this->output = "{$error}
						<div class='groupbox'>Edit Properties</div>
						<form action='<! HD_URL !>/admin.php?section=look&amp;act=lang&amp;code=doprop&amp;id={$l['id']}' method='post'>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<td class='option1' width='20%'><label for='name'>Name</label></td>
							<td class='option1' width='80%'><input type='text' name='name' id='name' value='{$name}' size='35' /></td>
						</tr>
						<tr>
							<td class='option2'><label for='lkey'>Key</label></td>
							<td class='option2'><input type='text' name='lkey' id='lkey' value='{$lkey}' size='5' /></td>
						</tr>
						</table>
						<div class='formtail'><input type='submit' name='submit' id='edit' value='Edit Properties' class='button' /></div>
						</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=look'>Look &amp; Feel</a>",
						   "<a href='<! HD_URL !>/admin.php?section=look&amp;act=skin'>Languages</a>",
						   "Edit Properties",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Languages' ) );
	}

	#=======================================
	# @ Do Prop
	# Update properties.
	#=======================================

	function do_prop()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['look_lang_manage'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		if ( ! $this->ifthd->input['name'] )
		{
			$this->show_prop('Please enter a name.');
		}

		if ( ! $this->ifthd->input['lkey'] )
		{
			$this->show_prop('Please enter a key.');
		}

		if ( ! $this->key_check( $this->ifthd->input['lkey'] ) )
		{
			$this->show_prop('Your key must be alphanumeric, lowercase, and contain no spaces.');
		}

		if ( strlen( $this->ifthd->input['lkey'] ) > 3 )
		{
			$this->show_prop('Your key must be no more than 3 characters long.');
		}


		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id' ),
											  	 'from'		=> 'languages',
							 				  	 'where'	=> array( array( 'lkey|lower', '=', $this->ifthd->input['lkey'] ), array( 'id', '!=', $this->ifthd->input['id'], 'and' ) ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			$this->show_prop('That key is already in use.  Please choose another.');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'lkey', 'name' ),
											  	 'from'		=> 'languages',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_skin');
		}

		$l = $this->ifthd->core->db->fetch_row();

		#=============================
		# Rename Folder
		#=============================

		if ( $this->ifthd->input['lkey'] != $l['lkey'] )
		{
			if ( ! @ rename( HD_PATH .'language/'. $l['lkey'], HD_PATH .'language/'. $this->ifthd->input['lkey'] ) )
			{
				$this->show_prop('folder_rename');
			}
		}

		#=============================
		# Update Properties
		#=============================

		$db_array = array(
						  'lkey'			=> $this->ifthd->input['lkey'],
						  'name'			=> $this->ifthd->input['name'],
						  );

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'languages',
											  	 'set'		=> $db_array,
							 				  	 'where'	=> array( 'id', '=', $l['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->rebuild_lang_cache();

		$this->ifthd->log( 'admin', "Language Properites Updated &#039;". $l['name'] ."&#039;" );

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=look&act=lang', 'prop_lang_success' );
		$this->list_langs( '', 'The language has been successfully updated.' );
	}

	#=======================================
	# @ Show Tools
	# Show a list of available tools.
	#=======================================

	function show_tools()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['acp']['look_lang_tools'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$lang_drop = $this->ifthd->build_lang_drop();

		$this->output = "<div class='groupbox'>Language Tools</div>
						<form action='<! HD_URL !>/admin.php?section=look&amp;act=lang&amp;code=dotools' method='post'>
						<div class='option1'>Switch ALL users to language: <select name='all_lang' id='all_lang'>{$lang_drop}</select> <input type='submit' name='all_users' id='all_users' value='Switch' /></div>
						<div class='option2'>Users using language <select name='first_lang' id='first_lang'>{$lang_drop}</select> switch to <select name='sec_lang' id='sec_lang'>{$lang_drop}</select> <input type='submit' name='some_users' id='some_users' value='Switch' /></div>
						</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=look'>Look &amp; Feel</a>",
						   "<a href='<! HD_URL !>/admin.php?section=look&amp;act=lang'>Languages</a>",
						   "Language Tools",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Languages' ) );
	}

	#=======================================
	# @ Do Tools
	# Perform specified action.
	#=======================================

	function do_tools()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['acp']['look_lang_tools'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		if ( $this->ifthd->input['all_users'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'members',
												  	 'set'		=> array( 'lang' => $this->ifthd->input['all_lang'] ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();
		}
		elseif ( $this->ifthd->input['some_users'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'members',
												  	 'set'		=> array( 'lang' => $this->ifthd->input['sec_lang'] ),
												  	 'where'	=> array( 'lang', '=', $this->ifthd->input['first_lang'] ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();
		}

		$this->ifthd->log( 'admin', "Language Tools Ran" );

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=look&act=lang&code=tools', 'switch_lang_success' );
		$this->list_langs( '', 'The members\' languages have been successfully updated.');
	}

	#=======================================
	# @ Show Language
	# Show a list editable language files.
	#=======================================

	function show_lang($error="")
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['look_lang_manage'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'languages',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_lang');
		}

		$l = $this->ifthd->core->db->fetch_row();

		#=============================
		# Grab Files
		#=============================

		$row_count = 0; // Initialize for Security

		$handle = opendir( HD_PATH. "language/". $l['lkey'] ."/" );

		while( $file = readdir( $handle ) )
		{
			if ( $file != '.' && $file != '..' && $file != 'index.html' )
			{
				$lang_files[] = $file;
			}
		}

		sort( $lang_files );

		while ( list( , $file ) = each( $lang_files ) )
		{
			$row_count ++;

			( $row_count & 1 ) ? $row_class = 'option1-med' : $row_class = 'option2-med';

			$short_file = str_replace( '.php', "", $file );

			if ( $human_names[ $file ] )
			{
				$file_name = $human_names[ $file ];
			}
			else
			{
				$file_name = $file;
			}

			$lang_rows .= "<tr>
								<td class='{$row_class}'><a href='<! HD_URL !>/admin.php?section=look&amp;act=lang&amp;code=edit&amp;lkey={$l['lkey']}&amp;file={$short_file}'>{$file_name}</a></td>
							</tr>";
		}

		$this->output = "<div class='groupbox'>Language Files List</div>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<th align='left'>File Name (Click to Edit)</th>
						</tr>
						". $lang_rows ."
						</table>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=look'>Look &amp; Feel</a>",
						   "<a href='<! HD_URL !>/admin.php?section=look&amp;act=lang'>Languages</a>",
						   $l['name'],
						   "List Languages",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Languages' ) );
	}

	#=======================================
	# @ Edit Language
	# Show edit language form.
	#=======================================

	function edit_lang($error="")
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['acp']['look_lang_manage'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'languages',
							 				  	 'where'	=> array( 'lkey', '=', $this->ifthd->input['lkey'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_lang');
		}

		$l = $this->ifthd->core->db->fetch_row();

		if ( ! is_file( HD_PATH. "language/". $l['lkey'] ."/". $this->ifthd->input['file'] .".php" ) )
		{
			$this->ifthd->skin->error('no_lang');
		}

		require HD_PATH. "language/". $l['lkey'] ."/". $this->ifthd->input['file'] .".php";

		#=============================
		# Do Output
		#=============================

		$row_count = 0; // Initialize for Security

		while ( list( $key, $replacement ) = each( $lang ) )
		{
			if ( preg_match( "(\n|\r)", $replacement ) )
			{
				$replacement = $this->ifthd->html_safe( stripslashes( $replacement ) );

				$r_field = "<textarea name='l_{$key}' cols='55' rows='4' style='width: 98%; height: 40px;'>{$replacement}</textarea>";
			}
			else
			{
				$replacement = $this->ifthd->html_safe( stripslashes( $replacement ) );

				$r_field = "<textarea name='l_{$key}' cols='55' rows='2' style='width: 98%; height: 40px;'>{$replacement}</textarea>";
			}

			$row_count ++;

			( $row_count & 1 ) ? $row_class = 'option1-med' : $row_class = 'option2-med';

			$lang_rows .= "<tr>
								<td class='{$row_class}'>{$key}</td>
								<td class='{$row_class}'>{$r_field}</td>
							</tr>";
		}

		unset( $lang );

		$this->output = "<div class='groupbox'>Edit Language</div>
						<form action='<! HD_URL !>/admin.php?section=look&amp;act=lang&amp;code=doedit&amp;lkey={$l['lkey']}&amp;file={$this->ifthd->input['file']}' method='post'>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<th width='25%' align='left'>Key</th>
							<th width='75%' align='left'>Replacement</th>
						</tr>
						". $lang_rows ."
						</table>
						<div class='formtail'><input type='submit' name='submit' id='edit' value='Edit Language File' class='button' /></div>
						</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=look'>Look &amp; Feel</a>",
						   "<a href='<! HD_URL !>/admin.php?section=look&amp;act=lang'>Languages</a>",
						   $l['name'],
						   "Edit Language",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Languages' ) );
	}

	#=======================================
	# @ Do Edit
	# Edit a language.
	#=======================================

	function do_edit()
	{
		if ( ! $this->ifthd->member['acp']['look_lang_manage'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'languages',
							 				  	 'where'	=> array( 'lkey', '=', $this->ifthd->input['lkey'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_lang');
		}

		$l = $this->ifthd->core->db->fetch_row();

		if ( ! is_file( HD_PATH. "language/". $l['lkey'] ."/". $this->ifthd->input['file'] .".php" ) )
		{
			$this->ifthd->skin->error('no_lang');
		}

		require HD_PATH. "language/". $l['lkey'] ."/". $this->ifthd->input['file'] .".php";

		$file = $this->ifthd->input['file'] .".php";

		#=============================
		# Generate File
		#=============================

		if ( $file == 'lang_email_content.php' )
		{
			$data = "<?php\n\n/*\n#======================================================\n";
			$data .= "|    | Trellis Desk Language File\n";
			$data .= "|    | ". $file ."\n";
			$data .= "#======================================================\n*/\n\n";

			while ( list( $key, ) = each( $lang ) )
			{
				if ( substr( $key, -4, 4) == '_sub' )
				{
					$data .= "\$lang['". $key ."'] = \"". str_replace( "'", "\'", $this->ifthd->convert_html( $this->ifthd->input[ 'l_'. $key ], 0, 0 ) ) ."\";\n\n";
				}
				else
				{
					$data .= "\$lang['". $key ."'] = <<<EOF\n". $this->ifthd->convert_html( $this->ifthd->input[ 'l_'. $key ], 0, 0 ) ."\nEOF;\n\n";
				}
			}

			$data .= "?>";
		}
		else
		{
			$data = "<?php\n\n/*\n#======================================================\n";
			$data .= "|    | Trellis Desk Language File\n";
			$data .= "|    | ". $file ."\n";
			$data .= "#======================================================\n*/\n\n";
			$data .= "\$lang = array(\n\n";

			while ( list( $key, ) = each( $lang ) )
			{
				$data .= "'{$key}' => '". str_replace( "'", "\'", $this->ifthd->convert_html( $this->ifthd->input[ 'l_'. $key ], 0, 0 ) ) ."',\n";
			}

			$data .= "\n);\n\n?>";
		}

		unset( $lang );

		#=============================
		# Save File
		#=============================

		if ( ! is_writable( ! HD_PATH. "language/". $l['lkey'] ."/". $this->ifthd->input['file'] .".php" ) )
		{
			$this->ifthd->skin->error('not_writable');
		}

		if ( ! $handle = fopen( HD_PATH. "language/". $l['lkey'] ."/". $this->ifthd->input['file'] .".php", 'wb' ) )
		{
			$this->ifthd->skin->error('no_open_file');
		}

		if ( fwrite( $handle, $data ) === false )
		{
			$this->ifthd->skin->error('not_writable');
		}

		@fclose( $handle );

		$this->ifthd->log( 'admin', "Language File &#039;". $this->ifthd->input['file'] ."&#039; Edited", 1, $l['id'] );

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=look&act=lang&code=show&id='. $l['id'], 'edit_lang_success' );
		$this->list_langs( '', 'The language file has been successfully updated.' );
	}

	#=======================================
	# @ Show Import
	# Display import language file page.
	#=======================================

	function show_import()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['acp']['look_lang_import'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		#=============================
		# Do Output
		#=============================

		$this->output = "<script type='text/javascript'>

							function validate_form(form)
							{
								if ( ! form.lang_file.value )
								{
									alert('Please select a file to upload.');
									form.lang_file.focus();
									return false;
								}
							}

						</script>
						<div class='groupbox'>Import a Language</div>
						<div class='subbox'>Please select a valid Trellis Desk XML Language File to import.</div>
						<form enctype='multipart/form-data' action='<! HD_URL !>/admin.php?section=look&amp;act=lang&amp;code=doimport' method='post' onsubmit='return validate_form(this)'>
						<div class='option1'><input type='file' name='lang_file' id='lang_file' size='40' /></div>
						<div class='formtail'><input type='submit' name='upload' id='upload' value='Import' class='button' /></div>
						</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=look'>Look &amp; Feel</a>",
						   "<a href='<! HD_URL !>/admin.php?section=look&amp;act=lang'>Languages</a>",
						   "Import Language",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Languages' ) );
	}

	#=======================================
	# @ Do Import
	# Uploads the specified XML file and
	# attempts to parse it, then finally
	# create the new language pack.
	#=======================================

	function do_import()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['acp']['look_lang_import'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		#=============================
		# Upload File
		#=============================

		if ( ! $_FILES['lang_file']['size'] )
		{
			$this->ifthd->skin->error('no_upload_size');
		}

		if ( $_FILES['lang_file']['type'] != 'text/xml' )
		{
			$this->ifthd->skin->error('no_upload_lang_xml');
		}

		$data = $this->ifthd->parseFile( $_FILES['lang_file']['tmp_name'] );

		#=============================
		# Format
		#=============================

		while ( list( , $info ) = each( $data ) )
		{
			while ( list( $key, $value ) = each( $info ) )
			{
				if ( $key == 'lang_file' )
				{
					$temp_file = $value;
				}
				elseif ( $key == 'lang_key' )
				{
					$temp_key = $value;
				}
				elseif ( $key == 'lang_replace' )
				{
					$temp_replace = $value;
				}

				if ( $temp_file && $temp_key && $temp_replace )
				{
					$new_data[ $temp_file ][ $temp_key ] = $temp_replace;

					$temp_file = "";
					$temp_key = "";
					$temp_replace = "";
				}
			}
		}

		#=============================
		# Create Files
		#=============================

		$i = "";

		while ( is_dir( HD_PATH. "language/". $this->ifthd->xml_lang_abbr . $i ) )
		{
			if ( ! $i ) $this->ifthd->xml_lang_abbr = substr( $this->ifthd->xml_lang_abbr, 0, 2 );

			$i++;
		}

		if ( ! @mkdir( HD_PATH. "language/". $this->ifthd->xml_lang_abbr . $i, 0777 ) )
		{
			$this->ifthd->skin->error('no_create_lang');
		}

		while ( list( $file, $sog ) = each( $new_data ) )
		{
			if ( $file == 'lang_email_content.php' )
			{
				$file_data = "<?php\n\n/*\n#======================================================\n";
				$file_data .= "|    | Trellis Desk Language File\n";
				$file_data .= "|    | ". $file ."\n";
				$file_data .= "#======================================================\n*/\n\n";

				while ( list( $aka, $ohnine ) = each( $sog ) )
				{
					if ( substr( $aka, -4, 4) == '_sub' )
					{
						$file_data .= "\$lang['". $aka ."'] = \"". $ohnine ."\";\n\n";
					}
					else
					{
						$file_data .= "\$lang['". $aka ."'] = <<<EOF\n". $ohnine ."\nEOF;\n\n";
					}
				}

				$file_data .= "?>";
			}
			else
			{
				$file_data = "<?php\n\n/*\n#======================================================\n";
				$file_data .= "|    | Trellis Desk Language File\n";
				$file_data .= "|    | ". $file ."\n";
				$file_data .= "#======================================================\n*/\n\n";
				$file_data .= "\$lang = array(\n\n";

				while ( list( $aka, $ohnine ) = each( $sog ) )
				{
					$file_data .= "'". $aka ."' => '". addslashes( $ohnine ) ."',\n";
				}

				$file_data .= "\n);\n\n?>";
			}

			if ( ! $handle = @fopen( HD_PATH. "language/". $this->ifthd->xml_lang_abbr . $i ."/". $file, 'w' ) )
			{
				$this->ifthd->skin->error('no_create_lang');
			}

			if ( @fwrite( $handle, $file_data ) === false )
			{
				$this->ifthd->skin->error('no_create_lang');
			}

			@fclose($handle);
		}

		$db_array = array(
						  'lkey'		=> $this->ifthd->xml_lang_abbr . $i,
						  'name'		=> $this->ifthd->xml_lang_name,
						 );

		$this->ifthd->core->db->construct( array(
											  	 'insert'	=> 'languages',
											  	 'set'		=> $db_array,
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'admin', "Language Pack &#039;". $this->ifthd->xml_lang_name ."&#039; Imported" );

		$this->ifthd->rebuild_lang_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=look&act=lang', 'import_lang_success' );
		$this->list_langs( '', 'The language has been successfully imported.' );
	}

	#=======================================
	# @ Do Default
	# Sets the specified language pack as
	# default.
	#=======================================

	function do_default()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['look_lang_manage'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'languages',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_lang');
		}

		$s = $this->ifthd->core->db->fetch_row();

		#=============================
		# Revert Old Default
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id' ),
											  	 'from'		=> 'languages',
							 				  	 'where'	=> array( 'default', '=', 1 ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$l = $this->ifthd->core->db->fetch_row();

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'languages',
											  	 'set'		=> array( 'default' => 0 ),
							 				  	 'where'	=> array( 'id', '=', $l['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		#=============================
		# Set New Default
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'languages',
											  	 'set'		=> array( 'default' => 1 ),
							 				  	 'where'	=> array( 'id', '=', $s['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->rebuild_lang_cache();

		$this->ifthd->log( 'admin', "Language Pack &#039;". $s['name'] ."&#039; Set as Default" );

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=look&act=lang', 'default_lang_success' );
		$this->list_langs( '', 'The language has been successfully set as default.' );
	}

	#=======================================
	# @ Do Export
	# Exports a specified language pack and
	# generates an XML file.
	#=======================================

	function do_export()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['look_lang_export'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'languages',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_lang');
		}

		$l = $this->ifthd->core->db->fetch_row();

		#=============================
		# Grab Files
		#=============================

		$file_data = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<language_pack abbr=\"". chunk_split( base64_encode( $l['lkey'] ) ) ."\" name=\"". chunk_split( base64_encode( $l['name'] ) ) ."\">\n";

		$handle = opendir( HD_PATH. "language/". $l['lkey'] ."/" );

		while( $file = readdir( $handle ) )
		{
			if ( $file != '.' && $file != '..' && $file != 'index.html' )
			{
				$lang_files[] = $file;
			}
		}

		sort( $lang_files );

		#=============================
		# Generate XML
		#=============================

		while ( list( , $file ) = each( $lang_files ) )
		{
			$file_data .= "\t<lang_file name=\"". chunk_split( base64_encode( $file ) ) ."\">\n";

			require HD_PATH. "language/". $l['lkey'] ."/". $file;

			while ( list( $key, $replace ) = each( $lang ) )
			{
				$file_data .= "\t\t<lang_bit>\n";

				$file_data .= "\t\t\t<lang_key>". chunk_split( base64_encode( $key ) ) ."</lang_key>\n";
				$file_data .= "\t\t\t<lang_replace>". chunk_split( base64_encode( $replace ) ) ."</lang_replace>\n";

				$file_data .= "\t\t</lang_bit>\n";
			}

			$file_data .= "\t</lang_file>\n";
		}

		$this->ifthd->log( 'admin', "Language Pack &#039;". $l['name'] ."&#039; Exported" );

		$file_data .= "</language_pack>";

		header('Content-type: text/xml');

		header('Content-Disposition: attachment; filename="lang_'. $l['lkey'] .'_td.xml"');

		print $file_data;
	}

	#=======================================
	# @ Do Delete
	# Delete the language pack. :'( WHY?!?!
	#=======================================

	function do_delete()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['look_lang_manage'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'languages',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_lang');
		}

		$l = $this->ifthd->core->db->fetch_row();

		if ( $l['default'] )
		{
			$this->ifthd->skin->error('no_delete_default_lang');
		}

		#=============================
		# Grab Files & Delete
		#=============================

		$handle = opendir( HD_PATH. "language/". $l['lkey'] ."/" );

		while( $file = readdir( $handle ) )
		{
			if ( $file != '.' && $file != '..' )
			{
				unlink( HD_PATH. "language/". $l['lkey'] ."/". $file );
			}
		}

		rmdir( HD_PATH. "language/". $l['lkey'] );

		#=============================
		# Delete
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'delete'	=> 'languages',
							 				  	 'where'	=> array( 'id', '=', $l['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'admin', "Language Pack &#039;". $l['name'] ."&#039; Deleted", 2, $l['id'] );

		$this->ifthd->rebuild_lang_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=look&act=lang', 'delete_lang_success' );
		$this->list_langs( 'The language has been successfully deleted.' );
	}

	#=======================================
	# @ Key Check
	# Checks to see if profile key is valid.
	#=======================================

	function key_check($key)
	{
		if ( preg_match( '/^[a-z0-9]*$/', $key ) )
		{
			return TRUE;
		}

		return FALSE;
	}

}

?>