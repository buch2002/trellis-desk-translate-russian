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
|    | Trellis Desk Main Class
#======================================================
*/

class ifthd {

	var $vername = 'v1.0.4 Final';
	var $vernum = '10440094';
	var $shutdown_funcs = "";
	var $lang;

	var $xml_lang_abbr = "";
	var $xml_lang_name = "";
	var $xml_lang_file = "";
	var $xml_lang_key = "";
	var $xml_lang_replace = "";
	var $xml_current_element = "";
	var $xml_lang_bits = array();

	#=======================================
	# @ A5 Help Desk :: Common Stuff
	# This function is automatically run so
	# all of our common stuff goes here.
	#=======================================

	function ifthd($backend=0)
	{
		$this->start_timer();

		#=============================
		# Get Incoming
		#=============================

		$this->input = $this->get_post();

		#=============================
		# Captcha
		#=============================

		if ( $this->input['act'] == 'captcha' )
		{
			if ( $this->input['code'] == 'create' )
			{
				$this->captcha_create();
			}

			exit();
		}

		#=============================
		# I'm Hungry
		#=============================

		if ( ! isset( $this->input['act'] ) )
		{
			$this->input['act'] = 'portal';
		}

		#=============================
		# Call The Core!
		#=============================

		require_once HD_PATH ."core/ift.php";

		$this->core = new iftcore();

		#=============================
		# Lets Play Nice With Output
		#=============================

		if ( ! $backend )
		{
			if ( $this->core->cache['config']['enable_gzip'] )
			{
				ob_start("ob_gzhandler");
			}
			else
			{
				ob_start();
			}
		}

		#=============================
		# Load Configuration
		#=============================

		if ( ! file_exists( HD_PATH ."config.php" ) )
		{
			header('Location: install/');
		}

		require_once HD_PATH ."config.php";
		
		if ( ! $config['port'] ) $config['port'] = '3306';

		$this->config = $config;

		if ( is_array( $this->core->cache['config'] ) ) $this->core->cache['config'] = array_merge( $this->core->cache['config'], $this->config );

		#=============================
		# Load Our Database
		#=============================

		$this->core->load_db_module( $config['driver'] );

		#=============================
		# Connect to DB
		#=============================

		if ( $this->core->cache['config']['shutdown_queries'] )
		{
			$config['shutdown'] = 1;
		}

		$this->core->db->connect( $config );

		#=============================
		# Full Initialization?
		#=============================

		if ( ! $backend )
		{
			#=============================
			# Check / Run Tasks
			#=============================

			$this->check_tasks();

			#=============================
			# Load Session
			#=============================

			require_once HD_INC ."class_session.php";

			$session = new session();
			$session->ifthd = &$this;

			$this->member = $session->load_session();

			#=============================
			# Boot Template Engine
			#=============================

			$this->core->load_module('template');

			#=============================
			# Load Skin
			#=============================

			require_once HD_INC ."class_skin.php";

			$this->skin = new skin();
			$this->skin->ifthd = &$this;
			$this->skin->ifthd->shutdown_funcs =& $this->shutdown_funcs;

			$this->skin->load_skin();

			#=============================
			# Login / Logout
			#=============================

			if ( $this->input['act'] == 'login' )
			{
				$session->do_login();
			}
			elseif ( $this->input['act'] == 'glogin' )
			{
				$session->do_guest_login();
			}
			elseif ( $this->input['act'] == 'logout' )
			{
				$session->do_logout();
			}
			elseif ( $this->input['act'] == 'tickets' && $this->input['code'] == 'view' && ! $this->member['id'] && $this->input['email'] && $this->input['key'] )
			{
				$session->do_guest_login(1);
			}
		}
	}

	#=======================================
	# @ Time Functions
	# Tick Tock. :P
	#=======================================

   	function start_timer()
	{
		$temp_time = explode(" ", microtime() );

		$this->start_time = $temp_time[1] + $temp_time[0];
	}

	function end_timer()
	{
		$temp_time = explode(" ", microtime() );

		$this->end_time = $temp_time[1] + $temp_time[0];

		return round( ( $this->end_time - $this->start_time ) ,5 );
	}

	#=======================================
	# @ Get Post
	# Combines incoming $_GET and $_POST
	# plus sanitizes data.
	#=======================================

	function get_post()
	{
		$data = array();

		#=============================
		# $_GET Data
		#=============================

		if ( is_array( $_GET ) )
		{
			while ( list( $n, $v ) = each( $_GET ) )
			{
				if ( is_array( $_GET[$n] ) )
				{
					while ( list( $n2, $v2 ) = each( $_GET[$n] ) )
					{
						  $data[ $this->sanitize_data($n)][ $this->sanitize_data($n2) ] = $this->sanitize_data($v2);
					}
				}
				else
				{
					$data[ $this->sanitize_data($n) ] = $this->sanitize_data($v);
				}
			}
		}

		#=============================
		# $_POST Data
		#=============================

		if ( is_array( $_POST ) )
		{
			while ( list( $n, $v ) = each( $_POST ) )
			{
				if ( is_array( $_POST[$n] ) )
				{
					while ( list( $n2, $v2 ) = each( $_POST[$n] ) )
					{
						  $data[ $this->sanitize_data($n) ][ $this->sanitize_data($n2) ] = $this->sanitize_data($v2);
					}
				}
				else
				{
					$data[ $this->sanitize_data($n) ] = $this->sanitize_data($v);
				}
			}
		}

		#=============================
		# Other Junk
		#=============================

		$data['ip_address'] = $this->sanitize_data( $_SERVER['REMOTE_ADDR'] );

		return $data;
	}

	#=======================================
	# @ Sanitize Data
	# Cleans incoming data (HTML Characters,
	# backslashes, etc).
	#=======================================

	function sanitize_data($data, $noquotes=0)
    {
    	if ( $data == "" )
    	{
    		return FALSE;
    	}

    	if ( $noquotes )
    	{
    		if ( get_magic_quotes_gpc() )
    		{
	    		$data = trim( htmlentities( $data, ENT_COMPAT, 'UTF-8' ) );
    		}
			else
			{
				$data = trim( htmlentities( addslashes( $data ), ENT_COMPAT, 'UTF-8' ) );
			}
    	}
    	else
    	{
    		if ( get_magic_quotes_gpc() )
    		{
    			$data = trim( stripslashes( htmlentities( $data, ENT_QUOTES, 'UTF-8' ) ) );
    		}
    		else
    		{
	    		$data = trim( htmlentities( $data, ENT_QUOTES, 'UTF-8' ) );
    		}
    	}

    	// Other :)
    	$data = str_replace( "(", '&#40;', $data );
    	$data = str_replace( ")", '&#41;', $data );

    	// Unicode
    	$data = preg_replace( "/&amp;#([0-9]+);/s", "&#\\1;", $data );

    	return $data;
    }

    #=======================================
	# @ Rebuild: Ticket Statistics
	# Recounts ticket statistics.
	#=======================================

	function r_ticket_stats($shutdown=0)
	{
		if ( $shutdown == 1 )
		{
			$this->shutdown_funcs[] = 'r_ticket_stats';
			return TRUE;
		}

		#=============================
		# Grab Tickets
		#=============================

		$this->core->db->construct( array(
									  	  'select'	=> array( 'id', 'status' ),
									  	  'from'	=> 'tickets',
							 	   ) 	);

		$this->core->db->execute();

		if ( $this->core->db->get_num_rows() )
		{
			while( $t = $this->core->db->fetch_row() )
			{
				$to_cache['total'] ++;

				if ( $t['status'] == 1 )
				{
					$to_cache['open'] ++;
				}
				elseif ( $t['status'] == 2 )
				{
					$to_cache['open'] ++;
				}
				elseif ( $t['status'] == 3 )
				{
					$to_cache['hold'] ++;
				}
				elseif ( $t['status'] == 4 )
				{
					$to_cache['aca'] ++;
				}
				elseif ( $t['status'] == 5 )
				{
					$to_cache['escalated'] ++;
				}
				elseif ( $t['status'] == 6 )
				{
					$to_cache['closed'] ++;
				}
			}
		}

		$this->core->add_cache( 'stats_ticket', $to_cache, 1 );
	}

    #=======================================
	# @ Rebuild: KB Statistics
	# Recounts KB statistics.
	#=======================================

	function r_kb_stats($shutdown=0)
	{
		if ( $shutdown == 1 )
		{
			$this->shutdown_funcs[] = 'r_kb_stats';
			return TRUE;
		}

		#=============================
		# Grab Articles
		#=============================

		$this->core->db->construct( array(
									  	  'select'	=> array( 'id', 'cat_id', 'votes', 'views', 'rating' ),
									  	  'from'	=> 'articles',
							 	   ) 	);

		$this->core->db->execute();

		if ( $to_cache['total'] = $this->core->db->get_num_rows() )
		{
			while( $a = $this->core->db->fetch_row() )
			{
				$cats[ $a['cat_id'] ] ++;

				$to_cache['total_votes'] += $a['votes'];
				$to_cache['total_views'] += $a['views'];
				$to_cache['total_comments'] += $a['comments'];

				$temp_rating += $a['rating'];
			}
		}

		$to_cache['avg_rating'] += round( ( ( $temp_rating / $to_cache['total'] ) * 100 ), 1 );

		#=============================
		# Update Categories
		#=============================

		while ( list( $cid, $a_count ) = each( $cats ) )
		{
			$this->core->db->construct( array(
										  	  'update'		=> 'categories',
											  'set'			=> array( 'articles' => $a_count ),
											  'where'		=> array( 'id', '=', $cid ),
											  'limit'		=> array( 1 ),
								 	   )	 );

			$this->core->db->execute();
		}

		$this->core->add_cache( 'stats_kb', $to_cache, 1 );
	}

    #=======================================
	# @ Rebuild: Member Statistics
	# Recounts member statistics.
	#=======================================

	function r_member_stats($shutdown=0)
	{
		if ( $shutdown == 1 )
		{
			$this->shutdown_funcs[] = 'r_member_stats';
			return TRUE;
		}

		#=============================
		# Grab Members
		#=============================

		$this->core->db->construct( array(
									  	  'select'	=> array( 'id', 'name' ),
									  	  'from'	=> 'members',
									  	  'order'	=> array( 'joined' => 'desc' ),
							 	   ) 	);

		$this->core->db->execute();

		if ( $to_cache['total'] = $this->core->db->get_num_rows() )
		{
			$m = $this->core->db->fetch_row();

			$to_cache['newest_id'] = $m['id'];
			$to_cache['newest_name'] = $m['name'];
		}

		$this->core->add_cache( 'stats_member', $to_cache, 1 );
	}

	#=======================================
	# @ Load Language
	# Loads the defined language file.
	#=======================================

	function load_lang($name)
	{
		if ( ! $this->member['lang'] || ! file_exists( HD_PATH. "language/". $this->member['lang'] ."/lang_". $name .".php" ) )
		{
			$this->member['lang'] = $this->core->cache['lang']['default'];
		}

		require_once HD_PATH. "language/". $this->member['lang'] ."/lang_". $name .".php";

		$this->skin->ifthd->lang = array_merge( (array)$lang , (array)$this->skin->ifthd->lang );
		$this->lang = array_merge( (array)$lang , (array)$this->lang );
	}

	#=======================================
	# @ Check / Run Tasks
	# Checks to see if any tasks need to be
	# run and runs them if so.
	#=======================================

	function check_tasks()
    {
		#=============================
		# Cache
		#=============================

		if ( $this->core->cache['cdate']['config'] < time() - ( 60 * 60 * 24 * 5 ) )
		{
			$this->rebuild_set_cache();
		}

		if ( $this->core->cache['cdate']['depart'] < time() - ( 60 * 60 * 24 * 4 ) )
		{
			$this->rebuild_dprt_cache();
		}

		if ( $this->core->cache['cdate']['kbcat'] < time() - ( 60 * 60 * 24 * 3 ) )
		{
			$this->rebuild_cat_cache();
		}

		if ( $this->core->cache['cdate']['group'] < time() - ( 60 * 60 * 24 * 8 ) )
		{
			$this->rebuild_group_cache();
		}

		if ( $this->core->cache['cdate']['announce'] < time() - ( 60 * 60 * 24 * 2 ) )
		{
			$this->rebuild_announce_cache();
		}

		if ( $this->core->cache['cdate']['lang'] < time() - ( 60 * 60 * 24 * 6 ) )
		{
			$this->rebuild_lang_cache();
		}

		if ( $this->core->cache['cdate']['skin'] < time() - ( 60 * 60 * 24 * 7 ) )
		{
			$this->rebuild_skin_cache();
		}

		if ( $this->core->cache['cdate']['pfields'] < time() - ( 60 * 60 * 24 * 3.5 ) )
		{
			$this->rebuild_pfields_cache();
		}

		if ( $this->core->cache['cdate']['dfields'] < time() - ( 60 * 60 * 24 * 2.5 ) )
		{
			$this->rebuild_dfields_cache();
		}

		if ( $this->core->cache['cdate']['canned'] < time() - ( 60 * 60 * 24 * 8 ) )
		{
			$this->rebuild_canned_cache();
		}

		if ( $this->core->cache['cdate']['staff'] < time() - ( 60 * 60 * 24 * 4.5 ) )
		{
			$this->rebuild_staff_cache();
		}

		if ( $this->core->cache['tasks']['auto_close'] < time() - ( 60 * 60 ) )
		{
			$this->check_auto_close();
		}
    }

	#=======================================
	# @ Rebuild Settings Cache
	# Rebuilds the setting values from the
	# data into the flat-file cache.
	#=======================================

	function rebuild_set_cache()
    {
    	$this->core->db->construct( array(
									  	  'select'		=> 'all',
										  'from'		=> 'settings',
										  'where'		=> array( 'cf_cache', '=', 1 ),
							 	   )	 );

		$this->core->db->execute();

		while ( $cfg = $this->core->db->fetch_row() )
		{
			$to_cache[ $cfg['cf_key'] ] = $cfg['cf_value'];
		}

		$this->core->add_cache( 'config', $to_cache, 1 );
    }

	#=======================================
	# @ Rebuild Departments Cache
	# Rebuilds the department information
	# from the database.
	#=======================================

	function rebuild_dprt_cache()
    {
    	$this->core->db->construct( array(
									  	  'select'		=> 'all',
										  'from'		=> 'departments',
										  'order'		=> array( 'position' => 'asc' ),
							 	   )	 );

		$this->core->db->execute();

		while ( $d = $this->core->db->fetch_row() )
		{
			$to_cache[ $d['id'] ] = $d;
		}

		$this->core->add_cache( 'depart', $to_cache, 1 );
    }

	#=======================================
	# @ Rebuild Categories Cache
	# Rebuilds the category information
	# from the database.
	#=======================================

	function rebuild_cat_cache()
    {
    	$this->core->db->construct( array(
									  	  'select'		=> 'all',
										  'from'		=> 'categories',
							 	   )	 );

		$this->core->db->execute();

		while ( $c = $this->core->db->fetch_row() )
		{
			$to_cache[ $c['id'] ] = $c;
		}
		$this->core->add_cache( 'kbcat', $to_cache, 1 );
    }

	#=======================================
	# @ Rebuild Group Cache
	# Rebuilds the group information cache
	# from the database.
	#=======================================

	function rebuild_group_cache()
    {
    	$this->core->db->construct( array(
									  	  'select'		=> 'all',
										  'from'		=> 'groups',
							 	   )	 );

		$this->core->db->execute();

		while ( $g = $this->core->db->fetch_row() )
		{
			$to_cache[ $g['g_id'] ] = $g;
		}

		$this->core->add_cache( 'group', $to_cache, 1 );
    }

	#=======================================
	# @ Rebuild Announcement Cache
	# Rebuilds the announcement cache from
	# the database.
	#=======================================

	function rebuild_announce_cache($limit=0)
    {
    	if ( ! $limit )
    	{
    		$limit = $this->core->cache['config']['announce_amount'];
    	}

    	$this->core->db->construct( array(
									  	  'select'		=> 'all',
										  'from'		=> 'announcements',
										  'order'		=> array( 'date' => 'desc' ),
										  'limit'		=> array( 0, $limit ),
							 	   )	 );

		$this->core->db->execute();

		while ( $a = $this->core->db->fetch_row() )
		{
			if ( ! $a['excerpt'] )
			{
				if ( $this->core->cache['config']['enable_news_rte'] )
				{
					if ( $this->core->cache['config']['news_excerpt_trim'] )
					{
						$a['excerpt'] = $this->shorten_str( $this->remove_html( $this->remove_dbl_spaces( $this->convert_html( $a['content'] ) ) ), $this->core->cache['config']['news_excerpt_trim'], 1 );
					}
					else
					{
						$a['excerpt'] = $this->remove_html( $this->remove_dbl_spaces( $this->convert_html( $a['content'] ) ) );
					}
				}
				else
				{
					if ( $this->core->cache['config']['news_excerpt_trim'] )
					{
						$a['excerpt'] = $this->shorten_str( $a['content'], $this->core->cache['config']['news_excerpt_trim'], 1 );
					}
					else
					{
						$a['excerpt'] = $a['content'];
					}
				}
			}

			unset( $a['content'] );

			$to_cache[ $a['id'] ] = $a;
		}

		$this->core->add_cache( 'announce', $to_cache, 1 );
    }

	#=======================================
	# @ Rebuild Languages Cache
	# Rebuilds the languages information
	# from the database.
	#=======================================

	function rebuild_lang_cache()
    {
    	$this->core->db->construct( array(
									  	  'select'		=> 'all',
										  'from'		=> 'languages',
							 	   )	 );

		$this->core->db->execute();

		while ( $l = $this->core->db->fetch_row() )
		{
			$to_cache[ $l['lkey'] ] = $l;

			if ( $l['default'] )
			{
				$to_cache['default'] = $l['lkey'];
			}
		}

		$this->core->add_cache( 'lang', $to_cache, 1 );
    }

	#=======================================
	# @ Rebuild Skin Cache
	# Rebuilds the skin sets information
	# from the database.
	#=======================================

	function rebuild_skin_cache()
    {
    	$this->core->db->construct( array(
									  	  'select'		=> 'all',
										  'from'		=> 'skins',
							 	   )	 );

		$this->core->db->execute();

		while ( $s = $this->core->db->fetch_row() )
		{
			$to_cache[ $s['id'] ] = $s;

			if ( $s['default'] )
			{
				$to_cache['default'] = $s['id'];
			}
		}

		$this->core->add_cache( 'skin', $to_cache, 1 );
    }

	#=======================================
	# @ Rebuild Profile Fields Cache
	# Rebuilds the profile fields information
	# from the database.
	#=======================================

	function rebuild_pfields_cache()
    {
    	$this->core->db->construct( array(
									  	  'select'		=> 'all',
										  'from'		=> 'profile_fields',
							 	   )	 );

		$this->core->db->execute();

		while ( $f = $this->core->db->fetch_row() )
		{
			$to_cache[ $f['id'] ] = $f;
		}

		$this->core->add_cache( 'pfields', $to_cache, 1 );
    }

	#=======================================
	# @ Rebuild Deparmtnet Fields Cache
	# Rebuilds the department fields
	# information from the database.
	#=======================================

	function rebuild_dfields_cache()
    {
    	$this->core->db->construct( array(
									  	  'select'		=> 'all',
										  'from'		=> 'depart_fields',
							 	   )	 );

		$this->core->db->execute();

		while ( $f = $this->core->db->fetch_row() )
		{
			$to_cache[ $f['id'] ] = $f;
		}

		$this->core->add_cache( 'dfields', $to_cache, 1 );
    }

	#=======================================
	# @ Rebuild Canned Cache
	# Rebuilds the canned replies
	# information from the database.
	#=======================================

	function rebuild_canned_cache()
    {
    	$this->core->db->construct( array(
									  	  'select'		=> array( 'id', 'name', 'description' ),
										  'from'		=> 'canned',
							 	   )	 );

		$this->core->db->execute();

		while ( $c = $this->core->db->fetch_row() )
		{
			$to_cache[ $c['id'] ] = $c;
		}

		$this->core->add_cache( 'canned', $to_cache, 1 );
    }

	#=======================================
	# @ Rebuild Staff Cache
	# Rebuilds the staff members
	# information from the database.
	#=======================================

	function rebuild_staff_cache()
    {
    	$this->core->db->construct( array(
									  	  'select'		=> array( 'm' => array( 'id', 'name', 'assigned' ), 'g' => array( 'g_depart_perm' ) ),
										  'from'		=> array( 'm' => 'members' ),
										  'join'		=> array( array( 'from' => array( 'g' => 'groups' ), 'where' => array( 'm' => 'mgroup', '=', 'g' => 'g_id' ) ) ),
										  'where'		=> array( array( 'g' => 'g_acp_access' ), '=', 1 ),
										  'order'		=> array( 'name' => array( 'm' => 'desc' ) ),
							 	   )	 );

		$this->core->db->execute();

		while ( $s = $this->core->db->fetch_row() )
		{
			$to_cache[ $s['id'] ] = $s;
		}

		$this->core->add_cache( 'staff', $to_cache, 1 );
    }

	#=======================================
	# @ Build Department Drop-Down
	# Builds a department drop-down list
	# from the core cache.
	#=======================================

	function build_dprt_drop($select="", $exclude=0, $admin=0, $type=1)
    {
    	# DO SUB DEPARTMENTS!

    	$html = ""; // Initialize for Security

    	$include = unserialize( $this->member['g_m_depart_perm'] );

    	foreach( $this->core->cache['depart'] as $id => $d )
    	{
    		if ( $id != $exclude )
    		{
    			if ( $include[ $id ] )
    			{
    				$do = 1;
    			}
    			elseif ( $admin )
    			{
    				$do = 1;
    			}

    			if ( $do )
    			{
    				if ( $type == 2 )
				    {
				    	$html .= "<tr><td width='1%'><input type='radio' name='department' id='d_". $id ."' value='". $id ."' class='radio' /></td><td width='99%'><label for='d_". $id ."'>". $d['name'] ."</label><br /><span class='descb'>". $d['description'] ."</span></td></tr>";
				    }
				    else
				    {
			    		if ( is_array( $select ) )
			    		{
			    			if ( $select[ $id ] )
					    	{
					    		$html .= "<option value='". $id ."' selected='selected'>". $d['name'] ."</option>";
					    	}
					    	else
					    	{
					    		$html .= "<option value='". $id ."'>". $d['name'] ."</option>";
					    	}
			    		}
			    		else
			    		{
				    		if ( $id == $select )
					    	{
					    		$html .= "<option value='". $id ."' selected='selected'>". $d['name'] ."</option>";
					    	}
					    	else
					    	{
					    		$html .= "<option value='". $id ."'>". $d['name'] ."</option>";
					    	}
			    		}
				    }
    			}
    		}

    		$do = 0; // Reset
    	}

    	return $html;
    }

	#=======================================
	# @ Build Category Drop-Down
	# Builds a category drop-down list
	# from the core cache.
	#=======================================

	function build_cat_drop($select=0, $exclude=0)
    {
    	# DO SUB CATEGORIES!

    	$html = ""; // Initialize for Security

    	while ( list( $id, $c ) = each( $this->core->cache['kbcat'] ) )
    	{
    		if ( $id != $exclude )
    		{
	    		if ( $id == $select )
	    		{
	    			$html .= "<option value='". $id ."' selected='selected'>". $c['name'] ."</option>";
	    		}
	    		else
	    		{
	    			$html .= "<option value='". $id ."'>". $c['name'] ."</option>";
	    		}
    		}
    	}

    	return $html;
    }

	#=======================================
	# @ Build Group Drop-Down
	# Builds a member group drop-down list
	# from the core cache.
	#=======================================

	function build_group_drop($select="", $exclude=0, $admin=0)
    {
    	$html = ""; // Initialize for Security

    	if ( is_array( $select ) )
    	{
    		$temp_select = $select;
    		$select = "";

    		while ( list( , $sid ) = each( $temp_select ) )
    		{
    			$select[ $sid ] = 1;
    		}

    		unset( $temp_select );
    	}
    	
    	if ( ! $admin && $this->member['acp']['manage_member_staff'] ) $admin = 1;

    	while ( list( $id, $g ) = each( $this->core->cache['group'] ) )
    	{
    		if ( $id != $exclude )
    		{
    			if ( $g['g_acp_access'] && ! $admin ) continue;
    			
    			if ( is_array( $select ) )
    			{
    				if ( $select[ $id ] )
    				{
    					$html .= "<option value='". $id ."' selected='selected'>". $g['g_name'] ."</option>";
    				}
		    		else
		    		{
		    			$html .= "<option value='". $id ."'>". $g['g_name'] ."</option>";
		    		}
    			}
    			else
    			{
		    		if ( $id == $select )
		    		{
		    			$html .= "<option value='". $id ."' selected='selected'>". $g['g_name'] ."</option>";
		    		}
		    		else
		    		{
		    			$html .= "<option value='". $id ."'>". $g['g_name'] ."</option>";
		    		}
    			}
    		}
    	}

    	return $html;
    }

	#=======================================
	# @ Build Staff Drop-Down
	# Builds a staff drop-down list
	# from the core cache.
	#=======================================

	function build_staff_drop($select=0)
    {
    	# DO SUB CATEGORIES!

    	$html = ""; // Initialize for Security

    	while ( list( $id, $s ) = each( $this->core->cache['staff'] ) )
    	{
    		if ( $id == $select )
	    	{
	    		$html .= "<option value='". $id ."' selected='selected'>". $s['name'] ."</option>";
	    	}
	    	else
	    	{
	    		$html .= "<option value='". $id ."'>". $s['name'] ."</option>";
	    	}
    	}

    	return $html;
    }

	#=======================================
	# @ Build Priority Drop-Down
	# Builds a priority drop-down list
	# from the core cache.
	#=======================================

	function build_priority_drop($select=0)
    {
    	# SELECT FROM DB IN FUTURE!

    	$priority[] = 1;
    	$priority[] = 2;
    	$priority[] = 3;
    	$priority[] = 4;

    	$html = ""; // Initialize for Security

    	while ( list( , $id ) = each( $priority ) )
    	{
    		if ( $id == $select )
    		{
    			$html .= "<option value='". $id ."' selected='selected'>". $this->lang[ 'priority_'. $id ] ."</option>";
    		}
    		else
    		{
    			$html .= "<option value='". $id ."'>". $this->lang[ 'priority_'. $id ] ."</option>";
    		}
    	}

    	return $html;
    }

	#=======================================
	# @ Build Time Zone Drop-Down
	# Builds a time zone drop-down list.
	#=======================================

	function build_time_zone_drop($select=0)
    {
    	$zone[-12] = $this->lang['gmt_n_1200'];
    	$zone[-11] = $this->lang['gmt_n_1100'];
    	$zone[-10] = $this->lang['gmt_n_1000'];
    	$zone[-9] = $this->lang['gmt_n_900'];
    	$zone[-8] = $this->lang['gmt_n_800'];
    	$zone[-7] = $this->lang['gmt_n_700'];
    	$zone[-6] = $this->lang['gmt_n_600'];
    	$zone[-5] = $this->lang['gmt_n_500'];
    	$zone[-4] = $this->lang['gmt_n_400'];
    	$zone[-3.5] = $this->lang['gmt_n_350'];
    	$zone[-3] = $this->lang['gmt_n_300'];
    	$zone[-2] = $this->lang['gmt_n_200'];
    	$zone[-1] = $this->lang['gmt_n_100'];
    	$zone[0] = $this->lang['gmt'];
    	$zone[1] = $this->lang['gmt_p_100'];
    	$zone[2] = $this->lang['gmt_p_200'];
    	$zone[3] = $this->lang['gmt_p_300'];
    	$zone[3.5] = $this->lang['gmt_p_350'];
    	$zone[4] = $this->lang['gmt_p_400'];
    	$zone[4.5] = $this->lang['gmt_p_450'];
    	$zone[5] = $this->lang['gmt_p_500'];
    	$zone[5.5] = $this->lang['gmt_p_550'];
    	$zone[6] = $this->lang['gmt_p_600'];
    	$zone[7] = $this->lang['gmt_p_700'];
    	$zone[8] = $this->lang['gmt_p_800'];
    	$zone[9] = $this->lang['gmt_p_900'];
    	$zone[9.5] = $this->lang['gmt_p_950'];
    	$zone[10] = $this->lang['gmt_p_1000'];
    	$zone[11] = $this->lang['gmt_p_1100'];
    	$zone[12] = $this->lang['gmt_p_1200'];

    	$html = ""; // Initialize for Security

    	while ( list( $id, $value ) = each( $zone ) )
    	{
    		if ( $id == $select )
    		{
    			$html .= "<option value='". $id ."' selected='selected'>{$value}</option>";
    		}
    		else
    		{
    			$html .= "<option value='". $id ."'>{$value}</option>";
    		}
    	}

    	return $html;
    }

	#=======================================
	# @ Build Language Drop
	# Builds a language group drop-down list
	# from the core cache.
	#=======================================

	function build_lang_drop($select=0)
    {
    	$html = ""; // Initialize for Security

    	while ( list( $id, $l ) = each( $this->core->cache['lang'] ) )
    	{
    		if ( $id != 'default' )
    		{
		    	if ( $id == $select )
		    	{
		    		$html .= "<option value='". $id ."' selected='selected'>". $l['name'] ."</option>";
		    	}
		    	else
		    	{
		    		$html .= "<option value='". $id ."'>". $l['name'] ."</option>";
	    		}
    		}
    	}

    	return $html;
    }

	#=======================================
	# @ Build Skin Drop
	# Builds a skin group drop-down list
	# from the core cache.
	#=======================================

	function build_skin_drop($select=0)
    {
    	$html = ""; // Initialize for Security

    	while ( list( $id, $s ) = each( $this->core->cache['skin'] ) )
    	{
    		if ( $id != 'default' )
    		{
		    	if ( $id == $select )
		    	{
		    		$html .= "<option value='". $id ."' selected='selected'>". $s['name'] ."</option>";
		    	}
		    	else
		    	{
		    		$html .= "<option value='". $id ."'>". $s['name'] ."</option>";
	    		}
    		}
    	}

    	return $html;
    }

	#=======================================
	# @ Check Fields
	# Checks input fields for the required
	# length defined in array.
	#=======================================

	function check_fields($required)
	{
		while ( list( $req, $length ) = each( $required ) )
		{
			if ( $length )
			{
				if ( strlen( $this->input[ $req ] ) < $length )
				{
					$this->skin->error('fill_form_lengths');
				}
			}
			else
			{
				if ( ! $this->input[ $req ] )
				{
					$this->ifthd->skin->error('fill_form_completely');
				}
			}
		}
	}

	#=======================================
	# @ A5 Date
	# Applies format and offsets to date.
	#=======================================

	function ift_date($time, $format='', $relative=0, $rev=0, $boring_time=0, $offset=0, $no_mem_offset=0)
	{
		$return = ""; // Initialize for Security
		$m_offset = 0;
		$h_offset = 0;
		$min_offset = 0;

		#=============================
		# Apply User Time Zone
		#=============================

		if ( ! $no_mem_offset )
		{
			if ( $this->member['time_zone'] )
			{
				$m_offset = $this->member['time_zone'] * 60 * 60;
			}

			if ( $this->member['dst_active'] )
			{
				$m_offset = $m_offset + ( 60 * 60 );
			}
		}

		if ( $this->core->cache['config']['hour_offset'] )
		{
			$h_offset = $this->core->cache['config']['hour_offset'] * 60 * 60;
		}

		if ( $this->core->cache['config']['minute_offset'] )
		{
			$min_offset = $this->core->cache['config']['minute_offset'] * 60;
		}

		$t_offset = $m_offset + $h_offset + $min_offset + $offset;

		if ( $rev )
		{
			$time = ( $time - $t_offset );
		}
		else
		{
			$time = ( $time + $t_offset );
		}

		#=============================
		# Relative Time?
		#=============================

		$g_relative = 1; // TO BE COMPLETED
		$g_exact_rel = 1; // TO BE COMPLETED

		if ( $g_relative )
		{
			$aminutes = ""; // Initialize for Security
			$ahours = ""; // Initialize for Security
			$adays = ""; // Initialize for Security
			$aweeks = ""; // Initialize for Security

			$time_diff = ( time() + $t_offset ) - $time;

			if ( $time_diff < 3600 )
			{
				if ( $time_diff < 120 )
				{
					$aminutes = "-1";
				}
				else
				{
					$aminutes = intval( $time_diff / 60 );
				}
			}
			elseif ( $time_diff < 86400 )
			{
				$ahours = intval( $time_diff / 3600 );
			}
			elseif ( $time_diff < 604800 )
			{
				$adays = intval( $time_diff / 86400 );
			}
			#elseif ( $time_diff < 3024000 )
			elseif ( $time_diff < 2419200 )
			{
				$aweeks = intval( $time_diff / 604900 );
			}

			if ( $relative )
			{
				if ( $aminutes == "-1" )
				{
					$return = $this->lang['less_than_a_minute_ago'];
				}
				elseif ( $aminutes )
				{
					$return = $aminutes .' '. $this->lang['minutes_ago'];
				}
				elseif ( $ahours )
				{
					$return = $ahours .' '. $this->lang['hours_ago'];
				}
				elseif ( $adays )
				{
					$return = $adays .' '. $this->lang['days_ago'];
				}
				elseif ( $aweeks )
				{
					$return = $aweeks .' '. $this->lang['weeks_ago'];
				}
				else
				{
					$return = "&nbsp;";
				}
			}
			else
			{
				$today = gmdate( 'd,m,Y', ( time() + $t_offset ) );
				$yesterday = gmdate('d,m,Y', ( time() + $t_offset - 86400 ) );

				$human_time = gmdate( 'd,m,Y', $time );

			    if ( $today == $human_time )
			    {
			        $return = $this->lang['today'];
			        $today_yesterday = 1;
			    }
			    else if ( $yesterday == $human_time )
			    {
			        $return = $this->lang['yesterday'];
			        $today_yesterday = 1;
			    }
			    else
			    {
			    	$boring_time = 1;
			    }

		    	$return .= ', ';

		    	$return .= gmdate( 'g:i A', $time ); // TO BE COMPLETED
			}
		}
		else
		{
			$boring_time = 1;
		}

		if ( $boring_time )
		{
			#=============================
			# Do We Have A Format?
			#=============================

			if ( ! $format )
			{
				$format = 'M j Y, g:i A'; // TO BE COMPLETED
			}

			$return = gmdate( $format, $time );
		}

		return $return;
	}

	#=======================================
	# @ Send Email
	# Sends an email.
	#=======================================

	function send_email($recipient, $message, $replacements='', $extra='', $reply_line=0)
	{
		if ( ! is_array( $recipient ) )
		{
			$old_recp = $recipient;
			unset( $recipient );

			$recipient[] = $old_recp;
		}

		$this->core->load_module('email');
		
		if ( $this->core->cache['config']['email_method'] == 'native' )
		{
			$email_int = array( 'method' => 'native' );
		}
		elseif ( $this->core->cache['config']['email_method'] == 'smtp' )
		{
			$email_int = array( 'method' => 'smtp', 'smtp_host' => $this->core->cache['config']['smtp_host'], 'smtp_port' => $this->core->cache['config']['smtp_port'], 'smtp_user' => $this->core->cache['config']['smtp_user'], 'smtp_pass' => $this->convert_html( $this->core->cache['config']['smtp_pass'] ), 'smtp_encrypt' => $this->core->cache['config']['smtp_encryption'] );
		}
		
		$this->core->email->initialize( $email_int );

		$langid = $this->member['lang'];
		if ( ! $langid ) $langid = $this->core->cache['lang']['default'];

		require HD_PATH. "language/". $langid ."/lang_email_content.php";

		while ( list( , $member_id ) = each( $recipient ) )
		{
			$this->core->db->construct( array(
										  	  'select'	=> array( 'id', 'name', 'email', 'email_html' ),
										  	  'from'	=> 'members',
										  	  'where'	=> array( 'id', '=', $member_id ),
										  	  'limit'	=> array( 0, 1 ),
								  	   ) 	 );

			$this->core->db->execute();

			if ( $this->core->db->get_num_rows() == 1 )
			{
				$mem = $this->core->db->fetch_row();

				if ( $extra['over_email'] )
				{
					$this->core->email->add_recipient( $extra['over_email'] );
					
					$replacements['MEM_EMAIL'] = $extra['over_email'];
				}
				else
				{
					$this->core->email->add_recipient( $mem['email'] );
					
					$replacements['MEM_EMAIL'] = $mem['email'];
				}

				$subject = $lang[ $message ."_sub" ];

				$replacements['MEM_NAME'] = $mem['name'];
				$replacements['MEM_ID'] = $mem['id'];
				$replacements['HD_NAME'] = $this->core->cache['config']['hd_name'];
				$replacements['HD_URL'] = $this->core->cache['config']['hd_url'];

				$email_msg = $lang['header'] ."\n\n" . $lang[ $message ] ."\n\n" . $lang['footer'];
				
				if ( $reply_line && $this->core->cache['config']['email_use_rline'] )
				{
					$email_msg = $this->core->cache['config']['email_reply_line'] ."\n\n". $email_msg;
				}

				foreach( $replacements as $search => $replace )
				{
					if ( $mem['email_html'] )
					{
						$replaceb = $replace;
					}
					else
					{
						$replaceb = preg_replace( "/<p>(.+?)<\/p>/", "$1\n\n", $this->convert_html( $replace ) );
						$replaceb = str_replace( '<br />', "\n", $replaceb );
					}

					$email_msg = str_replace( "<#". $search ."#>", $replaceb, $email_msg );
					$subject = str_replace( "<#". $search ."#>", $replace, $subject );
				}

				foreach ( $this->lang as $langkey => $langvalue )
				{
					$email_msg = str_replace("{lang.". $langkey ."}", $langvalue, $email_msg);
				}

				$config = array(
								'from_email'		=> $this->core->cache['config']['out_email'],
								'from_name'		=> $this->convert_html( $this->core->cache['config']['hd_name'] ),
								);

				if ( $extra['from_email'] )
				{
					$config['from_email'] = $extra['from_email'];
				}

				$this->core->email->update_config( $config );
			
				$this->core->email->set_subject( html_entity_decode( $subject, ENT_QUOTES, 'UTF-8' ) );
				
				if ( $mem['email_html'] )
				{
					$this->core->email->add_message( html_entity_decode( $this->convert_html( str_replace( '&nbsp;', " ", $email_msg ) ), ENT_QUOTES, 'UTF-8' ) );
				
					$this->core->email->add_message( html_entity_decode( $this->convert_html( nl2br( str_replace( '&nbsp;', " ", $email_msg ) ) ), ENT_QUOTES, 'UTF-8' ), 'text/html' );
				}
				else
				{
					$this->core->email->add_message( html_entity_decode( $this->convert_html( str_replace( '&nbsp;', " ", $email_msg ) ), ENT_QUOTES, 'UTF-8' ) );
				}

				$this->core->email->send_email();
				
				$this->core->email->flush();
			}
		}
    }

	#=======================================
	# @ Send Guest Email
	# Sends an email to a guest.
	#=======================================

	function send_guest_email($recipient, $message, $replacements='', $extra='', $reply_line=0)
	{
		if ( ! is_array( $recipient ) )
		{
			$old_recp = $recipient;
			unset( $recipient );

			$recipient[] = $old_recp;
		}

		$this->core->load_module('email');
		
		if ( $this->core->cache['config']['email_method'] == 'native' )
		{
			$email_int = array( 'method' => 'native' );
		}
		elseif ( $this->core->cache['config']['email_method'] == 'smtp' )
		{
			$email_int = array( 'method' => 'smtp', 'smtp_host' => $this->core->cache['config']['smtp_host'], 'smtp_port' => $this->core->cache['config']['smtp_port'], 'smtp_user' => $this->core->cache['config']['smtp_user'], 'smtp_pass' => $this->convert_html( $this->core->cache['config']['smtp_pass'] ), 'smtp_encrypt' => $this->core->cache['config']['smtp_encryption'] );
		}
		
		$this->core->email->initialize( $email_int );

		$langid = $this->member['lang'];
		if ( ! $langid ) $langid = $this->core->cache['lang']['default'];

		require HD_PATH. "language/". $langid ."/lang_email_content.php";

		while ( list( , $da_email ) = each( $recipient ) )
		{
			$this->core->email->add_recipient( $da_email );

			$subject = $lang[ $message ."_sub" ];

			$replacements['MEM_EMAIL'] = $da_email;
			$replacements['HD_NAME'] = $this->core->cache['config']['hd_name'];
			$replacements['HD_URL'] = $this->core->cache['config']['hd_url'];

			$email_msg = $lang['header'] ."\n\n" . $lang[ $message ] ."\n\n" . $lang['footer'];
				
			if ( $reply_line && $this->core->cache['config']['email_use_rline'] )
			{
				$email_msg = $this->core->cache['config']['email_reply_line'] ."\n\n" . $email_msg;
			}

			foreach( $replacements as $search => $replace )
			{
				$replaceb = preg_replace( "/<p>(.+?)<\/p>/", "$1\n\n", $this->convert_html( $replace ) );
				$replaceb = str_replace( '<br />', "\n", $replaceb );

				$email_msg = str_replace( "<#". $search ."#>", $replaceb, $email_msg );
				$subject = str_replace( "<#". $search ."#>", $replace, $subject );
			}

			foreach ( $this->lang as $langkey => $langvalue )
			{
				$email_msg = str_replace("{lang.". $langkey ."}", $langvalue, $email_msg);
			}

			$config = array(
							'from_email'		=> $this->core->cache['config']['out_email'],
							'from_name'		=> $this->convert_html( $this->core->cache['config']['hd_name'] ),
							);

			if ( $extra['from_email'] )
			{
				$config['from_email'] = $extra['from_email'];
			}

			$this->core->email->update_config( $config );
		
			$this->core->email->set_subject( html_entity_decode( $subject, ENT_QUOTES, 'UTF-8' ) );
		
			$this->core->email->add_message( html_entity_decode( $this->convert_html( str_replace( '&nbsp;', " ", $email_msg ) ), ENT_QUOTES, 'UTF-8' ) );
			
			$this->core->email->add_message( html_entity_decode( $this->convert_html( nl2br( str_replace( '&nbsp;', " ", $email_msg ) ) ), ENT_QUOTES, 'UTF-8' ), 'text/html' );

			$this->core->email->send_email();
			
			$this->core->email->flush();
		}
    }

	#=======================================
	# @ Set Cookie
	# Sets a cookie. :P
	#=======================================

	function set_cookie($name, $value, $time='')
	{
		if ( ! $time )
        {
        	$time = time() + 60*60*24*365; // Sec*Min*Hrs*Days
        }

        if ( $this->core->cache['config']['cookie_prefix'] ) $name = $this->core->cache['config']['cookie_prefix'] . $name;

        @setcookie( $name, $value, $time, $this->core->cache['config']['cookie_path'], $this->core->cache['config']['cookie_domain'] );
    }

	#=======================================
	# @ Get Cookie
	# Safely gets a cookie.
	#=======================================

	function get_cookie($name)
	{
		$cookie_data = ""; // Initialize for Security

		if ( $this->core->cache['config']['cookie_prefix'] ) $name = $this->core->cache['config']['cookie_prefix'] . $name;

		if ( isset( $_COOKIE[$name] ) )
		{
			$cookie_data = $this->sanitize_data( $_COOKIE[$name] );
		}

		return $cookie_data;
    }

	#=======================================
	# @ Delete Cookie
	# Deletes a cookie. :P
	#=======================================

	function delete_cookie($name)
	{
		if ( $this->core->cache['config']['cookie_prefix'] ) $name = $this->core->cache['config']['cookie_prefix'] . $name;

        @setcookie( $name, 0, time() - ( 60*60*24*365 ), $this->core->cache['config']['cookie_path'], $this->core->cache['config']['cookie_domain'] );
    }

	#=======================================
	# @ Validate Email
	# Checks to make sure the supplied email
	# address is valid.
	#=======================================

	function validate_email($email)
	{
		if( ereg( "^([0-9,a-z,A-Z]+)([.,_,-]([0-9,a-z,A-Z]+))*[@]([0-9,a-z,A-Z]+)([.,_,-]([0-9,a-z,A-Z]+))*[.]([0-9,a-z,A-Z]){2}([0-9,a-z,A-Z])*$", $email ) )
		{
			return $email;
		}
		else
		{
			return FALSE;
		}
	}

	#=======================================
	# @ Shorten String
	# Takes a string and shortens it to the
	# specified length.
	#=======================================

	function shorten_str($txt, $length, $add=1)
	{
		if ( strlen( $txt ) > $length )
		{
			if ( $add == 1 )
			{
				$txt = substr( $txt, 0, ( $length - 3) ) . "...";
				$txt = preg_replace( "/&(#(\d+;?)?)?\.\.\.$/", "...", $txt );
			}
			else
			{
				$txt = substr( $txt, 0, $length );
			}
		}

		return $txt;
	}

	#=======================================
	# @ Convert BBCode
	# Converts BBCode tags in incoming
	# string. (Parser).
	#=======================================

	function convert_bbcode($txt)
	{
		$txt = preg_replace('/\[b\](.+?)\[\/b\]/ims','<b>$1</b>',$txt);
		$txt = preg_replace('/\[i\](.+?)\[\/i\]/ims','<i>$1</i>',$txt);
		$txt = preg_replace('/\[u\](.+?)\[\/u\]/ims','<u>$1</u>',$txt);
		$txt = preg_replace('/\[sup\](.+?)\[\/sup\]/ims','<sup>$1</sup>',$txt);
		$txt = preg_replace('/\[sub\](.+?)\[\/sub\]/ims','<sub>$1</sub>',$txt);
		$txt = preg_replace('/\[s\](.+?)\[\/s\]/ims','<strike>$1</strike>',$txt);
		$txt = preg_replace('/\[center\](.+?)\[\/center\]/ims','<center>$1</center>',$txt);
		$txt = preg_replace('/\[url=(.+?)\](.+?)\[\/url\]/ims','<a href="$1">$2</a>',$txt);
		$txt = preg_replace('/\[img\](.+?)\[\/img\]/ims','<img src="$1" alt="userpostedimage" />',$txt);
		$txt = preg_replace('/\[email=(.+?)\](.+?)\[\/email\]/ims','<a href="mailto:$1">$2</a>',$txt);
		$txt = preg_replace('/\[color=(.+?)\](.+?)\[\/color\]/ims','<font color="$1">$2</font color>',$txt);
		$txt = preg_replace('/\[font=(.+?)\](.+?)\[\/font\]/ims','<font face="$1">$2</font face>',$txt);
		$txt = preg_replace('/\[size=(.+?)\](.+?)\[\/size\]/ims','<font size="$1">$2</font size>',$txt);

		return $txt;
	}

	#=======================================
	# @ HTML Safe
	# Converts HTML tags.
	#=======================================

	function html_safe($txt)
	{
		$txt = str_replace( '\'', '&#039;', $txt );
		$txt = str_replace( '\'', '&#39;', $txt );
		$txt = str_replace( '"', '&quot;', $txt );
		$txt = str_replace( '<', '&lt;', $txt );
		$txt = str_replace( '>', '&gt;', $txt );
		$txt = str_replace( '(', '&#40;', $txt );
    	$txt = str_replace( ')', '&#41;', $txt );

		return $txt;
	}

	#=======================================
	# @ Convert HTML
	# Converts HTML tags.
	#=======================================

	function convert_html($txt, $strip=0, $entity=1)
	{
		$txt = str_replace( '&amp;', '&', $txt );
		$txt = str_replace( '&#039;', '\'', $txt );
		$txt = str_replace( '&#39;', '\'', $txt );
		$txt = str_replace( '&quot;', '"', $txt );
		$txt = str_replace( '&lt;', '<', $txt );
		$txt = str_replace( '&gt;', '>', $txt );
		$txt = str_replace( '&#40;', '(', $txt );
    	$txt = str_replace( '&#41;', ')', $txt );

		if ( $strip )
		{
			$txt = stripslashes($txt);
		}

		return $txt;
	}

	#=======================================
	# @ Remove Extra Line Breaks
	# Removes extra <br />'s due to the RTE.
	#=======================================

	function remove_extra_lbs($txt)
	{
		$txt = preg_replace( '#(&lt;br /&gt;)*$#', "", $txt );

		return $txt;
	}

	#=======================================
	# @ Remove HTML
	# Removes HTML tags.
	#=======================================

	function remove_html($txt, $strip=0)
	{
		$txt = str_replace( '<br />', "\n", $txt );
		$txt = str_replace( '<br>', "\n", $txt );

		$txt = strip_tags($txt);

		return $txt;
	}

	#=======================================
	# @ Remove HTML Sanitized
	# Removes HTML tags.
	#=======================================

	function remove_html_s($txt, $strip=0)
	{
		$txt = $this->convert_html( $txt );
		$txt = html_entity_decode( $txt, ENT_QUOTES, 'UTF-8' );
		
		$txt = preg_replace( '/\<br(\s*)?\/?\>/i', "\n", $txt );

		$txt = strip_tags($txt);

		return $this->sanitize_data( $txt );
	}

	#=======================================
	# @ Convert Lang
	# Converts Language Tags tags.
	#=======================================

	function convert_lang($txt, $type=1)
	{
		if ( $type == 1 )
		{
			$txt = str_replace( '{', '&#123;', $txt );
			$txt = str_replace( '}', '&#125;', $txt );
		}
		else
		{
			$txt = str_replace( '&#123;', '{', $txt );
			$txt = str_replace( '&#125;', '}', $txt );
		}

		return $txt;
	}

	#=======================================
	# @ Prepare Output
	# Prepares incoming text for output to
	# the user. (Parser).
	#=======================================

	function prepare_output($txt, $bbcode=0, $html=0, $urls=0, $nonl2br=0)
	{
		if ( $bbcode )
		{
			$txt = $this->convert_bbcode($txt);
		}

		if ( $html )
		{
			$txt = $this->convert_html($txt);
		}

		/*if ( $urls )
		{
			$txt = preg_replace( "/[^href=('|\")](http:\/\/|ftp:\/\/)([^\s,<>\(\)]*)/i", "<a href='$1$2' target='_blank'>$1$2</a>", $txt );
		}*/

		if ( ! $nonl2br ) $txt = nl2br( $txt );

		return $txt;
	}

	#=======================================
	# @ Remove Double Spaces
	# Removes double spaces from TinyMCE.
	#=======================================

	function remove_dbl_spaces($txt)
	{
		$txt = str_replace( '&nbsp; ', " ", $txt );

		return $txt;
	}

	#=======================================
	# @ Get Priority
	# Returns priority name / level.
	#=======================================

	function get_priority($level)
	{
		if ( $level == 1 )
		{
			$return = $this->lang['priority_1'];
		}
		elseif ( $level == 2 )
		{
			$return = $this->lang['priority_2'];
		}
		elseif ( $level == 3 )
		{
			$return = $this->lang['priority_3'];
		}
		elseif ( $level == 4 )
		{
			$return = $this->lang['priority_4'];
		}

		return $return;
	}

	#=======================================
	# @ Get Status
	# Returns status name.
	#=======================================

	function get_status($status, $acp=0)
	{
		if ( $status == 1 )
		{
			$new_status = $this->lang['open'];
		}
		elseif ( $status == 2 )
		{
			$new_status = $this->lang['in_progress'];
		}
		elseif ( $status == 3 )
		{
			$new_status = $this->lang['on_hold'];
		}
		elseif ( $status == 4 )
		{
			if ( $acp == 1 )
			{
				$new_status = $this->lang['aca'];
			}
			elseif ( $acp == 2 )
			{
				$new_status = $this->lang['aca_full'];
			}
			else
			{
				$new_status = $this->lang['open'];
			}
		}
		elseif ( $status == 5 )
		{
			$new_status = $this->lang['escalated'];
		}
		elseif ( $status == 6 )
		{
			$new_status = $this->lang['closed'];
		}

		return $new_status;
	}

	#=======================================
	# @ Get Open Tickets
	# Gets the latest open tickets for the
	# user (sidebar).
	#=======================================

	function get_open_tickets()
	{
		$this->core->db->construct( array(
									  	  'select'	=> array( 'id', 'subject', 'status' ),
										  'from'	=> 'tickets',
										  'where'	=> array( array( 'mid', '=', $this->member['id'] ), array( 'status', '!=', 6, 'and' ) ),
							 			  'order'	=> array( 'date' => 'desc' ),
							 	   )	 );

		$this->core->db->execute();

		if ( $this->core->db->get_num_rows() )
		{
			while ( $t = $this->core->db->fetch_row() )
			{
				if ( $t['status'] == 4 )
				{
					$t['subject'] = '<b>'. $t['subject'] .'</b>';
				}

				$tickets[] = $t;
			}
		}
		else
		{
			return FALSE;
		}

		return $tickets;
	}

	#=======================================
	# @ Get Open Guest Tickets
	# Gets the latest open tickets for the
	# guest (sidebar).
	#=======================================

	function get_open_guest_tickets()
	{
		$this->core->db->construct( array(
									  	  'select'	=> 'all',
										  'from'	=> 'tickets',
										  'where'	=> array( array( 'email', '=', $this->member['s_email'] ), array( 'guest', '=', 1, 'and' ), array( 'status', '!=', 6, 'and' ) ),
							 			  'order'	=> array( 'date' => 'desc' ),
							 	   )	 );

		$this->core->db->execute();

		if ( $this->core->db->get_num_rows() )
		{
			while ( $t = $this->core->db->fetch_row() )
			{
				if ( $t['status'] == 4 )
				{
					$t['subject'] = '<b>'. $t['subject'] .'</b>';
				}

				$tickets[] = $t;
			}
		}
		else
		{
			return FALSE;
		}

		return $tickets;
	}

	#=======================================
	# @ Log
	# Log an action into the database.
	#=======================================

	function log($type, $action, $level=1, $extra='')
	{
		if ( $type == 'mod' )
		{
			$type_id = 1;
		}
		elseif ( $type == 'admin' )
		{
			$type_id = 2;
		}
		elseif ( $type == 'error' )
		{
			$type_id = 3;
		}
		elseif ( $type == 'security' )
		{
			$type_id = 4;
		}
		elseif ( $type == 'email' )
		{
			$type_id = 5;
		}
		elseif ( $type == 'member' )
		{
			$type_id = 6;
		}
		elseif ( $type == 'ticket' )
		{
			$type_id = 7;
		}
		else
		{
			$type_id = 9;
		}

		$db_array = array(
						  'mid'		=> $this->member['id'],
						  'mname'	=> $this->member['name'],
						  'action'	=> $action,
						  'extra'	=> $extra,
						  'type'	=> $type_id,
						  'level'	=> $level,
						  'date'	=> time(),
						  'ipadd'	=> $this->input['ip_address'],
						 );

		$this->core->db->construct( array(
									  	  'insert'	=> 'logs',
										  'set'		=> $db_array,
							 		) 	 );

		$this->core->db->next_shutdown();
		$this->core->db->execute();
	}

	#=======================================
	# @ Page Links
	# Generates pagination links. :)
	#=======================================

	function page_links($url, $total, $per_page, $start='', $admin='')
	{
		if ( ! $start )
		{
			$start = $this->input['st'];
		}

		if ( $total > $per_page )
		{
			$num_pages = ceil( $total / $per_page );
		}
		else
		{
   			$num_pages = 1;
		}

		$html = ""; // Initialize for Security

		if ( $num_pages > 1 )
		{
			$current_page = ( $start / $per_page ) +1;

			if ( $num_pages > 5 )
			{
				$over = 1;
			}

			// Display First Page Link
			if ( $current_page > 3 && $num_pages > 5 )
			{
				$html .= "<span class='plinkj'><a href='<! HD_URL !>/index.php{$url}&amp;st=0'>&laquo;</a></span>";
			}

			// Display Previous Page Link
			if ( $current_page != 1 )
			{
				$html .= "<span class='plink'><a href='<! HD_URL !>/index.php{$url}&amp;st=". ( $start - $per_page ) ."'>&lsaquo;</a></span>";
			}

			// Show Page Numbers
			for ( $i = 1; $i <= $num_pages; $i++ )
			{
				if ( $i != $current_page )
				{
					if ( $over )
					{
						if ( $i >= ( $current_page - 2 ) && $i <= ( $current_page + 2 ) )
						{
							$html .= "<span class='plink'><a href='<! HD_URL !>/index.php{$url}&amp;st=". ( $per_page * ( $i - 1 ) ) ."'>{$i}</a></span>";
						}
					}
					else
					{
						$html .= "<span class='plink'><a href='<! HD_URL !>/index.php{$url}&amp;st=". ( $per_page * ( $i - 1 ) ) ."'>{$i}</a></span>";
					}
				}
				else
				{
					$html .= "<span class='plinkc'>{$i}</span>";
				}
			}

			// Display Next Page Link
			if ( $current_page != $num_pages )
			{
				$html .= "<span class='plink'><a href='<! HD_URL !>/index.php{$url}&amp;st=". ( $start + $per_page ) ."'>&rsaquo;</a></span>";
			}

			if ( ( $current_page + 2 ) < $num_pages && $num_pages > 5 )
			{
				$html .= "<span class='plinkj'><a href='<! HD_URL !>/index.php{$url}&amp;st=". ( $per_page * ( ( $i - 1 ) - 1 ) ) ."'>&raquo;</a></span>";
			}
		}

		if ( $admin == '1' )
		{
			$html = str_replace( "index.php", "admin.php", $html );
		}

		return $html;
	}

	#=======================================
	# @ startElement
	# Start element handler.
	#=======================================

	function startElement($parser, $name, $attr)
	{
		$this->xml_current_element = $name;

		$my_var = 'xml_'. $name;
		$this->$my_var = "";

		if( strcmp( $name, "language_pack" ) == 0 )
		{
			$this->xml_lang_abbr = base64_decode( preg_replace( "/\s/", "", $attr["abbr"] ) );
			$this->xml_lang_name = base64_decode( preg_replace( "/\s/", "", $attr["name"] ) );
		}

		if( strcmp( $name, "lang_file" ) == 0 )
		{
			$this->xml_lang_file = $attr["name"];
		}
	}

	#=======================================
	# @ endElement
	# End element handler.
	#=======================================

	function endElement($parser, $name)
	{
		$elements = array( 'lang_file', 'lang_key', 'lang_replace' );

		if( strcmp( $name, "lang_bit" ) == 0 )
		{
			while ( list( , $element ) = each( $elements ) )
			{
				$my_var = 'xml_'. $element;
				$temp[ $element ] = base64_decode( preg_replace( "/\s/", "", $this->$my_var ) );
			}

			$this->xml_lang_bits[] = $temp;

			$this->xml_lang_key = "";
			$this->xml_lang_replace = "";
		}

		if( strcmp( $name, "lang_file" ) == 0 )
		{
			#$this->xml_lang_file = "";
		}
	}

	#=======================================
	# @ characterData
	# Character data handler.
	#=======================================

	function characterData($parser, $data)
	{
		$elements = array( 'lang_key', 'lang_replace' );

		while ( list( , $element ) = each( $elements ) )
		{
			if( $this->xml_current_element == $element )
			{
				$my_var = 'xml_'. $element;
				#$data = trim($data);
				$this->$my_var .= $data;
			}
		}
	}

	#=======================================
	# @ startElementB
	# Start element handler.
	#=======================================

	function startElementB($parser, $name, $attr)
	{
		$this->xml_current_element = $name;

		/*if( strcmp( $name, "skin_file" ) == 0 )
		{
			$this->xml_skin_file = base64_decode( $attr["name"] );
		}*/

		$my_var = 'xml_'. $name;
		$this->$my_var = "";
	}

	#=======================================
	# @ endElementB
	# End element handler.
	#=======================================

	function endElementB($parser, $name)
	{
		$elements = array( 'tname', 'tcontent' );
		$elementsb = array( 'sk_name', 'sk_img_dir', 'sk_author', 'sk_author_email', 'sk_author_web', 'sk_notes', 'sk_css' );
		$elementsc = array( 'filename', 'content', 'path' );

		if( strcmp( $name, "template" ) == 0 )
		{
			while ( list( , $element ) = each( $elements ) )
			{
				$my_var = 'xml_'. $element;
				$temp[ $element ] = base64_decode( preg_replace( "/\s/", "", $this->$my_var ) );
			}

			$this->xml_templates[] = $temp;

			$this->xml_tname = "";
			$this->xml_tcontent = "";
		}

		if( strcmp( $name, "image" ) == 0 )
		{
			while ( list( , $element ) = each( $elementsc ) )
			{
				$my_var = 'xml_'. $element;
				$tempb[ $element ] = base64_decode( preg_replace( "/\s/", "", $this->$my_var ) );
			}

			$this->xml_skin_images[] = $tempb;

			$this->xml_filename = "";
			$this->xml_content = "";
			$this->xml_path = "";
		}

		if( strcmp( $name, "skin_info" ) == 0 )
		{
			while ( list( , $element ) = each( $elementsb ) )
			{
				$my_var = 'xml_'. $element;
				$this->xml_skin_info[ $element ] = base64_decode( preg_replace( "/\s/", "", $this->$my_var ) );
			}
		}
	}

	#=======================================
	# @ characterDataB
	# Character data handler.
	#=======================================

	function characterDataB($parser, $data)
	{
		$elements = array( 'tname', 'tcontent', 'sk_name', 'sk_img_dir', 'sk_author', 'sk_author_email', 'sk_author_web', 'sk_notes', 'sk_css', 'filename', 'content', 'path' );

		while ( list( , $element ) = each( $elements ) )
		{
			if( $this->xml_current_element == $element )
			{
				$my_var = 'xml_'. $element;
				#$data = trim($data);
				$this->$my_var .= $data;
			}
		}
	}

	#=======================================
	# @ parseFile
	# Finally, lets parse the XML file.
	#=======================================

	function parseFile($xml_file, $type=1)
	{
		$xml_parser = xml_parser_create();

		xml_set_object( $xml_parser, $this );

		if ( $type == 1 )
		{
			xml_set_element_handler($xml_parser, "startElement", "endElement");
			xml_set_character_data_handler($xml_parser, "characterData");
		}
		elseif ( $type == 2 )
		{
			xml_set_element_handler($xml_parser, "startElementB", "endElementB");
			xml_set_character_data_handler($xml_parser, "characterDataB");
		}

		xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);

		if( !( $fp = fopen( $xml_file, "r" ) ) )
		{
			die("Cannot open ". $xml_file);
		}

		while( ( $data = fread( $fp, 4096 ) ) )
		{
			if( !xml_parse( $xml_parser, $data, feof($fp) ) )
			{
				die( sprintf("XML error at line %d column %d ", xml_get_current_line_number($xml_parser), xml_get_current_column_number($xml_parser) ) );
			}
		}

		xml_parser_free($xml_parser);

		if ( $type == 1 )
		{
			return $this->xml_lang_bits;
		}
		else
		{
			return array( $this->xml_skin_info, $this->xml_templates, $this->xml_skin_images );
		}
	}

	#=======================================
	# @ Shut Down
	# Runs final processes such as last
	# minute functions.
	#=======================================

	function shut_down()
	{
		if ( is_array( $this->shutdown_funcs ) )
		{
			while ( list( , $func ) = each( $this->shutdown_funcs ) )
			{
				$this->$func();
			}
		}
	}

	#=======================================
	# @ Implode Keys
	# Similar to implode() function except
	# uses keys instead of values
	#=======================================

	function implode_keys($glue, $array)
	{
		foreach( $array as $key => $value )
		{
			$ret[] = $key;
		}

		return implode($glue, $ret);
	}

	#=======================================
	# @ Captcha Create
	# Create a captcha image.
	#=======================================

	function captcha_create()
	{
		require_once HD_INC .'captcha/php-captcha.inc.php';

		$fonts = array( HD_INC .'captcha/VeraBd.ttf',  HD_INC .'captcha/VeraIt.ttf',  HD_INC .'captcha/Vera.ttf' );

		$this->captcha = new PhpCaptcha( $fonts, $this->input['width'], $this->input['height'] );

		if ( $this->input['fontsize'] )
		{
			$this->captcha->SetMaxFontSize( $this->input['fontsize'] );
		}

		$this->captcha->Create();
	}

	#=======================================
	# @ Captcha Validate
	# Validate code with captcha.
	#=======================================

	function captcha_validate($input)
	{
		require_once HD_INC .'captcha/php-captcha.inc.php';

		if ( PhpCaptcha::Validate( $input ) )
		{
			return TRUE;
		}

		return FALSE;
	}

	#=======================================
	# @ Create Token
	# Create form token hash.
	#=======================================

	function create_token($type)
	{
		if ( $this->core->cache['config']['use_form_tokens'] )
		{
			$token = strrev( md5( 't'. uniqid( rand(), true ) ) );

			$db_array = array(
							  'token'	=> $token,
							  'type'	=> $type,
							  'ipadd'	=> $this->input['ip_address'],
							  'date'	=> time(),
							 );

			$this->core->db->construct( array(
										  	  'insert'	=> 'tokens',
											  'set'		=> $db_array,
								 		) 	 );

			$this->core->db->next_shutdown();
			$this->core->db->execute();

			return "<input type='hidden' name='token' value='{$token}' />";
		}
		else
		{
			return "";
		}
	}

	#=======================================
	# @ Check Token
	# Check form token with database.
	#=======================================

	function check_token($type)
	{
		if ( $this->core->cache['config']['use_form_tokens'] )
		{
			if ( $this->core->cache['config']['token_ip_check'] )
			{
				$sql_where = array( array( 'token', '=', $this->input['token'] ), array( 'type', '=', $type, 'and' ), array( 'ipadd', '=', $this->input['ip_address'], 'and' ) );
			}
			else
			{
				$sql_where = array( array( 'token', '=', $this->input['token'] ), array( 'type', '=', $type, 'and' ) );
			}

			$this->core->db->construct( array(
										  	  'select'	=> array( 'id' ),
										  	  'from'	=> 'tokens',
										  	  'where'	=> $sql_where,
										  	  'limit'	=> array( 0, 1 ),
								 	   ) 	);

			$this->core->db->execute();

			if ( $this->core->db->get_num_rows() )
			{
				$this->core->db->construct( array(
											  	  'delete'	=> 'tokens',
											  	  'where'	=> array( 'token', '=', $this->input['token'] ),
											  	  'limit'	=> array( 1 ),
									 	   ) 	);

				$this->core->db->next_shutdown();
				$this->core->db->execute();

				return TRUE;
			}
			else
			{
				$this->skin->error('token_mismatch');
			}
		}
		else
		{
			return TRUE;
		}
	}

	#=======================================
	# @ Check Ticket Auto Close
	# Check tickets for ones that need to
	# be auto closed.
	#=======================================

	function check_auto_close()
	{
		$this->core->db->construct( array(
									  	  'select'	=> array( 'id', 'mid' ),
									  	  'from'	=> 'tickets',
									  	  'where'	=> array( array( 'auto_close', '<=', time() ), array( 'auto_close', '!=', 0, 'and' ), array( 'status', '=', 4, 'and' ) ),
							 	   ) 	);

		$this->core->db->execute();

		if ( $this->core->db->get_num_rows() )
		{
			while( $t = $this->core->db->fetch_row() )
			{
				$members[ $t['mid'] ] ++;

				$tickets[] = $t['id'];
			}

			$this->core->db->construct( array(
										  	  'update'	=> 'tickets',
										  	  'set'		=> array( 'close_reason' => 'No response from customer.', 'status' => 6 ),
										  	  'where'	=> array( 'id', 'in', $tickets ),
								 	   ) 	);

			$this->core->db->next_shutdown();
			$this->core->db->execute();

			while( list( $mid, $mtickets ) = each( $members ) )
			{
				$this->core->db->next_no_quotes('set');

				$this->core->db->construct( array(
											  	  'update'	=> 'members',
											  	  'set'		=> array( 'open_tickets' => 'open_tickets-'. $mtickets ),
							 				  	  'where'	=> "id = '". $mid ."'",
							 				  	  'limit'	=> array( 1 ),
								 	  	  ) 	);

				$this->core->db->next_shutdown();
				$this->core->db->execute();
			}

			$this->r_ticket_stats(1);
		}

		$to_cache = array(); // Initialize for Security

		$to_cache['auto_close'] = time();

		$this->core->add_cache( 'tasks', $to_cache );
	}

	#=======================================
	# @ Format Size
	# Convert size into appropriate format.
	#=======================================

	function format_size($bytes)
	{
		if ( $bytes < 1024 )
		{
			return $bytes .' '. $this->lang['bytes'];
		}

		$kb = $bytes / 1024;

		if ( $kb < 1024 )
		{
			return round( $kb, 2 ) .' '. $this->lang['kb'];
		}

		$mb = $kb / 1024;

		if ( $mb < 1024 )
		{
			return round( $mb, 2 ) .' '. $this->lang['mb'];
		}
	}

	#=======================================
	# @ Recount: Tickets per Member
	# Recounts the number of tickets per
	# member.
	#=======================================

	function r_tickets_per_member($shutdown=0)
	{
		if ( $shutdown == 1 )
		{
			$this->shutdown_funcs[] = 'r_tickets_per_member';
			return TRUE;
		}

		#=============================
		# Grab Tickets
		#=============================

		$this->core->db->construct( array(
									  	  'select'	=> array( 'id', 'mid', 'status' ),
									  	  'from'	=> 'tickets',
					 		  	   ) 	 );

		$this->core->db->execute();

		if ( $this->core->db->get_num_rows() )
		{
			while( $t = $this->core->db->fetch_row() )
			{
				if ( $t['status'] != 6 )
				{
					$o_tickets[ $t['mid'] ] ++;
				}

				$tickets[ $t['mid'] ] ++;
			}
		}

		#=============================
		# Grab Members
		#=============================

		$this->core->db->construct( array(
										  'select'	=> array( 'id' ),
									  	  'from'	=> 'members',
							   	   ) 	 );

		$this->core->db->execute();

		if ( $this->core->db->get_num_rows() )
		{
			while( $m = $this->core->db->fetch_row() )
			{
				$members[ $m['id'] ] = 1;
			}
		}

		#=============================
		# Update Members
		#=============================

		while ( list( $mid, ) = each( $members ) )
		{
			$this->core->db->construct( array(
											  'update'	=> 'members',
											  'set'		=> array( 'open_tickets' => $o_tickets[ $mid ], 'tickets' => $tickets[ $mid ] ),
										  	  'where'	=> array( 'id', '=', $mid ),
										  	  'limit'	=> array( 1 ),
								  	   ) 	 );

			$this->core->db->execute();
		}

		$this->log( 'admin', "Recounted Tickets Per Member" );
	}

	#=======================================
	# @ Recount: Tickets per Department
	# Recounts the number of tickets per
	# department.
	#=======================================

	function r_tickets_per_dept($shutdown=0)
	{
		if ( $shutdown == 1 )
		{
			$this->shutdown_funcs[] = 'r_tickets_per_dept';
			return TRUE;
		}

		#=============================
		# Grab Tickets
		#=============================

		$this->core->db->construct( array(
									  	  'select'	=> array( 'id', 'did' ),
									  	  'from'	=> 'tickets',
					 		  	   ) 	 );

		$this->core->db->execute();

		if ( $this->core->db->get_num_rows() )
		{
			while( $t = $this->core->db->fetch_row() )
			{
				$tickets[ $t['did'] ] ++;
			}
		}

		#=============================
		# Grab Departments
		#=============================

		$this->core->db->construct( array(
									  	  'select'	=> array( 'id' ),
									  	  'from'	=> 'departments',
							  	   ) 	 );

		$this->core->db->execute();

		if ( $this->core->db->get_num_rows() )
		{
			while( $d = $this->core->db->fetch_row() )
			{
				$departs[ $d['id'] ] = 1;
			}
		}

		#=============================
		# Update Departments
		#=============================

		while ( list( $did, ) = each( $departs ) )
		{
			$this->core->db->construct( array(
										  	  'update'	=> 'departments',
										  	  'set'		=> array( 'tickets' => $tickets[ $did ] ),
										  	  'where'	=> array( 'id', '=', $did ),
										  	  'limit'	=> array( 1 ),
						 		  	   ) 	 );

			$this->core->db->execute();
		}

		$this->log( 'admin', "Recounted Tickets Per Department" );
	}
}

?>