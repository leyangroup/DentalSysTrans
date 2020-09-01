<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);

	//醫師資料
	$RS=$conn->query("select no,id from leconfig.zhi_staff ");
	$drArr=[];
	foreach ($RS as $key => $value) {
		$drArr[$value['no']]=$value['id'];
	}

	//清除患者基本資料表
	//$conn->exec("truncate table registration");

	//患者基本資料
	$sql = "SELECT * FROM register.dat";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$keyword=addslashes($value['keyword']).addslashes($value['timesno']);
		$keys=addslashes($value['keyword']);
		$rdate=explode(".", $value['r_date']);
		$receipt=($value['receipts']=='')?0:$value['receipts'];
		$dt=($rdate[0]+1911)."-".$rdate[1]."-".$rdate[2];
		$seqno=substr('000'.$value['serno'],-3);
		if ($drArr[$value['doctor']]==null){
			$drsn=0;
		}else{
			$drsn=$drArr[$value['doctor']];
		}

		//stdate用來存keyword+timesno 就是患者的主鍵+該次掛號序==>pk， 要和處置的 icuploadd 關聯 ,trcode用來存患者的主鍵 方便對應患者
		$sql="insert into registration(ddate,seqno,stdate,trcode,drno1,drno2,treat_pay,rx_type) 
				values('$dt','$seqno','$keyword','$keys',$drsn,$drsn,$receipt,'2')";
		echo "$keyword<br>";
		$conn->exec($sql);
	}
	echo "資料關聯……";

	$sql="update registration r,trcusmap m
			 set r.cusno=m.cusno
		   where binary r.trcode=m.keyword
		     and r.cussn is null";

	$conn->exec($sql);

	$sql="update registration r,customer c 
			 set r.cussn=c.cussn
		   where r.cusno=c.cusno";
	$conn->exec($sql);

	echo "<h1>掛號資料 轉換完成</h1>";

?>