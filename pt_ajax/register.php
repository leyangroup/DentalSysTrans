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
	var_dump($drArr);


	//清除患者基本資料表
	$conn->exec("truncate table registration");

	//患者基本資料
	$sql = "SELECT * FROM register.dat";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$keyword=addslashes($value['keyword']).addslashes($value['timesno']);

		$rdate=explode(".", $value['r_date']);
		$receipt=($value['receipts']=='')?0:$value['receipts'];
		$dt=($rdate[0]+1911)."-".$rdate[1]."-".$rdate[2];
		$seqno=substr('000'.$value['serno'],-3);
		if ($drArr[$value['doctor']]==null){
			$drsn=0;
		}else{
			$drsn=$drArr[$value['doctor']];
		}

		//stdate用來存keyword,hospno用來存times
		$sql="insert into registration(ddate,seqno,stdate,drno1,drno2,treat_pay,rx_type) 
				values('$dt','$seqno','$keyword',$drsn,$drsn,$receipt,'2')";
		echo "$sql<br>";
		$conn->exec($sql);
	}

	$sql="update registration r, customer c ,trcusmap m
			 set r.cussn=c.cussn,r.cusno=c.cusno 
		   where left(stdate,4)=m.keyword
		     and m.cusno=c.cusno";
	$conn->exec($sql);
	echo "<h1>掛號資料 轉換完成</h1>";

?>