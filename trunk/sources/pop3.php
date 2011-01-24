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
|    | Email Pipe :: Sources
#======================================================
*/

#=============================
# Safe and Secure
#=============================

ini_set( 'register_globals', 0 );

if ( function_exists('date_default_timezone_get') )
{
     date_default_timezone_set( date_default_timezone_get() );
}

if ( @ini_get( 'register_globals' ) )
{
	while ( list( $key, $value ) = each( $_REQUEST ) )
	{
		unset( $$key );
	}
}

#=============================
# Itsy Bitsy Stuff
#=============================

define( 'IN_HD' , 1 );

ini_set( 'display_errors', 1 );

#=============================
# Define Our Paths
#=============================

define( "HD_PATH", "../" );
define( 'HD_INC', HD_PATH ."includes/" );
define( 'HD_SRC', HD_PATH ."sources/" );
define( 'HD_SKIN', HD_PATH ."skin/" );

define( 'HD_DEBUG', false );

#=============================
# Main Class
#=============================

require_once HD_INC . "ifthd.php";
$ifthd = new ifthd(1);

$ifthd->load_lang('global');
$ifthd->load_lang('tickets');

#=============================
# Pre-Checks
#=============================

if ( ! $ifthd->core->cache['config']['allow_new_tickets'] ) exit();

#=============================
# Grab Incoming Email
#=============================

foreach( $ifthd->core->cache['depart'] as $d )
{
	if ( $d['email_pop3'] )
	{
		if ( $mbox = imap_open( "{". $d['pop3_host'] .":110/pop3/notls}INBOX", $d['pop3_user'], $d['pop3_pass'] ) )
		{		
			$MC = imap_check($mbox);
			
			$result = imap_fetch_overview( $mbox, "1:{$MC->Nmsgs}", 0 );
			
			foreach ( $result as $msg )
			{
				$email = array(); // Initialize for Security	
				
				$raw_email = imap_fetchbody( $mbox, $msg->msgno, NULL );
				
				imap_delete( $mbox, $msg->msgno );
			
				#=============================
				# Now the Fun Begins :D
				#=============================

				require_once( HD_INC .'class_mailparse.php' );
				$mailparse = new mailparse();
				$mailparse->ifthd =& $ifthd;
				
				$email = $mailparse->decode( $raw_email );

				$mailparse->process( $email );
			}
			
			imap_expunge( $mbox );
			
			imap_close( $mbox );
		}
	}
}

#=============================
# Update Stats
#=============================

$ifthd->r_ticket_stats(1);

#=============================
# Bye Bye
#=============================

$ifthd->core->shut_down_q();
$ifthd->shut_down();
$ifthd->core->shut_down();

?>