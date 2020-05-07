<?php
	include_once "../include/db.php";
    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);

	//處置

	//建立temp 存患者與身份
	$conn->exec("drop table if exists cooperStatustmp");
	$sql="create table cooperStatustmp(cno varchar(10),times int(3),status varchar(3), partpay int(3) ) ";
	$conn->exec($sql);
	$conn->exec("ALTER TABLE `cooperstatustmp` ADD PRIMARY KEY( `cno`, `times`) ");

	$sql = "SELECT * FROM operate";
	$result=$db->query($sql);
	$r=0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$r++;
		foreach ($row as $k2=> $v2) {
			switch (mb_convert_encoding($k2,"UTF-8","BIG5")) {
				case '病歷編號':
					$cusno=$v2;
					break;
				case '就診次數':
					$cnt=$v2;
					break;
				case '部份負擔碼':
					switch ($v2) {
						case '1':
							$nhistatus='H10';
							break;
						case '2':
							$nhistatus='004';
							break;
						case '3':
							$nhistatus='003';
							break;
						case '4':
							$nhistatus='009';
							break;
						case '5':
							$nhistatus='H13'; //殘障手冊
							break;
						case '6':
							$nhistatus='001';
							break;
						case '7':
							$nhistatus='006';//職災
							break;
						case '8':
							$nhistatus='005';//結核病患
							break;
						case '9':
							$nhistatus='009';//災民
							break;
						case '10':
							$nhistatus='902';//三歲以下小孩
							break;
						case '11':
							$nhistatus='007';//山地醫療
							break;
						case '12':
							$nhistatus='901';//多氯聯本
							break;
						case '13':
							$nhistatus='903';//新生兒依附
							break;
						case '14':
							$nhistatus='906';//替代疫男
							break;
						case '15':
							$nhistatus='007';//平地巡迴免部份負擔
							break;
						case '16':
							$nhistatus='907';//原住民戒菸
							break;
						case '17':
							$nhistatus='009';//百歲人瑞
							break;
					}
					break;
				case '自付金額':
					$partpay=$v2;
					break;
				
			}
		}
		if ($nhistatus!='H10'){
			$insertsql="insert into cooperstatustmp(cno,times,status,partpay) value('$cusno',$cnt,'$nhistatus',$partpay )";
			echo "$r.$cusno<br> ";
			$conn->exec($insertsql);
		}

	}
	

	echo "<h1>完成";

?>