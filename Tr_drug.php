<?php
	
	include_once "include/db.php";

    header("content-Type:text/html;charset=utf-8");
    $serverName="192.168.1.20\bestchoice";
    $connectionInfo=array("Database"=>"Doctor","UID"=>'bestchoice',"PWD"=>"0937093374","CharacterSet"=>"UTF-8");
    $msConn=sqlsrv_connect($serverName,$connectionInfo);

    $mariaConn=MariaDBConnect();

    echo "轉入藥品組合<br>";
    $sql="truncate table drugcom";
    $mariaConn->exec($sql);

    $sql="truncate table drugcomdetails";
    $mariaConn->exec($sql);

    $sql="select DCID,DCName,Rxday from DrugCompose";
    $result=sqlsrv_query($msConn,$sql) or die("sql error:".sqlsrv_errors());
    while ($row=sqlsrv_fetch_array($result)) {
        $id=$row['DCID'];
        $na=$row['DCName'];
        $rxday=$row['Rxday'];
        $insertSQL="INSERT into drugcom(comno,name)
                    values('$id','$na')";
        $mariaConn->exec($insertSQL);                        
    }

    $sql="select DCID,DCFname,Freq,Rxday from DrugComposeFile";
    $result=sqlsrv_query($msConn,$sql) or die("sql error:".sqlsrv_errors());
    while ($row=sqlsrv_fetch_array($result)) {
        $comdno=$row['DCID'];
        $drugno=$row['DCFname'];
        $freq=$row['Freq'];
        $rxday=$row['Rxday'];
        $insertSQL="INSERT into drugcomdetails(comDno,drugno,dose,`times`,day)
              values('$comdno','$drugno','01','$freq',$rxday)";
              
        $mariaConn->exec($insertSQL);                        

    }

?>