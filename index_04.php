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
	<title>系統轉換1.0.9</title>
	<link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
	<div class="row">
	<form class="form-horizontal" role="form" id="main" name="main" >
		<div class="col-md-2 column">
		</div>
		<div class="col-md-6 column">
			<H2 style="text-align: center;">轉換 林氏</H2>
			<h2></h2>
			<h4>當天日期：<input type="text" id="DT" value=<?php echo $dt;  ?>>(ex.2019-10-03)日期會與預約轉入的資料有關</h4>
			<h4>程式路徑：<input type="text" id="path" value='c:\angel2'></h4>
			<div><label></label></div>
			
			<div>
				<button type='button' class="btn btn-info" id="idx">0.建立索引(若重複轉入，只需執行一次)</button>
				<input type='text' id='idx_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>
			<div><label></label></div>

			<div>
				<button type='button' class="btn btn-info" id="Dr">1.診所、醫師 資料轉換</button>
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
				<button type='button' class="btn btn-info" id="drug">6.藥品 資料轉換</button>
				<input type='text' id='drug_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>
			<div><label></label></div>

			<div>
				<button type='button' class="btn btn-info" id="appointment">7.預約 資料轉換</button>
				<input type='text' id='app_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>
			
			<div><label></label></div>
			<div>
				<button type='button' class="btn btn-info" id="tm">8.支付標準、其它支出項目 資料轉換</button>
				<input type='text' id='tm_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>

			
		
		</table>
	</form>
</body>
</html>

<script type="text/javascript" src="include/jquery.min.js"></script>
<script type="text/javascript">
 	$(document).ready(function(){
 		$("#idx").on("click",function(){
 			var d = new Date();
 			window.open("angel_ajax/create_Index.php?path="+$("#path").val(),"建立索引");
 			$("#idx_on").val('執行'+d); 
 		});
 		$("#Dr").on("click",function(){
 			var d = new Date();
 			window.open("angel_ajax/basicData.php?path="+$("#path").val(),"診所基本資料轉換");
 			$("#dr_on").val('執行'+d); 
 		});
 		$("#patient").on("click",function(){
 			var d = new Date();
 			window.open("angel_ajax/patient.php?path="+$("#path").val(),"患者基本資料");
 			$("#pt_on").val('執行'+d); 
 		});

 		$("#reg").on("click",function(){
 			var d = new Date();
 			window.open("angel_ajax/reg.php?path="+$("#path").val()+"&dt="+$("#DT").val(),"掛號資料");
 			$("#reg_on").val('執行'+d); 
 		});

 		$("#treat").on("click",function(){
 			var d = new Date();
 			window.open("angel_ajax/treat.php?path="+$("#path").val(),"處置資料");
 			$("#treat_on").val('執行'+d); 
 		});

 		$("#pre").on("click",function(){
 			var d = new Date();
 			window.open("angel_ajax/prescription.php?path="+$("#path").val(),"處方箋資料");
 			$("#pre_on").val('執行'+d); 
 		});

 		$("#drug").on("click",function(){
 			var d = new Date();
 			window.open("angel_ajax/drug.php?path="+$("#path").val(),"藥品 資料");
 			$("#drug_on").val('執行'+d); 
 		});

 		$("#appointment").on("click",function(){
 			var d = new Date();
 			window.open("angel_ajax/appointment.php?path="+$("#path").val()+"&dt="+$("#DT").val(),"預約 資料");
 			$("#app_on").val('執行'+d); 
 		});

 		$("#tm").on("click",function(){
 			var d = new Date();
 			window.open("angel_ajax/tm.php?path="+$("#path").val(),"支付標準 資料");
 			$("#tm_on").val('執行'+d); 
 		});
 		
 		
 	});

</script>