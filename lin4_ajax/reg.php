<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);

	//設定索引

	//醫師
	$drArray=[];
	$rs=$conn->query("select id,no from leconfig.zhi_staff");
	foreach ($rs as $key => $value) {
		$drArray[$value['no']]=$value['id'];
	}
	var_dump($drArray);

	//建立資料表來儲存療程的資料
	$conn->exec("drop table if exists tmpab");
	$conn->exec("CREATE TABLE `tmpab` 
					(
					  `csn` int(7) NOT NULL DEFAULT 0,
					  `cno` varchar(10) DEFAULT NULL,
					  `stdate` varchar(5) DEFAULT NULL,
					  `parentorder` varchar(4) DEFAULT NULL,
					  `parentdate` varchar(10) DEFAULT NULL
					) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='林氏-ab資料';
				");
	$conn->exec("ALTER TABLE `tmpAB` ADD KEY `cns` (`cns`,`stdate`)");

	//掛號表
	$conn->exec("truncate table registration");

	$sql = "SELECT * FROM wthick.dbf";
	$result=$db->query($sql);
	$dt='';
	$cnt=0;
	foreach ($result as $key => $value) {
		if ($dt!=trim($value['date'])){
			$cnt=1;
			$dt=trim($value['date']);
		}else{
			$cnt++;
		}
		$seqno=substr('000'.$cnt,-3);
		$csn=$value['sno'];
		$cno=trim($value['sinno']);
		$DT=WestDT(trim($value['date']));
		$stdate=trim($value['order']);  //患者第幾次看診
		$drno=trim($value['docter']);
		$dr=$drArray[$drno];
		$issop=$value['sop'];
		$icseqno=substr($value['date'],0,3).trim(mb_convert_encoding(addslashes($value['cardse']),"UTF-8","BIG5"));
		$icdatetime=trim($value['dtime']);
		$nhistatus=trim($value['pd']);
		$category=trim($value['class']);
		$rxdays=($value['days']==' '?0:$value['days']);
		$rxtype=$value['pts'];
		$trpay=$value['f1'];
		$tamt=$value['ptotal'];
		$partpay=$value['pcash'];
		$amount=$value['f1']+$value['ptotal'];
		$giaamt=$value['insufee'];
		$regpay=$value['regfee'];
		$memo=trim(mb_convert_encoding(addslashes($value['remark']),"UTF-8","BIG5"));
		if ($value['class']=='A3' || $value['class']=='B7'){
			$type='AC';
		}else{
			if ($value['sorder']!=' '){
				$type='AB';
			}else{
				$type='02';
			}
		}
		$sorder=trim($value['sorder']);
		$sdate=$value['sdate'];

		$sql="insert into registration(ddate,uploadd,seqno,cussn,cusno,drno1,drno2,is_sop,ic_seqno,ic_datetime,nhi_status,category,rx_day,rx_type,trpay,nhi_tamt,amount,nhi_partpay,giaamt,reg_pay)
				values('$DT','$stdate','$seqno',$csn,'$cno',$dr,$dr,$issop,'$icseqno','$icdatetime','$nhistatus','$category',$rxdays,'$rxtype',$trpay,$tamt,$amount,$partpay,$giaamt,$regpay)";
		$iok=$conn->exec($sql);
		if ($iok==0){
			echo "新增掛號失敗：".$sql."<br>";
		}

		if ($sorder!=''){
			$sql="insert into tmpab values($csn,'$cno','$stdate','$sorder','$sdate')";
			$iok=$conn->exec($sql);
			if ($iok==0){
				echo "新增療程資料失敗：$sql"."<br>";
			}
		}

	}

	$conn->exec("update registration set ic_type='02',case_history='4' where category in ('11','19','15') and nhi_status in ('001','003','004','902','H10')");
	$conn->exec("update registration set ic_type='AB',case_history='4' where category in ('11','19') and nhi_status='009' ");
	$conn->exec("update registration set ic_type='AC',case_history='4' where category in ('A3','B7') ") ;

	$conn->exec("update examfee e,registration r  
					set r.trcode=code
					where fee=trpay
					and rx_type ='1'
					and kind=1
					and rx=1");

	$conn->exec("update examfee e,registration r  
					set trcode=code
					where fee=trpay
					and rx_type in ('0','2')
					and kind=1
					and rx=0");



	echo "<h1>掛號 資料轉換完成</h1>";

?>