<?php 
    include_once "include/db.php";
    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=C:\cooper");

	//院所基本資料
	$sql = "SELECT * FROM clinic.dbf";
	$result=$db->query($sql);
	$rs=$result->fetchAll(PDO::FETCH_ASSOC);
	foreach ($rs as $key => $value) {
		foreach ($value as $key2=> $value2) {
			switch (mb_convert_encoding($key2,"UTF-8","BIG5")) {
				case '診所名稱':
					$cname=trim(mb_convert_encoding($value2,"UTF-8","BIG5"));
					break;
				case '負責人':
					$owner=trim(mb_convert_encoding($value2,"UTF-8","BIG5"));
					break;
				case '診所地址':
					$addr=trim(mb_convert_encoding($value2,"UTF-8","BIG5"));
					break;
				case '醫事機構號':
					$nhicode=trim($value2);
					break;
				case '診所電話':
					$tel=trim($value2);
					break;
				}
		}
		$sql="update basicset set bsname='$cname',bstel='$tel',bsaddr='$addr',owner='$owner',nhicode='$nhicode'";
		echo $sql;
		$conn->exec($sql);
	}
	
	//新增醫師
	$conn->exec("truncate table staff");
	$sql = "SELECT * FROM doctor.dbf";
	$result=$db->query($sql);
	$rs=$result->fetchAll(PDO::FETCH_ASSOC);
	foreach ($rs as $key => $value) {
		foreach ($value as $key2=> $value2) {
			switch (mb_convert_encoding($key2,"UTF-8","BIG5")) {
				case '醫師代號':
					$drno=trim($value2);
					break;
				case '醫師姓名':
					$drname=trim(mb_convert_encoding($value2,"UTF-8","BIG5"));
					break;
				case '到職日期':
					if (trim($value2)==''){
						$fdt='';
					}else{
						$y=substr(trim($value2),0,3)+1911;
						$fdt=$y.'-'.substr(trim($value2),3,2).'-'.substr(trim($value2),-2);
					}
					break;
				case '離職日期':
					if (trim($value2)==''){
						$ldt='';
					}else{
						$y=substr($value2,0,3)+1911;
						$ldt=$y.'-'.substr($value2,3,2).'-'.substr($value2,-2);
					}
					break;
				case '身份證號':
					$id=trim($value2);
					switch (substr($id,1,1)) {
						case '1':
						case 'A':
						case 'C':
						case 'Y':
							$sex='1';
							break;
						default:
							$sex='0';
							break;
					}
					break;
				case '出生日期':
					if (trim($value2)==''){
						$birth='';
					}else{
						$y=substr(trim($value2),0,3)+1911;
						$birth=$y.'-'.substr(trim($value2),3,2).'-'.substr(trim($value2),-2);
					}
					break;
				case '聯絡電話':
					$tel=trim($value2);
					break;
				case '聯絡地址':
					$addr=trim(mb_convert_encoding($value2,"UTF-8","BIG5"));
					break;
				case 'Gmail':
					$gmail=trim($v2);
				}
		}
		$sql="insert into staff(sfno,sfname,sfid,sfbirthday,sfsex,sftel,sfstartjob,sfendjob,sfaddr,position,gmailno)values
					('$drno','$drname','$id','$birth','$sex','$tel','$fdt','$ldt','$addr','D','$gmail') ";
		echo "新增醫師：".$sql."<br>";
		$conn->exec($sql);
	}
	
	$sql="select sfsn,sfno from staff ";
	$RS=$conn->query($sql);
	foreach ($RS as $key => $col) {
		$drArr[$col['sfno']]=$col['sfsn'];
	}

	//新增患者
	$conn->exec("truncate table customer");
	$sql = "SELECT * FROM patient.dbf";
	$result=$db->query($sql);
	$rs=$result->fetchAll(PDO::FETCH_ASSOC);
	foreach ($rs as $key => $value) {
		foreach ($value as $k2=> $v2) {
			switch (mb_convert_encoding($k2,"UTF-8","BIG5")) {
				case '病歷編號':
					$cusno=trim($v2);
					break;
				case '病患姓名':
					$cusname=trim(mb_convert_encoding($v2,"UTF-8","BIG5"));
					break;
				case '性別':
					if (trim($v2)=='女'){
						$cussex=0;
					}else{
						$cussex=1;
					}
					break;
				case '出生日期':
					if (trim($v2)==''){
						$cusbirthday='';
					}else{	
						$y=substr(trim($v2),0,3)+1911;
						$cusbirthday=$y.'-'.substr(trim($v2),3,2).'-'.substr(trim($v2),-2);
					}
					break;
				case '身份證號':
			 		$cusid=trim($v2);
					break;
				case '住宅電話':
					$custel=trim($v2);
					break;
				case '地址':
					$cusaddr=trim(mb_convert_encoding($v2,"UTF-8","BIG5"));
					break;
				case '行動電話':
					$cusmob=trim($v2);
					break;
				case '初診日期':
					if (trim($v2)==''){
						$fdt='';
					}else{
						$y=substr(trim($v2),0,3)+1911;
						$fdt=$y.'-'.substr(trim($v2),3,2).'-'.substr(trim($v2),-2);
					}
					break;
				case '主治醫師':
					$maindrsn=$drArr[trim($v2)];
					break;
				case '病患memo':
					$cusmemo=trim($v2);
					break;
			}
		}
		$sql="insert into customer (cusno,cusname,cussex,cusbirthday,cusid,custel,cusaddr,cusmob,firstdate,maindrno,cusmemo)
			values('$cusno','$cusname','$cussex','$cusbirthday','$cusid','$custel','$cusaddr','$cusmob','$fdt',$maindrsn,'$cusmemo')";
		echo "新增患者：".$sql."<br>";
		$conn->exec($sql);
	}

	// 捉追蹤事項 在patret
	$sql = "SELECT * FROM patret.dbf";
	$result=$db->query($sql);
	$rs=$result->fetchAll(PDO::FETCH_ASSOC);
	foreach ($rs as $key => $value) {
		foreach ($value as $k2=> $v2) {
			switch (mb_convert_encoding($k2,"UTF-8","BIG5")) {
				case '病歷編號':
					$cusno=$v2;
					break;
				case '追蹤內容':
					$tret=trim(mb_convert_encoding($v2,"UTF-8","BIG5"));
					break;
			}
		}
		echo 'tret='.$tret."<br>";
		if ($tret!=''){
			$sql="update customer set cusmemo=concat('$tret','。 ',cusmemo) where cusno='$cusno'";
			echo "追蹤內容：".$sql."<br>";
			$conn->exec($sql);
		}
		
	}

	//優待身份
	$conn->exec("truncate table disc_list");
	$sql = "SELECT * FROM pattype.dbf";
	$result=$db->query($sql);
	$rs=$result->fetchAll(PDO::FETCH_ASSOC);
	foreach ($rs as $key => $value) {
		foreach ($value as $k2=> $v2) {
			switch (mb_convert_encoding($k2,"UTF-8","BIG5")) {
				case '優免代號':
					$discno=$v2;
					break;
				case '優免名稱':	
					$discname=trim(mb_convert_encoding($v2,"UTF-8","BIG5"));
					break;
				case '複診優免':
					$discreg=$v2;
					break;
				case '免部份負擔':
					if ($v2=='T'){
						$discpt=50;
					}else{
						$discpt=0;
					}
					break;
			}
		}
		$sql="insert into disc_list(discid,disc_name,reg_disc,partpay_disc)values
				('$discno','$discname',$discreg,$discpt)";
		echo "新增優待身份.".$sql."<br>";
		$conn->exec($sql);
	}

	$sql="select discsn,discid from disc_list ";
	$RS=$conn->query($sql);
	foreach ($RS as $key => $col) {
		$discArr[$col['discid']]=$col['discsn'];
	}

	//掛號
	$conn->exec("truncate table registration");
	$sql = "SELECT * FROM operate";
	$result=$db->query($sql);
	$DT='';
	$seq=0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		foreach ($row as $k2=> $v2) {
			switch (mb_convert_encoding($k2,"UTF-8","BIG5")) {
				case '病歷編號':
					$cusno=$v2;
					break;
				case '就診次數':
					$cnt=$v2;
					break;
				case '就診日期':
					$year=substr($v2,0,3);
					$y=substr($v2,0,3)+1911;
					$dt=$y.'-'.substr($v2,3,2).'-'.substr($v2,-2);
					if ($DT!=$v2){
						$seq=1;
					}else{
						$seq++;
					}
					break;
				case '療程序號':
					if ($v2==''){
						$icseq=$year;
					}else{
						$icseq=$v2;
					}
					break;
				case '部份負擔碼':  //要看畫面有沒有
					$nhistatus=$v2;
					break;
				case '轉診院所碼':
					$thosp=$v2;
					break;
				case '是否轉出':
					if ($v2=='F'){
						$isout=0;
					}else{
						$isout=1;
					}
					break;
				case '調劑方式':
					$rxtype=$v2;
					break;
				case '處方天數':
					$rxday=$v2;
					break;
				case '醫師代號':
					$drsn=$drArr[$v2];
					break;
				case '健保診察碼':
					$trcode=$v2;
					break;
				case '診察費':
					$trpay=$v2;
					break;
				case '藥事服務費':
					$drugsv=$v2;
					break;
				case '用藥明細費':
					$damt=$v2;
					break;
				case '診療明細費':
					$tamt=$v2;
					break;
				case '健保金額':
					$amount=$v2;
					break;
				case '申報金額':
					$giaamt=$v2;
					break;
				case '申報金額':
					$giaamt=$v2;
					break;
				case '自付金額':
					$partpay=$v2;
					break;
				case '掛號費':
					$regpay=$v2;
					break;
				case '掛號時間':
					if ($v2<="1230"){
						$section='1';
					}else if($v2>='1800'){
						$section='3';
					}else{
						$section='2';
					}
					$regtime=substr($v2,0,2).":".substr($v2,-2);
					break;
				case '掛號編號':
					$seqno=substr('000'.$v2,-3);
					break;
				case '備註':
					$memo=mb_convert_encoding($v2,"UTF-8","BIG5");
					break;
				case '藥品自付':
					$drugpt=$v2;
					break;
				case '優免身份':
					if (trim($v2)==''){
						$disc=0;
					}else{
						$disc=$discArr[$v2];
					}
					break;
				case '掛號優免':
					$discpay=$v2;
					break;
				case '院方初診':
					$isnp=($v2=='T')?1:0;
					break;
				case '日期時間':
					$icdt=$v2;
					break;
			}
		}
		if(strlen($icseq)==7){
			$ic_type='02';
			if ($amount<300){
				$cate='11';
			}else{
				$cate='19';
			}
		}
		if (substr($icseq,3,3)=='IC8'){
			$ic_type='AC';
			$cate='A3';
		}
		if ($icseq=='IC07'){
			$ic_type='AC';
			$cate='B7';
		}
			
		$sql="insert into registration(ddate,seqno,stdate,cusno,drno1,drno2,reg_time,discid,section,isnp,
			trcode,nhi_status,category,rx_day,rx_type,is_out,hosp_from,ic_seqno,ic_type,
			ic_datetime,reg_pay,nhi_partpay,disc_pay,nhi_tamt,nhi_damt,drugsv,trpay,
			amount,giaamt,drug_partpay,memo)
			values('$dt','$seqno','$cnt','$cusno',$drsn,$drsn,'$regtime',$disc,'$section',$isnp,'$trcode',
					'$nhistatus','$cate',$rxday,'$rxtype',$isout,'$thosp','$icseq','$ic_type',
					'$icdt',$regpay,$partpay,$discpay,$tamt,$damt,$drugsv,$trpay,$amount,$giaamt,$drugpt,'$memo')";
		echo "掛號：".$sql."<br>";
		$conn->exec($sql);
	}

	// 填入registration.cussn
	$sql="update registration r,customer c set r.cussn=c.cussn where r.cusno=c.cusno";
	$conn->exec($sql);

	
	//處置
	$conn->exec("truncate table treat_record");
	$sql = "SELECT * FROM op_opasm";
	$result=$db->query($sql);
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		foreach ($row as $k2=> $v2) {
			switch (mb_convert_encoding($k2,"UTF-8","BIG5")) {
				case '病歷編號':
					$cusno=$v2;
					break;
				case '就診次數':
					$cnt=$v2;
					break;
				case '位置代號':
					//echo "v2=".$v2."--";
					$fdiarr=str_split($v2,3);
					//var_dump($fdiarr);
					$ans='';
					for($i=0;$i<sizeof($fdiarr);$i++){
						$rv='';
						$areaA=$i+1;
						$areaC=$i+5;
						
						//取各限象的值
						$areaVal=trim($fdiarr[$i]);
						switch ($areaVal) {
							case 'FM':
							case 'UN':
							case 'UA':
							case 'UB':
							case 'UR':
							case 'UL':
							case 'LL':
							case 'LR':
								$rv=$areaVal;
								break;
							case '':
								$rv='';
								break;	
							default:
								//折分各項限的牙位
								$len=strlen($areaVal);
								$v='';
								for ($j=0;$j<$len;$j++){
									$val=substr($areaVal,$j,1);
									switch ($val) {
										case 'A':
											$v=$areaC.'1';
											break;
										case 'B':
											$v=$areaC.'2';
											break;
										case 'C':
											$v=$areaC.'3';
											break;
										case 'D':
											$v=$areaC.'4';
											break;
										case 'E':
											$v=$areaC.'5';
											break;
										default:
											$v=$areaA.$val;
											break;
									}
									$rv=$rv.$v;
								}
								break;
						}
						$ans=$ans.$rv;
					}
					$fdi=$ans;
					//echo $fdi."<br>";
					//$fdi=$v2;
					break;	
				case '處置代號':
					$trcode=$v2;
					break;
				case '國際病碼':
					$sickno=$v2;
					break;
				case '處置病歷':
					$memo='TX.'.mb_convert_encoding($v2,"UTF-8","BIG5");
					break;
				case '健保數量1':
					$qty=$v2;
					break;
				case '部位':
					$part=$v2;
					break;
				case '健保單價1':
					$price=$v2;
					break;
				case '國際病碼a1':
					$icd10=$v2;
					break;
				case '手術代碼a1':
					$pcs1=$v2;
					break;
				case '手術代碼a2':
					$pcs2=$v2;
					break;
			}
		}
		$pamt=$price*$qty;
		$sql="insert into treat_record(regsn,uploadD,sickn,fdi,trcode,sickno,side,treat_memo,nums,punitfee,add_percent,pamt,icd10,icd10pcs1,icd10pcs2) values
				(0,'$cusno','$cnt','$fdi','$trcode','$sickno','$part','$memo',$qty,$price,1,$pamt,'$icd10','$pcs1','$pcs2')";
		echo "處置：".$sql."<br>";
		$conn->exec($sql);
	}
	//資料關連 掛號表與處置

	$sql="update registration r,treat_record t 
			 set t.regsn=r.regsn,t.ddate=r.ddate,t.seqno=r.seqno,t.cussn=r.cussn
		   where  r.cusno=t.uploadD
		      and r.stdate=t.SICKN";
	$conn->exec($sql);

	//處方箋
	$conn->exec("truncate table prescription");
	$sql = "SELECT * FROM op_drug";
	$result=$db->query($sql);
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		foreach ($row as $k2=> $v2) {
			switch (mb_convert_encoding($k2,"UTF-8","BIG5")) {
				case '病歷編號':
					$cusno=$v2;
					break;
				case '就診次數':
					$cnt=$v2;
					break;
				case '健保藥品碼':
					$nhicode=$v2;
					break;
				case '每次劑量':
					$dose=$v2;
					break;
				case '每日用量':
					$dayuse=$v2;
					break;
				case '天數':
					$days=$v2;
					break;
				case '健保單價':
					$price=$v2;
					break;
				case '使用頻率':
					$freq=trim($v2);
					break;
				case '給藥途徑':
					$part=trim($v2);
					break;
			}
		}
		$total=$dayuse*$days;
		$amt=$total*$price;
		$sql="insert into prescription (regsn,drugno,nhidrugno,qty,totalQ,dose,part,times,uprice,amount,day,uploadd,icuploadd)
					value(0,'$nhicode','$nhicode',$dayuse,$total,$dose,'$part','$freq','$price',$amt,$days,'$cusno','$cnt')";

		echo "處方箋：".$sql."<br>";
		$conn->exec($sql);
	}

	//修正dose
	$sql="update prescription p,drug_dose d set p.dose=d.doseno where p.dose=d.qty";
	$conn->exec($sql);

	$sql="update registration r,prescription p 
		     set p.ddate=r.ddate,p.seqno=r.seqno,p.regsn=r.regsn
		   where r.cusno=p.uploadD
		     and r.stdate=p.icuploadd";
	$conn->exec($sql);

	//藥品檔
	$conn->exec("truncate table drug");
	$sql = "SELECT * FROM drug";
	$result=$db->query($sql);
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		foreach ($row as $k2=> $v2) {
			switch (mb_convert_encoding($k2,"UTF-8","BIG5")) {
				case '藥品代號':
					$drugno=$v2;
					break;

				case '藥品名稱':
					$drugname=trim($v2);
					break;

				case '健保單價':
					$nhifee=$v2;
					break;

				case '每次劑量':
					$dose=$v2;
					break;

				case '使用頻率':
					$times=trim($v2);
					break;

				case '給藥途徑':
					$part=trim($v2);
					break;

				case '停用日期':
					if (trim($v2)==''){
						$isuse=0;
					}else{
						$isuse=1;
					}
					
					break;
			}
		}
		$sql="insert into drug(drugno,name,nhicode,nhifee,fee,dose,part,times,is_use)
				values('$drugno','$drugname','$drugno',$nhifee,$nhifee,$dose,'$part','$times',$isuse)";
		echo "藥品：".$sql."<br>";
		$conn->exec($sql);		
	}

	//更新drug中的dose
	$sql="update drug d, drug_dose s set d.dose=s.doseno where d.dose=s.qty";
	$conn->exec($sql);


	//預約
	$trDT=$_GET['DT'];
	$westy=substr($trDT,0,4)-1911;
	$westYY=$westy.'0101';
	$sql = "SELECT * FROM pregist  ";
	$result=$db->query($sql);
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		foreach ($row as $k2=> $v2) {
			switch (mb_convert_encoding($k2,"UTF-8","BIG5")) {
				case '預約日期':
					$westy=substr($v2,0,3)+1911;
					$westDT=$westy.'-'.substr($v2,3,2).'-'.substr($v2,-2);
					$schDT=$westDT;
					break;
				case '預約時間';
					$schtime=$v2;
					break;
				case '預估需時';
					$schlen=$v2;
					break;
				case '醫師代號';
					$drsn=$drArr[$v2];
					break;
				case '病歷編號';
					$cusno=$v2;
					break;
				case '備註';
					$memo=mb_convert_encoding($v2,"UTF-8","BIG5");
					break;
				case '結案別':
					$apstate='';
					$kind=trim(mb_convert_encoding($v2,"UTF-8","BIG5"));
					if ($kind=='取消'){
						$apstate='4';
					}elseif($kind=='爽約'){
						$apstate='5';
					}
					break;
				case '簡訊備註':
					$smsnote=mb_convert_encoding($v2,"UTF-8","BIG5");
					break;
			}
		}
		if ($kind!='已到'){
			$sql="insert into registration(seqno,ddate,cusno,sch_time,sch_note,schlen,apstate,drno1,drno2,noticemomo)values
					('000','$schDT','$cusno','$schtime','$mdemo',$schlen,'$apstate',$drsn,$drsn,'$smsnote')";
			echo "預約".$sql."<br>";
			$conn->exec($sql);
		}
	}
	//預約電話與手機對應
	$sql="update registration r,customer c set r.schtel=c.custel,r.schmobile=c.cusmob where r.cusno=c.cusno and r.seqno='000'";
	$conn->exec($sql);

	//支付標準表
	$conn->exec("truncate table treatment");
	$sql = "SELECT * FROM operasm";
	$result=$db->query($sql);
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		foreach ($row as $k2=> $v2) {
			switch (mb_convert_encoding($k2,"UTF-8","BIG5")) {
				case '複合處置碼':
					$trcode=$v2;
					break;
				case '處置金額';
					$price=$v2;
					break;
				case '對應病碼';
					$sickno=$v2;
					break;
				case '對應治療';
					$treatno=substr($v2,0,2);
					break;
				case '中文說明';
					$treatname=mb_convert_encoding($v2,"UTF-8","BIG5");
					break;
				case '對應病歷';
					$memo='Tx.'.mb_convert_encoding($v2,"UTF-8","BIG5");
					break;
				case '停用日期';
					$stop=0;
					if (trim($v2)!=''){
						$stop=1;
					}
					break;
				case '對應病碼a1';
					$icd10=$v2;
					break;
				case '對應手術a1';
					$pcs1=$v2;
					break;
			}
		}
		$sql="insert into treatment (trcode,nhicode,treatname,nhi_fee,treatno,sickno,memo,icd10cm,icd10pcs,disable)values
				('$trcode','$trcode','$treatname',$price,'$treatno','$sickno','$memo','$icd10','$pcs1',$stop)";
		echo "支付標準".$sql."<br>";
		$conn->exec($sql);
	}


	$sql = "SELECT * FROM operasms";
	$result=$db->query($sql);
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		foreach ($row as $k2=> $v2) {
			switch (mb_convert_encoding($k2,"UTF-8","BIG5")) {
				case '複合處置碼':
					$trcode=$v2;
					break;
				case '健保處置碼';
					$nhicode=$v2;
					break;
			}
		}

		$sql="update treatment set nhicode='$nhicode' where trcode='$trcode' ";
		echo "支付標準更新健保碼".$sql."<br>";
		$conn->exec($sql);
	}

	//更新其它資訊
	$sql="update treatment t,nhicode n 
			set t.category=n.category,t.is_oper=n.is_oper,t.is_endo=n.is_endo,t.is_peri=n.is_peri,t.is_oral=n.is_oral,t.is_xray=n.is_xray,t.is_pedo=n.is_pedo,
				t.tr_od=n.tr_od,t.tr_endo=n.tr_endo,t.tr_peri=n.tr_peri,t.tr_os=n.tr_os,t.tr_ospath=n.tr_ospath,t.tr_pedo=n.tr_pedo
			where t.nhicode=n.nhicode";
	$conn->exec($sql);

// 	$sql="update treatment set is_oper=1 where left(nhicode,2)='89' and length(nhicode)=6";
// 	$conn->exec($sql);

// 	$sql="update treatment set is_endo=1 where left(nhicode,2)='90' and length(nhicode)=6";
// 	$conn->exec($sql);

// 	$sql="update treatment set is_peri=1 where left(nhicode,2)='91' and length(nhicode)=6";
// 	$conn->exec($sql);

// 	$sql="update treatment set is_oral=1 where left(nhicode,2)='92' and length(nhicode)=6";
// 	$conn->exec($sql);

// 	$sql="update treatment set is_xray=1 where nhicode like '34%' "


// 	$sql="update treatment set is_pedo=1 where left(nhicode,2) in ('89','91','92','34','96')";
// 	$conn->exec($sql);
// 	$sql="update treatment set is_pedo=0 where nhicode in ('92013C','92014C','92015C','92016C','92030C','92031C','92032C','92033C','92050C','89013C','89113C')";
// 	$conn->exec($sql);

// 	$sql="update treatment set is_pedo=0 where nhicode like '90%'";
// 	$conn->exec($sql);
// 	$sql="update treatment set is_pedo=1 where nhicode in ('90004C','90005C','90015C','90016C','90018C','90088C')";
// 	$conn->exec($sql);
// //增加轉診加成的處置
// 	$sql="update treatment set tr_od=0,tr_endo=0,tr_peri=0,tr_os=0,tr_pedo=0,tr_peri=0,tr_ospath=0";
// 	$conn->exec($sql);
// 	$sql="update treatment set tr_od=1,tr_pedo=1 where nhicode like '89%' and nhicode not in ('89006C','89088C')";
// 	$conn->exec($sql);
// 	$sql="update treatment set tr_endo=1,tr_pedo=1 where nhicode like '90%' and nhicode not in ('90004C','90006C','90007C','90088C')";
// 	$conn->exec($sql);
// 	$sql="update treatment set tr_endo=1,tr_pedo=1 where nhicode in ('91009B','92030C','92031C','92032C','92033C')";
// 	$conn->exec($sql);
// 	$sql="update treatment set tr_peri=1,tr_pedo=1 where nhicode like '91%' and nhicode not in ('91001C','91003C','91004C','91088C')";
// 	$conn->exec($sql);
// 	$sql="update treatment set tr_peri=1,tr_pedo=1 where nhicode in ('92030C','92031C','92032C','92033C')";
// 	$conn->exec($sql);
// 	$sql="update treatment set tr_os=1,tr_pedo=1 where nhicode like '92%' and nhicode not in ('92001C','92013C','92088C')";
// 	$conn->exec($sql);
// 	$sql="update treatment set tr_ospath=1 where nhicode in ('92049B','92065B','92073C','92090C','92091C','92095C')";
// 	$conn->exec($sql);

	echo "<h2>轉換結束";
?>