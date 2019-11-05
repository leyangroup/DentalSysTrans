<?php 
    include_once "include/db.php";
    $conn=MariaDBConnect();

	//新增醫師
	$conn->exec('truncate table staff');
	$sql="insert into staff(sfno,sfname,sfid,position) values
			('101','楊清忠','H101046893','D'),
			('102','楊孟典','H122198169','D'),
			('103','林子玉','A225860121','D')";
	$conn->exec($sql);

	$sql="select sfsn,sfno from staff ";
	$RS=$conn->query($sql);
	foreach ($RS as $key => $col) {
		$drArr[$col['sfno']]=$col['sfsn'];
	}
	$dbCus = dbase_open('C:/VISD/CO01M.dbf',0);
	if ($dbCus){
		$conn->exec('truncate table customer');

		$record_numbers = dbase_numrecords($dbCus);
		for ($i = 1; $i <= $record_numbers; $i++) {
			$row = dbase_get_record_with_names($dbCus, $i);
			$name=trim(mb_convert_encoding($row['MNAME'], "UTF-8", "BIG5"));
			$name=str_replace('　', '', $name);
			$cno=$row['KCSTMR'];  //病編
		  	$csex=$row['MSEX'];   //姓別
		  	if (strlen(trim($row['MBIRTHDT']))==0){
				$birthDT='';
				
			}else{
				$yy=substr($row['MBIRTHDT'],0,3)+1911;
		  		$dt=$yy.'-'.substr($row['MBIRTHDT'],3,2).'-'.substr($row['MBIRTHDT'],-2);
				$birthDT=$dt;  //民國年生日
			}
		  	
  			$tel=trim(mb_convert_encoding($row['MTELH'], "UTF-8", "BIG5"));  //電話
			$id=$row['MPERSONID'];  //身份證字號

			if (strlen(trim($row['MLCASEDATE']))!=0){
				$yy=substr($row['MLCASEDATE'],0,3)+1911;
				$lastDT=$yy.'-'.substr($row['MLCASEDATE'],3,2).'-'.substr($row['MLCASEDATE'],-2);  //最後一次就診日
			}else{
				$lastDT='';
			}
			
			if (strlen(trim($row['MBEGDT']))!=''){
				$yy=substr($row['MBEGDT'],0,3)+1911;
				$firstDT=$yy.'-'.substr($row['MBEGDT'],3,2).'-'.substr($row['MBEGDT'],-2);   //初診日
			}else{
				$firstDT='';
			}
			$dr=$row['MID'].$row['MIDS'];
			$drsn=$drArr[$dr];
			$drsn=($drsn=='')?1:$drsn;
			$mobile=trim(mb_convert_encoding($row['MREC'], "UTF-8", "BIG5"));  //手機
			$zip=trim(mb_convert_encoding($row['MRM5'], "UTF-8", "BIG5"));     //zip
			$addr=trim(mb_convert_encoding($row['MADDR'], "UTF-8", "BIG5"));  //地址
			$sex=substr($id,2,1)=="1"?1:0;

			$sqlC="insert into checkexist(no,name)value('$cno','$name')";
			$conn->exec($sqlC);

			$sql="insert into customer (cusno,cusname,cusbirthday,custel,cusid,firstdate,lastdate,cusmob,cusaddr,cuszip,cussex,maindrno,lastdrno)
					values('$cno','$name','$birthDT','$tel','$id','$firstDT','$lastDT','$mobile','$addr','$zip',$sex,$drsn,$drsn)";
			$conn->exec($sql);
			echo $row['KCSTMR']."--".$name."----".$sql."<br>";
		}
	}

	$dbReg=dbase_open('C:/visd/CO03L.dbf',0);
	if ($dbReg){
		$conn->exec('truncate table registration');
		$record_numbers = dbase_numrecords($dbReg);
		$dt='';
		$j=0;
		for ($i = 1; $i <= $record_numbers; $i++) {
			$row = dbase_get_record_with_names($dbReg, $i);
			$cusno=$row['KCSTMR'];
			$yy=substr($row['DATE'],0,3)+1911;
			$ddate=$yy.'-'.substr($row['DATE'],3,2).'-'.substr($row['DATE'],-2);
			if ($dt=='' || $dt!=$row['DATE']){
				$j=1;
				$dt=$row['DATE'];
			}else{
				$j++;
			}
			$seqno=substr('000'.$j,-3);
			$time=$row['TIME'];
			$dr=$row['LID'].$row['LIDS'];
			$drsn=$drArr[$dr];
			$trpay=$row['A1'];
			$tamt=$row['A9'];
			$giaamt=$row['TOT'];
			$treatno=str_replace(',', '', $row['LITEM']);
			$cate=$row['LCS'];
			$rx_type=$row['LDRU'];
			$nhi_status=$row['LHIID'];
			$partpay=abs($row['A98']);
			$amount=$trpay+$tamt;
			$giaamt=$amount-$partpay;
			$trcode='';
			switch ($trpay) {
				case 600:
					$trcode='01271C';
					break;
				case 230:
					if ($rx_type=='1'){
						$trcode='00121C';
					}else{
						$trcode='00122C';
					}
					break;
				case 260:
				case 280:
				case 313:
				case 320:
					if ($rx_type=='1'){
						$trcode='00129C';
					}else{
						$trcode='00130C';
					}
					break;
			}
			
			$ic_type='';
			if (strlen(trim($row['LISRS']))==3){
				if ($row['LLDDT']!='' && $row['DATE']==$row['LLDDT']){
					$ic_type='02';
				}else if ($row['LLDDT']!='' && $row['DATE']!=$row['LLDDT']){
					$ic_type='AB';
					$yy=substr($row['LLDDT'],0,3)+1911;
					$abstartdt=$yy.'-'.substr($row['LLDDT'],3,2).'-'.substr($row['LLDDT'],-2);
				}
			}
			if (strlen(trim($row['EDATE'])==5)){
				$ic_seqno=substr($row['EDATE'],0,3).'IC'.substr($row['EDATE'],-2);
				$ic_type='AC';
			}else{
				$ic_seqno=$row['EDATE'];
			}
			$reg_time=substr($row['TIME'],0,2).':'.substr($row['TIME'],2,2);
			$end_time=substr($row['LTIME'],0,2).':'.substr($row['LTIME'],2,2);
			$sick=$row['LABNO'].','.$row['LACD'];

			$sql="insert into registration(ddate,cusno,stdate,drno1,drno2,category,rx_type,nhi_status,
			trpay,trcode,nhi_tamt,nhi_partpay,amount,giaamt,ic_type,ic_seqno,reg_time,end_time,treatno,ILLNAME,seqno,uploadno)
			values('$ddate','$cusno','$time',$drsn,$drsn,'$cate','$rx_type','$nhi_status',$trpay,'$trcode',$tamt,
			$partpay,$amount,$giaamt,'$ic_type','$ic_seqno','$reg_time','$end_time','$treatno','$sick','$seqno','$abstartdt')";
			echo $i.'.'.$sql.'<br>'; 	
			$conn->exec($sql);
		}	
	}

	$sql="update registration set section='1' where reg_time<='12:30'";
	$conn->exec($sql);
	$sql="update registration set section='2' where reg_time between '12:31' and '17:59'";
	$conn->exec($sql);
	$sql="update registration set section='3' where reg_time>='18:00'";
	$conn->exec($sql);

	$sql="update registration set nhi_status='009' where nhi_status='H10' and ic_type='AB' ";

	$sql="update registration r,customer c set r.cussn=c.cussn where r.cusno=c.cusno";
	$conn->exec($sql);

	$dbTreat=dbase_open('C:/visd/CO02P.dbf',0);
	if ($dbTreat){
		$conn->exec('truncate table treat_record');
		$conn->exec('truncate table prescription');
		$record_numbers = dbase_numrecords($dbTreat);
		for ($i = 1; $i <= $record_numbers; $i++) {
			$row = dbase_get_record_with_names($dbTreat, $i);
			$cusno=$row['KCSTMR'];  //存在uploadD中
			$yy=substr($row['PDATE'],0,3)+1911;
			$ddate=$yy.'-'.substr($row['PDATE'],3,2).'-'.substr($row['PDATE'],-2);
			$ptime=$row['PTIME'];
			if ($row['PTP']!='2'){
				//處置
				$fdi=trim($row['PLCA']);
				$trcode=trim($row['KDRUG']);
				$nums=$row['PTQTY'];
				$unitprice=$row['PPR'];
				$addp=$row['PMU'];
				$pamt=$nums*$unitprice*$addp;
				$sql="insert into treat_record (regsn,ddate,icuploadd,uploadd,fdi,trcode,nums,punitfee,pamt,add_percent)
				values(0,'$ddate','$ptime','$cusno','$fdi','$trcode',$nums,$unitprice,$pamt,$addp)";
				echo "處置".$sql."<br>";
				$conn->exec($sql);
			}else{
				//藥品
				$drugno=trim($row['KDRUG']);
				$dose=$row['PQTY'];  //每次量
				$fq=$row['PTFQ']==''?1:$row['PTFQ'];   //頻率量
				$times=trim($row['PFQ']);  //頻率
				if ($times=='') $times=trim($row['PLMU']);
				$part=trim($row['PPS']);    //部位
				$total=$row['PTQTY'];  //總量
				$unitprice=$row['PPR']; //單價
				$day=$row['PDAY'];
				$qty=round($total/$fq/$day);
				$amount=$total*$unitprice;
				$sql="insert into prescription(regsn,ddate,icuploadd,uploadd,drugno,qty,totalQ,dose,part,times,uprice,amount,day) 
						values(0,'$ddate','$ptime','$cusno','$drugno',$qty,$total,$dose,'$part','$times',$unitprice,$amount,$day)";
				echo '藥'.$sql."<br>";
				$conn->exec($sql);
			}
		}
	}

	$sql="update registration r,treat_record t 
			set t.seqno=r.seqno,t.cussn=r.cussn,t.regsn=r.regsn
			where r.ddate=t.ddate 
			and r.cusno=t.uploadd
			and r.stdate=t.icuploadd
			and r.cussn is not null";
	$conn->exec($sql);

	$sql="update registration r,prescription t 
			set t.seqno=r.seqno,t.regsn=r.regsn
			where r.ddate=t.ddate 
			and r.cusno=t.uploadd
			and r.stdate=t.icuploadd";
	$conn->exec($sql);

	$dbDrug=dbase_open('C:/visd/CO09D.dbf',0);
	if ($dbDrug){
		$conn->exec('truncate table drug');
		$record_numbers = dbase_numrecords($dbDrug);
		for ($i = 1; $i <= $record_numbers; $i++) {
			$row = dbase_get_record_with_names($dbDrug, $i);
			if (trim($row['DTYPE'])=='02'){
				if (strlen(trim($row['DNO']))==10){
					$drugno=$row['KDRUG'];
					$nhicode=$row['DNO'];
					$drugname=$row['DDESC'];
					$times=$row['DFREQ'];
					$sql="insert into drug (drugno,name,nhicode,dose,part,times)values
						('$drugno','$drugname','$nhicode','01','PO','$times')";
					echo $sql."<br>";
					$conn->exec($sql);
				}
			}
		}
	}

	// 填入藥品健保碼
	$sql="update prescription p,drug d set p.nhidrugno=d.nhicode where p.drugno=d.drugno";
	$conn->exec($sql);

	$sql="update prescription p,drug_dose d set p.dose=d.doseno where p.dose=d.qty";
	$conn->exec($sql);

	//計算藥品數量問題
	$sql="update prescription set qty=4,totalQ=4*day,amount=4*day*uprice where totalq!=4*day and times='QID'";
	$conn->exec($sql);

	//處理處置的中文, 與傷病
	$sql="update treat_record t,treatment m set t.treatname=m.treatname,t.sickno=m.sickno,t.icd10=m.icd10cm where t.trcode=m.trcode";
	$conn->exec($sql);

	//處理01271的問題 將01271的做刪除記錄
	$sql="update registration r, treat_record t 
			 set r.trcode=concat(t.trcode,'C')
			where r.regsn=t.regsn
			  and t.trcode like '0127%'";
	$conn->exec($sql);

	$sql="update treat_record set deldate='1911-01-01' where trcode like '0127%'";
	$conn->exec($sql);

	//處理療程開始日與卡號
	$sql="update registration r,treat_record t 
			 set t.start_date=r.uploadno,t.start_icseq=substr(r.ic_seqno,-4)
		   where r.regsn=t.regsn
		     and r.ic_type='AB'";
	$conn->exec($sql);

	$sql="update registration set ic_seqno=substr(ic_seqno,0,3) where ic_type='AB'";
	$conn->exec($sql);

	$conn->exec('truncate table drugcom');
	$conn->exec('truncate table drugcomdetails');



	// 產生二年的指標值


	// 最後就診日

	// 最後就診醫師

	echo "<h2>結束</h2>";
?>