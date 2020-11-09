<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    $giaPath=$_GET['path']."\giadata";
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);
	$dbgia=new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$giaPath);

	$today=$_GET['dt'];
	$yy=substr($today,0,4);
	$dr2=[];
	$dr1=[];
	//主治 有輸入身份證且主治就是申報 沒有輸入身份證的就不會在裡面

	// $sql="select a.sfsn,a.sfno,a.sfid,a.sfemail,b.*
	// 		from staff a,(select sfsn as sn,sfno as no,sfid as id from staff b where sfid=sfemail and sfid!='') b
	// 		where a.sfid=id";
	$sql="select sfsn,sfno from staff order by 1";
	$result=$conn->query($sql);
	foreach ($result as $key => $value) {
		$dr1[$value['sfno']]=$value['sfsn'];
	}
			
	$sql="select a.sfsn,a.sfno,a.sfid,a.sfemail,b.*
			from staff a,(select sfsn as sn,sfno as no,sfid as id from staff b where sfid=sfemail and sfid!='') b
			where a.sfemail=id";
	
	$result=$conn->query($sql);
	foreach ($result as $key => $value) {
		$dr2[$value['sfno']]=$value['sn'];
	}
	
	//取得健保身份
	$sql="select * from totabk.dbf";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$nhiSt[$value['inspct']]=$value['inspctno'];
	}

	//清除掛號表
	//$conn->exec("truncate table registration");

	//掛號資料
	$sql = "SELECT * FROM pathistk.dbf";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$DT=WestDT($value['ddate']);
		$seqno=$value['seqno'];
		$patno=trim(mb_convert_encoding($value['pat_no'],"UTF-8","BIG5"));
		if (strpos($patno,"傲")!==false || strpos($patno,"爛" )!==false || strpos($patno,"兄")!==false){
			$regtime=$value['ctime'];
			$drno1=($dr1[trim($value['dr_no'])]=='')?0:$dr1[trim($value['dr_no'])];
			$drno2=($dr2[trim($value['dr_no2'])]=='')?0:$dr2[trim($value['dr_no2'])];
			$sickn=$value['sickn'];
			$sickn2=$value['sickn2'];
			$sickn3=$value['sickn3'];
			$nhi_status=$nhiSt[$value['inspct']];
			$isnp=$value['np'];
			$regpay=$value['bpay'];
			$nhi_partpay=$value['bbtop'];
			$rx_type=$value['isdrug'];
			$tamt=$value['lingamt'];
			$damt=$value['lindamt'];
			$category=$value['totcat'];
			$icdt=$value['tream_dt']; 
			$ic_seqno=$value['cardyear'].$value['tream_no'];
			$ic_type=$value['case_type'];
			$endtime=$value['end_time'];
			$rxday=$value['rxday'];
			if ($regtime<='12:00'){
				$section='1';
			}elseif ($regtime>'12:00' && $regtime<='17:59'){
				$section='2';
			}else{
				$section='3';
			}
			if (strlen($ic_seqno)==7){
				$case_history='4';
			}else{
				$case_history='3';
			}
			$isoweic='0';
			
			switch (trim($value['inseq'])) {
				case '??':
					$isoweic='1';
					$casehistory='3';
					break;
				case 'IC':
					$isoweic='0';
					$casehistory='4';
					break;
			}
			
			$memo=trim(mb_convert_encoding($value['path_memo'],"UTF-8","BIG5")).'  '.trim(mb_convert_encoding($value['zhu_yan'],"UTF-8","BIG5"));
			$memo=str_replace("\\", "＼", $memo);
			$memo=trim(str_replace("'", "\'", $memo));
			$sql="insert into registration(ddate,seqno,cusno,reg_time,end_time,drno1,drno2,sickn,sickn2,sickn3,category,ic_type,ic_datetime,ic_seqno,
						nhi_status,nhi_partpay,rx_type,isnp,reg_pay,nhi_tamt,nhi_damt,memo,rx_day,section,icuploadd,case_history,is_oweic)
				  values('$DT','$seqno','$patno','$regtime','$endtime',$drno1,$drno2,'$sickn','$sickn2','$sickn3','$category','$ic_type','$icdt','$ic_seqno',
				  		'$nhi_status',$nhi_partpay,'$rx_type',$isnp,$regpay,$tamt,$damt,'$memo',$rxday,'$section','1911-01-01','$casehistory','$isoweic')";
			
			echo $sql."; <br>";
			//echo "$DT.$seqno";

			//$conn->exec($sql);
		}
	}

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

	echo "<br><br>填入病編流水號";
	$sql="update registration r,customer c 
		     set r.cussn=c.cussn
		   where r.cusno=c.cusno";
	$conn->exec($sql);

	//異動診察碼，診察費
	$sql="update registration a,tmp_giareg b 
			 set a.trcode=b.trcode,a.trpay=b.trpay
		   where a.ic_datetime=b.icdatetime 
			 and ic_type='02'
			 and a.trcode!=b.trcode
			 and b.trcode !=''";
	$conn->exec($sql);

	$sql="update registration set amount=trpay+nhi_tamt,giaamt=trpay+nhi_tamt-nhi_partpay where ddate like '2015%' and ic_type='02'";
	$conn->exec($sql);

	// $sql="update registration set amount=nhi_tamt,giaamt=nhi_tamt where ic_type in ('AB','AC')";
	// $conn->exec($sql);

	// $sql="update registration set trcode='00130C' where ic_type='02' and rx_type in ('0','2') ";
	// $conn->exec($sql);

	// $sql="update registration set trcode='00129C' where ic_type='02' and rx_type ='1' ";
	// $conn->exec($sql);

	// $sql="update registration set trcode='00305C',trpay=355,amount=355+nhi_tamt,giaamt=355+nhi_tamt-nhi_partpay where ic_type='02' and rx_type in ('0','2') and ddate>='2020-04-01'  ";
	// $conn->exec($sql);

	// $sql="update registration set trcode='00306C',trpay=355,amount=355+nhi_tamt,giaamt=355+nhi_tamt-nhi_partpay where ic_type='02' and rx_type='1' and ddate>='2020-04-01' ";
	// $conn->exec($sql);

	// $sql="update registration set trpay=320,amount=320+nhi_tamt,giaamt=320+nhi_tamt-nhi_partpay 
	//        where trcode in ('00130C','00129C') and ddate between '2017-01-01' and '2020-03-31' ";
	// $conn->exec($sql);

	// $sql="update registration set trpay=313,amount=313+nhi_tamt,giaamt=313+nhi_tamt-nhi_partpa  
	// 	   where trcode in ('00130C','00129C') and ddate between '2015-01-01' and '2016-12-31' ";
	// $conn->exec($sql);

	// $sql="update registration set trpay=285,amount=285+nhi_tamt,giaamt=285+nhi_tamt-nhi_partpa  
	// 	   where trcode in ('00130C','00129C') and ddate between '2014-02-01' and '2014-12-31' ";
	// $conn->exec($sql);

	// $sql="update registration set nhi_status='009',nhi_partpay=0,amount=nhi_tamt+nhi_damt,giaamt=nhi_tamt+nhi_damt 
	// 	   where ic_type='AB' and nhi_status!='009'";
	// $conn->exec($sql);

	// $sql="update registration set nhi_status='009',nhi_partpay=0,amount=nhi_tamt+nhi_damt,giaamt=nhi_tamt+nhi_damt 
	// 	   where ic_type='AC' and nhi_status!='009' and category='A3' ";
	// $conn->exec($sql);

	// $sql="update registration 
	// 		set nhi_partpay=50,giaamt=trpay+nhi_tamt+nhi_damt-50
	// 		where ic_type !=''
	// 		and nhi_status='H10' 
	// 		and nhi_partpay=0";
	// $conn->exec($sql);
			   
	// $sql="update registration set nhi_partpay=0,amount=nhi_tamt+nhi_damt,giaamt=nhi_tamt+nhi_damt 
	// 	   where ic_type='02' and nhi_status!='H10' ";
	// $conn->exec($sql);

	// $sql="update registration set nhi_partpay=0,amount=nhi_tamt+nhi_damt,giaamt=nhi_tamt+nhi_damt 
	// 	   where ic_type!='' and nhi_status='009' ";
	// $conn->exec($sql);

	// $sql="update registration set nhi_partpay=50,amount=trpay+nhi_tamt+nhi_damt,giaamt=trpay+nhi_tamt+nhi_damt-50-drug_partpay
	// 	   where ic_type='02' and nhi_status='H10' ";
	// $conn->exec($sql);

	echo "<br>產生charge";
	
	//產生charge
	$conn->exec("truncate table charge");
	
	$sql="insert into charge (ddate,chargetime,cussn,regpay,partpay,`add`,discsn,discreg,discpart,minus,balance ,is_oweic)
		  SELECT ddate,reg_time,cussn,reg_pay,nhi_partpay,reg_pay+nhi_partpay,case when r.discid is null then 0 else r.discid end ,
		         case when d.reg_disc is null then 0 else d.reg_disc end,
		         case when d.partpay_disc is null then 0 else d.partpay_disc end,
		         disc_pay,reg_pay+nhi_partpay-disc_pay,is_oweic
		    FROM registration r left join disc_list d on r.discid=d.discsn
		   where ddate like '$yy%'
			 and seqno<>'000'
		   order by ddate,seqno";
		   echo $sql;
	$conn->exec($sql);

	$sql="update registration r,charge c
			 set r.chargesn=c.sn
		   where r.ddate=c.ddate
			 and r.reg_time=c.chargetime
			 and r.cussn=c.cussn
			 and r.chargesn=0
			 and r.ddate like '$yy%'";
			 echo $sql;
	$conn->exec($sql);

	echo "<br><br>掛號 資料轉換完成!!";

?>