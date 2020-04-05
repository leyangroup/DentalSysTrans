<?php
    include_once "../include/db.php";

    header("content-Type:text/html;charset=utf-8");
    // $ip=$_GET['IP'];
    // $serverName=$ip."\bestchoice";
    // $connectionInfo=array("Database"=>"Doctor","UID"=>'bestchoice',"PWD"=>"0937093374","CharacterSet"=>"UTF-8");
    // $msConn=sqlsrv_connect($serverName,$connectionInfo);
    // if ($msConn===false){
    //     die(print_r(sqlsrv_errors(),true));
    // }
    $mariaConn=MariaDBConnect();
    $DT=$_GET['DT'];
    
   //配對患者編號
    echo "<br>1.配對患者編號<br>";
    $sql="update `registration` r,customer c set r.cussn=c.cussn where r.cusno=c.cusno";
    $mariaConn->exec($sql);  

    echo "<br>2.設定診別";
    $sql="update `registration` r,section s set r.section=s.sno where reg_time between s.start and s.end";
    $mariaConn->exec($sql);  

    echo "<br>3.串掛號與處置";
    $sql="update registration r, treat_record t 
            set t.regsn=r.regsn,t.seqno=r.seqno ,t.cussn=r.cussn
            where r.icuploadD=t.icuploadD and r.cussn is not null";
    $mariaConn->exec($sql);  

    echo "<br>4.串掛號與處方箋";
    $sql="update registration r,prescription p 
            set p.regsn=r.regsn,p.ddate=r.ddate,p.seqno=r.seqno 
            where r.icuploadd=p.icuploadd";
    $mariaConn->exec($sql);  

    echo "<br>5.預約患者的電話轉入";
    $sql="update registration r,customer c
             set r.schtel=c.custel,r.schmobile=c.cusmob
           where r.cussn=c.cussn
             and r.seqno='000'";
    $mariaConn->exec($sql);  

    echo "<br>6.處方箋整理";
    $sql="update prescription set dose='01'";
    $mariaConn->exec($sql);  

    $sql="update prescription p,drug_times t 
            set p.qty=t.qty
            where p.times=t.timesno";
    $mariaConn->exec($sql);  

    $sql="update registration set rx_type='1' where regsn in (select distinct regsn from prescription)";
    $mariaConn->exec($sql);  

    $sql="update registration set trcode='',trpay=0,amount=nhi_tamt+nhi_damt,giaamt=nhi_tamt+nhi_damt where ic_type='AB'";
    $mariaConn->exec($sql);  

    $sql="update registration r,(select regsn,max(day)maxday from prescription group by 1)a
                set r.rx_day=a.maxday
           where r.regsn=a.regsn";
    $mariaConn->exec($sql); 

    echo '<br>7.整理 check & finding';
    $sql="update registration r,treat_record t  
             set r.check_finding=t.treat_memo
           where r.trcode='01271C' 
             and r.regsn=t.regsn
             and t.trcode='01271'";
    $mariaConn->exec($sql); 

    $sql="update treat_record set deldate='1911-01-01' where trcode like '0127%'";
    $mariaConn->exec($sql); 

    echo "<br> 9.修正轉入的treatment的一些屬性";
    $sql="update treatment t,nhicode n 
            set t.is_oper=n.is_oper,t.is_endo=n.is_endo,t.is_oral=n.is_oral,t.is_peri=n.is_peri,t.is_xray=n.is_xray,t.is_pedo=n.is_pedo,
                t.tr_od=n.tr_od,t.tr_endo=n.tr_endo,t.tr_peri=n.tr_peri,t.tr_os=n.tr_os,t.tr_pedo=n.tr_pedo,
                t.fee_unit=n.feeunit,t.category=n.category
            where t.nhicode=n.nhicode ";
    $mariaConn->exec($sql); 

    //修正處置說明
    echo "<br> 10.修正處置說明";
    $sql="update treat_record 
            set treat_memo=replace(treat_memo,'【$牙位$】',concat('[',fdi,']')) 
          where deldate is null and treat_memo like '%【$牙位$】%'";
    $mariaConn->exec($sql); 

    $sql="update treat_record 
            set treat_memo=replace(treat_memo,'【$齒面$】',concat('[',side,']')) 
          where deldate is null and treat_memo like '%【$齒面$】%'";
    $mariaConn->exec($sql);

     //上線後發現的問題修正 1080517先轉完壹捌捌之後，再來看這些資料的內容，確定要處理 再把下方程式碼打開
    //hosp_from若=空白 ，要給null，prescription.單價 要有值=0,registration.trcode如果是空白 ，要填null
    echo "<br>11.hosp_from若=空白 處理";
    $sql="update registration set hosp_from=null WHERE hosp_from ='' ";
    $mariaConn->exec($sql); 

    // $sql="update prescription p,drug d set p.uprice=nhifee where p.nhidrugno=d.nhicode and uprice!=d.nhifee";
    // $mariaConn->exec($sql); 
    echo "<br>12.處理 01271";
    $sql="update registration set trcode=null WHERE trcode ='' ";
    $mariaConn->exec($sql); 

    $sql="update treatment t,examfee e set e.memo=t.memo WHERE nhicode=code";
    $mariaConn->exec($sql); 

    $sql="delete from treatment where nhicode in ('01271C','01272C','01273C')";
    $mariaConn->exec($sql); 
    
    $sql="update customer set cusmob=custel where cusmob='' ";
    $mariaConn->exec($sql); 

    $sql="update drug set dose='01' where dose='' ";
    $mariaConn->exec($sql); 

 //轉換親友，看資料是依地址來歸親友，所以目前將地址一樣的先用同一個群組
    echo "<br>13.轉換親友，看資料是依地址來歸親友，所以目前將地址一樣的先用同一個群組";
    $sql="CREATE TABLE `tmpfamily` (
            `sn` int(11) NOT NULL,
            `addr` varchar(100) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='暫存family群組'";
    $mariaConn->exec($sql); 

    $sql="ALTER TABLE `tmpFamily` ADD PRIMARY KEY (`sn`)";
    $mariaConn->exec($sql); 

    $sql="ALTER TABLE `tmpfamily` CHANGE `sn` `sn` INT(11) NOT NULL AUTO_INCREMENT";
    $mariaConn->exec($sql); 

    $sql="insert into tmpfamily(addr) select cusaddr from customer where cusaddr !='' group by cusaddr having count(*)>=2";
    $mariaConn->exec($sql); 

    $sql="insert into family_groups(id,created_at,updated_at) select sn,now(),now() from tmpfamily";
    $mariaConn->exec($sql); 

    $sql="update customer c,tmpfamily f set c.family_group_id=f.sn where cusaddr=addr";
    $mariaConn->exec($sql); 

    $mariaConn->exec("update treatment set nhicode='RCT00' where nhicode='RCT' ");
   
    echo "<h1>資料整理 完成</h1>";
?>