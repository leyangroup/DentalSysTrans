<?php
	include_once "../include/db.php";
    $conn=MariaDBConnect();
    set_time_limit (0); 
    
	//設定索引
	$conn->exec("ALTER TABLE customer ADD INDEX(`cusno`)");
	$conn->exec("ALTER TABLE `treat_record` ADD INDEX( `ddate`, `seqno`)");
	$conn->exec("ALTER TABLE `registration` ADD INDEX( `ddate`, `seqno`)");

	$conn->exec("ALTER TABLE `pasystemdi` ADD UNIQUE( `sn`)");
	$conn->exec("ALTER TABLE `pasystemdi` CHANGE `sn` `sn` INT(8) NOT NULL AUTO_INCREMENT COMMENT '  流水號  '");
	

?>