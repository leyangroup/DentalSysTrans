<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);
	
	//
	$conn->exec("truncate table lein.leyan_cuspics");
	$conn->exec("truncate table lein.trans_cuspics");

	$sql="SELECT p.Ddate,p.pat_no,p.vol,p.Fname,c.id from pathpict as p left join patient as c on p.pat_no=c.pat_no";
	$result=$db->query($sql);
	$r=0;
	foreach ($result as $key => $value) {
		$DT=WestDT($value['ddate']);
		$cusno=trim($value['pat_no']);
		$old_vol=addslashes($value['vol']);
		$old_file=trim(mb_convert_encoding($value['fname'],"UTF-8","BIG5"));
		$fname=explode("_",$old_file);
		$cusid=$value['id'];
		$new_folder="image";
		$new_file=trim($fname[1].$fname[2].$fname[3].str_replace('.jpg', '', $fname[4]).'_'.$cusno.'.jpg');
		$new_path=$cusid."\\\\".$new_folder."\\\\".$fname[1].$fname[2].$fname[3].str_replace('.jpg', '', $fname[4]).'_'.$cusno.'.jpg';
		
		$sql="insert into lein.leyan_cuspics(ddate,cussn,cusno,folder,fname,fcover)values('$DT',0,'$cusno','image','$new_file','$new_file')";
		$ok=$conn->exec($sql);
		if ($ok==0){
			echo "新增至leyan_cuspics失敗：$sql<br>";
		}

		$sql="INSERT into lein.trans_cuspics(DT,O_Vol,O_Fname,Patno,N_Fname,cussn,cusid) 
				values('$DT','$old_vol','$old_file','$cusno','$new_path',0,'$cusid')";
		$conn->exec($sql);
		if ($ok==0){
			echo "新增至trans_cuspics失敗：$sql<br>";
		}
	}
	$sql="update lein.leyan_cuspics a,eprodb.customer b
			set a.cussn=b.cussn
	       where a.cusno=b.cusno";		
	$conn->exec($sql);

	$sql="update lein.trans_cuspics a,eprodb.customer b
			set a.cussn=b.cussn
	       where a.patno=b.cusno";		
	$conn->exec($sql);

	echo "圖檔資料 轉換完成";

?>