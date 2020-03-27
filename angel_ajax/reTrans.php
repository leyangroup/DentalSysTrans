<?php
	include_once "../include/DTFC.php";
	include_once "../include/db.php";

    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);

	//設定索引
	
	// //新增醫師
	// $conn->exec("truncate table staff");

	// //sftitle 暫存申報醫師身份證
	// $sql = "SELECT * FROM user.dbf";
	// $result=$db->query($sql);
	// foreach ($result as $key => $value) {
	// 	$no=trim($value['ucode']);
	// 	$name=trim(mb_convert_encoding($value['username'],"UTF-8","BIG5"));
	// 	$id=$value['cid'];
	// 	if (trim($value['aid'])==' '){
	// 		$aid=$id;
	// 	}else{
	// 		$aid=$value['aid'];
	// 	}

	// 	$birth=$value['cbirthday'];
	// 	$BD='';
	// 	if (strlen(trim($birth))==7){
	// 		$yy=substr($birth,0,3)+1911;
	// 		echo 'yy='.$yy;
	// 		$BD=$yy.'-'.substr($birth,3,2).'-'.substr($birth,-2); 
	// 	}
	// 	$sex='';
	// 	switch (substr($id,1,1)) {
	// 		case '1':
	// 		case 'A':
	// 		case 'C':
	// 		case 'Y':
	// 			$sex='1';
	// 			break;
	// 		default:
	// 			$sex='0';
	// 			break;
	// 	}
	// 	$position=(substr($no,0,1)=='D')?'D':'A';
	// 	$endjob='';
	// 	if (strlen(trim($ldate))==7){
	// 		$yy=1911+substr($ldate,0,3);
	// 		$endjob=$yy.'-'.substr($ldate,3,2).'-'.substr($ldate,-2);
	// 	}
	// 	$sql="insert into staff(sfno,sfname,sfid,sfbirthday,sfsex,sfendjob,position,sfemail,drkind)values
	// 				('$no','$name','$id','$BD','$sex','$endjob','$position','$aid','0') ";
	// 	echo "新增醫師：".$sql."<br>";

	// 	$conn->exec($sql);
	// }

	// echo "<h1>醫師 重新 轉換完成</h1>";

	echo "<h1>轉 就醫日，掛序，醫師</h1>";

	$conn->exec("drop table if exists reg");
	$conn->exec("create table reg (DT varchar(10),sno varchar(10),drno varchar(5),drsn int,cusno varchar(10),cussn int )");


	//掛號資料
	$sql = "SELECT * FROM pathistk.dbf";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$DT=WestDT($value['ddate']);
		$sno=$value['seqno'];
		$patno=trim($value['pat_no']);
		$drno=$value['dr_no'];
		
		$sql="insert into reg(DT,sno,drno,drsn,cusno,cussn) values('$DT','$sno','$drno',0,'$patno',0)";
		echo "$DT.$sno <br>";
		$conn->exec($sql);
	}

	echo "<h1>結束</h1>";
?>