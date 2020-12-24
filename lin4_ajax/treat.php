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
		$sql="insert into treat_record(regsn,cussn,uploadd,fdi,trcode,side,punitfee,nums,add_percent,pamt,icd10,icd10pcs1,icd10pcs2)
				values(0,$csn,'$order','$fdi','$trcode','$face',$price,$qty,$addps,$pamt,'$icd10','$icd10pcs1','$icd10pcs2')";
		$iok=$conn->exec($sql);
		if ($iok==0){
			echo "新增處置失敗：".$sql."<br>";
		}
	}
	echo "<h1>處置 資料轉換完成</h1>";

?>