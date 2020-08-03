<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);

	//清除處方箋
	$conn->exec("truncate table prescription");

	$sql = "SELECT * FROM pathdrug.dbf";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$DT=WestDT($value['ddate']);
		$seqno=$value['seqno'];
		$dno=trim($value['drugno']);
		$qty=trim($value['qty']);
		$totalq=$value['qtotal'];
		$dose=trim($value['tdrug1']);
		$part=trim($value['tdrug2']);
		$times=trim($value['tdrug3']);
		$uprice=$value['uprice'];
		$days=$value['rxday'];
		$amt=round($price*$totalq,1);
		$sql="insert into prescription(regsn,ddate,seqno,drugno,qty,totalq,dose,part,times,uprice,amount,day)
			  values(0,'$DT','$seqno','$dno',$qty,$totalq,'$dose','$part','$times',$uprice,$amt,$days)";
		echo $sql;
		echo "<br>";
		$conn->exec($sql);
	}
	echo "regsn填入處理<br>";
	$sql="update registration r,prescription t 
			 set t.regsn=r.regsn
		   where r.ddate=t.ddate
		     and r.seqno=t.seqno";
	$conn->exec($sql);

	$sql="update prescription p,drug_times t set p.qty=t.qty where p.times=t.timesno";
	$conn->exec($sql);

	$sql="update prescription set dose='01' where dose='' ";
	$conn->exec($sql);
	
	echo "<br><br>處方箋 資料轉換完成!!";

?>