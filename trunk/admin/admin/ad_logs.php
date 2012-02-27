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
|    | Admin Logs
#======================================================
*/

class ad_logs {

	#=======================================
	# @ Auto Run
	# Function that is run automatically
	# when the file is required.
	#=======================================

	function auto_run()
	{
		$this->ifthd->skin->set_section( 'Журналирование - логи' );		
		$this->ifthd->skin->set_description( 'Просмотр логов на ошибки и действия, удаление лог-файлов - администратора, пользователей, отчёты безопасности и логи тикетов.' );
		
		if ( ! $this->ifthd->member['acp']['admin_logs'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		switch( $this->ifthd->input['code'] )
    	{
    		case 'prune':
				$this->prune_logs();
    		break;

    		default:
    			$this->show_logs();
    		break;
		}
	}

	#=======================================
	# @ Show Logs
	# Show the logs page.
	#=======================================

	function show_logs()
	{
		#=============================
		# What Type Of Logs?
		#=============================

		if ( ! $this->ifthd->input['code'] )
		{
			$this->ifthd->input['code'] = 'admin';
		}

		if ( ! $this->ifthd->member['acp'][ 'admin_logs_'. $this->ifthd->input['code'] ] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		if ( $this->ifthd->input['code'] == 'mod' )
		{
			$type_id = 1;
			$logs_name = 'Модераторов';
		}
		elseif ( $this->ifthd->input['code'] == 'admin' )
		{
			$type_id = 2;
			$logs_name = 'Администраторов';
		}
		elseif ( $this->ifthd->input['code'] == 'error' )
		{
			$type_id = 3;
			$logs_name = 'Ошибок';
		}
		elseif ( $this->ifthd->input['code'] == 'security' )
		{
			$type_id = 4;
			$logs_name = 'Безопасности';
		}
		elseif ( $this->ifthd->input['code'] == 'email' )
		{
			$type_id = 5;
			$logs_name = 'Email';
		}
		elseif ( $this->ifthd->input['code'] == 'member' )
		{
			$type_id = 6;
			$logs_name = 'Пользователей';
		}
		elseif ( $this->ifthd->input['code'] == 'ticket' )
		{
			$type_id = 7;
			$logs_name = 'Тикетов';
		}
		else
		{
			$type_id = 9;
			$logs_name = 'Прочие';
		}

		#=============================
		# Sorting Options
		#=============================

		$link_extra = ""; // Initialize for Security

		if ( $this->ifthd->input['code'] )
		{
			$link_extra = '&amp;code='. $this->ifthd->input['code'];
		}

		if ( $this->ifthd->input['sort'] )
		{
			$sort = $this->ifthd->input['sort'];
		}
		else
		{
			$sort = 'date';
		}

		$order_var = "order_". $sort;
		$img_var = "img_". $sort;

		if ( $this->ifthd->input['order'] )
		{
			$order = strtoupper( $this->ifthd->input['order'] );
		}
		elseif ( $sort == 'date' )
		{
			$order = 'DESC';
		}

		if ( $order == 'DESC' )
		{
			$$order_var = "&amp;order=asc";
			$$img_var = "&nbsp;<img src='<! IMG_DIR !>/arrow_up.gif' alt='Вверх' />";
		}
		else
		{
			$$order_var = "&amp;order=desc";
			$$img_var = "&nbsp;<img src='<! IMG_DIR !>/arrow_down.gif' alt='Вниз' />";
		}

		$link_action = "<a href='<! HD_URL !>/admin.php?section=admin&amp;act=logs". $link_extra ."&amp;sort=action". $order_action ."'>Действие". $img_action ."</a>";
		$link_date = "<a href='<! HD_URL !>/admin.php?section=admin&amp;act=logs". $link_extra ."&amp;sort=date". $order_date ."'>Дата". $img_date ."</a>";
		$link_user = "<a href='<! HD_URL !>/admin.php?section=admin&amp;act=logs". $link_extra ."&amp;sort=mname". $order_mname ."'>Пользователь". $img_mname ."</a>";
		$link_ipadd = "<a href='<! HD_URL !>/admin.php?section=admin&amp;act=logs". $link_extra ."&amp;sort=ipadd". $order_ipadd ."'>IP адресс". $img_ipadd ."</a>";

		if ( $this->ifthd->input['sort'] )
		{
			$link_extra .= "&amp;sort=". $this->ifthd->input['sort'];
		}
		if ( $this->ifthd->input['order'] )
		{
			$link_extra .= "&amp;order=". $this->ifthd->input['order'];
		}

		#=============================
		# Grab Logs
		#=============================

		if ( $this->ifthd->input['st'] )
		{
			$start = $this->ifthd->input['st'];
		}
		else
		{
			$start = 0;
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id' ),
											  	 'from'		=> 'logs',
							 				  	 'where'	=> array( 'type', '=', $type_id ),
							 				  	 'order'	=> array( $sort => $order ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$log_count = $this->ifthd->core->db->get_num_rows();

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'logs',
							 				  	 'where'	=> array( 'type', '=', $type_id ),
							 				  	 'order'	=> array( $sort => $order ),
							 				  	 'limit'	=> array( $start, 20 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$log_rows = ""; // Initialize for Security
		$row_count = 0; // Initialize for Security

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			while( $l = $this->ifthd->core->db->fetch_row() )
			{
				$row_count ++;
				
				( $row_count & 1 ) ? $log_class = 'option1-mini' : $log_class = 'option2-mini';
				
				#=============================
				# Fix Up Information
				#=============================

				$l['date'] = $this->ifthd->ift_date( $l['date'], "n/j/y g:i A" );

				if ( $l['level'] == 2 )
				{
					$l['action'] = "<font color='#790000'>". $l['action'] ."</font>";
					$l['date'] = "<font color='#790000'>". $l['date'] ."</font>";
					$l['mname'] = "<font color='#790000'>". $l['mname'] ."</font>";
					$l['ipadd'] = "<font color='#790000'>". $l['ipadd'] ."</font>";
				}

				$log_rows .= "<tr>
									<td class='{$log_class}'>{$l['action']}</td>
									<td class='{$log_class}'>{$l['date']}</td>
									<td class='{$log_class}' align='center'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=view&amp;id={$l['mid']}'>{$l['mname']}</a></td>
									<td class='{$log_class}' align='center'>{$l['ipadd']}</td>
								</tr>";
			}
		}
		else
		{
			$log_rows .= "<tr>
								<td class='option1' colspan='4'>Нет информации для отображения.</td>
							</tr>";
		}

		#=============================
		# Do Output
		#=============================

		$page_links = $this->ifthd->page_links( '?section=admin&amp;act=logs'. $link_extra, $log_count, 20, $start, 1 );

		$this->output = "<div class='groupbox'><div style='float: right'><a href=\"javascript:Effect.toggle('prunebox','slide',{duration: 0.5});\"><img src='<! IMG_DIR !>/button_mini_prune.gif' alt='Удалить' /></a></div>Логи {$logs_name}</div>
						<div id='prunebox' style='display:none'>
						<form action='<! HD_URL !>/admin.php?section=admin&amp;act=logs&amp;code=prune&amp;type={$type_id}' method='post'>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<td width='37%' class='option1' style='border-right: 1px solid #FFF;'>
								<input type='radio' name='ptype' id='d_old' value='1' class='radio' checked='checked' /> <label for='d_old'>Удалить логи за последние </label><input type='text' name='odays' id='odays' size='2' /><label for='d_old'> дней.</label>
							</td>
							<td width='63%' class='option1'>
								<input type='radio' name='ptype' id='d_all' value='2' class='radio' /> <label for='d_all'>Удалить все логи.</label>
							</td>
						</tr>
						<tr>
							<td colspan='3' class='option2'>
								<input type='submit' name='prune' id='prune' value='Удалить логи' class='button' />
							</td>
						</tr>
						</table>
						</form>
						</div>
						<table width='100%' cellpadding='0' cellspacing='0'>
						<tr>
							<th width='45%' align='left'>{$link_action}</th>
							<th width='21%' align='left'>{$link_date}</th>
							<th width='17%'>{$link_user}</th>
							<th width='17%'>{$link_ipadd}</th>
						</tr>
						". $log_rows ."
						</table>
						<br />{$page_links}";

		$this->ifthd->skin->add_output( $this->output );

		$this->nav = array(
						   "<a href='<! HD_URL !>/admin.php?section=admin&amp;act=logs'>Логи</a>",
						   $logs_name ." Логи",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Просмотр логов' ) );
	}

	#=======================================
	# @ Prune Logs
	# Delete logs.
	#=======================================

	function prune_logs()
	{
		#=============================
		# Security Check
		#=============================

		if ( ! $this->ifthd->member['acp']['admin_logs_prune'] )
		{
			$this->ifthd->skin->error('no_perm');
		}

		#=============================
		# Log Name
		#=============================

		if ( $this->ifthd->input['type'] == 1 )
		{
			$log_name = 'Модераторов';
		}
		elseif ( $this->ifthd->input['type'] == 2 )
		{
			$log_name = 'Администраторов';
		}
		elseif ( $this->ifthd->input['type'] == 3 )
		{
			$log_name = 'Ошибок';
		}
		elseif ( $this->ifthd->input['type'] == 4 )
		{
			$log_name = 'Безопасности';
		}
		elseif ( $this->ifthd->input['type'] == 5 )
		{
			$log_name = 'Email';
		}
		elseif ( $this->ifthd->input['type'] == 6 )
		{
			$log_name = 'Пользователей';
		}
		elseif ( $this->ifthd->input['type'] == 7 )
		{
			$log_name = 'Тикетов';
		}
		else
		{
			$log_name = 'Прочие';
		}

		#=============================
		# How Are We Pruning?
		#=============================

		if ( $this->ifthd->input['ptype'] == 1 )
		{
			#=============================
			# How Many Days?
			#=============================

			if ( ! $this->ifthd->input['odays'] )
			{
				$this->ifthd->skin->error('prune_no_days');
			}

			$last_day = time() - ( 60 * 60 * 24 * $this->ifthd->input['odays'] );

			#=============================
			# Delete Logs :(
			# OhNoes is smelly.
			#=============================

			$this->ifthd->core->db->construct( array(
												  	 'delete'	=> 'logs',
								 				  	 'where'	=> array( array( 'type', '=', $this->ifthd->input['type'] ), array( 'date', '<', $last_day, 'and' ) ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$this->ifthd->log( 'admin', "Удалить ". $log_name ." Логи", 2, $this->ifthd->input['type'] );

			#=============================
			# Redirect
			#=============================

			$this->ifthd->skin->redirect( '?section=admin&act=logs&code=admin', 'prune_logs_success' );
		}
		elseif ( $this->ifthd->input['ptype'] == 2 )
		{
			#=============================
			# Delete Logs :(
			#=============================

			$this->ifthd->core->db->construct( array(
												  	 'delete'	=> 'logs',
								 				  	 'where'	=> array( 'type', '=', $this->ifthd->input['type'] ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$this->ifthd->log( 'admin', "Удалить ". $log_name ." Логи", 2, $this->ifthd->input['type'] );

			#=============================
			# Redirect
			#=============================

			$this->ifthd->skin->redirect( '?section=admin&act=logs&code=admin', 'prune_logs_success' );
		}
	}

}

?>