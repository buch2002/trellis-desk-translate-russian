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
|    | Admin Article
#======================================================
*/

class ad_article {

	#=======================================
	# @ Auto Run
	# Function that is run automatically
	# when the file is required.
	#=======================================

	function auto_run()
	{
		if ( ! $this->ifthd->member['acp']['manage_article'] )
		{
			$this->ifthd->skin->error('no_perm');
		}
		
		$this->ifthd->skin->set_section( 'Knowledge Base / Custom Pages Control' );		
		$this->ifthd->skin->set_description( 'Manage your knowledge base, categories, articles and custom pages.' );

		if ( $this->ifthd->input['act'] == 'kb' )
		{
			switch( $this->ifthd->input['code'] )
	    	{
	    		case 'list':
					$this->list_articles();
	    		break;
	    		case 'add':
	    			$this->add_article();
	    		break;
	    		case 'edit':
	    			$this->edit_article();
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

	    		default:
	    			$this->show_cats();
	    		break;
			}
		}
		elseif ( $this->ifthd->input['act'] == 'kbcat' )
		{
			switch( $this->ifthd->input['code'] )
	    	{
	    		case 'list':
					$this->show_cats();
	    		break;
	    		case 'edit':
	    			$this->edit_cat();
	    		break;
	    		case 'delete':
	    			$this->delete_cat();
	    		break;

	    		case 'doadd':
	    			$this->do_add_cat();
	    		break;
	    		case 'doedit':
	    			$this->do_edit_cat();
	    		break;
	    		case 'dodel':
	    			$this->do_delete_cat();
	    		break;

	    		default:
	    			$this->show_cats();
	    		break;
			}
		}
	}

	#=======================================
	# @ Show Categories
	# Show the categories / default page.
	#=======================================

	function show_cats($error='', $alert='')
	{
		#=============================
		# Grab Departments
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'categories',
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$cat_rows = ""; // Initialize for Security
		$row_count = 0; // Initialize for Security

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			while( $c = $this->ifthd->core->db->fetch_row() )
			{
				$row_count ++;
					
				( $row_count & 1 ) ? $row_class = 'option1-med' : $row_class = 'option2-med';
				
				#=============================
				# Fix Up Information
				#=============================

				$c['description'] = $this->ifthd->shorten_str( $c['description'], 80, 1 );

				$cat_rows .= "<tr>
									<td class='{$row_class}'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb&amp;code=list&amp;cat={$c['id']}'>{$c['id']}</a></td>
									<td class='{$row_class}'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb&amp;code=list&amp;cat={$c['id']}'>{$c['name']}</a></td>
									<td class='{$row_class}' style='font-weight: normal'>{$c['description']}</td>
									<td class='{$row_class}' align='center'>{$c['articles']}</td>
									<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=kbcat&amp;code=edit&amp;id={$c['id']}'><img src='<! IMG_DIR !>/button_edit.gif' alt='Edit' /></a></td>
									<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=kbcat&amp;code=delete&amp;id={$c['id']}'><img src='<! IMG_DIR !>/button_delete.gif' alt='Delete' /></a></td>
								</tr>";
			}
		}
		else
		{
			$cat_rows .= "<tr>
								<td class='option1' colspan='6'>There are no categories to display.</td>
							</tr>";
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

		$this->output = "{$error}
						<div class='groupbox'><div style='float:right'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings&amp;code=find&amp;group=kb' title='Visit relevant settings page'><img src='<! IMG_DIR !>/button_mini_settings.gif' alt='Settings' /></a></div>Categories List</div>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<th width='5%' align='left'>ID</th>
							<th width='21%' align='left'>Name</th>
							<th width='51%' align='left'>Description</th>
							<th width='8%'>Articles</th>
							<th width='6%'>Edit</th>
							<th width='9%'>Delete</th>
						</tr>
						". $cat_rows ."
						</table><br />
						
						<script type='text/javascript'>

						function validate_form(form)
						{
							if ( ! form.name.value )
							{
								alert('Please enter a name.');
								form.name.focus();
								return false;
							}

							if ( ! form.description.value )
							{
								alert('Please enter a description.');
								form.description.focus();
								return false;
							}
						}

						</script>

						<form action='<! HD_URL !>/admin.php?section=manage&amp;act=kbcat&amp;code=doadd' method='post' onsubmit='return validate_form(this)'>
						<div class='groupbox'>Add A New Category</div>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<td class='option1' width='17%'><label for='name'>Name</label></td>
							<td class='option1' width='83%'><input type='text' name='name' id='name' value='{$this->ifthd->input['name']}' size='35' /></td>
						</tr>
						<tr>
							<td class='option2' valign='top'><label for='description'>Description</label></td>
							<td class='option2'><textarea name='description' id='description' cols='50' rows='2'>{$this->ifthd->input['description']}</textarea></td>
						</tr>
						</table>
						<div class='formtail'><input type='submit' name='submit' id='add' value='Add Category' class='button' /></div>
						</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb'>Knowledge Base</a>",
						   "List Categories",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Categories' ) );
	}

	#=======================================
	# @ Edit Category
	# Show edit category form.
	#=======================================

	function edit_cat($error="")
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_cat_edit'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'categories',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_category');
		}

		$c = $this->ifthd->core->db->fetch_row();

		#=============================
		# Do Output
		#=============================

		if ( $error )
		{
			$error = "<div class='critical'>{$error}}</div>";

			$name = $this->ifthd->input['name'];
			$description = $this->ifthd->input['description'];
		}
		else
		{
			$name = $c['name'];
			$description = $c['description'];
		}

		$this->output = "<script type='text/javascript'>

						function validate_form(form)
						{
							if ( ! form.name.value )
							{
								alert('Please enter a name.');
								form.name.focus();
								return false;
							}

							if ( ! form.description.value )
							{
								alert('Please enter a description.');
								form.description.focus();
								return false;
							}
						}

						</script>

						<form action='<! HD_URL !>/admin.php?section=manage&amp;act=kbcat&amp;code=doedit&amp;id={$c['id']}' method='post' onsubmit='return validate_form(this)'>
						<div class='groupbox'>Editing Category: {$c['name']}</div>
						{$error}
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<td class='option1' width='17%'><label for='name'>Name</label></td>
							<td class='option1' width='83%'><input type='text' name='name' id='name' value='{$name}' size='35' /></td>
						</tr>
						<tr>
							<td class='option2' valign='top'><label for='description'>Description</label></td>
							<td class='option2'><textarea name='description' id='description' cols='50' rows='2'>{$description}</textarea></td>
						</tr>
						</table>
						<div class='formtail'><input type='submit' name='submit' id='edit' value='Edit Category' class='button' /></div>
						</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb'>Knowledge Base</a>",
						   "Edit Category",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Categories' ) );
	}

	#=======================================
	# @ Delete Category
	# Show delete category form.
	#=======================================

	function delete_cat()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_cat_delete'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'categories',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_category');
		}

		$c = $this->ifthd->core->db->fetch_row();

		#=============================
		# Do Output
		#=============================

		$cat_drop = $this->ifthd->build_cat_drop( 0, $c['id'] );

		$this->output = "<form action='<! HD_URL !>/admin.php?section=manage&amp;act=kbcat&amp;code=dodel&amp;id={$c['id']}' method='post'>
						<div class='groupbox'>Deleting Category: {$c['name']}</div>
						<div class='subbox'>What would you like to do with the articles in this category?</div>
						<div class='option1'>
							<input type='radio' name='action' id='action1' value='1' checked='checked' /> <label for='action1'>Move the articles to this category:</label> <select name='moveto'>{$cat_drop}</select><br />
							<input type='radio' name='action' id='action2' value='2' /> <label for='action2'>Delete the articles</label>
						</div>
						<div class='formtail'><input type='submit' name='submit' id='delete' value='Delete Category' class='button' /></div>
						</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb'>Knowledge Base</a>",
						   "Delete Category",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Categories' ) );
	}

	#=======================================
	# @ Do Create Category
	# Create a new KB category.
	#=======================================

	function do_add_cat()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['acp']['manage_cat_add'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		if ( ! $this->ifthd->input['name'] )
		{
			$this->show_cats('Please enter a name.');
		}

		if ( ! $this->ifthd->input['description'] )
		{
			$this->show_cats('Please enter a description.');
		}

		#=============================
		# Add Category
		#=============================

		$db_array = array(
						  'name'		=> $this->ifthd->input['name'],
						  'description'	=> $this->ifthd->input['description'],
						 );

		$this->ifthd->core->db->construct( array(
											  	 'insert'	=> 'categories',
											  	 'set'		=> $db_array,
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$cat_id = $this->ifthd->core->db->get_insert_id();

		$this->ifthd->log( 'admin', "KB Category Added &#039;". $this->ifthd->input['name'] ."&#039;", 1, $cat_id );

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_cat_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=kbcat&code=list', 'add_cat_success' );
		$this->show_cats( '', 'The category has been successfully added.' );
	}

	#=======================================
	# @ Do Edit Category
	# Edit a KB category.
	#=======================================

	function do_edit_cat()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_cat_edit'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'categories',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_category');
		}

		if ( ! $this->ifthd->input['name'] )
		{
			$this->edit_cat('Please enter a name.');
		}

		if ( ! $this->ifthd->input['description'] )
		{
			$this->edit_cat('Please enter a description.');
		}

		#=============================
		# Edit Category
		#=============================

		$db_array = array(
						  'name'		=> $this->ifthd->input['name'],
						  'description'	=> $this->ifthd->input['description'],
						 );

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'categories',
											  	 'set'		=> $db_array,
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'admin', "KB Category Updated &#039;". $this->ifthd->input['name'] ."&#039;", 1, $this->ifthd->input['id'] );

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_cat_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=kbcat&code=list', 'edit_cat_success' );
		$this->show_cats( '', 'The category has been successfully updated.' );
	}

	#=======================================
	# @ Do Delete Category
	# Delete a KB category.
	#=======================================

	function do_delete_cat()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_cat_delete'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'categories',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_category');
		}

		$c = $this->ifthd->core->db->fetch_row();

		#=============================
		# Perform Our Action
		#=============================

		if ( $this->ifthd->input['action'] == 1 )
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> 'all',
												  	 'from'		=> 'categories',
								 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['moveto'] ),
								 				  	 'limit'	=> array( 0,1 ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			if ( ! $this->ifthd->core->db->get_num_rows() )
			{
				$this->ifthd->skin->error('no_category');
			}

			$cb = $this->ifthd->core->db->fetch_row();

			#=============================
			# Update New Category
			#=============================

			$this->ifthd->core->db->construct( array(
												  	 'select'	=> 'all',
												  	 'from'		=> 'articles',
								 				  	 'where'	=> array( 'cat_id', '=', $c['id'] ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$num_articles = $this->ifthd->core->db->get_num_rows();

			$this->ifthd->core->db->next_no_quotes('set');

			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'categories',
												  	 'set'		=> array( 'articles' => 'articles+'. $num_articles ),
								 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['moveto'] ),
								 				  	 'limit'	=> array( 1 ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			#=============================
			# Update Articles
			#=============================

			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'articles',
												  	 'set'		=> array( 'cat_id' => $cb['id'], 'cat_name' => $cb['name'] ),
								 				  	 'where'	=> array( 'cat_id', '=', $c['id'] ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();
		}
		elseif ( $this->ifthd->input['action'] == 2 )
		{
			$this->ifthd->core->db->construct( array(
												  	 'delete'	=> 'articles',
								 				  	 'where'	=> array( 'cat_id', '=', $c['id'] ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();
		}

		#=============================
		# Delete Category
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'delete'	=> 'categories',
							 				  	 'where'	=> array( 'id', '=', $c['id'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'admin', "KB Category Deleted &#039;". $c['name'] ."&#039;", 2, $c['id'] );

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_cat_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=kbcat&code=list', 'delete_cat_success' );
		$this->show_cats( 'The category has been successfully deleted.' );
	}

	#=======================================
	# @ List Articles
	# Show a list of articles.
	#=======================================

	function list_articles($error='', $alert='')
	{
		#=============================
		# Sorting Options
		#=============================

		$this->ifthd->input['cat'] = intval( $this->ifthd->input['cat'] );

		$link_extra = ""; // Initialize for Security

		if ( $this->ifthd->input['cat'] )
		{
			$link_extra = '&amp;cat='. $this->ifthd->input['cat'];
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

		$link_id = "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb&amp;code=list&amp;sort=id". $order_id ."'>ID". $img_id ."</a>";
		$link_name = "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb&amp;code=list&amp;sort=name". $order_name ."'>Name". $img_name ."</a>";
		$link_description = "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb&amp;code=list&amp;sort=description". $order_description ."'>Description". $img_description ."</a>";

		if ( $this->ifthd->input['sort'] )
		{
			$link_extra .= "&amp;sort=". $this->ifthd->input['sort'];
		}
		if ( $this->ifthd->input['order'] )
		{
			$link_extra .= "&amp;order=". $this->ifthd->input['order'];
		}

		#=============================
		# Grab Articles
		#=============================

		if ( $this->ifthd->input['st'] )
		{
			$start = $this->ifthd->input['st'];
		}
		else
		{
			$start = 0;
		}

		// Filter?
		if ( $this->ifthd->input['cat'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> 'all',
												  	 'from'		=> 'articles',
												  	 'where'	=> array( 'cat_id', '=', $this->ifthd->input['cat'] ),
							 				  	 	 'order'	=> array( $sort => $order ),
								 		  	  ) 	);
		}
		else
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> 'all',
												  	 'from'		=> 'articles',
							 				  	 	 'order'	=> array( $sort => $order ),
								 		  	  ) 	);
		}

		$this->ifthd->core->db->execute();

		$article_rows = ""; // Initialize for Security
		$row_count = 0; // Initialize for Security

		if ( ! $kb_count = $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_articles_found');
		}

		// Filter?
		if ( $this->ifthd->input['cat'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> 'all',
												  	 'from'		=> 'articles',
												  	 'where'	=> array( 'cat_id', '=', $this->ifthd->input['cat'] ),
							 				  	 	 'order'	=> array( $sort => $order ),
							 				  	 	 'limit'	=> array( $start, 20 ),
								 		  	  ) 	);
		}
		else
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> 'all',
												  	 'from'		=> 'articles',
							 				  	 	 'order'	=> array( $sort => $order ),
							 				  	 	 'limit'	=> array( $start, 20 ),
								 		  	  ) 	);
		}

		$this->ifthd->core->db->execute();

		while( $a = $this->ifthd->core->db->fetch_row() )
		{
			$row_count ++;
				
			( $row_count & 1 ) ? $row_class = 'option1-med' : $row_class = 'option2-med';
			
			#=============================
			# Fix Up Information
			#=============================

			$a['description'] = $this->ifthd->shorten_str( $a['description'], 80, 1 );

			$article_rows .= "<tr>
								<td class='{$row_class}'><a href='<! HD_URL !>/index.php?act=article&amp;code=view&amp;id={$a['id']}' target='_blank'>{$a['id']}</a></td>
								<td class='{$row_class}'><a href='<! HD_URL !>/index.php?act=article&amp;code=view&amp;id={$a['id']}' target='_blank'>{$a['name']}</a></td>
								<td class='{$row_class}' style='font-weight: normal'>{$a['description']}</td>
								<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb&amp;code=edit&amp;id={$a['id']}'><img src='<! IMG_DIR !>/button_edit.gif' alt='Edit' /></a></td>
								<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb&amp;code=dodel&amp;id={$a['id']}' onclick='return sure_delete()'><img src='<! IMG_DIR !>/button_delete.gif' alt='Delete' /></a></td>
							</tr>";
		}

		$page_links = $this->ifthd->page_links( '?section=manage&amp;act=kb&amp;code=list'. $link_extra, $kb_count, 20, $start, 1 );

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
								if ( confirm('Are you sure you want to delete this article?') )
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
						<div class='groupbox'><div style='float:right'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings&amp;code=find&amp;group=kb' title='Visit relevant settings page'><img src='<! IMG_DIR !>/button_mini_settings.gif' alt='Settings' /></a></div>Articles List</div>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<th width='5%' align='left'>{$link_id}</th>
							<th width='25%' align='left'>{$link_name}</th>
							<th width='52%' align='left'>{$link_description}</th>
							<th width='7%'>Edit</th>
							<th width='11%'>Delete</th>
						</tr>
						". $article_rows ."
						</table>
						<div class='formtail'><div class='fb_pad'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb&amp;code=add' class='fake_button'>Add A New Article</a></div></div>
						{$page_links}";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb'>Knowledge Base</a>",
						   "List Articles",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Articles' ) );
	}

	#=======================================
	# @ Add Article
	# Show add article form.
	#=======================================

	function add_article($error="")
	{
		#=============================
		# Do Output
		#=============================

		if ( ! $this->ifthd->member['acp']['manage_article_add'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$cat_drop = $this->ifthd->build_cat_drop();

		if ( $error )
		{
			$error = "<div class='critical'>{$error}</div>";
		}

		$this->output = "<script type='text/javascript'>

						function validate_form(form)
						{
							if ( ! form.name.value  )
							{
								alert('Please enter a title.');
								form.name.focus();
								return false;
							}

							if ( ! form.description.value  )
							{
								alert('Please enter a description.');
								form.description.focus();
								return false;
							}
						}

						</script>";

		if ( $this->ifthd->member['use_rte'] && $this->ifthd->core->cache['config']['enable_kb_rte'] )
		{
			$this->output .= "<script language='javascript' type='text/javascript' src='<! HD_URL !>/includes/tinymce/tiny_mce.js'></script>
							<script language='javascript' type='text/javascript'>
							tinyMCE.init({
								mode : 'exact',
								theme : 'advanced',
								elements : 'article',
								plugins : 'inlinepopups,safari,spellchecker',
								dialog_type : 'modal',
								forced_root_block : false,
								force_br_newlines : true,
								force_p_newlines : false,
								theme_advanced_toolbar_location : 'top',
								theme_advanced_toolbar_align : 'left',
								theme_advanced_path_location : 'bottom',
								theme_advanced_disable : 'styleselect,formatselect',
								theme_advanced_buttons1 : 'bold,italic,underline,strikethrough,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,bullist,numlist,separator,outdent,indent,sub,sup,separator,link,unlink,image,separator,removeformat,cleanup,code',
								theme_advanced_buttons2 : 'cut,copy,paste,separator,undo,redo,separator,forecolor,backcolor,separator,spellchecker,separator,fontsizeselect',
								theme_advanced_buttons3 : '',
								theme_advanced_resize_horizontal : false,
								theme_advanced_resizing : true
							});
							</script>";
		}

		$this->output .= "{$error}
							<form action='<! HD_URL !>/admin.php?section=manage&amp;act=kb&amp;code=doadd' method='post' onsubmit='return validate_form(this)'>
							<div class='groupbox'>Add A New Article</div>
							<table width='100%' cellpadding='0' cellspacing='0'>
							<tr>
								<td class='option1' width='17%'><label for='name'>Title</label></td>
								<td class='option1' width='83%'><input type='text' name='name' id='name' value='{$this->ifthd->input['name']}' size='35' /></td>
							</tr>
							<tr>
								<td class='option2'><label for='category'>Category</label></td>
								<td class='option2'><select name='category' id='category'>{$cat_drop}</select></td>
							</tr>
							<tr>
								<td class='option1' valign='top'><label for='description'>Description</label></td>
								<td class='option1'><textarea name='description' id='description' cols='50' rows='2'>{$this->ifthd->input['description']}</textarea></td>
							</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info1','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Toggle information</a>
										<div id='info1' style='display: none;'>
										<div>
											This will be displayed under the article name on the list articles page.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
								<td class='option2'><label for='keywords'>Keywords</label></td>
								<td class='option2' style='font-weight: normal'><input type='text' name='keywords' id='keywords' value='{$this->ifthd->input['keywords']}' size='35' /> (separate by comma)</td>
							</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info2','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Toggle information</a>
										<div id='info2' style='display: none;'>
										<div>
											These keywords will be used when searching to help improve search results.  Please separate each keyword with a comma.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
								<td class='option1'>Security</td>
								<td class='option1' style='font-weight: normal'>
									". $this->ifthd->skin->checkbox( 'dis_comments', 'Disable Comments', $this->ifthd->input['dis_comments'] ) ."&nbsp;&nbsp;
									". $this->ifthd->skin->checkbox( 'dis_rating', 'Disable Rating', $this->ifthd->input['dis_rating'] ) ."
								</td>
							</tr>
							<tr>
								<td class='option2' colspan='2'><textarea name='article' id='article' rows='10' cols='120' style='width: 98%; height: 350px;'>{$this->ifthd->input['article']}</textarea></td>
							</tr>
							</table>
							<div class='formtail'><input type='submit' name='submit' id='add' value='Add Article' class='button' /></div>
							</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb'>Knowledge Base</a>",
						   "Add Article",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Articles' ) );
	}

	#=======================================
	# @ Edit Article
	# Show edit article form.
	#=======================================

	function edit_article($error="")
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_article_edit'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'articles',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_article');
		}

		$a = $this->ifthd->core->db->fetch_row();

		#=============================
		# Do Output
		#=============================

		if ( $error )
		{
			$error = "<div class='critical'>{$error}</div>";

			$name = $this->ifthd->input['name'];
			$description = $this->ifthd->input['description'];
			$keywords = $this->ifthd->input['keywords'];
			$article = $this->ifthd->input['article'];
			$dis_comments = $this->ifthd->input['dis_comments'];
			$dis_rating = $this->ifthd->input['dis_rating'];

			$cat_drop = $this->ifthd->build_cat_drop( $this->ifthd->input['category'] );
		}
		else
		{
			$name = $a['name'];
			$description = $a['description'];
			$keywords = $a['keywords'];
			$article = $a['article'];
			$dis_comments = $a['dis_comments'];
			$dis_rating = $a['dis_rating'];

			$cat_drop = $this->ifthd->build_cat_drop( $a['cat_id'] );
		}

		$this->output = "<script type='text/javascript'>

						function validate_form(form)
						{
							if ( ! form.name.value  )
							{
								alert('Please enter a title.');
								form.name.focus();
								return false;
							}

							if ( ! form.description.value )
							{
								alert('Please enter a description.');
								form.description.focus();
								return false;
							}
						}

						</script>";

		if ( $this->ifthd->member['use_rte'] && $this->ifthd->core->cache['config']['enable_kb_rte'] )
		{
			$this->output .= "<script language='javascript' type='text/javascript' src='<! HD_URL !>/includes/tinymce/tiny_mce.js'></script>
							<script language='javascript' type='text/javascript'>
							tinyMCE.init({
								mode : 'exact',
								theme : 'advanced',
								elements : 'article',
								plugins : 'inlinepopups,safari,spellchecker',
								dialog_type : 'modal',
								forced_root_block : false,
								force_br_newlines : true,
								force_p_newlines : false,
								theme_advanced_toolbar_location : 'top',
								theme_advanced_toolbar_align : 'left',
								theme_advanced_path_location : 'bottom',
								theme_advanced_disable : 'styleselect,formatselect',
								theme_advanced_buttons1 : 'bold,italic,underline,strikethrough,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,bullist,numlist,separator,outdent,indent,sub,sup,separator,link,unlink,image,separator,removeformat,cleanup,code',
								theme_advanced_buttons2 : 'cut,copy,paste,separator,undo,redo,separator,forecolor,backcolor,separator,spellchecker,separator,fontsizeselect',
								theme_advanced_buttons3 : '',
								theme_advanced_resize_horizontal : false,
								theme_advanced_resizing : true
							});
							</script>";
		}

		$this->output .= "{$error}
							<form action='<! HD_URL !>/admin.php?section=manage&amp;act=kb&amp;code=doedit&amp;id={$a['id']}' method='post' onsubmit='return validate_form(this)'>
							<div class='groupbox'>Editing Article: {$a['name']}</div>
							<table width='100%' cellpadding='0' cellspacing='0'>
							<tr>
								<td class='option1' width='17%'><label for='name'>Title</label></td>
								<td class='option1' width='83%'><input type='text' name='name' id='name' value='{$name}' size='35' /></td>
							</tr>
							<tr>
								<td class='option2'><label for='category'>Category</label></td>
								<td class='option2'><select name='category' id='category'>{$cat_drop}</select></td>
							</tr>
							<tr>
								<td class='option1' valign='top'><label for='description'>Description</label></td>
								<td class='option1'><textarea name='description' id='description' cols='50' rows='2'>{$description}</textarea></td>
							</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info1','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Toggle information</a>
										<div id='info1' style='display: none;'>
										<div>
											This will be displayed under the article name on the list articles page.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
								<td class='option2'><label for='keywords'>Keywords</label></td>
								<td class='option2' style='font-weight: normal'><input type='text' name='keywords' id='keywords' value='{$keywords}' size='35' /> (separate by comma or bar)</td>
							</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info2','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Toggle information</a>
										<div id='info2' style='display: none;'>
										<div>
											These keywords will be used when searching to help improve search results.  Please separate each keyword with a comma.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
								<td class='option1'>Security</td>
								<td class='option1' style='font-weight: normal'>
									". $this->ifthd->skin->checkbox( 'dis_comments', 'Disable Comments', $dis_comments ) ."&nbsp;&nbsp;
									". $this->ifthd->skin->checkbox( 'dis_rating', 'Disable Rating', $dis_rating ) ."
								</td>
							</tr>
							<tr>
								<td class='option2' colspan='2'><textarea name='article' id='article' rows='10' cols='120' style='width: 98%; height: 350px;'>{$article}</textarea></td>
							</tr>
							</table>
							<div class='formtail'><input type='submit' name='submit' id='edit' value='Edit Article' class='button' /></div>
							</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Management</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb'>Knowledge Base</a>",
						   "Edit Article",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Manage Articles' ) );
	}

	#=======================================
	# @ Do Add Article
	# Create a new article.
	#=======================================

	function do_add()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['acp']['manage_article_add'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		if ( ! $this->ifthd->input['name'] )
		{
			$this->add_article('Please enter a name.');
		}

		if ( ! $this->ifthd->input['description'] )
		{
			$this->add_article('Please enter a description.');
		}
		
		$this->ifthd->input['article'] = $this->ifthd->remove_extra_lbs( $this->ifthd->input['article'] );

		if ( ! $this->ifthd->input['article'] )
		{
			$this->add_article('Please enter article content.');
		}

		#=============================
		# Grab Category
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'categories',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['category'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_category');
		}

		$c = $this->ifthd->core->db->fetch_row();

		#=============================
		# Keywords
		#=============================

		$keywords = ""; // Initialize for Security

		if ( $this->ifthd->input['keywords'] )
		{
			$raw_keys = preg_split( "(,|\|)", $this->ifthd->input['keywords'] );

			while ( list( , $good_key ) = each( $raw_keys ) )
    		{
    			$better_key = trim( $good_key );

    			if ( $better_key )
    			{
    				$keywords .= $better_key ."|";
    			}

    			$better_key = "";
    		}

    		$keywords = substr( $keywords, 0, -1 ); // Remove trailing bar
		}

		#=============================
		# Add Article
		#=============================

		$db_array = array(
						  'cat_id'		=> $c['id'],
						  'cat_name'	=> $c['name'],
						  'name'		=> $this->ifthd->input['name'],
						  'description'	=> $this->ifthd->input['description'],
						  'article'		=> $this->ifthd->input['article'],
						  'date'		=> time(),
						  'author_id'	=> $this->ifthd->member['id'],
						  'author_name'	=> $this->ifthd->member['name'],
						  'keywords'	=> $keywords,
						  'dis_comments'=> $this->ifthd->input['dis_comments'],
						  'dis_rating'	=> $this->ifthd->input['dis_rating'],
						 );

		$this->ifthd->core->db->construct( array(
											  	 'insert'	=> 'articles',
											  	 'set'		=> $db_array,
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$article_id = $this->ifthd->core->db->get_insert_id();

		$this->ifthd->log( 'admin', "KB Article Added &#039;". $this->ifthd->input['name'] ."&#039;", 1, $article_id );

		#=============================
		# Update Category
		#=============================

		$this->ifthd->core->db->next_no_quotes('set');

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'categories',
											  	 'set'		=> array( 'articles' => 'articles+1' ),
							 				  	 'where'	=> array( 'id', '=', $c['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_cat_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=kb&code=list&cat='. $c['id'], 'add_article_success' );
		$this->list_articles( '', 'The article has been successfully added.' );
	}

	#=======================================
	# @ Do Edit Article
	# Edit an article.
	#=======================================

	function do_edit()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_article_edit'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'articles',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_article');
		}

		$a = $this->ifthd->core->db->fetch_row();

		if ( ! $this->ifthd->input['name'] )
		{
			$this->edit_article('Please enter a name.');
		}

		if ( ! $this->ifthd->input['description'] )
		{
			$this->edit_article('Please enter a description.');
		}
		
		$this->ifthd->input['article'] = $this->ifthd->remove_extra_lbs( $this->ifthd->input['article'] );

		if ( ! $this->ifthd->input['article'] )
		{
			$this->edit_article('Please enter article content');
		}

		#=============================
		# Move Categories? *NoOoOo...*
		#=============================

		if ( $a['cat_id'] != $this->ifthd->input['category'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> 'all',
												  	 'from'		=> 'categories',
								 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['category'] ),
								 				  	 'limit'	=> array( 0,1 ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			if ( ! $this->ifthd->core->db->get_num_rows() )
			{
				$this->ifthd->skin->error('no_category');
			}

			$c = $this->ifthd->core->db->fetch_row();

			#=============================
			# Update Old Category
			#=============================

			$this->ifthd->core->db->next_no_quotes('set');

			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'categories',
												  	 'set'		=> array( 'articles' => 'articles-1' ),
								 				  	 'where'	=> array( 'id', '=', $a['cat_id'] ),
								 				  	 'limit'	=> array( 1 ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			#=============================
			# Update New Category
			#=============================

			$this->ifthd->core->db->next_no_quotes('set');

			$this->ifthd->core->db->construct( array(
												  	 'update'	=> 'categories',
												  	 'set'		=> array( 'articles' => 'articles+1' ),
								 				  	 'where'	=> array( 'id', '=', $c['id'] ),
								 				  	 'limit'	=> array( 1 ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			#=============================
			# Rebuild Cache
			#=============================

			$this->ifthd->rebuild_cat_cache();
		}
		else
		{
			$c = array(); // Initialize for Security

			$c['id'] = $a['cat_id'];
			$c['name'] = $a['cat_name'];
		}

		#=============================
		# Keywords
		#=============================

		$keywords = ""; // Initialize for Security

		if ( $this->ifthd->input['keywords'] )
		{
			$raw_keys = preg_split( "(,|\|)", $this->ifthd->input['keywords'] );

			while ( list( , $good_key ) = each( $raw_keys ) )
    		{
    			$better_key = trim( $good_key );

    			if ( $better_key )
    			{
    				$keywords .= $better_key ."|";
    			}

    			$better_key = "";
    		}

    		$keywords = substr( $keywords, 0, -1 ); // Remove trailing bar
		}

		#=============================
		# Edit Article
		#=============================

		$db_array = array(
						  'cat_id'		=> $c['id'],
						  'cat_name'	=> $c['name'],
						  'name'		=> $this->ifthd->input['name'],
						  'description'	=> $this->ifthd->input['description'],
						  'article'		=> $this->ifthd->input['article'],
						  'updated'		=> time(),
						  'keywords'	=> $keywords,
						  'dis_comments'=> $this->ifthd->input['dis_comments'],
						  'dis_rating'	=> $this->ifthd->input['dis_rating'],
						 );

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'articles',
											  	 'set'		=> $db_array,
							 				  	 'where'	=> array( 'id', '=', $a['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'admin', "KB Article Updated &#039;". $this->ifthd->input['name'] ."&#039;", 1, $a['id'] );

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_cat_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=kb&code=list&cat='. $c['id'], 'edit_article_success' );
		$this->list_articles( '', 'The article has been successfully updated.' );
	}

	#=======================================
	# @ Do Delete Article
	# Delete an article.
	#=======================================

	function do_delete()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_article_delete'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'articles',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_article');
		}

		$a = $this->ifthd->core->db->fetch_row();

		#=============================
		# DELETE *MwhaAaAaAaAa*
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'delete'	=> 'articles',
							 				  	 'where'	=> array( 'id', '=', $a['id'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'admin', "KB Article Deleted &#039;". $a['name'] ."&#039;", 2, $a['id'] );

		#=============================
		# Update Old Category
		#=============================

		$this->ifthd->core->db->next_no_quotes('set');

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'categories',
											  	 'set'		=> array( 'articles' => 'articles-1' ),
							 				  	 'where'	=> array( 'id', '=', $a['cat_id'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_cat_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=kb&code=list&cat='. $a['cat_id'], 'delete_article_success' );
		$this->list_articles( 'The article has been successfully deleted.' );
	}

}

?>