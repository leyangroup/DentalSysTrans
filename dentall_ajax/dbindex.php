<?php
	include_once "../include/db.php";

	$conn=MariaDBConnect();
    set_time_limit (0); 
	$conn->exec("ALTER TABLE `registration` ADD INDEX(`assistno`)");
	$conn->exec("ALTER TABLE `treat_record` ADD INDEX(`drno`)");
	$conn->exec("ALTER TABLE `prescription` ADD INDEX(`ic_sign`)");


	
	echo "<h1>索引處理完成</h1>";

?>