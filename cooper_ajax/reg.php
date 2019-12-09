<?php
	include_once "../include/db.php";
    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "2048M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);

	$sql="select discsn,discid from disc_list ";
	$RS=$conn->query($sql);
	foreach ($RS as $key => $col) {
		$discArr[$col['discid']]=$col['discsn'];
	}

	$sql="select sfsn,sfno from staff ";
	$RS=$conn->query($sql);
	foreach ($RS as $key => $col) {
		$drArr[$col['sfno']]=$col['sfsn'];
	}

	//掛號
	$conn->exec("truncate table registration");
	// $sql = "SELECT o.*,c.就醫類別 FROM operate o left join iccard c on  o.日期時間=c.日期時間 and o.病歷編號=c.病歷編號";
	$sql="select * from operate";
	$result=$db->query($sql);
	$DT='';
	$seq=0;
	$i=0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$i++;
		foreach ($row as $k2=> $v2) {
			switch (mb_convert_encoding($k2,"UTF-8","BIG5")) {
				case '病歷編號':
					$cusno=$v2;
					break;
				case '就診次數':
					$cnt=$v2;
					break;
				case '就診日期':
					$year=substr($v2,0,3);
					$y=substr($v2,0,3)+1911;
					$dt=$y.'-'.substr($v2,3,2).'-'.substr($v2,-2);
					if ($DT!=$v2){
						$seq=1;
					}else{
						$seq++;
					}
					break;
				case '療程序號':
					$is_oweic='0';
					if (trim($v2)==''){
						$icseq=$year;
						$ic_type='';
						$casehistory='2';
					}else{
						if (substr($v2,-4)=='  00'){
							$ic_type='';
							$is_oweic='1';
						}else{
							$icseq=trim($v2);
							$ic_type='02';
							if (substr($icseq,3,2)=='IC'){
								$ic_type='AC';
							}
							$casehistory='4';
						}
						
					}
					break;
				case '轉診院所碼':
					$thosp=$v2;
					break;
				case '是否轉出':
					if (!$v2){
						$isout=0;
					}else{
						$isout=1;
					}
					break;
				case '調劑方式':
					$rxtype=$v2;
					break;
				case '處方天數':
					$rxday=$v2;
					if ($v2=='') $rxday=0;
					break;
				case '醫師代號':
					$drsn=$drArr[$v2];
					if ($drsn==null || $drsn==''){
						$drsn=0;
					}
					break;
				case '健保診察碼':
					if (trim($v2)==''){
						$trcode=NULL;
						if (strlen($icseq)==3){
							$ic_type='';
						}else{
							if (substr($icseq,3,2)=='IC'){
								$ic_type='AC';
								$nhistatus='009';
							}else{
								$ic_type='AB';   //診察碼=空白，但有卡號，就是AB
								$nhistatus='009';
							}
						}
					}else{
						$trcode=$v2;
						$ic_type='02';
						$nhistatus='H10';
					}
					break;
				case '診察費':
					$trpay=$v2;
					break;
				case '藥事服務費':
					$drugsv=$v2;
					break;
				case '用藥明細費':
					$damt=$v2;
					break;
				case '診療明細費':
					$tamt=$v2;
					break;
				case '健保金額':
					$amount=$v2;
					break;
				case '申報金額':
					$giaamt=$v2;
					break;
				case '申報金額':
					$giaamt=$v2;
					break;
				case '自付金額':
					$partpay=$v2;
					break;
				case '掛號費':
					$regpay=$v2;
					break;
				case '掛號時間':
					if ($v2<="1230"){
						$section='1';
					}else if($v2>='1800'){
						$section='3';
					}else{
						$section='2';
					}
					$regtime=substr($v2,0,2).":".substr($v2,-2);
					break;
				case '掛號編號':
					$seqno=substr('000'.$v2,-3);
					break;
				case '備註':
					$memo=trim(mb_convert_encoding($v2,"UTF-8","BIG5"));
					break;
				case '藥品自付':
					$drugpt=$v2;
					break;
				case '優免身份':
					$disc=0;
					if (trim($v2)!=''){
						if ($discArr[$v2]==null || $discArr[$v2]==''){
							$disc=0;
						}else{
							$disc=$discArr[$v2];
						}
					}
					break;
				case '掛號優免':
					$discpay=$v2;
					break;
				case '院方初診':
					$isnp=($v2)?1:0;
					break;
				case '日期時間':
					$icdt=$v2;
					break;
				case '結束時間':
					if (trim($v2)!=''){
						$endtime=substr($v2,0,2).":".substr($v2,-2);
					}else{
						$endtime='';
					}
					break;
				case '卡片號碼':
					$cardid=trim($v2);
					break;
				case '保留c3':
					$category=substr($v2,16,2);
					break;
			}
		}
		switch ($rxtype) {
			case '1':  //cooper 1 是未開藥
				$rxtype='2';
				break;
			case '2':  //cooper 2 是有開藥
				if ($trcode=='00129C'){
					$rxtype='1';
				}else{
					$rxtype='0';
				}
				break;
			default:
				$rxtype='2';
				break;
		}
		$sql="insert into registration(ddate,seqno,stdate,cusno,drno1,drno2,reg_time,discid,section,isnp,
			trcode,nhi_status,category,rx_day,rx_type,is_out,hosp_from,ic_seqno,ic_type,
			ic_datetime,reg_pay,nhi_partpay,disc_pay,nhi_tamt,nhi_damt,drugsv,trpay,
			amount,giaamt,drug_partpay,memo,roomsn,end_time,card_ID,case_history,is_oweic,icuploadd)
			values('$dt','$seqno','$cnt','$cusno',$drsn,$drsn,'$regtime',$disc,'$section',$isnp,'$trcode',
					'$nhistatus','$category',$rxday,'$rxtype',$isout,'$thosp','$icseq','$ic_type',
					'$icdt',$regpay,$partpay,$discpay,$tamt,$damt,$drugsv,$trpay,$amount,$giaamt,$drugpt,'$memo',1,'$endtime','$cardid','$casehistory','$is_oweic','$dt')";
		echo $dt.'.'.$cusno.'、';
		// if ($dt>='2008-01-01' && $dt<='2008-01-31'){
		// 	echo $sql."<br>";
		// }
		$conn->exec($sql);
	}

	// 填入registration.cussn
	echo "填入患者sn";
	$sql="update registration r,customer c set r.cussn=c.cussn where r.cusno=c.cusno";
	$conn->exec($sql);

	echo "<h1>掛號轉換完成</h1>";


?>