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
    echo "<br>轉入院所資料 basicset<br>";
        $sql="truncate table basicset";
        $mariaConn->exec($sql);
    
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
            $insertSQL="insert into basicset(bsname,bstel,bsfax,bsaddr,owner,zip,nhicode) values
                    ('$clinicname','$tel','$fax','$addr','$owner','$zip','$clinicno')";
					echo $insertSQL;
            $mariaConn->exec($insertSQL);
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
    
    echo "<br>轉入患者  customer <br>";
        $sql="truncate table customer ";
        $mariaConn->exec($sql);

        $sql="SELECT *,convert(varchar(1000),notes)as Note,convert(varchar(1000),notesEX)as NEX,convert(varchar(1000),othernote)as ONote,
                convert(varchar,Birth,120) birthday,convert(varchar,FirstDate,120) FD,convert(varchar,LastDate,120) LD
            from Patients 
            order by PatNo";
        $result=sqlsrv_query($msConn,$sql) or die("sql error:".sqlsrv_errors());
    //display
        while ($row=sqlsrv_fetch_array($result)) {
            $cusno=$row['PatNo'];
            $cusname=$row['PatName'];
            $id=$row['ID'];
            if ($row['Sex']=='女'){
                $sex=0;
            }else{
                $sex=1;
            }
            $birth=$row['birthday'];
            $tel=$row['TelH'];
            $mobile=$row['Moble1'];
            $email=$row['Email'];
            $zip=$row['Zip1'];
            $addr=str_replace("'","’",$row['Addr1']);
            $job=$row['Job'];
            $intro=str_replace("'","’",$row['Introducer']);
            $lv=$row['Vip'];
            if ($row['FD']==null or $row['FD']=''){
                $firstdate='';
            }else{
                $firstdate=$row['FD'];
            }

            if ($row['LD']==null or $row['LD']=''){
                $lastdate='';
            }else{
                $lastdate=$row['LD'];
            }
            
            if ($row['FirstDoc']==null || $row['FirstDoc']==''){
                if ($row['Doctor']==NULL){
                    $fdr=0;
                }else{
                    $fdr=$staff[$row['Doctor']];;
                }
            }else{
                $fdr=$staff[$row['FirstDoc']];
            }
            if ($row['LastDoc']==null || $row['LastDoc']==''){
                if ($row['Doctor']==NULL){
                    $ldr=0;
                }else{
                    $ldr=$staff[$row['Doctor']];;
                }
            }else{
                $ldr=$staff[$row['LastDoc']];
            }
            
            $sp=$row['sp'];
        
            echo "患者：".$row['PatNo']."--".$row['PatName'].' ';

            $memo='';
            if ($row['DrugAllergy']!=''){
                $memo='過敏藥物：'.$row['DrugAllergy']." ";
            }
            if ($row['Question']!=''){
                $memo=$memo.'系統疾病：'.$row['Question']." ";
            }
            if ($row['Note']!=''){
                $memo=$memo.str_replace("'", "’", $row['Note'])."char(13)";
            }
        
            if ($row['NEX']!=''){
                $memo=$memo.str_replace("'", "’", $row['NEX'])."char(13)";
            }
        
            if ($row['ONote']!=''){
                $memo=$memo.str_replace("'", "’", $row['ONote'])."char(13)";
            }
            $insertSQL="INSERT into customer (cusno,cusname,cusid,cussex,cusbirthday,custel,cusmob,cusemail,cuszip,cusaddr,cusjob,cusintro,cuslv,firstdate,lastdate,maindrno,lastdrno,barrier,cusmemo)
            values('$cusno','$cusname','$id','$sex','$birth','$tel','$mobile','$email','$zip','$addr','$job',
            '$intro','$lv','$firstdate','$lastdate','$fdr','$ldr','$sp','$memo')";
            $mariaConn->exec($insertSQL);
        }

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
    
    echo "<br>轉入掛號 registration<br>";
        $sql="truncate table registration ";
        $mariaConn->exec($sql);

        $staff=[];
        $sql="select sfsn,sfno from staff order by sfsn";
        $result=$mariaConn->query($sql);
        foreach ($result as $key => $value) {
            $staff[$value['sfno']]=$value['sfsn'];
        }

        $sql="SELECT m.TreatNo ,aa.RegNo RNO,aa.PatNo PNO,convert(char(10),RegDate,120)regdate,
                    RegTime,CardNo,BurdenNo,BurdenAmt,[status] as st,Sp16,ClinicFrom,isOut,ApplyDoctor,
                    Totcat as cate,LookCode,LookAmt,DrugSerNo,DrugSerAmt,
                    DrugSubAmt,DealSubAmt,DrugTotalAmt,DealTotalAmt,
                    totalAmt,ApplyAmt,convert(varchar(1000),aa.MainDesc) as cc 
              from (select r.*,t.TreatNo
                      from register r left join TreatRegister t
                      on r.RegNo=t.RegNo
                      where r.Enable='1'
                     )aa left join Treatment m
                        on aa.TreatNo=m.TreatNo
                        order by RegDate,RegTime";
        // register.enable=0  -->刪除的，所以只讀enable=1
        $result=sqlsrv_query($msConn,$sql) or die("sql error:".sqlsrv_errors());
        while ($row=sqlsrv_fetch_array($result)) {
            $dt=$row['regdate'];
            $seqno=substr($row['RNO'],-3);
            echo $row['RNO'].'-'.$cusno.' ';
            $regno=$row['RNO'];
            $treatno=$row['TreatNo'];
            $ddate=$row['regdate'];
            $regtime=$row['RegTime'];
            $cusno=$row['PNO'];
            $icseq=substr($regno,0,3).$row['CardNo'];
            $nhistatus=substr($row['BurdenNo'],0,3);
            $partpay=$row['BurdenAmt'];
            $ictype=$row['st'];
            $barid=$row['Sp16'];
            $hospfrom=$row['ClinicFrom'];
            $isout=($row['isOut']=='')?0:$row['isOut'];
            $dr=$staff[$row['ApplyDoctor']];
            $cate=isset($row['cate'])?$row['cate']:'';
            $trcode=isset($row['LookCode'])?trim($row['LookCode']):'';
            $trpay=isset($row['LookAmt'])?$row['LookAmt']:0;
            // $rxtype=$row['DrugS'];
            $rxtype='2';
            $rxcode=isset($row['DrogSerNo'])?$row['DrogSerNo']:'';;
            $drugsv=isset($row['DrugSerAmt'])?$row['DrugSerAmt']:0;
            $damt=isset($row['DrugSubAmt'])?$row['DrugSubAmt']:0;
            $tamt=isset($row['DealSubAmt'])?$row['DealSubAmt']:0;
            $amount=isset($row['DealTotalAmt'])?$row['DealTotalAmt']:0;
            $giaamt=$amount-$partpay;
            $cc=isset($row['cc'])?$row['cc']:'';
            $cc=str_replace("'", "’", $cc);
            $insertSQL="INSERT into registration (ddate,seqno,cusno,reg_time,drno1,drno2,ic_seqno,ic_type,category,
                        trcode,trpay,rx_type,rx_code,drugsv,nhi_status,nhi_partpay,barid,hosp_from,is_out,
                        nhi_damt,nhi_tamt,amount,giaamt,cc,icuploadd,uploadd)
                        values('$ddate','$seqno','$cusno','$regtime','$dr','$dr','$icseq','$ictype','$cate','$trcode',$trpay,'$rxtype','$rxcode',$drugsv,'$nhistatus',$partpay,'$barid','$hospfrom',$isout,$damt,$tamt,$amount,$giaamt,'$cc','$regno','$treatno')";
            echo "<br>".$insertSQL;
            $mariaConn->exec($insertSQL);
        }

    echo "<br>轉入處置資料 treat_record<br>";
        $sql="truncate table treat_record ";
        $mariaConn->exec($sql);

        $sql="SELECT c.RegNo as RegNo,d.DealSimCode as code,DealCodeName,CONVERT(CHAR(10),DealDate,120) as DealDate,
                    DealAmt,TreatNo,fdi,face,nums,convert(varchar(1500),[Desc]) as descs,sp,sickno,muliter,sick10no
                from  RegisterDeal c,Deal d 
                where d.DealNo=c.DealNo
                  and d.deleteDate is null
                  and d.enable='1'";
        $result=sqlsrv_query($msConn,$sql) or die("sql error:".sqlsrv_errors());
        while ($row=sqlsrv_fetch_array($result)) {
            if ($row['code']!=''){
                $trcode=$row['code'];
                $trname=$row['DealCodeName'];
                $ddate=$row['DealDate'];
                $punitfee=$row['DealAmt'];
                $fdi=$row['fdi'];
                $side=$row['face'];
                $nums=$row['nums'];
                $pamt=$punitfee*$nums;
                // $desc=$row['descs'];
                $desc=str_replace('>','＞',$row['descs']);
                $desc=str_replace('<','＜',$desc);
                $desc=str_replace("'",'’',$desc);
                $sickno=$row['sickno'];
                $addp=round($row['muliter']/100,2);
                $icd10=$row['sick10no'];
                $icuploadd=$row['RegNo'];
                $uploadd=$row['TreatNo'];

                $insertSQL="INSERT into treat_record (ddate,fdi,trcode,treatname,side,add_percent,nums,punitfee,pamt,icd10,sickno,treat_memo,icuploadd,uploadd)
                    values
                    ('$ddate','$fdi','$trcode','$trname','$side',$addp,$nums,$punitfee,$pamt,'$icd10','$sickno','$desc','$icuploadd','$uploadd')";
                     echo $insertSQL."<br>";
                echo $ddate."--".$trcode."  ";
                $mariaConn->exec($insertSQL);
                //再用存在uploadd中的tratno去對應register 帶出開始日與號碼, t.uploadd=r.uploadd and t.ddate!=r.ddate
            }
        }

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
            $insertSQL="INSERT into prescription(drugno,nhidrugno,qty,totalQ,dose,part,`times`,amount,day,icuploadd,uploadd)
                  values
                  ('$drugno','$nhicode',$qty,$totalQ,'$dose','$part','$freq',$amount,$day,'$regno','$treatno')";
            echo $regno."--".$nhicode."  ";
            $mariaConn->exec($insertSQL);
        }          
    
    //轉換完後要帶入患者電話與手機
    echo "轉入預約資料 order<br>";
        $staff=[];
        $sql="select sfsn,sfno from staff order by sfsn";
        $result=$mariaConn->query($sql);
        foreach ($result as $key => $value) {
            $staff[$value['sfno']]=$value['sfsn'];
        }
        $sql="delete from registration where seqno='000'";
        $mariaConn->exec($sql);

        $sql="SELECT OrderNo,PatNo,patName,DoctorNo,
                     convert(char(16),StartDate,120)StartDate,
                     convert(char(16),EndDate,120)EndDate,
                     convert(char(10),StartDate,120)sd,
                     convert(char(10),EndDate,120)ed,Notes 
                from [Order]
               where StartDate>='$DT'
                 and Enable='1' ";
               echo $sql;
        $result=sqlsrv_query($msConn,$sql) or die("sql error:".sqlsrv_errors());
        while ($row=sqlsrv_fetch_array($result)) {
            $cusno=$row['PatNo'];
            $dr=$staff[$row['DoctorNo']];
            $dt=substr($row['sd'],0,10);
            $schtime=substr($row['StartDate'],-5);
            $min=(strtotime($row['EndDate'])-strtotime($row['StartDate']))/60;
            $note=str_replace("'","’",$row['Notes']);
            $insertSQL="insert into registration(ddate,seqno,cusno,sch_time,schlen,sch_note,drno1,drno2)
                        values('$dt','000','$cusno','$schtime',$min,'$note',$dr,$dr)";
            echo $cusno,'--'.$dt." ";
            $mariaConn->exec($insertSQL);            
        } 

        //處理newpatient
        $sql="update registration set cussn=-1 where cusno like 'NP%' ";
        $mariaConn->exec($sql);
        
        //增加newpatient 
        $sql="insert into newpatient (name,regsn,drno)  
              select cusno,regsn,drno1 
                from registration 
               where cussn=-1 and deldate is null";
        $mariaConn->exec($sql);
        
        //配對  
        $sql="update registration r,newpatient n 
                 set newpasn=n.sn
               where r.regsn=n.regsn";
        $mariaConn->exec($sql);

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
            $freq=$row['freq'];
            $rxday=$row['rxday'];
            $insertSQL="INSERT into drugcomdetails(comDno,drugno,dose,`times`,day)
                  values('$comdno','$drugno','01','$freq',$rxday)";
            $mariaConn->exec($insertSQL);                        
        }

    echo "建立索引";
    $sql="ALTER TABLE `treat_record` ADD INDEX( `icuploadD`, `uploadD`)";
    $mariaConn->exec($sql);  

    $sql="ALTER TABLE `registration` ADD INDEX( `icuploadD`, `uploadD`)";
    $mariaConn->exec($sql);  

    $sql="ALTER TABLE `prescription` ADD INDEX( `uploadD`, `icuploadd`)";
    $mariaConn->exec($sql);  

    //配對患者編號
    echo "<br>配對患者編號<br>";
    $sql="update `registration` r,customer c set r.cussn=c.cussn where r.cusno=c.cusno";
    $mariaConn->exec($sql);  

    echo "<br>設定診別";
    $sql="update `registration` r,section s set r.section=s.sno where reg_time between s.start and s.end";
    $mariaConn->exec($sql);  

    echo "<br>串掛號與處置";
    $sql="update registration r, treat_record t 
            set t.regsn=r.regsn,t.seqno=r.seqno ,t.cussn=r.cussn
            where r.icuploadD=t.icuploadD";
    $mariaConn->exec($sql);  

    echo "<br>串掛號與處方箋";
    $sql="update registration r,prescription p 
            set p.regsn=r.regsn,p.ddate=r.ddate,p.seqno=r.seqno 
            where r.icuploadd=p.icuploadd";
    $mariaConn->exec($sql);  

    echo "<br>預約患者的電話轉入";
    $sql="update registration r,customer c
             set r.schtel=c.custel,r.schmobile=c.cusmbo
           where r.cussn=c.cussn
             and r.seqno='000'";
    $mariaConn->exec($sql);  

    echo "<br>處方箋整理";
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

    echo '<br>整理 check & finding';
    $sql="update registration r,treat_record t  
             set r.check_finding=t.treat_memo
           where r.trcode='01271C' 
             and r.regsn=t.regsn
             and t.trcode='01271'";
    $mariaConn->exec($sql); 

    $sql="update treat_record set deldate='1911-01-01' where trcode like '0127%'";
    $mariaConn->exec($sql); 

    //填入療程卡號與療程開始日
    echo "<br> 插入 療程開始日";
    $sql="drop table if exists ABstart";
    $mariaConn->exec($sql); 

    $sql="create table ABstart(tsn int,tregsn int,rregsn int,icseqno varchar(4),startdt varchar(10))";
    $mariaConn->exec($sql); 

    $sql="insert into ABstart(tsn,tregsn,rregsn,icseqno,startdt)
            select t.treatsn,t.regsn,r.regsn,substr(r.ic_seqno,-4),r.ddate
         from treat_record t ,registration r
        where t.uploadd=r.uploadd
          and t.uploadd!=''
          and t.ddate!=r.ddate
          and r.ic_type!='AB'";
    $mariaConn->exec($sql); 

    $sql="ALTER TABLE `ABstart` ADD INDEX( `tsn`)";
    $mariaConn->exec($sql); 

    $sql="update treat_record t,abstart a 
             set t.start_date=a.startdt,start_icseq=icseqno
           where t.treatsn=a.tsn";
    $mariaConn->exec($sql); 

    //修正轉入的treatment的一些屬性
    $sql="update treatment t,nhicode n 
            set t.is_oper=n.is_oper,t.is_endo=n.is_endo,t.is_oral=n.is_oral,t.is_peri=n.is_peri,t.is_xray=n.is_xray,t.is_pedo=n.is_pedo,
                t.tr_od=n.tr_od,t.tr_endo=n.tr_endo,t.tr_peri=n.tr_peri,t.tr_os=n.tr_os,t.tr_pedo=n.tr_pedo,
                t.fee_unit=n.feeunit,t.category=n.category
            where t.nhicode=n.nhicode ";
    $mariaConn->exec($sql); 

    //修正處置說明
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
    $sql="update registration set hosp_from=null WHERE hosp_from ='' ";
    $mariaConn->exec($sql); 

    // $sql="update prescription p,drug d set p.uprice=nhifee where p.nhidrugno=d.nhicode and uprice!=d.nhifee";
    // $mariaConn->exec($sql); 

    $sql="update registration set trcode=null WHERE trcode ='' ";
    $mariaConn->exec($sql); 

    $sql="update  treatment t,examfee e set e.memo=t.memo WHERE nhicode=code";
    $mariaConn->exec($sql); 

    $sql="delete from treatment where nhicode in ('01271C','01272C','01273C')";
    $mariaConn->exec($sql); 
    
    $sql="update customer set cusmob=custel where cusmob='' ";
    $mariaConn->exec($sql); 

    $sql="update drug set dose='01' where dose='' ";
    $mariaConn->exec($sql); 

    //轉換親友，看資料是依地址來歸親友，所以目前將地址一樣的先用同一個群組
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

    $sql="insert into family_groups(id) select sn from tmpfamily";
    $mariaConn->exec($sql); 

    $sql="update customer c,tmpfamily f set c.family_group_id=f.sn where cusaddr=addr";
    $mariaConn->exec($sql); 
    
    echo "<Br><br>。。。。。。。 資料轉換完成 。。。。。。。";
?>