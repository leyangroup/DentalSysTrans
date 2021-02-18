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
    $conn->exec("truncate table registration");
	$conn->exec("truncate table treat_record");
	$conn->exec("truncate table prescription");



   $path=$_GET['path'];

	//新增患者
 	set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$path);

	//取得醫師
	$dr=$conn->query("select sfsn,sfname from staff where position='D' ");
	$drArray=[];
	foreach ($dr as $key => $drV) {
		$drname=trim($drV['sfname']);
		$drArray[$drname]=$drV['sfsn'];
	}

	//掛號-處置-處方
	$sql = "SELECT * FROM z_history.dbf";
	$result=$db->query($sql);
	$trA=[];
	$preArr=[];
	$upa='';
	$pcnt=0;
	$tcnt=0;
	$rsn=0; 
	$drsn=0;
	$rx_days=0;
	foreach ($result as $key => $value) {
		if ($upa!=$value['upatno']){
			if ($upa==''){
				$upa=$value['upatno'];
			}else{
				// save();
				$upa=$value['upatno'];
			}
		}
		$ukey=$value['ukey'];  //掛號主鍵
		$upatno=trim($value['upatno']);   //患者主鍵
		$rocDT=$value['date'];
		$westDT=WestDT($rocDT);
		$type=trim($value['type']);
		$dstr1=trim(mb_convert_encoding(addslashes($value['dstr1']),"UTF-8","BIG5"));
		$code=trim(mb_convert_encoding($value['code'],"UTF-8","BIG5"));
		$dstr2=trim(mb_convert_encoding(addslashes($value['dstr2']),"UTF-8","BIG5"));
		$patch=$value['patch'];
		$needsave=false;
		echo "$ukey,$upatno,$rocDT,$type,$dstr1,$code,$dstr2<br>";
		switch ($type) {
			case '1':
				$rsn++;
				switch ($dstr1) {
					case '其它專案':
						$trcode='00130C';
						$ictype='02';
						$treatno=$value['dstr2'];
						break;
					case '牙周病照護':
						$trcode='00130C';
						$ictype='02';
						$icseqno=substr($value['date'],0,3).$value['code'];
						$treatno=$value['dstr2'];
						break;
					case '預防保健':
						$trcode='';
						$ictype='AC';
						$icseqno=substr($value['date'],0,3).$value['code'];
						$treatno=$value['dstr2'];
						break;
					case '初診01271C':
						if ($rocDT<='1090331'){
							$trcode='01271C';
						}else{
							$trcode='00315C';
						}
						$ictype='02';
						$icseqno=substr($value['date'],0,3).$value['code'];
						$treatno=$value['dstr2'];
						break;
					case '初診01272C':
						if ($rocDT<='1090331'){
							$trcode='01272C';
						}else{
							$trcode='00316C';
						}
						$ictype='02';
						$icseqno=substr($value['date'],0,3).$value['code'];
						$treatno=$value['dstr2'];
						break;
					case '初診01273C':
						if ($rocDT<='1090331'){
							$trcode='01273C';
						}else{
							$trcode='00317C';
						}
						$ictype='02';
						$icseqno=substr($value['date'],0,3).$value['code'];
						$treatno=$value['dstr2'];
						break;
				}
				$rx_type=2;
				break;	
			
			case '3':
				$drname=str_replace('醫師', '', $dstr1);
				$drsn=$drArray[$drname];
				echo "3.drsn=".$drsn."<br>";
				$sickno=$code;
				break;

			case '4':
				$dstr1=fdiConvert($dstr1);
				if ($dstr1==''){
					if ($isTreat){
						if (substr($dstr2,0,2)=='CC'){
							$trA[$tcnt]['cc']=$dstr2;
						}else{
							$trA[$tcnt]['memo'].=$dstr2;
						}
					}else{
						$chkfinding.=$dstr2;
					}
				}else{
					if ($trA['trcode']!=''){
						$tcnt++;
					}
					$isTreat=true;
					$trA[$tcnt]['fdi']=$dstr1;
					$trA[$tcnt]['trcode']=$value['code'];
					$trA[$tcnt]['treatname']=$dstr2;
					$dtl=explode(';',$patch);
					$trA[$tcnt]['addPercent']=round($dtl[0]/100,2);
					$trA[$tcnt]['nums']=$dtl[1];
					$trA[$tcnt]['price']=$dtl[2];
					$trA[$tcnt]['pamt']=$dtl[3];
				}
				break;
			case '5':  //用藥天數
				$rx_days=$value['dstr1'];
				//有開藥 診察碼要改成有開藥的
				if ($trcode=='00130C'){
					$trcode='00129C';
				}
				$rx_type=1;
				break;

			case '6':
				if ($value['dstr1']!=''){
					$pcnt++;
					$dose=explode('#', $dstr1);
					$time=explode(',',$dose[1]);
					$drugQ=explode(';', str_replace("\r\n", "", $patch));
					$psA[$pcnt]['drugno']=$value['code'];
					$psA[$pcnt]['dose']=$dose[0];
					$psA[$pcnt]['times']=trim($time[0]);
					$psA[$pcnt]['dTotalQ']=$drugQ[0];
					$psA[$pcnt]['days']=$drugQ[3];
					if ($time[1]==''){
						$psA[$pcnt]['part']='PO';
					}else{
						$psA[$pcnt]['part']=$time[1];
					}
				}
				break;

			case '10':
				$amount=$value['code'];
				$partpay=$value['dstr2'];
				$other=explode(';', $patch);				
				$trpay=$other[1];
				$giaamt=$other[3];
				$tamt=$amount-$trpay;
				if ($value['dstr1']==''){
					$startdate='';  //療程開始日
					$startseqno='';  //療程卡號
				}else{
					$startdate=WestDT($dstr1);  //療程開始日
					$startseqno=substr($icseqno,-4);  //療程卡號
				}
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
				$cusno=$inf[1];
				$category=$inf[3];
				$icseqno=substr($rocDT,0,3).$inf[4];
				$nhistatus=$inf[6];
				$nhipartpay=$inf[7];
				$ztransukey=str_replace("\r\n", "", $inf[12]);
				echo 'inf[12]='.$inf[12];
				echo "nhipartpay=$nhipartpay";
				echo "tamt=$tamt";
				echo "trpay=$trpay";
				echo "amount=$amount";
				echo "giaamt=$giaamt";
				echo "ztransukey=$ztransukey";
				echo 'drname='.$drname;
				echo "ic_type=$ictype";
				echo "drsn=$drsn";
				//save
				$sql="insert into registration(regsn,ddate,stdate,trcode,nhi_status,treatno,category,rx_day,rx_type,ic_seqno,nhi_partpay,
					nhi_tamt,trpay,amount,giaamt,case_history,icuploadd,ic_type,drno1,drno2) 
					values
					($rsn,'$rocDT','$upatno','$trcode','$nhistatus','$treatno','$category',$rx_days,$rx_type,'$icseqno'
					,$nhipartpay,$tamt,$trpay,$amount,$giaamt,'4','$ztransukey','$ictype',$drsn,$drsn)";
				echo "$sql<br>";	
				$ok=$conn->exec($sql);	
				if ($ok==0){
					echo "掛號-新增失敗：$sql <br>";	
				}	

				// //存tre處置 $trA 陣列 0->處置碼 、1->加成、2->數量、3->單價、4->小計、5->處置名稱	、6->pcs	 7->icd10	 8->fdi
				foreach ($trA as $key => $treat) {
					$trcode=$treat['trcode'];
					$trname=$treat['treatname'];
					$nums=$treat['nums'];
					$addPercent=$treat['addPercent'];
					$price=$treat['price'];
					$pamt=$treat['pamt'];
					$fdi=$treat['fdi'];
					$sql="insert into treat_record(regsn,ddate,trcode,treatname,nums,add_percent,punitfee,pamt,start_date,start_icseq,icuploadd,icd10,sickno,icd10pcs1,fdi)values
					($rsn,'$date','$trcode','$trname',$nums','$addPercent',$price,$pamt,'$startdate','$startseqno','$ztransukey',
					'$icd10','$icd10','$pcs','$fdi')";
					$ok=$conn->exec($sql);
					if ($ok==0){
						echo '處置新增失敗 --'.$sql.'<br>';
					}
				}
				
				// //存pre處方箋				
				foreach ($psA as $key => $pre) {
					$drugno=$pre['drugno'];
					$totalQ=$pre["dTotalQ"];
					$dose=$pre['dose'];
					$part=$pre['part'];
					$time=$pre['times'];
					$days=$pre['days'];

					$sql="insert into prescription(regsn,ddate,drugno,nhidrugno,totalQ,dose,part,times,day,icuploadd) values
					($rsn,'$date','$drugno','$drugno','$totalQ','$dose','$part','$times',$days,'$ztransukey')";
					$ok=$conn->exec($sql);
					if ($ok==0){
						echo '處方箋新增失敗 --'.$sql.'<br>';
					}
				}				
				
				//清空所有變數
				$category='';
				$trcode='';
				$nhi_status='';
				$drugday=0;
				$rx_type=2;
				$partpay=0;
				$tamt=0;
				$trpay=0;
				$amount=0;
				$giaamt=0;
				$icseqno='';
				$treatno='';
				$dr='';
				$icd10='';
				$trcnt=0;
				$pscnt=0;
				$trA=array();
				$psA=array();
				//清空陣列 變數
				$tcnt=0;
				$pcnt=0;
				$trA=[];
				$preArr=[];
				$rx_days=0;
				$drsn=0;
				break;
			
		}
	}

	echo "<br>掛號 處置 處方箋 資料轉換完畢!!";

?>