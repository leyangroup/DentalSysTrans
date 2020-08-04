<?php
	include_once "../include/db.php";

    header("content-Type:text/html;charset=utf-8");
    
    $serverName="192.168.11.10\bestchoice";
    $connectionInfo=array("Database"=>"Doctor","UID"=>'bestchoice',"PWD"=>"0937093374","CharacterSet"=>"UTF-8");
    $msConn=sqlsrv_connect($serverName,$connectionInfo);
    if ($msConn===false){
        die(print_r(sqlsrv_errors(),true));
    }
    $mariaConn=MariaDBConnect();
    $DT=$_GET['DT'];

    $mariaConn->exec("create table tmpcus (cno varchar(10),cname varchar(50),cid varchar(10),tel varchar(20),mobile varchar(20) )");

    echo "<br>轉入患者電話  customer <br>";

        $sql="SELECT * from Patients order by PatNo";
        $result=sqlsrv_query($msConn,$sql) or die("sql error:".sqlsrv_errors());
        while ($row=sqlsrv_fetch_array($result)) {
            $cusno=$row['PatNo'];
            $cusname=$row['PatName'];
            $id=$row['ID'];
            $tel=$row['TelH'];
            $mobile=$row['Moble1'];
            
            $insertSQL="INSERT into tmpcus values('$cusno','$cusname','$id','$tel','$mobile')";
            echo $insertSQL;
            $mariaConn->exec($insertSQL);
        }
    sqlsrv_close($msConn);
    echo "<h3>患者電話 資料轉完 </h3>";

?>
