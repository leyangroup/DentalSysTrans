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
	<title>系統轉換1.0.7</title>
	<link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
	<div class="row">
	<form class="form-horizontal" role="form" id="main" name="main" >
		<div class="col-md-2 column">
		</div>
		<div class="col-md-6 column">
			<H2 style="text-align: center;">轉換醫聖資料</H2>
			<h2></h2>
			<h4>當天日期：<input type="text" id="DT" value=<?php echo $dt;  ?>>(ex.2019-10-03)日期會與預約轉入的資料有關</h4>
			<h4>程式路徑：<input type="text" id="path" value='c:\PTDat'></h4>
			<div><label></label></div>
			<h1>無需同步醫師資料至leconfig,程式已經直接將資料轉至樂易智</h1>

			<h2 style="color:red">一定要先產生申報才可以轉檔哦!!</h2>
			<h4 style="color:blue">請依序按鈕執行</h4>
			<div>
				<button type='button' class="btn btn-info" id="idx">0.建立索引(若重複轉入，只需執行一次)</button>
				<input type='text' id='idx_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>
			<div><label></label></div>

			<div>
				<button type='button' class="btn btn-info" id="dr">1.醫師 資料轉換</button>
				<input type='text' id='dr_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>

			<div>
				<button type='button' class="btn btn-info" id="patient">2.患者 資料轉換</button>
				<input type='text' id='pt_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>
			<div><label></label></div>

		
		</table>
	</form>
</body>
</html>

<script type="text/javascript" src="include/jquery.min.js"></script>
<script type="text/javascript">
 	$(document).ready(function(){
 		$("#idx").on("click",function(){
 			var d = new Date();
 			window.open("pt_ajax/create_Index.php?path="+$("#path").val(),"建立索引");
 			$("#idx_on").val('執行'+d); 
 		});

 		$("#dr").on("click",function(){
 			var d = new Date();
 			window.open("pt_ajax/dr.php?path="+$("#path").val(),"醫師資料");
 			$("#dr_on").val('執行'+d); 
 		});
 		
 		$("#patient").on("click",function(){
 			var d = new Date();
 			window.open("pt_ajax/patient.php?path="+$("#path").val(),"患者基本資料");
 			$("#pt_on").val('執行'+d); 
 		});

 	});

</script>