<?php
	include_once "../include/db.php";

    header("content-Type:text/html;charset=utf-8");
    $ip=$_GET['IP'];
    $serverName=$ip."\bestchoice";
    $connectionInfo=array("Database"=>"doctor","UID"=>'bestchoice',"PWD"=>"0937093374","CharacterSet"=>"UTF-8");
    $msConn=sqlsrv_connect($serverName,$connectionInfo);
    if ($msConn===false){
        die(print_r(sqlsrv_errors(),true));
    }
    $mariaConn=MariaDBConnect();
    $DT=$_GET['DT'];
    
	//院所基本資料
    echo "<br>轉入院所資料 basicset<br>";
        $sql="select * from Clinic ";
        $users=sqlsrv_query($msConn,$sql) or die("sql error:".sqlsrv_errors());
        while ($row=sqlsrv_fetch_array($users)){
            $clinicno=$row['ClinicCode'];
            $clinicname=$row['ClinicName'];
            $owner=$row['Owner'];
            $zip=$row['Zip1'];
            $addr=$row['Addr1'];
            $tel=$row['Tel1'];
            $fax=$row['Fax'];
            $updateSQL="update basicset set bsname='$clinicname',bstel='$tel',bsfax='$fax',bsaddr='$addr',owner='$owner',zip='$zip',nhicode='$clinicno' ";
            echo $updateSQL;
            $mariaConn->exec($updateSQL);
        }
    
    echo "<br>轉入使用者資料 staff<br>"; 
        $sql="truncate table staff";
        $mariaConn->exec($sql);
    
        $sql="select * from Users order by UserNo";
        $users=sqlsrv_query($msConn,$sql) or die("sql error:".sqlsrv_errors());
        while ($row=sqlsrv_fetch_array($users)){
            $userno=$row['UserNo'];
            $username=$row['UserName'];
            $userid=$row['ID'];
            echo "user:".$row['UserNo'].'  -   '.$row['UserName'].'  -  '.$row['ID'].' ';
            $position=substr($row['UserNo'],0,1);
            $insertSQL="insert into staff(sfno,sfname,sfid,position) 
            values('$userno','$username','$userid','$position')";
            $mariaConn->exec($insertSQL);
        }
        $staff=[];
        $sql="select sfsn,sfno from staff order by sfsn";
        $result=$mariaConn->query($sql);
        foreach ($result as $key => $value) {
            $staff[$value['sfno']]=$value['sfsn'];
        }
    sqlsrv_close($msConn);
    echo "<h1>基本資料 轉入完畢</h1>";

?>