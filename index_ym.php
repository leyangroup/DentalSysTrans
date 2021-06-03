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
		<div class="col-md-10 column">
			<H2 style="text-align: center;">耀美預約與自費資料轉換至樂晴</H2>
			<h2></h2>
			<h3><font color='red'>一、資料由耀美提供，放在c:\ym下面</font> </h3>
			<h3><font color='red'>二、預約資料：改存檔名為 『appointment.csv』</font></h3>
			<h3><font color='red'>三、收款資料：檔名改為『oepayment.csv』</font></h3>
			<h4><font color='green'>   1.將檔案用excel打開, 要先替換掉換行符號, crtl+H,將游標放在『尋找目標』按下ctrl+J,『取代成』空白就好，選『全部取代』儲存  ：</font></h4>
			<h4><font color='green'>   2.用notePad++打開檔案，取代六個逗號，crtl+H,將游標放在『尋找目標』按下6個逗號,『取代成』空白就好，選『全部取代』儲存</font></h4>


			<!-- <h4>資料路徑：<input type="text" id="path" value='c:\ym'></h4> -->
			<div><label></label></div>

			<h4 style="color:blue">請依序按鈕執行</h4>
			<tr>
				<td><button type='button' id="create_index">建立索引，只要一次即可</button></td>
				<td><input type='text' id='index_on' style="color:blue" readonly value='請按鈕' size=30></td>
			</tr>

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
				<button type='button' class="btn btn-info" id="consolidate">3.彙整自費</button>
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
 		$("#create_index").on("click",function(){
 			var d = new Date();
 			window.open("ym_ajax/create_index.php","建立索引");
 			$("#index_on").val('執行'+d); 
 		});
 		$("#appointment").on("click",function(){
 			var d = new Date();
 			window.open("ym_ajax/appointment.php","預約");
 			$("#appointment_on").val('執行'+d); 
 		});
 		$("#oe").on("click",function(){
 			var d = new Date();
 			window.open("ym_ajax/payment.php","自費");
 			$("#oe_on").val('執行'+d); 
 		});
		
		$("#consolidate").on("click",function(){
 			var d = new Date();
 			window.open("ym_ajax/consolidate.php","彙整");
 			$("#oe_on").val('執行'+d); 
 		});

 		
 	});

</script>