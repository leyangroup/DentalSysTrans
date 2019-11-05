<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);
	$dr2=[];
	$dr1=[];
	$sql="select a.sfsn,a.sfno,a.sfid,a.sfemail,b.*
			from staff a,(select sfsn as sn,sfno as no,sfid as id from staff b where sfid=sfemail and sfid!='') b
			where a.sfid=id";
	$result=$conn->query($sql);
	foreach ($result as $key => $value) {
		$dr1[$value['sfno']]=$value['sn'];
	}
			
	$sql="select a.sfsn,a.sfno,a.sfid,a.sfemail,b.*
			from staff a,(select sfsn as sn,sfno as no,sfid as id from staff b where sfid=sfemail and sfid!='') b
			where a.sfemail=id";
	
	$result=$conn->query($sql);
	foreach ($result as $key => $value) {
		$dr2[$value['sfno']]=$value['sn'];
	}

	var_dump($dr1);
	echo "<br>";

    var_dump($dr2);
	echo "<br>";
	
	
	//取得健保身份
	$sql="select * from totabk.dbf";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$nhiSt[$value['inspct']]=$value['inspctno'];
	}

	//清除掛號表
	$conn->exec("truncate table registration");

	//掛號資料
	$sql = "SELECT * FROM pathistk.dbf";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$DT=WestDT($value['ddate']);
		$seqno=$value['seqno'];
		$patno=trim($value['pat_no']);
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
		$memo=trim(mb_convert_encoding($value['path_memo'],"UTF-8","BIG5")).'  '.trim(mb_convert_encoding($value['zhu_yan'],"UTF-8","BIG5"));
		$memo=str_replace("\\", "＼", $memo);
		$memo=str_replace("'", "\'", $memo);
		$sql="insert into registration(ddate,seqno,cusno,reg_time,end_time,drno1,drno2,sickn,sickn2,sickn3,category,ic_type,ic_datetime,ic_seqno,
					nhi_status,nhi_partpay,rx_type,isnp,reg_pay,nhi_tamt,nhi_damt,memo,rx_day,section,icuploadd)
			  values('$DT','$seqno','$patno','$regtime','$endtime',$drno1,$drno2,'$sickn','$sickn2','$sickn3','$category','$ic_type','$icdt','$ic_seqno',
			  		'$nhi_status',$nhi_partpay,'$rx_type',$isnp,$regpay,$tamt,$damt,'$memo',$rxday,'$section','1911-01-01')";
		//echo $sql." <br>";
		echo "$DT.$seqno";
		$conn->exec($sql);
	}

	echo "<br><br>填入病編流水號";
	$sql="update registration r,customer c 
		     set r.cussn=c.cussn
		   where r.cusno=c.cusno";
	$conn->exec($sql);
   
	$sql="update registration set amount=nhi_tamt,giaamt=nhi_tamt where ic_type in ('AB','AC')";
	$conn->exec($sql);

	$sql="update registration set trcode='00130C' where ic_type='02' and rx_type in ('0','2') ";
	$conn->exec($sql);

	$sql="update registration set trcode='00129C' where ic_type='02' and rx_type ='1' ";
	$conn->exec($sql);

	$sql="update registration set trpay=320,amount=320+nhi_tamt,giaamt=320+nhi_tamt-nhi_partpay 
	       where trcode in ('00130C','00129C') and ddate >='2017-01-01' ";
	$conn->exec($sql);

	$sql="update registration set trpay=313,amount=313+nhi_tamt,giaamt=313+nhi_tamt-nhi_partpa  
		   where trcode in ('00130C','00129C') and ddate between '2015-01-01' and '2016-12-31' ";
	$conn->exec($sql);

	$sql="update registration set trpay=285,amount=285+nhi_tamt,giaamt=285+nhi_tamt-nhi_partpa  
		   where trcode in ('00130C','00129C') and ddate between '2014-02-01' and '2014-12-31' ";
	$conn->exec($sql);

	$sql="update registration set nhi_status='009',nhi_partpay=0,amount=nhi_tamt+nhi_damt,giaamt=nhi_tamt+nhi_damt 
		   where ic_type='AB' and nhi_status!='009'";
	$conn->exec($sql);

	$sql="update registration set nhi_status='009',nhi_partpay=0,amount=nhi_tamt+nhi_damt,giaamt=nhi_tamt+nhi_damt 
		   where ic_type='AC' and nhi_status!='009' and category='A3' ";
	$conn->exec($sql);

	$sql="update registration 
			set nhi_partpay=50,giaamt=trpay+nhi_tamt+nhi_damt-50
			where ic_type !=''
			and nhi_status='H10' 
			and nhi_partpay=0";
	$conn->exec($sql);
			   
	$sql="update registration set nhi_partpay=0,amount=nhi_tamt+nhi_damt,giaamt=nhi_tamt+nhi_damt 
		   where ic_type='02' and nhi_status!='H10' ";
	$conn->exec($sql);

	$sql="update registration set nhi_partpay=0,amount=nhi_tamt+nhi_damt,giaamt=nhi_tamt+nhi_damt 
		   where ic_type!='' and nhi_status='009' ";
	$conn->exec($sql);

	$sql="update registration set nhi_partpay=50,amount=trpay+nhi_tamt+nhi_damt,giaamt=trpay+nhi_tamt+nhi_damt-50-drug_partpay
		   where ic_type='02' and nhi_status='H10' ";
	$conn->exec($sql);

	echo "<br><br>掛號 資料轉換完成!!";

?>