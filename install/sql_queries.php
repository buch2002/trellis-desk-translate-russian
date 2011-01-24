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
|    | Trellis Desk Installation SQL Queries
#======================================================
*/

$SQL[] = "DROP TABLE IF EXISTS `". $core->cache['install']['sql_prefix'] ."announcements`;";
$SQL[] = "DROP TABLE IF EXISTS `". $core->cache['install']['sql_prefix'] ."article_rate`;";
$SQL[] = "DROP TABLE IF EXISTS `". $core->cache['install']['sql_prefix'] ."articles`;";
$SQL[] = "DROP TABLE IF EXISTS `". $core->cache['install']['sql_prefix'] ."asessions`;";
$SQL[] = "DROP TABLE IF EXISTS `". $core->cache['install']['sql_prefix'] ."attachments`;";
$SQL[] = "DROP TABLE IF EXISTS `". $core->cache['install']['sql_prefix'] ."canned`;";
$SQL[] = "DROP TABLE IF EXISTS `". $core->cache['install']['sql_prefix'] ."categories`;";
$SQL[] = "DROP TABLE IF EXISTS `". $core->cache['install']['sql_prefix'] ."comments`;";
$SQL[] = "DROP TABLE IF EXISTS `". $core->cache['install']['sql_prefix'] ."depart_fields`;";
$SQL[] = "DROP TABLE IF EXISTS `". $core->cache['install']['sql_prefix'] ."departments`;";
$SQL[] = "DROP TABLE IF EXISTS `". $core->cache['install']['sql_prefix'] ."groups`;";
$SQL[] = "DROP TABLE IF EXISTS `". $core->cache['install']['sql_prefix'] ."in_email_log`;";
$SQL[] = "DROP TABLE IF EXISTS `". $core->cache['install']['sql_prefix'] ."languages`;";
$SQL[] = "DROP TABLE IF EXISTS `". $core->cache['install']['sql_prefix'] ."logs`;";
$SQL[] = "DROP TABLE IF EXISTS `". $core->cache['install']['sql_prefix'] ."members`;";
$SQL[] = "DROP TABLE IF EXISTS `". $core->cache['install']['sql_prefix'] ."news_comments`;";
$SQL[] = "DROP TABLE IF EXISTS `". $core->cache['install']['sql_prefix'] ."pages`;";
$SQL[] = "DROP TABLE IF EXISTS `". $core->cache['install']['sql_prefix'] ."profile_fields`;";
$SQL[] = "DROP TABLE IF EXISTS `". $core->cache['install']['sql_prefix'] ."replies`;";
$SQL[] = "DROP TABLE IF EXISTS `". $core->cache['install']['sql_prefix'] ."reply_rate`;";
$SQL[] = "DROP TABLE IF EXISTS `". $core->cache['install']['sql_prefix'] ."sessions`;";
$SQL[] = "DROP TABLE IF EXISTS `". $core->cache['install']['sql_prefix'] ."settings`;";
$SQL[] = "DROP TABLE IF EXISTS `". $core->cache['install']['sql_prefix'] ."settings_groups`;";
$SQL[] = "DROP TABLE IF EXISTS `". $core->cache['install']['sql_prefix'] ."skins`;";
$SQL[] = "DROP TABLE IF EXISTS `". $core->cache['install']['sql_prefix'] ."tickets`;";
$SQL[] = "DROP TABLE IF EXISTS `". $core->cache['install']['sql_prefix'] ."tokens`;";
$SQL[] = "DROP TABLE IF EXISTS `". $core->cache['install']['sql_prefix'] ."upg_history`;";
$SQL[] = "DROP TABLE IF EXISTS `". $core->cache['install']['sql_prefix'] ."validation`;";

$SQL[] = "CREATE TABLE `". $core->cache['install']['sql_prefix'] ."announcements` (
  `id` int(11) NOT NULL auto_increment,
  `mid` int(11) NOT NULL default '0',
  `mname` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `excerpt` text NOT NULL,
  `content` text NOT NULL,
  `email` int(1) NOT NULL default '0',
  `dis_comments` tinyint(1) NOT NULL default '0',
  `comments` int(11) NOT NULL default '0',
  `start_date` int(10) NOT NULL default '0',
  `end_date` int(10) NOT NULL default '0',
  `date` int(10) NOT NULL default '0',
  `ipadd` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$SQL[] = "CREATE TABLE `". $core->cache['install']['sql_prefix'] ."article_rate` (
  `id` int(11) NOT NULL auto_increment,
  `aid` int(11) NOT NULL default '0',
  `mid` int(11) NOT NULL default '0',
  `rating` int(11) NOT NULL default '0',
  `date` int(10) NOT NULL default '0',
  `ipadd` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$SQL[] = "CREATE TABLE `". $core->cache['install']['sql_prefix'] ."articles` (
  `id` int(11) NOT NULL auto_increment,
  `cat_id` int(11) NOT NULL default '0',
  `cat_name` varchar(255) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `article` longtext NOT NULL,
  `votes` int(11) NOT NULL default '0',
  `rating` float NOT NULL default '0',
  `views` int(11) NOT NULL default '0',
  `comments` int(11) NOT NULL default '0',
  `date` int(10) NOT NULL default '0',
  `updated` int(10) NOT NULL default '0',
  `author_id` int(11) NOT NULL default '0',
  `author_name` varchar(255) NOT NULL default '',
  `keywords` text NOT NULL,
  `dis_comments` tinyint(1) NOT NULL default '0',
  `dis_rating` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `full_index` (`name`,`description`,`article`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$SQL[] = "CREATE TABLE `". $core->cache['install']['sql_prefix'] ."asessions` (
  `s_id` varchar(33) NOT NULL default '',
  `s_mid` int(11) NOT NULL default '0',
  `s_mname` varchar(255) NOT NULL default '',
  `s_ipadd` varchar(32) NOT NULL default '',
  `s_location` varchar(255) NOT NULL default '',
  `s_time` int(10) NOT NULL default '0',
  `s_inticket` int(11) NOT NULL default '0',
  PRIMARY KEY  (`s_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$SQL[] = "CREATE TABLE `". $core->cache['install']['sql_prefix'] ."attachments` (
  `id` int(11) NOT NULL auto_increment,
  `tid` int(11) NOT NULL default '0',
  `real_name` varchar(255) NOT NULL default '',
  `original_name` varchar(255) NOT NULL default '',
  `mid` int(11) NOT NULL default '0',
  `mname` varchar(255) NOT NULL default '',
  `size` int(11) NOT NULL default '0',
  `mime` varchar(255) NOT NULL default '',
  `ipadd` varchar(32) NOT NULL default '',
  `date` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$SQL[] = "CREATE TABLE `". $core->cache['install']['sql_prefix'] ."canned` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `content` longtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$SQL[] = "CREATE TABLE `". $core->cache['install']['sql_prefix'] ."categories` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `sub_id` int(11) NOT NULL default '0',
  `sub_name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `articles` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$SQL[] = "CREATE TABLE `". $core->cache['install']['sql_prefix'] ."comments` (
  `id` int(11) NOT NULL auto_increment,
  `aid` int(11) NOT NULL default '0',
  `mid` int(11) NOT NULL default '0',
  `mname` varchar(255) NOT NULL default '',
  `comment` longtext NOT NULL,
  `date` int(10) NOT NULL default '0',
  `ipadd` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$SQL[] = "CREATE TABLE `". $core->cache['install']['sql_prefix'] ."depart_fields` (
  `id` int(11) NOT NULL auto_increment,
  `fkey` varchar(255) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `type` varchar(25) NOT NULL default '',
  `extra` text NOT NULL,
  `required` tinyint(1) NOT NULL default '0',
  `departs` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$SQL[] = "CREATE TABLE `". $core->cache['install']['sql_prefix'] ."departments` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `sub_id` int(11) NOT NULL default '0',
  `sub_name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `tickets` int(11) NOT NULL default '0',
  `placeholder` tinyint(1) NOT NULL default '0',
  `ticket_own_close` tinyint(1) NOT NULL default '0',
  `ticket_own_reopen` tinyint(1) NOT NULL default '0',
  `can_escalate` tinyint(1) NOT NULL default '0',
  `escalate_depart` int(11) NOT NULL default '0',
  `escalate_wait` int(11) NOT NULL default '0',
  `close_reason` tinyint(1) NOT NULL default '0',
  `auto_close` int(11) NOT NULL default '0',
  `can_attach` tinyint(1) NOT NULL default '0',
  `email_pipe` tinyint(1) NOT NULL default '0',
  `guest_pipe` tinyint(1) NOT NULL default '0',
  `incoming_email` varchar(255) NOT NULL default '',
  `email_pop3` tinyint(1) NOT NULL default '0',
  `pop3_host` varchar(255) NOT NULL default '',
  `pop3_user` varchar(255) NOT NULL default '',
  `pop3_pass` varchar(255) NOT NULL default '',
  `auto_assign` int(11) NOT NULL default '0',
  `custom_fields` text NOT NULL,
  `position` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$SQL[] = "CREATE TABLE `". $core->cache['install']['sql_prefix'] ."groups` (
  `g_id` int(11) NOT NULL auto_increment,
  `g_name` varchar(255) NOT NULL default '',
  `g_members` int(11) NOT NULL default '0',
  `g_ticket_access` tinyint(1) NOT NULL default '0',
  `g_new_tickets` tinyint(1) NOT NULL default '0',
  `g_kb_access` tinyint(1) NOT NULL default '0',
  `g_kb_rate` tinyint(1) NOT NULL default '0',
  `g_kb_comment` tinyint(1) NOT NULL default '0',
  `g_news_comment` tinyint(1) NOT NULL default '0',
  `g_ticket_edit` tinyint(1) NOT NULL default '0',
  `g_reply_rate` tinyint(1) NOT NULL default '0',
  `g_reply_edit` tinyint(1) NOT NULL default '0',
  `g_reply_delete` tinyint(1) NOT NULL default '0',
  `g_change_skin` tinyint(1) NOT NULL default '0',
  `g_change_lang` tinyint(1) NOT NULL default '0',
  `g_com_edit_all` tinyint(1) NOT NULL default '0',
  `g_com_delete_all` tinyint(1) NOT NULL default '0',
  `g_acp_access` tinyint(1) NOT NULL default '0',
  `g_acp_perm` text NOT NULL,
  `g_depart_perm` text NOT NULL,
  `g_ticket_own_close` tinyint(1) NOT NULL default '0',
  `g_m_depart_perm` text NOT NULL,
  `g_ticket_attach` tinyint(1) NOT NULL default '0',
  `g_upload_size_max` int(11) NOT NULL default '0',
  PRIMARY KEY  (`g_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$SQL[] = "CREATE TABLE `". $core->cache['install']['sql_prefix'] ."in_email_log` (
  `id` int(11) NOT NULL auto_increment,
  `email` varchar(255) NOT NULL,
  `date` int(10) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$SQL[] = "CREATE TABLE `". $core->cache['install']['sql_prefix'] ."languages` (
  `id` int(11) NOT NULL auto_increment,
  `lkey` varchar(5) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `users` int(11) NOT NULL default '0',
  `default` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$SQL[] = "CREATE TABLE `". $core->cache['install']['sql_prefix'] ."logs` (
  `id` int(11) NOT NULL auto_increment,
  `mid` int(11) NOT NULL default '0',
  `mname` varchar(255) NOT NULL default '',
  `action` text NOT NULL,
  `extra` text NOT NULL,
  `type` tinyint(1) NOT NULL default '0',
  `level` tinyint(1) NOT NULL default '0',
  `date` int(10) NOT NULL default '0',
  `ipadd` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$SQL[] = "CREATE TABLE `". $core->cache['install']['sql_prefix'] ."members` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `password` varchar(255) NOT NULL default '',
  `pass_salt` varchar(255) NOT NULL default '',
  `login_key` varchar(255) NOT NULL default '',
  `mgroup` int(11) NOT NULL default '0',
  `msub_group` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `joined` int(10) NOT NULL default '0',
  `ipadd` varchar(32) NOT NULL default '',
  `open_tickets` int(11) NOT NULL default '0',
  `tickets` int(11) NOT NULL default '0',
  `rating` float NOT NULL default '0',
  `rating_total` int(11) NOT NULL default '0',
  `votes` int(11) NOT NULL default '0',
  `email_notify` tinyint(1) NOT NULL default '0',
  `email_html` tinyint(1) NOT NULL default '0',
  `email_new_ticket` tinyint(1) NOT NULL default '0',
  `email_ticket_reply` tinyint(1) NOT NULL default '0',
  `email_announce` tinyint(1) NOT NULL default '0',
  `ban_ticket_center` tinyint(1) NOT NULL default '0',
  `ban_ticket_open` tinyint(1) NOT NULL default '0',
  `ban_ticket_escalate` tinyint(1) NOT NULL default '0',
  `ban_ticket_rate` tinyint(1) NOT NULL default '0',
  `ban_kb` tinyint(1) NOT NULL default '0',
  `ban_kb_comment` tinyint(1) NOT NULL default '0',
  `ban_kb_rate` tinyint(1) NOT NULL default '0',
  `time_zone` varchar(3) NOT NULL default '',
  `dst_active` tinyint(1) NOT NULL default '0',
  `lang` varchar(3) NOT NULL default '',
  `skin` int(11) NOT NULL default '0',
  `email_val` tinyint(1) NOT NULL default '0',
  `admin_val` tinyint(1) NOT NULL default '0',
  `email_staff_new_ticket` tinyint(1) NOT NULL default '0',
  `email_staff_ticket_reply` tinyint(1) NOT NULL default '0',
  `use_rte` tinyint(1) NOT NULL default '0',
  `cpfields` text NOT NULL,
  `rss_key` varchar(255) NOT NULL default '',
  `assigned` int(11) NOT NULL default '0',
  `signature` text NOT NULL,
  `auto_sig` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$SQL[] = "CREATE TABLE `". $core->cache['install']['sql_prefix'] ."news_comments` (
  `id` int(11) NOT NULL auto_increment,
  `nid` int(11) NOT NULL default '0',
  `mid` int(11) NOT NULL default '0',
  `mname` varchar(255) NOT NULL default '',
  `comment` longtext NOT NULL,
  `date` int(10) NOT NULL default '0',
  `ipadd` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$SQL[] = "CREATE TABLE `". $core->cache['install']['sql_prefix'] ."pages` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `template` varchar(255) NOT NULL default '',
  `content` longtext NOT NULL,
  `type` tinyint(1) NOT NULL default '0',
  `date` int(10) NOT NULL default '0',
  `ipadd` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$SQL[] = "CREATE TABLE `". $core->cache['install']['sql_prefix'] ."profile_fields` (
  `id` int(11) NOT NULL auto_increment,
  `reg` tinyint(1) NOT NULL default '0',
  `fkey` varchar(255) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `type` varchar(25) NOT NULL default '',
  `extra` text NOT NULL,
  `perms` text NOT NULL,
  `required` tinyint(1) NOT NULL default '0',
  `ticket` tinyint(1) NOT NULL default '0',
  `staff` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

$SQL[] = "CREATE TABLE `". $core->cache['install']['sql_prefix'] ."replies` (
  `id` int(11) NOT NULL auto_increment,
  `tid` int(11) NOT NULL default '0',
  `mid` int(11) NOT NULL default '0',
  `mname` varchar(255) NOT NULL default '',
  `message` longtext NOT NULL,
  `staff` tinyint(1) NOT NULL default '0',
  `rte` tinyint(1) NOT NULL default '0',
  `secret` tinyint(1) NOT NULL default '0',
  `attach_id` int(11) NOT NULL default '0',
  `date` int(10) NOT NULL default '0',
  `ipadd` varchar(32) NOT NULL default '',
  `guest` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$SQL[] = "CREATE TABLE `". $core->cache['install']['sql_prefix'] ."reply_rate` (
  `id` int(11) NOT NULL auto_increment,
  `tid` int(11) NOT NULL default '0',
  `rid` int(11) NOT NULL default '0',
  `mid` int(11) NOT NULL default '0',
  `rating` int(11) NOT NULL default '0',
  `date` int(10) NOT NULL default '0',
  `ipadd` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$SQL[] = "CREATE TABLE `". $core->cache['install']['sql_prefix'] ."sessions` (
  `s_id` varchar(33) NOT NULL default '',
  `s_mid` int(11) NOT NULL default '0',
  `s_mname` varchar(255) NOT NULL default '',
  `s_email` varchar(255) NOT NULL default '',
  `s_ipadd` varchar(32) NOT NULL default '',
  `s_location` varchar(255) NOT NULL default '',
  `s_time` int(10) NOT NULL default '0',
  `s_guest` tinyint(1) NOT NULL default '0',
  `s_tkey` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`s_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$SQL[] = "CREATE TABLE `". $core->cache['install']['sql_prefix'] ."settings` (
  `cf_id` int(11) NOT NULL auto_increment,
  `cf_key` varchar(255) NOT NULL default '',
  `cf_title` varchar(255) NOT NULL default '',
  `cf_description` text NOT NULL,
  `cf_group` int(11) NOT NULL default '0',
  `cf_type` varchar(255) NOT NULL default '',
  `cf_default` text NOT NULL,
  `cf_extra` text NOT NULL,
  `cf_value` text NOT NULL,
  `cf_protected` tinyint(4) NOT NULL default '0',
  `cf_position` int(11) NOT NULL default '0',
  `cf_cache` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`cf_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$SQL[] = "CREATE TABLE `". $core->cache['install']['sql_prefix'] ."settings_groups` (
  `cg_id` int(11) NOT NULL auto_increment,
  `cg_key` varchar(255) NOT NULL default '',
  `cg_name` varchar(255) NOT NULL default '',
  `cg_description` text NOT NULL,
  `cg_set_count` int(11) NOT NULL default '0',
  `cg_hide` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`cg_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$SQL[] = "CREATE TABLE `". $core->cache['install']['sql_prefix'] ."skins` (
  `id` tinyint(4) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `img_dir` varchar(255) NOT NULL default '',
  `users` int(11) NOT NULL default '0',
  `default` tinyint(1) NOT NULL default '0',
  `author` varchar(255) NOT NULL default '',
  `author_email` varchar(255) NOT NULL default '',
  `author_web` varchar(255) NOT NULL default '',
  `notes` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$SQL[] = "CREATE TABLE `". $core->cache['install']['sql_prefix'] ."tickets` (
  `id` int(11) NOT NULL auto_increment,
  `tkey` varchar(255) NOT NULL default '',
  `did` int(11) NOT NULL default '0',
  `dname` varchar(255) NOT NULL default '',
  `mid` int(11) NOT NULL default '0',
  `mname` varchar(255) NOT NULL default '',
  `amid` int(11) NOT NULL default '0',
  `amname` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `subject` varchar(255) NOT NULL default '',
  `priority` tinyint(3) NOT NULL default '0',
  `message` longtext NOT NULL,
  `date` int(10) NOT NULL default '0',
  `last_reply` int(10) NOT NULL default '0',
  `last_reply_staff` int(10) NOT NULL default '0',
  `last_mid` int(11) NOT NULL default '0',
  `last_mname` varchar(255) NOT NULL default '',
  `ipadd` varchar(32) NOT NULL default '',
  `replies` int(11) NOT NULL default '0',
  `votes` int(11) NOT NULL default '0',
  `rating` float NOT NULL default '0',
  `rating_total` float NOT NULL default '0',
  `notes` text NOT NULL,
  `status` tinyint(3) NOT NULL default '0',
  `close_mid` int(11) NOT NULL default '0',
  `close_mname` varchar(255) NOT NULL default '',
  `close_reason` text NOT NULL,
  `auto_close` int(10) NOT NULL default '0',
  `attach_id` int(11) NOT NULL default '0',
  `cdfields` text NOT NULL,
  `guest` tinyint(1) NOT NULL default '0',
  `guest_email` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$SQL[] = "CREATE TABLE `". $core->cache['install']['sql_prefix'] ."tokens` (
  `id` int(11) NOT NULL auto_increment,
  `token` varchar(255) NOT NULL default '',
  `type` varchar(32) NOT NULL default '',
  `ipadd` varchar(32) NOT NULL default '',
  `date` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$SQL[] = "CREATE TABLE `". $core->cache['install']['sql_prefix'] ."upg_history` (
  `id` int(11) NOT NULL auto_increment,
  `verid` int(11) NOT NULL default '0',
  `verhuman` varchar(255) NOT NULL default '',
  `date` int(10) NOT NULL default '0',
  `username` varchar(255) NOT NULL default '',
  `ukey` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$SQL[] = "CREATE TABLE `". $core->cache['install']['sql_prefix'] ."validation` (
  `id` varchar(255) NOT NULL default '',
  `mid` int(11) NOT NULL default '0',
  `mname` varchar(255) NOT NULL default '',
  `new_email` varchar(255) NOT NULL default '',
  `date` int(10) NOT NULL default '0',
  `type` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."announcements` VALUES (1, 1, '". $core->cache['install']['admin_user'] ."', 'Welcome to Trellis Desk', 'This is a test announcement that can be deleted at any time.  To manage your announcements, login to the ACP, go to Management, and then click List Announcements.', 'The ACCORD5 Team would like to welcome you to Trellis Desk.&lt;br /&gt;&lt;br /&gt;We hope you find that Trellis Desk suits your needs.&amp;nbsp; If you ever need support, just &lt;a href=&quot;http://customer.accord5.com/trellis/&quot; target=&quot;_blank&quot;&gt;send us a ticket&lt;/a&gt;.&amp;nbsp; The &lt;a href=&quot;http://docs.accord5.com/&quot; target=&quot;_blank&quot;&gt;documentation&lt;/a&gt; is also a great resource.&lt;br /&gt;&lt;br /&gt;Enjoy Trellis Desk!', 0, 0, 0, 0, 0, ". time() .", '". $input['ip_address'] ."');";

$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."articles` VALUES (1, 1, 'Test Category', 'Test Article', 'This is a test article that can be deleted at any time.', 'This is a test article that can be deleted at any time.\r\n\r\nTo manage your articles and knowledge base categories, login to the ACP, click Management, and then click the appropriate link under Knowledge Base Control.', 0, 0, 0, 0, ". time() .", 0, 1, '". $core->cache['install']['admin_user'] ."', 'test|article', 0, 0);";

$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."canned` VALUES (1, 'Test Canned Reply', 'This is a test canned reply that can be deleted at any time.', 'This is a test canned reply that can be deleted at any time.');";

$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."categories` VALUES (1, 'Test Category', 0, '', 'This is a test category that can be deleted at any time.', 1);";

$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."departments` VALUES (1, 'Test Department', 0, '', 'This is a test department that can be deleted at any time.', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, '', '', '', 0, '', 0);";

$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."groups` VALUES (1, 'Members', 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 0, 0, 0, 'N;', 'N;', 1, 'a:1:{i:1;i:1;}', 1, 2048);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."groups` VALUES (2, 'Guests', 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'N;', 'N;', 0, 'N;', 0, 0);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."groups` VALUES (3, 'Validating', 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'N;', 'N;', 0, 'N;', 0, 0);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."groups` VALUES (4, 'Administrators', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 'a:76:{s:5:\"admin\";i:1;s:10:\"admin_logs\";i:1;s:16:\"admin_logs_admin\";i:1;s:17:\"admin_logs_member\";i:1;s:16:\"admin_logs_email\";i:1;s:16:\"admin_logs_error\";i:1;s:19:\"admin_logs_security\";i:1;s:17:\"admin_logs_ticket\";i:1;s:16:\"admin_logs_prune\";i:1;s:6:\"manage\";i:1;s:13:\"manage_ticket\";i:1;s:19:\"manage_ticket_reply\";i:1;s:25:\"manage_ticket_assign_self\";i:1;s:24:\"manage_ticket_assign_any\";i:1;s:18:\"manage_ticket_hold\";i:1;s:22:\"manage_ticket_escalate\";i:1;s:18:\"manage_ticket_move\";i:1;s:19:\"manage_ticket_close\";i:1;s:20:\"manage_ticket_delete\";i:1;s:20:\"manage_ticket_reopen\";i:1;s:13:\"manage_canned\";i:1;s:17:\"manage_canned_add\";i:1;s:18:\"manage_canned_edit\";i:1;s:20:\"manage_canned_delete\";i:1;s:13:\"manage_depart\";i:1;s:17:\"manage_depart_add\";i:1;s:18:\"manage_depart_edit\";i:1;s:20:\"manage_depart_delete\";i:1;s:21:\"manage_depart_reorder\";i:1;s:21:\"manage_depart_cfields\";i:1;s:15:\"manage_announce\";i:1;s:19:\"manage_announce_add\";i:1;s:20:\"manage_announce_edit\";i:1;s:22:\"manage_announce_delete\";i:1;s:13:\"manage_member\";i:1;s:17:\"manage_member_add\";i:1;s:18:\"manage_member_edit\";i:1;s:20:\"manage_member_delete\";i:1;s:21:\"manage_member_approve\";i:1;s:21:\"manage_member_cfields\";i:1;s:12:\"manage_group\";i:1;s:16:\"manage_group_add\";i:1;s:17:\"manage_group_edit\";i:1;s:19:\"manage_group_delete\";i:1;s:14:\"manage_article\";i:1;s:18:\"manage_article_add\";i:1;s:19:\"manage_article_edit\";i:1;s:21:\"manage_article_delete\";i:1;s:10:\"manage_cat\";i:1;s:14:\"manage_cat_add\";i:1;s:15:\"manage_cat_edit\";i:1;s:17:\"manage_cat_delete\";i:1;s:12:\"manage_pages\";i:1;s:16:\"manage_pages_add\";i:1;s:17:\"manage_pages_edit\";i:1;s:19:\"manage_pages_delete\";i:1;s:15:\"manage_settings\";i:1;s:22:\"manage_settings_update\";i:1;s:4:\"look\";i:1;s:9:\"look_skin\";i:1;s:16:\"look_skin_manage\";i:1;s:15:\"look_skin_tools\";i:1;s:16:\"look_skin_import\";i:1;s:16:\"look_skin_export\";i:1;s:9:\"look_lang\";i:1;s:16:\"look_lang_manage\";i:1;s:15:\"look_lang_tools\";i:1;s:16:\"look_lang_import\";i:1;s:16:\"look_lang_export\";i:1;s:5:\"tools\";i:1;s:11:\"tools_maint\";i:1;s:19:\"tools_maint_recount\";i:1;s:17:\"tools_maint_clean\";i:1;s:16:\"tools_maint_optm\";i:1;s:20:\"tools_maint_syscheck\";i:1;s:12:\"tools_backup\";i:1;}', 'N;', 1, 'a:1:{i:1;i:1;}', 1, 0);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."groups` VALUES (5, 'Staff', 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 'a:41:{s:5:\"admin\";i:1;s:10:\"admin_logs\";i:1;s:16:\"admin_logs_admin\";i:1;s:17:\"admin_logs_member\";i:1;s:16:\"admin_logs_email\";i:1;s:16:\"admin_logs_error\";i:1;s:19:\"admin_logs_security\";i:1;s:17:\"admin_logs_ticket\";i:1;s:6:\"manage\";i:1;s:13:\"manage_ticket\";i:1;s:19:\"manage_ticket_reply\";i:1;s:25:\"manage_ticket_assign_self\";i:1;s:18:\"manage_ticket_hold\";i:1;s:22:\"manage_ticket_escalate\";i:1;s:18:\"manage_ticket_move\";i:1;s:19:\"manage_ticket_close\";i:1;s:20:\"manage_ticket_delete\";i:1;s:20:\"manage_ticket_reopen\";i:1;s:13:\"manage_canned\";i:1;s:15:\"manage_announce\";i:1;s:19:\"manage_announce_add\";i:1;s:20:\"manage_announce_edit\";i:1;s:22:\"manage_announce_delete\";i:1;s:13:\"manage_member\";i:1;s:17:\"manage_member_add\";i:1;s:18:\"manage_member_edit\";i:1;s:20:\"manage_member_delete\";i:1;s:21:\"manage_member_approve\";i:1;s:14:\"manage_article\";i:1;s:18:\"manage_article_add\";i:1;s:19:\"manage_article_edit\";i:1;s:21:\"manage_article_delete\";i:1;s:10:\"manage_cat\";i:1;s:14:\"manage_cat_add\";i:1;s:15:\"manage_cat_edit\";i:1;s:17:\"manage_cat_delete\";i:1;s:5:\"tools\";i:1;s:11:\"tools_maint\";i:1;s:19:\"tools_maint_recount\";i:1;s:16:\"tools_maint_optm\";i:1;s:20:\"tools_maint_syscheck\";i:1;}', 'N;', 1, 'a:1:{i:1;i:1;}', 1, 0);";

$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."languages` VALUES (1, 'en', 'English', 1, 1);";

$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."members` VALUES (1, '". $core->cache['install']['admin_user'] ."', '". $core->cache['install']['admin_email'] ."', '". $core->cache['install']['admin_pass_hash'] ."', '". $core->cache['install']['admin_pass_salt'] ."', '". $core->cache['install']['admin_login_key'] ."', 4, '', 'Administrator', ". time() .", '". $input['ip_address'] ."', 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, ". intval( $core->cache['install']['admin_time_zone'] ) .", ". intval( $core->cache['install']['admin_dst_active'] ) .", 'en', 1, 1, 1, ". intval( $core->cache['install']['admin_email_ticket'] ) .", ". intval( $core->cache['install']['admin_email_reply'] ) .", ". intval( $core->cache['install']['admin_use_rte'] ) .", '', '". $core->cache['install']['admin_rss_key'] ."', 0, '', 0);";

$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."pages` VALUES (1, 'Test Page', 'This is a test page that can be deleted at any time.', 'custom_page', 'This is a test page that can be deleted at any time.', 0, ". time() .", '". $input['ip_address'] ."');";

$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (1, 'hd_name', 'Help Desk Name', 'This is the name of your help desk system.  It''s used when relating to this system.', 1, 'input', '". $core->cache['install']['set_hd_name'] ."', '', '". $core->cache['install']['set_hd_name'] ."', 1, 1, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (2, 'announce_amount', 'Announcements to Show on Portal', 'This is the number of announcements that will be shown on the portal page.', 7, 'input', '3', '', '3', 1, 3, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (3, 'recent_articles', 'Recent Articles to Show', 'This is the number of recent articles that will be shown on the portal / main page.', 1, 'input', '5', '', '5', 1, 4, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (4, 'popular_articles', 'Most Popular Articles to Show', 'This is the number of most popular articles that will be shown on the portal / main page.', 1, 'input', '5', '', '5', 1, 5, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (5, 'enable_registration', 'Allow Registration', 'If yes, guests will be able to register on this system as a new member.', 2, 'yes_no', '1', '', '1', 1, 1, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (6, 'email_validation', 'Require Email Validation', 'If yes, users will be required to verify their email before being placed in the members group.', 2, 'yes_no', '". $core->cache['install']['set_email_val'] ."', '', '". $core->cache['install']['set_email_val'] ."', 1, 2, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (7, 'session_timeout', 'Session Timeout', 'The number of minutes a session lasts before it expires.', 2, 'input', '20', '', '20', 1, 6, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (8, 'shutdown_queries', 'Enable Shutdown Queries', 'If yes, some non-important queries to the currently loading page will be saved and run after the output.', 1, 'yes_no', '1', '', '1', 1, 2, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (9, 'enable_gzip', 'Enable GZIP Compression', 'If yes, the HTML output to the browser will be compressed to save loading time.  Some servers do not support this.', 1, 'yes_no', '1', '', '1', 1, 3, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (10, 'allow_new_tickets', 'Allow New Tickets', 'If yes, members will be allowed to access the ''Open a Ticket'' page and submit a new ticket.', 3, 'yes_no', '1', '', '1', 1, 1, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (29, 'enable_ticket_rte', 'Enable Rich Text Editor', 'If enabled, staff will be able to reply to tickets using TinyMCE Rich Text Editor.', 3, 'yes_no', '1', '', '1', 1, 5, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (12, 'enable_kb', 'Enable Knowledge Base', 'If yes, the knowledge base section will be active.', 4, 'yes_no', '1', '', '1', 1, 1, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (13, 'allow_kb_rating', 'Allow Rating', 'If yes, members will be able to rate KB articles.  (Per group permission).', 4, 'yes_no', '". $core->cache['install']['set_allow_kb_rating'] ."', '', '". $core->cache['install']['set_allow_kb_rating'] ."', 1, 2, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (14, 'allow_kb_comment', 'Allow Commenting', 'If yes, members will be able to comment on KB articles.  (Per group permission).', 4, 'yes_no', '". $core->cache['install']['set_allow_kb_comment'] ."', '', '". $core->cache['install']['set_allow_kb_comment'] ."', 1, 3, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (15, 'ban_names', 'Banned Usernames', 'List of banned usernames, seperated by a line break.', 5, 'textarea', '', '', '', 1, 1, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (16, 'ban_emails', 'Banned Emails', 'List of banned emails, seperated by a line break.', 5, 'textarea', '', '', '', 1, 2, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (17, 'ban_ips', 'Banned IPs', 'List of banned ip addresses, seperated by a line break.', 5, 'textarea', '', '', '', 1, 3, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (18, 'allow_reply_rating', 'Allow Reply Rating', 'If yes, members will be able to rate staff replies to tickets with thumbs up or down.', 3, 'yes_no', '". $core->cache['install']['set_allow_reply_rating'] ."', '', '". $core->cache['install']['set_allow_reply_rating'] ."', 1, 3, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (19, 'allow_change_skin', 'Allow Skin Changing', 'If yes users will be able to change their skin based on group permissions.', 6, 'yes_no', '1', '', '1', 1, 1, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (20, 'allow_change_lang', 'Allow Language Changing', 'If yes, users will be able to change their language based on group permissions.', 6, 'yes_no', '1', '', '1', 1, 2, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (21, 'admin_validation', 'Require Admin Validation', 'If yes, an administrator must approve all new accounts before they are moved into the members group.', 2, 'yes_no', '". $core->cache['install']['set_admin_val'] ."', '', '". $core->cache['install']['set_admin_val'] ."', 1, 3, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (22, 'out_email', 'Outgoing Email', 'This email will be used when Trellis Desk sends emails to your users.', 8, 'input', '". $core->cache['install']['set_out_email'] ."', '', '". $core->cache['install']['set_out_email'] ."', 1, 1, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (23, 'use_captcha', 'Enable Captcha', 'If enabled, users will be required to enter a code from a captcha image on some forms.', 2, 'yes_no', '". $core->cache['install']['captcha'] ."', '', '". $core->cache['install']['captcha'] ."', 1, 7, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (24, 'enable_kb_rte', 'Enable Rich Text Editor', 'If enabled, users will be able to edit articles using TinyMCE Rich Text Editor.', 4, 'yes_no', '1', '', '1', 1, 4, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (25, 'color_priorities', 'Color Priorities', 'If yes, ticket priorities will be color coded for better identification in ticket lists.', 3, 'yes_no', '1', '', '1', 1, 4, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (26, 'session_ip_check', 'Check Session IP', 'If yes, the IP address of the user will be verified with the database on each load.', 2, 'yes_no', '0', '', '0', 1, 8, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (27, 'use_form_tokens', 'Enable Form Tokens', 'If enabled, a random token hash will be verified with the database on each form to help prevent spam.', 2, 'yes_no', '0', '', '0', 1, 9, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (28, 'token_ip_check', 'Check Token IP', 'If yes, the IP address of the user will be verified with each form token.', 2, 'yes_no', '1', '', '1', 1, 10, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (30, 'ticket_attachments', 'Allow Attachments', 'If yes, users will be able to attach files to tickets, based on group and department permissions.', 3, 'yes_no', '". $core->cache['install']['uploads'] ."', '', '". $core->cache['install']['uploads'] ."', 1, 6, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (31, 'upload_dir', 'Upload Directory', 'Full path to upload directory.', 1, 'input', '". $upload_path ."', '', '". $upload_path ."', 1, 7, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (32, 'upload_url', 'Upload URL', 'URL to the upload directory.', 1, 'input', '". $upload_url ."', '', '". $upload_url ."', 1, 8, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (33, 'upload_exts', 'Allowed Upload Extensions', 'A list of allowed upload extensions.  Seperate by a pipe (|).', 1, 'input', '.gif|.jpeg|.jpg|.png|.html|.doc|.docx|.xls|.xlsx|.txt|.pdf|.zip|.gz|.rar|.tar', '', '.gif|.jpeg|.jpg|.png|.html|.doc|.docx|.xls|.xlsx|.txt|.pdf|.zip|.gz|.rar|.tar', 1, 9, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (34, 'enable_news', 'Enable Announcements', 'If yes, the announcement system will be active.', 7, 'yes_no', '1', '', '1', 1, 1, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (35, 'display_qnews', 'Display Announcements on Portal', 'If yes, an announcements section will be added to the portal displaying the excerpts for each item.', 7, 'yes_no', '1', '', '1', 1, 2, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (36, 'enable_news_page', 'Enable News Page', 'If yes, the news page will be active.  The news page displays the full details of recent news items.', 7, 'yes_no', '1', '', '1', 1, 5, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (37, 'news_page_amount', 'Announcements to Show on News Page', 'The number of announcements that will be shown on the news page.  Leave blank to display all.', 7, 'input', '10', '', '10', 1, 6, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (38, 'news_comments', 'Allow Commenting', 'If yes, members will be able to comment on announcements. (Per group permission).', 7, 'yes_no', '". $core->cache['install']['set_news_comments'] ."', '', '". $core->cache['install']['set_news_comments'] ."', 1, 7, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (39, 'enable_news_rte', 'Enable Rich Text Editor', 'If enabled, users will be able to edit announcements using TinyMCE Rich Text Editor.', 7, 'yes_no', '1', '', '1', 1, 8, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (41, 'guest_ticket_emails', 'Allow Guest Ticket Notification Emails', 'If yes, a guest will have the option of receiving email notifications when updates have been made to their ticket.', 3, 'yes_no', '1', '', '1', 1, 7, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (42, 'guest_upgrade', 'Allow Guest Upgrading', 'If yes, guests will be able to upgrade their account to a registered member by simply providing additionally information, rather than having to manually register.  The guests tickets will also be saved and accessible in the registered account.', 3, 'yes_no', '1', '', '1', 1, 8, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (43, 'cookie_domain', 'Cookie Domain', 'The domain of all cookies set by Trellis Desk.  Use .yourdomain.com for global cookies.', 1, 'input', '', '', '', 1, 9, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (44, 'cookie_prefix', 'Cookie Prefix', 'Prefix used for all cookies names.  Allows multiple installations under the same path.', 1, 'input', '', '', '', 1, 10, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (45, 'cookie_path', 'Cookie Path', 'Relative path to Trellis Desk installation.  Usually this can be left blank.', 1, 'input', '', '', '', 1, 11, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (46, 'tickets_suggest', 'Enable KB Suggestions', 'If enabled, Trellis Desk will search the KB for articles that might answer the users'' inquiry before the ticket is sent.', 3, 'yes_no', '". $core->cache['install']['set_tickets_suggest'] ."', '', '". $core->cache['install']['set_tickets_suggest'] ."', 1, 2, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (47, 'news_excerpt_trim', 'Excerpt Character Limit', 'Character count cut-off for excerpts (only applies to announcements that do not have a custom excerpt).  Leave blank to disable.', 7, 'input', '200', '', '200', 1, 4, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (48, 'hour_offset', 'Hour Offset', 'Hour offset for Trellis Desk time.  Only adjust if your server''s time is not correctly set to GMT.', 1, 'input', '', '', '', 1, 13, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (49, 'minute_offset', 'Minute Offset', 'Minute offset for Trellis Desk time.  Only adjust if your server''s time is not correctly set to GMT.', 1, 'input', '', '', '', 1, 14, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (50, 'val_hours_p', 'Password Validation Expiration', 'The amount of hours in which a reset password validation code will expire.', 2, 'input', '1', '', '1', 1, 5, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (51, 'val_hours_e', 'Email Validation Expiration', 'The amount of hours in which a email validation code will expire.', 2, 'input', '168', '', '168', 1, 4, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (52, 'acp_help', 'Show ACP Inline Help', 'If set to yes, additional documentation will be available for several ACP settings.  To view this information, simply click the Toggle Information link.', 1, 'yes_no', '1', '', '1', 1, 15, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (53, 'email_method', 'Email Method', 'Your outgoing emails will be sent using this method.', 8, 'dropdown', 'native', 'native=PHP mail()\r\nsmtp=SMTP', 'native', 1, 2, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (54, 'smtp_host', 'SMTP Host', 'SMTP Host for outgoing emails (only applies if the Email Method is set to SMTP).', 8, 'input', 'localhost', '', 'localhost', 1, 3, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (55, 'smtp_port', 'SMTP Port', 'The connection port for the above SMTP host (only applies if the Email Method is set to SMTP).  This is usually 25.', 8, 'input', '25', '', '25', 1, 4, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (56, 'smtp_user', 'SMTP Username', 'SMTP username if authentication is required for SMTP (only applies if the Email Method is set to SMTP).', 8, 'input', '', '', '', 1, 5, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (57, 'smtp_pass', 'SMTP Password', 'SMTP password if authentication is required for SMTP (only applies if the Email Method is set to SMTP).  This password will be stored in plaintext in your database.', 8, 'input', '', '', '', 1, 6, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (58, 'smtp_encryption', 'SMTP Encryption', 'SMTP encryption method if your SMTP host requires it (only applies if the Email Method is set to SMTP).', 8, 'dropdown', '0', '0=None\r\nssl=SSL\r\ntls=TLS', '0', 1, 7, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (59, 'email_flood', 'Incoming Flood Prevention', 'If enabled, Trellis Desk will attempt to prevent incoming email floods and infinite loops due to auto-responders on piping and POP3.', 8, 'enabled_disabled', '1', '', '1', 1, 7, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (60, 'email_subject_regex', 'Subject Regular Expression', 'This is the regular expression used when detecting the ticket ID number for email ticket replies.', 8, 'input', '/Ticket ID #&#40;[0-9]+&#41;/i', '', '/Ticket ID #&#40;[0-9]+&#41;/i', 1, 8, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (61, 'email_use_rline', 'Enable Reply Above Line', 'If enabled, Trellis Desk will search for the reply line and only include the content above in ticket replies.', 8, 'yes_no', '1', '', '1', 1, 9, 1);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings` VALUES (62, 'email_reply_line', 'Reply Above Line', 'This is line Trellis Desk searches for in email ticket replies as the marker for the end of the reply message.', 8, 'input', '==== REPLY ABOVE THIS LINE ====', '', '==== REPLY ABOVE THIS LINE ====', 1, 10, 1);";

$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings_groups` VALUES (1, 'general', 'General Configuration', 'Basic settings for Help Desk such as URLs, paths, and global features.', 14, 0);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings_groups` VALUES (2, 'security', 'Security &amp; Privacy', 'Settings that control important security features such as session timeouts, IP matching, registration, etc.', 10, 0);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings_groups` VALUES (3, 'ticket', 'Ticket Settings', 'General ticket settings such as escalation time.', 8, 0);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings_groups` VALUES (4, 'kb', 'Knowledge Base Settings', 'General settings for the knowledge base such as allow the rating of articles, allow commenting, etc.', 4, 0);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings_groups` VALUES (5, 'ban', 'Ban Filters', 'These settings control the ban filters for Trellis Desk.', 3, 0);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings_groups` VALUES (6, 'skin_lang', 'Skins &amp; Languages', 'Settings such as allow members to change skin / language.', 2, 0);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings_groups` VALUES (7, 'news', 'Announcement Settings', 'General settings for the announcement system such as RTE support, commenting, etc.', 8, 0);";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."settings_groups` VALUES (8, 'email', 'Email Configuration', 'Email settings and configuration such as mailing method, outgoing email, etc.', 11, 0);";

$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."skins` VALUES (1, 'Trellis Desk Default', 'default', 1, 1, 'ACCORD5', 'sales@accord5.com', 'http://www.accord5.com/', '&copy; 2007 ACCORD5');";
$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."skins` VALUES (2, 'Trellis Desk Classic', 'classic', 0, 0, 'ACCORD5', 'sales@accord5.com', 'http://www.accord5.com/', '&copy; 2007 ACCORD5');";

$SQL[] = "INSERT INTO `". $core->cache['install']['sql_prefix'] ."upg_history` VALUES (1, ". VER_NUM .", '". VER_HUM ."', ". time() .", '". $core->cache['install']['admin_user'] ."', '');";

?>