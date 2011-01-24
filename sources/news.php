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
|    | News :: Sources
#======================================================
*/

class news {

	#=======================================
	# @ Auto Run
	# Function that is run automatically
	# when the file is required.
	#=======================================

	function auto_run()
	{
		#=============================
		# Initialize
		#=============================

		if ( ! $this->ifthd->core->cache['config']['enable_news'] || ! $this->ifthd->core->cache['config']['enable_news_page'] )
		{
			$this->ifthd->skin->error( 'news_disabled');
		}

		$this->ifthd->load_lang('news');

		switch( $this->ifthd->input['code'] )
    	{
    		case 'view':
				$this->show_news();
    		break;
    		case 'print':
				$this->show_news( 0, 'print' );
    		break;
    		case 'comment':
				$this->submit_comment();
    		break;
    		case 'edit':
				$this->edit_comment();
    		break;

    		case 'doedit':
				$this->do_edit();
    		break;
    		case 'delete':
				$this->do_delete();
    		break;

    		default:
    			$this->news_portal();
    		break;
		}
	}

	#=======================================
	# @ News Portal
	# Display all news. :)
	#=======================================

	function news_portal()
	{
		#=============================
		# Grab Annoucements
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'announcements',
							 				  	 'order'	=> array( 'date' => 'DESC' ),
							 				  	 'limit'	=> array( 0, $this->ifthd->core->cache['config']['news_page_amount'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$final_a = array(); // Initialize for Security
		$row_count = 0; // Initialize for Security

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			while ( $a = $this->ifthd->core->db->fetch_row() )
			{
				#=============================
				# Fix Up Information
				#=============================
				
				$row_count ++;
					
				( $row_count & 1 ) ? $a['class'] = 1 : $a['class'] = 2;

				$a['date'] = $this->ifthd->ift_date( $a['date'] );

				if ( $this->ifthd->core->cache['config']['enable_news_rte'] )
				{
					$a['content'] = $this->ifthd->prepare_output( $this->ifthd->remove_dbl_spaces( $this->ifthd->convert_html( $a['content'] ) ), 0, 0, 1, 1 );
				}
				else
				{
					$a['content'] = $this->ifthd->prepare_output( $a['content'], 0, 0, 1 );
				}

				$final_a[] = $a;

				$this->ifthd->core->template->set_var( 'news', $final_a );
			}
		}

		#=============================
		# Do Output
		#=============================

		$this->ifthd->core->template->set_var( 'sub_tpl', 'news.tpl' );

		$this->nav = array(
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=news'>{$this->ifthd->lang['news']}</a>",
						   );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => $this->ifthd->lang['news'] ) );
	}

	#=======================================
	# @ Show News
	# Display a news article.
	#=======================================

	function show_news($error="", $type="")
	{
		#=============================
		# Grab Annoucement
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'announcements',
							 				  	 'where'	=> array( 'id', '=', intval( $this->ifthd->input['id'] ) ),
							 				  	 'limit'	=> array( 0, 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "News Not Found ID: ". intval( $this->ifthd->input['id'] ) );

			$this->ifthd->skin->error('no_news');
		}

		$a = $this->ifthd->core->db->fetch_row();

		#=============================
		# Fix Up Information
		#=============================

		$a['date'] = $this->ifthd->ift_date( $a['date'] );

		if ( $this->ifthd->core->cache['config']['enable_news_rte'] )
		{
			$a['content'] = $this->ifthd->prepare_output( $this->ifthd->remove_dbl_spaces( $this->ifthd->convert_html( $a['content'] ) ), 0, 0, 1, 1 );
		}
		else
		{
			$a['content'] = $this->ifthd->prepare_output( $a['content'], 0, 0, 1 );
		}

		$this->ifthd->core->template->set_var( 'n', $a );

		#=============================
		# Grab Comments?
		#=============================
		
		$row_count = 0; // Initialize for Security

		if ( $a['comments'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> 'all',
												  	 'from'		=> 'news_comments',
								 				  	 'where'	=> array( 'nid', '=', $a['id'] ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$comments = array(); // Initialize for Security

			while( $c = $this->ifthd->core->db->fetch_row() )
			{
				#=============================
				# Fix Up Information
				#=============================
					
				$row_count ++;
					
				( $row_count & 1 ) ? $c['class'] = 1 : $c['class'] = 2;

				$c['time_ago'] = $this->ifthd->ift_date( $c['date'], '', 3 );

				$c['date'] = $this->ifthd->ift_date( $c['date'] );

				$c['comment'] = $this->ifthd->prepare_output( $c['comment'], 0, 0, 1 );

				$comments[] = $c;
			}

			$this->ifthd->core->template->set_var( 'comments', $comments );
		}

		#=============================
		# Do Output
		#=============================

		if ( $this->ifthd->core->cache['config']['news_comments'] && $this->ifthd->member['g_news_comment'] && ! $this->ifthd->member['ban_news_comment'] && ! $a['dis_comments'] && $this->ifthd->member['id'] )
		{
			$this->ifthd->core->template->set_var( 'show_comment_form', 1 );

			$this->ifthd->core->template->set_var( 'token_add_comment', $this->ifthd->create_token('acomment') );

			if ( $error )
			{
				$this->ifthd->core->template->set_var( 'error', $this->ifthd->lang[ 'err_'. $error ] );
			}
		}

		$this->nav = array(
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=news'>{$this->ifthd->lang['news']}</a>",
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=news&amp;code=view&amp;id={$a['id']}'>{$a['title']}</a>",
						   );

		if ( $type == 'print' )
		{
			$this->ifthd->core->template->set_var( 'sub_tpl', 'news_print.tpl' );

			$this->ifthd->skin->do_print( array( 'title' => $this->ifthd->lang['news'] .' :: '. $a['title'] ) );
		}
		else
		{
			$this->ifthd->core->template->set_var( 'sub_tpl', 'news_show.tpl' );

			$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => $this->ifthd->lang['news'] .' :: '. $a['title'] ) );
		}
	}

	#=======================================
	# @ Submit Comment
	# Adds a new comment to an announcement.
	#=======================================

	function submit_comment()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->create_token('acomment');

		if ( ! $this->ifthd->core->cache['config']['news_comments'] )
		{
			$this->ifthd->skin->error('news_comment_disabled');
		}

		if ( ! $this->ifthd->member['g_news_comment'] || $this->ifthd->member['ban_news_comment'] )
		{
			$this->ifthd->log( 'security', "Blocked News Comment" );

			$this->ifthd->skin->error('banned_news_comment');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'announcements',
							 				  	 'where'	=> array( 'id', '=', intval( $this->ifthd->input['id'] ) ),
							 				  	 'limit'	=> array( 0, 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "Announcement Not Found ID: ". intval( $this->ifthd->input['id'] ) );

			$this->ifthd->skin->error('no_announcement');
		}

		$a = $this->ifthd->core->db->fetch_row();

		if ( ! $this->ifthd->member['id'] )
		{
			$this->ifthd->log( 'security', "Comment Blocked From Guest &#039;". $a['title'] ."&#039;", 1, $a['id'] );

			$this->ifthd->skin->error( 'must_be_user', 1 );
		}

		if ( $a['dis_comments'] )
		{
			$this->ifthd->log( 'security', "Blocked News Comment" );

			$this->ifthd->skin->error('banned_news_comment');
		}

		if ( ! $this->ifthd->input['comment'] )
		{
			$this->show_news('no_comment');
		}

		#=============================
		# Add Comment
		#=============================

		$db_array = array(
						  'nid'			=> $a['id'],
						  'mid'			=> $this->ifthd->member['id'],
						  'mname'		=> $this->ifthd->member['name'],
						  'comment'		=> $this->ifthd->input['comment'],
						  'date'		=> time(),
						  'ipadd'		=> $this->ifthd->input['ip_address'],
						 );

		$this->ifthd->core->db->construct( array(
											  	 'insert'	=> 'news_comments',
											  	 'set'		=> $db_array,
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$comment_id = $this->ifthd->core->db->get_insert_id();

		$this->ifthd->log( 'member', "News Comment Added &#039;". $a['title'] ."&#039;", 1, $a['id'] );

		#=============================
		# Update Announcement
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'announcements',
											  	 'set'		=> array( 'comments' => $a['comments'] + 1 ),
							 				  	 'where'	=> array( 'id', '=', $a['id'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->next_shutdown();
		$this->ifthd->core->db->execute();

		#=============================
		# Redirect
		#=============================

		$this->ifthd->skin->redirect( '?act=news&code=view&id='. $a['id'] .'#com'. $comment_id, 'submit_comment_success' );
	}

	#=======================================
	# @ Edit Comment
	# Show edit comment form.
	#=======================================

	function edit_comment($error="")
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['g_com_edit_all'] )
		{
			$this->ifthd->log( 'security', "Blocked Editing of Comment" );

			$this->ifthd->skin->error('no_perm_com_edit');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'news_comments',
							 				  	 'where'	=> array( 'id', '=', intval( $this->ifthd->input['id'] ) ),
							 				  	 'limit'	=> array( 0, 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "News Comment Not Found ID: ". intval( $this->ifthd->input['id'] ) );

			$this->ifthd->skin->error('no_comment');
		}

		$c = $this->ifthd->core->db->fetch_row();

		$this->ifthd->core->template->set_var( 'comment', $c );

		#=============================
		# Do Output
		#=============================

		if ( $error )
		{
			$this->ifthd->core->template->set_var( 'error', $this->ifthd->lang[ 'err_'. $error ] );
		}

		$this->ifthd->core->template->set_var( 'token_edit_comment', $this->ifthd->create_token('acomment_edit') );

		$this->nav = array(
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=news'>{$this->ifthd->lang['news']}</a>",
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=news&amp;code=edit&amp;id={$c['id']}'>{$this->ifthd->lang['edit_comment']}</a>",
						   );

		$this->ifthd->core->template->set_var( 'sub_tpl', 'news_edit_comment.tpl' );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => $this->ifthd->lang['news'] .' :: '. $this->ifthd->lang['edit_comment'] ) );
	}

	#=======================================
	# @ Do Edit Comment
	# Edit comment. :P
	#=======================================

	function do_edit()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->check_token('acomment_edit');

		if ( ! $this->ifthd->member['g_com_edit_all'] )
		{
			$this->ifthd->log( 'security', "Blocked Editing of Comment" );

			$this->ifthd->skin->error('no_perm_com_edit');
		}

		if ( ! $this->ifthd->input['comment'] )
		{
			$this->edit_comment('no_comment');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'news_comments',
							 				  	 'where'	=> array( 'id', '=', intval( $this->ifthd->input['id'] ) ),
							 				  	 'limit'	=> array( 0, 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "News Comment Not Found ID: ". intval( $this->ifthd->input['id'] ) );

			$this->ifthd->skin->error('no_comment');
		}

		$c = $this->ifthd->core->db->fetch_row();

		#=============================
		# Update Comment
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'news_comments',
											  	 'set'		=> array( 'comment' => $this->ifthd->input['comment'] ),
							 				  	 'where'	=> array( 'id', '=', $c['id'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'member', "News Comment Edited ID #". $c['id'], 1, $c['id'] );

		#=============================
		# Do Output
		#=============================

		$this->ifthd->skin->redirect( '?act=news&code=view&id='. $c['nid'] .'#com'. $c['id'], 'edit_comment_success' );
	}

	#=======================================
	# @ Do Delete Comment
	# Delete comment. :(
	#=======================================

	function do_delete()
	{
		#=============================
		# Security Checks
		#=============================

		if ( ! $this->ifthd->member['g_com_delete_all'] )
		{
			$this->ifthd->log( 'security', "Blocked Deletion of Comment" );

			$this->ifthd->skin->error('no_perm_com_delete');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'c' => 'all', 'a' => array( 'comments' ) ),
											  	 'from'		=> array( 'c' => 'news_comments' ),
											  	 'join'		=> array( array( 'from' => array( 'a' => 'announcements' ), 'where' => array( 'c' => 'nid', '=', 'a' => 'id' ) ) ),
							 				  	 'where'	=> array( array( 'c' => 'id' ), '=', intval( $this->ifthd->input['id'] ) ),
							 				  	 'limit'	=> array( 0, 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "News Comment Not Found ID: ". intval( $this->ifthd->input['id'] ) );

			$this->ifthd->skin->error('no_comment');
		}

		$c = $this->ifthd->core->db->fetch_row();

		#=============================
		# Delete Comment
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'delete'	=> 'news_comments',
							 				  	 'where'	=> array( 'id', '=', $c['id'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'member', "News Comment Delete ID #". $c['id'], 2, $c['id'] );

		#=============================
		# Update Announcement
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'announcements',
											  	 'set'		=> array( 'comments' => $c['comments'] - 1 ),
							 				  	 'where'	=> array( 'id', '=', $c['nid'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->next_shutdown();
		$this->ifthd->core->db->execute();

		#=============================
		# Do Output
		#=============================

		$this->ifthd->skin->redirect( '?act=news&code=view&id='. $c['nid'], 'delete_comment_success' );
	}

}

?>