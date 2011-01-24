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
|    | Trellis Desk Installation Index
#======================================================
*/

ob_start();

#=============================
# Safe and Secure
#=============================

ini_set( 'register_globals', 0 );

if ( @ini_get( 'register_globals' ) )
{
	while ( list( $key, $value ) = each( $_REQUEST ) )
	{
		unset( $$key );
	}
}

$input = get_post();

#=============================
# Itsy Bitsy Stuff
#=============================

define( 'VER_NUM', '10440094' );
define( 'VER_HUM', 'v1.0.4 Final' );

error_reporting( E_ERROR | E_WARNING | E_PARSE );

#=============================
# Define Our Paths
#=============================

define( "HD_PATH", "../" );
define( 'HD_INC', HD_PATH ."includes/" );
define( 'HD_SRC', HD_PATH ."sources/" );
define( 'HD_SKIN', HD_PATH ."skin/" );

define( 'HD_DEBUG', false );

#=============================
# Security Check
#=============================

if ( file_exists( './install.lock' ) && ! $input['step'] != 8 && ! $input['submit'] )
{
	step_0('The installer has been locked.  Please delete the <i>install.lock</i> file before continuing.');
}

#=============================
# Load Core & Check Steps
#=============================

if ( $input['step'] > 1 )
{
	require_once HD_PATH ."core/ift.php";

	$core = new iftcore();
	
	if ( $input['step'] != 8 || $input['submit'] )
	{
		if ( ! $core->cache['install']['check_ran'] )
		{
			step_1('Hold on there!  We need to run a system check before you can jump to that step.');
		}
		
		if ( ! $core->cache['install']['sys_check'] )
		{
			step_1('You must correct all of the failed errors before continuing.');
		}
		
		if ( $input['step'] > 3 && ! $core->cache['install']['sql_connect'] && ! $input['submit'] )
		{
			step_3( 'Hold on there!  We need to connect to your database before you can jump to that step.', 1 );
		}
	}
	
	if ( ! $input['adv'] )
	{
		if ( $input['step'] == 7 || ( $input['step'] == 8 && $input['submit'] ) )
		{
			if ( ! $core->cache['install']['admin_set'] )
			{
				step_4( 'Hold on there!  You need to create your admin account before you can jump to that step.', 1 );
			}
			
			if ( ! $core->cache['install']['set_set'] && ! $input['submit'] )
			{
				step_5( 'Hold on there!  You need to configure your general settings before you can jump to that step.', 1 );
			}
			
			if ( ! $core->cache['install']['skin_set'] && ! $input['submit'] )
			{
				step_6( 'Hold on there!  We need to write your skin files before you can jump to that step.', 1 );
			}
		}
	}
}

if ( $input['step'] === 'a' )
{
	require_once HD_PATH ."core/ift.php";

	$core = new iftcore();
	
	if ( ! $core->cache['install']['check_ran'] )
	{
		step_1('Hold on there!  We need to run a system check before you can jump to that step.');
	}
		
	if ( ! $core->cache['install']['sys_check'] )
	{
		step_1('You must correct all of the failed errors before continuing.');
	}
}

#=============================
# Let's Get This Party Started
#=============================

switch( $input['step'] )
{
	case 1:
		step_1();
    break;
	case 2:
		step_2();
    break;
	case 3:
		step_3();
    break;
	case 4:
		step_4();
    break;
	case 5:
		step_5();
    break;
	case 6:
		step_6();
    break;
	case 7:
		step_7();
    break;
	case 8:
		step_8();
    break;
	case 'a':
		step_a();
    break;

	default:
		step_0();
    break;
}

function step_0($error='')
{
	$content = "";
	
	if ( $error ) $content .= "<div class='critical'>{$error}</div>";
	
	if ( ini_get('allow_url_fopen') )
	{
		$context = stream_context_create( array( 'http' => array( 'timeout'	=> 5 ) ) );
		
		$version_check = file_get_contents( 'http://core.accord5.com/trellis/version_check.php?type=text', null, $context );
		
		if ( intval( $version_check ) > VER_NUM )
		{
			$version_txt = "<span style='color:#D85C08'>There is a newer version of Trellis Desk available for <a href='http://www.accord5.com/trellis'>download</a>.  We recommended downloading the latest version before continuing the installation.</span>";
		}
		else
		{
			$version_txt = "It looks like you have the latest version of Trellis Desk.  To begin, click the button below.";
		}
	}
	else
	{
		$version_txt = "Due to your PHP's security settings, we were unable to check for the latest version of Trellis Desk.  We recommend checking the <a href='http://www.accord5.com/trellis'>Trellis Desk product page</a> to make sure you have the latest version.";
	}
	
	$content .= "<div class='groupbox'>About the Install Center</div>
				<div class='option1' style='font-weight: normal'><img src='../images/default/welcome.jpg' alt='Welcome to Trellis Desk by ACCORD5' /><br />Hello, and welcome to the Trellis Desk Install Center. The Install Center will guide you through the Trellis Desk installation process.  At any time, you can go back and make changes to previous steps.  On behalf of ACCORD5, we thank you for choosing and supporting Trellis Desk.</div>
				<br />
				<div class='groupbox'>Version Check</div>
				<div class='option1'>Your Version: ". VER_HUM ." (". VER_NUM .")<br /><br />". $version_txt ."</div>
				<div class='formtail'><div class='fb_pad'><a href='index.php?step=1' class='fake_button'>Let's Begin!</a></div></div>";
	
	do_output( $content, 0 );
}

function step_1($error='')
{
	$content = "";	
	$captcha = 0;					
	$config_found = 0;
	$uploads = 0;
	$chmod = 0;
	$fatal = 0;
	$sys_check = 0;
	
	$success = "<span style='color:#49701B'>Success</span>";
	$warning = "<span style='color:#D85C08'>Warning</span>";
	$failed = "<span style='color:#AC241A'>Failed</span>";
	$info_warning = "style='padding-top:8px;font-size:12px;'";
	$info_failed = "style='padding-top:8px;font-size:12px;color:#AC241A'";
	
	if ( $error ) $content .= "<div class='critical'>{$error}</div>";
	
	$content .= "<div class='groupbox'>Installed Software &amp; Configuration</div>
				<table width='100%' cellpadding='0' cellspacing='0'>
				<tr>
					<td class='option1' width='80%'>PHP Version (". PHP_VERSION .")</td>
					<td class='option1' width='20%' align='right'>";
	
	if ( version_compare( PHP_VERSION, '4.3', '>=' ) )
	{
		$content .= $success ."</td>
				</tr>";
	}
	else
	{
		$content .= $failed ."</td>
				</tr>
				<tr>
					<td class='infopop' colspan='2' {$info_failed}>Trellis Desk cannot be installed as it requires PHP version <b>4.3.0</b> or later.</td>
				</tr>";
		
		$fatal = 1;
	}
	
	$content .= "<tr>
					<td class='option1'>Safe Mode</td>
					<td class='option1' align='right'>";
	
	if( ini_get('safe_mode') )
	{
		$content .= $warning ."</td>
				</tr>
				<tr>
					<td class='infopop' colspan='2' {$info_warning}>Safe mode in PHP is enabled.  You can continue, however this may result in unexpected behavior with Trellis Desk.</td>
				</tr>";
	}
	else
	{
		$content .= $success ."</td>
				</tr>";
	}
	
	$memory_limit_bytes = return_bytes( ini_get('memory_limit') );
	$memory_limit_ini = ini_get('memory_limit');
	
	if ( $memory_limit_ini == '-1' )
	{
		$memory_limit = 'No Limit';
	}
	elseif ( $memory_limit_bytes )
	{
		$memory_limit = format_size( $memory_limit_bytes );
	}
	else
	{
		$memory_limit = 'No Limit';
	}
	
	$content .= "<tr>
					<td class='option1'>Memory Limit (". $memory_limit .")</td>
					<td class='option1' align='right'>";
	
	if( $memory_limit_ini && $memory_limit_ini != '-1' && $memory_limit_bytes < 5120 )
	{
		$content .= $warning ."</td>
				</tr>
				<tr>
					<td class='infopop' colspan='2' {$info_warning}>Your PHP's memory limit is set to ". $memory_limit .".  We recommend that this value is set to 5 MB or more.</td>
				</tr>";
	}
	else
	{
		$content .= $success ."</td>
				</tr>";
	}
	
	$content .= "<tr>
					<td class='option1'>GD Library</td>
					<td class='option1' align='right'>";
	
	if( extension_loaded('gd') )
	{
		$content .= $success ."</td>
				</tr>";
		
		$gd_info = gd_info();
	
		$content .= "<tr>
						<td class='option1'>FreeType Support</td>
						<td class='option1' align='right'>";
		
		if( $gd_info['FreeType Support'] )
		{
			$content .= $success ."</td>
					</tr>";
			
			$captcha = 1;
		}
		else
		{
			$content .= $warning ."</td>
					</tr>
					<tr>
						<td class='infopop' colspan='2' {$info_warning}>Your GD Library does not support FreeType, therefore security CAPTCHAs will be disabled.</td>
					</tr>";
		}
	}
	else
	{
		$content .= $warning ."</td>
				</tr>
				<tr>
					<td class='infopop' colspan='2' {$info_warning}>PHP could not load the GD Library, therefore security CAPTCHAs will be disabled.</td>
				</tr>";
	}
	
	$content .= "<tr>
					<td class='option1'>File Uploads</td>
					<td class='option1' align='right'>";
	
	if( ini_get('file_uploads') )
	{
		$content .= $success ."</td>
				</tr>";
		
		$uploads = 1;
		
		$upload_max_filesize_bytes = return_bytes( ini_get('upload_max_filesize') );
		$upload_max_filesize = format_size( $upload_max_filesize_bytes );
	
		$content .= "<tr>
						<td class='option1'>Maximum Upload Size (". $upload_max_filesize .")</td>
						<td class='option1' align='right'>";
		
		if( $upload_max_filesize_bytes >= 2097152 )
		{
			$content .= $success ."</td>
					</tr>";
		}
		else
		{
			$content .= $warning ."</td>
					</tr>
					<tr>
						<td class='infopop' colspan='2' {$info_warning}>PHP's maximum file upload size is set to ". $upload_max_filesize .".  For your convenience, we recommend that this value is set to at least 2 MB.</td>
					</tr>";
		}
		
		$post_max_size_bytes = return_bytes( ini_get('post_max_size') );
	
		$content .= "<tr>
						<td class='option1'>Maximum POST Size (". format_size( return_bytes( ini_get('post_max_size') ) ) .")</td>
						<td class='option1' align='right'>";
		
		if( $post_max_size_bytes > $upload_max_filesize_bytes )
		{
			$content .= $success ."</td>
					</tr>";
		}
		else
		{
			$content .= $warning ."</td>
					</tr>
					<tr>
						<td class='infopop' colspan='2' {$info_warning}>PHP's maximum POST size less than the maximum file upload size, therefore your file uploads will be limited to ". format_size( $post_max_size_bytes ) .".</td>
					</tr>";
		}
	}
	else
	{
		$content .= $warning ."</td>
				</tr>
				<tr>
					<td class='infopop' colspan='2' {$info_warning}>File uploads are not enabled in PHP, therefore you will not be able to upload attachments to Trellis Desk.</td>
				</tr>";
	}
	
	$content .= "</table>
				<br />
				<div class='groupbox'>File Permissions &amp; Config File</div>
				<table width='100%' cellpadding='0' cellspacing='0'>
				<tr>
					<td class='option1' width='80%'>Configuration File</td>
					<td class='option1' width='20%' align='right'>";
	
	if ( ! file_exists( HD_PATH .'config.php' ) )
	{
		if ( ! file_exists( HD_PATH .'config.php.dist') )
		{
			$content .= $failed ."</td>
					</tr>
					<tr>
						<td class='infopop' colspan='2' {$info_failed}>Trellis Desk could not locate your <i>config.php</i>.  Please upload <i>config.php.dist</i> and rename it to <i>config.php</i>.</td>
					</tr>";
		
			$fatal = 1;
		}
		else
		{
			if ( ! @rename( HD_PATH .'config.php.dist', HD_PATH .'config.php' ) )
			{
				$content .= $failed ."</td>
						</tr>
						<tr>
							<td class='infopop' colspan='2' {$info_failed}>Trellis Desk coult not rename <i>config.php.dist</i> for you. Please rename <i>config.php.dist</i> to <i>config.php</i>.</td>
						</tr>";
		
				$fatal = 1;
			}
			else
			{
				$config_found = 1;
			}
		}
	}
	else
	{
		$config_found = 1;
	}
	
	if ( $config_found )
	{
		if ( ! is_writable( HD_PATH .'config.php' ) )
		{
			@chmod( HD_PATH .'config.php', 0777 );
		}
		
		if ( ! is_writable( HD_PATH .'config.php' ) )
		{
			$content .= $failed ."</td>
			</tr>
			<tr>
				<td class='infopop' colspan='2' {$info_failed}>Trellis Desk does not have permission to write to <i>config.php</i>.  Please CHMOD this file to 0777.</td>
			</tr>";
		
			$fatal = 1;
		}
		else
		{
			$content .= $success ."</td>
					</tr>";
		}
	}
	
	$files = array(
					'core/cache'		=> array( 'Cache Folder', 0777 ),
					'core/tmp'			=> array( 'Temp Folder', 0777 ),
					'skin'				=> array( 'Skin Folder', 0777 ),
				 );
	
	if ( $uploads ) $files['uploads'] = array( 'Uploads Folder', 0777 );

	while ( list( $ck_file, $ck_perm ) = each( $files ) )
	{
		$content .= "<tr>
						<td class='option1'>". $ck_perm[0] ."</td>
						<td class='option1' align='right'>";
		
		if ( ! is_writable( HD_PATH . $ck_file ) )
		{
			@chmod( HD_PATH . $ck_file, $ck_perm[1] );
		}
		
		if ( ! is_writable( HD_PATH . $ck_file ) )
		{
			$content .= $failed ."</td>
					</tr>
					<tr>
						<td class='infopop' colspan='2' {$info_failed}>Trellis Desk does not have permission to write to <i>". $ck_file ."</i>.  Please CHMOD this file/folder to 0777.</td>
					</tr>";
				
			$fatal = 1;
		}
		else
		{
			$content .= $success ."</td>
					</tr>";
		}
	}
	
	$content .= "<tr>
					<td class='option1'>Install Folder</td>
					<td class='option1' align='right'>";
	
	if( ! is_writable( './') )
	{
		$content .= $warning ."</td>
				</tr>
				<tr>
					<td class='infopop' colspan='2' {$info_warning}>This <i>install</i> directory is not writeable, therefore the Install Center will be unable to create a lock file when installation is complete.  The lock file prevents the installer from being run twice and is strongly recommended for security purposes.  We recommend you CHMOD this <i>install</i> directory to 0777, however you may continue without doing so.</td>
				</tr>";
	}
	else
	{
		$content .= $success ."</td>
				</tr>";
	}
	
	if ( $fatal )
	{
		$button = "<a href='index.php?step=1' class='fake_button'>Try Again</a>";
	}
	else
	{
		$button = "<a href='index.php?step=2' class='fake_button'>Continue</a>";
		$sys_check = 1;
	}
	
	if ( is_writable( HD_PATH .'core/cache' ) )
	{
		require_once HD_PATH ."core/ift.php";

		$core = new iftcore();
		
		$to_cache = array(
						  'sys_check'	=> $sys_check,
						  'check_ran'	=> 1,
						  'captcha'		=> $captcha,
						  'uploads'		=> $uploads,
						  );
		
		$core->add_cache( 'install', $to_cache );
		
		$core->shut_down();
	}
	
	$content .= "</table>
				<div class='formtail'><div class='fb_pad'>{$button}</div></div>";
	
	do_output( $content, 1 );
}

function step_2()
{
	global $core;	
	
	$content = "<div class='groupbox'>Guided or Advanced</div>
				<div class='option1' style='font-weight: normal'>The Install Center offers you two ways to complete your Trellis Desk installation.  You can choose to continue with the guided installation, or select the advanced option if you are a more experienced user.  The guided installation will take you through six more steps, each one with detailed instructions.  Advanced will allow you to finish the installation in just one more step, while collecting only the necessary information to complete the installation.  Please make a selection below.</div>
				<div class='formtail'><div class='fb_pad'><a href='index.php?step=a' class='fake_button'>Advanced</a>&nbsp;<a href='index.php?step=3' class='fake_button'>Continue with Guided</a></div></div>";
	
	do_output( $content, 2 );
}

function step_3($error='', $bypass=0)
{
	global $core;
	
	$content = "";
	
	if ( $error )
	{
		$content .= "<div class='critical'>{$error}</div>";
	}
	
	if ( ! $error || ( $error && $bypass ) )
	{
		if ( ! $_POST['sql_host'] ) $_POST['sql_host'] = 'localhost';
		if ( ! $_POST['sql_port'] ) $_POST['sql_port'] = '3306';
		if ( ! $_POST['sql_prefix'] ) $_POST['sql_prefix'] = 'td_';
	}
	
	if ( $core->cache['install']['sql_connect'] && ! $error )
	{
		$_POST['sql_host'] = $core->cache['install']['sql_host'];
		$_POST['sql_port'] = $core->cache['install']['sql_port'];
		$_POST['sql_user'] = $core->cache['install']['sql_user'];
		$_POST['sql_db'] = $core->cache['install']['sql_db'];
		$_POST['sql_prefix'] = $core->cache['install']['sql_prefix'];
		
		$content .= "<div class='alert'>Trellis Desk already has your database credentials stored.  If you would like to change them, edit the information below and click Continue.  Otherwise, <a href='index.php?step=4'>click here</a>.</div>";
	}
	
	$content .= "<form action='index.php?step=4' method='post'>
				<div class='groupbox'>Database Credentials</div>
				<div class='option1'>Trellis Desk needs a <a href='http://www.mysql.com/' target='_blank'>MySQL database</a> to store your data such as members and tickets.  Please enter your database credentials below.  If you are unsure about this information, contact your hosting provider.</div>
				<table width='100%' cellpadding='0' cellspacing='0'>
				<tr>
					<td class='option2' width='25%'>MySQL Host</td>
					<td class='option2' width='75%'><input type='text' name='sql_host' id='sql_host' value='{$_POST['sql_host']}' size='40' /></td>
				</tr>
				<tr>
					<td colspan='2'>									
						<div class='infopop'>
							<a onclick=\"javascript:Effect.toggle('info1','blind',{duration: 0.5});\" class='fake_link'><img src='../images/default/toggle.gif' alt='+' /> Toggle information</a>
							<div id='info1' style='display: none;'>
							<div>
								The MySQL Host is the location of your MySQL server.  This can be a domain or IP address.  If your MySQL service is located on this machine, as it is in most cases, you can leave this at <i>localhost</i>.
							</div>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td class='option1'>MySQL Port</td>
					<td class='option1'><input type='text' name='sql_port' id='sql_user' value='{$_POST['sql_port']}' size='5' /></td>
				</tr>
				<tr>
					<td colspan='2'>									
						<div class='infopop'>
							<a onclick=\"javascript:Effect.toggle('info7','blind',{duration: 0.5});\" class='fake_link'><img src='../images/default/toggle.gif' alt='+' /> Toggle information</a>
							<div id='info7' style='display: none;'>
							<div>
								The MySQL port your MySQL service is running on.  Leave blank to use default (3306).
							</div>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td class='option1'>MySQL Username</td>
					<td class='option1'><input type='text' name='sql_user' id='sql_user' value='{$_POST['sql_user']}' size='40' /></td>
				</tr>
				<tr>
					<td colspan='2'>									
						<div class='infopop'>
							<a onclick=\"javascript:Effect.toggle('info2','blind',{duration: 0.5});\" class='fake_link'><img src='../images/default/toggle.gif' alt='+' /> Toggle information</a>
							<div id='info2' style='display: none;'>
							<div>
								This is your MySQL username used to connect to your MySQL Server.  This user must have SELECT, CREATE, INSERT, ALTER, UPDATE, DROP, DELETE, and INDEX permissions to the MySQL database (see below).
							</div>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td class='option2'>MySQL Password</td>
					<td class='option2'><input type='password' name='sql_pass' id='sql_pass' value='' size='40' /></td>
				</tr>
				<tr>
					<td colspan='2'>									
						<div class='infopop'>
							<a onclick=\"javascript:Effect.toggle('info3','blind',{duration: 0.5});\" class='fake_link'><img src='../images/default/toggle.gif' alt='+' /> Toggle information</a>
							<div id='info3' style='display: none;'>
							<div>
								Simply your password for the MySQL username you entered above.
							</div>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td class='option1'>Password Confirm</td>
					<td class='option1'><input type='password' name='sql_pass_b' id='sql_pass_b' value='' size='40' /></td>
				</tr>
				<tr>
					<td colspan='2'>									
						<div class='infopop'>
							<a onclick=\"javascript:Effect.toggle('info4','blind',{duration: 0.5});\" class='fake_link'><img src='../images/default/toggle.gif' alt='+' /> Toggle information</a>
							<div id='info4' style='display: none;'>
							<div>
								No, we are not doing this just for fun; it's for security purposes!  Please enter the same password you just entered above.
							</div>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td class='option2'>MySQL Database</td>
					<td class='option2'><input type='text' name='sql_db' id='sql_db' value='{$_POST['sql_db']}' size='40' /></td>
				</tr>
				<tr>
					<td colspan='2'>									
						<div class='infopop'>
							<a onclick=\"javascript:Effect.toggle('info5','blind',{duration: 0.5});\" class='fake_link'><img src='../images/default/toggle.gif' alt='+' /> Toggle information</a>
							<div id='info5' style='display: none;'>
							<div>
								This is the name of the MySQL database where your Trellis Desk data will be stored.  The MySQL user entered above must have permission to access this database.
							</div>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td class='option1'>MySQL Table Prefix</td>
					<td class='option1'><input type='text' name='sql_prefix' id='sql_prefix' value='{$_POST['sql_prefix']}' size='4' /></td>
				</tr>
				<tr>
					<td colspan='2'>									
						<div class='infopop'>
							<a onclick=\"javascript:Effect.toggle('info6','blind',{duration: 0.5});\" class='fake_link'><img src='../images/default/toggle.gif' alt='+' /> Toggle information</a>
							<div id='info6' style='display: none;'>
							<div>
								The MySQL Table Prefix is the prefix Trellis Desk will place in front of all its table names.  This is especially useful for storing multiple Trellis Desk installations in the same database.  If you are unclear on what this does, it's best to leave it at the default, <i>td_</i>.
							</div>
							</div>
						</div>
					</td>
				</tr>
				</table>
				<div class='formtail'><input type='submit' name='submit' id='continue_button' value='Continue' class='button' /></div>
				</form>";
	
	do_output( $content, 3 );
}

function step_4($error='', $bypass=0)
{
	global $core, $input;
	
	if ( $input['submit'] && ! $error )
	{	
		if ( $_POST['sql_pass'] != $_POST['sql_pass_b'] )
		{
			step_3('Your MySQL passwords do not match.');
		}
		
		if ( ! $_POST['sql_port'] ) $_POST['sql_port'] = '3306';
		
		if ( ! @mysql_connect( $_POST['sql_host'] .':'. $_POST['sql_port'], $_POST['sql_user'], $_POST['sql_pass'] ) )
		{
			step_3('We could not connect to the MySQL Server.  Please check that your MySQL credentails are correct and try again.');
		}
	
		$mysql_ver = mysql_get_server_info();
	
		if ( strpos( $mysql_ver, '-' ) )
		{
			$mysql_ver = substr( $mysql_ver, 0, strpos( $mysql_ver, '-' ) );
		}
	
		if ( version_compare( $mysql_ver, '4.1', '<' ) )
		{
			step_3('Sorry, Trellis Desk cannot be installed as it requires MySQL version 4.1 or later.');
		}
	
		if ( ! @mysql_select_db( $_POST['sql_db'] ) )
		{
			step_3('We could not connect to the MySQL Database.  Please check that your MySQL credentails are correct and try again.');
		}
		
		$tables = array();
		$sql_exists = 0;
		
		if ( $tables_sql = @mysql_query('SHOW TABLES') )
		{
			while( $tables_result = mysql_fetch_array( $tables_sql ) )
			{
				$tables[ $tables_result[0] ] = 1;
			}
		}
		
		if ( $tables[ $_POST['sql_db'] .'upg_history'] ) $sql_exists = 1;
		
		$to_cache = array(
						  'sql_connect'	=> 1,
						  'sql_host'	=> $_POST['sql_host'],
						  'sql_user'	=> $_POST['sql_user'],
						  'sql_pass'	=> $_POST['sql_pass'],
						  'sql_db'		=> $_POST['sql_db'],
						  'sql_prefix'	=> $_POST['sql_prefix'],
						  'sql_exists'	=> $sql_exists,
						  );
		
		$core->add_cache( 'install', $to_cache );
	}
	
	$content = "";
	
	if ( $error ) 
	{
		$content .= "<div class='critical'>{$error}</div>";
	}
	
	if ( ! $error || ( $error && $bypass ) )
	{
		if ( ! $input['admin_dst_active'] ) $input['admin_dst_active'] = 0;
		if ( ! $input['admin_use_rte'] ) $input['admin_use_rte'] = 1;
		if ( ! $input['admin_email_ticket'] ) $input['admin_email_ticket'] = 1;
		if ( ! $input['admin_email_reply'] ) $input['admin_email_reply'] = 1;
	}
	
	if ( $core->cache['install']['admin_set'] && ! $error )
	{
		$input['admin_user'] = $core->cache['install']['admin_user'];
		$input['admin_email'] = $core->cache['install']['admin_email'];
		$input['admin_time_zone'] = $core->cache['install']['admin_time_zone'];
		$input['admin_dst_active'] = $core->cache['install']['admin_dst_active'];
		$input['admin_use_rte'] = $core->cache['install']['admin_use_rte'];
		$input['admin_email_ticket'] = $core->cache['install']['admin_email_ticket'];
		$input['admin_email_reply'] = $core->cache['install']['admin_email_reply'];
		
		$content .= "<div class='alert'>Trellis Desk already has your admin account credentials stored.  If you would like to change them, edit the information below and click Continue.  Otherwise, <a href='index.php?step=5'>click here</a>.</div>";
	}
	
	( $input['admin_dst_active'] ) ? $dst_select1 = " checked='checked'" : $dst_select0 = " checked='checked'";
	( $input['admin_use_rte'] ) ? $rte_select1 = " checked='checked'" : $rte_select0 = " checked='checked'";
	
	if ( $input['admin_email_ticket'] ) $email_ticket_select1 = " checked='checked'";
	if ( $input['admin_email_reply'] ) $email_reply_select1 = " checked='checked'";
	
	$content .= "<form action='index.php?step=5' method='post'>
				<div class='groupbox'>Your Admin Account Credentials</div>
				<div class='option1'>You will now create your admin account for your Trellis Desk installation.  This account will be used to access the Administrator Control Panel and has the highest level of access.  Please choose these credentials carefully.</div>
				<table width='100%' cellpadding='0' cellspacing='0'>
				<tr>
					<td class='option2' width='25%'>Username</td>
					<td class='option2' width='75%'><input type='text' name='admin_user' id='admin_user' value='{$input['admin_user']}' size='40' /></td>
				</tr>
				<tr>
					<td colspan='2'>									
						<div class='infopop'>
							<a onclick=\"javascript:Effect.toggle('info1','blind',{duration: 0.5});\" class='fake_link'><img src='../images/default/toggle.gif' alt='+' /> Toggle information</a>
							<div id='info1' style='display: none;'>
							<div>
								This is the username that you will use to login to the Administrator Control Panel.
							</div>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td class='option1'>Password</td>
					<td class='option1'><input type='password' name='admin_pass' id='admin_pass' value='' size='40' /></td>
				</tr>
				<tr>
					<td colspan='2'>									
						<div class='infopop'>
							<a onclick=\"javascript:Effect.toggle('info2','blind',{duration: 0.5});\" class='fake_link'><img src='../images/default/toggle.gif' alt='+' /> Toggle information</a>
							<div id='info2' style='display: none;'>
							<div>
								This is your password that you will use, along with your username, to login to the Administrator Control Panel.
							</div>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td class='option2'>Confirm Password</td>
					<td class='option2'><input type='password' name='admin_pass_b' id='admin_pass_b' value='' size='40' /></td>
				</tr>
				<tr>
					<td colspan='2'>									
						<div class='infopop'>
							<a onclick=\"javascript:Effect.toggle('info3','blind',{duration: 0.5});\" class='fake_link'><img src='../images/default/toggle.gif' alt='+' /> Toggle information</a>
							<div id='info3' style='display: none;'>
							<div>
								Please confirm your password for security.  Be sure not to forget it!
							</div>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td class='option1'>Email Address</td>
					<td class='option1'><input type='text' name='admin_email' id='admin_email' value='{$input['admin_email']}' size='40' /></td>
				</tr>
				<tr>
					<td colspan='2'>									
						<div class='infopop'>
							<a onclick=\"javascript:Effect.toggle('info4','blind',{duration: 0.5});\" class='fake_link'><img src='../images/default/toggle.gif' alt='+' /> Toggle information</a>
							<div id='info4' style='display: none;'>
							<div>
								Your email address that you wish to be attached to your admin account.  This email address will receive email notifications from Trellis Desk for events such as a new ticket submission, etc.  You can manage your email notification preferences in My Account after the installation is complete.
							</div>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td class='option1'>Time Zone</td>
					<td class='option1'><select name='admin_time_zone' id='admin_time_zone'>". build_time_zone_drop( $input['admin_time_zone'] ) ."</select></td>
				</tr>
				<tr>
					<td colspan='2'>									
						<div class='infopop'>
							<a onclick=\"javascript:Effect.toggle('info5','blind',{duration: 0.5});\" class='fake_link'><img src='../images/default/toggle.gif' alt='+' /> Toggle information</a>
							<div id='info5' style='display: none;'>
							<div>
								Please select your time zone, regardless of Daylight Savings (you will select that below).
							</div>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td class='option1'>DST Active</td>
					<td class='option1' style='font-weight: normal'>
						<label for='dst_active1'><input type='radio' name='admin_dst_active' id='dst_active1' value='1' class='radio'{$dst_select1} /> Yes</label>&nbsp;&nbsp;<label for='dst_active0'><input type='radio' name='admin_dst_active' id='dst_active0' value='0' class='radio'{$dst_select0} /> No</label>
					</td>
				</tr>
				<tr>
					<td colspan='2'>									
						<div class='infopop'>
							<a onclick=\"javascript:Effect.toggle('info6','blind',{duration: 0.5});\" class='fake_link'><img src='../images/default/toggle.gif' alt='+' /> Toggle information</a>
							<div id='info6' style='display: none;'>
							<div>
								Please select Yes if Daylight Saving Time is currently active in your time zone.
							</div>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td class='option2'>Rich Text Editor</td>
					<td class='option2' style='font-weight: normal'>
						<label for='use_rte1'><input type='radio' name='admin_use_rte' id='use_rte1' value='1' class='radio'{$rte_select1} /> Enabled</label>&nbsp;&nbsp;<label for='use_rte0'><input type='radio' name='admin_use_rte' id='use_rte0' value='0' class='radio'{$rte_select0} /> Disabled</label>
					</td>
				</tr>
				<tr>
					<td colspan='2'>									
						<div class='infopop'>
							<a onclick=\"javascript:Effect.toggle('info7','blind',{duration: 0.5});\" class='fake_link'><img src='../images/default/toggle.gif' alt='+' /> Toggle information</a>
							<div id='info7' style='display: none;'>
							<div>
								Select whether or not you would like to use the rich text editor. The rich text editor allows you to apply text styles such as bold to ticket replies, etc.
							</div>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td class='option2'>Email Notifications</td>
					<td class='option2' style='font-weight: normal'>
						<label for='email_staff_new_ticket1'><input type='checkbox' name='admin_email_ticket' id='email_staff_new_ticket1' value='1' class='ckbox'{$email_ticket_select1} /> New Tickets in My Departments</label>
						<div style='margin-top:3px'><label for='email_staff_ticket_reply1'><input type='checkbox' name='admin_email_reply' id='email_staff_ticket_reply1' value='1' class='ckbox'{$email_reply_select1} /> New Replies in My Departments</label></div>
					</td>
				</tr>
				<tr>
					<td colspan='2'>									
						<div class='infopop'>
							<a onclick=\"javascript:Effect.toggle('info8','blind',{duration: 0.5});\" class='fake_link'><img src='../images/default/toggle.gif' alt='+' /> Toggle information</a>
							<div id='info8' style='display: none;'>
							<div>
								Select the event types for which you would like to receive email notifications for.  For example, if you selecet New Tickets in My Departments, you will receive an email notification for every new ticket created in your departments.
							</div>
							</div>
						</div>
					</td>
				</tr>
				</table>
				<div class='formtail'><input type='submit' name='submit' id='continue_button' value='Continue' class='button' /></div>
				</form>";
	
	do_output( $content, 4 );
}

function step_5($error='', $bypass=0)
{
	global $core, $input;
	
	if ( $input['submit'] && ! $error )
	{
		if ( ! $input['admin_user'] )
		{
			step_4('Please enter a username.');
		}
		
		if ( $input['admin_pass'] != $input['admin_pass_b'] )
		{
			step_4('Your passwords do not match.');
		}
		
		if ( ! validate_email( $input['admin_email'] ) )
		{
			step_4('Please enter a valid email address.');
		}
		
		$pass_salt = substr( md5( 'ps'. uniqid( rand(), true ) ), 0, 9 );
		$pass_hash = sha1( md5( $input['admin_pass'] . $pass_salt ) );
		$login_key = str_replace( "=", "", base64_encode( strrev( crypt( $input['admin_pass'] ) ) ) );
		$rss_key = md5( 'rk'. uniqid( rand(), true ) );
		
		$to_cache = array(
						  'admin_set'			=> 1,
						  'admin_user'			=> $input['admin_user'],
						  'admin_pass_hash'		=> $pass_hash,
						  'admin_pass_salt'		=> $pass_salt,
						  'admin_login_key'		=> $login_key,
						  'admin_email'			=> $input['admin_email'],
						  'admin_time_zone'		=> $input['admin_time_zone'],
						  'admin_dst_active'	=> $input['admin_dst_active'],
						  'admin_use_rte'		=> $input['admin_use_rte'],
						  'admin_email_ticket'	=> $input['admin_email_ticket'],
						  'admin_email_reply'	=> $input['admin_email_reply'],
						  'admin_rss_key'		=> $rss_key,
						  );
		
		$core->add_cache( 'install', $to_cache );
	}
	
	$content = "";
	
	if ( $error )
	{
		$content .= "<div class='critical'>{$error}</div>";
	}
	
	if ( ! $error || ( $error && $bypass ) )
	{
		if ( ! $core->cache['install']['admin_email'] ) $core->cache['install']['admin_email'] = $input['admin_email'];
		
		if ( ! $input['set_hd_name'] ) $input['set_hd_name'] = 'Trellis Desk';	
		if ( ! $input['set_out_email'] ) $input['set_out_email'] = $core->cache['install']['admin_email'];
		if ( ! $input['set_email_val'] ) $input['set_email_val'] = 1;
		if ( ! $input['set_admin_val'] ) $input['set_admin_val'] = 0;
		if ( ! $input['set_tickets_suggest'] ) $input['set_tickets_suggest'] = 1;
		if ( ! $input['set_news_comments'] ) $input['set_news_comments'] = 1;
		if ( ! $input['set_allow_kb_comment'] ) $input['set_allow_kb_comment'] = 1;
		if ( ! $input['set_allow_kb_rating'] ) $input['set_allow_kb_rating'] = 1;
		if ( ! $input['set_allow_reply_rating'] ) $input['set_allow_reply_rating'] = 1;
	}
	
	if ( $core->cache['install']['set_set'] && ! $error )
	{
		$input['set_hd_name'] = $core->cache['install']['set_hd_name'];
		$input['set_out_email'] = $core->cache['install']['set_out_email'];
		$input['set_email_val'] = $core->cache['install']['set_email_val'];
		$input['set_admin_val'] = $core->cache['install']['set_admin_val'];
		$input['set_tickets_suggest'] = $core->cache['install']['set_tickets_suggest'];
		$input['set_news_comments'] = $core->cache['install']['set_news_comments'];
		$input['set_allow_kb_comment'] = $core->cache['install']['set_allow_kb_comment'];
		$input['set_allow_kb_rating'] = $core->cache['install']['set_allow_kb_rating'];
		$input['set_allow_reply_rating'] = $core->cache['install']['set_allow_reply_rating'];
		
		$content .= "<div class='alert'>Trellis Desk already has your settings stored.  If you would like to change them, edit the information below and click Continue.  Otherwise, <a href='index.php?step=6'>click here</a>.</div>";
	}
	
	( $input['set_email_val'] ) ? $email_val_select1 = " checked='checked'" : $email_val_select0 = " checked='checked'";
	( $input['set_admin_val'] ) ? $admin_val_select1 = " checked='checked'" : $admin_val_select0 = " checked='checked'";
	( $input['set_tickets_suggest'] ) ? $tickets_suggest_select1 = " checked='checked'" : $tickets_suggest_select0 = " checked='checked'";
	( $input['set_news_comments'] ) ? $news_comments_select1 = " checked='checked'" : $news_comments_select0 = " checked='checked'";
	( $input['set_allow_kb_comment'] ) ? $kb_comments_select1 = " checked='checked'" : $kb_comments_select0 = " checked='checked'";
	( $input['set_allow_kb_rating'] ) ? $kb_rating_select1 = " checked='checked'" : $kb_rating_select0 = " checked='checked'";
	( $input['set_allow_reply_rating'] ) ? $ticket_rating_select1 = " checked='checked'" : $ticket_rating_select0 = " checked='checked'";
	
	$content .= "<form action='index.php?step=6' method='post'>
				<div class='groupbox'>System Settings</div>
				<div class='option1'>Below you can configure common settings for Trellis Desk.  It is safe to skip this step by leaving the settings at their default values and clicking Continue.</div>
				<table width='100%' cellpadding='0' cellspacing='0'>
				<tr>
					<td class='option2' width='30%'>Help Desk Name</td>
					<td class='option2' width='70%'><input type='text' name='set_hd_name' id='set_hd_name' value='{$input['set_hd_name']}' size='40' /></td>
				</tr>
				<tr>
					<td colspan='2'>									
						<div class='infopop'>
							<a onclick=\"javascript:Effect.toggle('info1','blind',{duration: 0.5});\" class='fake_link'><img src='../images/default/toggle.gif' alt='+' /> Toggle information</a>
							<div id='info1' style='display: none;'>
							<div>
								This is the name of your help desk system. It's used when relating to this system.  For example, you could use <i>ACCORD5 Customer Service</i>.
							</div>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td class='option1'>Outgoing Email</td>
					<td class='option1'><input type='text' name='set_out_email' id='set_out_email' value='{$input['set_out_email']}' size='40' /></td>
				</tr>
				<tr>
					<td colspan='2'>									
						<div class='infopop'>
							<a onclick=\"javascript:Effect.toggle('info2','blind',{duration: 0.5});\" class='fake_link'><img src='../images/default/toggle.gif' alt='+' /> Toggle information</a>
							<div id='info2' style='display: none;'>
							<div>
								This email will be used when Trellis Desk sends emails to your users (From header).
							</div>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td class='option2'>Require Email Validation</td>
					<td class='option2' style='font-weight: normal'>
						<label for='email_validation1'><input type='radio' name='set_email_val' id='email_validation1' value='1' class='radio'{$email_val_select1} /> Yes</label>&nbsp;&nbsp;<label for='email_validation0'><input type='radio' name='set_email_val' id='email_validation0' value='0' class='radio'{$email_val_select0} /> No</label>
					</td>
				</tr>
				<tr>
					<td colspan='2'>									
						<div class='infopop'>
							<a onclick=\"javascript:Effect.toggle('info3','blind',{duration: 0.5});\" class='fake_link'><img src='../images/default/toggle.gif' alt='+' /> Toggle information</a>
							<div id='info3' style='display: none;'>
							<div>
								If set to yes, users will be required to verify their email before being placed in the members group.
							</div>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td class='option1'>Require Admin Validation</td>
					<td class='option1' style='font-weight: normal'>
						<label for='admin_validation1'><input type='radio' name='set_admin_val' id='admin_validation1' value='1' class='radio'{$admin_val_select1} /> Yes</label>&nbsp;&nbsp;<label for='admin_validation0'><input type='radio' name='set_admin_val' id='admin_validation0' value='0' class='radio'{$admin_val_select0} /> No</label>
					</td>
				</tr>
				<tr>
					<td colspan='2'>									
						<div class='infopop'>
							<a onclick=\"javascript:Effect.toggle('info4','blind',{duration: 0.5});\" class='fake_link'><img src='../images/default/toggle.gif' alt='+' /> Toggle information</a>
							<div id='info4' style='display: none;'>
							<div>
								If set to yes, an administrator must approve all new accounts before they are moved into the members group.
							</div>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td class='option2'>Enable KB Suggestions</td>
					<td class='option2' style='font-weight: normal'>
						<label for='tickets_suggest1'><input type='radio' name='set_tickets_suggest' id='tickets_suggest1' value='1' class='radio'{$tickets_suggest_select1} /> Yes</label>&nbsp;&nbsp;<label for='tickets_suggest0'><input type='radio' name='set_tickets_suggest' id='tickets_suggest0' value='0' class='radio'{$tickets_suggest_select0} /> No</label>
					</td>
				</tr>
				<tr>
					<td colspan='2'>									
						<div class='infopop'>
							<a onclick=\"javascript:Effect.toggle('info5','blind',{duration: 0.5});\" class='fake_link'><img src='../images/default/toggle.gif' alt='+' /> Toggle information</a>
							<div id='info5' style='display: none;'>
							<div>
								If enabled, Trellis Desk will search the knowledge base for articles that might answer the users' inquiry before the ticket is sent.
							</div>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td class='option1'>Enable News Commenting</td>
					<td class='option1' style='font-weight: normal'>
						<label for='news_comments1'><input type='radio' name='set_news_comments' id='news_comments1' value='1' class='radio'{$news_comments_select1} /> Yes</label>&nbsp;&nbsp;<label for='news_comments0'><input type='radio' name='set_news_comments' id='news_comments0' value='0' class='radio'{$news_comments_select0} /> No</label>
					</td>
				</tr>
				<tr>
					<td colspan='2'>									
						<div class='infopop'>
							<a onclick=\"javascript:Effect.toggle('info6','blind',{duration: 0.5});\" class='fake_link'><img src='../images/default/toggle.gif' alt='+' /> Toggle information</a>
							<div id='info6' style='display: none;'>
							<div>
								If set to yes, members will be able to comment on announcements. (Per group permission are also be applied).
							</div>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td class='option2'>Enable KB Commenting</td>
					<td class='option2' style='font-weight: normal'>
						<label for='allow_kb_comment1'><input type='radio' name='set_allow_kb_comment' id='allow_kb_comment1' value='1' class='radio'{$kb_comments_select1} /> Yes</label>&nbsp;&nbsp;<label for='allow_kb_comment0'><input type='radio' name='set_allow_kb_comment' id='allow_kb_comment0' value='0' class='radio'{$kb_comments_select0} /> No</label>
					</td>
				</tr>
				<tr>
					<td colspan='2'>									
						<div class='infopop'>
							<a onclick=\"javascript:Effect.toggle('info7','blind',{duration: 0.5});\" class='fake_link'><img src='../images/default/toggle.gif' alt='+' /> Toggle information</a>
							<div id='info7' style='display: none;'>
							<div>
								If set to yes, members will be able to comment on knowledge base articles. (Per group permission are also be applied).
							</div>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td class='option1'>Enable KB Rating</td>
					<td class='option1' style='font-weight: normal'>
						<label for='allow_kb_rating1'><input type='radio' name='set_allow_kb_rating' id='allow_kb_rating1' value='1' class='radio'{$kb_rating_select1} /> Yes</label>&nbsp;&nbsp;<label for='allow_kb_rating0'><input type='radio' name='set_allow_kb_rating' id='allow_kb_rating0' value='0' class='radio'{$kb_rating_select0} /> No</label>
					</td>
				</tr>
				<tr>
					<td colspan='2'>									
						<div class='infopop'>
							<a onclick=\"javascript:Effect.toggle('info8','blind',{duration: 0.5});\" class='fake_link'><img src='../images/default/toggle.gif' alt='+' /> Toggle information</a>
							<div id='info8' style='display: none;'>
							<div>
								If set to yes, members will be able to rate knowledge base articles. (Per group permission are also be applied).
							</div>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td class='option2'>Enable Staff Reply Rating</td>
					<td class='option2' style='font-weight: normal'>
						<label for='allow_reply_rating1'><input type='radio' name='set_allow_reply_rating' id='allow_reply_rating1' value='1' class='radio'{$ticket_rating_select1} /> Yes</label>&nbsp;&nbsp;<label for='allow_reply_rating0'><input type='radio' name='set_allow_reply_rating' id='allow_reply_rating0' value='0' class='radio'{$ticket_rating_select0} /> No</label>
					</td>
				</tr>
				<tr>
					<td colspan='2'>									
						<div class='infopop'>
							<a onclick=\"javascript:Effect.toggle('info9','blind',{duration: 0.5});\" class='fake_link'><img src='../images/default/toggle.gif' alt='+' /> Toggle information</a>
							<div id='info9' style='display: none;'>
							<div>
								If set to yes, members will be able to rate staff replies to tickets as a positive (helpful) or negative (not-so-helpful) response.
							</div>
							</div>
						</div>
					</td>
				</tr>
				</table>
				<div class='formtail'><input type='submit' name='submit' id='continue_button' value='Continue' class='button' /></div>
				</form>";
	
	do_output( $content, 5 );
}

function step_6($error='')
{
	global $core, $input;
	
	if ( $input['submit'] && ! $error )
	{
		if ( ! $input['set_hd_name'] )
		{
			step_5('Please enter a help desk name.');
		}
		
		if ( ! validate_email( $input['set_out_email'] ) )
		{
			step_5('Please enter a valid outgoing email address.');
		}
		
		$to_cache = array(
						  'set_set'					=> 1,
						  'set_hd_name'				=> $input['set_hd_name'],
						  'set_out_email'			=> $input['set_out_email'],
						  'set_email_val'			=> $input['set_email_val'],
						  'set_admin_val'			=> $input['set_admin_val'],
						  'set_tickets_suggest'		=> $input['set_tickets_suggest'],
						  'set_news_comments'		=> $input['set_news_comments'],
						  'set_allow_kb_comment'	=> $input['set_allow_kb_comment'],
						  'set_allow_kb_rating'		=> $input['set_allow_kb_rating'],
						  'set_allow_reply_rating'	=> $input['set_allow_reply_rating'],
						  );
		
		$core->add_cache( 'install', $to_cache );
	}
	
	$content = "";
	
	if ( $error ) $content .= "<div class='critical'>{$error}</div>";
	
	if ( is_dir( HD_PATH .'skin/s1' ) || is_dir( HD_PATH .'skin/s2' ) )
	{	
		$content .= "<div class='critical'>We have detected existing skin files in the <i>skin</i> directory.  By continuing, these will files will be overwritten with default templates and CSS.</div>";
	}
	
	$content .= "<form action='index.php?step=7' method='post'>
				<div class='groupbox'>Install Skin Sets</div>
				<div class='option1'>The Trellis Desk Install Center will now install your skin sets by writing the skin templates and CSS to the <i>skin</i> directory.  Please click Continue below.</div>
				<div class='formtail'><input type='submit' name='submit' id='continue_button' value='Continue' class='button' /></div>
				</form>";
	
	do_output( $content, 6 );
}

function step_7($error='')
{
	global $core, $input;
	
	if ( $input['submit'] && ! $error )
	{
		$parser = new td_parser();
		
		$skins = array( 1 => 'skin_trellis_desk_default_td.xml', 2 => 'skin_trellis_desk_classic_td.xml' );
		
		while ( list( $skin_id, $skin_file ) = each( $skins ) )
		{		
			$data = $parser->parseFile( './'. $skin_file );
			$sinfo = $data[0];
			$templates = $data[1];
			
			if ( ! is_dir( HD_PATH .'skin/s'. $skin_id ) && ! @ mkdir( HD_PATH .'skin/s'. $skin_id ) )
			{
				step_6('We could not create the directory <i>skin/s'. $skin_id .'</i>.  Please CHMOD <i>skin</i> to 0777.');
			}
			
			@chmod( HD_PATH .'skin/s'. $skin_id, 0777 );
	
			while ( list( , $tinfo ) = each( $templates ) )
			{
				if( $handlet = @fopen( HD_PATH .'skin/s'. $skin_id .'/'. $tinfo['tname'], 'w' ) )
				{
					if ( ! @fwrite( $handlet, $tinfo['tcontent'] ) )
					{
						step_6('We could not write to the file <i>skin/s'. $skin_id .'/'. $tinfo['tname'] .'</i>.  Please CHMOD <i>skin/s'. $skin_id .'/'. $tinfo['tname'] .'</i> to 0777.');
					}
		
					@fclose($handlet);
					
					@chmod( HD_PATH .'skin/s'. $skin_id .'/'. $tinfo['tname'], 0777 );
				}
				else
				{
					step_6('We could not create the file <i>skin/s'. $skin_id .'/'. $tinfo['tname'] .'</i>.  Please CHMOD <i>skin/s'. $skin_id .'</i> to 0777.');
				}
			}
			
			if( $handle = @fopen( HD_PATH .'skin/s'. $skin_id .'/style.css', 'w' ) )
			{
				if ( ! @fwrite( $handle, $sinfo['sk_css'] ) )
				{
					step_6('We could not write to the file <i>skin/s'. $skin_id .'/style.css</i>.  Please CHMOD <i>skin/s'. $skin_id .'/style.css</i> to 0777.');
				}
		
				@fclose($handle);
				
				@chmod( HD_PATH .'skin/s'. $skin_id .'/style.css', 0777 );
			}
			else
			{
				step_6('We could not create the file <i>skin/s'. $skin_id .'/style.css</i>.  Please CHMOD <i>skin/s'. $skin_id .'</i> to 0777.');
			}
		}
		
		$core->add_cache( 'install', array( 'skin_set' => 1 ) );
	}
	
	$content = "";
	
	if ( $error ) $content .= "<div class='critical'>{$error}</div>";
	
	if ( $core->cache['install']['sql_exists'] ) $content .= "<div class='critical'>We have detected that an installation of Trellis Desk already exists.  By completing this installation, you will overwrite your all your data and start over fresh.</div>";
	
	$content .= "<form action='index.php?step=8' method='post'>
				<div class='groupbox'>Write Installation Data</div>
				<div class='option1'>The Trellis Desk Install Center has finished gathering the required information to install Trellis Desk.  To complete the installation, click the Complete Installation button below.  Trellis Desk will install its database structure and write to the configuration file.  After you complete this step, you will be presented with a security check report.</div>
				<div class='formtail'><input type='submit' name='submit' id='continue_button' value='Complete Installation' class='button' /></div>
				</form>";
	
	do_output( $content, 7 );
}

function step_8()
{
	global $core, $input;
	
	$installed = 0;
	
	if ( $input['submit'] )
	{
		if ( $input['adv'] )
		{
			if ( ! $input['admin_user'] )
			{
				step_a('Please enter a username.');
			}
			
			if ( $input['admin_pass'] != $input['admin_pass_b'] )
			{
				step_a('Your admin passwords do not match.');
			}
			
			if ( ! validate_email( $input['admin_email'] ) )
			{
				step_a('Please enter a valid email address.');
			}
			
			if ( $input['sql_pass'] != $input['sql_pass_b'] )
			{
				step_a('Your MySQL passwords do not match.');
			}
			
			if ( ! $_POST['sql_port'] ) $_POST['sql_port'] = '3306';
			
			$parser = new td_parser();
		
			$skins = array( 1 => 'skin_trellis_desk_default_td.xml', 2 => 'skin_trellis_desk_classic_td.xml' );
			
			while ( list( $skin_id, $skin_file ) = each( $skins ) )
			{		
				$data = $parser->parseFile( './'. $skin_file );
				$sinfo = $data[0];
				$templates = $data[1];
				
				if ( ! is_dir( HD_PATH .'skin/s'. $skin_id ) && ! @ mkdir( HD_PATH .'skin/s'. $skin_id ) )
				{
					step_a('We could not create the directory <i>skin/s'. $skin_id .'</i>.  Please CHMOD <i>skin</i> to 0777.');
				}
				
				@chmod( HD_PATH .'skin/s'. $skin_id, 0777 );
		
				while ( list( , $tinfo ) = each( $templates ) )
				{
					if( $handlet = @fopen( HD_PATH .'skin/s'. $skin_id .'/'. $tinfo['tname'], 'w' ) )
					{
						if ( ! @fwrite( $handlet, $tinfo['tcontent'] ) )
						{
							step_a('We could not write to the file <i>skin/s'. $skin_id .'/'. $tinfo['tname'] .'</i>.  Please CHMOD <i>skin/s'. $skin_id .'/'. $tinfo['tname'] .'</i> to 0777.');
						}
			
						@fclose($handlet);
						
						@chmod( HD_PATH .'skin/s'. $skin_id .'/'. $tinfo['tname'], 0777 );
					}
					else
					{
						step_a('We could not create the file <i>skin/s'. $skin_id .'/'. $tinfo['tname'] .'</i>.  Please CHMOD <i>skin/s'. $skin_id .'</i> to 0777.');
					}
				}
				
				if( $handle = @fopen( HD_PATH .'skin/s'. $skin_id .'/style.css', 'w' ) )
				{
					if ( ! @fwrite( $handle, $sinfo['sk_css'] ) )
					{
						step_a('We could not write to the file <i>skin/s'. $skin_id .'/style.css</i>.  Please CHMOD <i>skin/s'. $skin_id .'/style.css</i> to 0777.');
					}
			
					@fclose($handle);
					
					@chmod( HD_PATH .'skin/s'. $skin_id .'/style.css', 0777 );
				}
				else
				{
					step_a('We could not create the file <i>skin/s'. $skin_id .'/style.css</i>.  Please CHMOD <i>skin/s'. $skin_id .'</i> to 0777.');
				}
			}
			
			$pass_salt = substr( md5( 'ps'. uniqid( rand(), true ) ), 0, 9 );
			$pass_hash = sha1( md5( $input['admin_pass'] . $pass_salt ) );
			$login_key = str_replace( "=", "", base64_encode( strrev( crypt( $input['admin_pass'] ) ) ) );
			$rss_key = md5( 'rk'. uniqid( rand(), true ) );
		
			$new_cache = array(
								'sql_host'					=> $_POST['sql_host'],
								'sql_port'					=> $_POST['sql_port'],
								'sql_user'					=> $_POST['sql_user'],
								'sql_pass'					=> $_POST['sql_pass'],
								'sql_db'					=> $_POST['sql_db'],
								'sql_prefix'				=> $_POST['sql_prefix'],
								'admin_user'				=> $input['admin_user'],
								'admin_pass_hash'			=> $pass_hash,
								'admin_pass_salt'			=> $pass_salt,
								'admin_login_key'			=> $login_key,
								'admin_email'				=> $input['admin_email'],
								'admin_time_zone'			=> 0,
								'admin_dst_active'			=> 0,
								'admin_use_rte'				=> 1,
								'admin_email_ticket'		=> 1,
								'admin_email_reply'			=> 1,
								'admin_rss_key'				=> $rss_key,
								'set_hd_name'				=> 'Trellis Desk',
								'set_out_email'				=> $input['admin_email'],
								'set_email_val'				=> 1,
								'set_admin_val'				=> 0,
								'set_tickets_suggest'		=> 1,
								'set_news_comments'			=> 1,
								'set_allow_kb_comment'		=> 1,
								'set_allow_kb_rating'		=> 1,
								'set_allow_reply_rating'	=> 1,
								);
			
			$core->cache['install'] = array_merge( $new_cache, $core->cache['install'] );
			
			$error_fun = 'step_a';
		}
		else
		{
			$error_fun = 'step_7';
		}
		
		if ( ! $handle = @fopen( HD_PATH .'config.php', 'w' ) )
		{
			$error_fun('We could not write to the configuration file.  Please make sure that <i>config.php</i> is CHMODed to 0777.');
		}
	
		$url = str_replace( "/install/index.php", "", $_SERVER['HTTP_REFERER'] );
		$url = str_replace( "/install/", "", $url );
		$url = str_replace( "/install", "", $url );
		$url = str_replace( "index.php", "", $url );	
		$url = substr( $url, 0, strpos( $url, '?' ) );
		$url = str_replace( "?", "", $url );
	
		$path = str_replace( "/install/index.php", "", $_SERVER['SCRIPT_FILENAME'] );
		$path = str_replace( "/install/", "", $path );
		$path = str_replace( "/install", "", $path );
		$path = str_replace( "index.php", "", $path );
	
		$dir = dirname( dirname( __FILE__ ) );
		$position = strrpos( $path, '/' ) + 1;
		$cookie_path = substr($path, $position);
	
		$upload_path = $path ."/uploads";
		$upload_url = $url ."/uploads";
	
		$file_data = "<?php\n\n";
	
		$file_data .= "\$config['driver'] = 'mysql';\n";
		$file_data .= "\$config['host'] = '". $core->cache['install']['sql_host'] ."';\n";
		$file_data .= "\$config['port'] = '". $core->cache['install']['sql_port'] ."';\n";
		$file_data .= "\$config['user'] = '". $core->cache['install']['sql_user'] ."';\n";
		$file_data .= "\$config['pass'] = '". $core->cache['install']['sql_pass'] ."';\n";
		$file_data .= "\$config['name'] = '". $core->cache['install']['sql_db'] ."';\n";
		$file_data .= "\$config['prefix'] = '". $core->cache['install']['sql_prefix'] ."';\n";
		$file_data .= "\$config['start'] = '". time() ."';\n";
		$file_data .= "\$config['hd_url'] = '". $url ."';\n\n";
		$file_data .= "\$config['acp_session_timeout'] = 3;\n\n";
	
		$file_data .= "?>";
	
		if ( ! @fwrite( $handle, $file_data ) )
		{
			$error_fun('We could not write to the configuration file.  Please make sure that <i>config.php</i> is CHMODed to 0777.');
		}
	
		@fclose($handle);
		
		if ( ! @mysql_connect( $core->cache['install']['sql_host'] .':'. $core->cache['install']['sql_port'], $core->cache['install']['sql_user'], $core->cache['install']['sql_pass'] ) )
		{
			$error_fun('We could not connect to the MySQL Server.  Please check that your MySQL credentails are correct and try again.');
		}
	
		if ( ! @mysql_select_db( $core->cache['install']['sql_db'] ) )
		{
			$error_fun('We could not connect to the MySQL Database.  Please check that your MySQL credentails are correct and try again.');
		}
	
		require_once "./sql_queries.php";
	
		while ( list( , $sql_query ) = each( $SQL ) )
		{
			if ( ! @mysql_query($sql_query) )
			{
				$error_fun('An error encountered while trying to run the following SQL Query.<br /><br />'. $sql_query .'<br /><br />MySQL returned the following error.<br /><br />'. mysql_error() .'<br /><br />'. mysql_errno() );
			}
		}
	
		$new_session = md5( time() . '1' . uniqid( rand(), true ) );
	
		mysql_query( "INSERT INTO ". $core->cache['install']['sql_prefix'] ."sessions SET s_id = '". $new_session ."', s_mid = 1, s_mname = '". $core->cache['install']['admin_user'] ."', s_ipadd = '". $input['ip_address'] ."', s_time = ". time() );
	
		setcookie( 'hdsid', $new_session, time() + ( 20 * 60 ), '/'. $cookie_path .'/' );
	
		setcookie( 'hdmid', 1, time() + ( 60 * 60 * 24 * 365 ), '/'. $cookie_path .'/' );
		setcookie( 'hdphash', $core->cache['install']['admin_login_key'], time() + ( 60 * 60 * 24 * 365 ), '/'. $cookie_path .'/' );
	
		require_once "../includes/ifthd.php";
		$ifthd = new ifthd(1);
	
		$ifthd->core->cache['config']['enable_news_rte'] = 1;
		$ifthd->core->cache['config']['announce_amount'] = 3;
	
		$ifthd->rebuild_set_cache();
		$ifthd->rebuild_dprt_cache();
		$ifthd->rebuild_cat_cache();
		$ifthd->rebuild_group_cache();
		$ifthd->rebuild_announce_cache();
		$ifthd->rebuild_lang_cache();
		$ifthd->rebuild_skin_cache();
		$ifthd->rebuild_pfields_cache();
		$ifthd->rebuild_dfields_cache();
		
		$installed = 1;
		
		$to_cache = array( 'clear_time' => time() );
		
		$ifthd->core->add_cache( 'install', $to_cache, 1 );
	
		$ifthd->core->shut_down_q();
		$ifthd->core->shut_down();
		
		$content .= "<div class='alert'>Congratulations!  Your Trellis Desk installation is now complete.  <a href='". $url ."' target='_blank'>Click here</a> to go to your new help desk.</div>";
		
		if ( $l_handle = @fopen( './install.lock', 'w' ) )
		{
			@fwrite( $l_handle, time() );
	
			@fclose($l_handle);
		}
	}
	elseif ( $input['clear'] )
	{
		if ( is_writable( HD_PATH .'core/cache/'. base64_encode( 'install' ) .'.IFT' ) )
		{
			$to_cache = array( 'clear_time' => time() );
			
			$core->add_cache( 'install', $to_cache, 1 );
			
			$core->shut_down();
		}
	}
	
	$success = "<span style='color:#49701B'>Success</span>";
	$warning = "<span style='color:#D85C08'>Warning</span>";
	$failed = "<span style='color:#AC241A'>Failed</span>";
	$info_warning = "style='padding-top:8px;font-size:12px;'";
	$info_failed = "style='padding-top:8px;font-size:12px;color:#AC241A'";
	
	$content .= "<form action='index.php?step=8' method='post'><div class='groupbox'>Protect Your Installation</div>
				<table width='100%' cellpadding='0' cellspacing='0'>
				<tr>
					<td class='option1' width='80%'>Installation Data</td>
					<td class='option1' width='20%' align='right'>";
	
	
	$install_data = 0;
	
	if ( file_exists( HD_PATH .'core/cache/'. base64_encode( 'install' ) .'.IFT' ) )
	{
		$raw_data = file_get_contents( HD_PATH .'core/cache/'. base64_encode( 'install' ) .'.IFT' );
	
		$install_cache = unserialize( base64_decode( $raw_data ) );
		
		if ( $install_cache['sql_user'] || $install_cache['sql_pass'] || $install_cache['sql_db'] || $install_cache['admin_pass_hash'] )
		{
			$content .= $failed ."</td>
					</tr>
					<tr>
						<td class='infopop' colspan='2' {$info_failed}>Your installation data is still in Trellis Desk's cache!  This means someone could steal information such as your SQL and admin passwords.  Please click the Clear Installation Data button below.  The Install Center will attempt to clear this data for you.  If this does not work, please delete the <b><i>core/cache/". base64_encode( 'install' ) .".IFT</i></b> file immediately.</td>
					</tr>";
					
			$install_data = 1;
		}
		else
		{
			$content .= $success ."</td>
					</tr>";
		}
	}
	else
	{
		$content .= $success ."</td>
				</tr>";
	}
	
	$content .= "<tr>
					<td class='option2'>Configuration File</td>
					<td class='option2' align='right'>";
	
	if( is_writable( HD_PATH .'config.php' ) )
	{
		@chmod( HD_PATH .'config.php', 0644 );
	}
	
	if( is_writable( HD_PATH .'config.php' ) )
	{
		$content .= $warning ."</td>
				</tr>
				<tr>
					<td class='infopop' colspan='2' {$info_warning}>Your configuration file is still writeable.  We recommend that you CHMOD <i>config.php</i> to 0644 for added security.</td>
				</tr>";
	}
	else
	{
		$content .= $success ."</td>
				</tr>";
	}
	
	$content .= "<tr>
					<td class='option1'>Install Folder</td>
					<td class='option1' align='right'>";
	
	if( is_dir( HD_PATH .'install/' ) )
	{
		$content .= $warning ."</td>
				</tr>
				<tr>
					<td class='infopop' colspan='2' {$info_warning}>Your <i>install</i> directory still exists.  We recommend renaming or deleting this <i>install</i> directory for added security.</td>
				</tr>";
	}
	else
	{
		$content .= $success ."</td>
				</tr>";
	}
	
	$content .= "</table>";
	
	if ( $install_data )
	{
		if ( $installed )
		{
			$content .= "<div class='option2'>Your Trellis Desk installation is complete.  However, your installation data still exists in cache.  It is recommended that you click Clear Installation Data below to remove any private installation data.</div>";
		}
		
		$content .= "<div class='formtail'><input type='submit' name='clear' id='clear_button' value='Clear Installation Data' class='button' /></div>";
	}
	elseif ( $installed )
	{
		$content .= "<div class='formtail'><div class='fb_pad'><a href='". $url ."' class='fake_button'>Go To Help Desk</a></div></div>";
	}
	
	$content .= "</form>";
	
	do_output( $content, 8 );
}

function step_a($error='')
{
	global $core, $input;
	
	$content = "";
	
	if ( $error )
	{
		$content .= "<div class='critical'>{$error}</div>";
	}
	else
	{
		if ( ! $_POST['sql_host'] ) $_POST['sql_host'] = 'localhost';
		if ( ! $_POST['sql_port'] ) $_POST['sql_port'] = '3306';
		if ( ! $_POST['sql_prefix'] ) $_POST['sql_prefix'] = 'td_';
	}
	
	$content .= "<form action='index.php?step=8' method='post'>
				<input type='hidden' name='adv' value='1' />
				<div class='groupbox'>Configuration</div>
				<table width='100%' cellpadding='0' cellspacing='0'>
				<tr>
					<td class='option1' width='25%'>MySQL Host</td>
					<td class='option1' width='75%'><input type='text' name='sql_host' id='sql_host' value='{$_POST['sql_host']}' size='40' /></td>
				</tr>
				<tr>
					<td class='option2'>MySQL Port</td>
					<td class='option2'><input type='text' name='sql_port' id='sql_port' value='{$_POST['sql_port']}' size='5' /></td>
				</tr>
				<tr>
					<td class='option2'>MySQL Username</td>
					<td class='option2'><input type='text' name='sql_user' id='sql_user' value='{$_POST['sql_user']}' size='40' /></td>
				</tr>
				<tr>
					<td class='option1'>MySQL Password</td>
					<td class='option1'><input type='password' name='sql_pass' id='sql_pass' value='' size='40' /></td>
				</tr>
				<tr>
					<td class='option2'>Password Confirm</td>
					<td class='option2'><input type='password' name='sql_pass_b' id='sql_pass_b' value='' size='40' /></td>
				</tr>
				<tr>
					<td class='option1'>MySQL Database</td>
					<td class='option1'><input type='text' name='sql_db' id='sql_db' value='{$_POST['sql_db']}' size='40' /></td>
				</tr>
				<tr>
					<td class='option2'>MySQL Table Prefix</td>
					<td class='option2'><input type='text' name='sql_prefix' id='sql_prefix' value='{$_POST['sql_prefix']}' size='4' /></td>
				</tr>
				</table><br />
				
				<div class='groupbox'>Admin Account</div>
				<table width='100%' cellpadding='0' cellspacing='0'>
				<tr>
					<td class='option1' width='25%'>Username</td>
					<td class='option1' width='75%'><input type='text' name='admin_user' id='admin_user' value='{$input['admin_user']}' size='40' /></td>
				</tr>
				<tr>
					<td class='option2'>Password</td>
					<td class='option2'><input type='password' name='admin_pass' id='admin_pass' value='' size='40' /></td>
				</tr>
				<tr>
					<td class='option1'>Confirm Password</td>
					<td class='option1'><input type='password' name='admin_pass_b' id='admin_pass_b' value='' size='40' /></td>
				</tr>
				<tr>
					<td class='option2'>Email Address</td>
					<td class='option2'><input type='text' name='admin_email' id='admin_email' value='{$input['admin_email']}' size='40' /></td>
				</tr>
				</table>
				<div class='formtail'><input type='submit' name='submit' id='continue_button' value='Complete Installation' class='button' /></div>
				</form>";
	
	do_output( $content, 'a' );
}

function do_output($content, $step=0)
{
	global $core, $input;
	
	$wrapper = <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Trellis Desk :: Install Center</title>
	<style type="text/css" media="all">
		@import "../includes/global.css";
		@import "../includes/local.css";
	</style>
	<script src='../includes/scripts/global.js' type='text/javascript'></script>
	<script src='../includes/scripts/prototype.js' type='text/javascript'></script>
	<script src='../includes/scripts/scriptaculous.js' type='text/javascript'></script>
</head>
<body>
<div id="acpwrap">

	<!-- GLOBAL: Header block -->
	<div id="header">
        <div class="lefty">
        <img src="../images/default/install_logo.jpg" alt="Trellis Desk Install Center" width="264" height="56" />
        </div>
    </div>

    <!-- GLOBAL: Navigation bar -->
    <div id="navbar">
    	<div class="righty">
        </div>
        <div class="lefty">
		</div>
        <ul>
        	<li class="current"><a href="index.php">Install Center</a></li>
        	<li><a href="http://docs.accord5.com/Installing_Trellis_Desk" target="_blank">Getting Started</a></li>
        	<li><a href="http://docs.accord5.com" target="_blank">Documentation</a></li>
        	<li><a href="../upgrade/">Upgrade Center</a></li>
        	<li><a href="http://customer.accord5.com/trellis" target="_blank">Help &amp; Support</a></li>
        </ul>
    </div>

    <!-- GLOBAL: Content block -->
    <div id="content">
        <div id="acpblock">
        
        	<!-- GLOBAL: Page ID -->
            <p class="pageid">Install Center</p>
            
            <!-- GLOBAL: Info bar -->
            <div id="infobar">Your first step into setting up a powerful, robust helpdesk solution for your business.</div>
            
            <!-- GLOBAL: ACP inner container -->
            <!-- This is where the action happens! -->
            <div id="acpinner">
            
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="22%" valign="top">
				
            	<!-- LEFT SIDE -->
            	<!-- GLOBAL: ACP page menu -->
				<div id="acpmenu">
                    <div class="menucat"><a href='index.php'>Install Trellis Desk</a></div>
                	<ul>
                    	<% STEPS %>
                    </ul>
					<div id="acphelp"><a href="http://docs.accord5.com">View product documentation</a></div>
            	</div>
								
				</td>
				<td width="78%" valign="top">
				
                <!-- RIGHT SIDE -->
                <!-- GLOBAL: ACP page content -->
                <div id="acppage">
                	<h1><% TITLE %></h1>
					<% CONTENT %>
                </div>

				</td>
			</tr>
			</table>

            </div>
            <br class="end" />

            <!-- GLOBAL: Copyright bar -->
	    	<div id='powerbar'>
                <div class='righty' style='font-weight: normal'>Designed by ACCORD5 in California</div>
                <div class='lefty'>Powered by Trellis Desk <% VERSION %>, &copy; <% YEAR %> <a href='http://www.accord5.com/'>ACCORD5</a></div>
            </div>

        </div>
    </div>
    <div id="close">
    <div class="righty"></div>
    <div class="lefty"></div>
    </div>
</div>
</body>
</html>
EOF;
	
	$wrapper = str_replace( "<% STEPS %>"	, build_steps_list($step)	, $wrapper );
	$wrapper = str_replace( "<% TITLE %>"	, get_page_title($step)		, $wrapper );
	$wrapper = str_replace( "<% CONTENT %>"	, $content					, $wrapper );
	$wrapper = str_replace( "<% VERSION %>"	, VER_HUM					, $wrapper );
	$wrapper = str_replace( "<% YEAR %>"	, date('Y')					, $wrapper );

	header ('Content-type: text/html; charset=utf-8');

	print $wrapper;
	
	if ( $step > 1 && ! $input['clear'] )
	{	
		$core->shut_down();
	}

	exit();
}

function build_steps_list($step)
{
	global $input;
	
	$html = "";
	
	$steps = array(
					0 => 'Introduction',
					1 => 'System Check',
					2 => 'Installation Type',
					3 => 'Database Setup',
					4 => 'Create Admin Account',
					5 => 'Configure General Settings',
					6 => 'Write Skin Files',
					7 => 'Finish Installation',
					'a' => 'Advanced Installation',
					8 => 'Security Check',
					);
	
	if ( $step === 'a' || $input['adv'] )
	{
		unset( $steps[6] );
	}
	
	while( list( $num, $name ) = each( $steps ) )
	{
		if ( ( $num === 'a' && ( $step === 'a' || $input['adv'] ) ) || $num !== 'a' )
		{
			if ( $step === $num )
			{
				$html .= "<li><a href='index.php?step=". $num ."'><b>". $name ."</b></a></li>";
			}
			else
			{
				$html .= "<li><a href='index.php?step=". $num ."'>". $name ."</a></li>";
			}
		}
	}
	
	return $html;
}

function get_page_title($step)
{	
	$titles = array(
					0 => 'Welcome to Trellis Desk',
					1 => 'System Check',
					2 => 'Installation Type',
					3 => 'Database Setup',
					4 => 'Create Admin Account',
					5 => 'Configure General Settings',
					6 => 'Write Skin Files',
					7 => 'Finish Installation',
					8 => 'Security Check',
					'a' => 'Advanced Installation'
					);
	
	return $titles[ $step ];
}

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
					  $data[ sanitize_data($n)][ sanitize_data($n2) ] = sanitize_data($v2);
				}
			}
			else
			{
				$data[ sanitize_data($n) ] = sanitize_data($v);
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
					  $data[ sanitize_data($n) ][ sanitize_data($n2) ] = sanitize_data($v2);
				}
			}
			else
			{
				$data[ sanitize_data($n) ] = sanitize_data($v);
			}
		}
	}

	#=============================
	# Other Junk
	#=============================

	$data['ip_address'] = sanitize_data( $_SERVER['REMOTE_ADDR'] );

	return $data;
}

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

function return_bytes($val)
{
	$val = trim($val);
    $last = strtolower($val{strlen($val)-1});
    switch($last)
	{
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
}

function format_size($bytes)
{
	if ( $bytes < 1024 )
	{
		return $bytes .' bytes';
	}

	$kb = $bytes / 1024;

	if ( $kb < 1024 )
	{
		return round( $kb, 2 ) .' KB';
	}

	$mb = $kb / 1024;

	if ( $mb < 1024 )
	{
		return round( $mb, 2 ) .' MB';
	}
}

function build_time_zone_drop($select=0)
{
   	$zone[-12] = 'GMT - 12:00 Hours';
   	$zone[-11] = 'GMT - 11:00 Hours';
   	$zone[-10] = 'GMT - 10:00 Hours';
   	$zone[-9] = 'GMT - 9:00 Hours';
   	$zone[-8] = 'GMT - 8:00 Hours';
   	$zone[-7] = 'GMT - 7:00 Hours';
   	$zone[-6] = 'GMT - 6:00 Hours';
   	$zone[-5] = 'GMT - 5:00 Hours';
   	$zone[-4] = 'GMT - 4:00 Hours';
   	$zone[-3.5] = 'GMT - 3:30 Hours';
   	$zone[-3] = 'GMT - 3:00 Hours';
   	$zone[-2] = 'GMT - 2:00 Hours';
   	$zone[-1] = 'GMT - 1:00 Hours';
   	$zone[0] = 'GMT';
   	$zone[1] = 'GMT + 1:00 Hours';
   	$zone[2] = 'GMT + 2:00 Hours';
   	$zone[3] = 'GMT + 3:00 Hours';
   	$zone[3.5] = 'GMT + 3:30 Hours';
   	$zone[4] = 'GMT + 4:00 Hours';
   	$zone[4.5] = 'GMT + 4:30 Hours';
   	$zone[5] = 'GMT + 5:00 Hours';
   	$zone[5.5] = 'GMT + 5:30 Hours';
   	$zone[6] = 'GMT + 6:00 Hours';
   	$zone[7] = 'GMT + 7:00 Hours';
   	$zone[8] = 'GMT + 8:00 Hours';
   	$zone[9] = 'GMT + 9:00 Hours';
   	$zone[9.5] = 'GMT + 9:30 Hours';
   	$zone[10] = 'GMT + 10:00 Hours';
   	$zone[11] = 'GMT + 11:00 Hours';
   	$zone[12] = 'GMT + 12:00 Hours';

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

class td_parser {

	function startElement($parser, $name, $attr)
	{
		$this->xml_current_element = $name;
		
		$my_var = 'xml_'. $name;
		$this->$my_var = "";
	}

	function endElement($parser, $name)
	{
		$elements = array( 'tname', 'tcontent' );
		$elementsb = array( 'sk_name', 'sk_img_dir', 'sk_author', 'sk_author_email', 'sk_author_web', 'sk_notes', 'sk_css' );

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

		if( strcmp( $name, "skin_info" ) == 0 )
		{
			while ( list( , $element ) = each( $elementsb ) )
			{
				$my_var = 'xml_'. $element;
				$this->xml_skin_info[ $element ] = base64_decode( preg_replace( "/\s/", "", $this->$my_var ) );
			}
		}
	}

	function characterData($parser, $data)
	{
		$elements = array( 'tname', 'tcontent', 'sk_name', 'sk_img_dir', 'sk_author', 'sk_author_email', 'sk_author_web', 'sk_notes', 'sk_css' );

		while ( list( , $element ) = each( $elements ) )
		{
			if( $this->xml_current_element == $element )
			{
				$my_var = 'xml_'. $element;
				$this->$my_var .= $data;
			}
		}
	}

	function parseFile($xml_file)
	{
		$xml_parser = xml_parser_create();

		xml_set_object( $xml_parser, $this );

		xml_set_element_handler($xml_parser, "startElement", "endElement");
		xml_set_character_data_handler($xml_parser, "characterData");

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

		return array( $this->xml_skin_info, $this->xml_templates );
	}
}

?>