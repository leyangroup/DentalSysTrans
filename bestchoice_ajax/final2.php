<?php
    include_once "../include/db.php";

    header("content-Type:text/html;charset=utf-8");
    
    $mariaConn=MariaDBConnect();

    //填入療程卡號與療程開始日
    echo "<br> 插入 療程開始日";
    $sql="drop table if exists ABstart";
    $mariaConn->exec($sql); 

    $mariaConn->exec("create table ABstart(tsn int,tregsn int,rregsn int,icseqno varchar(4),startdt varchar(10))"); 
    $mariaConn->exec("ALTER TABLE ABstart ADD INDEX(tsn)");

    $sql="insert into ABstart(tsn,tregsn,rregsn,icseqno,startdt)
            select t.treatsn,t.regsn,r.regsn,substr(r.ic_seqno,-4),r.ddate
         from treat_record t ,registration r
        where t.uploadd=r.uploadd
          and t.uploadd!=''
          and t.ddate!=r.ddate
          and r.ic_type!='AB'";
    echo $sql."<br>";
    $mariaConn->exec($sql); 

    $sql="update treat_record t,abstart a 
             set t.start_date=a.startdt,start_icseq=icseqno
           where t.treatsn=a.tsn";
    echo $sql."<br>";
    $mariaConn->exec($sql); 
    echo "<h1>AB療程 資料整理 完成</h1>";

    
?>