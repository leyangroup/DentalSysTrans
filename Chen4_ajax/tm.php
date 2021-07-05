<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    $path=$_GET['path'].'\dent01_10.mdb';

	//新增處置
 	set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	//目前先不轉，等上完課確認資料

	//先對應處置中文
	$sql="update treat_record t,treatment m set t.treatname=m.treatname where t.trcode=m.trcode";
	$conn->exec($sql);

    

	// $db = new PDO("odbc:Driver={Microsoft Access Driver (*.mdb)};DBQ=".$path);

	
	// $sql = "select b.no as trcode,c.no as nhicode,c.sale1 as price,icd9 as sickno,icd9 as icd10cm ,
	// 				spec,treat,c.name as name
 //  			  from thfile a,thfile2 b ,THPRICE c
	// 		 where a.no=b.no
	// 		   and b.no1=c.no
	// 		 order by a.no";
	// $result = $db->query($sql);
	// $conn->exec("truncate table treatment");
	// foreach ($result as $key => $value) {
	// 	$trcode=$value['trcode'];
	// 	$nhicode=$value['nhicode'];
	// 	$name=addslashes(mb_convert_encoding($value['name'],"UTF-8","BIG5"));
	// 	$price=($value['price']=='')?0:$value['price'] ;
	// 	$sickno=$value['sickno'];
	// 	$icd10cm=$value['icd10cm'];
	// 	$category=$value['spec'];
	// 	$treat=$value['treat'];

	// 	$sql="insert into treatment(trcode,nhicode,treatname,nhi_fee,category,treatno,icd10cm,sickno) 
	// 			values('$trcode','$nhicode','$name',$price,'$category','$treat','$icd10cm','')";
	// 	$ok=$conn->exec($sql);
	// 	if ($ok==0){
	// 		echo "新增處置資料失敗 $sql<br>";
	// 	}
	// }

	// $conn->exec("drop table if exists test.tm_memo");
	// $conn->exec("CREATE TABLE test.tm_memo (
	// 			  `trcode` varchar(10) NOT NULL,
	// 			  `memo` text DEFAULT NULL
	// 			) ENGINE=MyISAM DEFAULT CHARSET=utf8");
	// $conn->exec("ALTER TABLE `tm_memo` ADD INDEX( `trcode`)");

	// $sql = "SELECT * FROM Thstatus";
	// $result = $db->query($sql);
	// foreach ($result as $key=> $value) {
	// 	$trcode=$value['NO'];
	// 	$memo=($value['NAME1']=='')?"":addslashes(mb_convert_encoding($value['NAME1'],"UTF-8","BIG5")).'\n';
	// 	$memo.=($value['NAME2']=='')?"":addslashes(mb_convert_encoding($value['NAME2'],"UTF-8","BIG5")).'\n';
	// 	$memo.=($value['NAME3']=='')?"":addslashes(mb_convert_encoding($value['NAME3'],"UTF-8","BIG5")).'\n';
	// 	$memo.=($value['NAME4']=='')?"":addslashes(mb_convert_encoding($value['NAME4'],"UTF-8","BIG5")).'\n';
	// 	$memo.=($value['NAME5']=='')?"":addslashes(mb_convert_encoding($value['NAME5'],"UTF-8","BIG5")).'\n';
	// 	$memo.=($value['NAME6']=='')?"":addslashes(mb_convert_encoding($value['NAME6'],"UTF-8","BIG5")).'\n';
	// 	$memo.=($value['NAME7']=='')?"":addslashes(mb_convert_encoding($value['NAME7'],"UTF-8","BIG5")).'\n';
	// 	$memo.=($value['NAME8']=='')?"":addslashes(mb_convert_encoding($value['NAME8'],"UTF-8","BIG5")).'\n';
	// 	$memo.=($value['NAME9']=='')?"":addslashes(mb_convert_encoding($value['NAME9'],"UTF-8","BIG5")).'\n';
	// 	$memo.=($value['NAME10']=='')?"":addslashes(mb_convert_encoding($value['NAME10'],"UTF-8","BIG5")).'\n';
	// 	$insertSQL="INSERT into test.tm_memo values('$trcode','$memo')";
	// 	$ok=$conn->exec($insertSQL);
	// 	if ($ok==0){
	// 		echo "新增處置說明失敗$insertSQL<br>";
	// 	}
	// }

	// $conn->exec("update eprodb.treatment a,test.tm_memo b 
	// 				set a.memo=b.memo
	// 			  where a.trcode=b.trcode");

	// //處理計價單位 轉診加成註記
	// $conn->exec("update nhicode set tr_ospath=1 where nhicode in ('92049B','92065B','92073C','92090C','92091C','92095C')");
	// $sql="update treatment set fee_unit=0";  //先將計價單位改成計顆
	// $conn->exec($sql);
	
	//  $sql="update treatment t,nhicode n 
	// 		set t.nhi_fee=n.fee,t.category=n.category,fee_unit=feeunit,
	// 		    t.tr_od=n.tr_od,t.tr_endo=n.tr_endo,t.tr_peri=n.tr_peri,
	// 		    t.tr_os=n.tr_os,t.tr_ospath=n.tr_ospath,t.tr_pedo=n.tr_pedo
	//  where t.nhicode=n.nhicode";

	// $conn->exec($sql);

	// //處理 科別
	// $conn->exec("update treatment set is_oper=0,is_endo=0,is_oral=0,is_peri=0,is_xray=0,is_pedo=0;");
	// $conn->exec("update treatment set is_oper=1 where nhicode like '89%' and nhicode!='89' ");
	// $conn->exec("update treatment set is_endo=1 where nhicode like '90%' ");
	// $conn->exec("update treatment set is_oral=1 where nhicode like '92%' ");
	// $conn->exec("update treatment set is_peri=1 where nhicode like '91%' ");
	// $conn->exec("update treatment set is_xray=1 where nhicode like '34%' ");
	// $conn->exec("update treatment set is_pedo=1 where nhicode between '81' and '92229B' ");
	// $conn->exec("update treatment set is_pedo=0 where nhicode in ('89006C','89007C','89013C','89113C','90001C','90002C','90003C','90019C','90020C','90017C','92096C','91021C','91022C','91023C') ");

	echo "<h1>新增處置資料完成</h1>";
?>





