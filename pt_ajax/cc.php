<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);

	$conn->exec("drop table if exists CC");
	$conn->exec("CREATE TABLE `cc` (
				  `id` varchar(6) NOT NULL,
				  `maintell` text DEFAULT NULL
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='cc';
			");
	$conn->exec("ALTER TABLE `cc` ADD PRIMARY KEY (`id`)");
	
	//患者基本資料
	$sql = "SELECT * FROM maintell.dat order by keywords";
	$result=$db->query($sql);
	$KW='';
	$cc='';
	foreach ($result as $key => $value) {
		if ($KW!=$value['keywords']){
			if ($KW==''){
				$KW=$value['keywords'];
			}else{
			    $sql="insert into cc(id,maintell) values('$KW','$cc')";
			    echo "$sql<br>";
				$conn->exec($sql);
				$KW=$value['keywords'];
				$cc='';
			}
		}else{
			$KW=$value['keywords'];
		}
		$maintell=str_replace("<0x19>", "", $value['maintell']);
		$cc=$cc.trim(mb_convert_encoding(addslashes($maintell),"UTF-8","BIG5"));;
	}

    $sql="insert into cc(id,maintell) values('$KW','$cc')";
    echo "$sql<br>";
	$conn->exec($sql);
	echo "<h1>主訴資料 轉換完成</h1>";
?>

