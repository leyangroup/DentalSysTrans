<?php
	include_once "../include/db.php";

    $conn=MariaDBConnect();

    $path=$_GET['path'];

	//新增醫師
 	set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Access Driver (*.mdb)};DBQ=".$path);

	






