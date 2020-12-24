<?php
	include_once "../include/db.php";
	use XBase\Table;

    $conn=MariaDBConnect();

    //$path=$_GET['path'];

	//新增醫師
	

	//新增患者
	$table = new Table('c:/zdent/ZPAT.dbf');

	while ($record = $table->nextRecord()) {
    	//echo $record->get('my_column');
    	//or
    	echo $record->patno;
    	echo $record->name;
	}

		echo "<br>患者資料轉換完畢!!";
	

?>