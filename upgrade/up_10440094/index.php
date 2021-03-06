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
|    | Upgrade 10440094
#======================================================
*/

class up_10440094 {

	var $u_ver_id = '10440094';
	var $u_ver_human = 'v1.0.4 Final';

	function auto_run()
	{
		$steps = array( 1 => 'Information', 2 => 'Run SQL Queries', 3 => 'Finish' );
		
		set_steps( 'Upgrade Trellis Desk', $steps );
		set_titles( $steps );	
		
		switch( $this->ifthd->input['step'] )
		{
			case 4:
				$this->step_4();
		    break;
			case 3:
				$this->step_3();
		    break;
			case 2:
				$this->step_2();
		    break;

			default:
				$this->step_1();
		    break;
		}
	}

	function step_1()
	{
		$content = "<div class='groupbox'>Upgrade to ". $this->u_ver_human ."</div>
					<div class='option1'>
						Please review the information below.  When you are ready to upgrade to Trellis Desk ". $this->u_ver_human .", click Continue.<br /><br />
						
						What's New? (Overview)<br />
						&rsaquo; Increased staff security<br />
						&rsaquo; Various Bugs Fixes
					</div>
					<div class='option1'>Don't forget to backup your files and databases.</div>
					<div class='formtail'><div class='fb_pad'><a href='index.php?do=". $this->u_ver_id ."&amp;step=2' class='fake_button'>Continue</a></div></div>";
		
		do_output( $content, 1 );
	}
	
	function step_2($error='')
	{		
		$content = "<div class='groupbox'>Update SQL Database</div>
					<div class='option1'>Click Continue to run the required SQL queries for the upgrade.</div>
					<div class='formtail'><div class='fb_pad'><a href='index.php?do=". $this->u_ver_id ."&amp;step=3' class='fake_button'>Continue</a></div></div>";
		
		do_output( $content, 2 );
	}

	function step_3()
	{
		$this->ukey = md5( $this->u_ver_id . time() . $this->m['id'] . uniqid( rand(), true ) );

		require_once "./up_". $this->u_ver_id ."/sql_queries.php";

		while ( list( , $sql_query ) = each( $SQL ) )
		{
			if ( ! mysql_query($sql_query) )
			{
				$this->step_3( "An error encountered while trying to run the following SQL Query.<br /><br />". $sql_query ."<br /><br />MySQL returned the following error.<br /><br />". mysql_error() ."<br /><br />". mysql_errno() );
			}
		}
		
		$this->ifthd->core->add_cache( 'upgrade', array( '10440094' => array( 'updated_old' => 0, 'new_skin_id' => 0) ) );
		
		$this->ifthd->core->add_cache( 'temp', array( 'vercheck_time' => 0 ) );
		
		$content = "<div class='groupbox'>Upgrade Complete</div>
					<div class='option1'>Congratulations, Trellis Desk has been successfully upgraded to ". $this->u_ver_human .".  Click the link below to return to Trellis Desk.</div>
					<div class='option2'><a href='http://docs.accord5.com/Whats_New' target='_blank'>To learn more about this upgrade, such as where new features are located and how to configure them, click here.</a></div>
					<div class='formtail'><div class='fb_pad'><a href='../index.php' class='fake_button'>Finish</a></div></div>";
		
		do_output( $content, 3 );
	}
}

?>