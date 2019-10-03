<?php
	include_once "../include/db.php";
    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=C:\cooper");

	$DT=$_GET['DT'];
	$year=substr($DT,0,4);

	//支付標準表
	$conn->exec("truncate table treatment");
	$sql = "SELECT * FROM operasm";
	$result=$db->query($sql);
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		foreach ($row as $k2=> $v2) {
			switch (mb_convert_encoding($k2,"UTF-8","BIG5")) {
				case '複合處置碼':
					$trcode=trim($v2);
					break;
				case '處置金額';
					$price=$v2;
					break;
				case '對應病碼';
					$sickno=trim($v2);
					break;
				case '對應治療';
					$treatno=substr($v2,0,2);
					break;
				case '中文說明';
					$treatname=trim(mb_convert_encoding($v2,"UTF-8","BIG5"));
					break;
				case '對應病歷';
					$v2=str_replace("'", " ", $v2);
					$memo='Tx.'.trim(mb_convert_encoding($v2,"UTF-8","BIG5"));
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
		// echo "支付標準".$sql."<br>";
		echo "$trcode-$treatname 。 ";
		$conn->exec($sql);
	}


	$sql = "SELECT * FROM operasms";
	$result=$db->query($sql);
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		foreach ($row as $k2=> $v2) {
			switch (mb_convert_encoding($k2,"UTF-8","BIG5")) {
				case '複合處置碼':
					$trcode=trim($v2);
					break;
				case '健保處置碼';
					$nhicode=trim($v2);
					break;
			}
		}

		$sql="update treatment set nhicode='$nhicode' where trcode='$trcode' ";
		echo "支付標準更新健保碼".$nhicode." 。";
		$conn->exec($sql);
	}

	//更新其它資訊
	$sql="update treatment t,nhicode n 
			set t.category=n.category,t.is_oper=n.is_oper,t.is_endo=n.is_endo,t.is_peri=n.is_peri,t.is_oral=n.is_oral,
			t.is_xray=n.is_xray,t.is_pedo=n.is_pedo,
			t.tr_od=n.tr_od,t.tr_endo=n.tr_endo,t.tr_peri=n.tr_peri,t.tr_os=n.tr_os,t.tr_ospath=n.tr_ospath,
			t.tr_pedo=n.tr_pedo,t.fee_unit=n.feeunit
			where t.nhicode=n.nhicode";
	$conn->exec($sql);

	echo "<h3> 更新treat_record的名稱";
	$sql="update treat_record t ,treatment m set t.treatname=m.treatname where t.trcode=m.trcode ";
	$conn->exec($sql);

	//計算成數
	echo "<h3> 更新treat_record的成數";
	$sql="update treat_record t,treatment m 
			 set add_percent=round(punitfee/nhi_fee,1),punitfee=nhi_fee
		   where t.trcode=m.trcode
			 and punitfee!=nhi_fee and ddate like '2019%'";

	echo "<h2> 支付標準更新完畢";

	$sql="update customer c set lastdate=(select max(ddate) from registration where cussn=c.cussn and deldate is null and seqno!='000')";
	$conn->exec($sql);
	
	$sql="update customer set lastdate = firstdate where lastdate is null";
	$conn->exec($sql);

	$sql="update treatment set nhicode='RCT00' where trcode='01002C' ";
	$conn->exec($sql);

	$sql="update treatment set nhicode='RCT01' where trcode='01001C' ";
	$conn->exec($sql);
	
	echo "<h2> 修正療程";

	$sql="UPDATE registration r,treat_record t  set t.start_icseq=r.ic_seqno WHERE r.regsn=t.regsn and r.ic_type='AB' ";
	$conn->exec($sql);

	$sql="UPDATE registration r,treat_record t  set t.start_date=r.ddate WHERE r.cussn=t.cussn and r.ic_seqno=t.start_icseq";
	$conn->exec($sql);

	$sql="UPDATE registration set duptdate=ic_seqno,ic_seqno=left(ic_seqno,3) where ic_type='AB'";
	$conn->exec($sql);

	$sql="UPDATE treat_record set start_icseq=right(start_icseq,4) where length(start_icseq)=7";
	$conn->exec($sql);


	$sql="UPDATE registration set rx_type=1 where trcode in ('00121C','00123C','00125C','00129C','00133C') ";
	$conn->exec($sql);

	$sql="update registration r,treat_record t set r.cc=t.cc where r.regsn=t.regsn and t.cc is not null and t.cc!=''";
	$conn->exec($sql);

	echo "產生charge";
	$sql="truncate table charge";
	$conn->exec($sql);
	
	$sql="insert into charge (ddate,chargetime,cussn,regpay,partpay,`add`,discsn,discreg,discpart,minus,balance ,is_oweic)
			SELECT ddate,reg_time,cussn,reg_pay,nhi_partpay,reg_pay+nhi_partpay,case when r.discid is null then 0 else r.discid end ,
			case when d.reg_disc is null then 0 else d.reg_disc end,
			case when d.partpay_disc is null then 0 else d.partpay_disc end,
			disc_pay,reg_pay+nhi_partpay-disc_pay,'0'
			FROM registration r left join disc_list d
			on r.discid=d.discsn
			where concat(ddate,reg_time)between '2019-01-0100:00' and '2019-08-1412:59'
			and seqno<>'000'
			and is_oweic !='1'
			order by ddate,seqno";
	$conn->exec($sql);

	$sql="update registration r,charge c
			 set r.chargesn=c.sn
		   where r.ddate=c.ddate
			 and r.reg_time=c.chargetime
			 and r.cussn=c.cussn
			 and r.chargesn=0
			 and r.ddate between '2019-01-01' and '2019-08-14' ";
	$conn->exec($sql);

	//十年以上診所會有00127C的初診診察，要把處置改成01271
	$sql="update registration r,treat_record t 
			 set r.trcode=concat(left(t.trcode,5),'C')
		   where r.trcode='00127C'
			 and r.regsn=t.regsn
			 and t.trcode like '0127%';
	$conn->exec($sql);


	
	echo "<h1> 資料轉換完成";


?>