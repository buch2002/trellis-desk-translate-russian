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

class askin {

	var $outhtml		= "";
	var $description	= "";
	var $data			= array();
	var $section		= "";

	#=======================================
	# @ Load Skin
	# Loads the skin.  Inclues skin info,
	# css, wrapper, etc.
	#=======================================

	function load_skin()
	{
		$this->ifthd->load_lang('global');

		#=============================
		# Skin Stuff
		#=============================

		$this->data['image_dir'] = 'default';

		$this->data['wrapper'] = <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Trellis Desk :: Administration Control Panel</title>
	<style type="text/css" media="all">
		@import "<! HD_URL !>/includes/global.css";
		@import "<! HD_URL !>/includes/local.css";
	</style>
	<script src='<! HD_URL !>/includes/scripts/global.js' type='text/javascript'></script>
	<script src='<! HD_URL !>/includes/scripts/prototype.js' type='text/javascript'></script>
	<script src='<! HD_URL !>/includes/scripts/scriptaculous.js' type='text/javascript'></script>
</head>
<body>
<div id="acpwrap">

	<!-- GLOBAL: Header block -->
	<div id="header">
    	<div class="righty" style="margin-left: -367px;">
        <% H_R_LINKS %>
        </div>
        <div class="lefty">
        <img src="<! IMG_DIR !>/acp_logo.jpg" alt="Trellis Desk Administration Control Panel" width="367" height="56" />
        </div>
    </div>

    <!-- GLOBAL: Navigation bar -->
    <div id="navbar">
    	<div class="righty">
        </div>
        <div class="lefty">
		</div>
        <ul>
        	<% ACP_TABS %>
        </ul>
    </div>

    <!-- GLOBAL: Content block -->
    <div id="content">
        <div id="acpblock">
        
        	<!-- GLOBAL: Page ID -->
            <p class="pageid"><% SECTION %></p>
            
            <!-- GLOBAL: Info bar -->
            <div id="infobar"><% DESCRIPTION %></div>
            
            <!-- GLOBAL: ACP inner container -->
            <!-- This is where the action happens! -->
            <div id="acpinner">
            
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="22%" valign="top">
				
            	<!-- LEFT SIDE -->
            	<!-- GLOBAL: ACP page menu -->
				<% SIDEBAR %>
								
				</td>
				<td width="78%" valign="top">
				
                <!-- RIGHT SIDE -->
                <!-- GLOBAL: ACP page content -->
                <div id="acppage">
                	<h1><% TITLE %></h1>
					<!-- <% NAVIGATION %> -->
					<% CONTENT %>
                </div>

				</td>
			</tr>
			</table>

            </div>
            <br class="end" />

            <!-- GLOBAL: Copyright bar -->
	    			<% COPYRIGHT %>

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

		$this->data['wrapper_e'] = <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Trellis Desk :: Administration Control Panel</title>
	<style type="text/css" media="all">
		@import "<! HD_URL !>/includes/global.css";
		@import "<! HD_URL !>/includes/local.css";
	</style>
	<script src='<! HD_URL !>/includes/scripts/global.js' type='text/javascript'></script>
</head>
<body>
<div id="acpwrap">

	<!-- GLOBAL: Header block -->
	<div id="header">
    	<div class="righty" style="margin-left: -367px;">
        <% H_R_LINKS %>
        </div>
        <div class="lefty">
        <img src="<! IMG_DIR !>/acp_logo.jpg" alt="Trellis Desk Administration Control Panel" width="367" height="56" />
        </div>
    </div>

    <!-- GLOBAL: Navigation bar -->
    <div id="navbar">
    	<div class="righty">
        </div>
        <div class="lefty">
		</div>
        <ul>
        	<% ACP_TABS %>
        </ul>
    </div>

    <!-- GLOBAL: Content block -->
    <div id="content">
        <div id="acpblock">
        
        	<!-- GLOBAL: Page ID -->
            <p class="pageid">Administration Control Panel</p>
            
            <!-- GLOBAL: Info bar -->
            <div id="infobar">Manage your help desk settings, content, and appearance using your Administration Control Panel.</div>
            
            <!-- GLOBAL: ACP inner container -->
            <!-- This is where the action happens! -->
            <div id="acpinner">
                
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="22%" valign="top">
				
            	<!-- LEFT SIDE -->
            	<!-- GLOBAL: ACP page menu -->
				<% SIDEBAR %>
								
				</td>
				<td width="78%" valign="top">
				
                <!-- RIGHT SIDE -->
                <!-- GLOBAL: ACP page content -->
			
				<div class='critical'>
					<% ERROR %><br /><br />
					<span class='small'>{lang.try_again}</span>
                </div>

				</td>
			</tr>
			</table>

            </div>
            <br class="end" />

            <!-- GLOBAL: Copyright bar -->
	    <% COPYRIGHT %>

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

		$this->data['wrapper_e_l'] = <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Trellis Desk :: Administration Control Panel</title>
	<style type="text/css" media="all">
		@import "<! HD_URL !>/includes/global.css";
		@import "<! HD_URL !>/includes/local.css";
	</style>
	<script src='<! HD_URL !>/includes/scripts/global.js' type='text/javascript'></script>
</head>
<body>
<div id="acpwrap">

	<!-- GLOBAL: Header block -->
	<div id="header">
    	<div class="righty" style="margin-left: -367px;">
        &nbsp;
        </div>
        <div class="lefty">
        <img src="<! IMG_DIR !>/acp_logo.jpg" alt="Trellis Desk Administration Control Panel" width="367" height="56" />
        </div>
    </div>

    <!-- GLOBAL: Navigation bar -->
    <div id="navbar">
    	<div class="righty">
        </div>
        <div class="lefty">
		</div>
        <ul>
            <li>&nbsp;</li>
        </ul>
    </div>

    <!-- GLOBAL: Content block -->
    <div id="content">
        <div id="acpblock">
        
        	<!-- GLOBAL: Page ID -->
            <p class="pageid">Administration Control Panel</p>
            
            <!-- GLOBAL: Info bar -->
            <div id="infobar">Manage your help desk settings, content, and appearance using your Administration Control Panel.</div>
            
            <!-- GLOBAL: ACP inner container -->
            <!-- This is where the action happens! -->
            <div id="acpinner">
            
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="100%" valign="top">
				
                <!-- RIGHT SIDE -->
                <!-- GLOBAL: ACP page content -->
                <div id="acppage">
			
				<div class='critical'>
					<% ERROR %><br /><br />
					<span class='small'>{lang.try_again}</span>
                </div>

				<form action="<% SELF %>" method="post">
				<input type="hidden" name="do_login" value="1" />
				<div class='groupbox'>Log In</div>
				<div class='option1'><input type="text" name="username" id="username" value="<% PREMEM %>" onfocus="clear_value(this, 'Username')" onblur="reset_value(this, 'Username')" size="30" /></div>
				<div class='option2'><input type="password" name="password" id="password" value="password" onfocus="clear_value(this, 'password')" onblur="reset_value(this, 'password')" size="30" /></div>
				<div class='formtail'><input type="submit" name="submit" id="login" value="Log In" class="button" /></div>
				</form>
                
                </div>

				</td>
			</tr>
			</table>

            </div>
            <br class="end" />

            <!-- GLOBAL: Copyright bar -->
	    <% COPYRIGHT %>

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

		$this->data['wrapper_r'] = <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Trellis Desk :: Administration Control Panel</title>
	<style type="text/css" media="all">
		@import "<! HD_URL !>/includes/global.css";
		@import "<! HD_URL !>/includes/local.css";
	</style>
	<script src='<! HD_URL !>/includes/scripts/global.js' type='text/javascript'></script>
</head>
<body>
<div id="acpwrap">

	<!-- GLOBAL: Header block -->
	<div id="header">
    	<div class="righty" style="margin-left: -367px;">
        &nbsp;
        </div>
        <div class="lefty">
        <img src="<! IMG_DIR !>/acp_logo.jpg" alt="Trellis Desk Administration Control Panel" width="367" height="56" />
        </div>
    </div>

    <!-- GLOBAL: Navigation bar -->
    <div id="navbar">
    	<div class="righty">
        </div>
        <div class="lefty">
		</div>
        <ul>
            <li>&nbsp;</li>
        </ul>
    </div>

    <!-- GLOBAL: Content block -->
    <div id="content">
        <div id="acpblock">
        
        	<!-- GLOBAL: Page ID -->
            <p class="pageid">Administration Control Panel</p>
            
            <!-- GLOBAL: Info bar -->
            <div id="infobar">Manage your help desk settings, content, and appearance using your Administration Control Panel.</div>
            
            <!-- GLOBAL: ACP inner container -->
            <!-- This is where the action happens! -->
            <div id="acpinner">
            
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="100%" valign="top">
				
                <!-- RIGHT SIDE -->
                <!-- GLOBAL: ACP page content -->
                <div id="acppage">
			
				<div class='alert'>
					<% MESSAGE %><br /><br />
					<span class='small'>{lang.transfer_you} <a href='<% URL %>'>{lang.click_here}</a>.</span>
                </div>
                
                </div>

				</td>
			</tr>
			</table>

            </div>
            <br class="end" />

            <!-- GLOBAL: Copyright bar -->
	    <% COPYRIGHT %>

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

		return $this->data;
	}

	#=======================================
	# @ Get Sidebar
	# Return sidebar menu.
	#=======================================

	function get_sidebar()
	{
		if ( $this->ifthd->input['section'] == 'manage' )
		{
			$sidebar = <<<EOF
            	<div id="acpmenu">
					<div class="menucat"><a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets'>Ticket Control</a></div>
                	<ul>
                   		<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=list'>Manage Tickets</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=depart&amp;code=list'>Manage Departments</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=depart&amp;code=add'>Add Department</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=cdfields'>Custom Department Fields</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=canned'>Manage Canned Replies</a></li>
                    </ul>

                    <div class="menucat"><a href='<! HD_URL !>/admin.php?section=manage&amp;act=announce'>Announcement Control</a></div>
                	<ul>
                  	 	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=announce&amp;code=list'>Manage Announcements</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=announce&amp;code=add'>Add Announcement</a></li>
                    </ul>

                    <div class="menucat"><a href='<! HD_URL !>/admin.php?section=manage&amp;act=member'>Member Control</a></div>
                	<ul>
                   		<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=list'>Manage Members</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=group&amp;code=list'>Manage Groups</a></li>
                   	 	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=mod'>Approve Members</a></li>
                   		<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=add'>Add A New Member</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=group&amp;code=add'>Add A New Group</a></li>
                   		<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=cpfields'>Custom Profile Fields</a></li>
                    </ul>

                    <div class="menucat"><a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb'>KB / Pages Control</a></div>
                	<ul>
	                   	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb&amp;code=list'>Manage Articles</a></li>
	                   	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=kbcat&amp;code=list'>Manage Categories</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb&amp;code=add'>Add A New Article</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=pages&amp;code=add'>Add A New Custom Page</a></li>
                   		<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=pages&amp;code=list'>Manage Custom Pages</a></li>
                    </ul>

                    <div class="menucat"><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings'>System Settings</a></div>
                	<ul>
                   		<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings'>View All Settings</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings&amp;code=find&amp;group=general'>General Configuration</a></li>
                   		<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings&amp;code=find&amp;group=security'>Security &amp; Privacy</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings&amp;code=find&amp;group=ticket'>Ticket Settings</a></li>
                   		<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings&amp;code=find&amp;group=kb'>Knowledge Base Settings</a></li>
                    </ul>
					<div id="acphelp">
                		<a href="http://docs.accord5.com">View product documentation</a>
                	</div>
            	</div>
EOF;

		}
		elseif( $this->ifthd->input['section'] == 'tools' )
		{
			$sidebar = <<<EOF
            	<div id="acpmenu">
                    <div class="menucat">Trellis Desk</div>
                	<ul>
                   		<li><a href='http://www.accord5.com/trellis'>Product Page</a></li>
                    	<li><a href='http://docs.accord5.com/trellis'>Documentation</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?act=tdinfo'>Trellis Desk Info</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?act=phpinfo'>PHP Info</a></li>
                    </ul>

                   	<div class="menucat">Maintenance</div>
                	<ul>
                   		<li><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=recount'>Recount Functions</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=rebuild'>Rebuild Functions</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=clean'>Spring Cleaning</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=syscheck'>System Check</a></li>
                    </ul>

                	<div class="menucat">Backups &amp; Updates</div>
                	<ul>
                   		<li><a href='<! HD_URL !>/admin.php?section=tools&amp;act=backup&amp;code=full'>Full Backup</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=tools&amp;act=backup&amp;code=sql'>SQL Backup</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=tools&amp;act=backup&amp;code=file'>File Backup</a></li>
                    </ul>

                    <div class="menucat">Log Center</div>
                	<ul>
                  	 	<li><a href='<! HD_URL !>/admin.php?section=admin&amp;act=logs&amp;code=admin'>Admin Logs</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=admin&amp;act=logs&amp;code=member'>Member Logs</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=admin&amp;act=logs&amp;code=error'>Error Logs</a></li>
                   		<li><a href='<! HD_URL !>/admin.php?section=admin&amp;act=logs&amp;code=security'>Security Logs</a></li>
                   	 	<li><a href='<! HD_URL !>/admin.php?section=admin&amp;act=logs&amp;code=ticket'>Ticket Logs</a></li>
                    </ul>
					<div id="acphelp">
                		<a href="http://docs.accord5.com">View product documentation</a>
                	</div>
            	</div>
EOF;

		}
		elseif( $this->ifthd->input['section'] == 'look' )
		{
			$sidebar = <<<EOF
            	<div id="acpmenu">
                    <div class="menucat">Manage Skins</div>
                	<ul>
                   		<li><a href='<! HD_URL !>/admin.php?section=look&amp;act=skin&amp;code=manage'>Manage Skins</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=look&amp;act=skin&amp;code=tools'>Skin Tools</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=look&amp;act=skin&amp;code=import'>Import Skin</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=look&amp;act=skin&amp;code=export'>Export Skin</a></li>
                    </ul>

                    <div class="menucat">Manage Languages</div>
                	<ul>
                   		<li><a href='<! HD_URL !>/admin.php?section=look&amp;act=lang&amp;code=manage'>Manage Languages</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=look&amp;act=lang&amp;code=tools'>Language Tools</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=look&amp;act=lang&amp;code=import'>Import Language</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=look&amp;act=lang&amp;code=export'>Export Language</a></li>
                    </ul>
					<div id="acphelp">
                		<a href="http://docs.accord5.com">View product documentation</a>
                	</div>
            	</div>
EOF;

		}
		else
		{
			$sidebar = <<<EOF
            	<div id="acpmenu">
                    <div class="menucat">Trellis Desk</div>
                	<ul>
                   		<li><a href='http://www.accord5.com/trellis'>Product Page</a></li>
                    	<li><a href='http://docs.accord5.com/trellis'>Documentation</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?act=tdinfo'>Trellis Desk Info</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?act=phpinfo'>PHP Info</a></li>
                    </ul>

                    <div class="menucat">Quick Links</div>
                	<ul>
                   		<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=group&amp;code=acpperm'>ACP Permissions</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=syscheck'>System Check</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings'>System Settings</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=member'>Manage Members</a></li>
                    </ul>

                    <div class="menucat"><a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets'>Ticket Control</a></div>
                	<ul>
                   		<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=list'>Manage Tickets</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=depart&amp;code=list'>Manage Departments</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=depart&amp;code=add'>Add Department</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=cdfields'>Custom Department Fields</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=canned'>Manage Canned Replies</a></li>
                    </ul>

                    <div class="menucat">Log Center</div>
                	<ul>
                   		<li><a href='<! HD_URL !>/admin.php?section=admin&amp;act=logs&amp;code=admin'>Admin Logs</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=admin&amp;act=logs&amp;code=member'>Member Logs</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=admin&amp;act=logs&amp;code=error'>Error Logs</a></li>
                   		<li><a href='<! HD_URL !>/admin.php?section=admin&amp;act=logs&amp;code=security'>Security Logs</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=admin&amp;act=logs&amp;code=ticket'>Ticket Logs</a></li>
                    </ul>
					<div id="acphelp">
                		<a href="http://docs.accord5.com">View product documentation</a>
                	</div>
            	</div>
EOF;

		}

		return $sidebar;
	}

	#=======================================
	# @ Build Tabs
	# Generates ACP tabs / links.
	#=======================================
	
	function build_tabs($current)
	{
		$tabs = array(
						'<! HD_URL !>/admin.php?section=admin' => array( 'Administration Home', 'admin' ),
						'<! HD_URL !>/admin.php?section=manage' => array( 'Management', 'manage' ),
						'<! HD_URL !>/admin.php?section=look' => array( 'Look &amp; Feel', 'look' ),
						'<! HD_URL !>/admin.php?section=tools' => array( 'Tools &amp; Maintenance', 'tools' ),
						'http://customer.accord5.com/trellis/' => array( 'Help &amp; Support' ),
					);
		
		$html = ""; // Initialize for Security
		
		while( list( $link, $title ) = each( $tabs ) )
		{
			if ( ( $current && $current == $title[1] ) || ( ! $current && $title[1] == 'admin' ) )
			{
				$html .= '<li class="current"><a href="'. $link .'">'. $title[0] .'</a></li>';
			}
			else
			{
				if ( strpos( $link, '<! HD_URL !>' ) === false )
				{
					$html .= '<li><a href="'. $link .'" target="_blank">'. $title[0] .'</a></li>';
				}
				else
				{
					$html .= '<li><a href="'. $link .'">'. $title[0] .'</a></li>';
				}
			}
		}
		
		return $html;
	}

	#=======================================
	# @ Head Right Links
	# Generates text / links for header.
	#=======================================
	
	function head_right_links()
	{
		return 'Logged in as '. $this->ifthd->member['name'] .' <a href="<! HD_URL !>/index.php">Return to Helpdesk</a> <a href="<! HD_URL !>/admin.php?act=logout">Logout</a>';
	}

	#=======================================
	# @ Yes / No Radio
	# Generate a yes / no radio.
	#=======================================

	function yes_no_radio($input_name, $selected=0, $default='', $id=0)
    {
    	$revert = 0; // Initialize for Security

    	if ( isset( $default ) && ( $selected != $default ) && $id )
    	{
    		$revert = 1;
    	}

        if ( $selected == 1 )
        {
        	if ( $revert )
        	{
	        	$html = "<div style='float:right;margin:-2px 0'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings&amp;code=revert&amp;id={$id}'><img src='<! IMG_DIR !>/revert_icon.gif' alt='Revert' /></a></div><div align='left'><label for='{$input_name}1'><input type='radio' name='{$input_name}' id='{$input_name}1' value='1' class='radio' checked='checked' /> Yes</label>&nbsp;&nbsp;";
	        	$html .= "<label for='{$input_name}0'><input type='radio' name='{$input_name}' id='{$input_name}0' value='0' class='radio' /> No</label></div>";
        	}
        	else
        	{
        		$html = "<label for='{$input_name}1'><input type='radio' name='{$input_name}' id='{$input_name}1' value='1' class='radio' checked='checked' /> Yes</label>&nbsp;&nbsp;";
	        	$html .= "<label for='{$input_name}0'><input type='radio' name='{$input_name}' id='{$input_name}0' value='0' class='radio' /> No</label>";
        	}
        }
        else
        {
        	if ( $revert )
        	{
	        	$html = "<div style='float:right;margin:-2px 0'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings&amp;code=revert&amp;id={$id}'><img src='<! IMG_DIR !>/revert_icon.gif' alt='Revert' /></a></div><div align='left'><label for='{$input_name}1'><input type='radio' name='{$input_name}' id='{$input_name}1' value='1' class='radio' /> Yes</label>&nbsp;&nbsp;";
	        	$html .= "<label for='{$input_name}0'><input type='radio' name='{$input_name}' id='{$input_name}0' value='0' class='radio' checked='checked' /> No</label></div>";
        	}
        	else
        	{
        		$html = "<label for='{$input_name}1'><input type='radio' name='{$input_name}' id='{$input_name}1' value='1' class='radio' /> Yes</label>&nbsp;&nbsp;";
	        	$html .= "<label for='{$input_name}0'><input type='radio' name='{$input_name}' id='{$input_name}0' value='0' class='radio' checked='checked' /> No</label>";
        	}
        }

        return $html;
    }

	#=======================================
	# @ Enabled / Disabled Radio
	# Generate an enabled / disabled radio.
	#=======================================

	function enabled_disabled_radio($input_name, $selected=0, $default='', $id=0)
    {
    	$revert = 0; // Initialize for Security

    	if ( isset( $default ) && ( $selected != $default ) && $id )
    	{
    		$revert = 1;
    	}

        if ( $selected == 1 )
        {
        	if ( $revert )
        	{
	        	$html = "<div style='float:right;margin:-2px 0'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings&amp;code=revert&amp;id={$id}'><img src='<! IMG_DIR !>/revert_icon.gif' alt='Revert' /></a></div><div align='left'><label for='{$input_name}1'><input type='radio' name='{$input_name}' id='{$input_name}1' value='1' class='radio' checked='checked' /> Enabled</label>&nbsp;&nbsp;";
	        	$html .= "<label for='{$input_name}0'><input type='radio' name='{$input_name}' id='{$input_name}0' value='0' class='radio' /> Disabled</label></div>";
        	}
        	else
        	{
        		$html = "<label for='{$input_name}1'><input type='radio' name='{$input_name}' id='{$input_name}1' value='1' class='radio' checked='checked' /> Enabled</label>&nbsp;&nbsp;";
	        	$html .= "<label for='{$input_name}0'><input type='radio' name='{$input_name}' id='{$input_name}0' value='0' class='radio' /> Disabled</label>";
        	}
        }
        else
        {
        	if ( $revert )
        	{
	        	$html = "<div style='float:right;margin:-2px 0'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings&amp;code=revert&amp;id={$id}'><img src='<! IMG_DIR !>/revert_icon.gif' alt='Revert' /></a></div><div align='left'><label for='{$input_name}1'><input type='radio' name='{$input_name}' id='{$input_name}1' value='1' class='radio' /> Enabled</label>&nbsp;&nbsp;";
	        	$html .= "<label for='{$input_name}0'><input type='radio' name='{$input_name}' id='{$input_name}0' value='0' class='radio' checked='checked' /> Disabled</label></div>";
        	}
        	else
        	{
        		$html = "<label for='{$input_name}1'><input type='radio' name='{$input_name}' id='{$input_name}1' value='1' class='radio' /> Enabled</label>&nbsp;&nbsp;";
	        	$html .= "<label for='{$input_name}0'><input type='radio' name='{$input_name}' id='{$input_name}0' value='0' class='radio' checked='checked' /> Disabled</label>";
        	}
        }

        return $html;
    }

	#=======================================
	# @ Special Radio
	# Generate a special, very special radio.
	#=======================================

	function special_radio($input_name, $yes_name, $no_name, $selected=0)
    {
        if ( $selected == 1 )
        {
        	$html = "<label for='{$input_name}1'><input type='radio' name='{$input_name}' id='{$input_name}1' value='1' class='radio' checked='checked' /> {$yes_name}</label>&nbsp;&nbsp;";
        	$html .= "<label for='{$input_name}0'><input type='radio' name='{$input_name}' id='{$input_name}0' value='0' class='radio' /> {$no_name}</label>";
        }
        else
        {
        	$html = "<label for='{$input_name}1'><input type='radio' name='{$input_name}' id='{$input_name}1' value='1' class='radio' /> {$yes_name}</label>&nbsp;&nbsp;";
        	$html .= "<label for='{$input_name}0'><input type='radio' name='{$input_name}' id='{$input_name}0' value='0' class='radio' checked='checked' /> {$no_name}</label>";
        }

        return $html;
    }

	#=======================================
	# @ Checkbox
	# Generate a checkbox.
	#=======================================

	function checkbox($input_name, $option_name, $selected=0, $default='', $id=0)
    {
    	$revert = 0; // Initialize for Security

    	if ( isset( $default ) && ( $selected != $default ) && $id )
    	{
    		$revert = 1;
    	}

        if ( $selected == 1 )
        {
        	if ( $revert )
        	{
        		$html = "<div style='float:right;margin:-2px 0'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings&amp;code=revert&amp;id={$id}'><img src='<! IMG_DIR !>/revert_icon.gif' alt='Revert' /></a></div><div align='left'><label for='{$input_name}1'><input type='checkbox' name='{$input_name}' id='{$input_name}1' value='1' class='ckbox' checked='checked' /> {$option_name}</label></div>";
        	}
        	else
        	{
        		$html = "<label for='{$input_name}1'><input type='checkbox' name='{$input_name}' id='{$input_name}1' value='1' class='ckbox' checked='checked' /> {$option_name}</label>";
        	}
        }
        else
        {
        	if ( $revert )
        	{
        		$html = "<div style='float:right;margin:-2px 0'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings&amp;code=revert&amp;id={$id}'><img src='<! IMG_DIR !>/revert_icon.gif' alt='Revert' /></a></div><div align='left'><label for='{$input_name}1'><input type='checkbox' name='{$input_name}' id='{$input_name}1' value='1' class='ckbox' /> {$option_name}</label></div>";
        	}
        	else
        	{
        		$html = "<label for='{$input_name}1'><input type='checkbox' name='{$input_name}' id='{$input_name}1' value='1' class='ckbox' /> {$option_name}</label>";
        	}
        }

        return $html;
    }

	#=======================================
	# @ Input
	# Generate a text input.
	#=======================================

	function input($input_name, $value, $default='', $id=0, $length=0)
    {
    	if ( ! $length )
    	{
    		$length = 35;
    	}

    	if ( isset( $default ) && ( $value != $default ) && $id )
    	{
    		$html = "<div style='float:right;margin:-2px 0'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings&amp;code=revert&amp;id={$id}'><img src='<! IMG_DIR !>/revert_icon.gif' alt='Revert' /></a></div><div align='left'><input type='text' name='{$input_name}' id='{$input_name}' value='{$value}' size='{$length}' /></div>";
    	}
    	else
    	{
    		$html = "<input type='text' name='{$input_name}' id='{$input_name}' value='{$value}' size='{$length}' />";
    	}

        return $html;
    }

	#=======================================
	# @ Textarea
	# Generate a textarea.
	#=======================================

	function textarea($input_name, $value, $default='', $id=0, $cols=0, $rows=0)
    {
    	if ( ! $cols )
    	{
    		$cols = 45;
    	}
    	if ( ! $rows )
    	{
    		$rows = 8;
    	}

    	if ( 0 ) #$default && ( $value != $default )
    	{
    		$html = "<div style='float:right;margin:-2px 0'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings&amp;code=revert&amp;id={$id}'><img src='<! IMG_DIR !>/revert_icon.gif' alt='Revert' /></a></div><div align='left'><textarea name='{$input_name}' id='{$input_name}' cols='{$cols}' rows='{$rows}' />{$value}</textarea></div>";
    	}
    	else
    	{
    		$html = "<textarea name='{$input_name}' id='{$input_name}' cols='{$cols}' rows='{$rows}' />{$value}</textarea>";
    	}

        return $html;
    }

	#=======================================
	# @ Drop Down
	#=======================================

	function drop_down($input_name, $selected='', $options, $default='', $id=0)
    {
    	$html = "";
    	
    	if ( isset( $default ) && ( $selected != $default ) && $id )
    	{
    		$html .= "<div style='float:right;margin:-2px 0'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings&amp;code=revert&amp;id={$id}'><img src='<! IMG_DIR !>/revert_icon.gif' alt='{lang.revert}' /></a></div>";
    	}
    	
    	$html .= "<select name='{$input_name}' id='{$input_name}'>";
    	
    	while ( list( $key, $value ) = each ( $options ) )
    	{
    		if ( $selected == $key )
    		{
    			$html .= "<option value='{$key}' selected='selected'>{$value}</option>";
    		}
    		else
    		{
    			$html .= "<option value='{$key}'>{$value}</option>";
    		}
    	}
    	
    	$html .= "</select>";

        return $html;
	}

	#=======================================
	# @ Add To Output
	# Simply adds to the output. :P
	#=======================================

	function add_output($html)
    {
        $this->outhtml .= $html;
    }

	#=======================================
	# @ Set Section
	# Sets the page section.
	#=======================================

	function set_section($txt)
    {
        $this->section = $txt;
    }

	#=======================================
	# @ Set Description
	# Sets the page description.
	#=======================================

	function set_description($txt)
    {
        $this->description = $txt;
    }

	#=======================================
	# @ Do Output
	# Generates and sends all the HTML, etc
	# to the browser.
	#=======================================

	function do_output($extra="")
	{
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
			
			if ( isset( $extra['nav'] ) )
			{
				$nav_links = $extra['nav'];
			}
		}

		#=============================
		# Navigation
		#=============================

		$nav_tree = '<div id="navstrip">&raquo; <a href="<! HD_URL !>/admin.php">Home</a>'; // Initialize for Security

		if ( is_array( $nav_links ) )
		{
			while ( list( , $nlink ) = each( $nav_links ) )
			{
				$nav_tree .= " &rsaquo; ". $nlink;
			}
		}

		#=============================
		# Sidebar & Tabs
		#=============================

		$sidebar = $this->get_sidebar();
		
		$acp_tabs = $this->build_tabs( $this->ifthd->input['section'] );
 
		/**********************************************************************/
		/* REMOVAL OF THE COPYRIGHT WITHOUT PURCHASING COPYRIGHT REMOVAL WILL */
		/* VIOLATE THE LICENSE YOU AGREED TO WHEN DOWNLOADING AND REGISTERING */
		/* THIS PORDUCT.  IF THIS HAPPENS, IT COULD RESULT IN REMOVAL OF THIS */
		/* SYSTEM AND POSSIBLY CRIMINAL CHARGES.  THANK YOU FOR UNDERSTANDING */
		/***********************************************************************/

		/**********************************************************************/
		/* You can legally remove the copyright notice after purchasing       */
		/* the ACCORD5 copyright removal service:                             */
		/* http://www.accord5.com/copyright                                   */
		/**********************************************************************/

		$query_count = $this->ifthd->core->db->get_query_count();
		$query_s_count = $this->ifthd->core->db->get_query_s_count();
		$exe_time = $this->ifthd->end_timer();

		$copyright = "<div id='powerbar'>
            	<div class='righty'><span title='". $query_count ." Normal | ". $query_s_count ." Shutdown'>". $query_count ." Queries</span> // ". $exe_time ." Seconds</div>
                <div class='lefty'>Powered by Trellis Desk {$this->ifthd->vername}, &copy; ". date('Y') ." <a href='http://www.accord5.com/'>ACCORD5</a></div>
            </div>";

		#=============================
		# Generate HTML
		#=============================

		$this->skin = array(); // Initialize for Security

		$this->skin['wrapper'] = $this->data['wrapper'];

		$this->skin['wrapper'] = str_replace("<% H_R_LINKS %>"	, $this->head_right_links() , $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<% ACP_TABS %>"	, $acp_tabs 				, $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<% SECTION %>"	, $this->section 			, $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<% TITLE %>"		, $title		 			, $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<% DESCRIPTION %>", $this->description		, $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<% CONTENT %>"	, $this->outhtml 			, $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<% COPYRIGHT %>"	, $copyright				, $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<% NAVIGATION %>"	, $nav_tree					, $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<% SIDEBAR %>"	, $sidebar					, $this->skin['wrapper']);

		#=============================
		# Language
		#=============================

		while ( list( $langkey, $langvalue ) = each( $this->ifthd->lang ) )
		{
			$this->skin['wrapper'] = str_replace("{lang.". $langkey ."}", $langvalue, $this->skin['wrapper']);
		}

		#=============================
		# Final Bits! :D
		#=============================

		$this->skin['wrapper'] = str_replace("<! IMG_DIR !>"	, "images/". $this->data['image_dir']						, $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<! MEM_NAME !>"	, $this->ifthd->member['name']								, $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<! MEM_ID !>"		, $this->ifthd->member['id']								, $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<! HD_NAME !>"	, $this->ifthd->core->cache['config']['hd_name']			, $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<! HD_URL !>"		, $this->ifthd->core->cache['config']['hd_url']				, $this->skin['wrapper']);

		#=============================
		# Can't Forget XHTML!
		#=============================

		$this->skin['wrapper'] = preg_replace( "/\{sel(.+?)\}/i", "", $this->skin['wrapper'] );
		$this->skin['wrapper'] = preg_replace( "/\{lang\.(.+?)\}/i", "", $this->skin['wrapper'] );

		header ('Content-type: text/html; charset=utf-8');

		print $this->skin['wrapper'];

		$this->ifthd->core->shut_down_q();
		$this->ifthd->shut_down();
		$this->ifthd->core->shut_down();

		if ( HD_DEBUG )
		{
			echo "<br /><br />------------------<br /><br />". $this->ifthd->core->db->queries_ran;
		}

		exit();
	}

	#================================================
	# Redirect
	#================================================

	function redirect($url, $msg, $fast=0, $full=0)
	{
		if ( $fast )
		{
			$this->ifthd->core->shut_down_q();
			$this->ifthd->shut_down();
			$this->ifthd->core->shut_down();
			
			header('Location: '. $url);

			exit();
		}
		
		$this->ifthd->ad_load_lang('error');

		#=============================
		# Initialize
		#=============================

		$this->ifthd->ad_load_lang('redirect');

		if ( $full )
		{
			$url = $full;
		}
		else
		{
			$url = $this->ifthd->core->cache['config']['hd_url'] ."/admin.php". $url;
		}

		#=============================
		# Do We Have A Title?
		#=============================

		$title = $this->ifthd->core->cache['config']['hd_name'] ." :: {lang.please_wait}";

		/**********************************************************************/
		/* REMOVAL OF THE COPYRIGHT WITHOUT PURCHASING COPYRIGHT REMOVAL WILL */
		/* VIOLATE THE LICENSE YOU AGREED TO WHEN DOWNLOADING AND REGISTERING */
		/* THIS PORDUCT.  IF THIS HAPPENS, IT COULD RESULT IN REMOVAL OF THIS */
		/* SYSTEM AND POSSIBLY CRIMINAL CHARGES.  THANK YOU FOR UNDERSTANDING */
		/***********************************************************************/

		/**********************************************************************/
		/* You can legally remove the copyright notice after purchasing       */
		/* the ACCORD5 copyright removal service:                             */
		/* http://www.accord5.com/copyright                                   */
		/**********************************************************************/

		$query_count = $this->ifthd->core->db->get_query_count();
		$query_s_count = $this->ifthd->core->db->get_query_s_count();
		$exe_time = $this->ifthd->end_timer();

		$copyright = "<div id='powerbar'>
            	<div class='righty'><span title='". $query_count ." Normal | ". $query_s_count ." Shutdown'>". $query_count ." Queries</span> // ". $exe_time ." Seconds</div>
                <div class='lefty'>Powered by Trellis Desk {$this->ifthd->vername}, &copy; ". date('Y') ." <a href='http://www.accord5.com/'>ACCORD5</a></div>
            </div>";

		#=============================
		# Generate HTML
		#=============================

		$this->skin = array(); // Initialize for Security

		$this->skin['wrapper'] = $this->data['wrapper_r'];

		$this->skin['wrapper'] = str_replace("<% CONTENT %>"	, $this->outhtml 			, $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<% COPYRIGHT %>"	, $copyright				, $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<% NAVIGATION %>"	, $nav						, $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<% SIDEBAR %>"	, ""						, $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<% MESSAGE %>"	, "{lang.". $msg ."}"		, $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<% URL %>"		, $url						, $this->skin['wrapper']);

		#=============================
		# Language
		#=============================

		while ( list( $langkey, $langvalue ) = each( $this->ifthd->lang ) )
		{
			$this->skin['wrapper'] = str_replace("{lang.". $langkey ."}", $langvalue, $this->skin['wrapper']);
		}

		#=============================
		# Final Bits! :D
		#=============================

		$this->skin['wrapper'] = str_replace("<! IMG_DIR !>"	, "images/". $this->data['image_dir']						, $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<! MEM_NAME !>"	, $this->ifthd->member['name']								, $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<! MEM_ID !>"		, $this->ifthd->member['id']								, $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<! HD_NAME !>"	, $this->ifthd->core->cache['config']['hd_name']			, $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<! HD_URL !>"		, $this->ifthd->core->cache['config']['hd_url']				, $this->skin['wrapper']);

		#=============================
		# Can't Forget XHTML!
		#=============================

		$this->skin['wrapper'] = preg_replace( "/\{sel(.+?)\}/i", "", $this->skin['wrapper'] );
		$this->skin['wrapper'] = preg_replace( "/\{lang\.(.+?)\}/i", "", $this->skin['wrapper'] );

		header ('Content-type: text/html; charset=utf-8');

		print $this->skin['wrapper'];

		$this->ifthd->core->shut_down_q();
		$this->ifthd->shut_down();
		$this->ifthd->core->shut_down();

		if ( HD_DEBUG )
		{
			echo "<br /><br />------------------<br /><br />". $this->ifthd->core->db->queries_ran;
		}

		#=============================
		# Redirect! Duh!
		#=============================

		header('Refresh: 3; URL='. $url);

		exit();
	}

	#================================================
	# Error
	#================================================

	function error($msg, $login=0)
	{
		$this->ifthd->ad_load_lang('error');

		$nav_links = ""; // Initialize for Security

		#=============================
		# Navigation
		#=============================

		$nav_tree = '&raquo; <a href="<! HD_URL !>/admin.php">Home</a>'; // Initialize for Security

		if ( is_array( $nav_links ) )
		{
			while ( list( , $nlink ) = each( $nav_links ) )
			{
				$nav_tree .= " &rsaquo; ". $nlink;
			}
		}

		#=============================
		# Sidebar & Tabs
		#=============================

		$sidebar = "";
		
		$acp_tabs = $this->build_tabs( $this->ifthd->input['section'] );

		/**********************************************************************/
		/* REMOVAL OF THE COPYRIGHT WITHOUT PURCHASING COPYRIGHT REMOVAL WILL */
		/* VIOLATE THE LICENSE YOU AGREED TO WHEN DOWNLOADING AND REGISTERING */
		/* THIS PORDUCT.  IF THIS HAPPENS, IT COULD RESULT IN REMOVAL OF THIS */
		/* SYSTEM AND POSSIBLY CRIMINAL CHARGES.  THANK YOU FOR UNDERSTANDING */
		/***********************************************************************/

		$query_count = $this->ifthd->core->db->get_query_count();
		$query_s_count = $this->ifthd->core->db->get_query_s_count();
		$exe_time = $this->ifthd->end_timer();
		
		/**********************************************************************/
		/* You can legally remove the copyright notice after purchasing       */
		/* the ACCORD5 copyright removal service:                             */
		/* http://www.accord5.com/copyright                                   */
		/**********************************************************************/

		$copyright = "<div id='powerbar'>
            	<div class='righty'><span title='". $query_count ." Normal | ". $query_s_count ." Shutdown'>". $query_count ." Queries</span> // ". $exe_time ." Seconds</div>
                <div class='lefty'>Powered by Trellis Desk {$this->ifthd->vername}, &copy; ". date('Y') ." <a href='http://www.accord5.com/'>ACCORD5</a></div>
            </div>";

		#=============================
		# Generate HTML
		#=============================

		$this->skin = array(); // Initialize for Security

		if ( $login )
		{
			$cookie_sid = $this->ifthd->get_cookie('hdsid');
			$cookie_mid = $this->ifthd->get_cookie('hdmid');

			if ( $cookie_sid )
			{
				$this->ifthd->core->db->construct( array(
													  	 'select'	=> array( 's_mid', 's_mname' ),
													  	 'from'		=> 'sessions',
									 				  	 'where'	=> array( array( 's_id', '=', $cookie_sid ), array( 's_mid', '!=', 0, 'and' ) ),
									 		  	  ) 	);
			}
			elseif ( $cookie_mid )
			{
				$this->ifthd->core->db->construct( array(
													  	 'select'	=> array( 'name' ),
													  	 'from'		=> 'members',
									 				  	 'where'	=> array( 'id', '=', intval( $cookie_mid ) ),
									 		  	  ) 	);
			}

			if ( $cookie_sid || $cookie_mid )
			{
				$this->ifthd->core->db->execute();

				if ( $this->ifthd->core->db->get_num_rows() )
				{
					$pre_mem = $this->ifthd->core->db->fetch_row();

					if ( $cookie_sid )
					{
						$pre_mem['name'] = $pre_mem['s_mname'];
					}
				}
				else
				{
					$pre_mem['name'] = $this->ifthd->lang['username'];
				}
			}
			else
			{
				$pre_mem['name'] = $this->ifthd->lang['username'];
			}

			$this->skin['wrapper'] = $this->data['wrapper_e_l'];
		}
		else
		{
			$this->skin['wrapper'] = $this->data['wrapper_e'];

			$sidebar = $this->get_sidebar();
		}		
		
		$self = $this->ifthd->core->cache['config']['hd_url'] .'/admin.php';
		
		if ( $_SERVER['QUERY_STRING'] ) $self .= '?'. $this->ifthd->sanitize_data( $_SERVER['QUERY_STRING'] );
		
		$this->skin['wrapper'] = str_replace("<% H_R_LINKS %>"	, $this->head_right_links() , $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<% ACP_TABS %>"	, $acp_tabs 				, $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<% CONTENT %>"	, $this->outhtml 			, $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<% SELF %>"		, $self						, $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<% COPYRIGHT %>"	, $copyright				, $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<% NAVIGATION %>"	, $nav_tree					, $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<% SIDEBAR %>"	, $sidebar					, $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<% ERROR %>"		, "{lang.". $msg ."}"		, $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<% PREMEM %>"		, $pre_mem['name']			, $this->skin['wrapper']);

		#=============================
		# Language
		#=============================

		while ( list( $langkey, $langvalue ) = each( $this->ifthd->lang ) )
		{
			$this->skin['wrapper'] = str_replace("{lang.". $langkey ."}", $langvalue, $this->skin['wrapper']);
		}

		#=============================
		# Final Bits! :D
		#=============================

		$this->skin['wrapper'] = str_replace("<! IMG_DIR !>"	, "images/". $this->data['image_dir']						, $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<! MEM_NAME !>"	, $this->ifthd->member['name']								, $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<! MEM_ID !>"		, $this->ifthd->member['id']								, $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<! HD_NAME !>"	, $this->ifthd->core->cache['config']['hd_name']			, $this->skin['wrapper']);
		$this->skin['wrapper'] = str_replace("<! HD_URL !>"		, $this->ifthd->core->cache['config']['hd_url']				, $this->skin['wrapper']);

		#=============================
		# Can't Forget XHTML!
		#=============================

		$this->skin['wrapper'] = preg_replace( "/\{sel(.+?)\}/i", "", $this->skin['wrapper'] );
		$this->skin['wrapper'] = preg_replace( "/\{lang\.(.+?)\}/i", "", $this->skin['wrapper'] );

		header ('Content-type: text/html; charset=utf-8');

		print $this->skin['wrapper'];

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