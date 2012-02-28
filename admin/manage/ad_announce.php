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
|    | Admin Announcements
#======================================================
*/

class ad_announce {

	#=======================================
	# @ Auto Run
	# Function that is run automatically
	# when the file is required.
	#=======================================

	function auto_run()
	{
		if ( ! $this->ifthd->member['acp']['manage_announce'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		if ( ! $this->ifthd->core->cache['config']['enable_news'] )
		{
			$this->ifthd->skin->error('news_disabled');
		}
		
		$this->ifthd->skin->set_section( 'Управление Новостями' );		
		$this->ifthd->skin->set_description( 'Управление текущими новостями, создание новостей и электронных писем.' );

		switch( $this->ifthd->input['code'] )
    	{
    		case 'list':
				$this->list_announcements();
    		break;
    		case 'add':
    			$this->add_announcement();
    		break;
    		case 'edit':
    			$this->edit_announcement();
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
    			$this->list_announcements();
    		break;
		}
	}

	#=======================================
	# @ List Announcements
	# Show a list of announcements.
	#=======================================

	function list_announcements($error='', $alert='')
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
		elseif ( $sort == 'id' )
		{
			$order = 'DESC';
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

		$link_id = "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=announce&amp;code=list&amp;sort=id". $order_id ."'>ID". $img_id ."</a>";
		$link_title = "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=announce&amp;code=list&amp;sort=title". $order_title ."'>Заголовок". $img_title ."</a>";

		#=============================
		# Grab Announcements
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'announcements',
							 				  	 'order'	=> array( $sort => $order ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$announce_rows = ""; // Initialize for Security
		$row_count = 0; // Initialize for Security

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			while( $a = $this->ifthd->core->db->fetch_row() )
			{
				$row_count ++;
				
				( $row_count & 1 ) ? $row_class = 'option1-med' : $row_class = 'option2-med';
				
				#=============================
				# Fix Up Information
				#=============================

				if ( $a['excerpt'] )
				{
					$a['content'] = $this->ifthd->prepare_output( $a['excerpt'] );
				}
				else
				{
					if ( $this->ifthd->core->cache['config']['enable_news_rte'] )
					{
						$a['content'] = $this->ifthd->shorten_str( $this->ifthd->remove_html( $this->ifthd->remove_dbl_spaces( $this->ifthd->convert_html( $a['content'] ) ) ), 80, 1 );
					}
					else
					{
						$a['content'] = $this->ifthd->shorten_str( $a['excerpt'], 80, 1 );
					}
				}

				$announce_rows .= "<tr>
									<td class='{$row_class}'>{$a['id']}</td>
									<td class='{$row_class}'>{$a['title']}</td>
									<td class='{$row_class}' style='font-weight: normal'>{$a['content']}</td>
									<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=announce&amp;code=edit&amp;id={$a['id']}'><img src='<! IMG_DIR !>/button_edit.gif' alt='Редактировать' /></a></td>
									<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=announce&amp;code=dodel&amp;id={$a['id']}' onclick='return sure_delete()'><img src='<! IMG_DIR !>/button_delete.gif' alt='Удалить' /></a></td>
								</tr>";
			}
		}
		else
		{
			$announce_rows .= "<tr>
								<td class='option1' colspan='5'>Нет новостей для отображения.</td>
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

		$this->output = "<script type='text/javascript'>

							function sure_delete()
							{
								if ( confirm('Вы уверены, что хотите удалить эту новость?') )
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
						<div class='groupbox'><div style='float:right'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings&amp;code=find&amp;group=news' title='Посетите страницу настроек'><img src='<! IMG_DIR !>/button_mini_settings.gif' alt='Настройки' /></a></div>Список Новостей</div>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<th width='4%' align='left'>{$link_id}</th>
							<th width='24%' align='left'>{$link_title}</th>
							<th width='61%' align='left'>Описание</th>
							<th width='5%'>Редактировать</th>
							<th width='7%'>Удалить</th>
						</tr>
						". $announce_rows ."
						</table>
						<div class='formtail'><div class='fb_pad'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=announce&amp;code=add' class='fake_button'>Добавить Новость</a></div></div>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Управление</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=announce'>Новости</a>",
						   "Список Новостей",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Управление Новостями' ) );
	}

	#=======================================
	# @ Add Announcement
	# Show add announcement form.
	#=======================================

	function add_announcement($error="")
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_announce_add'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		#=============================
		# Do Output
		#=============================

		if ( $error )
		{
			$error = "<div class='critical'>{$error}</div>";
		}

		$this->output = "<script type='text/javascript'>

							function validate_form(form)
							{
								if ( ! form.title.value )
								{
									alert('Пожалуйста введите заголовок.');
									form.title.focus();
									return false;
								}
							}

							</script>";



		if ( $this->ifthd->member['use_rte'] && $this->ifthd->core->cache['config']['enable_news_rte'] )
		{
			$this->output .= "<script language='javascript' type='text/javascript' src='<! HD_URL !>/includes/tinymce/tiny_mce.js'></script>
							<script language='javascript' type='text/javascript'>
							tinyMCE.init({
								mode : 'exact',
								theme : 'advanced',
								elements : 'contentb',
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

		$this->output .= "<form action='<! HD_URL !>/admin.php?section=manage&amp;act=announce&amp;code=doadd' method='post' onsubmit='return validate_form(this)'>
							{$error}
							<div class='groupbox'>Добавить Новость</div>
							<table width='100%' cellpadding='0' cellspacing='0'>
							<tr>
								<td class='option1' width='19%'><label for='title'>Заголовок</label></td>
								<td class='option1' width='81%'><input type='text' name='title' id='title' value='{$this->ifthd->input['title']}' size='35' /></td>
							</tr>
							<tr>
								<td class='option2' valign='top'><label for='excerpt'>Аннотация</label><br /><br /><div class='desc'>(Дополнительно)</div></td>
								<td class='option2'><textarea name='excerpt' id='excerpt' cols='50' rows='2'>{$this->ifthd->input['excerpt']}</textarea></td>
							</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info1','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Дополнительная информация</a>
										<div id='info1' style='display: none;'>
										<div>
											Это маленький анонс на портале. Отображается всегда перед самой новостью. Если не указать анонс, то отображаются первые ". $this->ifthd->core->cache['config']['news_excerpt_trim'] ." символов самой новости.
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
									". $this->ifthd->skin->checkbox( 'email_members', 'Отправить Email пользователям Members *', $this->ifthd->input['email_members'] ) ."
								</td>
							</tr>
							<tr>
								<td colspan='2' class='option2'><textarea name='contentb' id='contentb' rows='10' cols='120' style='width: 98%; height: 230px;'>{$this->ifthd->input['contentb']}</textarea></td>
							</tr>
							</table>
							<div class='formtail'><input type='submit' name='submit' id='add' value='Добавить новость' class='button' /></div>
							</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Управление</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=announce'>Новости</a>",
						   "Добавить новость",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Управление новостями' ) );
	}

	#=======================================
	# @ Edit Announcement
	# Show edit announcement form.
	#=======================================

	function edit_announcement($error="")
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_announce_edit'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'announcements',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_announcement');
		}

		$a = $this->ifthd->core->db->fetch_row();

		#=============================
		# Do Output
		#=============================

		if ( $error )
		{
			$error = "<div class='critical'>{$error}</div>";

			$title = $this->ifthd->input['title'];
			$content = $this->ifthd->input['contentb'];
			$excerpt = $this->ifthd->input['excerpt'];
			$dis_comments = $this->ifthd->input['dis_comments'];
		}
		else
		{
			$title = $a['title'];
			$content = $a['content'];
			$excerpt = $a['excerpt'];
			$dis_comments = $a['dis_comments'];
		}

		$this->output = "<script type='text/javascript'>

							function validate_form(form)
							{
								if ( ! form.title.value )
								{
									alert('Пожалуйста, введите название.');
									form.title.focus();
									return false;
								}
							}

							</script>";



		if ( $this->ifthd->member['use_rte'] && $this->ifthd->core->cache['config']['enable_news_rte'] )
		{
			$this->output .= "<script language='javascript' type='text/javascript' src='<! HD_URL !>/includes/tinymce/tiny_mce.js'></script>
							<script language='javascript' type='text/javascript'>
							tinyMCE.init({
								mode : 'exact',
								theme : 'advanced',
								elements : 'contentb',
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

		$this->output .= "<form action='<! HD_URL !>/admin.php?section=manage&amp;act=announce&amp;code=doedit&amp;id={$a['id']}' method='post' onsubmit='return validate_form(this)'>
							<div class='groupbox'>Редактирование новости: {$a['title']}</div>
							{$error}
							<table width='100%' cellpadding='0' cellspacing='0'>
							<tr>
								<td class='option1' width='19%'><label for='title'>Заголовок</label></td>
								<td class='option1' width='81%'><input type='text' name='title' id='title' value='{$title}' size='35' /></td>
							</tr>
							<tr>
								<td class='option2' valign='top'><label for='excerpt'>Аннотация</label><br /><br /><div class='desc'>(Дополнительно)</div></td>
								<td class='option2'><textarea name='excerpt' id='excerpt' cols='50' rows='2'>{$excerpt}</textarea></td>
							</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info1','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Дополнительная информация</a>
										<div id='info1' style='display: none;'>
										<div>
											Это маленький анонс на портале. Отображается всегда перед самой новостью. Если не указать анонс, то отображаются первые ". $this->ifthd->core->cache['config']['news_excerpt_trim'] ." символов самой новости.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
								<td class='option1'>Опции</td>
								<td class='option1' style='font-weight: normal'>
									". $this->ifthd->skin->checkbox( 'dis_comments', 'Отключить комментарии', $dis_comments ) ."
								</td>
							</tr>
							<tr>
								<td colspan='2' class='option2'><textarea name='contentb' id='contentb' rows='10' cols='120' style='width: 98%; height: 230px;'>{$content}</textarea></td>
							</tr>
							</table>
							<div class='formtail'><input type='submit' name='submit' id='edit' value='Сохранить изменения' class='button' /></div>
							</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Управление</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=announce'>Новости</a>",
						   "Редактировать новость",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Управление новостями' ) );
	}

	#=======================================
	# @ Do Add
	# Create a new announcement.
	#=======================================

	function do_add()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['acp']['manage_announce_add'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		if ( ! $this->ifthd->input['title'] )
		{
			$this->add_announcement('Пожалуйста, введите заголовок.');
		}

		if ( ! $this->ifthd->input['contentb'] )
		{
			$this->add_announcement('Пожалуйста, введите новость');
		}
		
		$this->ifthd->input['contentb'] = $this->ifthd->remove_extra_lbs( $this->ifthd->input['contentb'] );

		#=============================
		# Add Announcement
		#=============================

		$db_array = array(
						  'mid'			=> $this->ifthd->member['id'],
						  'mname'		=> $this->ifthd->member['name'],
						  'title'		=> $this->ifthd->input['title'],
						  'excerpt'		=> $this->ifthd->input['excerpt'],
						  'content'		=> $this->ifthd->input['contentb'],
						  'email'		=> $this->ifthd->input['email_members'],
						  'dis_comments'=> $this->ifthd->input['dis_comments'],
						  'date'		=> time(),
						  'ipadd'		=> $this->ifthd->input['ip_address'],
						 );

		$this->ifthd->core->db->construct( array(
											  	 'insert'	=> 'announcements',
											  	 'set'		=> $db_array,
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$announce_id = $this->ifthd->core->db->get_insert_id();

		$this->ifthd->log( 'admin', "Новость добавлена &#039;". $this->ifthd->input['title'] ."&#039;", 1, $announce_id );

		#=============================
		# Send Email
		#=============================

		if ( $this->ifthd->input['email_members'] && $start_date < time() )
		{
			$to_mail = array(); // Initialize for Security

			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'id' ),
												  	 'from'		=> 'members',
												  	 'where'	=> array( array( 'email_notify', '=', 1 ), array( 'email_announce', '=', 1, 'and' ) ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$replace = ""; // Initialize for Security

			$replace['TITLE'] = $this->ifthd->input['title'];
			$replace['CONTENT'] = $this->ifthd->input['contentb'];

			while( $mem = $this->ifthd->core->db->fetch_row() )
			{
				$to_mail[] = $mem['id'];
			}

			while( list( , $mid ) = each( $to_mail ) )
			{
				$this->ifthd->send_email( $mid, 'announcement', $replace );
			}
		}

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_announce_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=announce&code=list', 'add_announce_success' );
		$this->list_announcements( '', 'Новость успешно добавлена.' );
	}

	#=======================================
	# @ Do Edit
	# Edit an announcement.
	#=======================================

	function do_edit()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_announce_edit'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id' ),
											  	 'from'		=> 'announcements',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_announcement');
		}

		if ( ! $this->ifthd->input['title'] )
		{
			$this->edit_announcement('Пожалуйста, введите заголовок.');
		}

		if ( ! $this->ifthd->input['contentb'] )
		{
			$this->edit_announcement('Пожалуйста, введите новость.');
		}
		
		$this->ifthd->input['contentb'] = $this->ifthd->remove_extra_lbs( $this->ifthd->input['contentb'] );

		#=============================
		# Edit Announcement
		#=============================

		$db_array = array(
						  'excerpt'		=> $this->ifthd->input['excerpt'],
						  'title'		=> $this->ifthd->input['title'],
						  'content'		=> $this->ifthd->input['contentb'],
						  'dis_comments'=> $this->ifthd->input['dis_comments'],
						 );

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'announcements',
											  	 'set'		=> $db_array,
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'admin', "Новость отредактирована &#039;". $this->ifthd->input['title'] ."&#039;", 1, $this->ifthd->input['id'] );

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_announce_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=announce&code=list', 'edit_announce_success' );
		$this->list_announcements( '', 'Новость успешно обновлена.' );
	}

	#=======================================
	# @ Do Delete
	# Delete an announcement.
	#=======================================

	function do_delete()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['acp']['manage_announce_delete'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'announcements',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_announcement');
		}

		$a = $this->ifthd->core->db->fetch_row();

		#=============================
		# Delete Announcement
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'delete'	=> 'announcements',
							 				  	 'where'	=> array( 'id', '=', $a['id'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'admin', "Новость удалена &#039;". $a['title'] ."&#039;", 2, $a['id'] );

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_announce_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=announce&code=list', 'delete_announce_success' );
		$this->list_announcements( 'Новость успешно удалена.' );
	}

}

?>