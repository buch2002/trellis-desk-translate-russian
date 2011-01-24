#!/usr/bin/php -q
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

$raw_email = "";
$email = array();

if ( $fd = @fopen( "php://stdin", 'r' ) )
{
	while ( ! feof( $fd ) )
	{
		$raw_email .= fread( $fd, 1024 );
	}

	@fclose( $fd );
}
else
{
	$ifthd->log( 'error', "Unable to Open Connection to Stdin" );

	$ifthd->core->shut_down_q();
	$ifthd->shut_down();
	$ifthd->core->shut_down();
}

#=============================
# Now the Fun Begins :D
#=============================

require_once( HD_INC .'class_mailparse.php' );
$mailparse = new mailparse();
$mailparse->ifthd =& $ifthd;

$email = $mailparse->decode( $raw_email );

$mailparse->process( $email );

$ifthd->core->shut_down_q();
$ifthd->shut_down();
$ifthd->core->shut_down();

?>