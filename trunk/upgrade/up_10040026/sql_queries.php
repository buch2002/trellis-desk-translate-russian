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
|    | Trellis Desk Upgrade 10040026 SQL Queries
#======================================================
*/

$SQL[] = "INSERT INTO `". DB_PRE ."settings` VALUES (NULL, 'email_method', 'Email Method', 'Your outgoing emails will be sent using this method.', 8, 'dropdown', 'native', 'native=PHP mail()\r\nsmtp=SMTP', 'native', 1, 2, 1);";
$SQL[] = "INSERT INTO `". DB_PRE ."settings` VALUES (NULL, 'smtp_host', 'SMTP Host', 'SMTP Host for outgoing emails (only applies if the Email Method is set to SMTP).', 8, 'input', 'localhost', '', 'localhost', 1, 3, 1);";
$SQL[] = "INSERT INTO `". DB_PRE ."settings` VALUES (NULL, 'smtp_port', 'SMTP Port', 'The connection port for the above SMTP host (only applies if the Email Method is set to SMTP).  This is usually 25.', 8, 'input', '25', '', '25', 1, 4, 1);";
$SQL[] = "INSERT INTO `". DB_PRE ."settings` VALUES (NULL, 'smtp_user', 'SMTP Username', 'SMTP username if authentication is required for SMTP (only applies if the Email Method is set to SMTP).', 8, 'input', '', '', '', 1, 5, 1);";
$SQL[] = "INSERT INTO `". DB_PRE ."settings` VALUES (NULL, 'smtp_pass', 'SMTP Password', 'SMTP password if authentication is required for SMTP (only applies if the Email Method is set to SMTP).  This password will be stored in plaintext in your database.', 8, 'input', '', '', '', 1, 6, 1);";
$SQL[] = "INSERT INTO `". DB_PRE ."settings` VALUES (NULL, 'smtp_encryption', 'SMTP Encryption', 'SMTP encryption method if your SMTP host requires it (only applies if the Email Method is set to SMTP).', 8, 'dropdown', '0', '0=None\r\nssl=SSL\r\ntls=TLS', '0', 1, 7, 1);";

$SQL[] = "INSERT INTO `". DB_PRE ."settings_groups` VALUES (8, 'email', 'Email Configuration', 'Email settings and configuration such as mailing method, outgoing email, etc.', 1, 0);";

$SQL[] = "UPDATE `". DB_PRE ."settings` SET `cf_group` = '8', `cf_position` = '1' WHERE `cf_key` = 'out_email';";

$SQL[] = "INSERT INTO `". DB_PRE ."upg_history` VALUES (NULL, '". $this->u_ver_id ."', '". $this->u_ver_human ."', '". time() ."', '". $this->ifthd->member['name'] ."', '". $this->ukey ."');";

?>