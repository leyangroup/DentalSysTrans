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
			<h2 style="text-align: center;">轉換醫聖資料</h2>
			<h2></h2>
			<h4>當天日期：<input type="text" id="DT" value=<?php echo $dt;  ?>>(ex.2019-10-03)日期會與預約轉入的資料有關</h4>
			<h4>程式路徑：<input type="text" id="path" value='c:\PTDat'></h4>
			<h4>舊圖檔路徑：<input type="text" id="pathImg" value='c:\PTDat\Img' style="width: 500px;"></h4>
			<h4>圖檔從第幾筆開始轉：<input type="text" id="start" value='1'> (每次轉500筆)</h4>
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
			<div><label></label></div>

			<div>
				<button type='button' class="btn btn-info" id="patient">2.患者 資料轉換</button>
				<input type='text' id='pt_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>
			<div><label></label></div>

			<div>
				<button type='button' class="btn btn-info" id="register">3.掛號 資料轉換</button>
				<input type='text' id='reg_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>
			<div><label></label></div>

			<div>
				<button type='button' class="btn btn-info" id="cc">4.主訴 資料轉換</button>
				<input type='text' id='cc_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>
			<div><label></label></div>

			<div>
				<button type='button' class="btn btn-info" id="tm">5.處置範本 資料轉換</button>
				<input type='text' id='tm_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>
			<div><label></label></div>

			<div>
				<button type='button' class="btn btn-info" id="treat">6.處置明細 資料轉換</button>
				<input type='text' id='treat_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>
			<div><label></label></div>

			<div>
				<button type='button' class="btn btn-info" id="image">7.病歷圖檔 資料轉換</button>
				<input type='text' id='img_on' style="color:blue" readonly value='請按鈕' size=50>
			</div>
			<div><label></label></div>

		</div>
	</form>
</body>
</html>

<script type="text/javascript" src="include/jquery.min.js"></script>
<script type="text/javascript">
 	$(document).ready(function(){
 		$("#idx").on("click",function(){
 			var d = new Date();
 			window.open("pt_ajax/create_index.php?path="+$("#path").val(),"建立索引");
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

 		$("#register").on("click",function(){
 			var d = new Date();
 			window.open("pt_ajax/register.php?path="+$("#path").val(),"掛號 基本資料");
 			$("#reg_on").val('執行'+d); 
 		});


 		$("#cc").on("click",function(){
 			var d = new Date();
 			window.open("pt_ajax/cc.php?path="+$("#path").val(),"主訴 基本資料");
 			$("#cc_on").val('執行'+d); 
 		});

 		$("#tm").on("click",function(){
 			var d = new Date();
 			window.open("pt_ajax/treatment.php?path="+$("#path").val(),"處置範本 基本資料");
 			$("#tm_on").val('執行'+d); 
 		});


 		$("#treat").on("click",function(){
 			var d = new Date();
 			window.open("pt_ajax/treat.php?path="+$("#path").val(),"處置明細 基本資料");
 			$("#treat_on").val('執行'+d); 
 		});

 		$("#image").on("click",function(){
 			var d = new Date();
 			window.open("pt_ajax/image_record.php?path="+$("#path").val()+"&imagePath="+$("#pathImg").val()+"&start="+$("#start").val());
 			$("#img_on").val('執行'+d);
 		});

 	});

</script>