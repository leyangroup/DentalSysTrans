<?php
	include_once "../include/db.php";
    $conn=MariaDBConnect();
    $conn->exec("ALTER TABLE `customer` ADD INDEX( `cusid`) ");
	$conn->exec("ALTER TABLE `registration` ADD INDEX( uploadno,ddate,icuploadd)");
	$conn->exec("ALTER TABLE treat_record ADD INDEX(icuploadd_m,ddate,icuploadd,drno)");
	$conn->exec("ALTER TABLE prescription ADD INDEX(icuploadd_m,ddate,icuploadd)");
	$conn->exec("ALTER TABLE tmemo ADD INDEX( `idno`, `date`, `ser`, `sets`)");
	$conn->exec("ALTER TABLE `tmemo` ADD INDEX( `idno`, `date`, `ser`, `sets`)");
	$conn->exec("ALTER TABLE `registration` ADD INDEX( `cussn`, `ic_seqno`)");
	$conn->exec("ALTER TABLE `treat_record` ADD INDEX( `cussn`, `start_icseq`)");
	echo "<h1>索引建立 完成</h1>";

?>