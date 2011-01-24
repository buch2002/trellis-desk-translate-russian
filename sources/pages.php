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
|    | Custom Pages :: Sources
#======================================================
*/

class pages {

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

		$this->show_page();
	}

	#=======================================
	# @ Show Page
	# Display a custom page.
	#=======================================

	function show_page()
	{
		#=============================
		# Grab Page
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> 'all',
											  	 'from'		=> 'pages',
							 				  	 'where'	=> array( 'id', '=', intval( $this->ifthd->input['id'] ) ),
							 				  	 'limit'	=> array( 0, 1 ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		if ( ! $this->ifthd->core->db->get_num_rows() )
		{
			$this->ifthd->log( 'error', "Custom Page Not Found ID: ". intval( $this->ifthd->input['id'] ) );

			$this->ifthd->skin->error('no_page');
		}

		$p = $this->ifthd->core->db->fetch_row();

		#=============================
		# Fix Up Information
		#=============================

		if ($p['type'] )
		{
			$p['template'] .= '.tpl';

			$this->ifthd->core->template->set_var( 'template', $p['template'] );
		}
		else
		{
			$p['content'] = $this->ifthd->prepare_output( $this->ifthd->remove_dbl_spaces( $this->ifthd->convert_html( $p['content'] ) ), 0, 0, 1 );
		}

		$this->ifthd->core->template->set_var( 'p', $p );

		#=============================
		# Do Output
		#=============================

		$this->nav = array( "<a href='{$this->ifthd->core->cache['config']['hd_url']}/index.php?act=pages&amp;id={$p['id']}'>{$p['name']}</a>" );

		$this->ifthd->core->template->set_var( 'sub_tpl', 'page_show.tpl' );

		$this->ifthd->skin->do_output( array( 'nav' => $this->nav, 'title' => $p['name'] ) );
	}

}

?>