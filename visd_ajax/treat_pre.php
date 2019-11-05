<?php
	include_once "../include/db.php";
    $conn=MariaDBConnect();

    $path=$_GET['path'];

	$dbTreat=dbase_open($path.'/CO02P.dbf',2);
	if (dbase_pack($dbTreat)){
		echo "pack ok";
	}else{
		echo "pack error  ";
	}	
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
				$side=$row['PPS'];
				$sql="insert into treat_record (regsn,ddate,icuploadd,uploadd,fdi,trcode,nums,punitfee,pamt,add_percent,side)
				values(0,'$ddate','$ptime','$cusno','$fdi','$trcode',$nums,$unitprice,$pamt,$addp,'$side')";
				//echo "處置.".$ddate."(".$cusno.")。 ";
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
				//echo '處方.'.$ddate."(".$cusno.")。 ";
				$conn->exec($sql);
			}
			echo $i.", ";
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

	echo "<br>醫令轉換完成!!";
?>