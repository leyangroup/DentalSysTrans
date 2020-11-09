<?php
	//本程式已經合進去reg.php

	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    $giaPath="c:\angel\giadata";
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=c:\angel");
	$dbgia=new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$giaPath);


	//從申報檔中轉入資料 診察碼，診察費， 健保身份，  
	$conn->exec("drop table if exists tmp_giareg");
	$conn->exec("create table tmp_giareg 
						(DT varchar(10),
						seq varchar(3),
						icdatetime varchar(13),
						trcode varchar(10) ,
						trpay int,
						tamt int,
						rx_type varchar(2),
						icseqno varchar(7),
						nhistatus varchar(3),
						amount int,
						giaamt int 
					)ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='小天使申報檔-轉檔產生的'");
	$conn->Exec("ALTER TABLE `tmp_giareg` ADD INDEX( `DT`,`seq`,`icseqno`)");
	$conn->Exec("ALTER TABLE `tmp_giareg` ADD INDEX(`icdatetime`)");

	//讀取申報月份的db 1070101之前就捉最後一次的申報， 1070101開始，就捉上傳的回傳值有資料的
	//1070101之前舊資料
	$sql="select Ddate1,substr(ddate1,2,4)mon,max(seq)seq from giatop.dbf where giaok and ddate1<'1070101' group by 1";
	$result=$db->query($sql);
	$monStr='';
	foreach ($result as $key => $value) {
		$giamon=$value['mon'].$value['seq'];
		if ($monStr==''){
			$monStr=$giamon;
		}else{
			$monStr.=",$giamon";
		}
	}
	//後來上傳會回傳資訊，改捉別的欄位
	$sql="select Ddate1,substr(ddate1,2,4)mon,seq from giatop.dbf where giaok and ddate1>='1070101' and cislocalid !=' '";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$giamon=$value['mon'].$value['seq'];
		if ($monStr==''){
			$monStr=$giamon;
		}else{
			$monStr.=",$giamon";
		}
	}

	$MonArray=explode(",",$monStr);
	foreach ($MonArray as $key => $value) {
		$sql = "SELECT * FROM ".$value.".dbf";
		echo "$sql<br>";
		$gia=$dbgia->query($sql);
		foreach ($gia as $key => $value) {
			$dt=(substr($value['ddate'],0,3)+1911)."-".substr($value['ddate'],3,2)."-".substr($value['ddate'],-2);
			$seq=$value['patseq'];
			//$patno=trim(mb_convert_encoding(trim($value['pat_no']),"UTF-8","BIG5"));
			$icdatetime=$value['tream_dt'];
			$trcode=trim($value['tcode']);
			$trpay=$value['tamt'];
			$pamt=$value['pamt'];
			$rx_type=$value['isdrug'];
			$icseqno=$value['cardyear'].$value['tream_no'];
			$nhistatus=$value['inspct'];
			$amount=$value['amt'];
			$giaamt=$value['aamt'];
			$insSQL="insert into tmp_giareg
					  values('$dt','$seq','$icdatetime','$trcode',$trpay,$pamt,'$rx_type','$icseqno','$nhistatus',$amount,$giaamt)";
			echo "$insSQL<br>";
			$conn->exec($insSQL);

		}
	}

	echo "<br><br>申報 資料轉換完成!!";

?>