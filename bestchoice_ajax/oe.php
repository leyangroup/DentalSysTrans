<?php
	include_once "../include/db.php";

    header("content-Type:text/html;charset=utf-8");
    $ip=$_GET['IP'];
    $serverName=$ip."\bestchoice";
    $connectionInfo=array("Database"=>"DoctorProject","UID"=>'bestchoice',"PWD"=>"0937093374","CharacterSet"=>"UTF-8");
    $msConn=sqlsrv_connect($serverName,$connectionInfo);
    if ($msConn===false){
        die(print_r(sqlsrv_errors(),true));
    }
    $mariaConn=MariaDBConnect();
    $DT=$_GET['DT'];

    //讀取醫師資料
    $dr=$mariaConn->query("SELECT identity,min(id) id from leconfig.zhi_staff WHERE identity!='' group by 1 order by 1 ");
    $drArr=[];
    foreach ($dr as $key => $value) {
        $staffid=$value["identity"];
        $drArr[$staffid]=$value['id'];
    }
    var_dump($drArr);

    //清空oemaster,oedetail,oepayment,oepayment_detail
    $mariaConn->exec("truncate table oemaster");
    $mariaConn->exec("truncate table oedetail");
    $mariaConn->exec("truncate table oepayment");
    $mariaConn->exec("truncate table oepayment_detail");
    $mariaConn->exec("truncate table oebasemap");
    $mariaConn->exec("truncate table oereceipt");
    $mariaConn->exec("truncate table oesoap");


    //讀取優勢第三層的自費項目
    $sql="select * from DealData";  
    $BC_cate3=[];     
    $result=sqlsrv_query($msConn,$sql) or die("1.sql error:".sqlsrv_errors());
    while ($row=sqlsrv_fetch_array($result)){
        if ($row['Enable']=='1'){
            $no=$row['DealNo'];
            $name=$row['DealName'];
            $BC_cate3[$no]=$name;
        }
    }

    //樂晴中類別
    $sql="select id,name from leconfig.zhi_category where depth=1";
    $result=$mariaConn->query($sql);
    foreach ($result as $key => $value) {
        $id=$value['id'];
        $name=$value['name'];
        $LeCate2[$name]=$id;
    }

    $sql="select id,parent_id,name from leconfig.zhi_category where depth=2";
    $result=$mariaConn->query($sql);
    foreach ($result as $key => $value) {
        $id=$value['id'];
        $parent_id=$value['parent_id'];
        $name=$value['name'];
        $LeCate3[$parent_id][$name]=$id;  //父類的id 和 小類的名稱 其中中類和小類的名稱相同
    }

    //樂晴付款方式
    $LePayway['現金']=0;
    $LePayway['支票']=1;
    $LePayway['信用卡']=2;

	//自費資料
    echo "<br>轉入自費<br>";
    $sql="SELECT a.ProID,a.ProName,a.Description,a.PatNo,a.ProType1,a.ProType2,MainDoctor,a.TotaMoney,
                  a.NotrMoney,a.DiscMoney,a.LastMoney,
                 b.PdID,b.fdi,b.place,b.DcID,b.TheCharges,
                 b.Nums,b.DealAmt,b.PayType,b.ReallyTC,
                 convert(char(10),b.ActDate,120) ActDate,
                 (select stname from SecondType where StID=a.ProType2) Type2Name,
                 (select value2 from code where CodeID='002' and no!=0 and PayType=no)payway
            from Project a,Project_Detail b 
           where a.ProID=b.ProID
             and a.Enable=1
             and b.Enable=1
             order by a.ProID,b.PdID";

    $result=sqlsrv_query($msConn,$sql) or die("2.sql error:".sqlsrv_errors());
    $sn=0;  //oemaster的sn
    $dsn=1; //oedetail的sn
    $psn=1;  //oepayment的sn
    $saveMaster=false;
    $ProID='';
    while ($row=sqlsrv_fetch_array($result)){
        //master只需存一次
        //detail要每次輸入每次存 因為優勢沒有detail 是在每次儲存時才會選 做那一種處理 及費用，所以會把它存在detail 第二層不會進來，只會以文字顯示，若不這麼做，就無法在detail中呈現
        if ($ProID!=$row['ProID']){
            $saveMaster=true;
            $sn++;  //
            $dsn++;  // sn 和 dsn都只有一筆 所以每新增一筆才加1
        }
        $ProID=$row['ProID']; //優勢代碼
        $yy=substr($ProID,0,3)+1911;
        $mm=substr($ProID,3,2);
        $dd=substr($ProID,5,2);
        $DT=$yy.'-'.$mm.'-'.$dd;//建單日
        $oeno=$row['ProID']; //優勢代碼
        $month=$yy.'-'.$mm;
        $cusno=$row['PatNo']; //病編 先存在sign 到時候再對應
        $tmplan=addslashes($row['ProName']); //計畫名稱
        $memo=$row['Description'];  //備註
        $drid=$row['MainDoctor'];   //醫師身份證
        $c1id=$row['ProType1'];  //大類
        $Type2Name=$row['Type2Name'];  //中類名稱
        //要讓中類和小類的名稱都一樣 但其實他們對應的id是不同的

        $c2id=$LeCate2[$Type2Name]; //取得中類的id
        $c3id=$LeCate3[$c2id][$Type2Name]; //用中類的id和名稱找出小類的id 中類與小類的名稱相同，所以可以這樣找

        if ($c2id==null){
            $c2id=0;
        }

        if ($c3id==null){
            $c3id=0;
        }
        $maindr=$drArr[$drid];   //le醫師代碼
        if ($maindr==null){
            $maindr=0;
        }
        $amount=$row['LastMoney'];  //總應收
        $discount=$row['DiscMoney']; //優待金額
        $discamt=$amount-$discount;  //優待後應收
        $paid=$row['NotrMoney']; //已收
        if ($paid==$discamt){
            $status='F';
        }elseif ($paid<$discamt){
            $status='C';
        }else{
            $status='A';
        }
        $fdi=$row['fdi'];
        $payway=$row['payway'];
        $pw=$LePayway[$payway];
        $DealNo=$row['DcID'];
        if ($DealNo==''){
            $DealName='';
        }else{
            $DealName=$BC_cate3[$DealNo];
        }
        $ActDate=$row['ActDate'];
        $PdID=$row['PdID'];
        $payamt=$row['TheCharges'];
        if ($saveMaster){
            //save oemaster
            //用電子簽名的欄位來暫存患者編號
            $insertSQL="INSERT into oemaster(ose,oeno,date,cussn,sign,tmplan,amount,discamt,total,discount,maindr,status,paid,tax,dcsn,rtamt,tyl,tyr,creator)
                        values($sn,'$ProID','$DT',0,'$cusno','$tmplan',$amount,$discamt,$discamt,$discount,
                        $maindr,'$status',$paid,0,0,0,'','',0)";
            $isok=$mariaConn->exec($insertSQL);
            if ($isok==0){
                echo "新增 oemaster 失敗：$insertSQL"."<br>";
            }
            $saveMaster=false;

            //save oedetail
            $insertSQL="INSERT into oedetail(dsn,osn,stage,tmdr,fdi,cate1,cate2,cate3,qty,ogprice,price,amt,discamt) 
                            values($dsn,$sn,1,$maindr,'$fdi',$c1id,$c2id,$c3id,1,$amount,$amount,$amount,$discamt)";
            $isok=$mariaConn->exec($insertSQL);
            if ($isok==0){
                echo "新增 oedetail 失敗：$insertSQL"."<br>";
            }
            
        }

        //save oepayment 
        $insertSQL="INSERT into oepayment(psn,osn,payno,paydate,kind,payway,payamt,memo)
                        values($psn,$sn,'$PdID','$ActDate','I','$pw',$payamt,'$DealName') ";
        $isok=$mariaConn->exec($insertSQL);
        if ($isok==0){
            echo "新增 oepayment 失敗：$insertSQL"."<br>";
        }

        //save oepayment_detail
        $insertSQL="INSERT into oepayment_detail(osn,psn,dsn,month,stage,drsn,cate3,type,pay,created_at,created_by)
                        values($sn,$psn,$dsn,'$month','1',$maindr,$c3id,1,$payamt,now(),0) ";
        $isok=$mariaConn->exec($insertSQL);
        if ($isok==0){
            echo "新增oepayment_detail 失敗：$insertSQL"."<br>";
        }
        $psn++;
    }

    //對應患者代碼
    $mariaConn->exec("UPDATE oemaster o,customer c set o.cussn=c.cussn where c.cusno=o.sign");

    //因為已收款在優勢的系統找不到，所以自己算
    $mariaConn->exec("UPDATE oemaster m
                        set paid=(select sum(payamt) from oepayment where osn=m.ose)
                      where paid!=(select sum(payamt) from oepayment where osn=m.ose)");

    $mariaConn->exec("UPDATE oemaster set status='C' where discamt!=paid ");
    
    echo "<h1>自費 資料 轉入完畢</h1>";
    sqlsrv_close($msConn);
?>