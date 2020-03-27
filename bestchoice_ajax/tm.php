<?php
    include_once "include/db.php";

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
    
    echo "<br>轉入 treatment <br>";
        $sql="truncate table treatment ";
        $mariaConn->exec($sql);
        $sql="select * from DealCode where enddate is null";
        $result=sqlsrv_query($msConn,$sql) or die("sql error:".sqlsrv_errors());  
        while ($row=sqlsrv_fetch_array($result)){
            $trcode=$row['DealSimCode'];
            $nhicode=$row['DealCode'];
            $treatname=$row['DealName'];
            $fee=$row['DealAmt'];
            $memo=str_replace('<','＜',$row['Desc']);
            $memo=str_replace('>', '＞', $memo);
            $memo=str_replace("'", '’', $memo);
            $sick=$row['SickNo'];
            $treatno=$row['Sp'];
            $icd10=$row['Sick10No'];
            $insertSQL="INSERT INTO treatment(trcode,nhicode,treatname,nhi_fee,treatno,sickno,icd10cm,memo)
                        values('$trcode','$nhicode','$treatname',$fee,'$treatno','$sick','$icd10','$memo')";
            echo $insertSQL."<br>";
            $mariaConn->exec($insertSQL);
        }

    sqlsrv_close($msConn);
    echo "<h1>支付標準轉換完成</h1>";
?>