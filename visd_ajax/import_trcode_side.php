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
		$conn->exec("drop table if exists trcodeside");
		$sql="create table trcodeside(ddate varchar(10),ptime varchar(7),cusno varchar(8),fdi varchar(12),trcode varchar(10),side varchar(6))";
		$conn->exec($sql);

		$record_numbers = dbase_numrecords($dbTreat);
		for ($i = 1; $i <= $record_numbers; $i++) {
			$row = dbase_get_record_with_names($dbTreat, $i);
			$cusno=$row['KCSTMR'];  //存在uploadD中
			$yy=substr($row['PDATE'],0,3)+1911;
			$ddate=$yy.'-'.substr($row['PDATE'],3,2).'-'.substr($row['PDATE'],-2);
			$ptime=$row['PTIME'];
			if ($row['PTP']=='9' && substr($row['KDRUG'],0,2)=='89'){
				//處置
				$fdi=trim($row['PLCA']);
				$trcode=trim($row['KDRUG']);
				$side=$row['PPS'];
				$sql="insert into trcodeside (ddate,ptime,cusno,fdi,trcode,side)
				values('$ddate','$ptime','$cusno','$fdi','$trcode','$side')";
				//echo "牙面.$sql <br>";
				$conn->exec($sql);
			}
			echo $i.", ";
		}
	}
	
	echo "<br>牙面 轉換完成!!";
?>