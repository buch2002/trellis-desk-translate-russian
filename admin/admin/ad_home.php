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
|    | Admin Home
#======================================================
*/

class ad_home {

	#=======================================
	# @ Auto Run
	# Function that is run automatically
	# when the file is required.
	#=======================================

	function auto_run()
	{
		$this->ifthd->skin->set_section( 'Контрольная Панель Администрирования' );		
		$this->ifthd->skin->set_description( 'Добро пожаловать в Trellis Desk. Управляйте вашими настройками, тикетами, содержанием и внешним видом вашей Панели Администрирования.' );
		
		switch( $this->ifthd->input['code'] )
    	{
    		case 'notes':
				$this->notes_action();
    		break;

    		default:
    			$this->show_home();
    		break;
		}
	}

	#=======================================
	# @ Show Home
	# Show the home ACP page.
	#=======================================

	function show_home($notes="")
	{
		#=============================
		# Security Check
		#=============================
		
		if ( file_exists( HD_PATH .'install/install.lock' ) )
		{
			$alert = 'Директория <i>install</i> всё ещё существует. В то время как инсталятор заблокирован, мы рекомендуем удалить или переименовать директорию <i>install</i> для дополнительной безопасности.';
		}
		elseif ( is_dir( HD_PATH .'install' ) )
		{
			$error = 'Директория <i>install</i> всё ещё существует. Мы настоятельно рекомендуем удалить или переименовать директорию <i>install</i> для дополнительной безопасности.';
		}
		
		#=============================
		# Grab Content
		#=============================

		if ( ! $notes )
		{
			$notes = $this->ifthd->core->cache['misc']['notes'];
		}
		else
		{
			$notes_msg = "<p class='bldesc'>Примечания сохранены.</p>";
		}

		#=============================
		# Grab Tickets
		#=============================

		if ( is_array( unserialize( $this->ifthd->member['g_depart_perm'] ) ) )
		{
			$rev_perms = array(); // Initialize for Security

			foreach( unserialize( $this->ifthd->member['g_depart_perm'] ) as $did => $access )
			{
				if ( $access == 1 ) $rev_perms[] = $did;
			}

			$sql_where = array( array( 'status', '!=', 4 ), array( 'status', '!=', 6, 'and' ), array( 'did', 'in', $rev_perms, 'and' ) );
		}
		else
		{
			$sql_where = array( array( 'status', '!=', 4 ), array( 'status', '!=', 6, 'and' ) );
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'tickets',
											  	 'where'	=> $sql_where,
											  	 'order'	=> array( 'date' => 'desc' ),
											  	 'limit'	=> array( 0,5 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$ticket_rows = ""; // Initialize for Security
		$row_count = 0; // Initialize for Security

		if ( $this->ifthd->core->db->get_num_rows() )
		{
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

				$t['priority'] = $this->ifthd->get_priority( $t['priority'] );

				$t['date'] = $this->ifthd->ift_date( $t['date'], "n/j/y g:i A" );

				$t['status'] = $this->ifthd->get_status( $t['status'], 1 );
				
				$ticket_rows .= "<tr>
									<td width='3%' class='{$row_class}'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=view&amp;id={$t['id']}'>{$t['id']}</a></td>
									<td width='31%' class='{$row_class}'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=view&amp;id={$t['id']}'>{$t['subject']}</a></td>
									<td width='13%' class='{$row_class}'>{$t['p_img']}{$t['priority']}</td>
									<td width='20%' class='{$row_class}' style='font-weight: normal'>{$t['dname']}</td>
									<td width='20%' class='{$row_class}' style='font-weight: normal'>{$t['date']}</td>
									<td width='13%' class='{$row_class}'>{$t['status']}</td>
								</tr>";
			}
		}
		else
		{
			$ticket_rows .= "<tr>
								<td class='option1' align='center'>Нет тикетов ожидающих ответа.</td>
							</tr>";
		}

		#=============================
		# Grab Logs
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'logs',
											  	 'where'	=> array( 'type', '=', 2 ),
											  	 'order'	=> array( 'date' => 'desc' ),
											  	 'limit'	=> array( 0,5 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$log_rows = ""; // Initialize for Security
		$log_count = 0; // Initialize for Security

		while( $l = $this->ifthd->core->db->fetch_row() )
		{
			$log_count ++;
			
			( $log_count & 1 ) ? $log_class = 'option1-mini' : $log_class = 'option2-mini';
			
			$l['date'] = $this->ifthd->ift_date( $l['date'], "n/j/y g:i A" );

			if ( $l['level'] == 2 )
			{
				$l['action'] = "<font color='#790000'>". $l['action'] ."</font>";
				$l['date'] = "<font color='#790000'>". $l['date'] ."</font>";
				$l['mname'] = "<font color='#790000'>". $l['mname'] ."</font>";
				$l['ipadd'] = "<font color='#790000'>". $l['ipadd'] ."</font>";
			}

			$log_rows .= "<tr>
							<td width='45%' class='{$log_class}'>{$l['action']}</td>
							<td width='21%' class='{$log_class}' style='font-weight: normal'>{$l['date']}</td>
							<td width='17%' class='{$log_class}' align='center'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=view&amp;id={$l['mid']}'>{$l['mname']}</a></td>
							<td width='17%' class='{$log_class}' align='center' style='font-weight: normal'>{$l['ipadd']}</td>
						</tr>";
		}

		#=============================
		# Version Check
		#=============================
		
		if ( $this->ifthd->core->cache['temp']['vercheck_time'] < ( time() - ( 60 * 60 * 24 * 7 ) ) )
		{
			$version_check_url = 'http://core.accord5.com/trellis/update.php?v='. $this->ifthd->vernum;
			
			if ( ini_get('allow_url_fopen') == 1 )
			{
				$context = stream_context_create( array( 'http' => array( 'timeout'	=> 5 ) ) );
				
				$response = file_get_contents( $version_check_url, null, $context );
			}
			elseif ( function_exists('curl_version') )
			{
				$ch = curl_init();
				
				curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 5 );
				curl_setopt( $ch, CURLOPT_TIMEOUT, 5 );
				curl_setopt( $ch, CURLOPT_URL, $version_check_url );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
				
				$response = curl_exec($ch);
				
				curl_close($ch);
			}
			
			$this->ifthd->core->add_cache( 'temp', array( 'vercheck_time' => time(), 'vercheck_response' => $response ) );
		}
		else
		{
			$response = $this->ifthd->core->cache['temp']['vercheck_response'];
		}
		
		if ( $response == 1 )
		{
			$version_img_url = '<! IMG_DIR !>/update_available.jpg';
		}
		elseif ( $response == 2 )
		{
			$version_img_url = '<! IMG_DIR !>/up_to_date.jpg';
		}
		else
		{
			$version_img_url = 'http://core.accord5.com/trellis/version_check.php?v='. $this->ifthd->vernum;
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

		$acp_content = "{$error}
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<td width='49%' valign='top'>
							
								<!-- Блок блокнота админа -->
								<div class='groupbox'>Администраторский блокнот</div>
								<form action='<! HD_URL !>/admin.php?section=admin&amp;code=notes&amp;do=update' method='post'>
								<table width='100%' cellpadding='0' cellspacing='0'>
								<tr>
									<td class='option1'>								
										<textarea name='notes' id='notes' rows='3' cols='40' style='width: 98%; height: 96px;'>". $notes ."</textarea>
									</td>
								</tr>
								<tr>
									<td class='option2' align='center'>								
										<input type='submit' name='submit' id='save' value='Сохранить' style='cursor: pointer;' />
									</td>
								</tr>
								</table>
								</form>
							
							</td>
							
							<!-- Spacer -->
							<td width='2%'>&nbsp;</td>
							
							<td width='49%' valign='top'>
								
								<!-- Блок Состояние системы -->
								<div class='groupbox'>Состояние системы</div>
								<table width='100%' cellpadding='0' cellspacing='0'>
								<tr>
									<td class='option1' colspan='2'>
									
									<div style='padding: 4px; border: 1px solid #9F9F9F; background: #FFF; text-align: center;'>
									<a href='http://www.accord5.com/trellis'><img src='{$version_img_url}' alt='Проверка обновлений' /></a>
									</div>
									
									</td>
								</tr>
								<tr>
									<td width='40%' class='option2'>Версия продукта</td>
									<td width='60%' class='option2' style='font-weight: normal;'>". substr( $this->ifthd->vername, 1 ) ." ({$this->ifthd->vernum})</td>
								</tr>
								<tr>
									<td width='40%' class='option1'>PHP версия</td>
									<td width='60%' class='option1' style='font-weight: normal;'>". phpversion() ."</td>
								</tr>
								<tr>
									<td width='40%' class='option2'>MySQL версия</td>
									<td width='60%' class='option2' style='font-weight: normal;'>". mysql_get_server_info() ."</td>
								</tr>
								</table>
							
							</td>
						</tr>						
						<!-- Spacer -->
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td colspan='3'>
							
								<!-- Блок ожидающих тикетов -->
								<div class='groupbox'>Тикеты ожидающие ответа</div>
								<table width='100%' cellpadding='0' cellspacing='0'>
								". $ticket_rows ."
								</table>
							
							</td>
						</tr>						
						<!-- Spacer -->
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td colspan='3'>
							
								<!-- Блок Последних 5 действий администратора -->
								<div class='groupbox'>Последние 5 действий администратора</div>
								<table width='100%' cellpadding='0' cellspacing='0'>
								". $log_rows ."
								</table>
							
							</td>
						</tr>
						</table>";

		$this->ifthd->skin->add_output( $acp_content );

		$this->ifthd->skin->do_output( array( 'title' => 'Обзор системы' ) );
	}

	#=======================================
	# @ Notes Action
	# Perform an action with ACP notes.
	#=======================================

	function notes_action()
	{
		if ( $this->ifthd->input['do'] == 'update' )
		{
			$to_cache['notes'] = $this->ifthd->input['notes'];

			$this->ifthd->core->add_cache( 'misc', $to_cache );

			$this->show_home( $to_cache['notes'] );
		}
	}

}

?>