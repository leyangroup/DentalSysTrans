<?php
	include_once "../include/db.php";
    $conn=MariaDBConnect();
    set_time_limit (0); 
    
	//設定索引
	$conn->exec("truncate table registration");
	$conn->exec("truncate table treat_record");
	$conn->exec("truncate table prescription");
	$conn->exec("ALTER TABLE `registration` ADD INDEX(`stdate`)");
	$conn->exec("ALTER TABLE `customer` CHANGE `cusfax` `cusfax` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '傳真號碼';");
	$conn->exec("ALTER TABLE `customer` ADD INDEX(`familyno`)");
	echo "<h1>ok</h1>";
?>