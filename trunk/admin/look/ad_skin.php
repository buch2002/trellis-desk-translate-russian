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
|    | Admin Skin
#======================================================
*/

class ad_skin {

	#=======================================
	# @ Auto Run
	# Function that is run automatically
	# when the file is required.
	#=======================================

	function auto_run()
	{
		if ( ! $this->ifthd->member['acp']['look_skin'] )
		{
			$this->ifthd->skin->error('no_perm');
		}
		
		$this->ifthd->skin->set_section( 'Look &amp; Feel' );		
		$this->ifthd->skin->set_description( 'Manage the presentation of your helpdesk with skins and languages.' );

		switch( $this->ifthd->input['code'] )
	    {
	    	case 'list':
				$this->list_skins();
	    	break;
	    	case 'prop':
				$this->show_prop();
	    	break;
	    	case 'css':
				$this->show_css();
	    	break;
	    	case 'wrapper':
	    		$this->show_wrapper();
	    	break;
	    	case 'templates':
	    		$this->list_templates();
	    	break;
	    	case 'edittpl':
	    		$this->edit_tpl();
	    	break;
	    	case 'tools':
	    		$this->show_tools();
	    	break;
    		case 'import':
    			$this->show_import();
    		break;
    		case 'export':
    			$this->list_skins( '', '', 'export');
    		break;

	    	case 'doprop':
				$this->do_prop();
	    	break;
    		case 'docss':
    			$this->do_css();
    		break;
    		case 'dowrapper':
    			$this->do_wrapper();
    		break;
	    	case 'doedittpl':
	    		$this->do_edit_tpl();
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
    			$this->list_skins();
    		break;
		}
	}

	#=======================================
	# @ List Skins
	# Show a list of skin sets.
	#=======================================

	function list_skins($error='', $alert='', $instr='')
	{
		#=============================
		# Grab Skins
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'name', 'default' ),
											  	 'from'		=> 'skins',
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$skin_rows = ""; // Initialize for Security
		$row_count = 0; // Initialize for Security

		while( $s = $this->ifthd->core->db->fetch_row() )
		{
			if ( $s['default'] )
			{
				$s['default'] = "<span class='disabled' style='font-weight: normal'>Is Default</span>";
				$s['delete'] = "<span class='disabled'><img src='<! HD_URL !>/images/default/button_delete_disabled.gif' alt='Delete' /></span>";
			}
			else
			{
				$s['default'] = "<a href='<! HD_URL !>/admin.php?section=look&amp;act=skin&amp;code=default&amp;id={$s['id']}'>Make Default</a>";
				$s['delete'] = "<a href='<! HD_URL !>/admin.php?section=look&amp;act=skin&amp;code=delete&amp;id={$s['id']}' onclick='return sure_delete()'><img src='<! HD_URL !>/images/default/button_delete.gif' alt='Delete' /></a>";
			}
			
			$row_count ++;
					
			( $row_count & 1 ) ? $row_class = 'option1-med' : $row_class = 'option2-med';

			$skin_rows .= "<tr>
								<td class='{$row_class}'><a href='<! HD_URL !>/admin.php?section=look&amp;act=skin&amp;code=prop&amp;id={$s['id']}'>{$s['name']}</a></td>
								<td class='{$row_class}' align='center'>{$s['default']}</td>
								<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=look&amp;act=skin&amp;code=css&amp;id={$s['id']}'><img src='<! HD_URL !>/images/default/page_white_code_red.png' alt='Modify CSS' /></a></td>
								<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=look&amp;act=skin&amp;code=wrapper&amp;id={$s['id']}'><img src='<! HD_URL !>/images/default/page_white_code_red.png' alt='Modify Wrapper' /></a></td>
								<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=look&amp;act=skin&amp;code=templates&amp;id={$s['id']}'><img src='<! HD_URL !>/images/default/page_white_code_red.png' alt='Modify Templates' /></a></td>
								<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=look&amp;act=skin&amp;code=doexport&amp;id={$s['id']}'><img src='<! HD_URL !>/images/default/button_export.gif' alt='Export' /></a></td>
								<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=look&amp;act=skin&amp;code=prop&amp;id={$s['id']}'><img src='<! HD_URL !>/images/default/button_edit.gif' alt='Edit' /></a></td>
								<td class='{$row_class}' align='center'>{$s['delete']}</td>
							</tr>";
		}

		if ( $instr == 'export' )
		{
			$add_txt = "<div class='option1'>To export a skin pack, simply click the Export link next to the appropriate skin pack.</div>";
		}

		#=============================
		# Output
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
								if ( confirm('Are you sure you want to delete this skin pack?') )
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
						<div class='groupbox'>Skin List</div>
						{$add_txt}
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<th width='34%' align='left'>Name</th>
							<th width='10%'>Default</th>
							<th width='7%'>CSS</th>
							<th width='11%'>Wrapper</th>
							<th width='13%'>Templates</th>
							<th width='9%'>Export</th>
							<th width='7%'>Properties</th>
							<th width='9%'>Delete</th>
						</tr>
						". $skin_rows ."
						</table>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=look'>Look &amp; Feel</a>",
						   "<a href='<! HD_URL !>/admin.php?section=look&amp;act=skin'>Skins</a>",
						   "List Skins",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Skins' ) );
	}

	#=======================================
	# @ List Templates
	# Show a list of template.
	#=======================================

	function list_templates()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['look_skin_manage'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'name' ),
											  	 'from'		=> 'skins',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_skin');
		}

		$s = $this->ifthd->core->db->fetch_row();

		#=============================
		# Grab Files
		#=============================
		
		$row_count = 0; // Initialize for Security

		$handle = opendir( HD_PATH. "skin/s". $s['id'] ."/" );

		while( $file = readdir( $handle ) )
		{
			if ( $file != '.' && $file != '..' && $file != 'index.html' && $file != 'wrapper.tpl' )
			{
				$skin_files[] = $file;
			}
		}

		sort( $skin_files );

		while ( list( , $file ) = each( $skin_files ) )
		{
			$row_count ++;
					
			( $row_count & 1 ) ? $row_class = 'option1-med' : $row_class = 'option2-med';
			
			$short_file = str_replace( '.tpl', "", $file );

			if ( $human_names[ $file ] )
			{
				$file_name = $human_names[ $file ];
			}
			else
			{
				$file_name = $file;
			}

			$skin_rows .= "<tr>
								<td class='{$row_class}'><a href='<! HD_URL !>/admin.php?section=look&amp;act=skin&amp;code=edittpl&amp;id={$s['id']}&amp;file={$short_file}'>{$file_name}</a></td>
							</tr>";
		}

		$this->output = "<script type='text/javascript'>

							function sure_delete()
							{
								if ( confirm('Are you sure you want to delete this template?') )
								{
									return true;
								}
								else
								{
									return false;
								}
							}

						</script>
						<div class='groupbox'>Template List</div>
						<table width='100%' cellpadding='3' cellspacing='1' class='smtable'>
						<tr>
							<th align='left'>File Name (Click to Edit)</th>
						</tr>
						". $skin_rows ."
						</table>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=look'>Look &amp; Feel</a>",
						   "<a href='<! HD_URL !>/admin.php?section=look&amp;act=skin'>Skins</a>",
						   $s['name'],
						   "List Templates",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Skins' ) );
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

		if ( ! $this->ifthd->member['acp']['look_skin_manage'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'name', 'img_dir', 'author', 'author_email', 'author_web', 'notes' ),
											  	 'from'		=> 'skins',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_skin');
		}

		$s = $this->ifthd->core->db->fetch_row();

		#=============================
		# Output
		#=============================

		if ( $error )
		{
			$name = $this->ifthd->input['name'];
			$img_dir = $this->ifthd->input['img_dir'];
			$author = $this->ifthd->input['author'];
			$author_email = $this->ifthd->input['author_email'];
			$author_web = $this->ifthd->input['author_web'];
			$notes = $this->ifthd->input['notes'];

			$error = "<div id='smallerror'>
						<p>{lang.err_{$error}}</p>
					</div>";
		}
		else
		{
			$name = $s['name'];
			$img_dir = $s['img_dir'];
			$author = $s['author'];
			$author_email = $s['author_email'];
			$author_web = $s['author_web'];
			$notes = $s['notes'];
		}

		$this->output = "{$error}
						<div class='groupbox'>Edit Properties</div>
						<form action='<! HD_URL !>/admin.php?section=look&amp;act=skin&amp;code=doprop&amp;id={$s['id']}' method='post'>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<td class='option1' width='20%'><label for='name'>Name</label></td>
							<td class='option1' width='80%'><input type='text' name='name' id='name' value='{$name}' size='35' /></td>
						</tr>
						<tr>
							<td class='option2'><label for='img_dir'>Image Directory</label></td>
							<td class='option2'><input type='text' name='img_dir' id='img_dir' value='{$img_dir}' size='35' /></td>
						</tr>
						<tr>
							<td class='option1'><label for='author'>Author</label></td>
							<td class='option1'><input type='text' name='author' id='author' value='{$author}' size='35' /></td>
						</tr>
						<tr>
							<td class='option2'><label for='author_email'>Author Email</label></td>
							<td class='option2'><input type='text' name='author_email' id='author_email' value='{$author_email}' size='35' /></td>
						</tr>
						<tr>
							<td class='option1'><label for='author_web'>Author Website</label></td>
							<td class='option1'><input type='text' name='author_web' id='author_web' value='{$author_web}' size='35' /></td>
						</tr>
						<tr>
							<td class='option2' valign='top'><label for='notes'>Notes</label></td>
							<td class='option2'><textarea name='notes' id='notes' cols='45' rows='3'>{$notes}</textarea></td>
						</tr>
						</table>
						<div class='formtail'><input type='submit' name='submit' id='edit' value='Edit Properties' class='button' /></div>
						</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=look'>Look &amp; Feel</a>",
						   "<a href='<! HD_URL !>/admin.php?section=look&amp;act=skin'>Skins</a>",
						   "Edit Properties",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Skins' ) );
	}

	#=======================================
	# @ Show CSS
	# Show edit CSS form.
	#=======================================

	function show_css()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['look_skin_manage'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id' ),
											  	 'from'		=> 'skins',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_skin');
		}

		$s = $this->ifthd->core->db->fetch_row();

		#=============================
		# Grab CSS
		#=============================

		$css = $this->ifthd->html_safe( file_get_contents( HD_PATH .'skin/s'. $s['id'] .'/style.css' ) );

		if ( ! is_writable( HD_PATH .'skin/s'. $s['id'] .'/style.css' ) )
		{
			$write_warning = "<div class='alert'>Warning: ./skin/s{$s['id']}/style.css is not writable.  Please CHMOD to 0777.</div>";
		}

		$this->output = "{$write_warning}
						<div class='groupbox'>Edit CSS</div>
						<form action='<! HD_URL !>/admin.php?section=look&amp;act=skin&amp;code=docss&amp;id={$s['id']}' method='post'>
						<div class='option1'><textarea name='css' id='css' rows='20' cols='120' style='width: 98%; height: 400px;'>{$css}</textarea></div>
						<div class='formtail'><input type='submit' id='edit' value='Edit CSS' class='button' /></div>
						</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=look'>Look &amp; Feel</a>",
						   "<a href='<! HD_URL !>/admin.php?section=look&amp;act=skin'>Skins</a>",
						   "Edit CSS",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Skins' ) );
	}

	#=======================================
	# @ Show Wrapper
	# Show edit wrappers form.
	#=======================================

	function show_wrapper()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['look_skin_manage'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id' ),
											  	 'from'		=> 'skins',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_skin');
		}

		$s = $this->ifthd->core->db->fetch_row();

		#=============================
		# Grab CSS
		#=============================

		$file_contents = $this->ifthd->html_safe( file_get_contents( HD_PATH .'skin/s'. $s['id'] .'/wrapper.tpl' ) );

		if ( ! is_writable( HD_PATH .'skin/s'. $s['id'] .'/wrapper.tpl' ) )
		{
			$write_warning = "<p class='errortxt'>Warning: ./skin/s{$s['id']}/wrapper.tpl is not writable.  Please CHMOD to 0777.</p>";
		}

		#=============================
		# Output
		#=============================

		$this->output = "{$write_warning}
						<div class='groupbox'>Edit Wrapper</div>
						<form action='<! HD_URL !>/admin.php?section=look&amp;act=skin&amp;code=dowrapper&amp;id={$s['id']}' method='post'>
						<div class='option1'><textarea name='wrapper' id='wrapper' rows='20' cols='120' style='width: 98%; height: 400px;'>{$file_contents}</textarea></div>
						<div class='formtail'><input type='submit' name='submit' id='edit' value='Edit Wrapper' class='button' /></div>
						</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=look'>Look &amp; Feel</a>",
						   "<a href='<! HD_URL !>/admin.php?section=look&amp;act=skin'>Skins</a>",
						   $s['name'],
						   "Edit Wrappers",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Skins' ) );
	}

	#=======================================
	# @ Edit TPL
	# Show edit template form.
	#=======================================

	function edit_tpl($error="")
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['look_skin_manage'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'name' ),
											  	 'from'		=> 'skins',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_skin');
		}

		$s = $this->ifthd->core->db->fetch_row();

		#=============================
		# Grab File
		#=============================

		if ( ! file_exists( HD_PATH .'skin/s'. $s['id'] .'/'. $this->ifthd->input['file'] .'.tpl' ) )
		{
			$this->ifthd->skin->error('no_template');
		}

		$file_contents = $this->ifthd->html_safe( file_get_contents( HD_PATH .'skin/s'. $s['id'] .'/'. $this->ifthd->input['file'] .'.tpl' ) );

		#=============================
		# Output
		#=============================

		if ( ! is_writable( HD_PATH .'skin/s'. $s['id'] .'/'. $this->ifthd->input['file'] .'.tpl' ) )
		{
			$write_warning = "<div class='alert'>Warning: ./skin/s". $s['id'] ."/". $this->ifthd->input['file'] .".tpl is not writable.  Please CHMOD to 0777.</div>";
		}

		if ( $error )
		{
			$template = $this->ifthd->convert_lang( $this->ifthd->html_safe($this->ifthd->input['template'] ) );

			$error = "<div class='critical'>{$error}</div>";
		}
		else
		{
			$template = $this->ifthd->convert_lang( $this->ifthd->html_safe( $s['template'] ) );
		}

		$this->output = "{$error}
						{$write_warning}
						<div class='groupbox'>Editing Template: {$this->ifthd->input['file']}.tpl</div>
						<form action='<! HD_URL !>/admin.php?section=look&amp;act=skin&amp;code=doedittpl&amp;id={$s['id']}&amp;file={$this->ifthd->input['file']}' method='post'>
						<div class='option1'><textarea name='template' id='template' rows='20' cols='120' style='width: 98%; height: 400px;'>{$file_contents}</textarea></div>
						<div class='formtail'><input type='submit' name='submit' id='edit' value='Edit Template' class='button' /></div>
						</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=look'>Look &amp; Feel</a>",
						   "<a href='<! HD_URL !>/admin.php?section=look&amp;act=skin'>Skins</a>",
						   "<a href='<! HD_URL !>/admin.php?section=look&amp;act=skin&amp;code=templates&amp;id={$s['id']}'>{$s['name']}</a>",
						   "Edit Template",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Skins' ) );
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

		if ( ! $this->ifthd->member['acp']['look_skin_manage'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		if ( ! $this->ifthd->input['name'] )
		{
			$this->show_prop('no_name');
		}

		if ( ! $this->ifthd->input['img_dir'] )
		{
			$this->show_prop('no_img_dir');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'name' ),
											  	 'from'		=> 'skins',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_skin');
		}

		$s = $this->ifthd->core->db->fetch_row();

		#=============================
		# Update Properties
		#=============================

		$db_array = array(
						  'name'			=> $this->ifthd->input['name'],
						  'img_dir'			=> $this->ifthd->input['img_dir'],
						  'author'			=> $this->ifthd->input['author'],
						  'author_email'	=> $this->ifthd->input['author_email'],
						  'author_web'		=> $this->ifthd->input['author_web'],
						  'notes'			=> $this->ifthd->input['notes'],
						  );

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'skins',
											  	 'set'		=> $db_array,
							 				  	 'where'	=> array( 'id', '=', $s['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->rebuild_skin_cache();

		$this->ifthd->log( 'admin', "Skin Properites Updated &#039;". $s['name'] ."&#039;" );

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=look&act=skin', 'prop_skin_success' );
		$this->list_skins( '', 'The skin information has been successfully updated.' );
	}

	#=======================================
	# @ Do CSS
	# Update CSS.
	#=======================================

	function do_css()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['look_skin_manage'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'name' ),
											  	 'from'		=> 'skins',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_skin');
		}

		$s = $this->ifthd->core->db->fetch_row();

		#=============================
		# Update CSS
		#=============================

		if ( ! is_writable( HD_PATH .'skin/s'. $s['id'] .'/style.css' ) )
		{
			$this->ifthd->skin->error('not_writable');
		}

		$handle = @fopen( HD_PATH .'skin/s'. $s['id'] .'/style.css', 'w' );

		if ( ! @fwrite( $handle, $this->ifthd->convert_html( $this->ifthd->input['css'] ) ) )
		{
			$this->ifthd->skin->error('not_writable');
		}

		@fclose($handle);

		$this->ifthd->log( 'admin', "Skin CSS Updated &#039;". $s['name'] ."&#039;" );

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=look&act=skin', 'css_skin_success' );
		$this->list_skins( '', 'The skin CSS has been successfully updated.' );
	}

	#=======================================
	# @ Do Wrapper
	# Update wrappers.
	#=======================================

	function do_wrapper()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['look_skin_manage'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'name' ),
											  	 'from'		=> 'skins',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_skin');
		}

		$s = $this->ifthd->core->db->fetch_row();

		#=============================
		# Check File
		#=============================

		if ( ! file_exists( HD_PATH .'skin/s'. $s['id'] .'/wrapper.tpl' ) )
		{
			$this->ifthd->skin->error('no_template');
		}

		#=============================
		# Update Template
		#=============================

		$file_contents = $this->ifthd->convert_lang( $this->ifthd->convert_html( $this->ifthd->input['wrapper'] ), 2 );

		$handle = @fopen( HD_PATH .'skin/s'. $s['id'] .'/wrapper.tpl', 'w' );

		if ( ! @fwrite( $handle, $file_contents ) )
		{
			$this->ifthd->skin->error('not_writable');
		}

		@fclose($handle);

		$this->ifthd->log( 'admin', "Skin Wrapper Updated &#039;". $s['name'] ."&#039;" );

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=look&act=skin', 'wrapper_skin_success' );
		$this->list_skins( '', 'The wrapper information has been successfully updated.' );
	}

	#=======================================
	# @ Do Edit TPL
	# Update template.
	#=======================================

	function do_edit_tpl()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['look_skin_manage'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'name' ),
											  	 'from'		=> 'skins',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_skin');
		}

		$s = $this->ifthd->core->db->fetch_row();

		#=============================
		# Check File
		#=============================

		if ( ! file_exists( HD_PATH .'skin/s'. $s['id'] .'/'. $this->ifthd->input['file'] .'.tpl' ) )
		{
			$this->ifthd->skin->error('no_template');
		}

		#=============================
		# Update Template
		#=============================

		$file_contents = $this->ifthd->convert_lang( $this->ifthd->convert_html( $this->ifthd->input['template'] ), 2 );

		$handle = @fopen( HD_PATH .'skin/s'. $s['id'] .'/'. $this->ifthd->input['file'] .'.tpl', 'w' );

		if ( ! @fwrite( $handle, $file_contents ) )
		{
			$this->ifthd->skin->error('not_writable');
		}

		@fclose($handle);

		$this->ifthd->log( 'admin', "Skin Template Edited &#039;". $this->ifthd->input['file'] .".tpl&#039;", 0, $s['id'] );

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=look&act=skin&code=templates&id='. $s['id'], 'template_skin_success' );
		$this->list_skins( '', 'The skin template has been successfully updated.' );
	}

	#=======================================
	# @ Show Import
	# Display import skin file page.
	#=======================================

	function show_import()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['acp']['look_skin_import'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		#=============================
		# Do Output
		#=============================

		if ( ! is_writable( HD_PATH .'skin/' ) )
		{
			$write_warning = "<p class='errortxt'>Warning: ./skin/ is not writable.  Please CHMOD to 0777.</p>";
		}

		$this->output = "<script type='text/javascript'>

							function validate_form(form)
							{
								if ( ! form.skin_file.value )
								{
									alert('Please select a file to upload.');
									form.skin_file.focus();
									return false;
								}
							}

						</script>
						{$write_warning}
						<div class='groupbox'>Import a Skin</div>
						<div class='subbox'>Please select a valid Trellis Desk XML Skin File to import.</div>
						<form enctype='multipart/form-data' action='<! HD_URL !>/admin.php?section=look&amp;act=skin&amp;code=doimport' method='post' onsubmit='return validate_form(this)'>
						<div class='option1'><input type='file' name='skin_file' id='skin_file' size='40' /></div>
						<div class='formtail'><input type='submit' name='upload' id='upload' value='Import' class='button' /></div>
						</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=look'>Look &amp; Feel</a>",
						   "<a href='<! HD_URL !>/admin.php?section=look&amp;act=skin'>Skins</a>",
						   "Import Skin",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Skins' ) );
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

		if ( ! $this->ifthd->member['acp']['look_skin_tools'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$skin_drop = $this->ifthd->build_skin_drop();

		$this->output = "<div class='groupbox'>Skin Tools</div>
						<form action='<! HD_URL !>/admin.php?section=look&amp;act=skin&amp;code=dotools' method='post'>
						<div class='option1'>Switch ALL users to skin: <select name='all_skin' id='all_skin'>{$skin_drop}</select> <input type='submit' class='submit' name='all_users' id='all_users' value='Switch' /></div>
						<div class='option2'>Users using skin <select name='first_skin' id='first_skin'>{$skin_drop}</select> switch to <select name='sec_skin' id='sec_skin'>{$skin_drop}</select> <input type='submit' class='submit' name='some_users' id='some_users' value='Switch' /></div>
						<div class='option1'><input type='submit' class='submit' name='chmod' id='chmod' value='Set Skin File Permissions' /> (CHMODs to 0777 for outside editing)</div>
						</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=look'>Look &amp; Feel</a>",
						   "<a href='<! HD_URL !>/admin.php?section=look&amp;act=skin'>Skins</a>",
						   "Skin Tools",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Skins' ) );
	}

	#=======================================
	# @ Do Import
	# Uploads the specified XML file and
	# attempts to parse it, then finally
	# create the new skin pack.
	#=======================================

	function do_import()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['acp']['look_skin_import'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		#=============================
		# Upload File
		#=============================

		if ( ! $_FILES['skin_file']['size'] )
		{
			$this->ifthd->skin->error('no_upload_size');
		}

		if ( $_FILES['skin_file']['type'] != 'text/xml' )
		{
			$this->ifthd->skin->error('no_upload_skin_xml');
		}

		if ( ! is_writable( HD_PATH .'skin/' ) )
		{
			$this->ifthd->skin->error('not_writable_skin');
		}

		if ( ! is_writable( HD_PATH .'images/' ) )
		{
			$this->ifthd->skin->error('not_writable_img_dir');
		}

		$data = $this->ifthd->parseFile( $_FILES['skin_file']['tmp_name'], 2 );

		#=============================
		# Format
		#=============================

		$sinfo = $data[0];
		$templates = $data[1];
		$images = $data[2];

		#=============================
		# Check Image Directory
		#=============================

		if ( is_dir( HD_PATH ."images/". $sinfo['sk_img_dir'] ) )
		{
			$sinfo['sk_img_dir'] = $this->find_img_dir( $sinfo['sk_img_dir'] );
		}

		#=============================
		# Create Image Directory
		#=============================

		if ( ! @mkdir( HD_PATH ."images/". $sinfo['sk_img_dir'] ) )
		{
			
			$this->ifthd->skin->error('not_writable_img_dir');
		}

		#=============================
		# Insert Skin
		#=============================

		$db_array = array(
						  'name'			=> $this->ifthd->sanitize_data( $sinfo['sk_name'] ),
						  'img_dir'			=> mysql_real_escape_string( $sinfo['sk_img_dir'] ),
						  'author'			=> $this->ifthd->sanitize_data( $sinfo['sk_author'] ),
						  'author_email'	=> $this->ifthd->sanitize_data( $sinfo['sk_author_email'] ),
						  'author_web'		=> $this->ifthd->sanitize_data( $sinfo['sk_author_web'] ),
						  'notes'			=> $this->ifthd->sanitize_data( $sinfo['sk_notes'] ),
						 );

		$this->ifthd->core->db->construct( array(
											  	 'insert'	=> 'skins',
											  	 'set'		=> $db_array,
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$skin_id = $this->ifthd->core->db->get_insert_id();

		#=============================
		# Insert Templates
		#=============================

		if ( ! @ mkdir( HD_PATH .'skin/s'. $skin_id ) )
		{
			$this->ifthd->skin->error('not_writable_skin');
		}

		while ( list( , $tinfo ) = each( $templates ) )
		{
			$handlec = @fopen( HD_PATH ."skin/s". $skin_id ."/". $tinfo['tname'], 'w' );

			@fwrite( $handlec, $tinfo['tcontent'] );

			@fclose($handlec);
		}

		#=============================
		# Insert Images
		#=============================

		while ( list( , $iinfo ) = each( $images ) )
		{
			if ( $iinfo['path'] )
			{
				if ( ! is_dir( HD_PATH ."images/". $sinfo['sk_img_dir'] ."/". $iinfo['path'] ) )
				{
					@mkdir( HD_PATH ."images/". $sinfo['sk_img_dir'] ."/". $iinfo['path'] );
				}

				$image_file = $iinfo['path'] ."/". $iinfo['filename'];
			}
			else
			{
				$image_file = $iinfo['filename'];
			}

			$handleb = @fopen( HD_PATH ."images/". $sinfo['sk_img_dir'] ."/". $image_file, 'w' );

			@fwrite( $handleb, $iinfo['content'] );

			@fclose($handleb);
		}

		$this->ifthd->log( 'admin', "Skin Pack &#039;". $sinfo['sk_name'] ."&#039; Imported" );

		#=============================
		# Files
		#=============================

		$handle = @fopen( HD_PATH .'skin/s'. $skin_id .'/style.css', 'w' );

		@fwrite( $handle, $sinfo['sk_css'] );

		@fclose($handle);

		$this->ifthd->rebuild_skin_cache();

		#=============================
		# Redirect
		#=============================

		$this->ifthd->skin->redirect( '?section=look&act=skin', 'import_skin_success' );
		$this->list_skins( '', 'The skin has been successfully imported.' );
	}

	#=======================================
	# @ Do Export
	# Exports a specified skin pack and
	# generates an XML file.
	#=======================================

	function do_export()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['look_skin_export'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'skins',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_skin');
		}

		$s = $this->ifthd->core->db->fetch_row();

		$safe_name = strtolower( $s['name'] );
		$safe_name = str_replace( " ", "_", $safe_name );
		$safe_name = ereg_replace( "[^a-z0-9_]", "", $safe_name );

		#=============================
		# Grab Files
		#=============================

		$handle = opendir( HD_PATH. "skin/s". $s['id'] ."/" );

		while( $file = readdir( $handle ) )
		{
			if ( $file != '.' && $file != '..' && $file != 'index.html' && $file != 'style.css' )
			{
				$skin_files[] = $file;
			}
		}

		sort( $skin_files );

		#=============================
		# Generate XML
		#=============================

		$css = file_get_contents( HD_PATH .'skin/s'. $s['id'] .'/style.css' );

		$file_data = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<skin_pack>\n";

		$file_data .= "\t<skin_info>\n";
		$file_data .= "\t\t<sk_name>". chunk_split( base64_encode( $s['name'] ) ) ."</sk_name>\n";
		$file_data .= "\t\t<sk_img_dir>". chunk_split( base64_encode( $s['img_dir'] ) ) ."</sk_img_dir>\n";
		$file_data .= "\t\t<sk_author>". chunk_split( base64_encode( $s['author'] ) ) ."</sk_author>\n";
		$file_data .= "\t\t<sk_author_email>". chunk_split( base64_encode( $s['author_email'] ) ) ."</sk_author_email>\n";
		$file_data .= "\t\t<sk_author_web>". chunk_split( base64_encode( $s['author_web'] ) ) ."</sk_author_web>\n";
		$file_data .= "\t\t<sk_notes>". chunk_split( base64_encode( $s['notes'] ) ) ."</sk_notes>\n";
		$file_data .= "\t\t<sk_css>". chunk_split( base64_encode( $css ) ) ."</sk_css>\n";
		$file_data .= "\t</skin_info>\n";

		$file_data .= "\t<skin_files>\n";

		while ( list( , $file ) = each( $skin_files ) )
		{
			$file_contents = file_get_contents( HD_PATH .'skin/s'. $s['id'] .'/'. $file );

			$file_data .= "\t\t<template>\n";
			$file_data .= "\t\t\t<tname>". chunk_split( base64_encode( $file ) ) ."</tname>\n";
			$file_data .= "\t\t\t<tcontent>". chunk_split( base64_encode( $file_contents ) ) ."</tcontent>\n";
			$file_data .= "\t\t</template>\n";
		}

		$file_data .= "\t</skin_files>\n";

		$file_data .= "\t<skin_images>\n";

		$handle = opendir( HD_PATH ."images/". $s['img_dir'] );

		while( $file = readdir( $handle ) )
		{
			if ( $file != '.' && $file != '..' && $file != 'index.html' && $file != 'Thumbs.db' )
			{
				if ( is_dir( HD_PATH ."images/". $s['img_dir'] ."/". $file ) )
				{
					$handlec = opendir( HD_PATH ."images/". $s['img_dir'] ."/". $file );

					while( $fileb = readdir( $handlec ) )
					{
						if ( $fileb != '.' && $fileb != '..' && $fileb != 'index.html' && $fileb != 'Thumbs.db' )
						{
							if ( $handled = fopen( HD_PATH ."images/". $s['img_dir'] ."/". $file ."/". $fileb, 'rb' ) )
							{
								$file_data .= "\t\t<image>\n";
								$file_data .= "\t\t\t<filename>". chunk_split( base64_encode( $fileb ) ) ."</filename>\n";
								$file_data .= "\t\t\t<content>". chunk_split( base64_encode( @fread( $handled, filesize( HD_PATH ."images/". $s['img_dir'] ."/". $file ."/". $fileb ) ) ) ) ."</content>\n";
								$file_data .= "\t\t\t<path>". chunk_split( base64_encode( $file ) ) ."</path>\n";
								$file_data .= "\t\t</image>\n";
								fclose( $handled );
							}
						}
					}

					fclose($handlec);
				}
				else
				{
					if ( $handleb = fopen( HD_PATH ."images/". $s['img_dir'] ."/". $file, 'rb' ) )
					{
						$file_data .= "\t\t<image>\n";
						$file_data .= "\t\t\t<filename>". chunk_split( base64_encode( $file ) ) ."</filename>\n";
						$file_data .= "\t\t\t<content>". chunk_split( base64_encode( fread( $handleb, filesize( HD_PATH ."images/". $s['img_dir'] ."/". $file ) ) ) ) ."</content>\n";
						$file_data .= "\t\t\t<path></path>\n";
						$file_data .= "\t\t</image>\n";
						fclose( $handleb );
					}
				}
			}
		}

		fclose($handle);

		$file_data .= "\t</skin_images>\n";

		$this->ifthd->log( 'admin', "Skin Pack &#039;". $s['name'] ."&#039; Exported" );

		$file_data .= "</skin_pack>";

		header('Content-type: text/xml');

		header('Content-Disposition: attachment; filename="skin_'. $safe_name .'_td.xml"');

		print $file_data;
	}

	#=======================================
	# @ Do Delete
	# Delete skin.
	#=======================================

	function do_delete()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['look_skin_manage'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'name' ),
											  	 'from'		=> 'skins',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_skin');
		}

		$s = $this->ifthd->core->db->fetch_row();

		#=============================
		# Delete Skin
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'delete'	=> 'skins',
							 				  	 'where'	=> array( 'id', '=', $s['id'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		#=============================
		# Grab Files & Delete
		#=============================

		$handle = @opendir( HD_PATH ."skin/s". $s['id'] ."/" );

		while( $file = @readdir( $handle ) )
		{
			if ( $file != '.' && $file != '..' )
			{
				@unlink( HD_PATH ."skin/s". $s['id'] ."/". $file );
			}
		}

		@rmdir( HD_PATH ."skin/s". $s['id'] );

		#=============================
		# Switch Users To Default
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'members',
											  	 'set'		=> array( 'skin' => $this->ifthd->core->cache['skin']['default'] ),
							 				  	 'where'	=> array( 'skin', '=', $s['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'admin', "Skin Set &#039;". $s['name'] ."&#039; Deleted", 2 );

		$this->ifthd->rebuild_skin_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=look&act=skin', 'delete_skin_success' );
		$this->list_skins( 'The skin information has been successfully deleted.' );
	}

	#=======================================
	# @ Do Default
	# Sets the specified skin pack as
	# default.
	#=======================================

	function do_default()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['look_skin_manage'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'name', 'default' ),
											  	 'from'		=> 'skins',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0, 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_skin');
		}

		$s = $this->ifthd->core->db->fetch_row();

		if ( $s['default'] )
		{
			$this->ifthd->skin->error('no_delete_default_skin');
		}

		#=============================
		# Revert Old Default
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id' ),
											  	 'from'		=> 'skins',
							 				  	 'where'	=> array( 'default', '=', 1 ),
							 				  	 'limit'	=> array( 0, 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$sd = $this->ifthd->core->db->fetch_row();

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'skins',
											  	 'set'		=> array( 'default' =>  0 ),
							 				  	 'where'	=> array( 'id', '=', $sd['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		#=============================
		# Set New Default
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'skins',
											  	 'set'		=> array( 'default' => 1 ),
							 				  	 'where'	=> array( 'id', '=', $s['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->rebuild_skin_cache();

		$this->ifthd->log( 'admin', "Skin Set &#039;". $s['name'] ."&#039; Set as Default" );

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=look&act=skin', 'default_skin_success' );
		$this->list_skins( '', 'The skin has been successfully set to default.' );
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

		if ( ! $this->ifthd->member['acp']['look_skin_tools'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		if ( $this->ifthd->input['all_users'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'members',
												  	 'set'		=> array( 'skin' => $this->ifthd->input['all_skin'] ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();
		}
		elseif ( $this->ifthd->input['some_users'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'members',
												  	 'set'		=> array( 'skin' => $this->ifthd->input['sec_skin'] ),
												  	 'where'	=> array( 'skin', '=', $this->ifthd->input['first_skin'] ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();
		}
		elseif ( $this->ifthd->input['chmod'] )
		{
			$handle = opendir( HD_PATH. "skin/" );

			while( $file = readdir( $handle ) )
			{
				if ( $file != '.' && $file != '..' && $file != 'index.html' )
				{
					if ( is_dir( HD_PATH. "skin/". $file ."/" ) )
					{
						@chmod( HD_PATH. "skin/". $file ."/", 0777 );
						
						$handleb = opendir( HD_PATH. "skin/". $file ."/" );
						
						while( $fileb = readdir( $handleb ) )
						{
							if ( $fileb != '.' && $fileb != '..' && $fileb != 'index.html' )
							{
								@chmod( HD_PATH. "skin/". $file ."/". $fileb, 0777 );
							}
						}
					}
				}
			}
		}

		$this->ifthd->log( 'admin', "Skin Tools Ran" );

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=look&act=skin&code=tools', 'switch_skin_success' );
		$this->list_skins( '', 'The skin tools have been successfully run.' );
	}

	#=======================================
	# @ Key Check
	# Checks to see if profile key is valid.
	#=======================================

	function key_check($key)
	{
		if ( preg_match( '/^[a-z0-9_]*$/', $key ) )
		{
			return TRUE;
		}

		return FALSE;
	}

	#=======================================
	# @ Find Img Dir
	# Finds a usable image directory name.
	#=======================================

	function find_img_dir($org_dir, $num=1)
	{
		$num ++;

		$new_dir = $org_dir . $num;

		if ( is_dir( HD_PATH ."images/". $new_dir ) )
		{
			return $this->find_img_dir( $org_dir, $num );
		}
		else
		{
			return $new_dir;
		}
	}

}

?>