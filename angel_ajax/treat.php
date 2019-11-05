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
		$cc=str_replace("\\", "＼", $cc);
		$cc=str_replace("'", "\'", $cc);
		$startdt=WestDT($value['stdate']);
		$startno=str_replace("'", "\'",$value['tream_no']);
		$sick='';
		$icd10='';
		if ($DT<='2015-12-31'){
			$sick=substr(trim($value['sickn']),0,5);
		}else{
			$icd10=trim($value['sickn']);
		}	
		$side=trim(mb_convert_encoding($value['sideno'],"UTF-8","BIG5"));
		$side=str_replace("\\", "＼", $side);
		$side=str_replace("'", "\'", $side);
		$treatname=trim(mb_convert_encoding($value['pname'],"UTF-8","BIG5"));
		$tname=str_replace("\\", "＼", $treatname);
		$tname=str_replace("'", "\'", $tname);

		$memo=trim(mb_convert_encoding($value['descm'],"UTF-8","BIG5"));
		$memo=str_replace("\\", "＼", $memo);
		$memo=str_replace("'", "\'", $memo);
		$sql="insert into treat_record(regsn,ddate,seqno,trcode,fdi,side,nums,cc,start_date,start_icseq,sickno,icd10,treat_memo,treatname)
			  values(0,'$DT','$seqno','$trcode','$fdi','$side','$nums','$cc','$startdt','$startno','$sick','$icd10','$memo','$tname')";
		//if ($DT!='2017-05-05'){
			echo "$DT.$seqno。";
			
		// }else{
		// 	echo $sql;
		// 	echo "<br>";
		// }  
		$conn->exec($sql);
	}
	echo "資料處理<br>";
	$sql="update registration r,treat_record t 
			 set t.regsn=r.regsn,t.cussn=r.cussn
		   where r.ddate=t.ddate
		     and r.seqno=t.seqno";
	$conn->exec($sql);

	$sql="update registration r,treat_record t 
			 set r.trcode=case when length(trim(t.trcode))=5 then concat(trim(t.trcode),'C') else t.trcode end,
			 r.check_finding=t.treat_memo,trpay=600,amount=600+nhi_tamt+nhi_damt,giaamt=600+nhi_tamt+nhi_damt-nhi_partpay-drug_partpay
		   where r.regsn=t.regsn
		     and t.trcode in ('01271','01272','01273','01271C','01272C','01273C')";
	$conn->exec($sql);
	  	     
	$sql="update registration r,treat_record t 
			 set r.trcode=case when length(trim(t.trcode))=5 then concat(trim(t.trcode),'C') else t.trcode end,
			 r.check_finding=t.treat_memo,trpay=400,amount=400+nhi_tamt+nhi_damt,giaamt=400+nhi_tamt+nhi_damt-nhi_partpay-drug_partpay
		   where r.regsn=t.regsn
		     and t.trcode in ('00127','00127C') ";
	$conn->exec($sql);
	
	$sql="update treat_record set deldate='1911-01-01' where trcode in ('00127','00127C','01271','01272','01273','01271C','01272C','01273C')";
	$conn->exec($sql);

	$conn->exec("update treat_record set start_date=null where start_date=''");
	$conn->exec("update treat_record set start_icseq=null where start_icseq=''");

	echo "<br><br>掛號 資料轉換完成!!";

?>