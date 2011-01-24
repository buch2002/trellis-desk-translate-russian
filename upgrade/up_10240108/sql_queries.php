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
|    | Trellis Desk Upgrade 10240108 SQL Queries
#======================================================
*/

$SQL[] = "ALTER TABLE `". DB_PRE ."departments` ADD `ticket_own_reopen` TINYINT(1) NOT NULL;";

$SQL[] = "INSERT INTO `". DB_PRE ."upg_history` VALUES (NULL, '". $this->u_ver_id ."', '". $this->u_ver_human ."', '". time() ."', '". $this->ifthd->member['name'] ."', '". $this->ukey ."');";

?>