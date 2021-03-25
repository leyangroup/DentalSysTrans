<?php
	require_once '../include/db.php';
    $conn=MariaDBConnect();
    $conn->exec("drop table if exists z_appointment");

	$conn->exec("CREATE TABLE `z_appointment` (
				  `DT` varchar(10) DEFAULT NULL,
				  `PatNo` varchar(15) DEFAULT NULL,
				  `tel` varchar(20) DEFAULT NULL,
				  `mobile` varchar(20) DEFAULT NULL,
				  `regtime` varchar(20) DEFAULT NULL,
				  `schtime` varchar(20) DEFAULT NULL,
				  `schend` varchar(20) DEFAULT NULL,
				  `dr` varchar(15) DEFAULT NULL,
				  `memo` text DEFAULT NULL,
				  `schlen` int(5) NOT NULL DEFAULT 0
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='appointment';
			");

	$row = 1;
	$handle = fopen("C:\ym\ocsv\appointment-2.csv","r");
	while ($data = fgetcsv($handle)) {

	    $num = count($data);
	    $row++;
	    $DT=str_replace('/', '-', $data[0]);
	    $patno=trim(mb_convert_encoding($data[1],"UTF-8","BIG5"));
	    $tel=addslashes( mb_convert_encoding($data[2],"UTF-8","BIG5"));
	    $mobile=addslashes( mb_convert_encoding($data[3],"UTF-8","BIG5"));
	    $regtime=mb_convert_encoding($data[4],"UTF-8","BIG5");
	    $startTime=mb_convert_encoding($data[5],"UTF-8","BIG5");
	    $endTime=mb_convert_encoding($data[6],"UTF-8","BIG5");
	    $doctor=mb_convert_encoding($data[7],"UTF-8","BIG5");
	    $note=addslashes(mb_convert_encoding($data[8],"UTF-8","BIG5"));
	    //預約開始時間
	    $number = substr_count($startTime,'上午');
	    if ($number>=1){
	    	//上午
	    	$Time1=str_replace('上午', '', $startTime);
	    }else{
	    	//下午
	    	$Time1=str_replace('下午', '', $startTime);
	    	$trTime=$Time1." PM";
	    	$Time1 = date("H:i", strtotime($trTime));
	    }
	    //預約結束時間
	    $number = substr_count($endTime,'上午');
	    if ($number>=1){
	    	//上午
	    	$Time2=str_replace('上午', '', $endTime);

	    }else{
	    	//下午
	    	$Time2=str_replace('下午', '', $endTime);
	    	$trTime=$Time2." PM";
	    	$Time2 = date("H:i", strtotime($trTime));
	    }
		$schlen=(strtotime($Time2) - strtotime($Time1) ) / 60;

	    $sql="insert into z_appointment 
	    		values('$DT','$patno','$tel','$mobile','','$Time1','$Time2','$doctor','$note',$schlen) ";
	    $ok=$conn->exec($sql);
	    if ($ok==0){
	    	echo "預約轉人失敗 $sql <br>";
	    }
	}
	echo "<h1>預約資料傳送完成</h1>";
	fclose($handle);

?>