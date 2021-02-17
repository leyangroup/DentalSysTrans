<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);

	//清除處置資料
	$conn->exec("truncate table treat_record");

	$sql = "SELECT * FROM procsk.dbf";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$DT=WestDT($value['ddate']);
		$seqno=$value['seqno'];
		$trcode=trim(mb_convert_encoding($value['pcode'],"UTF-8","BIG5"));
		$fdi=trim(mb_convert_encoding($value['fdi'],"UTF-8","BIG5"));
		$nums=$value['nums'];
		$cc=trim(mb_convert_encoding($value['cc'],"UTF-8","BIG5"));
		$cc=addslashes($cc);
		$startdt=WestDT($value['stdate']);
		$startno=addslashes($value['tream_no']);
		$sick='';
		$icd10='';
		if ($DT<='2015-12-31'){
			$sick=substr(trim($value['sickn']),0,5);
		}else{
			$icd10=trim($value['sickn']);
		}	
		$side=trim(mb_convert_encoding($value['sideno'],"UTF-8","BIG5"));
		$side=addslashes($side);
		$treatname=trim(mb_convert_encoding($value['pname'],"UTF-8","BIG5"));
		$tname=addslashes($treatname);

		$memo=trim(mb_convert_encoding($value['descm'],"UTF-8","BIG5"));
		$memo=addslashes($memo);
		$sql="insert into treat_record
				(regsn,ddate,seqno,trcode,fdi,side,nums,cc,start_date,start_icseq,sickno,icd10,treat_memo,treatname)
			  values(0,'$DT','$seqno','$trcode','$fdi','$side','$nums','$cc','$startdt','$startno','$sick','$icd10','$memo','$tname')";
		
		$ok=$conn->exec($sql);
		if ($ok==0){
			echo "新增處置失敗：$sql<br>";
		}
	}
	echo "資料處理<br>";
	$sql="update registration set cussn=0 where cussn is null";
	$conn->exec($sql);

	$sql="update registration r,treat_record t 
			 set t.regsn=r.regsn,t.cussn=r.cussn
		   where r.ddate=t.ddate
		     and r.seqno=t.seqno";
	$conn->exec($sql);

	$sql="update registration r,treat_record t 
			 set r.trcode=case when length(trim(t.trcode))=5 then concat(trim(t.trcode),'C') else t.trcode end,
			 r.check_finding=t.treat_memo,trpay=punitfee,amount=punitfee+nhi_tamt+nhi_damt,giaamt=punitfee+nhi_tamt+nhi_damt-nhi_partpay-drug_partpay
		   where r.regsn=t.regsn
		     and t.trcode in ('01271','01272','01273','01271C','01272C','01273C') ";

	$conn->exec($sql);
	  	     
	$sql="update registration r,treat_record t 
			 set r.trcode=case when length(trim(t.trcode))=5 then concat(trim(t.trcode),'C') else t.trcode end,
			 	r.check_finding=t.treat_memo,trpay=punitfee,
			 	amount=punitfee+nhi_tamt+nhi_damt+drugsv,giaamt=punitfee+nhi_tamt+nhi_damt+drugsv-nhi_partpay-drug_partpay
		   where r.regsn=t.regsn
		     and t.trcode in ('00127','00127C') ";
	$conn->exec($sql);

	
	$sql="update registration r
			set trcode='00315C', trpay=635,amount=635+nhi_tamt+nhi_damt,giaamt=635+nhi_tamt+nhi_damt-nhi_partpay-drug_partpay
		   where r.ddate >='2020-04-01'
		     and trcode in ('01271','01271C')";
	$conn->exec($sql);
	
	$sql="update registration r
			set trcode='00316C', trpay=635,amount=635+nhi_tamt+nhi_damt,giaamt=635+nhi_tamt+nhi_damt-nhi_partpay-drug_partpay
		   where r.ddate >='2020-04-01'
		     and trcode in ('01272','01272C')";
	$conn->exec($sql);

	$sql="update registration r
			set trcode='00317C',trpay=635,amount=635+nhi_tamt+nhi_damt,giaamt=635+nhi_tamt+nhi_damt-nhi_partpay-drug_partpay
		   where r.ddate >='2020-04-01'
		     and trcode in ('01273','01273C')";
	$conn->exec($sql);

	$sql="update treat_record set deldate='1911-01-01' where trcode in ('00127','00127C','01271','01272','01273','01271C','01272C','01273C','00315C','00316C','00317C','00315','00316','00317')";
	$conn->exec($sql);

	$conn->exec("update treat_record set start_date=null where start_date=''");
	$conn->exec("update treat_record set start_icseq=null where start_icseq=''");
	$conn->exec("update treat_record set deldate='1911-01-01' where trcode='' ");
	$conn->exec("update registration r, treat_record t set r.cc=t.cc where r.regsn =t.regsn and t.cc is not null and t.cc !='' ");

	//加成處理
	$sql="UPDATE registration r,treat_record t ,customer c
			set t.add_percent=1.3
			WHERE r.regsn=t.regsn
			and r.cussn=c.cussn
			and r.ddate <=concat(left(date_add(cusbirthday,interval 48 month),8),'31')
			and r.ic_type in ('02','AB')
			and r.category!='16'";
	$conn->exec($sql);

	$sql="UPDATE treat_record t, tmp_giadtl d
			set t.add_percent=d.addpercent,t.punitfee=d.price,t.nums=d.nums,t.pamt=d.pamt
			where t.ddate=d.DT
			and t.seqno=d.seq
			and t.fdi=d.fdi
			and t.trcode=d.trcode";
	$conn->exec($sql);

	$conn->exec("UPDATE registration r where r.nhi_tamt=(select sum(pamt) from treat_record where regsn=r.regsn");
	$conn->exec("UPDATE registration r where r.nhi_tamt=(select sum(pamt) from treat_record where regsn=r.regsn");

	
	echo "<br><br>掛號 資料轉換完成!!";

?>