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
|    | Feed :: Sources
#======================================================
*/

class feed {

	#=======================================
	# @ Auto Run
	# Function that is run automatically
	# when the file is required.
	#=======================================

	function auto_run()
	{
		#=============================
		# Security Checks
		#=============================

		switch( $this->ifthd->input['code'] )
    	{
    		case 'stickets':
				$this->show_feed('stickets');
    		break;

    		default:
    			$this->show_feed('announcements');
    		break;
		}
	}

	#=======================================
	# @ Show Feed
	# Prepares for feed output.
	#=======================================

	function show_feed($type, $mid=0, $create=0)
	{
		if ( $type == 'announcements' )
		{
			$this->feed_file = 'feed_announcements.td';

			$this->output_feed($type);
		}
		elseif ( $type == 'stickets' )
		{
			if ( ! $mid )
			{
				$mid = intval( $this->ifthd->input['id'] );
			}

			$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'm' => array( 'id', 'name', 'mgroup', 'rss_key' ), 'g' => array( 'g_depart_perm', 'g_acp_access' ) ),
												  	 'from'		=> array( 'm' => 'members' ),
												  	 'join'		=> array( array( 'from' => array( 'g' => 'groups' ), 'where' => array( 'g' => 'g_id', '=', 'm' => 'mgroup' ) ) ),
								 				  	 'where'	=> array( array( array( 'm' => 'id' ), '=', $mid ), array( array( 'g' => 'g_acp_access' ), '=', 1, 'and' ) ),
								 				  	 'limit'	=> array( 0, 1 ),
								 		  	  ) 	);

			$this->ifthd->core->db->execute();

			if ( $this->ifthd->core->db->get_num_rows() )
			{
				$this->sm = $this->ifthd->core->db->fetch_row();

				$this->feed_file = 'feed_stickets_m'. $this->sm['id'] .'.td';

				if ( $create )
				{
					$this->create_feed($type);
				}
				else
				{
					if ( $this->ifthd->input['key'] == $this->sm['rss_key'] )
					{
						$this->output_feed($type);
					}
				}
			}
		}
	}

	#=======================================
	# @ Output Feed
	# Outputs the feed.
	#=======================================

	function output_feed($type)
	{
		header('Content-type: application/xml');

		if ( file_exists( HD_PATH .'core/tmp/'. $this->feed_file ) )
		{
			readfile( HD_PATH .'core/tmp/'. $this->feed_file );
		}
		else
		{
			print $this->create_feed($type);
		}
	}

	#=======================================
	# @ Create Feed
	# Creates a feed.  What else?
	#=======================================

	function create_feed($type)
	{
		$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
				<rss version=\"2.0\">
				<channel>";

		if ( $type == 'announcements' )
		{
			$xml .= "<title>". $this->ifthd->core->cache['config']['hd_name'] ." Announcements</title>
					<link>". $this->ifthd->core->cache['config']['hd_url'] ."</link>
					<description>Latest announcements from ". $this->ifthd->core->cache['config']['hd_name'] ."</description>
					<lastBuildDate>". gmdate( 'D, d M Y H:i:s' ) ." GMT</lastBuildDate>";

			$this->ifthd->core->db->construct( array(
												  	  'select'		=> 'all',
													  'from'		=> 'announcements',
													  'order'		=> array( 'date' => 'desc' ),
													  'limit'		=> array( 0, $this->ifthd->core->cache['config']['announce_amount'] ),
										 	   )	 );

			$this->ifthd->core->db->execute();

			while ( $a = $this->ifthd->core->db->fetch_row() )
			{
				$xml .= "<item>
							<title>". $a['title'] ."</title>
							<link>". $this->ifthd->core->cache['config']['hd_url'] ."</link>
							<guid>". $this->ifthd->core->cache['config']['hd_url'] ."/announcements/". $a['id'] ."</guid>
							<pubDate>". gmdate( 'D, d M Y H:i:s', $a['date'] ) ." GMT</pubDate>
							<author>". $this->ifthd->core->cache['config']['out_email'] ." (". $a['mname'] .")</author>
							<description><![CDATA[ ". $this->ifthd->prepare_output( $a['content'], 0, 1 ) ." ]]></description>
						</item>";
			}

			$feed_file = 'feed_announcements.td';
		}
		elseif ( $type == 'stickets' )
		{
			$xml .= "<title>My Department Tickets</title>
					<link>". $this->ifthd->core->cache['config']['hd_url'] ."/admin.php</link>
					<description>Latest tickets in your department.</description>
					<lastBuildDate>". gmdate( 'D, d M Y H:i:s' ) ." GMT</lastBuildDate>";

			$this->sm['g_depart_perm'] = unserialize( $this->sm['g_depart_perm'] );

			if ( is_array( $this->sm['g_depart_perm'] ) )
			{
				$rev_perms = array(); // Initialize for Security

				foreach( $this->sm['g_depart_perm'] as $did => $access )
				{
					if ( $access == 1 ) $rev_perms[] = $did;
				}
			}

			// Tickets
			if ( is_array( $rev_perms ) )
			{
				$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'id', 'did', 'dname', 'mid', 'mname', 'email', 'subject', 'priority', 'message', 'date' ),
												  	 'from'		=> 'tickets',
								 				  	 'where'	=> array( 'did', 'in', $rev_perms ),
								 				  	 'order'	=> array( 'date' => 'DESC' ),
								 				  	 'limit'	=> array( 0, 15 ),
								 		  	  ) 	);

				$this->ifthd->core->db->execute();
			}
			else
			{
				$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'id', 'did', 'dname', 'mid', 'mname', 'email', 'subject', 'priority', 'message', 'date' ),
												  	 'from'		=> 'tickets',
								 				  	 'order'	=> array( 'date' => 'DESC' ),
								 				  	 'limit'	=> array( 0, 15 ),
								 		  	  ) 	);

				$this->ifthd->core->db->execute();
			}

			$tickets = array(); // Initialize for Security

			while ( $t = $this->ifthd->core->db->fetch_row() )
			{
				$t['type'] = 'ticket';

				$rss_items[ $t['date'] ] = $t;
			}

			// Replies
			if ( is_array( $rev_perms ) )
			{
				$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'r' => array( 'id', 'tid', 'mid', 'mname', 'message', 'date' ), 't' => array( 'did', 'dname', 'email', 'subject' ) ),
												  	 'from'		=> array( 'r' => 'replies' ),
												  	 'join'		=> array( array( 'from' => array( 't' => 'tickets' ), 'where' => array( 'r' => 'tid', '=', 't' => 'id' ) ) ),
								 				  	 'order'	=> array( 'date' => array( 'r' => 'DESC' ) ),
								 				  	 'where'	=> array( array( array( 't' => 'did' ), 'in', $rev_perms ), array( array( 'r' => 'staff' ), '!=', 1, 'and' ) ),
								 				  	 'limit'	=> array( 0, 15 ),
								 		  	  ) 	);

				$this->ifthd->core->db->execute();
			}
			else
			{
				$this->ifthd->core->db->construct( array(
												  	 'select'	=> array( 'r' => array( 'id', 'tid', 'mid', 'mname', 'message', 'date' ), 't' => array( 'did', 'dname', 'email', 'subject' ) ),
												  	 'from'		=> array( 'r' => 'replies' ),
												  	 'join'		=> array( array( 'from' => array( 't' => 'tickets' ), 'where' => array( 'r' => 'tid', '=', 't' => 'id' ) ) ),
								 				  	 'order'	=> array( 'date' => array( 'r' => 'DESC' ) ),
								 				  	 'where'	=> array( array( 'r' => 'staff' ), '!=', 1 ),
								 				  	 'limit'	=> array( 0, 15 ),
								 		  	  ) 	);

				$this->ifthd->core->db->execute();
			}

			$replies = array(); // Initialize for Security

			while ( $r = $this->ifthd->core->db->fetch_row() )
			{
				$r['type'] = 'reply';

				$rss_items[ $r['date'] ] = $r;
			}

			// Sort
			krsort( $rss_items );

			$count = 0;

			while ( list( $date, $data ) = each( $rss_items ) )
			{
				if ( $count > 14 ) break;

				if ( $data['type'] == 'ticket' )
				{
					$xml .= "<item>
								<title>". $data['subject'] ."</title>
								<link><![CDATA[ ". $this->ifthd->core->cache['config']['hd_url'] ."/admin.php?section=manage&act=tickets&code=view&id=". $data['id'] ." ]]></link>
								<guid><![CDATA[ ". $this->ifthd->core->cache['config']['hd_url'] ."/admin.php?section=manage&act=tickets&code=view&id=". $data['id'] ." ]]></guid>
								<category>". $data['dname'] ."</category>
								<pubDate>". gmdate( 'D, d M Y H:i:s', $data['date'] ) ." GMT</pubDate>
								<author>". $data['email'] ." (". $data['mname'] .")</author>
								<description><![CDATA[ ". $this->ifthd->prepare_output( $data['message'] ) ." ]]></description>
							</item>";
				}
				elseif ( $data['type'] == 'reply' )
				{
					$xml .= "<item>
								<title>". $data['subject'] ." (Reply)</title>
								<link><![CDATA[ ". $this->ifthd->core->cache['config']['hd_url'] ."/admin.php?section=manage&act=tickets&code=view&id=". $data['tid'] ."#reply". $data['id'] ." ]]></link>
								<guid><![CDATA[ ". $this->ifthd->core->cache['config']['hd_url'] ."/admin.php?section=manage&act=tickets&code=view&id=". $data['tid'] ."#reply". $data['id'] ." ]]></guid>
								<category>". $data['dname'] ."</category>
								<pubDate>". gmdate( 'D, d M Y H:i:s', $data['date'] ) ." GMT</pubDate>
								<author>". $data['email'] ." (". $data['mname'] .")</author>
								<description><![CDATA[ ". $this->ifthd->prepare_output( $data['message'] ) ." ]]></description>
							</item>";
				}

				$count ++;
			}
		}

		$xml .= "</channel>
				</rss>";

		if ( $handle = @fopen( HD_PATH .'core/tmp/'. $this->feed_file, 'w' ) )
		{
			fwrite( $handle, $xml );

			@fclose($handle);

			return $xml;
		}

		return FALSE;
	}

}
?>