<?php
	include_once "../include/db.php";
    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);

	$sql="select sfsn,sfno from staff ";
	$RS=$conn->query($sql);
	foreach ($RS as $key => $col) {
		$drArr[$col['sfno']]=$col['sfsn'];
	}

	$conn->exec("delete from registration where seqno='000' ");
	//預約
	$trDT=$_GET['DT'];
	$westy=substr($trDT,0,4)-1911;
	$westYY=$westy.'0101';
	$sql = "SELECT * FROM pregist  ";
	$result=$db->query($sql);
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		foreach ($row as $k2=> $v2) {
			switch (mb_convert_encoding($k2,"UTF-8","BIG5")) {
				case '預約日期':
					$westy=substr($v2,0,3)+1911;
					$westDT=$westy.'-'.substr($v2,3,2).'-'.substr($v2,-2);
					$schDT=$westDT;
					break;
				case '預約時間';
					$schtime=substr($v2,0,2).":".substr($v2,-2);
					break;
				case '預估需時';
					$schlen=$v2;
					break;
				case '醫師代號';
					$drsn=$drArr[$v2];
					break;
				case '病歷編號';
					$cusno=$v2;
					break;
				case '備註';
					$v2=str_replace("'","\'",$v2);
					$memo=trim(mb_convert_encoding($v2,"UTF-8","BIG5"));
					break;
				// case '結案別':
				// 	$apstate='';
				// 	$delDT=NULL;
				// 	$kind=trim(mb_convert_encoding($v2,"UTF-8","BIG5"));
				// 	if ($kind=='取消'){
				// 		$apstate='4';
				// 		$delDT='$trDT';
				// 	}elseif($kind=='爽約'){
				// 		$apstate='5';
				// 		$delDT='$trDT';
				// 	}
				// 	break;
				case '簡訊備註':
					$v2=str_replace("'","\'",$v2);
					$smsnote=trim(mb_convert_encoding($v2,"UTF-8","BIG5"));
					break;
			}
		}
		if ($kind!='已到' && $schDT>=$trDT){
			$sql="insert into registration(seqno,ddate,cusno,sch_time,sch_note,schlen,drno1,drno2,noticemomo,roomsn)values
					('000','$schDT','$cusno','$schtime','$memo',$schlen,$drsn,$drsn,'$smsnote',1)";
			// echo $cusno."-".$schDT.",";
			echo $sql."<br>";
			$conn->exec($sql);
		}
	}
	//預約電話與手機對應
	$sql="update registration r,customer c set r.schtel=c.custel,r.schmobile=c.cusmob,r.cussn=c.cussn where r.cusno=c.cusno and r.seqno='000'";
	$conn->exec($sql);

	$sql="truncate table delappom";
	$conn->exec($sql);

	$sql="insert into delappom(regsn,ddate,cussn,cusno,drno1,userid,sch_time,schlen,sch_note,schqty,muid,schtel,chgdate,chgtype,
				colorsn)
		  select regsn,ddate,cussn,cusno,drno1,1,sch_time,schlen,trim(sch_note),1,1,schtel,left(now(),16),'a',0
		    from registration
		   where seqno='000'
		     and ddate>='$trDT'";
	echo $sql;

	$conn->exec($sql);
	
	echo "<h1>預約轉換完成";
?>