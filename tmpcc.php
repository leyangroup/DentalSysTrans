<?php
    include_once "include/db.php";

    header("content-Type:text/html;charset=utf-8");
    $serverName="192.168.1.20\bestchoice";
    $connectionInfo=array("Database"=>"Doctor","UID"=>'bestchoice',"PWD"=>"0937093374","CharacterSet"=>"UTF-8");
    $msConn=sqlsrv_connect($serverName,$connectionInfo);
    if ($msConn===false){
        die(print_r(sqlsrv_errors(),true));
    }
    $mariaConn=MariaDBConnect();
    $DT=$_GET['DT'];
    
    //轉入的ab療程的診察費和診察碼要清空

    //院所基本資料
    echo "<br>主訴轉錯欄位，重轉<br>";
        // $sql="create table tmpCC (RegNo varchar(10), MainDesc varchar(255))";
        // $mariaConn->exec($sql);
        $sql="truncate table tmpCC ";
        $mariaConn->exec($sql);
    
        $sql="select RegNo,convert(varchar(1000),MainDesc)MainDesc from Register where MainDesc is not null and MainDesc != '' ";
        $RegCC=sqlsrv_query($msConn,$sql) or die("sql error:".sqlsrv_errors());
        while ($row=sqlsrv_fetch_array($RegCC)){
            $regno=$row['RegNo'];
            $cc=$row['MainDesc'];
            $insertSQL="insert into tmpCC(RegNo,MainDesc) values
                    ('$regno','$cc')";
            echo $insertSQL."<br>";
            $mariaConn->exec($insertSQL);
        }
    
    
    
    echo "<Br><br>。。。。。。。 主訴 補完 。。。。。。。";
?>