<html>
   <title>Test Page</title>
   <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<body>
<div id="ok">LAURENTIU</div>
<div>
<script text="text/javascript">
	var emp={};
	emp.username=document.getElementById("ok").innerHTML;;
	console.log(emp);
	$.ajax({
		url:"php.php",
		method:"post",
		data:emp,
		success: function(res){ console.log(res);}
	})
</script>
</div></body>
</html>
