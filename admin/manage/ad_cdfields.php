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
|    | Admin Custom Department Fields
#======================================================
*/

class ad_cdfields {

	#=======================================
	# @ Auto Run
	# Function that is run automatically
	# when the file is required.
	#=======================================

	function auto_run()
	{
		if ( ! $this->ifthd->member['acp']['manage_depart_cfields'] )
		{
			$this->ifthd->skin->error('no_perm');
		}
		
		$this->ifthd->skin->set_section( 'Управление тикетами' );		
		$this->ifthd->skin->set_description( 'Управление тикетами, отделами, настраиваемые поля отдела и шаблоны ответов.' );

		switch( $this->ifthd->input['code'] )
	    {
	    	case 'list':
				$this->list_fields();
	    	break;
	    	case 'add':
	    		$this->add_field();
	    	break;
	    	case 'edit':
	    		$this->edit_field();
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
    			$this->list_fields();
    		break;
		}
	}

	#=======================================
	# @ List Fields
	# Show a list of custom department fields.
	#=======================================

	function list_fields($error='', $alert='')
	{
		#=============================
		# Grab Fields
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'depart_fields',
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$field_rows = ""; // Initialize for Security
		$row_count = 0; // Initialize for Security

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			while( $f = $this->ifthd->core->db->fetch_row() )
			{
				$row_count ++;
				
				( $row_count & 1 ) ? $row_class = 'option1-med' : $row_class = 'option2-med';
				
				#=============================
				# Fix Up Information
				#=============================

				if ( $f['required'] )
				{
					$f['required'] = 'Да';
				}
				else
				{
					$f['required'] = 'Нет';
				}

				if ( $f['type'] == 'textfield' )
				{
					$f['type'] = 'Текстовое поле';
				}
				elseif ( $f['type'] == 'textarea' )
				{
					$f['type'] = 'Текстовая область';
				}
				elseif ( $f['type'] == 'dropdown' )
				{
					$f['type'] = 'Выпадающий список';
				}
				elseif ( $f['type'] == 'checkbox' )
				{
					$f['type'] = 'Флажок';
				}
				elseif ( $f['type'] == 'radio' )
				{
					$f['type'] = 'Радио кнопка';
				}

				$field_rows .= "<tr>
									<td class='{$row_class}'>{$f['id']}</td>
									<td class='{$row_class}'>{$f['name']}</td>
									<td class='{$row_class}' style='font-weight: normal'>{$f['fkey']}</td>
									<td class='{$row_class}' style='font-weight: normal'>{$f['type']}</td>
									<td class='{$row_class}' align='center'>{$f['required']}</td>
									<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=cdfields&amp;code=edit&amp;id={$f['id']}'><img src='<! IMG_DIR !>/button_edit.gif' alt='Редактировать' /></a></td>
									<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=cdfields&amp;code=dodel&amp;id={$f['id']}' onclick='return sure_delete()'><img src='<! IMG_DIR !>/button_delete.gif' alt='Удалить' /></a></td>
								</tr>";
			}
		}
		else
		{
			$field_rows .= "<tr>
								<td class='option1' colspan='7'>Нет полей для отображения.</td>
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
								if ( confirm(\"Вы уверены, что хотите удалить это поле?.\") )
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
						<div class='groupbox'>Список настраиваемых полей отдела</div>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<th width='5%' align='left'>ID</th>
							<th width='28%' align='left'>Название</th>
							<th width='23%' align='left'>Ключ</th>
							<th width='19%' align='left'>Тип поля</th>
							<th width='11%'>Обязательное поле</th>
							<th width='6%'>Редактировать</th>
							<th width='8%'>Удалить</th>
						</tr>
						". $field_rows ."
						</table>
						<div class='formtail'><div class='fb_pad'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=cdfields&amp;code=add' class='fake_button'>Добавить новое поле</a></div></div>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Управление</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=cdfields'>Настраиваемые поля отдела</a>",
						   "Список полей",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Управление настраиваемыми полями отдела' ) );
	}

	#=======================================
	# @ Add Field
	# Show add custom field form.
	#=======================================

	function add_field($error="")
	{
		#=============================
		# Generate Permissions
		#=============================

		if ( is_array( $this->ifthd->input['departs'] ) )
		{
			while ( list( , $depart ) = each( $this->ifthd->input['departs'] ) )
			{
				$departs[ $depart ] = 1;
			}
		}

		#=============================
		# Do Output
		#=============================

		if ( $this->ifthd->input['type'] == 'textfield' ) $sel_textfield = " selected='selected'";
		if ( $this->ifthd->input['type'] == 'textarea' ) $sel_textarea = " selected='selected'";
		if ( $this->ifthd->input['type'] == 'dropdown' ) $sel_dropdown = " selected='selected'";
		if ( $this->ifthd->input['type'] == 'checkbox' ) $sel_checkbox = " selected='selected'";
		if ( $this->ifthd->input['type'] == 'radio' ) $sel_radio = " selected='selected'";

		if ( $error )
		{
			$error = "<div class='critical'>{$error}</div>";
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

							if ( form.fkey.value.length < 3 )
							{
								alert('Пожалуйста, введите ключ не менее 3 символов.');
								form.fkey.focus();
								return false;
							}
						}

						</script>
						{$error}
						<form action='<! HD_URL !>/admin.php?section=manage&amp;act=cdfields&amp;code=doadd' method='post' onsubmit='return validate_form(this)'>
						<div class='groupbox'>Добавление настраиваемых полей отдела</div>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<td class='option1' width='28%'><label for='name'>Название</label></td>
							<td class='option1' width='72%'><input type='text' name='name' id='name' value='{$this->ifthd->input['name']}' size='35' /></td>
						</tr>
						<tr>
							<td class='option2'><label for='fkey'>Ключ</label></td>
							<td class='option2' style='font-weight: normal'><input type='text' name='fkey' id='fkey' value='{$this->ifthd->input['fkey']}' size='20' /> <span class='addesc'>(Без пробелов. Может содержать только цифры и буквы латинского алфавита в нижнем регистре.)</span></td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info1','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Дополнительная информация</a>
										<div id='info1' style='display: none;'>
										<div>
											Этот ключ, уникальный идентификатор для этого дополнительного поля. Он не должен содержать пробелов, но может содержать только цифры и буквы латинского алфавита в нижнем регистре.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
							<td class='option1'>Тип поля</td>
							<td class='option1'>
								<select name='type' id='type'><option value='textfield'{$sel_textfield}>Текстовое поле</option><option value='textarea'{$sel_textarea}>Текстовая область</option><option value='dropdown'{$sel_dropdown}>Выпадающий список</option><option value='checkbox'{$sel_checkbox}>Флажок</option><option value='radio'{$sel_radio}>Радио кнопка</option></select>
							</td>
						</tr>
						<tr>
							<td class='option2' valign='top'><label for='fextra'>Дополнительно</label><div class='addesc' style='font-weight: normal; font-size: 12px'>Это окно используется при выборе &#8220;Выпадающего списока&#8221; или &#8220;Радио кнопки&#8221;. Указывайте каждый вариант с новой строки.<br /><br />Format: key=Value</div></td>
							<td class='option2'><textarea name='fextra' id='fextra' cols='32' rows='3'>{$this->ifthd->input['extra']}</textarea></td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info2','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Дополнительная информация</a>
										<div id='info2' style='display: none;'>
										<div>
											Пример выпадающего списка: <select name='example'><option value='yes'>Да</option><option value='no'>Нет</option><option value='maybe'>Возможно</option></select><br /><br />
											yes=Да<br />
											no=Нет<br />
											maybe=Возможно
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
							<td class='option1'>Обязательное поле</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'required', $this->ifthd->input['required'] ) ."
							</td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info3','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Дополнительная информация</a>
										<div id='info3' style='display: none;'>
										<div>
											Если необходимо чтоб это поле было перед принятием тикета.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
							<td class='option2' valign='top'>Отделы</td>
							<td class='option2'>
								<select name='departs[]' id='departs' size='5' multiple='multiple'>
								". $this->ifthd->build_dprt_drop( $departs, 0, 1 ) ."
								</select>
							</td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info4','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Дополнительная информация</a>
										<div id='info4' style='display: none;'>
										<div>
											Выберите отделы, в которых это поле будет появляться при отправке тикета. Вы можете выбрать более одного отдела.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "</table>
						<div class='formtail'><input type='submit' name='submit' id='add' value='Добавить поле' class='button' /></div>
						</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Управление</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=cdfields'>Настраиваемые поля отдела</a>",
						   "Добавить отдел",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Управление настраиваемыми полями отдела' ) );
	}

	#=======================================
	# @ Edit Field
	# Show edit custom field form.
	#=======================================

	function edit_field($error="")
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'depart_fields',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_dfield');
		}

		$f = $this->ifthd->core->db->fetch_row();

		#=============================
		# Do Output
		#=============================

		if ( $error )
		{
			$error = "<div class='critical'>{$error}</div>";

			$name = $this->ifthd->input['name'];
			$fkey = $this->ifthd->input['fkey'];
			$extra = $this->ifthd->input['fextra'];
			$required = $this->ifthd->input['required'];

			if ( $this->ifthd->input['type'] == 'textfield' ) $sel_textfield = " selected='selected'";
			if ( $this->ifthd->input['type'] == 'textarea' ) $sel_textarea = " selected='selected'";
			if ( $this->ifthd->input['type'] == 'dropdown' ) $sel_dropdown = " selected='selected'";
			if ( $this->ifthd->input['type'] == 'checkbox' ) $sel_checkbox = " selected='selected'";
			if ( $this->ifthd->input['type'] == 'radio' ) $sel_radio = " selected='selected'";

			if ( is_array( $this->ifthd->input['departs'] ) )
			{
				while ( list( , $depart ) = each( $this->ifthd->input['departs'] ) )
				{
					$departs[ $depart ] = 1;
				}
			}
		}
		else
		{
			$name = $f['name'];
			$fkey = $f['fkey'];
			$extra = $f['extra'];
			$required = $f['required'];

			if ( $f['type'] == 'textfield' ) $sel_textfield = " selected='selected'";
			if ( $f['type'] == 'textarea' ) $sel_textarea = " selected='selected'";
			if ( $f['type'] == 'dropdown' ) $sel_dropdown = " selected='selected'";
			if ( $f['type'] == 'checkbox' ) $sel_checkbox = " selected='selected'";
			if ( $f['type'] == 'radio' ) $sel_radio = " selected='selected'";

			$departs = unserialize( $f['departs'] );
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

							if ( form.fkey.value.length < 3 )
							{
								alert('Пожалуйста, введите ключ не менее 3 символов.');
								form.fkey.focus();
								return false;
							}
						}

						</script>
						{$error}
						<form action='<! HD_URL !>/admin.php?section=manage&amp;act=cdfields&amp;code=doedit&amp;id={$f['id']}' method='post' onsubmit='return validate_form(this)'>
						<div class='groupbox'>Редактирование настраиваемых полей отдела: {$f['name']}</div>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<td class='option1' width='28%'><label for='name'>Название</label></td>
							<td class='option1' width='72%'><input type='text' name='name' id='name' value='{$name}' size='35' /></td>
						</tr>
						<tr>
							<td class='option2'><label for='fkey'>Ключ</label></td>
							<td class='option2' style='font-weight: normal'><input type='text' name='fkey' id='fkey' value='{$fkey}' size='20' /> <span class='addesc'>(Без пробелов. Может содержать только цифры и буквы латинского алфавита в нижнем регистре.)</span></td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info1','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Дополнительная информация</a>
										<div id='info1' style='display: none;'>
										<div>
											Этот ключ, уникальный идентификатор для этого дополнительного поля. Он не должен содержать пробелов, но может содержать только цифры и буквы латинского алфавита в нижнем регистре.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
							<td class='option1'>Тип поля</td>
							<td class='option1'>
								<select name='type' id='type'><option value='textfield'{$sel_textfield}>Текстовое поле</option><option value='textarea'{$sel_textarea}>Текстовая область</option><option value='dropdown'{$sel_dropdown}>Выпадающий список</option><option value='checkbox'{$sel_checkbox}>Флажок</option><option value='radio'{$sel_radio}>Радио кнопка</option></select>
							</td>
						</tr>
						<tr>
							<td class='option2' valign='top'><label for='fextra'>Дополнительно</label><div class='addesc' style='font-weight: normal; font-size: 12px'>Это окно используется при выборе &#8220;Выпадающего списока&#8221; или &#8220;Радио кнопки&#8221;. Указывайте каждый вариант с новой строки.<br /><br />Format: key=Value</div></td>
							<td class='option2'><textarea name='fextra' id='fextra' cols='32' rows='3'>{$extra}</textarea></td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info2','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Дополнительная информация</a>
										<div id='info2' style='display: none;'>
										<div>
											Пример выпадающего списка: <select name='example'><option value='yes'>Да</option><option value='no'>Нет</option><option value='maybe'>Возможно</option></select><br /><br />
											yes=Да<br />
											no=Нет<br />
											maybe=Возможно
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
							<td class='option1'>Обязательное поле</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'required', $required ) ."
							</td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info3','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Дополнительная информация</a>
										<div id='info3' style='display: none;'>
										<div>
											Если необходимо чтоб это поле было перед принятием тикета.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
							<td class='option2' valign='top'>Отделы</td>
							<td class='option2'>
								<select name='departs[]' id='departs' size='5' multiple='multiple'>
								". $this->ifthd->build_dprt_drop( $departs, 0, 1 ) ."
								</select>
							</td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info4','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Дополнительная информация</a>
										<div id='info4' style='display: none;'>
										<div>
											Выберите отделы, в которых это поле будет появляться при отправке тикета. Вы можете выбрать более одного отдела.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "</table>
						<div class='formtail'><input type='submit' name='submit' id='edit' value='Редактировать поле' class='button' /></div>
						</form>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Управление</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=cdfields'>Настраиваемые поля отдела</a>",
						   "Edit Department",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Управление настраиваемыми полями отдела' ) );
	}

	#=======================================
	# @ Do Add
	# Add a new custom field.
	#=======================================

	function do_add()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->input['name'] )
		{
			$this->add_field('Пожалуйста, введите название.');
		}

		if ( strlen( $this->ifthd->input['fkey'] ) < 3 )
		{
			$this->add_field('Пожалуйста, введите ключ.');
		}

		if ( ! $this->key_check( $this->ifthd->input['fkey'] ) )
		{
			$this->add_field('Ваш ключ может содержать только цифры и буквы латинского алфавита в нижнем регистре и без пробелов.');
		}

		if ( $this->ifthd->input['type'] == 'dropdown' )
		{
			if ( strlen( $this->ifthd->input['fextra'] ) < 3 )
			{
				$this->add_field('Пожалуйста, введите значения для выпадающего списка в дополнительном текстовом поле.');
			}
		}

		if ( $this->ifthd->input['type'] == 'radio' )
		{
			if ( strlen( $this->ifthd->input['fextra'] ) < 3 )
			{
				$this->add_field('Пожалуйста, введите значения для радио кнопки в дополнительное текстовое поле.');
			}
		}

		#=============================
		# Generate Permissions
		#=============================

		if ( is_array( $this->ifthd->input['departs'] ) )
		{
			while ( list( , $depart ) = each( $this->ifthd->input['departs'] ) )
			{
				$departs[ $depart ] = 1;
			}
		}

		#=============================
		# Add Field
		#=============================

		$db_array = array(
						  'fkey'			=> $this->ifthd->input['fkey'],
						  'name'			=> $this->ifthd->input['name'],
						  'type'			=> $this->ifthd->input['type'],
						  'extra'			=> $this->ifthd->input['fextra'],
						  'required'		=> $this->ifthd->input['required'],
						  'departs'			=> serialize($departs),
						 );

		$this->ifthd->core->db->construct( array(
											  	 'insert'	=> 'depart_fields',
											  	 'set'		=> $db_array,
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$field_id = $this->ifthd->core->db->get_insert_id();

		$this->ifthd->log( 'admin', "Поле для Отдела добавлено &#039;". $this->ifthd->input['name'] ."&#039;", 1, $field_id );

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_dfields_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=cdfields&code=list', 'add_dfield_success' );
		$this->list_fields( '', 'Настраиваемое поле отдела успешно добавлено.' );
	}

	#=======================================
	# @ Do Edit
	# Edit a custom field.
	#=======================================

	function do_edit()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'depart_fields',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_dfield');
		}

		if ( ! $this->ifthd->input['name'] )
		{
			$this->edit_field('Пожалуйста, введите название.');
		}

		if ( strlen( $this->ifthd->input['fkey'] ) < 3 )
		{
			$this->edit_field('Пожалуйста, введите ключ.');
		}

		if ( ! $this->key_check( $this->ifthd->input['fkey'] ) )
		{
			$this->edit_field('Ваш ключ может содержать только цифры и буквы латинского алфавита в нижнем регистре и без пробелов.');
		}

		if ( $this->ifthd->input['type'] == 'dropdown' )
		{
			if ( strlen( $this->ifthd->input['fextra'] ) < 3 )
			{
				$this->edit_field('Пожалуйста, введите значения для выпадающего списка в дополнительном текстовом поле.');
			}
		}

		if ( $this->ifthd->input['type'] == 'radio' )
		{
			if ( strlen( $this->ifthd->input['fextra'] ) < 3 )
			{
				$this->edit_field('Пожалуйста, введите значения для радио кнопки в дополнительное текстовое поле.');
			}
		}

		#=============================
		# Generate Permissions
		#=============================

		if ( is_array( $this->ifthd->input['departs'] ) )
		{
			while ( list( , $depart ) = each( $this->ifthd->input['departs'] ) )
			{
				$departs[ $depart ] = 1;
			}
		}

		#=============================
		# Edit Field
		#=============================

		$db_array = array(
						  'fkey'			=> $this->ifthd->input['fkey'],
						  'name'			=> $this->ifthd->input['name'],
						  'type'			=> $this->ifthd->input['type'],
						  'extra'			=> $this->ifthd->input['fextra'],
						  'required'		=> $this->ifthd->input['required'],
						  'departs'			=> serialize($departs),
						 );

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'depart_fields',
											  	 'set'		=> $db_array,
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'admin', "Поле для Отдела отредактировано &#039;". $this->ifthd->input['name'] ."&#039;", 1, $this->ifthd->input['id'] );

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_dfields_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=cdfields&code=list', 'edit_dfield_success' );
		$this->list_fields( '', 'Настраиваемое поле отдела успешно обновлено.' );
	}

	#=======================================
	# @ Do Delete
	# Delete a custom field.
	#=======================================

	function do_delete()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'depart_fields',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_dfield');
		}

		$f = $this->ifthd->core->db->fetch_row();

		#=============================
		# Delete Field
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'delete'	=> 'depart_fields',
							 				  	 'where'	=> array( 'id', '=', $f['id'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'admin', "Поле для Отдела удалено &#039;". $f['name'] ."&#039;", 2, $f['id'] );

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_dfields_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=cdfields&code=list', 'delete_dfield_success' );
		$this->list_fields( 'Настраиваемое поле Отдела успешно удалено.' );
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

}
?>