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
	<title>系統轉換 1.1.2</title>
	<link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
	<div class="row">
	<form class="form-horizontal" role="form" id="main" name="main" >
		<div class="col-md-1 column">
		</div>
		<div class="col-md-6 column">
			<H2 style="text-align: center;">樂衍轉換管理系統</H2>
			<h2></h2>
			<h4 style="text-align: center;">日期：<input type="text" id="DT" value=<?php echo $dt;  ?>>(ex.2019-04-10)</h4>
			
			<table class="table table-striped" width='80%'>
				<tr>
					<td>
						<button class="btn btn-info" type='button' id="BestChoice">優勢 BestChoice</button>
						
						<button type='button' id="BestChoice_OD">補雙鄰接面OD資料(1080730)</button>
						
						<button type='button' id="BestChoice_AC">補預防保健類掛號(1080802)</button>
						
						<button type='button' id="BestChoice_cus">補 患者(1080802)</button>
					</td>
				</tr>	
			
				<tr>
					<td>
						<button class="btn btn-success" type='button' id="VISD">展望 VISD</button>
					</td>
				</tr>
				<tr>
					<td><button class="btn btn-info" type='button' id="COOPER">北昕 COOPER </button></td>
				</tr>
				<tr>
					<td><button class="btn btn-success" type='button' id="Angel" >梵谷 Angel</button></td>
				</tr>
				<tr>
					<td><button class="btn btn-success" type='button' id="dentall" >牙醫通 Dentall</button></td>
				</tr>
				<tr>
					<td><button class="btn btn-success" type='button' id="PT" >醫聖-全人物理治療所</button></td>
				</tr>
				<tr>
					<td><button class="btn btn-success" type='button' id="Lin" >林氏 wise</button></td>
				</tr>
				<tr>
					<td><button class="btn btn-success" type='button' id="zdent" >牙谷 zdent</button></td>
				</tr>
			</table>
		</div>
	</form>
</body>
</html>

<script type="text/javascript" src="include/jquery.min.js"></script>
<script type="text/javascript">
 	$(document).ready(function(){
 		$("#BestChoice").on("click",function(){
 			document.location.href="index_bestchoice.php?DT="+$("#DT").val();    
 		});
 		$("#VISD").on("click",function(){
 			document.location.href="index_visd.php";   
 		});

		$("#COOPER").on("click",function(){
 			document.location.href="index_cooper.php?DT="+$("#DT").val(); 
 		});

 		$("#Angel").on("click",function(){
 			document.location.href="index_angel.php?DT="+$("#DT").val(); 
 		});

 		$("#dentall").on("click",function(){
 			document.location.href="index_dentall.php?DT="+$("#DT").val(); 
 		});

 		$("#PT").on("click",function(){
 			document.location.href="index_PT.php?DT="+$("#DT").val(); 
 		});

 		$("#Lin").on("click",function(){
 			document.location.href="index_04.php?DT="+$("#DT").val(); 
 		});

 		$("#zdent").on("click",function(){
 			document.location.href="index_zdent.php?DT="+$("#DT").val(); 
 		});
 	});

</script>
