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

    echo "轉入處方箋資料 prescription<br>";
        $sql="truncate table prescription ";
        $mariaConn->exec($sql);

        $sql="select * from drug_dose";
        $dose=[];
        $drugdose=$mariaConn->query($sql);
        foreach ($drugdose as $key => $col) {
            $dose[$col['qty']]=$col['doseno'];
        }

        $sql="SELECT RegNo,DrugID,DrugCode,DrugName,rxday,Qty,Freq,way,Qtotal,DrugAmt,TreatNo
                from Drug 
                where DeleteDate is null
                  and enable='1'";
        $result=sqlsrv_query($msConn,$sql) or die("sql error:".sqlsrv_errors());
        while ($row=sqlsrv_fetch_array($result)) {
            $regno=$row['RegNo'];
            $treatno=$row['TreatNo'];
            $drugno=$row['DrugCode'];
            $nhicode=$row['DrugID'];
            $day=$row['rxday'];
            $qty=$row['Qty']*$day;
            $dose=$dose[$row['qty']];  //這裡是一次的數量，樂晴是一天的量，要對應至drug_dose
            $freq=$row['Freq'];
            $part=$row['way'];
            $totalQ=$row['Qtotal'];
            $amount=$row['DrugAmt'];
            $insertSQL="INSERT into prescription(regsn,drugno,nhidrugno,qty,totalQ,dose,part,`times`,amount,day,icuploadd,uploadd)
                  values
                  (0,'$drugno','$nhicode',$qty,$totalQ,'$dose','$part','$freq',$amount,$day,'$regno','$treatno')";
            echo $regno."--".$nhicode."  ";
            $mariaConn->exec($insertSQL);
        }       

    sqlsrv_close($msConn);
    echo "<h1>轉入處方箋資料 完畢</h1>";

?>