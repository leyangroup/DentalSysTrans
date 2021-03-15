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

	$conn->exec("insert into treatment(trcode,nhicode,treatname,nhi_fee,treatno,sickno,memo)
				 SELECT a.no,a.no,a.name,price,spec,icd9 ,'Tx.'
				   FROM wprice a 
				   left join wthgrp b on a.no=b.gpn1");

	$conn->exec("update treat_record t,treatment m  set t.treatname=m.treatname where t.trcode=m.trcode");

	$sql="UPDATE treatment t,nhicode n 
			set t.category=n.category,t.is_oper=n.is_oper,t.is_endo=n.is_endo,t.is_peri=n.is_peri,t.is_oral=n.is_oral,
			t.is_xray=n.is_xray,t.is_pedo=n.is_pedo,
			t.tr_od=n.tr_od,t.tr_endo=n.tr_endo,t.tr_peri=n.tr_peri,t.tr_os=n.tr_os,t.tr_ospath=n.tr_ospath,
			t.tr_pedo=n.tr_pedo,t.fee_unit=n.feeunit,t.nhi_fee=n.fee, t.icd10cm=n.icd10cm,t.icd10pcs=n.icd10pcs
			where t.nhicode=n.nhicode";
	$conn->exec($sql);

	echo "<h1>處置 資料轉換完成</h1>";

?>