<?php
	include_once "../include/db.php";
    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);

	//院所基本資料
	$sql = "SELECT * FROM com_name.dbf";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$cname=trim(mb_convert_encoding($value['coname'],"UTF-8","BIG5"));
		
		$tel=$value['tel'];
		$zip=$value['zip1'];

		$sql="update basicset set bsname='$cname',bstel='$tel',bsaddr='$addr',zip='$zip',owner='$owner',nhicode='$nhicode',bsfax='',volume='',opendate=''";
		echo $sql;

		$conn->exec($sql);
	}
	
	

	echo "資料轉換完成";

?>