<?php
#creating an array containing all the data from the form that is now stored in the $_POST global array
#this new array is to be sent to the python script
$data = array($_POST['fname'],$_POST['lname'],$_POST['username'],$_POST['email'],$_POST['password'],$_POST['conf_password']); 
$after_json = json_encode($data);
//sending the data to the python script
//!! This path must be changed in order to run the application on another machine
$output=shell_exec("py C:\wamp64\www\Mongoredis_project\S2.py ".$after_json);

//the value '1' is an equivalent of "The user is correct and was injected in the database"
if($output==1)
{
	echo "You'll be redirect to the login page in 10 seconds";
	//the header() function is used to redirect this page to a new url; the refresh attribute indicate the latency of this action
	header("refresh:10, url=login.html");
	exit();
}
else
	//in case of an inconsistency, we will display the message error given by the python script
	print_r($output);
?>