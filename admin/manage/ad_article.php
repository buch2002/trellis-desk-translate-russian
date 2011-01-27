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
		
		$this->ifthd->skin->set_section( 'База знаний / Управление страницами' );		
		$this->ifthd->skin->set_description( 'Управление базой знаний, категорями, статьями и личными страницами.' );

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
									<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=kbcat&amp;code=edit&amp;id={$c['id']}'><img src='<! IMG_DIR !>/button_edit.gif' alt='Редактировать' /></a></td>
									<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=kbcat&amp;code=delete&amp;id={$c['id']}'><img src='<! IMG_DIR !>/button_delete.gif' alt='Удалить' /></a></td>
								</tr>";
			}
		}
		else
		{
			$cat_rows .= "<tr>
								<td class='option1' colspan='6'>Нет категорий для отображения.</td>
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
						<div class='groupbox'><div style='float:right'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings&amp;code=find&amp;group=kb' title='Посетите страницу настроек'><img src='<! IMG_DIR !>/button_mini_settings.gif' alt='Настройки' /></a></div>Список категорий</div>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<th width='5%' align='left'>ID</th>
							<th width='21%' align='left'>Название</th>
							<th width='51%' align='left'>Описание</th>
							<th width='8%'>Статьи</th>
							<th width='6%'>Редактировать</th>
							<th width='9%'>Удалить</th>
						</tr>
						". $cat_rows ."
						</table><br />
						
						<script type='text/javascript'>

						function validate_form(form)
						{
							if ( ! form.name.value )
							{
								alert('Пожалуйста, введите название.');
								form.name.focus();
								return false;
							}

							if ( ! form.description.value )
							{
								alert('Пожалуйста, введите описание.');
								form.description.focus();
								return false;
							}
						}

						</script>

						<form action='<! HD_URL !>/admin.php?section=manage&amp;act=kbcat&amp;code=doadd' method='post' onsubmit='return validate_form(this)'>
						<div class='groupbox'>Добавить новую категорию</div>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<td class='option1' width='17%'><label for='name'>Название</label></td>
							<td class='option1' width='83%'><input type='text' name='name' id='name' value='{$this->ifthd->input['name']}' size='35' /></td>
						</tr>
						<tr>
							<td class='option2' valign='top'><label for='description'>Описание</label></td>
							<td class='option2'><textarea name='description' id='description' cols='50' rows='2'>{$this->ifthd->input['description']}</textarea></td>
						</tr>
						</table>
						<div class='formtail'><input type='submit' name='submit' id='add' value='Добавить категорию' class='button' /></div>
						</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Управление</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb'>База знаний</a>",
						   "Список категорий",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Управление категориями' ) );
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
								alert('Пожалуйста, введите название.');
								form.name.focus();
								return false;
							}

							if ( ! form.description.value )
							{
								alert('Пожалуйста, введите описание.');
								form.description.focus();
								return false;
							}
						}

						</script>

						<form action='<! HD_URL !>/admin.php?section=manage&amp;act=kbcat&amp;code=doedit&amp;id={$c['id']}' method='post' onsubmit='return validate_form(this)'>
						<div class='groupbox'>Редактирование категории: {$c['name']}</div>
						{$error}
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<td class='option1' width='17%'><label for='name'>Название</label></td>
							<td class='option1' width='83%'><input type='text' name='name' id='name' value='{$name}' size='35' /></td>
						</tr>
						<tr>
							<td class='option2' valign='top'><label for='description'>Описание</label></td>
							<td class='option2'><textarea name='description' id='description' cols='50' rows='2'>{$description}</textarea></td>
						</tr>
						</table>
						<div class='formtail'><input type='submit' name='submit' id='edit' value='Сохранить изменения' class='button' /></div>
						</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Управление</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb'>База знаний</a>",
						   "Редактировать категорию",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Управление категориями' ) );
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
						<div class='groupbox'>Удаление категории: {$c['name']}</div>
						<div class='subbox'>Что вы хотите зделать со статьями в этой категории?</div>
						<div class='option1'>
							<input type='radio' name='action' id='action1' value='1' checked='checked' /> <label for='action1'>Переместить статьи в эту категорию:</label> <select name='moveto'>{$cat_drop}</select><br />
							<input type='radio' name='action' id='action2' value='2' /> <label for='action2'>Удалить все статьи в этой категории</label>
						</div>
						<div class='formtail'><input type='submit' name='submit' id='delete' value='Удалить категорию' class='button' /></div>
						</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Управление</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb'>База знаний</a>",
						   "Удалить категорию",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Управление категориями' ) );
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
			$this->show_cats('Пожалуйста, введите название.');
		}

		if ( ! $this->ifthd->input['description'] )
		{
			$this->show_cats('Пожалуйста, введите описание.');
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

		$this->ifthd->log( 'admin', "БЗ категория добавлена &#039;". $this->ifthd->input['name'] ."&#039;", 1, $cat_id );

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_cat_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=kbcat&code=list', 'add_cat_success' );
		$this->show_cats( '', 'Категория успешно добавлена.' );
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
			$this->edit_cat('Пожалуйста, введите название.');
		}

		if ( ! $this->ifthd->input['description'] )
		{
			$this->edit_cat('Пожалуйста, введите описание.');
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

		$this->ifthd->log( 'admin', "БЗ категория обновлена &#039;". $this->ifthd->input['name'] ."&#039;", 1, $this->ifthd->input['id'] );

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_cat_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=kbcat&code=list', 'edit_cat_success' );
		$this->show_cats( '', 'Категория успешно обновлена.' );
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

		$this->ifthd->log( 'admin', "БЗ категория удалена &#039;". $c['name'] ."&#039;", 2, $c['id'] );

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_cat_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=kbcat&code=list', 'delete_cat_success' );
		$this->show_cats( 'Категория успешно удалена.' );
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
			$$img_var = "&nbsp;<img src='<! IMG_DIR !>/arrow_down.gif' alt='Вниз' />";
		}
		else
		{
			$$order_var = "&amp;order=desc";
			$$img_var = "&nbsp;<img src='<! IMG_DIR !>/arrow_up.gif' alt='Вверх' />";
		}

		$link_id = "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb&amp;code=list&amp;sort=id". $order_id ."'>ID". $img_id ."</a>";
		$link_name = "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb&amp;code=list&amp;sort=name". $order_name ."'>Название". $img_name ."</a>";
		$link_description = "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb&amp;code=list&amp;sort=description". $order_description ."'>Описание". $img_description ."</a>";

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
								<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb&amp;code=edit&amp;id={$a['id']}'><img src='<! IMG_DIR !>/button_edit.gif' alt='Редактировать' /></a></td>
								<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb&amp;code=dodel&amp;id={$a['id']}' onclick='return sure_delete()'><img src='<! IMG_DIR !>/button_delete.gif' alt='Удалить' /></a></td>
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
								if ( confirm('Вы уверены, что хотите удалить эту статью?') )
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
						<div class='groupbox'><div style='float:right'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings&amp;code=find&amp;group=kb' title='Посетите страницу настроек'><img src='<! IMG_DIR !>/button_mini_settings.gif' alt='Настройки' /></a></div>Список статей</div>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<th width='5%' align='left'>{$link_id}</th>
							<th width='25%' align='left'>{$link_name}</th>
							<th width='52%' align='left'>{$link_description}</th>
							<th width='7%'>Редактировать</th>
							<th width='11%'>Удалить</th>
						</tr>
						". $article_rows ."
						</table>
						<div class='formtail'><div class='fb_pad'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb&amp;code=add' class='fake_button'>Add A New Article</a></div></div>
						{$page_links}";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Управление</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb'>База знаний</a>",
						   "Список статей",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Управление статьями' ) );
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
								alert('Пожалуйста, введите название.');
								form.name.focus();
								return false;
							}

							if ( ! form.description.value  )
							{
								alert('Пожалуйста, введите описание.');
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
							<div class='groupbox'>Добавление новой статьи</div>
							<table width='100%' cellpadding='0' cellspacing='0'>
							<tr>
								<td class='option1' width='17%'><label for='name'>Заголовок</label></td>
								<td class='option1' width='83%'><input type='text' name='name' id='name' value='{$this->ifthd->input['name']}' size='35' /></td>
							</tr>
							<tr>
								<td class='option2'><label for='category'>Категория</label></td>
								<td class='option2'><select name='category' id='category'>{$cat_drop}</select></td>
							</tr>
							<tr>
								<td class='option1' valign='top'><label for='description'>Описание</label></td>
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
											Это будет отображаться под названием статьи на странице со списком статей.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
								<td class='option2'><label for='keywords'>Ключевые слова</label></td>
								<td class='option2' style='font-weight: normal'><input type='text' name='keywords' id='keywords' value='{$this->ifthd->input['keywords']}' size='35' /> (через запятую)</td>
							</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info2','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Toggle information</a>
										<div id='info2' style='display: none;'>
										<div>
											Эти ключевые слова будут использоваться при поиске, для улучшения результатов поиска. Ключевые слова должны разделяться запятой.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
								<td class='option1'>Опции</td>
								<td class='option1' style='font-weight: normal'>
									". $this->ifthd->skin->checkbox( 'dis_comments', 'Отключить комментарии', $this->ifthd->input['dis_comments'] ) ."&nbsp;&nbsp;
									". $this->ifthd->skin->checkbox( 'dis_rating', 'Отключить голосование за статью', $this->ifthd->input['dis_rating'] ) ."
								</td>
							</tr>
							<tr>
								<td class='option2' colspan='2'><textarea name='article' id='article' rows='10' cols='120' style='width: 98%; height: 350px;'>{$this->ifthd->input['article']}</textarea></td>
							</tr>
							</table>
							<div class='formtail'><input type='submit' name='submit' id='add' value='Добавить статью' class='button' /></div>
							</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Управление</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb'>База знаний</a>",
						   "Добавить статью",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Управление статьями' ) );
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
								alert('Пожалуйста, введите название.');
								form.name.focus();
								return false;
							}

							if ( ! form.description.value )
							{
								alert('Пожалуйста, введите описание.');
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
							<div class='groupbox'>Редактирование статьи: {$a['name']}</div>
							<table width='100%' cellpadding='0' cellspacing='0'>
							<tr>
								<td class='option1' width='17%'><label for='name'>Заголовок</label></td>
								<td class='option1' width='83%'><input type='text' name='name' id='name' value='{$name}' size='35' /></td>
							</tr>
							<tr>
								<td class='option2'><label for='category'>Категория</label></td>
								<td class='option2'><select name='category' id='category'>{$cat_drop}</select></td>
							</tr>
							<tr>
								<td class='option1' valign='top'><label for='description'>Описание</label></td>
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
											Это будет отображаться под названием статьи на странице со списком статей.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
								<td class='option2'><label for='keywords'>Ключевые слова</label></td>
								<td class='option2' style='font-weight: normal'><input type='text' name='keywords' id='keywords' value='{$keywords}' size='35' /> (через запятую)</td>
							</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info2','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Toggle information</a>
										<div id='info2' style='display: none;'>
										<div>
											Эти ключевые слова будут использоваться при поиске, для улучшения результатов поиска. Ключевые слова должны разделяться запятой.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
								<td class='option1'>Опции</td>
								<td class='option1' style='font-weight: normal'>
									". $this->ifthd->skin->checkbox( 'dis_comments', 'Отключить комментарии', $dis_comments ) ."&nbsp;&nbsp;
									". $this->ifthd->skin->checkbox( 'dis_rating', 'Отключить голосование за статью', $dis_rating ) ."
								</td>
							</tr>
							<tr>
								<td class='option2' colspan='2'><textarea name='article' id='article' rows='10' cols='120' style='width: 98%; height: 350px;'>{$article}</textarea></td>
							</tr>
							</table>
							<div class='formtail'><input type='submit' name='submit' id='edit' value='Сохранить изменения' class='button' /></div>
							</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Управление</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb'>База знаний</a>",
						   "Редактировать статью",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Управление статьями' ) );
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
			$this->add_article('Пожалуйста, введите название.');
		}

		if ( ! $this->ifthd->input['description'] )
		{
			$this->add_article('Пожалуйста, введите описание.');
		}
		
		$this->ifthd->input['article'] = $this->ifthd->remove_extra_lbs( $this->ifthd->input['article'] );

		if ( ! $this->ifthd->input['article'] )
		{
			$this->add_article('Пожалуйста, введите содержание статьи.');
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

		$this->ifthd->log( 'admin', "БЗ статья добавлена &#039;". $this->ifthd->input['name'] ."&#039;", 1, $article_id );

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
		$this->list_articles( '', 'Статья успешно добавлена.' );
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
			$this->edit_article('Пожалуйста, введите название.');
		}

		if ( ! $this->ifthd->input['description'] )
		{
			$this->edit_article('Пожалуйста, введите описание.');
		}
		
		$this->ifthd->input['article'] = $this->ifthd->remove_extra_lbs( $this->ifthd->input['article'] );

		if ( ! $this->ifthd->input['article'] )
		{
			$this->edit_article('Пожалуйста, введите содержание статьи.');
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

		$this->ifthd->log( 'admin', "БЗ статья обновлена &#039;". $this->ifthd->input['name'] ."&#039;", 1, $a['id'] );

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_cat_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=kb&code=list&cat='. $c['id'], 'edit_article_success' );
		$this->list_articles( '', 'Статья успешно обновлена.' );
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

		$this->ifthd->log( 'admin', "БЗ статья удалена &#039;". $a['name'] ."&#039;", 2, $a['id'] );

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
		$this->list_articles( 'Статья успешно удалена.' );
	}

}

?>