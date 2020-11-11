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
			<H2 style="text-align: center;">轉換 優勢 資料</H2>
			<h2></h2>
			<h4>當天日期：<input type="text" id="DT" value=<?php echo $dt;  ?>>(ex.2019-10-03)日期會與預約轉入的資料有關</h4>
			<h4>主機IP：<input type="text" id="IP" value='192.168.15.91'></h4>
			<div><label></label></div>
			<h4 style="color:blue">請依序按鈕執行</h4>
			<div>
				<button type='button' class="btn btn-info" id="index">建立索引 只要run一次就可以</button>
				<input type='text' id='index_on' style="color:blue" readonly value='請按鈕' size=50>
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
				<button type='button' class="btn btn-info" id="tm">8.支付標準 資料轉換</button>
				<input type='text' id='tm_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>

			<div><label></label></div>
			<div>
				<button type='button' class="btn btn-info" id="final">9.資料整合</button>
				<input type='text' id='final_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>

			<div><label></label></div>
			<div>
				<button type='button' class="btn btn-info" id="final2">10.療程日期與開始日填入</button>
				<input type='text' id='final2_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>
			<div><label></label></div>

			<div><label></label></div>
			<div>
				<button type='button' class="btn btn-info" id="charge">11.charge</button>
				<input type='text' id='charge_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>

			<div><label></label></div>
			<div>
				<button type='button' class="btn btn-info" id="drugdeal">『森源』 藥品處理</button>
				<input type='text' id='drugdeal_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>

			<div><label></label></div>
			<div>

				<button type='button' class="btn btn-info" id="FdiLen">『森源』 牙位過長處理</button>
				<input type='text' id='FdiLen_on' style="color:blue" readonly value='請按鈕' size=50>

			</div>
		</table>
	</form>
</body>
</html>

<script type="text/javascript" src="include/jquery.min.js"></script>
<script type="text/javascript">
 	$(document).ready(function(){
 		$("#index").on("click",function(){
 			var d = new Date();
 			window.open("bestchoice_ajax/create_index.php?IP="+$("#IP").val(),"診所基本資料轉換");
 			$("#index_on").val('執行'+d); 
 		});

 		$("#Dr").on("click",function(){
 			var d = new Date();
 			window.open("bestchoice_ajax/basic.php?IP="+$("#IP").val(),"診所基本資料轉換");
 			$("#dr_on").val('執行'+d); 
 		});

 		$("#patient").on("click",function(){
 			var d = new Date();
 			window.open("bestchoice_ajax/patient.php?IP="+$("#IP").val(),"患者基本資料");
 			$("#pt_on").val('執行'+d); 
 		});

 		$("#reg").on("click",function(){
 			var d = new Date();
 			window.open("bestchoice_ajax/reg.php?IP="+$("#IP").val(),"掛號資料");
 			$("#reg_on").val('執行'+d); 
 		});

 		$("#treat").on("click",function(){
 			var d = new Date();
 			window.open("bestchoice_ajax/treat.php?IP="+$("#IP").val(),"處置資料");
 			$("#treat_on").val('執行'+d); 
 		});

 		$("#pre").on("click",function(){
 			var d = new Date();
 			window.open("bestchoice_ajax/prescription.php?IP="+$("#IP").val(),"處方箋資料");
 			$("#pre_on").val('執行'+d); 
 		});

 		$("#drug").on("click",function(){
 			var d = new Date();
 			window.open("bestchoice_ajax/drug.php?IP="+$("#IP").val(),"藥品 資料");
 			$("#drug_on").val('執行'+d); 
 		});

 		$("#appointment").on("click",function(){
 			var d = new Date();
 			window.open("bestchoice_ajax/appointment.php?IP="+$("#IP").val()+"&dt="+$("#DT").val(),"預約 資料");
 			$("#app_on").val('執行'+d); 
 		});

 		$("#tm").on("click",function(){
 			var d = new Date();
 			window.open("bestchoice_ajax/tm.php?IP="+$("#IP").val(),"支付標準 資料");
 			$("#tm_on").val('執行'+d); 
 		});
 		
 		$("#final").on("click",function(){
 			var d = new Date();
 			window.open("bestchoice_ajax/final.php?IP="+$("#IP").val(),"資料整合");
 			$("#final_on").val('執行'+d); 
 		});

 		$("#final2").on("click",function(){
 			var d = new Date();
 			window.open("bestchoice_ajax/final2.php?IP="+$("#IP").val(),"療程日期與開始日填入");
 			$("#final2_on").val('執行'+d); 
 		});

 		$("#drugdeal").on("click",function(){
 			var d = new Date();
 			window.open("bestchoice_ajax/drugdeal.php?IP="+$("#IP").val(),"資料整合");
 			$("#drugdeal_on").val('執行'+d); 
 		});

 		$("#charge").on("click",function(){
 			var d = new Date();
 			window.open("bestchoice_ajax/createCharge.php?IP="+$("#IP").val()+"&dt="+$("#DT").val(),"charge");
 			$("#charge_on").val('執行'+d); 
 		});

 		$("#FdiLen").on("click",function(){
 			var d = new Date();
 			window.open("bestchoice_ajax/treat_FDI_len.php?IP="+$("#IP").val(),"資料整合");
 			$("#FdiLen_on").val('執行'+d); 
 		});


 	});

</script>