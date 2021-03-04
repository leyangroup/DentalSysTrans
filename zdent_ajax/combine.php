<?php
	include_once "../include/db.php";

    $conn=MariaDBConnect();

    //zhis1.cussn
    
	echo "填入cussn,cusno <br>!!";
    $conn->exec("update zhis1 a,customer c set a.cussn=c.cussn,a.cusno=c.cusno where a.upatno=c.familyno");
    $conn->exec("update zhis1 set cusno='' where cusno is null");
    
    //設定zhis4,zhis6的regsn
	echo "設定zhis4,zhis6的regsn <br>!!";

    $conn->exec("update zhis1 a,zhis4 b 
    				set b.regsn=a.regsn 
    			  where a.upatno=b.upatno and a.date=b.date and a.icseqno=b.icseqno");

    $conn->exec("update zhis1 a,zhis6 b 
    				set b.regsn=a.regsn 
    			  where a.upatno=b.upatno and a.date=b.date and a.icseqno=b.icseqno");
	
	echo "insert 掛號 <br>!!";
    //registration
    $conn->exec("truncate table registration");
	$sql="insert ignore into registration(regsn,ddate,cussn,cusno,category,ic_type,trcode,ic_seqno,treatno,amount,nhi_partpay,trpay,giaamt,nhi_tamt,stdate,section,case_history,rx_day,rx_type)
		  SELECT a.regsn,a.westDT,cussn,cusno,a.category,a.ic_type,a.trcode,a.icseqno,treatno,amount,partpay,trpay,giaamt,tamt,a.upatno,'1','4',0,'2'
			FROM zhis1 a, zhis10  b  
		   WHERE a.regsn=b.regsn";
	$conn->exec($sql);
	//填看診醫師
	$conn->exec("update registration r, zhis3 b set r.drno1=b.drsn,r.drno2=b.drsn where r.regsn=b.regsn");

	//填健保身份
	$conn->exec("update registration r,zhis88 b  
					set nhi_status=nhistatus,reg_memo=cusname,nhi_partpay=nhipartpay,uploadno=ztransukey
				  where r.regsn=b.regsn
                    and r.stdate=b.upatno");
	//填取卡時間
	$conn->exec("update registration r,z_nupload b
					set r.ic_datetime=b.icdat ,r.reg_time=concat(substr(icdate,8,2),':',substr(icdate,10,2))
				  where r.uploadno=b.ubkey");

	echo "產生班別……早……<br>";
	$sql="update registration set section='1' where reg_time<='12:30'";
	$conn->exec($sql);

	echo "產生班別……午……<br>";
	$sql="update registration set section='2' where reg_time  between '12:31' and '17:30' ";
	$conn->exec($sql);

	echo "產生班別……晚……<br>";
	$sql="update registration set section='3' where reg_time>='17:31'";
	$conn->exec($sql);

	echo "填入seqno";
	$sql="drop table if exists newseqno";
	$conn->exec($sql);

	$sql="create table newseqno(regsn int,dt char(10),icdt char(13),rownum int,seqno int,dtals char(10))";
	$conn->exec($sql);

	$sql="ALTER TABLE `newseqno` ADD UNIQUE(`regsn`)";
	$conn->exec($sql);

	$sql="insert into newseqno
	select regsn,ddate,ic_datetime,@rownum:=@rownum+1,if(@dt=aa.ddate,@seqno:=@seqno+1,@seqno:=1) as seqno,@dt:=aa.ddate
	from (
		select regsn,ddate,ic_datetime,b.*
					from (select regsn,ddate,ic_datetime
						  from registration )a , (select @rownum:=0,@dt:=null, @seqno:=0)b
	    ) aa order by ddate,ic_datetime";
	$conn->exec($sql);

	$sql="update newseqno n,registration r 
			set r.seqno=n.seqno
			WHERE n.regsn=r.regsn";
	$conn->exec($sql);	

	$conn->exec("update registration set seqno=substr(concat('000',seqno),-3)");
	//填處置
	echo "insert 處置 <br>!!";

	$conn->exec("truncate table treat_record");
	$conn->exec("insert into treat_record (regsn,ddate,fdi,trcode,treatname,nums,punitfee,add_percent,pamt) 
				 select regsn,westDT,fdi,trcode,left(trname,90),nums,price,addpercent,pamt
				   from zhis4
				  where memo is null ");

	$conn->exec("update registration r,treat_record t 
				    set t.cussn=r.cussn,t.seqno=r.seqno
				  where r.regsn=t.regsn");

	//填處方箋
	echo "insert 處方箋 <br>!!";

	$conn->exec("truncate table prescription");
	$conn->exec("insert into prescription (regsn,ddate,drugno,nhidrugno,qty,totalq,dose,part,times,uprice,amount,day)
				 SELECT regsn,westDT,code,code,round(totalq/days),totalq,(select code from leconfig.zhi_drug_dose where qty=z.dose limit 1),part,times,0,0,days 
				   from zhis6 z");
	//更新seqno
	$conn->exec("update registration r,prescription p set p.seqno=r.seqno where r.regsn=p.regsn");
	//填處方天數
	$conn->exec("update registration r,zhis5 z set r.rx_day=z.rx_day,rx_type='1',trcode=case trcode when '00130C' then '00129C' when '00306C' then '00305C' end  where r.regsn=z.regsn ");

	//填傷病
	$conn->exec("update treat_record t,zhis3 z set t.sickno=case when length(z.sickno)=4 then z.sickno else '' end ,icd10=z.sickno where t.regsn=z.regsn");

	//處理療程
	$conn->exec("update treat_record t , zhis10 z
					set start_date=z.startdate,start_icseq=z.starticseq
				  where t.regsn=z.regsn
					and startdate!=''");

	$conn->exec("update registration r, treat_record t 
					set ic_type='AB',ic_seqno=left(ic_seqno,3),r.trcode=''
				  where r.regsn=t.regsn
					and r.ic_type='02'
					and t.start_date is not null 
					and t.start_date !=''
					and r.trpay=0");

	//處理身障
	$conn->exec("update `registration` 
					set barid=left(treatno,2)
				  WHERE treatno REGEXP 'FC|FD|FE|FF|FG|FH|FI|FJ|FK|FL|FM|FN|FV|FX|FU|FZ|L1|L5|L6|L7|L8|L9|LA|LF|LG|LH'");

	$conn->exec("update registration r,treat_record t 
					set baraddps=add_percent
				  WHERE barid REGEXP 'FC|FD|FE|FF|FG|FH|FI|FJ|FK|FL|FM|FN|FV|FX|FU|FZ|L1|L5|L6|L7|L8|L9|LA|LF|LG|LH'
					and r.regsn=t.regsn
					and t.add_percent!=1");

	//填入最後就診醫師


	//$conn->exec();



?>