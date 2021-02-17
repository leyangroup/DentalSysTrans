<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";
	require_once '../include/fdiHelpers.php';

	function unicode_to_utf8($unicode_str) {
	    $utf8_str = '';
	    $code = intval(hexdec($unicode_str));
	    //这里注意转换出来的code一定得是整形，这样才会正确的按位操作
	    $ord_1 = decbin(0xe0 | ($code >> 12));
	    $ord_2 = decbin(0x80 | (($code >> 6) & 0x3f));
	    $ord_3 = decbin(0x80 | ($code & 0x3f));
	    $utf8_str = chr(bindec($ord_1)) . chr(bindec($ord_2)) . chr(bindec($ord_3));
	    return $utf8_str;
	}
   $conn=MariaDBConnect();
	$conn->exec("DROP TABLE IF EXISTS `zhis4`");
	$conn->exec("CREATE TABLE `zhis4` (
				  `ukey` varchar(10) DEFAULT NULL,
				  `upatno` varchar(10) DEFAULT NULL,
				  `date` varchar(10) DEFAULT NULL,
				  `fdi` varchar(12) DEFAULT NULL,
				  `trcode` varchar(10) DEFAULT NULL,
				  `trname` varchar(100) DEFAULT NULL,
				  `price` int(9) NOT NULL DEFAULT 0,
				  `addpercent` decimal(3,1) NOT NULL DEFAULT 0.0,
				  `nums` int(9) NOT NULL DEFAULT 0,
				  `pamt` int(9) NOT NULL DEFAULT 0,
				  `memo` text DEFAULT NULL,
				  `regsn` int(10) NOT NULL DEFAULT 0,
				  `westDT` varchar(10) DEFAULT NULL,
				  `icseqno` varchar(7) DEFAULT NULL
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='zhis_type4'");
	
	// $conn->exec("ALTER TABLE `zhis4`
	// 				  ADD KEY `regsn` (`regsn`),
	// 				  ADD KEY `upatno` (`upatno`,`date`,`icseqno`)");

   $path=$_GET['path'];

	//新增患者
 	set_time_limit (0); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$path);

	//取得醫師
	$dr=$conn->query("select sfsn,sfname from staff where position='D' ");
	$drArray=[];
	foreach ($dr as $key => $drV) {
		$drname=trim($drV['sfname']);
		$drArray[$drname]=$drV['sfsn'];
	}

	//處置
	echo "<br> 處置 資料轉換 ......";

	//$sql = "SELECT * FROM z_history.dbf where type in ('1','4') and ukey == 'TCUJ1913' ";
	$sql = "SELECT * FROM z_history.dbf where type in ('1','4')";
	$result=$db->query($sql);
	
	foreach ($result as $key => $value) {
		$ukey=$value['ukey'];  //掛號主鍵
		$upatno=trim($value['upatno']);   //患者主鍵
		$rocDT=trim($value['date']);
		$westDT=WestDT($rocDT);
		$type=trim($value['type']);
		//$dstr1=trim(addslashes(mb_convert_encoding($value['dstr1'],"UTF-8","BIG5")));
		$code=trim(mb_convert_encoding($value['code'],"UTF-8","BIG5"));
		$dstr2=trim(addslashes(mb_convert_encoding($value['dstr2'],"UTF-8","BIG5")));
		$patch=$value['patch'];
		$fdi=fdiConvert(utf8_encode(trim($value['dstr1'])));

// echo '<br>';
// 		echo '1. ' .$value['dstr1'].'<br>';
// 		echo '2. ' .$fdi.'<br>';

// 		exit;


		// if ($code!=''){
		//    // echo "牙位：".$value['dstr1']."處置：".$code."utf8=".unicode_to_utf8($dstr1)."fdiConvert=".fdiConvert($value['dstr1'])."dstr1=".$dstr1."<br>";

		// }
		switch ($type) {
			case '1':
				$icseqno=substr($rocDT,0,3).$code;
				break;
			case '4':
				if ($code==''){
					if ($dstr2=='00127C初診-pano'){
						$trcode='01271';
					}
					$memo=$dstr2;
					$sql="insert into zhis4(ukey,upatno,date,fdi,trcode,memo,westDT,icseqno)
						value('$ukey','$upatno','$rocDT','$fdi','$trcode','$memo','$westDT','$icseqno')";
				}else{
					$trcode=$code;
					$treatname=$dstr2;
					$dtl=explode(';',$patch);
					$addPercent=round($dtl[0]/100,2);
					$nums=$dtl[1];
					$price=$dtl[2];
					$pamt=(is_numeric($dtl[3]))?$dtl[3]:0;
					$sql="insert into zhis4(ukey,upatno,date,fdi,trcode,trname,price,addPercent,nums,pamt,westDT,icseqno)
						value('$ukey','$upatno','$rocDT','$fdi','$trcode','$treatname',$price,$addPercent,$nums,$pamt,'$westDT','$icseqno')";
				}
				$ok=$conn->exec($sql);
				if ($ok==0){
					echo "4-失敗：$sql <br>";	
				}	
				// echo $sql."<br>";
				break;
		}
	}

	echo "<br> 牙位 資料轉換 ......";
	$sql = "SELECT * FROM z_ICTREAT.dbf ";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$ztransukey=$value['ukey'];
		$a9=$value['a9'];
		$code=$value['code'];
		$posi=$value['posi'];
		$qnt=$value['qnt'];
		$drugday=(is_numeric($value['drugday']))?$value['drugday']:0;
		$total=$value['total'];
		$sql="insert into ztreat(ztransukey,a9,code,posi,qnt,drugday,total)
				values
				('$ztransukey','$a9','$code','$posi','$qnt',$drugday,$total)";
		$ok=$conn->exec($sql);
		if ($ok==0){
			echo "處置新增失敗：$sql <br>";	
		}			
	}

	echo "<br> 處置與牙位 資料轉換完畢!!";

?>