<?php
#Receiving the data sent by post method
$user=$_POST['username'];
$psd=$_POST['password'];

#encoding the data in json type
$data = array($user,$psd); 
$after_json = json_encode($data);

#Calling the python script
$output=shell_exec("py C:\wamp64\www\Mongoredis_project\login.py ".$after_json);

echo $output;
#If the first character sent back is equal to 1 => the user is OK 
if($output[0]==1)
{
	#This command opens the file of the global variables that are stored in the $_SESSION array
	session_start();
	$_SESSION['username'] = $user;
	$_SESSION['id']=substr($output, 2);
	#Calling the next php page in order to display the conversations
	#header("Location: Conversation.php");
}
else 
	echo "Invalid credentials";
?>