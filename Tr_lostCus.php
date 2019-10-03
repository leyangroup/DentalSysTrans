<?php

	include_once "include/db.php";

    header("content-Type:text/html;charset=utf-8");
    header("content-Type:text/html;charset=utf-8");
    $serverName="192.168.1.20\bestchoice";
    $connectionInfo=array("Database"=>"Doctor","UID"=>'bestchoice',"PWD"=>"0937093374","CharacterSet"=>"UTF-8");
    $msConn=sqlsrv_connect($serverName,$connectionInfo);

    $mariaConn=MariaDBConnect();

    $sql="select distinct cusno from registration where cussn is null";
    $reg=$mariaConn->query($sql);
    $cusStr='';
    foreach ($reg as $key => $value) {
    	if ($cusStr==''){
    		$cusStr="'".$value['cusno']."'";
    	}else{
    		$cusStr.=",'".$value['cusno']."'";
    	}
    }

    $staff=[];
    $sql="select sfsn,sfno from staff order by sfsn";
    $result=$mariaConn->query($sql);
    foreach ($result as $key => $value) {
        $staff[$value['sfno']]=$value['sfsn'];
    }
	
	$sql="SELECT *,convert(varchar(1000),notes)as Note,convert(varchar(1000),notesEX)as NEX,convert(varchar(1000),othernote)as ONote,
                  convert(varchar,Birth,120) birthday,convert(varchar,FirstDate,120) FD,convert(varchar,LastDate,120) LD
            from Patients
           where PatNo in ($cusStr) 
           order by PatNo";
         echo "<br>".$sql;
    $result=sqlsrv_query($msConn,$sql) or die("sql error:".sqlsrv_errors());

    while ($row=sqlsrv_fetch_array($result)) {
        $cusno=$row['PatNo'];
        $cusname=$row['PatName'];
        $id=$row['ID'];
        if ($row['Sex']=='女'){
            $sex=0;
        }else{
            $sex=1;
        }
        $birth=$row['birthday'];
        $tel=$row['TelH'];
        $mobile=$row['Moble1'];
        $email=$row['Email'];
        $zip=$row['Zip1'];
        $addr=$row['Addr1'];
        $job=$row['Job'];
        $intro=$row['Introducer'];
        $lv=$row['Vip'];
        $firstdate=$row['FD'];;
            $lastdate=$row['LD'];
        $fdr=$staff[$row['FirstDoc']];
        $ldr=$staff[$row['LastDoc']];
        $sp=$row['sp'];
    
        $memo='';
        if ($row['DrugAllergy']!=''){
            $memo='過敏藥物：'.$row['DrugAllergy']."char(13)";
        }
        if ($row['Question']!=''){
            $memo=$memo.'系統疾病：'.$row['Question']."char(13)";
        }
        if ($row['Note']!=''){
            $memo=$memo.$row['Note']."char(13)";
        }
    
        if ($row['NEX']!=''){
            $memo=$memo.$row['NEX']."char(13)";
        }
    
        if ($row['ONote']!=''){
            $memo=$memo.$row['ONote']."char(13)";
        }

        $memo=str_replace("'", "\'", $memo);
        // $memo=str_replace("char(13)", char(10), $memo);
    
        $insertSQL="INSERT into customer (cusno,cusname,cusid,cussex,cusbirthday,custel,cusmob,cusemail,cuszip,cusaddr,cusjob,cusintro,cuslv,firstdate,lastdate,maindrno,lastdrno,barrier,cusmemo)
        values('$cusno','$cusname','$id','$sex','$birth','$tel','$mobile','$email','$zip','$addr','$job',
        '$intro','$lv','$firstdate','$lastdate','$fdr','$ldr','$sp','$memo');";
        echo "<br>".$insertSQL;
        // $mariaConn->exec($insertSQL);
        // $mariaConn->exec($sql);

    }
    // $sql="update registration r,customer c set r.cussn=c.cussn where r.cusno=c.cusno and r.cussn is null";
    // echo "<br>".$sql;
    
    
?>