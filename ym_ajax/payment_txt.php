<?php
	require_once '../include/db.php';
    $conn=MariaDBConnect();

	echo "1.付款 資料!!<br>";
	//$patfile = fopen("C:\ym\data_txt\patient.txt", "r") or die("Unable to open file!");
	// 输出单行直到 end-of-file
	$row = 1;
	//$handle = fopen("C:\ym\ocsv\oepayment.txt","r");


	$conn->exec("drop table if exists oe");
	$sql="CREATE TABLE `oe` (
			  `id` int(10) NOT NULL,
			  `patno` varchar(12) DEFAULT NULL,
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

	$conn->exec("drop table if exists payamt");
	$sql="CREATE TABLE `payment` (
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
	$conn->exec("ALTER TABLE `payment` ADD PRIMARY KEY (`id`)");
	$conn->exec("ALTER TABLE `payment` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT");

	$conn->exec($sql);
	$patfile = fopen("C:\ym\ocsv\oepayment.txt", "r") or die("Unable to open file!");
	// 输出单行直到 end-of-file
	$row=0;
	while(!feof($patfile)) {
		$str=fgets($patfile);
		$data=explode(",", $str);

	    $row++;
	    if ($row>=2){
			$num = count($data);
		    echo "欄位數：$num<br>";
		    $p=0;
		    $patno=str_replace('"','',$data[0]);
		    $dr=str_replace('"','',$data[1]);
		    $DT=str_replace('"','',str_replace("/", "-",  $data[2]));
		    $Tname=str_replace('"','',trim($data[3]));
		    $total=str_replace('"','',$data[4]);
		    $paid=str_replace('"','',$data[5]);
		    $shouldpay=str_replace('"','',$data[6]);
		    $mattotal=str_replace('"','',$data[7]);
		    $matpaid=str_replace('"','',$data[8]);
		    $matshouldpay=str_replace('"','',$data[9]);
		    echo "$patno-$dr-$Tname<br>";
		    $cycle=($num-10)/6;
		    //儲存 oe 
		    $sql="INSERT into oe(id,patno,dr,dt,tname,total,paid,shouldpay,mattotal,matpaid,matshouldpay)
		    	values($row,'$patno','$dr','$DT','$Tname',$total,$paid,$shouldpay,$mattotal,$matpaid,$matshouldpay)";
		    $ok=$conn->exec($sql);
		    if ($ok==0){
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
		    	// echo $l1.'-'.$l2.'-'.$l3.'-'.$l4.'-'.$l5.'-'.$l6.'。。';
		    	// echo $data[$l1];
		    	$paydt=str_replace('"','',str_replace("/", "-", $data[$l1]));
		    	$payway=str_replace('"','',$data[$l2]);
		    	$pay=str_replace('"','',$data[$l3]);
		    	$paymemo=str_replace('"','',$data[$l4]);
		    	$matpay=str_replace('"','',$data[$l5]);
		    	$matmemo=str_replace('"','',$data[$l6]);
		    	//儲存付款
		    	$sql="INSERT into payment(oeid,payDT,payway,pay,memo,matpay,matmemo)
		    			values($row,'$paydt','$payway',$pay,'$paymemo',$matpay,'$matmemo')";
		    	$ok=$conn->exec($sql);
		    	if ($ok==0){
			    	echo "自費-付款-儲存失敗 oe---$sql<br>";
			    }
		    }
		}
	}
	fclose($handle);
?>