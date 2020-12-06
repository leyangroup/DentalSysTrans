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
			<H2 style="text-align: center;">轉換小天使資料</H2>
			<h2></h2>
			<h4>當天日期：<input type="text" id="DT" value=<?php echo $dt;  ?>>(ex.2019-10-03)日期會與預約轉入的資料有關</h4>
			<h4>最後申報月份：<input type="text" id="giamon" value=<?php echo substr($dt,0,7);  ?>>(ex.2020-10 與捉申報資料有關，一定要注意</h4>
			<h4>程式路徑：<input type="text" id="path" value='d:\angel2'></h4>
			<div><label></label></div>
			<h2 style="color:red">一定要先產生申報才可以轉檔哦!!</h2>
			<h4 style="color:blue">請依序按鈕執行</h4>
			<div>
				<button type='button' class="btn btn-info" id="idx">0.建立索引(若重複轉入，只需執行一次)</button>
				<input type='text' id='idx_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>
			<div><label></label></div>

			<div>
				<button type='button' class="btn btn-info" id="Dr">1.診所、醫師、優待身份 資料轉換</button>
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

			<div><label></label></div>
			<div>
				<button type='button' class="btn btn-info" id="allergic">9.過敏藥物、病史 資料轉換</button>
				<input type='text' id='allergic_on' style="color:blue" readonly value='請按鈕' size=50>
				<label>小天使的過敏藥物與系統疾病是放在同一個資料表的，所以allergic,systemdisease都會放</label>
			</div>

			<div><label></label></div>
			<div>
				<button type='button' class="btn btn-info" id="reTrans">10.陽安 重轉掛號與醫師 資料轉換</button>
				<input type='text' id='reTrans' style="color:blue" readonly value='請按鈕' size=50>
				<label></label>
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
 			window.open("angel_ajax/reg.php?path="+$("#path").val()+"&dt="+$("#DT").val()+"&giamon="+$("#giamon").val(),"掛號資料");
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
 		
 		$("#allergic").on("click",function(){
 			var d = new Date();
 			window.open("angel_ajax/allergic.php?path="+$("#path").val(),"過敏藥物、病史 資料");
 			$("#allergic_on").val('執行'+d); 
 		});

 		$("#reTrans").on("click",function(){
 			var d = new Date();
 			window.open("angel_ajax/reTrans.php?path="+$("#path").val(),"重新轉陽安掛號 資料");
 			$("#reTrans").val('執行'+d); 
 		});
 	});

</script>