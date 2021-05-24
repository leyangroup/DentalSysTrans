<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    $path=$_GET['path'].'\dent01_10.mdb';

	//新增藥品
 	set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Access Driver (*.mdb)};DBQ=".$path);

	//頻率
	$sql = "SELECT * FROM thfrqt";
	$Freq = $db->query($sql);
	foreach ($Freq as $key => $vf) {
		$no=$vf['no'];
		$freqA[$no]=$vf['frqt'];
	}

	//部位
	$sql="SELECT * FROM THPATH";
	$Part_result=$db->query($sql);
	foreach ($Part_result as $key => $Vp) {
		$no=$Vp['NO'];
		$partA[$no]=$Vp['PATH'];
	}

	//drug
	$sql="SELECT * FROM THINVFL";
	$drug_result=$db->query($sql);
	foreach ($drug_result as $key => $vd) {
		$no=$vd['NO'];
		$drugA[$no]=$vd['NO2'];
	}


	//處方箋
	$sql = "SELECT * FROM THMED";
	$result = $db->query($sql);
	$conn->exec("truncate table prescription");
	foreach ($result as $key => $value) {
		$ddate=WestDT($value['DATE']); 
		$idno=$value['IDNO'];  // 將暫存在icupload_m中
		$ser=$value['SER'];    // 將暫存在icuploadd
		$drugno=$value['NO1'];
		$nhicode=$drugA[$drugno];
		$qty=$value['QTY'];
		$totalQ=$value['TOT'];
		$part=$partA[$value['USE']];
		if (strlen($value['FQT'])==3){
			$times=$value['FQT'];
		}else{
			$times=$freqA[$value['FQT']];
		}
		$uprice=$value['PRICE'];
		$amount=round($value['PRICE']*$value['TOT']);
		$day=$value['DAY'];

		$sql="insert into prescription(
					regsn,ddate,drugno,nhidrugno,qty,totalQ,dose,part,times,Uprice,amount,day,icuploadd,icuploadd_m) 
				values(0,'$ddate','$drugno','$nhicode',$qty,$totalQ,'01','$part','$times',$uprice,$amount,$day,'$ser','$idno')";
		$ok=$conn->exec($sql);
		if ($ok==0){
			echo "新增處方資料失敗 $sql<br>";
		}
	}

	echo "關連至registration<br>";
	$conn->exec("UPDATE registration r,prescription t  
					set t.regsn=r.regsn,t.seqno=r.seqno
				  WHERE r.ddate=t.ddate
					and r.uploadno=t.icuploadd_m
					and r.icuploadd=t.icuploadd");
	echo "<h1>新增處方資料完成</h1>";
?>





