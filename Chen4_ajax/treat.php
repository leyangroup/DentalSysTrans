<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();

    $path=$_GET['path'].'\dent01_10.mdb';

	//新增醫師
 	set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Access Driver (*.mdb)};DBQ=".$path);

	$conn->exec("truncate table treat_record");
	//原系統key  idno+date+ser  轉檔程式存在 icuploadd_m,ddate,icuploadd
	$sql = "SELECT * FROM THPNAME1";
	$result = $db->query($sql);
	$r=0;
	foreach ($result as $key=> $value) {
		$drno=$value['set'];    // set暫存在drno裡 要串處置內容
		$cusid=$value['IDNO'];  // 將暫存在icupload_m中
		$regDT=WestDT($value['DATE']);
		$ser=$value['ser'];     // 將暫存在icuploadd
		$fdi=trim($value['pos1'].$value['pos2'].$value['pos3']);
		$trcode=$value['no'];
		$punitfee=$value['price'];
		$addpercent=round($value['per']/100,2);
		$nums=$value['tot'];
		$pamt=round($punitfee*$addpercent*$nums,0);
		if (substr($value['ser'],3,2)=='AB'){
			$starticseq=substr($value['ser'],5,4);
		}
		$insertSQL="insert into treat_record(regsn,ddate,fdi,trcode,punitfee,nums,add_percent,start_icseq,icuploadd_m,icuploadd,drno,pamt)
					values(0,'$regDT','$fdi','$trcode',$punitfee,$nums,$addpercent,'$starticseq','$cusid','$ser',$drno,$pamt)";
		$ok=$conn->exec($insertSQL);
		if ($ok==0){
			echo "新增處置資料失敗 $insertSQL<br>";
		}
	}

	//匯入處置說明
	$conn->exec("drop table if exists test.tmemo");
	$conn->exec("CREATE TABLE test.tmemo (
				  `idno` varchar(10) NOT NULL,
				  `date` varchar(10) NOT NULL,
				  `ser` varchar(8) DEFAULT NULL,
				  `sets` int(5) NOT NULL DEFAULT 0,
				  `memo` text DEFAULT NULL
				) ENGINE=MyISAM DEFAULT CHARSET=utf8");
	$conn->exec("ALTER TABLE `tmemo` ADD INDEX( `idno`, `date`, `ser`, `sets`)");
	$sql = "SELECT * FROM thstate";
	$result = $db->query($sql);
	foreach ($result as $key=> $value) {
		$idno=$value['idno'];
		$date=WestDT($value['date']);
		$ser=$value['ser'];
		$sets=$value['set'];
		$memo=($value['name1']=='')?"":addslashes(mb_convert_encoding($value['name1'],"UTF-8","BIG5")).'\n';
		$memo.=($value['name2']=='')?"":addslashes(mb_convert_encoding($value['name2'],"UTF-8","BIG5")).'\n';
		$memo.=($value['name3']=='')?"":addslashes(mb_convert_encoding($value['name3'],"UTF-8","BIG5")).'\n';
		$memo.=($value['name4']=='')?"":addslashes(mb_convert_encoding($value['name4'],"UTF-8","BIG5")).'\n';
		$memo.=($value['name5']=='')?"":addslashes(mb_convert_encoding($value['name5'],"UTF-8","BIG5")).'\n';
		$memo.=($value['name6']=='')?"":addslashes(mb_convert_encoding($value['name6'],"UTF-8","BIG5")).'\n';
		$memo.=($value['name7']=='')?"":addslashes(mb_convert_encoding($value['name7'],"UTF-8","BIG5")).'\n';
		$memo.=($value['name8']=='')?"":addslashes(mb_convert_encoding($value['name8'],"UTF-8","BIG5")).'\n';
		$memo.=($value['name9']=='')?"":addslashes(mb_convert_encoding($value['name9'],"UTF-8","BIG5")).'\n';
		$memo.=($value['name10']=='')?"":addslashes(mb_convert_encoding($value['name10'],"UTF-8","BIG5")).'\n';
		$insertSQL="INSERT into test.tmemo values('$idno','$date','$ser','$sets','$memo')";
		$ok=$conn->exec($insertSQL);
		if ($ok==0){
			echo "新增處置說明失敗$insertSQL<br>";
		}
	}

	echo "掛號與處置串接<br>";
	$conn->exec("UPDATE registration r,treat_record t  
					set t.regsn=r.regsn,t.seqno=r.seqno, t.cussn=r.cussn
				  WHERE r.ddate=t.ddate
					and r.uploadno=t.icuploadd_m
					and r.icuploadd=t.icuploadD");

	echo "串接處置說明<br>";
	$conn->exec("UPDATE eprodb.treat_record t,test.tmemo m
					set t.treat_memo=m.memo
		          where t.icuploadd_m=m.idno
		            and t.ddate=m.date 
		            and t.icuploadd=m.ser
		            and t.drno=m.sets ");

	echo "處理AB療程卡號";
	$conn->exec("update registration r,treat_record t set start_icseq=ic_seqno where r.regsn=t.regsn and r.ic_type='AB'");

	$conn->exec("update registration r,treat_record t 
					set start_date=r.ddate
				  where r.ic_seqno=t.start_icseq
					and r.cussn=t.cussn
					and ic_type='02'");
	$conn->exec("update registration set card_id=ic_seqno,ic_seqno='' where ic_type='AB'");

	echo "<h1>新增處置-結束</h1>";
	
?>





