<?php
$data = array($_POST['fname'],$_POST['lname'],$_POST['username'],$_POST['email'],$_POST['password'],$_POST['conf_password']); 
$after_json = json_encode($data);
#echo $after_json."<br>";
$output=shell_exec("py C:\wamp64\www\Mongoredis_project\S2.py ".$after_json);
#$output=shell_exec("py C:\wamp64\www\Mongoredis_project\register.py ".$after_json);


//print_r($output);

if($output==1)
{
	echo "You'll be redirect to the login page in 10 seconds";
	header("refresh:10, url=login.html");
	exit();
}
else
	print_r($output);
?>