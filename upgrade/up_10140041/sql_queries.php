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
|    | Trellis Desk Upgrade 10140041 SQL Queries
#======================================================
*/

$SQL[] = "CREATE TABLE `". DB_PRE ."in_email_log` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
`email` VARCHAR( 255 ) NOT NULL,
`date` INT( 10 ) NOT NULL
) ENGINE = MYISAM DEFAULT CHARSET=utf8;";

$SQL[] = "ALTER TABLE `". DB_PRE ."departments` ADD `auto_assign` INT NOT NULL;";

$SQL[] = "INSERT INTO `". DB_PRE ."settings` VALUES (NULL, 'email_flood', 'Incoming Flood Prevention', 'If enabled, Trellis Desk will attempt to prevent incoming email floods and infinite loops due to auto-responders on piping and POP3.', 8, 'enabled_disabled', '1', '', '1', 1, 7, 1);";
$SQL[] = "INSERT INTO `". DB_PRE ."settings` VALUES (NULL, 'email_subject_regex', 'Subject Regular Expression', 'This is the regular expression used when detecting the ticket ID number for email ticket replies.', 8, 'input', '/Ticket ID #&#40;[0-9]+&#41;/i', '', '/Ticket ID #&#40;[0-9]+&#41;/i', 1, 8, 1);";
$SQL[] = "INSERT INTO `". DB_PRE ."settings` VALUES (NULL, 'email_use_rline', 'Enable Reply Above Line', 'If enabled, Trellis Desk will search for the reply line and only include the content above in ticket replies.', 8, 'yes_no', '1', '', '1', 1, 9, 1);";
$SQL[] = "INSERT INTO `". DB_PRE ."settings` VALUES (NULL, 'email_reply_line', 'Reply Above Line', 'This is line Trellis Desk searches for in email ticket replies as the marker for the end of the reply message.', 8, 'input', '==== REPLY ABOVE THIS LINE ====', '', '==== REPLY ABOVE THIS LINE ====', 1, 10, 1);";

$SQL[] = "INSERT INTO `". DB_PRE ."upg_history` VALUES (NULL, '". $this->u_ver_id ."', '". $this->u_ver_human ."', '". time() ."', '". $this->ifthd->member['name'] ."', '". $this->ukey ."');";

?>