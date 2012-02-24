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
	<title>Trellis Desk :: Административная панель управления</title>
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
        <img src="<! IMG_DIR !>/acp_logo.jpg" alt="Административная панель управления Trellis Desk" width="367" height="56" />
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
	<title>Trellis Desk :: Административная панель управления</title>
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
        <img src="<! IMG_DIR !>/acp_logo.jpg" alt="Административная панель управления Trellis Desk" width="367" height="56" />
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
            <p class="pageid">Административная панель управления</p>
            
            <!-- GLOBAL: Info bar -->
            <div id="infobar">Управляйте вашим HelpDesk, содержание и внешний вид настраивается с помощью Административной панели управления.</div>
            
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
	<title>Trellis Desk :: Административная панель управления</title>
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
        <img src="<! IMG_DIR !>/acp_logo.jpg" alt="Административная панель управления Trellis Desk" width="367" height="56" />
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
            <p class="pageid">Административная панель управления</p>
            
            <!-- GLOBAL: Info bar -->
            <div id="infobar">Управляйте вашим HelpDesk, содержание и внешний вид настраивается с помощью Административной панели управления.</div>
            
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
	<title>Trellis Desk :: Административная панель управления</title>
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
        <img src="<! IMG_DIR !>/acp_logo.jpg" alt="Административная панель управления Trellis Desk" width="367" height="56" />
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
            <p class="pageid">Административная панель управления</p>
            
            <!-- GLOBAL: Info bar -->
            <div id="infobar">Управляйте вашим HelpDesk, содержание и внешний вид настраивается с помощью Административной панели управления.</div>
            
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
					<div class="menucat"><a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets'>Работа с тиккетами</a></div>
                	<ul>
                   		<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=list'>Управление тиккетами</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=depart&amp;code=list'>Управление отделами</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=depart&amp;code=add'>Добавить отдел</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=cdfields'>Пользовательские поля отдела</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=canned'>Управление шаблонами ответов</a></li>
                    </ul>

                    <div class="menucat"><a href='<! HD_URL !>/admin.php?section=manage&amp;act=announce'>Работа с объявлениями</a></div>
                	<ul>
                  	 	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=announce&amp;code=list'>Управление объявлениями</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=announce&amp;code=add'>Добавить объявление</a></li>
                    </ul>

                    <div class="menucat"><a href='<! HD_URL !>/admin.php?section=manage&amp;act=member'>Работа с пользователями</a></div>
                	<ul>
                   		<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=list'>Управление пользователями</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=group&amp;code=list'>Управление группами</a></li>
                   	 	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=mod'>Утверждение пользователей</a></li>
                   		<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=member&amp;code=add'>Добавить нового пользователя</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=group&amp;code=add'>Добавить новую группу</a></li>
                   		<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=cpfields'>Дополнительные поля профиля</a></li>
                    </ul>

                    <div class="menucat"><a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb'>База знаний / Пользовательские элементы управления страницей</a></div>
                	<ul>
	                   	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb&amp;code=list'>Управление статьями</a></li>
	                   	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=kbcat&amp;code=list'>Управление категориями</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=kb&amp;code=add'>Добавить новую статью</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=pages&amp;code=add'>Добавить новую пользовательскую страницу</a></li>
                   		<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=pages&amp;code=list'>Управление пользовательскими страницами</a></li>
                    </ul>

                    <div class="menucat"><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings'>Настройки системы</a></div>
                	<ul>
                   		<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings'>Все параметры</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings&amp;code=find&amp;group=general'>Общие настройки</a></li>
                   		<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings&amp;code=find&amp;group=security'>Безопасность и конфиденциальность</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings&amp;code=find&amp;group=ticket'>Настройки тиккетов</a></li>
                   		<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings&amp;code=find&amp;group=kb'>Настройки Базы Знаний</a></li>
                    </ul>
					<div id="acphelp">
                		<a href="http://docs.accord5.com">Просмотр документации по продукту (ENG)</a>
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
                   		<li><a href='http://www.accord5.com/trellis'>Страница продукта</a></li>
                    	<li><a href='http://docs.accord5.com/trellis'>Документация</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?act=tdinfo'>Trellis Desk Инфо</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?act=phpinfo'>PHP Инфо</a></li>
                    </ul>

                   	<div class="menucat">Обслуживание</div>
                	<ul>
                   		<li><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=recount'>Пересчет функции</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=rebuild'>Перестроение функций</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=clean'>Генеральная уборка</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=syscheck'>Проверка системы</a></li>
                    </ul>

                	<div class="menucat">Резервное копирование и обновление</div>
                	<ul>
                   		<li><a href='<! HD_URL !>/admin.php?section=tools&amp;act=backup&amp;code=full'>Полное резервное копирование</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=tools&amp;act=backup&amp;code=sql'>Резервное копирование SQL</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=tools&amp;act=backup&amp;code=file'>Резервное копирование Файлов</a></li>
                    </ul>

                    <div class="menucat">Журналирование- логи</div>
                	<ul>
                  	 	<li><a href='<! HD_URL !>/admin.php?section=admin&amp;act=logs&amp;code=admin'>Логи Администратор</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=admin&amp;act=logs&amp;code=member'>Логи Пользователи</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=admin&amp;act=logs&amp;code=error'>Логи Ошибки</a></li>
                   		<li><a href='<! HD_URL !>/admin.php?section=admin&amp;act=logs&amp;code=security'>Логи Безопасность</a></li>
                   	 	<li><a href='<! HD_URL !>/admin.php?section=admin&amp;act=logs&amp;code=ticket'>Логи Тиккет</a></li>
                    </ul>
					<div id="acphelp">
                		<a href="http://docs.accord5.com">Просмотр документации по продукту (ENG)</a>
                	</div>
            	</div>
EOF;

		}
		elseif( $this->ifthd->input['section'] == 'look' )
		{
			$sidebar = <<<EOF
            	<div id="acpmenu">
                    <div class="menucat">Управление шкурами</div>
                	<ul>
                   		<li><a href='<! HD_URL !>/admin.php?section=look&amp;act=skin&amp;code=manage'>Управление шкурами</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=look&amp;act=skin&amp;code=tools'>Инструменты для шкур</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=look&amp;act=skin&amp;code=import'>Импортировать шкуру</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=look&amp;act=skin&amp;code=export'>Экспортировать шкуру</a></li>
                    </ul>

                    <div class="menucat">Управление языками</div>
                	<ul>
                   		<li><a href='<! HD_URL !>/admin.php?section=look&amp;act=lang&amp;code=manage'>Управление языками</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=look&amp;act=lang&amp;code=tools'>Инструменты для языка</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=look&amp;act=lang&amp;code=import'>Импортировать язык</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=look&amp;act=lang&amp;code=export'>Экспортировать язык</a></li>
                    </ul>
					<div id="acphelp">
                		<a href="http://docs.accord5.com">Просмотр документации по продукту (ENG)</a>
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
                   		<li><a href='http://www.accord5.com/trellis'>Страница продукта</a></li>
                    	<li><a href='http://docs.accord5.com/trellis'>Документация</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?act=tdinfo'>Trellis Desk Инфо</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?act=phpinfo'>PHP Инфо</a></li>
                    </ul>

                    <div class="menucat">Навигация</div>
                	<ul>
                   		<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=group&amp;code=acpperm'>ACP разрешения</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=tools&amp;act=maint&amp;code=syscheck'>Проверка системы</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings'>Настройки системы</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=member'>Управление пользователями</a></li>
                    </ul>

                    <div class="menucat"><a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets'>Работа с тиккетами</a></div>
                	<ul>
                   		<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=tickets&amp;code=list'>Управление тиккетами</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=depart&amp;code=list'>Управление отделами</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=depart&amp;code=add'>Добавить отдел</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=cdfields'>Пользовательские поля отдела</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=manage&amp;act=canned'>Управление шаблонами ответов</a></li>
                    </ul>

                    <div class="menucat">Журналирование- логи</div>
                	<ul>
                   		<li><a href='<! HD_URL !>/admin.php?section=admin&amp;act=logs&amp;code=admin'>Логи Администратор</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=admin&amp;act=logs&amp;code=member'>Логи Пользователи</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=admin&amp;act=logs&amp;code=error'>Логи Ошибки</a></li>
                   		<li><a href='<! HD_URL !>/admin.php?section=admin&amp;act=logs&amp;code=security'>Логи Ошибки</a></li>
                    	<li><a href='<! HD_URL !>/admin.php?section=admin&amp;act=logs&amp;code=ticket'>Логи Тиккет</a></li>
                    </ul>
					<div id="acphelp">
                		<a href="http://docs.accord5.com">Просмотр документации по продукту (ENG)</a>
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
						'<! HD_URL !>/admin.php?section=admin' => array( 'Администрация Главная', 'admin' ),
						'<! HD_URL !>/admin.php?section=manage' => array( 'Управление системой', 'manage' ),
						'<! HD_URL !>/admin.php?section=look' => array( 'Оформление и языки', 'look' ),
						'<! HD_URL !>/admin.php?section=tools' => array( 'Инструменты и обслуживание', 'tools' ),
						'http://customer.accord5.com/trellis/' => array( 'Помощь и поддержка' ),
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
		return 'Вы вошли как '. $this->ifthd->member['name'] .' <a href="<! HD_URL !>/index.php">Перейти в Helpdesk</a> <a href="<! HD_URL !>/admin.php?act=logout">Выход</a>';
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
	        	$html = "<div style='float:right;margin:-2px 0'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings&amp;code=revert&amp;id={$id}'><img src='<! IMG_DIR !>/revert_icon.gif' alt='Revert' /></a></div><div align='left'><label for='{$input_name}1'><input type='radio' name='{$input_name}' id='{$input_name}1' value='1' class='radio' checked='checked' /> Да</label>&nbsp;&nbsp;";
	        	$html .= "<label for='{$input_name}0'><input type='radio' name='{$input_name}' id='{$input_name}0' value='0' class='radio' /> Нет</label></div>";
        	}
        	else
        	{
        		$html = "<label for='{$input_name}1'><input type='radio' name='{$input_name}' id='{$input_name}1' value='1' class='radio' checked='checked' /> Да</label>&nbsp;&nbsp;";
	        	$html .= "<label for='{$input_name}0'><input type='radio' name='{$input_name}' id='{$input_name}0' value='0' class='radio' /> Нет</label>";
        	}
        }
        else
        {
        	if ( $revert )
        	{
	        	$html = "<div style='float:right;margin:-2px 0'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings&amp;code=revert&amp;id={$id}'><img src='<! IMG_DIR !>/revert_icon.gif' alt='Revert' /></a></div><div align='left'><label for='{$input_name}1'><input type='radio' name='{$input_name}' id='{$input_name}1' value='1' class='radio' /> Да</label>&nbsp;&nbsp;";
	        	$html .= "<label for='{$input_name}0'><input type='radio' name='{$input_name}' id='{$input_name}0' value='0' class='radio' checked='checked' /> Нет</label></div>";
        	}
        	else
        	{
        		$html = "<label for='{$input_name}1'><input type='radio' name='{$input_name}' id='{$input_name}1' value='1' class='radio' /> Да</label>&nbsp;&nbsp;";
	        	$html .= "<label for='{$input_name}0'><input type='radio' name='{$input_name}' id='{$input_name}0' value='0' class='radio' checked='checked' /> Нет</label>";
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
	        	$html = "<div style='float:right;margin:-2px 0'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings&amp;code=revert&amp;id={$id}'><img src='<! IMG_DIR !>/revert_icon.gif' alt='Revert' /></a></div><div align='left'><label for='{$input_name}1'><input type='radio' name='{$input_name}' id='{$input_name}1' value='1' class='radio' checked='checked' /> Включено</label>&nbsp;&nbsp;";
	        	$html .= "<label for='{$input_name}0'><input type='radio' name='{$input_name}' id='{$input_name}0' value='0' class='radio' /> Отключено</label></div>";
        	}
        	else
        	{
        		$html = "<label for='{$input_name}1'><input type='radio' name='{$input_name}' id='{$input_name}1' value='1' class='radio' checked='checked' /> Включено</label>&nbsp;&nbsp;";
	        	$html .= "<label for='{$input_name}0'><input type='radio' name='{$input_name}' id='{$input_name}0' value='0' class='radio' /> Отключено</label>";
        	}
        }
        else
        {
        	if ( $revert )
        	{
	        	$html = "<div style='float:right;margin:-2px 0'><a href='<! HD_URL !>/admin.php?section=manage&amp;act=settings&amp;code=revert&amp;id={$id}'><img src='<! IMG_DIR !>/revert_icon.gif' alt='Revert' /></a></div><div align='left'><label for='{$input_name}1'><input type='radio' name='{$input_name}' id='{$input_name}1' value='1' class='radio' /> Включено</label>&nbsp;&nbsp;";
	        	$html .= "<label for='{$input_name}0'><input type='radio' name='{$input_name}' id='{$input_name}0' value='0' class='radio' checked='checked' /> Отключено</label></div>";
        	}
        	else
        	{
        		$html = "<label for='{$input_name}1'><input type='radio' name='{$input_name}' id='{$input_name}1' value='1' class='radio' /> Включено</label>&nbsp;&nbsp;";
	        	$html .= "<label for='{$input_name}0'><input type='radio' name='{$input_name}' id='{$input_name}0' value='0' class='radio' checked='checked' /> Отключено</label>";
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

		$nav_tree = '<div id="navstrip">&raquo; <a href="<! HD_URL !>/admin.php">Домой</a>'; // Initialize for Security

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
            	<div class='righty'><span title='". $query_count ." Normal | ". $query_s_count ." Shutdown'>". $query_count ." Запрос(ов)</span> // ". $exe_time ." Секунд</div>
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

		$nav_tree = '&raquo; <a href="<! HD_URL !>/admin.php">Домой</a>'; // Initialize for Security

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