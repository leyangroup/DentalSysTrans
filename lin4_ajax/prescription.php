<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);
	
	//處方箋
	$conn->exec("truncate table prescription");

	$sql = "SELECT * FROM wdrug.dbf";
	$result=$db->query($sql);
	//用uploadd, icuploadd來存資料，uploadd存cussn,icuploadd存第幾次來 這樣才能對應
	foreach ($result as $key => $value) {
		$csn=$value['sno'];
		$order=$value['order'];
		$drugno=trim($value['drugno']);
		$dprice=$value['dprice'];
		$dqty=$value['dqty'];
		$dfrq=$value['dfrq'];
		$df=$value['df'];
		$day=$value['day'];
		$totalprice=$value['totprice'];
		$totalq=$day*$dqty;
		$sql="insert into prescription(regsn,drugno,qty,totalq,dose,times,uprice,amount,day,uploadd,icuploadd)
				values(0,'$drugno',$dqty,$totalq,'01','$dfrq',$dprice,$totalprice,$day,$csn,'$order')";
		$iok=$conn->exec($sql);
		if ($iok==0){
			echo "新增處方失敗：".$sql."<br>";
		}
	}
	echo "<h1>處方 資料轉換完成</h1>";

?>