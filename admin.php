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
|    | Trellis Desk Admin Index
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
define( 'IN_HDA' , 1 );

#ini_set( 'display_errors', 1 );
error_reporting( E_ERROR | E_WARNING | E_PARSE );

ob_start("ob_gzhandler");

#=============================
# Define Our Paths
#=============================

define( "HD_PATH", "./" );
define( 'HD_INC', HD_PATH ."includes/" );
define( 'HD_SRC', HD_PATH ."sources/" );
define( 'HD_SKIN', HD_PATH ."skin/" );
define( 'HD_ADMIN', HD_PATH ."admin/" );

define( 'HD_DEBUG', false );

#=============================
# Main Class
#=============================

require_once HD_INC . "ifthd.php";
require_once HD_INC . "ifthd_admin.php";
$ifthd = new ifthd_admin();

#=============================
# Special
#=============================

if ( $ifthd->input['act'] == 'phpinfo' )
{
	phpinfo();

	exit();
}
elseif ( $ifthd->input['act'] == 'tdinfo' )
{
	$ifthd->tdinfo();

	exit();
}

#=============================
# Other Junk
#=============================

$choice = array(
				'admin'		=> array(
									 'home'			=> 'home',
									 'logs'			=> 'logs',
									),

				'manage'	=> array(
									 'announce'		=> 'announce',
									 'canned'		=> 'canned',
									 'cdfields'		=> 'cdfields',
									 'cpfields'		=> 'cpfields',
									 'depart'		=> 'depart',
									 'kb'			=> 'article',
									 'group'		=> 'group',
									 'kbcat'		=> 'article',
									 'member'		=> 'member',
									 'pages'		=> 'pages',
									 'reply'		=> 'tickets',
									 'settings'		=> 'settings',
									 'tickets'		=> 'tickets',
									),

				'look'		=> array(
									 'skin'			=> 'skin',
									 'lang'			=> 'lang',
									),

				'tools'		=> array(
									 'maint'		=> 'maint',
									 'backup'		=> 'backup',
									),
			   );

#=============================
# Require & Run
#=============================

$folder = $ifthd->input['section'];
$required = $choice[ $ifthd->input['section'] ][ $ifthd->input['act'] ];

if ( ! isset( $required ) )
{
	if ( $ifthd->input['section'] == 'manage' )
	{
		$folder = 'manage';
		$required = 'tickets';
	}
	elseif ( $ifthd->input['section'] == 'look' )
	{
		$folder = 'look';
		$required = 'skin';
	}
	elseif ( $ifthd->input['section'] == 'tools' )
	{
		$folder = 'tools';
		$required = 'maint';
	}
	else
	{
		$folder = 'admin';
		$required = 'home';
	}
}

if ( ! $ifthd->member['acp'][ $folder ] )
{
	$ifthd->skin->error('no_perm');
}

$required = "ad_". $required;

require_once HD_ADMIN . $folder ."/". $required .".php";

$run = new $required();
$run->ifthd =& $ifthd;

$run->auto_run();

?>