<?php
	include_once "../include/db.php";
    $conn=MariaDBConnect();

    $path=$_GET['path'];

    $sql="select sfsn,sfno from staff ";
	$RS=$conn->query($sql);
	foreach ($RS as $key => $col) {
		$drArr[$col['sfno']]=$col['sfsn'];
	}


	$dbReg=dbase_open($path.'/CO03L.dbf',2);
	if (dbase_pack($dbReg)){
		echo "pack ok ";
	}else{
		echo "pack error  ";
	}

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
			$drsn=($drsn=='')?0:$drsn;
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
			$abstartdt='';
			if (strlen(trim($row['LISRS']))==3){
				if ($row['LLDDT']!='' && $row['DATE']==$row['LLDDT']){
					$ic_type='02';
				}else if (strlen(trim($row['LLDDT']))==7 && $row['DATE']!=$row['LLDDT']){
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
			echo $i.'.'.$ddate.'('.$cusno.')。 '; 	
			$conn->exec($sql);
		}	
	}

	$sql="update registration set section='1' where reg_time<='12:30'";
	$conn->exec($sql);
	$sql="update registration set section='2' where reg_time between '12:31' and '17:59'";
	$conn->exec($sql);
	$sql="update registration set section='3' where reg_time>='18:00'";
	$conn->exec($sql);

	$sql="update registration r,customer c  set r.cussn=c.cussn where r.cusno=c.cusno";
	$conn->exec($sql);

	echo "<br>";
	echo "掛號轉換完畢!!!";
?>