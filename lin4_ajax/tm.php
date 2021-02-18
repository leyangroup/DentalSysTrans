<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);
	
	$conn->exec("truncate table treatment");

	$conn->exec("DROP TABLE IF EXISTS wgnote");
	$conn->exec("CREATE TABLE wgnote (
				  gpcode varchar(10) NOT NULL,
				  note text NOT NULL
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='04-tm'");

	$conn->exec("DROP TABLE IF EXISTS wprice");
	$conn->exec("CREATE TABLE wprice (
				  No varchar(10) NOT NULL,
				  price int(11) NOT NULL DEFAULT 0,
				  spec varchar(10) DEFAULT NULL,
				  name varchar(100) DEFAULT NULL
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='04-tm-price'");

	$conn->exec("DROP TABLE IF EXISTS wthgrp");
	$conn->exec("CREATE TABLE wthgrp (
				  gpcode varchar(10) NOT NULL,
				  gpn1 varchar(10) DEFAULT NULL,
				  gpn2 varchar(10) DEFAULT NULL,
				  name varchar(30) DEFAULT NULL,
				  icd9 varchar(10) DEFAULT NULL
				) ENGINE=MyISAM DEFAULT CHARSET=utf8");


	$sql = "SELECT * FROM wgnote.dbf";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$gpcode=trim($value['gpcode']);
		$note=addslashes( trim(mb_convert_encoding($value['note'],"UTF-8","BIG5")));

		$sql="insert into wgnote(gpcode,note) values('$gpcode','$note')";
		$iok=$conn->exec($sql);
		if ($iok==0){
			echo "新增wgnote失敗：".$sql."<br>";
		}
	}

	$sql = "SELECT * FROM wprice.dbf";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$no=trim($value['no']);
		$price=$value['price'];
		$spec=trim(mb_convert_encoding($value['spec'],"UTF-8","BIG5"));
		$name=addslashes( trim(mb_convert_encoding($value['descp'],"UTF-8","BIG5")));

		$sql="insert into wprice(no,price,spec,name) values('$no',$price,'$spec','$name')";
		$iok=$conn->exec($sql);
		if ($iok==0){
			echo "新增wprice 失敗：".$sql."<br>";
		}
	}


	$sql = "SELECT * FROM wthgrp.dbf";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$gpcode=trim($value['gpcode']);
		$gpn1=trim(mb_convert_encoding($value['gpname1'],"UTF-8","BIG5"));
		$gpn2=trim(mb_convert_encoding($value['gpname2'],"UTF-8","BIG5"));
		$name=addslashes( trim(mb_convert_encoding($value['descp'],"UTF-8","BIG5")));
		$icd9=trim($value['upcode']);

		$sql="insert into wthgrp(gpcode,gpn1,gpn2,name,icd9) values('$gpcode','$gpn1','$gpn2','$name','$icd9')";
		$iok=$conn->exec($sql);
		if ($iok==0){
			echo "wthgrp 失敗：".$sql."<br>";
		}
	}

	// $conn->exec("insert into treatment(trcode,nhicode,treatname,nhi_fee,treatno,sickno,memo)
	// 			select aa.no,aa.no,aa.name,price,spec,icd9,note
	// 			  from (SELECT a.no,b.gpcode,b.gpn1,a.name,price,spec,icd9 
	// 					  FROM wprice a 
	// 					  left join wthgrp b on a.no=b.gpn1
	// 			       )aa left join wgnote cc 
	// 			             on aa.gpcode=cc.gpcode
	// 			             where cc.note!=''");

	$conn->exec("insert into treatment(trcode,nhicode,treatname,nhi_fee,treatno,sickno)
				 SELECT a.no,a.no,a.name,price,spec,icd9 
				   FROM wprice a 
				   left join wthgrp b on a.no=b.gpn1");


	$conn->exec("update treat_record t,treatment m  set t.treatname=m.treatname where t.trcode=m.trcode");

	$conn->exec("update treatment set category='A3' where nhicode between '8A' and '8P' ");
	$conn->exec("update treatment set category='A3' where nhicode in ('81','87','88','89') ");
	$conn->exec("update treatment set category='B7' where nhicode like 'E10%' ");
	$conn->exec("update treatment set category='19' where category is null ");


	echo "<h1>處置 資料轉換完成</h1>";

?>