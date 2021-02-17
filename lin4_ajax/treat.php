<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);
	$conn->exec("ALTER TABLE `treat_record` CHANGE `side` `side` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '牙面';");
	
	//處置表
	$conn->exec("truncate table treat_record");

	$sql = "SELECT * FROM wpcode.dbf";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$csn=$value['sno'];
		$order=trim($value['order']);  //患者第幾次看診
		$fdi=str_replace(',', '',trim($value['loc']));
		$trcode=trim($value['pcode']);
		$face= str_replace(' ', '', mb_convert_encoding(addslashes($value['surface']),"UTF-8","BIG5"));
		$price=$value['uprice'];
		$qty=$value['qty'];
		$addps=$value['adds'];
		$pamt=$value['price'];
		$opcode1=$value['opcode1'];
		$opcode2=$value['opcode2'];
		$icd10=$value['upcode10'];	
		$sickno=$value['tcode'];	
		$sql="insert into treat_record(regsn,cussn,uploadd,fdi,trcode,side,punitfee,nums,add_percent,pamt,icd10,icd10pcs1,icd10pcs2,sickno)
				values(0,$csn,'$order','$fdi','$trcode','$face',$price,$qty,$addps,$pamt,'$icd10','$icd10pcs1','$icd10pcs2','$sickno')";
		$iok=$conn->exec($sql);
		if ($iok==0){
			echo "新增處置失敗：".$sql."<br>";
		}
	}

	$conn->exec("update registration r,treat_record t 
					set t.regsn=r.regsn
					where r.cussn=t.cussn
					and r.uploadd=t.uploadd");

	$conn->exec("update registration r,treat_record t 
					set r.trcode=t.trcode
					where r.regsn=t.regsn
					and t.trcode in ('00127C','01271C','01272C','00315C','00316C')");

	$conn->exec("update `registration` 
					set trcode='00130C'
					where  ic_type ='02'
					and rx_type in ('0','2')
					and trcode is null");

	$conn->exec("update `registration` 
					set trcode='00129C'
					where  ic_type ='02'
					and rx_type='1'
					and trcode is null");

	$conn->exec("update treat_record set deldate ='1911-01-01' where trcode in ('00127C','01271C','01272C','01273C','00315C','00316C','00317C')");


	echo "<h1>處置 資料轉換完成</h1>";

?>