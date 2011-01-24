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
|    | Trellis Desk Admin Class
#======================================================
*/

class ifthd_admin extends ifthd {

	#=======================================
	# @ A5 Help Desk Admin :: Common Stuff
	# This function is automatically run so
	# all of our common stuff goes here.
	#=======================================

	function ifthd_admin()
	{
		$this->start_timer();

		#=============================
		# Get Incoming
		#=============================

		$this->input = $this->get_post();

		#=============================
		# I'm Hungry
		#=============================

		if ( ! isset( $this->input['act'] ) )
		{
			$this->input['act'] = 'home';
		}

		#=============================
		# Call The Core!
		#=============================

		require_once HD_PATH ."core/ift.php";

		$this->core = new iftcore();

		#=============================
		# Load Configuration
		#=============================

		if ( ! file_exists( HD_PATH ."config.php" ) )
		{
			header('Location: install/');
		}

		require_once HD_PATH ."config.php";

		$this->core->cache['config'] = array_merge( $this->core->cache['config'], $config );
		
		define( 'ACP_HELP', $this->core->cache['config']['acp_help'] );

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
		# Check / Run Tasks
		#=============================

		$this->check_tasks();

		#=============================
		# Load Skin
		#=============================

		require_once HD_INC ."class_askin.php";

		$this->skin = new askin();
		$this->skin->ifthd = &$this;
		$this->skin->ifthd->shutdown_funcs =& $this->shutdown_funcs;

		$this->skin->load_skin();

		#=============================
		# Load Session
		#=============================

		require_once HD_INC ."class_asession.php";

		$session = new asession();
		$session->ifthd = &$this;

		#=============================
		# Login / Logout
		#=============================

		if ( $this->input['do_login'] )
		{
			$this->member = $session->do_login();
		}
		else
		{
			$this->member = $session->load_session();
		}

		if ( $this->input['act'] == 'logout' )
		{
			$session->do_logout();
		}
	}

	#=======================================
	# @ Admin Load Language
	# Loads the defined admin language file.
	#=======================================

	function ad_load_lang($name)
	{
		if ( ! $this->member['lang'] || ! file_exists( HD_PATH. "language/". $this->member['lang'] ."/ad_lang_". $name .".php" ) )
		{
			$this->member['lang'] = $this->core->cache['lang']['default'];
		}

		require_once HD_PATH. "language/". $this->member['lang'] ."/ad_lang_". $name .".php";

		$this->skin->ifthd->lang = array_merge( (array)$lang , (array)$this->skin->ifthd->lang );
		#$this->lang = $this->skin->ifthd->lang;
	}

	#=======================================
	# @ TD Info
	# Display Trellis Desk Info page. :)
	#=======================================

	function tdinfo()
	{
		$this->skin->set_section( 'Administration Control Panel' );		
		$this->skin->set_description( 'Need some technically information regarding Trellis Desk?  Chances are you can find it here.' );
		
		$this->output = "<!-- REMOVAL OF THE COPYRIGHT WITHOUT PURCHASING COPYRIGHT REMOVAL WILL
						VIOLATE THE LICENSE YOU AGREED TO WHEN DOWNLOADING AND REGISTERING
						THIS PRODUCT.  IF THIS HAPPENS, IT COULD RESULT IN REMOVAL OF THIS
						SYSTEM AND POSSIBLY CRIMINAL CHARGES.  THANK YOU FOR UNDERSTANDING -->
						
						<div class='groupbox'>Version Information</div>
						<div class='option1'>
							Build: {$this->vernum} | PHP Version: ". phpversion() ." | MySQL Version: ". mysql_get_server_info() ."<br /><a href='http://www.accord5.com/trellis'>Trellis Desk</a> {$this->vername} &copy; ". date('Y') ." <a href='http://www.accord5.com/'>ACCORD5</a>
						</div>
						
						<br />
						
						<div class='groupbox'>About</div>
						<div class='option1' style='font-weight: normal'>
							Trellis Desk is a free help desk system that is designed to be easy to use, powerful, and clean.  <a href='http://www.sogonphp.com/'>DJ</a> codes Trellis Desk during his free time, his goal to make a free help desk system just as good as many commercial systems.  <a href='http://www.accord5.com/trellis/download'>Click here</a> for License Agreement.
						</div>
						
						<br />
						
						<div class='groupbox'>Credits</div>
						<div class='option1'>
							Head Developer / Project Manager - <a href='http://forums.accord5.com/index.php?showuser=9'>someotherguy</a><br />
							Coding - <a href='http://forums.accord5.com/index.php?showuser=9'>someotherguy</a><br />
							A5 Core - <a href='http://forums.accord5.com/index.php?showuser=9'>someotherguy</a><br />
							Design &amp; Layout - <a href='http://forums.accord5.com/index.php?showuser=2'>Aaron</a> and <a href='http://www.iftomatoes.com/forums/index.php?showuser=1'>Charles</a><br />
							Documentation - <a href='http://forums.accord5.com/index.php?showuser=2'>Aaron</a><br /><br />
							Outgoing Email System - <a href='http://www.swiftmailer.org/'>Swift Mailer</a> by <a href='http://chriscorbyn.co.uk/'>Chris Corbyn</a><br />
							Rich Text Editor - <a href='http://tinymce.moxiecode.com/'>TinyMCE</a> by <a href='http://www.moxiecode.com/'>Moxiecode Systems</a><br />
							Captcha - <a href='http://www.ejeliot.com/pages/php-captcha'>PhpCaptcha</a> by <a href='http://www.ejeliot.com/'>Edward Eliot</a><br />
							SQL Backup Function - <a href='http://www.programmingtalk.com/member.php?userid=141445'>Unreal Ed</a> modified by <a href='http://forums.accord5.com/index.php?showuser=9'>someotherguy</a><br />
							Zip Class - <a href='mailto:eric@themepark.com'>Eric Mueller</a>, <a href='mailto:webmaster@atlant.ru'>Denis125</a>, and <a href='mailto:mlady@users.sourceforge.net'>Peter Listiak</a><br />
							Javascript Libraries / Frameworks - <a href='http://script.aculo.us/'>script.aculo.us</a> and <a href='http://prototypejs.org/'>Prototype</a>
						</div>
						
						<br />
						
						<div class='groupbox'>Special Thanks</div>
						<div class='option1'>
							<a href='http://forums.accord5.com/index.php?showuser=2'>Aaron</a> <span class='desc'>- I can't thank you enough for all the help you've given me and your kindness.  I really love working with you.</span><br />
							<a href='http://forums.accord5.com/index.php?showuser=5'>Ryan</a> <span class='desc'>- Thanks so much for your hard work.  You have helped make Trellis Desk a success.</span><br /> 
							<a href='http://forums.accord5.com/index.php?showuser=1'>Chris</a> - <span class='desc'>I didn't see you around much.  But I know you were always there backing me up 100%.</span><br />
							<a href='http://forums.accord5.com/index.php?showuser=3'>James</a> - <span class='desc'>Thanks a bunch for introducing me to ACCORD5.  You opened up a wonderful door of opportunities for me.</span><br />
							<a href='http://www.iftomatoes.com/forums/index.php?showuser=1'>Charles</a> - <span class='desc'>Your my idol.  I would have never gotten this far without you.</span><br />
							<a href='http://www.iftomatoes.com/forums/index.php?showuser=3'>Neil</a> - <span class='desc'>IFTomatoes was fun.  Something I will never forget.</span><br />
							<a href='http://www.iftomatoes.com/forums/index.php?showuser=2'>Biomech</a> - <span class='desc'>Your PHP learning forced me to stay on top of my game. :P</span><br />
							<a href='http://www.iftomatoes.com/forums/index.php?showuser=802'>Chuck</a> - <span class='desc'>What would I do without your sense of humor to cheer me up?</span><br />
							<a href='http://www.iftomatoes.com/forums/index.php?showuser=254'>Ali</a> - <span class='desc'>I have learned much from your high standards and excellent <strike>grammer</strike> grammar. :P</span><br />
							<a href='http://www.iftomatoes.com/forums/index.php?showuser=3017'>thatphpguy</a> - <span class='desc'>Another one of my awesome PHP friends.  Our geek talk is great! :D</span>
						</div>
						
						<br />
						
						<div class='groupbox'>Other</div>
						<div class='option1'>
							<a href='http://www.php.net/'><img src='<! IMG_DIR !>/logos/php.png' alt='PHP' /></a> <a href='http://www.mysql.com/'><img src='<! IMG_DIR !>/logos/mysql.png' alt='MySQL' /></a> <a href='http://www.zend.com/'><img src='<! IMG_DIR !>/logos/zend.png' alt='Zend' /></a> <a href='http://tinymce.moxiecode.com/'><img src='<! IMG_DIR !>/logos/tinymce.png' alt='TinyMCE' /></a> <a href='http://validator.w3.org/check?uri=referer'><img src='<! IMG_DIR !>/logos/xhtml10.png' alt='XHML' /></a> <a href='http://jigsaw.w3.org/css-validator/'><img src='<! IMG_DIR !>/logos/vcss.png' alt='CSS' /></a><br />
							<a href='http://www.feedvalidator.org/'><img src='<! IMG_DIR !>/logos/vrss.png' alt='Valid RSS' /></a> <img src='<! IMG_DIR !>/logos/feed.png' alt='Feed' />
						</div>";

		$this->skin->add_output( $this->output );

		$this->nav = array(
						   "Trellis Desk Info",
						   );

		$this->skin->do_output( array( 'nav' => $this->nav, 'title' => 'Trellis Desk Information' ) );
	}

}

?>