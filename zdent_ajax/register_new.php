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
	
	$conn->exec("DROP TABLE IF EXISTS `zhis1`");
	$conn->exec("CREATE TABLE `zhis1` (
				  `ukey` varchar(10) DEFAULT NULL,
				  `upatno` varchar(10) DEFAULT NULL,
				  `type` varchar(2) DEFAULT NULL,
				  `date` varchar(10) DEFAULT NULL,
				  `category` varchar(2) DEFAULT NULL,
				  `ic_type` varchar(2) DEFAULT NULL,
				  `trcode` varchar(10) DEFAULT NULL,
				  `icseqno` varchar(7) DEFAULT NULL,
				  `treatno` varchar(8) DEFAULT NULL,
				  `cussn` int(10) NOT NULL DEFAULT 0,
				  `regsn` int(10) NOT NULL DEFAULT 0,
				  `westDT` varchar(10) DEFAULT NULL,
				  `barid` varchar(4) DEFAULT NULL,
				  `categoryName` varchar(20) DEFAULT NULL,
				  `cusno` varchar(10) DEFAULT NULL
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='zhis_type1'");

	$conn->exec("DROP TABLE IF EXISTS `zhis3`");
	$conn->exec("CREATE TABLE `zhis3` (
				  `ukey` varchar(10) NOT NULL,
				  `upatno` varchar(10) DEFAULT NULL,
				  `date` varchar(10) DEFAULT NULL,
				  `drname` varchar(10) DEFAULT NULL,
				  `sickno` varchar(10) DEFAULT NULL,
				  `drsn` int(10) NOT NULL DEFAULT 0,
				  `regsn` int(10) NOT NULL DEFAULT 0,
				  `westDT` varchar(10) DEFAULT NULL,
				  `icseqno` varchar(7) DEFAULT NULL
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='zhis3'");

	

	$conn->exec("DROP TABLE IF EXISTS `zhis5`");
	$conn->exec("CREATE TABLE `zhis5` (
				  `ukey` varchar(10) DEFAULT NULL,
				  `upatno` varchar(10) DEFAULT NULL,
				  `date` varchar(10) DEFAULT NULL,
				  `rx_day` int(5) NOT NULL DEFAULT 0,
				  `regsn` int(10) NOT NULL DEFAULT 0,
				  `westDT` varchar(10) DEFAULT NULL,
				  `icseqno` varchar(7) DEFAULT NULL
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='zhis_type5'");

	

	$conn->exec("DROP TABLE IF EXISTS `zhis10`");
	$conn->exec("CREATE TABLE `zhis10` (
				  `ukey` varchar(10) DEFAULT NULL,
				  `upatno` varchar(10) DEFAULT NULL,
				  `date` varchar(10) DEFAULT NULL,
				  `amount` int(10) NOT NULL DEFAULT 0,
				  `partpay` int(10) NOT NULL DEFAULT 0,
				  `trpay` int(10) NOT NULL DEFAULT 0,
				  `giaamt` int(10) NOT NULL DEFAULT 0,
				  `tamt` int(10) NOT NULL DEFAULT 0,
				  `startdate` varchar(10) DEFAULT NULL,
				  `starticseq` varchar(10) DEFAULT NULL,
				  `regsn` int(10) NOT NULL DEFAULT 0,
				  `westDT` varchar(10) DEFAULT NULL,
				  `icseqno` varchar(7) DEFAULT NULL
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='zhis_type10'");

	$conn->exec("DROP TABLE IF EXISTS `zhis88`");
	$conn->exec("CREATE TABLE `zhis88` (
				  `ukey` varchar(10) DEFAULT NULL,
				  `upatno` varchar(10) DEFAULT NULL,
				  `date` varchar(10) DEFAULT NULL,
				  `cusname` varchar(20) DEFAULT NULL,
				  `cusno` varchar(10) DEFAULT NULL,
				  `cusid` varchar(10) DEFAULT NULL,
				  `icno` varchar(7) DEFAULT NULL,
				  `nhistatus` varchar(3) DEFAULT NULL,
				  `nhipartpay` int(5) NOT NULL DEFAULT 0,
				  `ztransukey` varchar(10) DEFAULT NULL,
				  `regsn` int(10) NOT NULL DEFAULT 0,
				  `westDT` varchar(10) DEFAULT NULL,
				  `category` varchar(2) DEFAULT NULL,
				  `icseqno` varchar(7) DEFAULT NULL
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='zhis_type88'");

	$conn->exec("DROP TABLE IF EXISTS `znupload`");
	$conn->exec("CREATE TABLE `znupload` (
				  `ukey` varchar(8) DEFAULT NULL,
				  `ubkey` varchar(8) DEFAULT NULL,
				  `upatno` varchar(8) DEFAULT NULL,
				  `name` varchar(20) DEFAULT NULL,
				  `id` varchar(10) DEFAULT NULL,
				  `dr` varchar(10) DEFAULT NULL,
				  `drid` varchar(10) DEFAULT NULL,
				  `icd9` varchar(10) DEFAULT NULL,
				  `icdate` varchar(13) DEFAULT NULL,
				  `selfare` int(5) NOT NULL DEFAULT 0,
				  `totalfare` int(9) NOT NULL DEFAULT 0
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='NUPload'");

	$conn->exec("DROP TABLE IF EXISTS `ztreat`");
	$conn->exec("CREATE TABLE `ztreat` (
				  `ztransukey` varchar(10) DEFAULT NULL,
				  `a9` varchar(2) DEFAULT NULL,
				  `code` varchar(12) DEFAULT NULL,
				  `posi` varchar(12) DEFAULT NULL,
				  `qnt` varchar(15) DEFAULT NULL,
				  `drugday` varchar(5) DEFAULT NULL,
				  `total` int(9) NOT NULL DEFAULT 0,
				  `note` int(10) NOT NULL DEFAULT 0,
				  `regsn` int(11) NOT NULL DEFAULT 0
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='z處置牙位'");

	$conn->exec("DROP TABLE IF EXISTS znupload");
	$conn->exec("CREATE TABLE `znupload` (
				  `ukey` varchar(8) DEFAULT NULL,
				  `ubkey` varchar(8) DEFAULT NULL,
				  `upatno` varchar(8) DEFAULT NULL,
				  `name` varchar(20) DEFAULT NULL,
				  `id` varchar(10) DEFAULT NULL,
				  `dr` varchar(10) DEFAULT NULL,
				  `drid` varchar(10) DEFAULT NULL,
				  `icd9` varchar(10) DEFAULT NULL,
				  `icdate` varchar(13) DEFAULT NULL,
				  `selfare` int(5) NOT NULL DEFAULT 0,
				  `totalfare` int(9) NOT NULL DEFAULT 0
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='NUPload' ");

	$conn->exec("ALTER TABLE `znupload`
					ADD KEY `ubkey` (`ubkey`),
					ADD KEY `icdate` (`icdate`)");

	$conn->exec("ALTER TABLE `zhis1`
					  ADD KEY `regsn` (`regsn`),
					  ADD KEY `upatno` (`upatno`,`date`,`icseqno`)");

	$conn->exec("ALTER TABLE `zhis3`
					  ADD KEY `regsn` (`regsn`),
					  ADD KEY `upatno` (`upatno`,`date`,`icseqno`)");


	$conn->exec("ALTER TABLE `zhis5`
					  ADD KEY `regsn` (`regsn`),
					  ADD KEY `upatno` (`upatno`,`date`,`icseqno`)");

	$conn->exec("ALTER TABLE `zhis10`
					  ADD KEY `regsn` (`regsn`),
					  ADD KEY `upatno` (`upatno`,`date`,`icseqno`)");

	$conn->exec("ALTER TABLE `zhis88`
					  ADD KEY `regsn` (`regsn`),
					  ADD KEY `upatno` (`upatno`,`date`,`icseqno`)");

	$conn->exec("ALTER TABLE `znupload`
					  ADD KEY `ubkey` (`ubkey`),
					  ADD KEY `icdate` (`icdate`)");
	
	$conn->exec("ALTER TABLE `ztreat`
					  ADD KEY `ztransukey` (`ztransukey`),
					  ADD KEY `regsn` (`regsn`) ");
	
   	$path=$_GET['path'];

	// //新增患者
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
	$sql = "SELECT * FROM z_history.dbf where type in ('1','3','5','10','88')";
	$result=$db->query($sql);
	$upa='';
	$rsn=0; 
	$drsn=0;
	$rxdays=0;
	$PT='';
	$DT='';
	$seq=0;
	foreach ($result as $key => $value) {
		$ukey=$value['ukey'];  //掛號主鍵
		$upatno=trim($value['upatno']);   //患者主鍵
		$rocDT=trim($value['date']);
		$westDT=WestDT($rocDT);
		$type=trim($value['type']);
		$dstr1=trim(addslashes(mb_convert_encoding($value['dstr1'],"UTF-8","BIG5")));
		$code=trim(mb_convert_encoding($value['code'],"UTF-8","BIG5"));
		$dstr2=str_replace('"', '', $value['dstr2']);
		$dstr2=str_replace('`', '', $dstr2);
		$dstr2=trim(addslashes(mb_convert_encoding($dstr2,"UTF-8","BIG5")));
		$patch=$value['patch'];
		$categoryname=$dstr1;
		switch ($type) {
			case '1':
				$rsn++;
				switch ($dstr1) {
					case '牙周病照護':
						if ($rocDT<'1090301'){
							$category='15';
						}else{
							$category='19';
						}
						$ictype='02';
						$icseqno=substr($rocDT,0,3).$code;
						if ($rocDT<='1090331'){
							$trcode='00130C';
						}else{
							$trcode='00306C';
						}
						$treatno=$dstr2;
						
						break;
					case '預防保健':
						$category='A3';
						$ictype='AC';
						$icseqno=substr($rocDT,0,3).$code;
						$trcode='';
						$treatno=$dstr2;
						break;
					case '初診01271C':
						$category='19';
						$ictype='02';
						$icseqno=substr($rocDT,0,3).$code;
						if ($rocDT<='1090331'){
							$trcode='01271C';
						}else{
							$trcode='00315C';
						}
						$treatno=$dstr2;
						break;
					case '初診01272C':
						$category='19';
						$ictype='02';
						$icseqno=substr($rocDT,0,3).$code;
						if ($rocDT<='1090331'){
							$trcode='01272C';
						}else{
							$trcode='00316C';
						}
						$treatno=$dstr2;
						break;
					case '初診01273C':
						$category='19';
						$ictype='02';
						$icseqno=substr($rocDT,0,3).$code;
						if ($rocDT<='1090331'){
							$trcode='01273C';
						}else{
							$trcode='00317C';
						}
						$treatno=$dstr2;
						break;
					case '(團)極重度特殊':
					case '(團)重度特殊':
					case '(團)中度特殊':
					case '(團)輕度特殊':
						$barid=substr($dstr2,0,2);
						$category='16';
						$ictype='02';
						$icseqno=substr($rocDT,0,3).$code;
						$treatno=$dstr2;
						switch ($barid) {
							case 'FG':
							case 'FH':
							case 'FK':
							case 'FL':
								$trcode=($rocDT<='1090331')?'00128C':'00311C';
								break;
							case 'FI':
							case 'FM':
							case 'FV':
							case 'FX':
								$trcode=($rocDT<='1090331')?'00301C':'00312C';
								break;
							case 'FC':
							case 'FD':
							case 'FE':
							case 'FF':
								$trcode=($rocDT<='1090331')?'00302C':'00313C';
								break;
							case 'FN':
							case 'FJ':
								$trcode=($rocDT<='1090331')?'00303C':'00314C';
								break;
						}
					 	break;
					default :
						$category='19';
						$ictype='02';
						$icseqno=substr($rocDT,0,3).$code;
						if ($rocDT<='1090331'){
							$trcode='00130C';
						}else{
							$trcode='00306C';
						}
						$treatno=$dstr2;
						break;
				}
				$sql="insert into zhis1(ukey,upatno,date,category,ic_type,trcode,icseqno,treatno,regsn,westDT,
										barid,categoryname)
					  values('$ukey','$upatno','$rocDT','$category','$ictype','$trcode','$icseqno',
					  		'$treatno',$rsn,'$westDT','$barid','$categoryname')";
				$ok=$conn->exec($sql);
				if ($ok==0){
					echo "1-失敗：$sql <br>";	
				}	
				break;	
			
			case '3':
				$drname=str_replace('醫師','', $dstr1);
				$drsn=$drArray[$drname];
				if ($drsn==null){
					$drsn=0;
				}
				$sickno=$code;
				$sql="insert into zhis3(ukey,upatno,date,drname,sickno,drsn,westDT,icseqno,regsn)
				values('$ukey','$upatno','$rocDT','$drname','$sickno',$drsn,'$westDT','$icseqno',$rsn)";
				$ok=$conn->exec($sql);
				if ($ok==0){
					echo "3-失敗：$sql <br>";	
				}	
				break;

			case '5':  //用藥天數
				$rxday=($dstr1=='')?0:$dstr1;
				//有開藥 診察碼要改成有開藥的
				
				$rx_type=1;
				$sql="insert into zhis5(ukey,upatno,date,rx_day,westDT,icseqno,regsn)values('$ukey','$upatno','$rocDT',$rxday,'$westDT','$icseqno',$rsn)";
				$ok=$conn->exec($sql);
				if ($ok==0){
					echo "5-失敗：$sql <br>";	
				}	
				break;
			case '10':
				$amount=(is_numeric($code))?$code:0;
				$partpay=(is_numeric($dstr2))?$dstr2:0;
				$other=explode(';', $patch);
				$trpay=(is_numeric($other[1]))?$other[1]:0;
				$giaamt=(is_numeric($other[3]))?$other[3]:0;
				$tamt=$amount-$trpay;
				if ($dstr1==''){
					$startdate='';  //療程開始日
					$startseqno='';  //療程卡號
				}else{
					$startdate=str_replace('/', '-', $dstr1) ;  //療程開始日
					$startseqno=substr($icseqno,-4);  //療程卡號
				}
				$sql="insert into zhis10(ukey,upatno,date,amount,partpay,trpay,giaamt,tamt,startdate,starticseq,westDT,icseqno,regsn)
						values('$ukey','$upatno','$rocDT',$amount,$partpay,$trpay,$giaamt,$tamt,'$startdate','$startseqno','$westDT','$icseqno',$rsn)";
				$ok=$conn->exec($sql);
				if ($ok==0){
					echo "10-失敗：$sql <br>";	
				}	
				$startdate='';
				$startseqno='';
				break;
			case '88':
				if ($startdate!=''){
					$ictype='AB';
					$trcode='';
					$nhi_status='009';
					$nhi_partpay=0;
				}
				$Data=$dstr1.$code.$dstr2;
				$inf=explode(';',$Data ) ;
				if (count($inf)==13){
					$cusname=$inf[0];
					$cusno=$inf[1];
					$cusid=$inf[2];
					$category=$inf[3];
					$icno=substr($rocDT,0,3).$inf[4];
					$nhistatus=$inf[6];
					$nhipartpay=($inf[7]==null)?0:$inf[7];
					$ztransukey=str_replace("\r\n", "", $inf[11]);
					$sql="insert into zhis88(ukey,upatno,date,cusname,cusno,cusid,icno,nhistatus,nhipartpay,ztransukey,westDT,category,icseqno,regsn)
						values('$ukey','$upatno','$rocDT','$cusname','$cusno','$cusid','$icno','$nhistatus',$nhipartpay,'$ztransukey','$westDT','$category','$icseqno',$rsn)";
				}else{
					$cusname=$inf[0];
					$cusno=$inf[1];			
					$sql="insert into zhis88(ukey,upatno,date,cusname,cusno,cusid,icno,nhistatus,nhipartpay,ztransukey,westDT,category,icseqno,regsn)
						values('$ukey','$upatno','$rocDT','$cusname','$cusno','','','',0,'','','','$icseqno',$rsn)";
				}
				$ok=$conn->exec($sql);	
				if ($ok==0){
					echo "88-失敗：$sql <br>";	
				}	
				$icseqno='';
				break;
		}
	}

	//丟nupload 
	$sql = "SELECT * FROM z_nupload.dbf";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$ukey=$value['ukey'];
		$ubkey=$value['ubkey'];
		$upatno=$value['upatno'];
		$name=trim(addslashes(mb_convert_encoding($value['name'],"UTF-8","BIG5")));
		$id=$value['id'];
		$dr=trim(addslashes(mb_convert_encoding($value['doc'],"UTF-8","BIG5")));
		$drid=$value['docid'];
		$icd9=$value['icd9'];
		$icdate=$value['date'];
		$selfare=$value['selfare'];
		$totalfare=$value['totalfare'];
		$sql="insert into znupload 
				values('$ukey','$ubkey','$upatno','$name','$id','$dr','$drid','$icd9','$icdate',$selfare,$totalfare)";
		$ok=$conn->exec($sql);
		if ($ok==0){
			echo "nupload-失敗：$sql<br>";
		}
	}



	echo "<br>掛號 資料轉換完畢!!";

?>