<?php
	require_once '../include/db.php';
    $conn=MariaDBConnect();
    
    
    $conn->exec("truncate table oemaster");
    $conn->exec("truncate table oedetail");
    $conn->exec("truncate table oepayment");
    $conn->exec("truncate table oepayment_detail");

    echo "<h2>自費項目填入</h2>";

    echo "<h2>產生oemaster</h2>";
    $sql="insert into oemaster(ose,oeno,date,cussn,tmplan,amount,total,paid,discamt,tax,discount,dcsn,
                  maindr,item,rtamt,tyl,tyr,creator,memo)
          SELECT id,id,DT,0,tname,total,total,paid,total,0,0,0,
          (select id from leconfig.zhi_staff where name=dr order by id desc limit 1),0,0,'','',0,patno
          FROM z_oe a";
    $conn->exec($sql);

    $conn->exec("update oemaster set status='F' where total=paid");
	$conn->exec("update oemaster set status='C' where total!=paid");


    $conn->exec("update oemaster m,customer c set m.cussn=c.cussn where m.memo=c.cusno");

    echo "<h2>產生oedetail</h2>";
    $sql="insert into oedetail(osn,cate1,cate2,cate3,qty,ogprice,price,amt,discamt,tmdr)
    		select id,1,2,3,1,0,total,total,total,(select id from leconfig.zhi_staff where name=dr order by id desc limit 1) 
    		  from z_oe";
    $conn->exec($sql);

    echo "<h2>產生oepayment</h2>";
    $sql="insert into oepayment(osn,payno,paydate,kind,payamt,payway,memo)
    		select oeid,id,paydt,'I',pay,case when payway='現金' then 0 else 1 end,concat(memo,matmemo)
    		  from z_payment";
    $conn->exec($sql);

    echo "<h2>產生oepayment_detail</h2>";
    $sql="insert into oepayment_detail(osn,psn,dsn,month,stage,drsn,cate3,type,pay,created_at,created_by)
		  SELECT p.osn,p.psn,d.dsn,left(paydate,7),stage,tmdr,cate3,0,payamt,now(),0
			FROM oepayment p,oedetail d 
		   where p.osn=d.osn";
    $conn->exec($sql);


	echo "<h1>彙整完成</h1>";

?>