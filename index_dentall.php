<?php
	include "include/db.php";
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
	<title>系統轉換1.0.8</title>
	<link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
	<div class="row">
	<form class="form-horizontal" role="form" id="main" name="main" >
		<div class="col-md-2 column">
		</div>
		<div class="col-md-6 column">
			<H2 style="text-align: center;">轉換 牙醫通 資料</H2>
			<h2></h2>
			<h4>當天日期：<input type="text" id="DT" value=<?php echo $dt;  ?>>(ex.2019-10-03)日期會與預約轉入的資料有關</h4>
			
			<div><label></label></div>
			
			<h4 style="color:blue">請依序按鈕執行</h4>
			<div>
				<button type='button' class="btn btn-info" id="dbindex">索引處理(一次就好)</button>
				<input type='text' id='dbindex_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>
			<div><label></label></div>

			<div>
				<button type='button' class="btn btn-info" id="Dr">1.醫師 資料轉換</button>
				<input type='text' id='dr_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>
			<div><label></label></div>

			<div>
				<button type='button' class="btn btn-info" id="patient">2.患者基本 資料轉換</button>
				<input type='text' id='pt_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>
			<div><label></label></div>

			<div>
				<button type='button' class="btn btn-info" id="reg">3.掛號 資料轉換</button>
				<input type='text' id='reg_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>
			<div><label></label></div>

			<div>
				<button type='button' class="btn btn-info" id="treat">4.處置 資料轉換</button>
				<input type='text' id='treat_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>
			<div><label></label></div>

			<div>
				<button type='button' class="btn btn-info" id="pre">5.處方箋 資料轉換</button>
				<input type='text' id='pre_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>
			<div><label></label></div>

			<div>
				<button type='button' class="btn btn-info" id="drug">6.藥品、過敏藥、病史 資料轉換</button>
				<input type='text' id='drug_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>
			<div><label></label></div>

			<div>
				<button type='button' class="btn btn-info" id="appointment">7.預約 資料轉換</button>
				<input type='text' id='app_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>
			
			<div><label></label></div>
			<div>
				<button type='button' class="btn btn-info" id="tm">8.支付標準 與 資料整理</button>
				<input type='text' id='tm_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>

			<div><label></label></div>
			<div>
				<button type='button' class="btn btn-info" id="oe">自費轉出</button>
				<input type='text' id='oe_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>
			
		</table>
	</form>
</body>
</html>

<script type="text/javascript" src="include/jquery.min.js"></script>
<script type="text/javascript">
 	$(document).ready(function(){
 		$("#dbindex").on("click",function(){
 			var d = new Date();
 			window.open("dentall_ajax/dbindex.php","索引 處理");
 			$("#dbindex_on").val('執行'+d); 
 		});
 		$("#Dr").on("click",function(){
 			var d = new Date();
 			window.open("dentall_ajax/doctor.php","使用者 資料轉換");
 			$("#dr_on").val('執行'+d); 
 		});

 		$("#patient").on("click",function(){
 			var d = new Date();
 			window.open("dentall_ajax/patient.php","患者 資料轉換");
 			$("#pt_on").val('執行'+d); 
 		});

 		$("#reg").on("click",function(){
 			var d = new Date();
 			window.open("dentall_ajax/registration.php","掛號 資料轉換");
 			$("#reg_on").val('執行'+d); 
 		});

 		$("#treat").on("click",function(){
 			var d = new Date();
 			window.open("dentall_ajax/treat.php","處置 資料轉換");
 			$("#treat_on").val('執行'+d); 
 		});

 		$("#pre").on("click",function(){
 			var d = new Date();
 			window.open("dentall_ajax/prescription.php","處方 資料轉換");
 			$("#pre_on").val('執行'+d); 
 		});

 		$("#drug").on("click",function(){
 			var d = new Date();
 			window.open("dentall_ajax/drug.php","藥品 資料轉換");
 			$("#drug_on").val('執行'+d); 
 		});
 
 		$("#appointment").on("click",function(){
 			var d = new Date();
 			window.open("dentall_ajax/appointment.php","預約 資料轉換");
 			$("#app_on").val('執行'+d); 
 		});

 		$("#tm").on("click",function(){
 			var d = new Date();
 			window.open("dentall_ajax/treatment.php","處置 資料轉換");
 			$("#tm_on").val('執行'+d); 
 		});

 		$("#oe").on("click",function(){
 			var d = new Date();
 			window.open("dentall_ajax/oe.php","處置 資料轉換");
 			$("#oe_on").val('執行'+d); 
 		});
 	});

</script>