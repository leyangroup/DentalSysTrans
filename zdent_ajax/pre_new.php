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

	$conn->exec("DROP TABLE IF EXISTS `zhis6`");
	$conn->exec("CREATE TABLE `zhis6` (
				  `ukey` varchar(10) DEFAULT NULL,
				  `upatno` varchar(10) DEFAULT NULL,
				  `date` varchar(10) DEFAULT NULL,
				  `code` varchar(10) DEFAULT NULL,
				  `dose` varchar(10) DEFAULT NULL,
				  `times` varchar(6) DEFAULT NULL,
				  `part` varchar(5) DEFAULT NULL,
				  `drugname` varchar(50) DEFAULT NULL,
				  `totalq` int(9) NOT NULL DEFAULT 0,
				  `days` int(5) DEFAULT 0,
				  `regsn` int(9) NOT NULL DEFAULT 0,
				  `westDT` varchar(10) DEFAULT NULL,
				  `icseqno` varchar(7) DEFAULT NULL
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='zhi_type6'");
	
	$conn->exec("ALTER TABLE `zhis6`
					  ADD KEY `upatno` (`upatno`,`date`,`icseqno`),
					  ADD KEY `regsn` (`regsn`)");
	
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

	//掛號-處置-處方
	$sql = "SELECT * FROM z_history.dbf where type in ('1','6') ";
	$result=$db->query($sql);
	$upa='';
	$rsn=0; 
	$drsn=0;
	$rxdays=0;
	$PT='';
	$DT='';
	foreach ($result as $key => $value) {
		
		$ukey=$value['ukey'];  //掛號主鍵
		$upatno=trim($value['upatno']);   //患者主鍵
		$rocDT=trim($value['date']);
		$westDT=WestDT($rocDT);
		$type=trim($value['type']);
		$dstr1=trim(addslashes(mb_convert_encoding($value['dstr1'],"UTF-8","BIG5")));
		$code=trim(mb_convert_encoding($value['code'],"UTF-8","BIG5"));
		$dstr2=trim(addslashes(mb_convert_encoding($value['dstr2'],"UTF-8","BIG5")));
		$patch=$value['patch'];
		switch ($type) {
			case '1':
				$icseqno=substr($rocDT,0,3).$code;
				break;
			case '6':
				if ($code!=''){
					$d1=explode('#', $dstr1);
					$time=explode(',',$d1[1]);
					$drugQ=explode(';', str_replace("\r\n", "", $patch));
					$drugno=$code;
					$dose=$d1[0];
					$times=trim($time[0]);
					$TotalQ=$drugQ[0];
					$days=($drugQ[3]==null)?0:$drugQ[3];
					$part=substr($time[1],-2);
					$sql="insert into zhis6(ukey,upatno,date,code,dose,times,part,drugname,totalq,days,westDT,icseqno)
							values('$ukey','$upatno','$rocDT','$code','$dose','$times','$part','$dstr2',$TotalQ,$days,'$westDT','$icseqno')";
					$ok=$conn->exec($sql);
					if ($ok==0){
						echo "6-失敗：$sql <br>";	
					}	
				}
				break;
		}
	}

	echo "<br>處方箋 資料轉換完畢!!";

?>