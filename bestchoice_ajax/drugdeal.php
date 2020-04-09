ebi<?php
    include_once "../include/db.php";

    header("content-Type:text/html;charset=utf-8");
   
    $mariaConn=MariaDBConnect();
    $DT=$_GET['DT'];
    
    //森源藥品處理
    $sql="delete from drug where drugno in ('43','44','11','45','A03','29','A01','32','31','37','39','40','10','42','05','06','A02','36','09','24','33','34')";
    $mariaConn->exec($sql); 

    $sql="update prescription p,drug d 
        set p.drugno=d.drugno
        where p.nhidrugno=d.nhicode
        and p.drugno!=d.drugno";
    $mariaConn->exec($sql); 


    echo "<h1>資料整理 完成</h1>";
?>