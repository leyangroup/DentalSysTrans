<?php
	include_once "../include/db.php";
    $conn=MariaDBConnect();
    set_time_limit (0); 
    
	//設定索引
	$conn->exec("ALTER TABLE customer ADD INDEX(`cusno`)");
	$conn->exec("ALTER TABLE `registration` ADD INDEX( `ddate`, `seqno`)");
	$conn->exec("ALTER TABLE `registration` ADD INDEX(`ic_datetime`)");
	$conn->exec("ALTER TABLE `treat_record` ADD INDEX( `ddate`, `seqno`)");
	$conn->exec("ALTER TABLE `pasystemdi` ADD UNIQUE( `sn`)");
	$conn->exec("ALTER TABLE `pasystemdi` CHANGE `sn` `sn` INT(8) NOT NULL AUTO_INCREMENT COMMENT '  流水號  '");
	$conn->exec("ALTER TABLE `registration` CHANGE `SICKN` `SICKN` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '保留欄-傷病一代號'");
	$conn->exec("ALTER TABLE `registration` CHANGE `SICKN2` `SICKN2` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '保留欄-傷病二代號', CHANGE `SICKN3` `SICKN3` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '保留欄-傷病三代號'");
?>