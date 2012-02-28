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
|    | Admin Custom Profile Fields
#======================================================
*/

class ad_cpfields {

	#=======================================
	# @ Auto Run
	# Function that is run automatically
	# when the file is required.
	#=======================================

	function auto_run()
	{
		if ( ! $this->ifthd->member['acp']['manage_member_cfields'] )
		{
			$this->ifthd->skin->error('no_perm');
		}
		
		$this->ifthd->skin->set_section( 'Управление пользователями' );		
		$this->ifthd->skin->set_description( 'Управление вашими пользователями, группами, настраиваемые поля профиля и пользователи ожидающие проверки.' );

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
	# Show a list of custom profile fields.
	#=======================================

	function list_fields($error='', $alert='')
	{
		#=============================
		# Grab Fields
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'profile_fields',
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
									<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=cpfields&amp;code=edit&amp;id={$f['id']}'><img src='<! IMG_DIR !>/button_edit.gif' alt='Редактировать' /></a></td>
									<td class='{$row_class}' align='center'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=cpfields&amp;code=dodel&amp;id={$f['id']}' onclick='return sure_delete()'><img src='<! IMG_DIR !>/button_delete.gif' alt='Удалить' /></a></td>
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
						<div class='groupbox'>Список настраиваемых полей профиля</div>
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
						<div class='formtail'><div class='fb_pad'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=cpfields&amp;code=add' class='fake_button'>Добавить новое поле</a></div></div>";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Управление</a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=cpfields'>Настраиваемые поля профиля</a>",
						   "Список полей",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Управление настраиваемыми полями профиля' ) );
	}

	#=======================================
	# @ Add Field
	# Show add custom field form.
	#=======================================

	function add_field($error="")
	{
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
						<form action='<! HD_URL !>/admin.php?section=manage&amp;act=cpfields&amp;code=doadd' method='post' onsubmit='return validate_form(this)'>
						<div class='groupbox'>Добавление настраиваемых полей профиля</div>
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
											Это поле должно быть заполненым при обновлении информации о профиле. Однако, это поле будет отображаться только на странице регистрации, если выбрать в опциях &#8220;Показать при регистрации&#8221; значение &#8220;Да&#8221; ниже.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
							<td class='option2'>Показать при регистрации</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'reg', $this->ifthd->input['reg'] ) ." <span class='addesc'>(Пропускает разрешения)</span>
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
											Если установлено значение Да, то это поле будет показано на странице регистрации, независимо от группы разрешений ниже.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
							<td class='option1'>Показывать при просмотре тикета</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'ticket', $this->ifthd->input['ticket'] ) ."
							</td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info5','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Дополнительная информация</a>
										<div id='info5' style='display: none;'>
										<div>
											Если установлено значение Да, то это поле и его значение будет отображаться на странице просмотра тикета.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
							<td class='option2'>Только для персонала</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'staff', $this->ifthd->input['staff'] ) ."
							</td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info6','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Дополнительная информация</a>
										<div id='info6' style='display: none;'>
										<div>
											Если установлено значение Да, то это поле будет только видимым и редактируемым сотрудникам, которые имеют доступ к Административной Контрольной Панели. Это полезно для хранения информации о клиентах, только сотрудники должны их видеть.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
							<td class='option1' valign='top'>Разрешения для Групп</td>
							<td class='option1'>
								<select name='perms[]' id='perms' size='5' multiple='multiple'>
								". $this->ifthd->build_group_drop( $this->ifthd->input['perms'] ) ."
								</select>
							</td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info7','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Дополнительная информация</a>
										<div id='info7' style='display: none;'>
										<div>
											Выберите группы, в которых это поле будет доступным. Вы можете выбрать более одной группы.
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
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=cpfields'>Настраиваемые поля профиля</a>",
						   "Add Department",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Управление настраиваемыми полями профиля' ) );
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
											  	 'from'		=> 'profile_fields',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_pfield');
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
			$reg = $this->ifthd->input['reg'];
			$ticket = $this->ifthd->input['ticket'];
			$staff = $this->ifthd->input['staff'];
			$perms = $this->ifthd->input['perms'];

			if ( $this->ifthd->input['type'] == 'textfield' ) $sel_textfield = " selected='selected'";
			if ( $this->ifthd->input['type'] == 'textarea' ) $sel_textarea = " selected='selected'";
			if ( $this->ifthd->input['type'] == 'dropdown' ) $sel_dropdown = " selected='selected'";
			if ( $this->ifthd->input['type'] == 'checkbox' ) $sel_checkbox = " selected='selected'";
			if ( $this->ifthd->input['type'] == 'radio' ) $sel_radio = " selected='selected'";
		}
		else
		{
			$name = $f['name'];
			$fkey = $f['fkey'];
			$extra = $f['extra'];
			$required = $f['required'];
			$ticket = $f['ticket'];
			$staff = $f['staff'];
			$reg = $f['reg'];

			if ( $f['type'] == 'textfield' ) $sel_textfield = " selected='selected'";
			if ( $f['type'] == 'textarea' ) $sel_textarea = " selected='selected'";
			if ( $f['type'] == 'dropdown' ) $sel_dropdown = " selected='selected'";
			if ( $f['type'] == 'checkbox' ) $sel_checkbox = " selected='selected'";
			if ( $f['type'] == 'radio' ) $sel_radio = " selected='selected'";

			$f['perms'] = unserialize( $f['perms'] );

			if ( is_array( $f['perms'] ) )
			{
				while( list( $gid, ) = each( $f['perms'] ) )
				{
					$perms[] = $gid;
				}
			}
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
						<form action='<! HD_URL !>/admin.php?section=manage&amp;act=cpfields&amp;code=doedit&amp;id={$f['id']}' method='post' onsubmit='return validate_form(this)'>
						<div class='groupbox'>Редактирование настраиваемых полей профиля: {$f['name']}</div>
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
											Это поле должно быть заполненым при обновлении информации о профиле. Однако, это поле будет отображаться только на странице регистрации, если выбрать в опциях &#8220;Показать при регистрации&#8221; значение &#8220;Да&#8221; ниже.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
							<td class='option2'>Показать при регистрации</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'reg', $reg ) ." <span class='addesc'>(Пропускает разрешения)</span>
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
											Если установлено значение Да, то это поле будет показано на странице регистрации, независимо от группы разрешений ниже.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
							<td class='option1'>Показывать при просмотре тикета</td>
							<td class='option1' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'ticket', $ticket ) ."
							</td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info5','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Дополнительная информация</a>
										<div id='info5' style='display: none;'>
										<div>
											Если установлено значение Да, то это поле и его значение будет отображаться на странице просмотра тикета.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
							<td class='option2'>Только для персонала</td>
							<td class='option2' style='font-weight: normal'>
								". $this->ifthd->skin->yes_no_radio( 'staff', $staff ) ."
							</td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info6','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Дополнительная информация</a>
										<div id='info6' style='display: none;'>
										<div>
											Если установлено значение Да, то это поле будет только видимым и редактируемым сотрудникам, которые имеют доступ к Административной Контрольной Панели. Это полезно для хранения информации о клиентах, только сотрудники должны их видеть.
										</div>
										</div>
									</div>
								</td>
							</tr>";
		}
		
		$this->output .= "<tr>
							<td class='option1' valign='top'>Разрешения для Групп</td>
							<td class='option1'>
								<select name='perms[]' id='perms' size='5' multiple='multiple'>
								". $this->ifthd->build_group_drop( $perms ) ."
								</select>
							</td>
						</tr>";
		
		if ( ACP_HELP )
		{
			$this->output .= "<tr>
								<td colspan='2'>									
									<div class='infopop'>
										<a onclick=\"javascript:Effect.toggle('info7','blind',{duration: 0.5});\" class='fake_link'><img src='<! IMG_DIR !>/toggle.gif' alt='+' /> Дополнительная информация</a>
										<div id='info7' style='display: none;'>
										<div>
											Выберите группы, в которых это поле будет доступным. Вы можете выбрать более одной группы.
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
						   "<a href='<! HD_URL !>/admin.php?section=manage'>Управление/a>",
						   "<a href='<! HD_URL !>/admin.php?section=manage&amp;act=cpfields'>Настраиваемые поля профиля</a>",
						   "Edit Department",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Управление настраиваемыми полями профиля' ) );
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

		if ( is_array( $this->ifthd->input['perms'] ) )
		{
			while ( list( , $perm ) = each( $this->ifthd->input['perms'] ) )
			{
				$perms[ $perm ] = 1;
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
						  'perms'			=> serialize( $perms ),
						  'required'		=> $this->ifthd->input['required'],
						  'ticket'			=> $this->ifthd->input['ticket'],
						  'staff'			=> $this->ifthd->input['staff'],
						  'reg'				=> $this->ifthd->input['reg'],
						 );

		$this->ifthd->core->db->construct( array(
											  	 'insert'	=> 'profile_fields',
											  	 'set'		=> $db_array,
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$field_id = $this->ifthd->core->db->get_insert_id();

		$this->ifthd->log( 'admin', "Поле для Профиля добавлено &#039;". $this->ifthd->input['name'] ."&#039;", 1, $field_id );

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_pfields_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=cpfields&code=list', 'add_pfield_success' );
		$this->list_fields( '', 'Настраиваемое поле профиля успешно добавлено.' );
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
											  	 'from'		=> 'profile_fields',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_pfield');
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

		if ( is_array( $this->ifthd->input['perms'] ) )
		{
			while ( list( , $perm ) = each( $this->ifthd->input['perms'] ) )
			{
				$perms[ $perm ] = 1;
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
						  'perms'			=> serialize( $perms ),
						  'required'		=> $this->ifthd->input['required'],
						  'ticket'			=> $this->ifthd->input['ticket'],
						  'staff'			=> $this->ifthd->input['staff'],
						  'reg'				=> $this->ifthd->input['reg'],
						 );

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'profile_fields',
											  	 'set'		=> $db_array,
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'admin', "Поле для Профиля отредактировано &#039;". $this->ifthd->input['name'] ."&#039;", 1, $this->ifthd->input['id'] );

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_pfields_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=cpfields&code=list', 'edit_pfield_success' );
		$this->list_fields( '', 'Настраиваемое поле профиля успешно обновлено.' );
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
											  	 'from'		=> 'profile_fields',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0,1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->skin->error('no_pfield');
		}

		$f = $this->ifthd->core->db->fetch_row();

		#=============================
		# Delete Field
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'delete'	=> 'profile_fields',
							 				  	 'where'	=> array( 'id', '=', $f['id'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'admin', "Поле для Профиля удалено &#039;". $f['name'] ."&#039;", 2, $f['id'] );

		#=============================
		# Rebuild Cache
		#=============================

		$this->ifthd->rebuild_pfields_cache();

		#=============================
		# Redirect
		#=============================

		#$this->ifthd->skin->redirect( '?section=manage&act=cpfields&code=list', 'delete_pfield_success' );
		$this->list_fields( 'Настраиваемое поле профиля успешно удалено.' );
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