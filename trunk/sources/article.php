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
|    | Article :: Sources
#======================================================
*/

class article {

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

		if ( ! $this->ifthd->core->cache['config']['enable_kb'] )
		{
			$this->ifthd->skin->error('kb_disabled');
		}

		if ( ! $this->ifthd->member['g_kb_access'] || $this->ifthd->member['ban_kb'] )
		{
			$this->ifthd->log( 'security', "Заблокирован доступ к базе знаний" );

			$this->ifthd->skin->error('banned_kb');
		}

		$this->ifthd->load_lang('article');

		switch( $this->ifthd->input['code'] )
    	{
    		case 'view':
				$this->show_article();
    		break;
    		case 'print':
				$this->show_article( 0, 'print' );
    		break;
    		case 'cat':
				$this->show_category();
    		break;
    		case 'search':
				$this->do_search();
    		break;
    		case 'comment':
				$this->submit_comment();
    		break;
    		case 'rate':
				$this->do_rate();
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
    			$this->show_categories();
    		break;
		}
	}

	#=======================================
	# @ Show Article
	# Simply shows an article. :)
	#=======================================

	function show_article($error="", $type="")
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'articles',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0, 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "Статья не найдена ID: ". $this->ifthd->input['id'] );

			$this->ifthd->skin->error('no_article');
		}

		#=============================
		# Fix Up Information
		#=============================

		$a = $this->ifthd->core->db->fetch_row();

		$a['date'] = $this->ifthd->ift_date( $a['date'] );

		if ( $this->ifthd->core->cache['config']['enable_kb_rte'] )
		{
			$a['article'] = $this->ifthd->prepare_output( $this->ifthd->remove_dbl_spaces( $this->ifthd->convert_html( $a['article'] ) ), 0, 0, 1, 1 );
		}
		else
		{
			$a['article'] = $this->ifthd->prepare_output( $a['article'], 0, 0, 1 );
		}

		$this->ifthd->core->template->set_var( 'article', $a );

		#=============================
		# Can We Rate?
		#=============================

		if ( $this->ifthd->member['id'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> 'all',
												  	 'from'		=> 'article_rate',
								 				  	 'where'	=> array( array( 'aid', '=', $a['id'] ), array( 'mid', '=', $this->ifthd->member['id'], 'and' ) ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			if ( $this->ifthd->core->db->get_num_rows() || ! $this->ifthd->core->cache['config']['allow_kb_rating'] || ! $this->ifthd->member['g_kb_rate'] || $this->ifthd->member['ban_kb_rate'] || $a['dis_rating'] )
			{
				$rate = $this->rate_stars( $a['rating'], 0, $a['id'] );
			}
			else
			{
				$rate = $this->rate_stars( $a['rating'], 1, $a['id'] );
			}
		}
		else
		{
			$rate = $this->rate_stars( $a['rating'], 0, $a['id'] );
		}

		$this->ifthd->core->template->set_var( 'rate', $rate );

		#=============================
		# Grab Comments?
		#=============================

		if ( $a['comments'] )
		{
			$this->ifthd->core->db->construct( array(
												  	 'select'	=> 'all',
												  	 'from'		=> 'comments',
								 				  	 'where'	=> array( 'aid', '=', $a['id'] ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			$comments = array(); // Initialize for Security
			$row_count = 0; // Initialize for Security

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

		if ( $this->ifthd->core->cache['config']['allow_kb_comment'] && $this->ifthd->member['g_kb_comment'] && ! $this->ifthd->member['ban_kb_comment'] && ! $a['dis_comments'] && $this->ifthd->member['id'] )
		{
			$this->ifthd->core->template->set_var( 'show_comment_form', 1 );

			$this->ifthd->core->template->set_var( 'token_add_comment', $this->ifthd->create_token('acomment') );

			if ( $error )
			{
				$this->ifthd->core->template->set_var( 'error', $this->ifthd->lang[ 'err_'. $error ] );
			}
		}

		#=============================
		# Update Article
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'articles',
											  	 'set'		=> array( 'views' => $a['views'] + 1 ),
							 				  	 'where'	=> array( 'id', '=', $a['id'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->next_shutdown();
		$this->ifthd->core->db->execute();

		#=============================
		# Do Output
		#=============================

		$this->nav = array(
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=kb'>{$this->ifthd->lang['knowledge_base']}</a>",
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=kb&amp;code=cat&amp;id={$a['cat_id']}'>{$a['cat_name']}</a>",
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=kb&amp;code=view&amp;id={$a['id']}'>{$a['name']}</a>",
						   );

		if ( $type == 'print' )
		{
			$this->ifthd->core->template->set_var( 'sub_tpl', 'kb_print_article.tpl' );

			$this->ifthd->skin->do_print( array( 'title' => $this->ifthd->lang['knowledge_base'] .' :: '. $a['name'] ) );
		}
		else
		{
			$this->ifthd->core->template->set_var( 'sub_tpl', 'kb_show_article.tpl' );

			$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => $this->ifthd->lang['knowledge_base'] .' :: '. $a['name'] ) );
		}
	}

	#=======================================
	# @ Submit Comment
	# Adds a new comment to an article.
	#=======================================

	function submit_comment()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->create_token('acomment');

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->core->cache['config']['allow_kb_comment'] )
		{
			$this->ifthd->skin->error('kb_comment_disabled');
		}

		if ( ! $this->ifthd->member['g_kb_comment'] || $this->ifthd->member['ban_kb_comment'] )
		{
			$this->ifthd->log( 'security', "Заблокированные cтатьи и комментарии" );

			$this->ifthd->skin->error('banned_kb_comment');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'name', 'comments', 'dis_comments' ),
											  	 'from'		=> 'articles',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0, 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "Статья не найдена ID: ". $this->ifthd->input['id'] );

			$this->ifthd->skin->error('no_article');
		}

		$a = $this->ifthd->core->db->fetch_row();

		if ( ! $this->ifthd->member['id'] )
		{
			$this->ifthd->log( 'security', "Заблокированные комментарии от Гостей &#039;". $a['name'] ."&#039;", 1, $a['id'] );

			$this->ifthd->skin->error( 'must_be_user', 1 );
		}

		if ( $a['dis_comments'] )
		{
			$this->ifthd->log( 'security', "Заблокированные cтатьи и комментарии" );

			$this->ifthd->skin->error('banned_kb_comment');
		}

		if ( ! $this->ifthd->input['comment'] )
		{
			$this->show_article('no_comment');
		}

		#=============================
		# Add Comment
		#=============================

		$db_array = array(
						  'aid'			=> $this->ifthd->input['id'],
						  'mid'			=> $this->ifthd->member['id'],
						  'mname'		=> $this->ifthd->member['name'],
						  'comment'		=> $this->ifthd->input['comment'],
						  'date'		=> time(),
						  'ipadd'		=> $this->ifthd->input['ip_address'],
						 );

		$this->ifthd->core->db->construct( array(
											  	 'insert'	=> 'comments',
											  	 'set'		=> $db_array,
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$comment_id = $this->ifthd->core->db->get_insert_id();

		$this->ifthd->log( 'member', "Комментарий к статье добавлен &#039;". $a['name'] ."&#039;", 1, $a['id'] );

		#=============================
		# Update Article
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'articles',
											  	 'set'		=> array( 'comments' => $a['comments'] + 1 ),
							 				  	 'where'	=> array( 'id', '=', $a['id'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->next_shutdown();
		$this->ifthd->core->db->execute();

		#=============================
		# Redirect
		#=============================

		$this->ifthd->skin->redirect( '?act=article&code=view&id='. $a['id'] .'#com'. $comment_id, 'submit_comment_success' );
	}

	#=======================================
	# @ Show Categories
	# Show Knowledge Base categories.
	#=======================================

	function show_categories()
	{
		#=============================
		# Grab Categories
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'categories',
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$cats = ""; // Initialize for Security

		$c_count = $this->ifthd->core->db->get_num_rows();
		$row_count_right = 0; // Initialize for Security
		$row_count_left = 0; // Initialize for Security

		$cats_left = array(); // Initialize for Security
		$cats_right = array(); // Initialize for Security

		while ( $c = $this->ifthd->core->db->fetch_row() )
		{
			$cat_count ++;

			if ( $cat_count & 1 )
			{
				$row_count_left ++;
				
				( $row_count_left & 1 ) ? $c['class'] = 1 : $c['class'] = 2;
				
				$cats_left[] = $c;
			}
			else
			{
				$row_count_right ++;
				
				( $row_count_right & 1 ) ? $c['class'] = 1 : $c['class'] = 2;
				
				$cats_right[] = $c;
			}
		}

		#=============================
		# Do Output
		#=============================

		$this->ifthd->core->template->set_var( 'token_kb_search', $this->ifthd->create_token('search') );

		$this->ifthd->core->template->set_var( 'cats_left', $cats_left );
		$this->ifthd->core->template->set_var( 'cats_right', $cats_right );

		$this->nav = array(
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=kb'>{$this->ifthd->lang['knowledge_base']}</a>",
						   );

		$this->ifthd->core->template->set_var( 'sub_tpl', 'knowledge_base.tpl' );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => $this->ifthd->lang['knowledge_base'] ) );
	}

	#=======================================
	# @ Show Category
	# Show categories and articles.
	#=======================================

	function show_category()
	{
		#=============================
		# Grab Category
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'categories',
											  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
											  	 'limit'	=> array( 0, 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "KB Category Not Found ID: ". $this->ifthd->input['id'] );

			$this->ifthd->skin->error('no_category');
		}

		$c = $this->ifthd->core->db->fetch_row();

		$this->ifthd->core->template->set_var( 'c', $c );

		#=============================
		# Grab Articles
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'name', 'description', 'date' ),
											  	 'from'		=> 'articles',
											  	 'where'	=> array( 'cat_id', '=', $c['id'] ),
											  	 'order'	=> array( 'date' => 'desc' ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$articles = array(); // Initialize for Security
		$row_count = 0; // Initialize for Security

		while ( $a = $this->ifthd->core->db->fetch_row() )
		{
			$row_count ++;
			
			( $row_count & 1 ) ? $a['class'] = 1 : $a['class'] = 2;
			
			$a['date'] = $this->ifthd->ift_date( $a['date'] );

			$articles[] = $a;
		}

		$this->ifthd->core->template->set_var( 'articles', $articles );

		#=============================
		# Do Output
		#=============================

		$this->ifthd->core->template->set_var( 'token_kb_search', $this->ifthd->create_token('search') );

		$this->nav = array(
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=kb'>{$this->ifthd->lang['knowledge_base']}</a>",
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=kb&amp;code=cat&amp;id={$c['id']}'>{$c['name']}</a>",
						   );

		$this->ifthd->core->template->set_var( 'sub_tpl', 'kb_show_category.tpl' );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => $this->ifthd->lang['knowledge_base'] .' :: '. $c['name'] ) );
	}

	#=======================================
	# @ Do Search
	# Performs a search and returns results.
	#=======================================

	function do_search()
	{
		#=============================
		# Search!
		#=============================

		$this->ifthd->check_token('search');

		$searchstring = $this->ifthd->input['keywords'];

		$sql = "SELECT *, ( 1.6 * ( MATCH(keywords) AGAINST ('". mysql_real_escape_string( $searchstring ) ."' IN BOOLEAN MODE) ) + 0.9 * ( MATCH(name) AGAINST ('". mysql_real_escape_string( $searchstring ) ."' IN BOOLEAN MODE) ) + ( 0.6 * ( MATCH(article) AGAINST ('". mysql_real_escape_string( $searchstring ) ."' IN BOOLEAN MODE) ) ) ) AS score FROM ". DB_PRE ."articles WHERE MATCH(name, description, article) AGAINST ('". mysql_real_escape_string( $searchstring ) ."' IN BOOLEAN MODE) ORDER BY score DESC";

		$this->ifthd->core->db->query( $sql );

		$art = ""; // Initialize for Security

		while( $m = $this->ifthd->core->db->fetch_row() )
		{
		   	if ( $m['score'] > $max_score )
		   	{
		   		$max_score = $m['score'];
		   	}

		   	$art[] = $m;
		}

		$articles = array(); // Initialize for Security
		$row_count = 0; // Initialize for Security

		if ( is_array( $art ) )
		{
			while ( list( , $a ) = each( $art ) )
		    {
				#=============================
				# Fix Up Information
				#=============================
				
				$row_count ++;
				
				( $row_count & 1 ) ? $a['class'] = 1 : $a['class'] = 2;

				$a['date'] = $this->ifthd->ift_date( $a['date'] );

				$a['score'] = @ round( ( $a['score'] / $max_score ) * 100 ) ."%";

		    	$articles[] = $a;
		    }

		    $this->ifthd->core->template->set_var( 'results', $articles );
	    }

	    #=============================
		# Do Output
		#=============================

		$this->nav = array(
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=kb'>{$this->ifthd->lang['knowledge_base']}</a>",
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=kb&amp;code=search'>{$this->ifthd->lang['search_results']}</a>",
						   );

		$this->ifthd->core->template->set_var( 'sub_tpl', 'kb_search_results.tpl' );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => $this->ifthd->lang['knowledge_base'] .' :: '. $this->ifthd->lang['search_results'] ) );
	}

	#=======================================
	# @ Do Rate
	# Adding rating to article.
	#=======================================

	function do_rate()
	{
		#=============================
		# Security Checks
		#=============================

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );
		$this->ifthd->input['amount'] = intval( $this->ifthd->input['amount'] );

		if ( ! $this->ifthd->core->cache['config']['allow_kb_rating'] )
		{
			$this->ifthd->skin->error('kb_rating_disabled');
		}

		if ( ! $this->ifthd->member['g_kb_rate'] || $this->ifthd->member['ban_kb_rate'] )
		{
			$this->ifthd->log( 'security', "Blocked Article Rating" );

			$this->ifthd->skin->error('banned_kb_rate');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'name', 'votes', 'rating', 'dis_rating' ),
											  	 'from'		=> 'articles',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0, 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "Статья не найдена ID: ". $this->ifthd->input['id'] );

			$this->ifthd->skin->error('no_article');
		}

		$a = $this->ifthd->core->db->fetch_row();

		if ( ! $this->ifthd->member['id'] )
		{
			$this->ifthd->log( 'security', "Article Rating Blocked From Guest &#039;". $a['name'] ."&#039;", 1, $a['id'] );

			$this->ifthd->skin->error( 'must_be_user', 1 );
		}

		if ( $a['dis_rating'] )
		{
			$this->ifthd->log( 'security', "Blocked Article Rating" );

			$this->ifthd->skin->error('banned_kb_rate');
		}

		$allowed_ratings = array( 1, 2, 3, 4, 5 );

		if ( ! in_array( $this->ifthd->input['amount'], $allowed_ratings ) )
		{
			$this->ifthd->log( 'security', "Invalid Article Rating Amount &#039;". $a['name'] ."&#039;", 1, $a['id'] );

			$this->ifthd->skin->error('invalid_rate_value');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'article_rate',
							 				  	 'where'	=> array( array( 'aid', '=', $a['id'] ), array( 'mid', '=', $this->ifthd->member['id'], 'and' ) ),
							 				  	 'limit'	=> array( 0, 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'security', "Already Rated Article By Member &#039;". $a['name'] ."&#039;", 1, $a['id'] );

			$this->ifthd->skin->error('already_rated');
		}

		#=============================
		# Add Rating
		#=============================

		$db_array = array(
						  'aid'			=> $a['id'],
						  'mid'			=> $this->ifthd->member['id'],
						  'rating'		=> $this->ifthd->input['amount'],
						  'date'		=> time(),
						  'ipadd'		=> $this->ifthd->input['ip_address'],
						 );

		$this->ifthd->core->db->construct( array(
											  	 'insert'	=> 'article_rate',
											  	 'set'		=> $db_array,
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'member', "Article Rating Value ". $this->ifthd->input['amount'] ." Added &#039;". $a['name'] ."&#039;", 1, $a['id'] );

		#=============================
		# Update Article
		#=============================

		$new_rating = round( ( $a['rating'] + $this->ifthd->input['amount'] ) / ( $a['votes'] + 1 ), 2 );

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'articles',
											  	 'set'		=> array( 'votes' => $a['votes'] + 1, 'rating' => $new_rating ),
							 				  	 'where'	=> array( 'id', '=', $a['id'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->next_shutdown();
		$this->ifthd->core->db->execute();

	    #=============================
		# Do Output
		#=============================

		$this->ifthd->skin->redirect( '?act=article&code=view&id='. $a['id'], 'add_rating_success' );
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

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['g_com_edit_all'] )
		{
			$this->ifthd->log( 'security', "Blocked Editing of Comment" );

			$this->ifthd->skin->error('no_perm_com_edit');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'comments',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0, 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "Comment Not Found ID: ". $this->ifthd->input['id'] );

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
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=kb'>{$this->ifthd->lang['knowledge_base']}</a>",
						   "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=kb&amp;code=edit&amp;id={$c['id']}'>{$this->ifthd->lang['edit_comment']}</a>",
						   );

		$this->ifthd->core->template->set_var( 'sub_tpl', 'kb_edit_comment.tpl' );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => $this->ifthd->lang['knowledge_base'] .' :: '. $this->ifthd->lang['edit_comment'] ) );
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

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

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
											  	 'from'		=> 'comments',
							 				  	 'where'	=> array( 'id', '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0, 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "Comment Not Found ID: ". $this->ifthd->input['id'] );

			$this->ifthd->skin->error('no_comment');
		}

		$c = $this->ifthd->core->db->fetch_row();

		#=============================
		# Update Comment
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'comments',
											  	 'set'		=> array( 'comment' => $this->ifthd->input['comment'] ),
							 				  	 'where'	=> array( 'id', '=', $c['id'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'member', "Article Comment Edited ID #". $c['id'], 1, $c['id'] );

		#=============================
		# Do Output
		#=============================

		$this->ifthd->skin->redirect( '?act=article&code=view&id='. $c['aid'] .'#com'. $c['id'], 'edit_comment_success' );
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

		$this->ifthd->input['id'] = intval( $this->ifthd->input['id'] );

		if ( ! $this->ifthd->member['g_com_delete_all'] )
		{
			$this->ifthd->log( 'security', "Blocked Deletion of Comment" );

			$this->ifthd->skin->error('no_perm_com_delete');
		}

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'c' => 'all', 'a' => array( 'comments' ) ),
											  	 'from'		=> array( 'c' => 'comments' ),
											  	 'join'		=> array( array( 'from' => array( 'a' => 'articles' ), 'where' => array( 'c' => 'aid', '=', 'a' => 'id' ) ) ),
							 				  	 'where'	=> array( array( 'c' => 'id' ), '=', $this->ifthd->input['id'] ),
							 				  	 'limit'	=> array( 0, 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "Comment Not Found ID: ". $this->ifthd->input['id'] );

			$this->ifthd->skin->error('no_comment');
		}

		$c = $this->ifthd->core->db->fetch_row();

		#=============================
		# Delete Comment
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'delete'	=> 'comments',
							 				  	 'where'	=> array( 'id', '=', $c['id'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$this->ifthd->log( 'member', "Article Comment Deleted ID #". $c['id'], 2, $c['id'] );

		#=============================
		# Update Article
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'update'	=> 'articles',
											  	 'set'		=> array( 'comments' => $c['comments'] - 1 ),
							 				  	 'where'	=> array( 'id', '=', $c['aid'] ),
							 				  	 'limit'	=> array( 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->next_shutdown();
		$this->ifthd->core->db->execute();

		#=============================
		# Do Output
		#=============================

		$this->ifthd->skin->redirect( '?act=article&code=view&id='. $c['aid'], 'delete_comment_success' );
	}

	#=======================================
	# @ rate_stars()
	#=======================================

	function rate_stars($rating, $rate=0, $fid=0)
	{
		$half = 0; // Initialize for Security

		if ( strstr( $rating, '.' ) )
		{
			$half = 1;
		}

		$real_rating = $rating;

		$rating = round( $rating );

		for ( $x = 1; $x < $rating + 1; $x++ )
	    {
	    	if ( $half && $x == $rating )
	    	{
		    	if ( $rate )
		    	{
		    		$ift_html .= "<a href='". $this->ifthd->core->cache['config']['hd_url'] ."/index.php?act=article&amp;code=rate&amp;amount=". $x ."&amp;id={$fid}' title='". $x ." ". $this->ifthd->lang['stars'] ."'><img src='images/". $this->ifthd->skin->data['img_dir'] ."/rate_half.gif' alt='". $this->ifthd->lang['half_star'] ."' name='rate". $x ."' style='vertical-align:middle' onmouseover='amirate(". $x .")' onmouseout='unamirate(". $real_rating .")' /></a>";
		    	}
		    	else
		    	{
		    		$ift_html .= "<img src='images/". $this->ifthd->skin->data['img_dir'] ."/rate_half.gif' alt='". $this->ifthd->lang['half_star'] ."' name='rate". $x ."' style='vertical-align:middle' />";
		    	}
	    	}
	    	else
	    	{
	    		if ( $rate )
		    	{
		    		$ift_html .= "<a href='". $this->ifthd->core->cache['config']['hd_url'] ."/index.php?act=article&amp;code=rate&amp;amount=". $x ."&amp;id={$fid}' title='". $x ." ". $this->ifthd->lang['stars'] ."'><img src='images/". $this->ifthd->skin->data['img_dir'] ."/rate_on.gif' alt='". $this->ifthd->lang['lang.full_star'] ."' name='rate". $x ."' style='vertical-align:middle' onmouseover='amirate(". $x .")' onmouseout='unamirate(". $real_rating .")' /></a>";
		    	}
		    	else
		    	{
		    		$ift_html .= "<img src='images/". $this->ifthd->skin->data['img_dir'] ."/rate_on.gif' alt='". $this->ifthd->lang['full_star'] ."' name='rate". $x ."' style='vertical-align:middle' />";
		    	}
	    	}
	    }

	    for ( $x = $x; $x <= 5; $x++ )
	    {
	    	if ( $rate )
	    	{
	    		$ift_html .= "<a href='". $this->ifthd->core->cache['config']['hd_url'] ."/index.php?act=article&amp;code=rate&amp;amount=". $x ."&amp;id={$fid}' title='". $x ." ". $this->ifthd->lang['stars'] ."'><img src='images/". $this->ifthd->skin->data['img_dir'] ."/rate_off.gif' alt='". $this->ifthd->lang['no_star'] ."' name='rate". $x ."' style='vertical-align:middle' onmouseover='amirate(". $x .")' onmouseout='unamirate(". $real_rating .")' /></a>";
	    	}
	    	else
	    	{
	    		$ift_html .= "<img src='images/". $this->ifthd->skin->data['img_dir'] ."/rate_off.gif' alt='". $this->ifthd->lang['no_star'] ."' name='rate". $x ."' style='vertical-align:middle' />";
	    	}
	    }

		return $ift_html;
	}

}

?>