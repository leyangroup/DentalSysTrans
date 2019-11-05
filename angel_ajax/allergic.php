<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);
	
	//過敏藥物與系統疾病
	$conn->exec("truncate table allergic");
	$conn->exec("truncate table systemdisease");

	$sql = "SELECT * FROM senlist.dbf";
	$result=$db->query($sql);
	$r=0;
	foreach ($result as $key => $value) {
		$cno=trim($value['cno']);
		$memo=trim(mb_convert_encoding($value['name'],"UTF-8","BIG5"));
		$memo=str_replace("\\", "＼", $memo);
		$memo=str_replace("'", "\'", $memo);

		$sql="insert into allergic (ano,aname) values('$cno','$memo') ";
		echo $sql;
		$conn->exec($sql);

		$sql="insert into systemdisease(ano,aname) values('$cno','$memo') ";
		echo $sql;
		$conn->exec($sql);
	}

	$tmpArray=[];
	$sql="select * from allergic order by asn";
	$result=$conn->query($sql);
	foreach ($result as $key => $value) {
		$tmpArray[$value['ano']]=$value['asn'];
	}
	$conn->exec("drop table if exists tmpalg");
	$conn->exec("create table tmpalg(pathno varchar(10) ,sickno varchar(30) ) ");
	$conn->exec("ALTER TABLE `tmpalg` ADD INDEX(`pathno`)");
	$conn->exec("ALTER TABLE `tmpalg` ADD INDEX(`sickno`)");

	$sql="select * from pathsen.dbf";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$pathno=$value['pathno'];
		$sickno=$value['sickno'];
		$sql="insert into tmpalg(pathno,sickno) values('$pathno','$sickno')";
		$conn->exec($sql);
	}


	$sql="select c.cussn,a.asn
			from tmpalg t,customer c,allergic a
		   where t.pathno=c.cusno
		     and t.sickno=a.ano";
	$result=$conn->query($sql);
	foreach ($result as $key => $value) {
		$cussn=$value['cussn'];
		$asn=$value['asn'];

		$conn->exec("insert into paallergic (cussn,asn) value($cussn,$asn)");
		$conn->exec("insert into pasystemdi (cussn,sdsn) value($cussn,$asn)");

	}
	$conn->exec("drop table if exists tmpalg");
	echo "<br>";
	echo "過敏藥物與系統疾病 資料轉換完成";

?>