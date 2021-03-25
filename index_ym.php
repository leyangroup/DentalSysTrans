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
			<H2 style="text-align: center;">耀美預約與自費資料轉換至樂晴</H2>
			<h2></h2>
			<h4>資料路徑：<input type="text" id="path" value='c:\ym'></h4>
			<div><label></label></div>

			<h4 style="color:blue">請依序按鈕執行</h4>
			<div>
				<button type='button' class="btn btn-info" id="appointment">1.預約</button>
				<input type='text' id='appointment_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>
			<div><label></label></div>
			<div>
				<button type='button' class="btn btn-info" id="oe">2.自費</button>
				<input type='text' id='oe_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>
			<div><label></label></div>
			
			<div>
				<button type='button' class="btn btn-info" id="consolidate">3.彙整</button>
				<input type='text' id='consolidate_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>
			<div><label></label></div>
		</table>
	</form>
</body>
</html>

<script type="text/javascript" src="include/jquery.min.js"></script>
<script type="text/javascript">
 	$(document).ready(function(){
 		$("#appointment").on("click",function(){
 			var d = new Date();
 			window.open("ym_ajax/appointment.php?path="+$("#path").val(),"預約");
 			$("#appointment_on").val('執行'+d); 
 		});
 		$("#oe").on("click",function(){
 			var d = new Date();
 			window.open("ym_ajax/payment.php?path="+$("#path").val(),"自費");
 			$("#oe_on").val('執行'+d); 
 		});
		
		$("#consolidate").on("click",function(){
 			var d = new Date();
 			window.open("ym_ajax/consolidate.php?path="+$("#path").val(),"彙整");
 			$("#oe_on").val('執行'+d); 
 		});

 		
 	});

</script>