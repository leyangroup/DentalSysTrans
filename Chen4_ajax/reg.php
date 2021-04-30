<?php
	include_once "../include/db.php";

    $conn=MariaDBConnect();

    $path=$_GET['path'].'\dent01_10.mdb';

	//新增醫師
 	set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Access Driver (*.mdb)};DBQ=".$path);
	$sql = "SELECT * FROM ththick";
	$result = $db->query($sql);
	$conn->exec("truncate table registration");
	$conn->exec("truncate table treat_record");
	$dr=$conn->query("select id,no from leconfig.zhi_staff");
	$drArray=[];
	foreach ($dr as $key => $value) {
		$no=$value['no'];
		$drArray[$no]=$value['id'];
	}

	foreach ($result as $key => $value) {
		$cusid=$value['idno'];
		if (strlen($value['date'])<7){
			$regDT='';
		}else{
			$yy=substr($value['date'],0,3);
			$WestYY=$yy+1911;
			$mm=substr($value['date'],3,2);
			$dd=substr($value['date'],-2);
			$retDT=$yy.'-'.$mm.'-'.$dd;
		}
		if(strlen($value['ser'])==8){
			$ic_type=substr($value['ser'],2,2);
			$icseqno=$yy.substr($value['ser'],4,4);
		}else{
			$ic_type='02';
			$icseqno=$yy.$value['ser'];
		}
		$nhi_status=$value['part'];
		$category=$value['spec1'];
		if ($value['code1']!=''){
			
		}

		$cusno=addslashes(mb_convert_encoding($value['sino'],"UTF-8","BIG5"));
		
		$reg_sql="insert into registration()
				value()";

		$ok=$conn->exec($reg_sql);
		if ($ok==0){
			echo "新增掛號資料失敗 $sql";
		}
	}
	echo "<h1>新增掛號-處置結束</h1>";
	
?>





