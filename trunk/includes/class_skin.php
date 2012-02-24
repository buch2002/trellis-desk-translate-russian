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
|    | Skin Class
#======================================================
*/

class skin {

	var $outhtml	= "";
	var $data		= array();
	var $global 	= "";

	#=======================================
	# @ Load Skin
	# Loads the skin.  Inclues skin info,
	# css, wrapper, etc.
	#=======================================

	function load_skin()
	{
		header('Content-Type: text/html; charset=utf-8');
		
		#=============================
		# Do We Have A Skin ID?
		#=============================

		if ( ! $this->ifthd->member['skin'] )
		{
			$this->ifthd->member['skin'] = $this->ifthd->core->cache['skin']['default'];
		}

		#=============================
		# Load Skin
		#=============================

		$this->data = $this->ifthd->core->cache['skin'][ $this->ifthd->member['skin'] ];

		#=============================
		# Load Globals
		#=============================

		$this->ifthd->load_lang('global');

		return $this->data;
	}

	#=======================================
	# @ Do Output
	# Generates and sends all the HTML, etc
	# to the browser.
	#=======================================

	function do_output($extra="")
	{
		#=============================
		# Initialize
		#=============================

		$this->ifthd->core->template->config_set( 'tpl_path', HD_SKIN .'s'. $this->ifthd->member['skin'] .'/' );

		$footer = ""; // Initialize for Security
		$nav_links = ""; // Initialize for Security

		#=============================
		# Something Extra 4 ME? :O
		#=============================

		if ( is_array( $extra ) )
		{
			if ( isset( $extra['title'] ) )
			{
				$title = $extra['title'];
			}

			if ( isset( $extra['footer'] ) )
			{
				$footer = $extra['footer'];
			}

			if ( isset( $extra['nav'] ) )
			{
				$nav_links = $extra['nav'];
			}
		}

		#=============================
		# Do We Have A Title?
		#=============================

		if ( ! isset( $title ) )
		{
			$title = $this->ifthd->core->cache['config']['hd_name'];
		}
		else
		{
			$title = $this->ifthd->core->cache['config']['hd_name'] .' :: '. $title;
		}

		$this->ifthd->core->template->set_var( 'title', $title );

		#=============================
		# Navigation
		#=============================

		$nav_tree = ""; // Initialize for Security

		if ( is_array( $nav_links ) )
		{
			while ( list( , $nlink ) = each( $nav_links ) )
			{
				$nav_tree .= ' &rsaquo; '. $nlink;
			}
		}

		$this->ifthd->core->template->set_var( 'nav_links', $nav_tree );

		#=============================
		# Sidebar
		#=============================

		if ( $this->ifthd->member['id'] )
		{
			$this->ifthd->core->template->set_var( 'tickets', $this->ifthd->get_open_tickets() );
		}
		elseif ( $this->ifthd->member['s_tkey'] )
		{
			$this->ifthd->core->template->set_var( 'tickets', $this->ifthd->get_open_guest_tickets() );
		}
		else
		{
			$this->ifthd->core->template->set_var( 'token_g_login', $this->ifthd->create_token('login') );
		}

		$this->ifthd->core->template->set_var( 'token_g_search', $this->ifthd->create_token('search') );

		/**********************************************************************/
		/* REMOVAL OF THE COPYRIGHT WITHOUT PURCHASING COPYRIGHT REMOVAL WILL */
		/* VIOLATE THE LICENSE YOU AGREED TO WHEN DOWNLOADING AND REGISTERING */
		/* THIS PORDUCT.  IF THIS HAPPENS, IT COULD RESULT IN REMOVAL OF THIS */
		/* SYSTEM AND POSSIBLY CRIMINAL CHARGES.  THANK YOU FOR UNDERSTANDING */
		/***********************************************************************/

		$query_count = $this->ifthd->core->db->get_query_count();
		$query_s_count = $this->ifthd->core->db->get_query_s_count();
		$exe_time = $this->ifthd->end_timer();

		$copyright = "<div id='copyright'>Powered By <a href='http://www.accord5.com/products/trellis/'>Trellis Desk</a> {$this->ifthd->vername} &copy; ". date('Y') ." <a href='http://www.accord5.com/'>ACCORD5</a><br /><span title='". $query_count ." Normal | ". $query_s_count ." Shutdown'>". $query_count ." Запрос(ов)</span> // ". $exe_time ." Секунд</div>";

		#=============================
		# Global Variables
		#=============================

		$this->ifthd->core->template->set_var( 'tpl_url', $this->ifthd->core->cache['config']['hd_url'] .'/skin/s'. $this->ifthd->member['skin'] );
		$this->ifthd->core->template->set_var( 'td_url', $this->ifthd->core->cache['config']['hd_url'] );
		$this->ifthd->core->template->set_var( 'td_name', $this->ifthd->core->cache['config']['hd_name'] );
		$this->ifthd->core->template->set_var( 'img_url', $this->ifthd->core->cache['config']['hd_url'] .'/images/'. $this->data['img_dir'] );
		$this->ifthd->core->template->set_var( 'copyright', $copyright );
		$this->ifthd->core->template->set_var( 'extra_l', $this->ifthd->input['extra_l'] );

		$this->ifthd->core->template->set_var( 'member', &$this->ifthd->member );
		$this->ifthd->core->template->set_var( 'cache', &$this->ifthd->core->cache );
		$this->ifthd->core->template->set_var( 'input', &$this->ifthd->input );

		$this->ifthd->core->template->set_var( 'lang', &$this->ifthd->lang );

		#=============================
		# Output
		#=============================

		$this->ifthd->core->template->set_var( 'wrapper_type', 1 );

		$this->ifthd->core->template->display( 'wrapper.tpl' );

		$this->ifthd->core->shut_down_q();
		$this->ifthd->shut_down();
		$this->ifthd->core->shut_down();

		if ( HD_DEBUG )
		{
			echo "<br /><br />------------------<br /><br />". $this->ifthd->core->db->queries_ran;
		}

		exit();
	}

	#=======================================
	# @ Do Print
	# Generates and sends all the HTML, etc
	# to the browser for printing.
	#=======================================

	function do_print($extra="")
	{
		#=============================
		# Initialize
		#=============================

		$this->ifthd->core->template->config_set( 'tpl_path', HD_SKIN .'s'. $this->ifthd->member['skin'] .'/' );

		$footer = ""; // Initialize for Security

		#=============================
		# Something Extra 4 ME? :O
		#=============================

		if ( is_array( $extra ) )
		{
			if ( isset( $extra['title'] ) )
			{
				$title = $extra['title'];
			}

			if ( isset( $extra['footer'] ) )
			{
				$footer = $extra['footer'];
			}
		}

		#=============================
		# Do We Have A Title?
		#=============================

		if ( ! isset( $title ) )
		{
			$title = $this->ifthd->core->cache['config']['hd_name'];
		}
		else
		{
			$title = $this->ifthd->core->cache['config']['hd_name'] .' :: '. $title;
		}

		$this->ifthd->core->template->set_var( 'title', $title );

		/**********************************************************************/
		/* REMOVAL OF THE COPYRIGHT WITHOUT PURCHASING COPYRIGHT REMOVAL WILL */
		/* VIOLATE THE LICENSE YOU AGREED TO WHEN DOWNLOADING AND REGISTERING */
		/* THIS PORDUCT.  IF THIS HAPPENS, IT COULD RESULT IN REMOVAL OF THIS */
		/* SYSTEM AND POSSIBLY CRIMINAL CHARGES.  THANK YOU FOR UNDERSTANDING */
		/***********************************************************************/

		$copyright = "<div id='copyright'>Powered By <a href='http://www.accord5.com/products/trellis/'>Trellis Desk</a> {$this->ifthd->vername} &copy; ". date('Y') ." <a href='http://www.accord5.com/'>ACCORD5</a><br /><span title='". $query_count ." Normal | ". $query_s_count ." Shutdown'>". $query_count ." Запрос(ов)</span> // ". $exe_time ." Секунд</div>";

		#=============================
		# Global Variables
		#=============================

		$this->ifthd->core->template->set_var( 'tpl_url', $this->ifthd->core->cache['config']['hd_url'] .'/skin/s'. $this->ifthd->member['skin'] );
		$this->ifthd->core->template->set_var( 'td_url', $this->ifthd->core->cache['config']['hd_url'] );
		$this->ifthd->core->template->set_var( 'td_name', $this->ifthd->core->cache['config']['hd_name'] );
		$this->ifthd->core->template->set_var( 'img_url', $this->ifthd->core->cache['config']['hd_url'] .'/images/'. $this->data['img_dir'] );
		$this->ifthd->core->template->set_var( 'copyright', $copyright );

		$this->ifthd->core->template->set_var( 'member', &$this->ifthd->member );
		$this->ifthd->core->template->set_var( 'cache', &$this->ifthd->core->cache );
		$this->ifthd->core->template->set_var( 'input', &$this->ifthd->input );

		$this->ifthd->core->template->set_var( 'lang', &$this->ifthd->lang );

		#=============================
		# Output
		#=============================

		$this->ifthd->core->template->display( 'print.tpl' );

		$this->ifthd->core->shut_down_q();
		$this->ifthd->shut_down();
		$this->ifthd->core->shut_down();

		if ( HD_DEBUG )
		{
			echo "<br /><br />------------------<br /><br />". $this->ifthd->core->db->queries_ran;
		}

		exit();
	}

	#=======================================
	# Redirect
	# Generates and sends all the HTML, etc
	# to the browser for a redirect.
	#=======================================

	function redirect($url, $msg, $full=0)
	{
		#=============================
		# Initialize
		#=============================

		$this->ifthd->core->template->config_set( 'tpl_path', HD_SKIN .'s'. $this->ifthd->member['skin'] .'/' );

		$footer = ""; // Initialize for Security
		$nav_links = ""; // Initialize for Security

		$this->ifthd->load_lang('redirect');

		if ( ! $full )
		{
			$url = $this->ifthd->core->cache['config']['hd_url'] .'/index.php'. $url; // TO BE COMPLETED
		}

		$this->ifthd->core->template->set_var( 'redirect_url', str_replace( "&", '&amp;', $url ) );
		$this->ifthd->core->template->set_var( 'redirect_msg', $this->ifthd->lang[ $msg ] );

		#=============================
		# Do We Have A Title?
		#=============================

		$this->ifthd->core->template->set_var( 'title', $this->ifthd->core->cache['config']['hd_name'] .' :: '. $this->ifthd->lang['please_wait'] );

		#=============================
		# Navigation
		#=============================

		$nav_tree = ' &rsaquo; '. $this->ifthd->lang['redirect'];

		$this->ifthd->core->template->set_var( 'nav_links', $nav_tree );

		/**********************************************************************/
		/* REMOVAL OF THE COPYRIGHT WITHOUT PURCHASING COPYRIGHT REMOVAL WILL */
		/* VIOLATE THE LICENSE YOU AGREED TO WHEN DOWNLOADING AND REGISTERING */
		/* THIS PORDUCT.  IF THIS HAPPENS, IT COULD RESULT IN REMOVAL OF THIS */
		/* SYSTEM AND POSSIBLY CRIMINAL CHARGES.  THANK YOU FOR UNDERSTANDING */
		/***********************************************************************/

		$query_count = $this->ifthd->core->db->get_query_count();
		$query_s_count = $this->ifthd->core->db->get_query_s_count();
		$exe_time = $this->ifthd->end_timer();

		$copyright = "<div id='copyright'>Powered By <a href='http://www.accord5.com/products/trellis/'>Trellis Desk</a> {$this->ifthd->vername} &copy; ". date('Y') ." <a href='http://www.accord5.com/'>ACCORD5</a><br /><span title='". $query_count ." Normal | ". $query_s_count ." Shutdown'>". $query_count ." Запрос(ов)</span> // ". $exe_time ." Секунд</div>";

		#=============================
		# Global Variables
		#=============================

		$this->ifthd->core->template->set_var( 'tpl_url', $this->ifthd->core->cache['config']['hd_url'] .'/skin/s'. $this->ifthd->member['skin'] );
		$this->ifthd->core->template->set_var( 'td_url', $this->ifthd->core->cache['config']['hd_url'] );
		$this->ifthd->core->template->set_var( 'td_name', $this->ifthd->core->cache['config']['hd_name'] );
		$this->ifthd->core->template->set_var( 'img_url', $this->ifthd->core->cache['config']['hd_url'] .'/images/'. $this->data['img_dir'] );
		$this->ifthd->core->template->set_var( 'copyright', $copyright );

		$this->ifthd->core->template->set_var( 'member', &$this->ifthd->member );
		$this->ifthd->core->template->set_var( 'cache', &$this->ifthd->core->cache );
		$this->ifthd->core->template->set_var( 'input', &$this->ifthd->input );

		$this->ifthd->core->template->set_var( 'lang', &$this->ifthd->lang );

		#=============================
		# Output
		#=============================

		$this->ifthd->core->template->set_var( 'wrapper_type', 2 );

		$this->ifthd->core->template->display( 'wrapper.tpl' );

		$this->ifthd->core->shut_down_q();
		$this->ifthd->shut_down();
		$this->ifthd->core->shut_down();

		#=============================
		# Redirect! Duh!
		#=============================

		header('Refresh: 3; URL='. $url);

		if ( HD_DEBUG )
		{
			echo "<br /><br />------------------<br /><br />". $this->ifthd->core->db->queries_ran;
		}

		exit();
	}

	#=======================================
	# Error
	# Generates and sends all the HTML, etc
	# to the browser or an error page.
	#=======================================

	function error($msg, $login=0)
	{
		#=============================
		# Initialize
		#=============================

		$this->ifthd->load_lang('error');

		$this->ifthd->core->template->config_set( 'tpl_path', HD_SKIN .'s'. $this->ifthd->member['skin'] .'/' );

		$footer = ""; // Initialize for Security
		$nav_links = ""; // Initialize for Security

		$this->ifthd->core->template->set_var( 'error_msg', $this->ifthd->lang[ $msg ] );

		if ( $login )
		{
			$this->ifthd->core->template->set_var( 'token_e_login', $this->ifthd->create_token('login') );
		}

		#=============================
		# Do We Have A Title?
		#=============================

		$this->ifthd->core->template->set_var( 'title', $this->ifthd->core->cache['config']['hd_name'] .' :: '. $this->ifthd->lang['error'] );

		#=============================
		# Navigation
		#=============================

		$nav_tree = ' &rsaquo; '. $this->ifthd->lang['error'];

		$this->ifthd->core->template->set_var( 'nav_links', $nav_tree );

		#=============================
		# Sidebar
		#=============================

		if ( $this->ifthd->member['id'] )
		{
			$this->ifthd->core->template->set_var( 'tickets', $this->ifthd->get_open_tickets() );
		}
		elseif ( $this->ifthd->member['s_tkey'] )
		{
			$this->ifthd->core->template->set_var( 'tickets', $this->ifthd->get_open_guest_tickets() );
		}
		else
		{
			$this->ifthd->core->template->set_var( 'token_g_login', $this->ifthd->create_token('login') );
		}

		$this->ifthd->core->template->set_var( 'token_g_search', $this->ifthd->create_token('search') );

		/**********************************************************************/
		/* REMOVAL OF THE COPYRIGHT WITHOUT PURCHASING COPYRIGHT REMOVAL WILL */
		/* VIOLATE THE LICENSE YOU AGREED TO WHEN DOWNLOADING AND REGISTERING */
		/* THIS PORDUCT.  IF THIS HAPPENS, IT COULD RESULT IN REMOVAL OF THIS */
		/* SYSTEM AND POSSIBLY CRIMINAL CHARGES.  THANK YOU FOR UNDERSTANDING */
		/***********************************************************************/

		$query_count = $this->ifthd->core->db->get_query_count();
		$query_s_count = $this->ifthd->core->db->get_query_s_count();
		$exe_time = $this->ifthd->end_timer();

		$copyright = "<div id='copyright'>Powered By <a href='http://www.accord5.com/products/trellis/'>Trellis Desk</a> {$this->ifthd->vername} &copy; ". date('Y') ." <a href='http://www.accord5.com/'>ACCORD5</a><br /><span title='". $query_count ." Normal | ". $query_s_count ." Shutdown'>". $query_count ." Запрос(ов)</span> // ". $exe_time ." Секунд</div>";

		#=============================
		# Global Variables
		#=============================

		$this->ifthd->core->template->set_var( 'tpl_url', $this->ifthd->core->cache['config']['hd_url'] .'/skin/s'. $this->ifthd->member['skin'] );
		$this->ifthd->core->template->set_var( 'td_url', $this->ifthd->core->cache['config']['hd_url'] );
		$this->ifthd->core->template->set_var( 'td_name', $this->ifthd->core->cache['config']['hd_name'] );
		$this->ifthd->core->template->set_var( 'img_url', $this->ifthd->core->cache['config']['hd_url'] .'/images/'. $this->data['img_dir'] );
		$this->ifthd->core->template->set_var( 'copyright', $copyright );
		$this->ifthd->core->template->set_var( 'extra_l', $this->ifthd->input['extra_l'] );

		$this->ifthd->core->template->set_var( 'member', &$this->ifthd->member );
		$this->ifthd->core->template->set_var( 'cache', &$this->ifthd->core->cache );
		$this->ifthd->core->template->set_var( 'input', &$this->ifthd->input );

		$this->ifthd->core->template->set_var( 'lang', &$this->ifthd->lang );

		#=============================
		# Output
		#=============================

		if ( $login )
		{
			$this->ifthd->core->template->set_var( 'wrapper_type', 4 );
		}
		else
		{
			$this->ifthd->core->template->set_var( 'wrapper_type', 3 );
		}

		$this->ifthd->core->template->display( 'wrapper.tpl' );

		$this->ifthd->core->shut_down_q();
		$this->ifthd->shut_down();
		$this->ifthd->core->shut_down();

		if ( HD_DEBUG )
		{
			echo "<br /><br />------------------<br /><br />". $this->ifthd->core->db->queries_ran;
		}

		exit();
	}
}

?>