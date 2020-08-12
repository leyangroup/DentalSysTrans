<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);

	$conn->exec("truncate table treatment");
	
	//處置資料
	$sql = "SELECT * FROM medical.dat";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$type=trim(mb_convert_encoding($value['type'],"UTF-8","BIG5"));
		if ($type=='收費項目'){
			$no=trim($value['drug_no']);
			$name=trim(mb_convert_encoding($value['making'],"UTF-8","BIG5"));
			$price=$value['price_self'];
			$sql="insert into treatment (trcode,treatname,nhi_fee,nhicode) values ('$no','$name',$price,'')";
			echo "$sql<br>";
			$conn->exec($sql);
		}
	}
	echo "<h1>處置範本 轉換完成</h1>";

?>