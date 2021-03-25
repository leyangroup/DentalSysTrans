<?php
	require_once '../include/db.php';
    $conn=MariaDBConnect();

	echo "1.付款 資料!!<br>";
	
	$conn->exec("drop table if exists z_oe");
	$sql="CREATE TABLE `z_oe` (
			  `id` int(10) NOT NULL,
			  `patno` varchar(10) DEFAULT NULL,
			  `dr` varchar(15) DEFAULT NULL,
			  `DT` varchar(10) DEFAULT NULL,
			  `tname` varchar(50) DEFAULT NULL,
			  `total` int(10) NOT NULL DEFAULT 0,
			  `paid` int(10) NOT NULL DEFAULT 0,
			  `shouldpay` int(10) NOT NULL DEFAULT 0,
			  `mattotal` int(10) NOT NULL DEFAULT 0,
			  `matpaid` int(10) NOT NULL DEFAULT 0,
			  `matshouldpay` int(10) NOT NULL DEFAULT 0
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='oe'";
	$conn->exec($sql);
	$conn->exec("ALTER TABLE `oe` ADD PRIMARY KEY (`id`)");
	$conn->exec("ALTER TABLE `oe` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT ");

	$conn->exec("drop table if exists z_payment");
	$sql="CREATE TABLE `z_payment` (
			  `id` int(10) NOT NULL,
			  `oeid` int(10) NOT NULL DEFAULT 0,
			  `payDT` varchar(10) DEFAULT NULL,
			  `payway` varchar(10) DEFAULT NULL,
			  `pay` int(10) NOT NULL DEFAULT 0,
			  `memo` text DEFAULT NULL,
			  `matpay` int(10) NOT NULL DEFAULT 0,
			  `matmemo` text DEFAULT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='pay'";
	$conn->exec($sql);
	$conn->exec("ALTER TABLE `z_payment` ADD PRIMARY KEY (`id`)");
	$conn->exec("ALTER TABLE `z_payment` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT");

	$file = fopen("C:\ym\ocsv\oepayment-1.csv","r");
	$row=0;
	$l1=0;$l2=0;$l3=0;$l4=0;$l5=0;$l6=0;
	while ($data = fgetcsv($file)) {
	    $row++;
	    //if ($row>=2){
			$num = count($data);
		    
		    $p=0;
		    $patno=trim($data[0]);
		    $dr=mb_convert_encoding(addslashes($data[1]),"UTF-8","BIG5");
		    $ODT=explode('/', $data[2]);
		    $Oyy=$ODT[0];
		    $Omm=substr('0'.$ODT[1],-2);
		    $Odd=substr('0'.$ODT[2],-2);
		    $oeDT=$Oyy.'-'.$Omm.'-'.$Odd;
		    $Tname=mb_convert_encoding(addslashes($data[3]),"UTF-8","BIG5");
		    $total=mb_convert_encoding(addslashes($data[4]),"UTF-8","BIG5");
		    $paid=mb_convert_encoding(addslashes($data[5]),"UTF-8","BIG5");
		    $shouldpay=mb_convert_encoding(addslashes($data[6]),"UTF-8","BIG5");
		    $mattotal=mb_convert_encoding(addslashes($data[7]),"UTF-8","BIG5");
		    $matpaid=mb_convert_encoding(addslashes($data[8]),"UTF-8","BIG5");
		    $matshouldpay=mb_convert_encoding(addslashes($data[9]),"UTF-8","BIG5");
		    // echo "$patno-$dr-$Tname<br>";
		    $cycle=($num-10)/6;
		    //儲存 oe 
		    $sql="INSERT into z_oe(id,patno,dr,dt,tname,total,paid,shouldpay,mattotal,matpaid,matshouldpay)
		    	values($row,'$patno','$dr','$oeDT','$Tname',$total,$paid,$shouldpay,$mattotal,$matpaid,$matshouldpay)";
		    $ok=$conn->exec($sql);
		    if ($ok==0){
		    	echo "欄位數：$num<br>";
		    	echo "自費儲存失敗 oe---$sql<br>";
		    }
		    $start=9;
		    for($i=1;$i<=$cycle;$i++){
		    	$l1=$start+1;
		    	$l2=$start+2;
		    	$l3=$start+3;
		    	$l4=$start+4;
		    	$l5=$start+5;
		    	$l6=$start+6;
		    	$start=$l6;
		    	if ($data[$l1]!=''){
			    	// echo $l1.'-'.$l2.'-'.$l3.'-'.$l4.'-'.$l5.'-'.$l6.'。。';
			    	// echo $data[$l1];
			    	$DT=explode('/',  $data[$l1]);
			    	$yy=$DT[0];
			    	$mm=substr('0'.$DT[1],-2);
			    	$dd=substr('0'.$DT[2],-2);
			    	// echo "日期：$data[$l1] ";
			    	$paydt= $yy.'-'.$mm.'-'.$dd;
			    	$payway=mb_convert_encoding(addslashes($data[$l2]),"UTF-8","BIG5");
			    	$pay=mb_convert_encoding(addslashes($data[$l3]),"UTF-8","BIG5");
			    	$paymemo=mb_convert_encoding(addslashes($data[$l4]),"UTF-8","BIG5");
			    	$matpay=mb_convert_encoding(addslashes($data[$l5]),"UTF-8","BIG5");
			    	$matmemo=mb_convert_encoding(addslashes($data[$l6]),"UTF-8","BIG5");
		    		//儲存付款
			    	$sql="INSERT into z_payment(oeid,payDT,payway,pay,memo,matpay,matmemo)
			    			values($row,'$paydt','$payway',$pay,'$paymemo',$matpay,'$matmemo')";
			    	$ok=$conn->exec($sql);
			    	if ($ok==0){
			    		echo "欄位數：$num<br>";
				    	echo "自費-付款-儲存失敗 oe---$sql<br>";
				    }
		    	}
		    }
		//}
	}
	fclose($file);
	echo "<h1>付款資料轉換完畢!!!
	</h1>";
?>