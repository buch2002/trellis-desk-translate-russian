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
|    | Portal :: Sources
#======================================================
*/

class portal {

	#=======================================
	# @ Auto Run
	# Function that is run automatically
	# when the file is required.
	#=======================================

	function auto_run()
	{
		#=============================
		# Grab Annoucements
		#=============================

		if ( $this->ifthd->core->cache['config']['enable_news'] && $this->ifthd->core->cache['config']['display_qnews'] )
		{
			$final_a = array(); // Initialize for Security
			$row_count = 0; // Initialize for Security
			$news = 0; // Initialize for Security

			while ( list( $id, $a ) = each( $this->ifthd->core->cache['announce'] ) )
			{
				#=============================
				# Fix Up Information
				#=============================
				
				$row_count ++;
				
				( $row_count & 1 ) ? $a['class'] = 1 : $a['class'] = 2;

				$a['date'] = $this->ifthd->ift_date( $a['date'] );

				$final_a[] = $a;
			}

			$this->ifthd->core->template->set_var( 'announcements', $final_a );
		}

		#=============================
		# Grab Tickets
		#=============================

		if ( $this->ifthd->member['id'] || $this->ifthd->member['s_tkey'] )
		{
			if ( $this->ifthd->member['id'] )
			{
				$this->ifthd->core->db->construct( array(
													  	 'select'	=> array( 'id', 'dname', 'subject', 'priority', 'date', 'status' ),
													  	 'from'		=> 'tickets',
									 				  	 'where'	=> array( 'mid', '=', $this->ifthd->member['id'] ),
									 				  	 'order'	=> array( 'date' => 'DESC' ),
									 				  	 'limit'	=> array( 0, 8 ),
									 		  	  ) 	);
			}
			elseif ( $this->ifthd->member['s_tkey'] )
			{
				$this->ifthd->core->db->construct( array(
													  	 'select'	=> array( 'id', 'dname', 'subject', 'priority', 'date', 'status' ),
													  	 'from'		=> 'tickets',
									 				  	 'where'	=> array( array( 'email', '=', $this->ifthd->member['s_email'] ), array( 'guest', '=', 1, 'and' ) ),
									 				  	 'order'	=> array( 'date' => 'DESC' ),
									 				  	 'limit'	=> array( 0, 8 ),
									 		  	  ) 	);
			}

			$this->ifthd->core->db->execute();

			$ticket_rows = array(); // Initialize for Security
			$row_count = 0; // Initialize for Security

			if ( $this->ifthd->core->db->get_num_rows() )
			{
				while( $t = $this->ifthd->core->db->fetch_row() )
				{
					#=============================
					# Fix Up Information
					#=============================
					
					$row_count ++;
					
					( $row_count & 1 ) ? $t['class'] = 1 : $t['class'] = 2;
					
					if ( $t['priority'] == 1 ) $p_color = 'blue';
					if ( $t['priority'] == 2 ) $p_color = 'yellow';
					if ( $t['priority'] == 3 ) $p_color = 'orange';
					if ( $t['priority'] == 4 ) $p_color = 'red';
	
					$t['p_img'] = "<img src='images/". $this->ifthd->skin->data['img_dir'] ."/sq_". $p_color .".gif' class='pip' alt='priority' />&nbsp;&nbsp;";

					$t['priority'] = $this->ifthd->get_priority( $t['priority'] );

					$t['date'] = $this->ifthd->ift_date( $t['date'], "n/j/y g:i A" );

					$t['status'] = $this->ifthd->get_status( $t['status'] );

					$ticket_rows[] = $t;
				}

				$this->ifthd->core->template->set_var( 'tickets_ov', $ticket_rows );
			}
		}

		#=============================
		# Grab Recent Articles
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'name', 'description' ),
											  	 'from'		=> 'articles',
											  	 'order'	=> array( 'date' => 'desc' ),
											  	 'limit'	=> array( 0, $this->ifthd->core->cache['config']['recent_articles'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$recent = array(); // Initialize for Security

		while( $a = $this->ifthd->core->db->fetch_row() )
		{
			$recent[] = $a;
		}

		$this->ifthd->core->template->set_var( 'recent_articles', $recent );

		#=============================
		# Grab Most Popular
		#=============================

		$this->ifthd->core->db->construct( array(
											  	 'select'	=> array( 'id', 'name', 'description' ),
											  	 'from'		=> 'articles',
											  	 'order'	=> array( 'rating' => 'desc' ),
											  	 'limit'	=> array( 0, $this->ifthd->core->cache['config']['popular_articles'] ),
							 		  	  ) 	);

		$this->ifthd->core->db->execute();

		$popular = array(); // Initialize for Security

		while( $a = $this->ifthd->core->db->fetch_row() )
		{
			$popular[] = $a;
		}

		$this->ifthd->core->template->set_var( 'popular_articles', $popular );

		#=============================
		# Do Output
		#=============================

		$this->ifthd->core->template->set_var( 'sub_tpl', 'portal.tpl' );

		$this->ifthd->skin->do_output();
	}

}

?>