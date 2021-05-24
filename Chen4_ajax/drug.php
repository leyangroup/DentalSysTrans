<?php
	include_once "../include/db.php";

    $conn=MariaDBConnect();
    $path=$_GET['path'].'\dent01_10.mdb';

	//新增藥品
 	set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Access Driver (*.mdb)};DBQ=".$path);
	$sql = "SELECT * FROM THINVFL";
	$result = $db->query($sql);
	$conn->exec("truncate table leconfig.zhi_drug");
	foreach ($result as $key => $value) {
		$drugno=$value['NO'];
		$nhicode=$value['NO2'];
		$drugname=$value['NAME'];
		$price=($value['PRICE']=='')?0:$value['PRICE'];
		$dose='01';
		$part='PO';
		$times='TID';
		$sql="insert into leconfig.zhi_drug(name,drugno,nhicode,nhifee,dose,part,times,is_lockQ,amt,created_at,created_by) 
				values('$drugname','$drugno','$nhicode',$price,'$dose','$part','times',0,0,now(),0)";
		$ok=$conn->exec($sql);
		if ($ok==0){
			echo "新增藥品資料失敗 $sql<br>";
		}
	}
	echo "<h1>新增藥品資料完成</h1>";
?>





