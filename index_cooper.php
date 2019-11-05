<?php
	$dt=getToday();
	function getToday(){
		date_default_timezone_set('Asia/Taipei');
		$d=new DateTime();
		return $d->format('Y-m-d');
	}
	
?>

<!DOCTYPE html>
<html>
<head>
	<title>系統轉換</title>
</head>
<body>
	<form>
		<H2>Cooper系統之病患病歷 轉換 至 樂晴 </H2>
		<h4>日期：<input type="text" id="DT" value=<?php echo $dt;  ?>>(ex.2019-04-10)</h4>
		<p>
		<table border=1 width='80%'>
			<tr>
				<td>COOPER資料轉換</td>
				<td>請依順序按鈕執行</td>
			</tr>
			<tr>
				<td><button type='button' id="COOPER_dr">1.診所、醫師、優待身份 資料轉換</button></td>
				<td><input type='text' id='dr_on' style="color:blue" readonly value='請按鈕' size=30></td>
			</tr>
			<tr>
				<td><button type='button' id="COOPER_patient">2.患者基本 資料轉換</button></td>
				<td><input type='text' id='pt_on' style="color:blue" readonly value='請按鈕' size=30></td>
			</tr>
			<tr>
				<td><button type='button' id="COOPER_reg">3.掛號 資料轉換</button></td>
				<td><input type='text' id='reg_on' style="color:blue" readonly value='請按鈕' size=30></td>
			</tr>
			<tr>
				<td><button type='button' id="COOPER_treat">4.處置 資料轉換</button></td>
				<td><input type='text' id='treat_on' style="color:blue" readonly value='請按鈕' size=30></td>
			</tr>
			<tr>
				<td><button type='button' id="COOPER_pre">5.處方箋 資料轉換</button></td>
				<td><input type='text' id='pre_on' style="color:blue" readonly value='請按鈕' size=30></td>
			</tr>
			<tr>
				<td><button type='button' id="COOPER_app">6.預約 資料轉換</button></td>
				<td><input type='text' id='app_on' style="color:blue" readonly value='請按鈕' size=30></td>
			</tr>
			<tr>
				<td><button type='button' id="COOPER_drug">7.藥品 資料轉換</button></td>
				<td><input type='text' id='drug_on' style="color:blue" readonly value='請按鈕' size=30></td>
			</tr>
			
			<tr>
				<td><button type='button' id="COOPER_other">8.支付標準 與其它 資料轉換</button></td>
				<td><input type='text' id='other_on' style="color:blue" readonly value='請按鈕' size=30></td>
			</tr>
		</table>
	</form>
</body>
</html>

<script type="text/javascript" src="include/jquery.min.js"></script>
<script type="text/javascript">
 	$(document).ready(function(){
 		$("#COOPER_dr").on("click",function(){
 			var d = new Date();
 			window.open("cooper_ajax/doctor.php","醫師資料轉換");
 			$("#dr_on").val('執行'+d); 
 		});

 		$("#COOPER_patient").on("click",function(){
 			window.open("cooper_ajax/customer.php","患者資料轉換");
 			var d = new Date();
 			$("#pt_on").val('執行'+d) ; 
 		});

 		$("#COOPER_reg").on("click",function(){
 			window.open("cooper_ajax/reg.php","掛號資料轉換");
 			var d = new Date();
 			$("#reg_on").val('執行'+d) ; 
 		});

 		$("#COOPER_treat").on("click",function(){
 			window.open("cooper_ajax/treat.php","處置資料轉換");
 			var d = new Date();
 			$("#treat_on").val('執行'+d) ;
 		});

 		$("#COOPER_pre").on("click",function(){
 			window.open("cooper_ajax/prescription.php","處方箋資料轉換");
 			var d = new Date();
 			$("#pre_on").val('執行'+d);  
 		});

 		$("#COOPER_app").on("click",function(){
 			window.open("cooper_ajax/appointment.php?DT="+$("#DT").val(),"預約資料轉換");
 			var d = new Date();
 			$("#app_on").val('執行'+d);  
 		});

 		$("#COOPER_drug").on("click",function(){
 			window.open("cooper_ajax/drug.php","藥品相關資料轉換");
 			var d = new Date();
 			$("#drug_on").val('執行'+d);
 		});

 		$("#COOPER_other").on("click",function(){
 			window.open("cooper_ajax/other.php?DT="+$("#DT").val(),"支付標準與其它資料轉換");
 			var d = new Date();
 			$("#other_on").val('執行'+d);  
 		});

 		$("#BestChoice_OD").on("click",function(){
 			window.open("bc_OD.php","補 雙鄰接面處置轉入");
 		});

 		$("#BestChoice_AC").on("click",function(){
 			window.open("bc_AC.php","補 預防保健資料轉入");
 		});

 		$("#BestChoice_cus").on("click",function(){
 			window.open("bc_CUS.php","補 患者");
 		});
 	});

</script>