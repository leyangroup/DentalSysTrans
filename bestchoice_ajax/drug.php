<?php
    include_once "../include/db.php";

    header("content-Type:text/html;charset=utf-8");
    $ip=$_GET['IP'];
    $serverName=$ip."\bestchoice";
    $connectionInfo=array("Database"=>"Doctor","UID"=>'bestchoice',"PWD"=>"0937093374","CharacterSet"=>"UTF-8");
    $msConn=sqlsrv_connect($serverName,$connectionInfo);
    if ($msConn===false){
        die(print_r(sqlsrv_errors(),true));
    }
    $mariaConn=MariaDBConnect();
    $DT=$_GET['DT'];

    echo "轉入藥品代碼<br>";
        $sql="truncate table drug";
        $mariaConn->exec($sql);

        $sql="select * from drug_dose";
        $dose=[];
        $drugdose=$mariaConn->query($sql);
        foreach ($drugdose as $key => $col) {
            $dose[$col['qty']]=$col['doseno'];
        }

        $sql="select DrugID,DrugCode,DrugName,Price,Qty,Freq,way from DrugCode";
        $result=sqlsrv_query($msConn,$sql) or die("sql error:".sqlsrv_errors());
        while ($row=sqlsrv_fetch_array($result)) {
            $drugno=$row['DrugCode'];
            $nhicode=$row['DrugID'];
            $dname=$row['DrugName'];
            $nhifee=$row['Price'];
            $dose=$dose[$row['Qty']];
            $freq=$row['Freq'];
            $part=$row['way'];
            $insertSQL="INSERT into drug(drugno,name,nhicode,nhifee,dose,part,`times`)
                        values
                        ('$drugno','$dname','$nhicode',$nhifee,'$dose','$part','$freq')";
            echo $drugno."-".$nhicode."  ";
            $mariaConn->exec($insertSQL);            
        }

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
                  echo $insertSQL;
            $mariaConn->exec($insertSQL);                        
        }


    sqlsrv_close($msConn);
    echo "<h1>藥品資料轉入 完畢</h1>";
?>