<?php
	include_once "../include/db.php";
    $conn=MariaDBConnect();

    $path=$_GET['path'];

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

	
	$dbCus = dbase_open($path.'/CO01M.dbf',2);
	if (dbase_pack($dbCus)){
		echo "pack ok";
	}else{
		echo "pack error";
	}

	if ($dbCus){
		$conn->exec('truncate table customer');

		$record_numbers = dbase_numrecords($dbCus);
		for ($i = 1; $i <= $record_numbers; $i++) {
			$row = dbase_get_record_with_names($dbCus, $i);
			$name=trim(mb_convert_encoding($row['MNAME'], "UTF-8", "BIG5"));
			$name=str_replace('　', '', $name);
			$cno=$row['KCSTMR'];  //病編
		  	if (strlen(trim($row['MBIRTHDT']))==0){
				$birthDT='';
			}else{
				$yy=substr($row['MBIRTHDT'],0,3)+1911;
		  		$dt=$yy.'-'.substr($row['MBIRTHDT'],3,2).'-'.substr($row['MBIRTHDT'],-2);
				$birthDT=$dt;  //民國年生日
			}
		  	
  			$tel=trim(mb_convert_encoding($row['MTELH'], "UTF-8", "BIG5"));  //電話
			$id=$row['MPERSONID'];  //身份證字號
			switch (substr($id,1,1)) {
				case '1':
				case 'A':
				case 'C':
				case 'Y':
					$sex=1;
					break;
				
				default:
					$sex=0;
					break;
			}
			

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
			
			$mobile=(strlen($mobile)==0)?$tel:$mobile;

			$sqlC="insert into checkexist(no,name)value('$cno','$name')";
			$conn->exec($sqlC);

			$sql="insert into customer (cusno,cusname,cusbirthday,custel,cusid,firstdate,lastdate,cusmob,cusaddr,cuszip,cussex,maindrno,lastdrno)
					values('$cno','$name','$birthDT','$tel','$id','$firstDT','$lastDT','$mobile','$addr','$zip',$sex,$drsn,$drsn)";
			$conn->exec($sql);
			echo $row['KCSTMR']."-".$name."。 ";
		}
		echo "<br>患者資料轉換完畢!!";
	}

?>