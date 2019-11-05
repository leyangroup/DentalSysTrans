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
	<link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
	<div class="row">
	<form class="form-horizontal" role="form" id="main" name="main" >
		<div class="col-md-2 column">
		</div>
		<div class="col-md-6 column">
			<H2 style="text-align: center;">展望資料轉換至樂晴</H2>
			<h2></h2>
			<h4>程式路徑：<input type="text" id="path" value='c:\visd'></h4>
			<div><label></label></div>

			<h4 style="color:blue">請依序按鈕執行</h4>
			<div>
				<button type='button' class="btn btn-info" id="patient">1.醫師、患者 資料轉換</button>
				<input type='text' id='patient_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>
			<div><label></label></div>
			<div>
				<button type='button' class="btn btn-info" id="reg">2.掛號 資料轉換</button>
				<input type='text' id='reg_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>
			<div><label></label></div>
			<div>
				<button type='button' class="btn btn-info" id="treat">3.處置與處方箋 資料轉換</button>
				<input type='text' id='treat_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>
			
			<div><label></label></div>
			<div>
				<button type='button' class="btn btn-info" id="drug">4.藥品 資料轉換</button>
				<input type='text' id='drug_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>

			<div><label></label></div>
			<div>
				<button type='button' class="btn btn-info" id="add">補牙面 的資料</button>
				<input type='text' id='add_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>
		</table>
	</form>
</body>
</html>

<script type="text/javascript" src="include/jquery.min.js"></script>
<script type="text/javascript">
 	$(document).ready(function(){
 		$("#patient").on("click",function(){
 			var d = new Date();
 			window.open("visd_ajax/basicData.php?path="+$("#path").val(),"患者基本資料轉換");
 			$("#patient_on").val('執行'+d); 
 		});
 		$("#reg").on("click",function(){
 			var d = new Date();
 			window.open("visd_ajax/reg.php?path="+$("#path").val(),"掛號資料");
 			$("#reg_on").val('執行'+d); 
 		});

 		$("#treat").on("click",function(){
 			var d = new Date();
 			window.open("visd_ajax/treat_pre.php?path="+$("#path").val(),"醫令資料");
 			$("#treat_on").val('執行'+d); 
 		});

 		$("#drug").on("click",function(){
 			var d = new Date();
 			window.open("visd_ajax/drug.php?path="+$("#path").val(),"藥品資料");
 			$("#drug_on").val('執行'+d); 
 		});

 		$("#add").on("click",function(){
 			var d = new Date();
 			window.open("visd_ajax/import_trcode_side.php?path="+$("#path").val(),"牙面 資料");
 			$("#add_on").val('執行'+d); 
 		});
 		
 	});

</script>