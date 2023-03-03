<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
   <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<style>
.vertical-menu {
  overflow-y: auto;
  width: 35%; 
  float:left; 
  height:100vh;
  background-color: red;
}
.vertical-menu a {
  background-color: #eee;
  color: black;
  display: block;
  padding: 12px;
  text-decoration: none;
}
.vertical-menu a:hover {
  background-color: #ccc;
}
.vertical-menu a.active {
  background-color: #04AA6D;
  color: white;
}

.user{
	display: flex; 
	justify-content: flex-end;
	height:40px;
	background-color: #FBD603;
}

.conversations{
	background-color: #FBD603;
	width: 100%;
	height: 30px;
}
input.conversations:hover{
background-color: blue;
cursor: pointer;
}

.dropbtn {
  background-color: #04AA6D;
  color: white;
  padding: 16px;
  font-size: 16px;
  border: none;
}
.dropdown {
  position: relative;
  display: inline-block;
}
.dropdown-content {
  display: none;
  position: absolute;
  background-color: #f1f1f1;
  min-width: 160px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
}
.dropdown-content a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
}
.dropdown-content a:hover {background-color: #ddd;}
.dropdown:hover .dropdown-content {display: block;}
.dropdown:hover .dropbtn {background-color: #3e8e41;}
</style>
</head>
<body onload="display()">
<?php 
//we recuperate the id of the authenticated user
session_start();
$id=$_SESSION['id'];

//if it is a new conversation or if we are on the root /Conversation.php, the id of the conversation is 0
if(isset($_GET['new_conversation']) || !(isset($_GET['new_conversation'])||isset($_GET['new_username']) || isset($_GET['username'])))
	$_SESSION['id_conv']=0;

require 'C:/wamp64/www/Mongoredis_project/vendor/autoload.php';

//I used the Guzzle client in order to establish the connection to the API 
use GuzzleHttp\Client;
$client = new \GuzzleHttp\Client(array( 'curl' => array( CURLOPT_SSL_VERIFYPEER => false, ), ));
$u = $client->get('https://63ef781c4d5eb64db0ca0583.mockapi.io/user',[
    'auth' => ['user', 'pass']
]);
$m = $client->get('https://63ef781c4d5eb64db0ca0583.mockapi.io/conversation',[
    'auth' => ['user', 'pass']
]);

//creating an array of users; the getBody() function returns an output type object (std class), that's why we need the conversion
$users=$u->getBody();                
$users=(array)json_decode($users);

//creating an array of conversations
$msgs=$m->getBody();
$msgs=(array)json_decode($msgs);

//using the unique username, we will search for the id of the receiver in order to send it to the python script
//that will come in handy when we will create/update the conversations
foreach ($users as $usr)
	{
		$usr=(array)$usr;
		if((isset($_GET['username'])&& $usr['username']==$_GET['username']) || (isset($_GET['new_username'])&&$usr['username']==$_GET['new_username']) && !(isset($receiver)))
		{
			$receiver=$client->request('GET', 'https://63ef781c4d5eb64db0ca0583.mockapi.io/user', ['query' => ['id' => $usr['id']]]);
			$receiver=json_decode($receiver->getBody());
			$receiver=(array)$receiver[0];
			$_SESSION['id_conv']=0;
		}
	}
	
//we evalue the conversation as new if the "New conversation" button was pressed OR the new_user was chosen from the list
if(isset($_GET['new_conversation']) || isset($_GET['new_username']))
	$_SESSION['new_conv']=1;
else
	//in any other cases, we are not on a new conversation
	$_SESSION['new_conv']=0;

//if we were not previously on a specific conversation, but the username is now in the url or the conversation is new with an user already chosen
//we try to get the id of the conversation in order to pass it further to the python script
if($_SESSION['id_conv']==0 && (!isset($_GET['new_conversation']) && (isset($_GET['username']) || isset($_GET['new_username']))))
{
	foreach ($msgs as $m)
	{
		$m=(array)$m;
		if ($m['Sender']==$id && $m['Receiver']==$receiver['id'] || $m['Sender']==$receiver['id'] && $m['Receiver']==$id)
		{ $_SESSION['id_conv']=$m['id']; break; }
	}
}

//we will need the id of the receiver to send it to the python script
if(isset($receiver)) {$_SESSION['id_r']=$receiver['id'];}
?>
<div class="user" id="user"><?php  echo $_SESSION['username'];?></div>
<div style="width: 65%; float:right; background-color:#eee; height:100vh">
   <div id="receiver" style='width:50%; height:10%; visibility:visible'><?php if (isset($_GET['username'])) echo $_GET['username']; else {if (isset($_GET['new_username'])) echo $_GET['new_username'];}?></div>
   <div id="status" style="width:50%; height:5%; color:green; visibility:hidden"></div>
   <div id="conv" style="height:50%; visibility:visible; overflow:auto;">
   <div class="dropdown" style="visibility:hidden" id="list">
	<button class="dropbtn">Dropdown</button>
		<div class="dropdown-content">
				<form action="" method="GET">
					<?php 
					//we will display a list of possible users that don't already have a conversation opened with the authenticated user
					foreach($users as $u)
						{
							$flag=1;
							$u=(array)$u;
							//we exclude the authenticated user
							if($u['id']==str_replace("\n", "", $id))
								$flag=0;
							foreach ($msgs as $m)
							{
								if(!$flag) break;
								$m=(array)$m;
								//excluding the conversations that already exist
								if ($m['Sender']==$id && $m['Receiver']==$u['id'] || $m['Sender']==$u['id'] && $m['Receiver']==$id)
								{
									$flag=0; break;
								}
							}
							if($flag)  echo '<input class="conversations" type="submit" name="new_username" value="'.$u['username'].'"></input>';
						}	
					?>
				</form>
		</div>
	</div>
<script>
function send_msg(){
	//the function will be triggered by the "send" button in the conversation window
	
	//we recuperate the div that contains the conversation
	var conv=document.getElementById("conv");
	//here we access the input where we will find the new message typed
	var msg=document.getElementById("new_message");
	
	//we add the message in the conversation as a div
	conv.innerHTML +="<div style='text-align: end;'>"+msg.value+"</div>";
	
	//in order to hide the dropdown list of users (seen previously) 
	var list=document.getElementById("list");
	if(list)
		document.getElementById("list").style.visibility='hidden';	
	
	//I used AJAX in oder to send data from the client side to the server side by calling a php file that will communicate to python
	//the python script will update the database
	var emp={};
	emp.message=msg.value;
	console.log(emp);
	$.ajax({
		url:"php.php",
		method:"post",
		data:emp,
		success: function(res){ console.log(res);}
	})
	
	//we clear the input value
	msg.value='';
}
</script>
   </div>
   <div>
   <input id="new_message" style="height:20%; width:90%; visibility:hidden"></input>
   <button id="send" style="visibility:hidden" onclick="send_msg()">send</button> 
   </div>
<script>
function display(){
	var element = document.getElementById("receiver");
	
	//establishing the current status of the user
	var con='<?php if (isset($_GET["username"])||isset($_GET["new_username"])) {if (strtoupper($receiver["connected"])=="FALSE" || $receiver["connected"]==0) {echo "NOT CONNECTED";} else {echo "CONNECTED";}}?>';
	
	//if is set (a value exists in the div)
	if (element.innerHTML!='')
	{
		//we display the status that was previously hidden 
		document.getElementById("status").style.visibility='visible';
		document.getElementById("status").innerHTML=con;
		//setting the color by status
		if(con=="CONNECTED")
			document.getElementById('status').style.color = 'green';
		else
			document.getElementById('status').style.color = 'grey';
		//displaying the input and the send button
		document.getElementById("new_message").style.visibility='visible';
		document.getElementById("send").style.visibility='visible';
		//getting the actual conversation
		var conv=document.getElementById("conv");
		//clearing the div
		document.getElementById("conv").innerHTML=null;
		//refreshing the messages and align them in oreder to see which are form the (now)sender and which from the (now)receiver
		var nm="<?php if (isset($_GET['username'])){foreach($msgs as $elem) {$m=(array)$elem; if($m['Sender']==$id && $m['Receiver']==$receiver['id'] || $m['Sender']==$receiver['id'] && $m['Receiver']==$id) {foreach($m['Messages'] as $ms) {$ms=(array)$ms; if($ms['user']!=$id) {echo '<div>'.$ms['text'].'</div>';} else {echo '<div style=\'text-align: end;\'>'.$ms['text'].'</div>';}}}}}?>";
		//appending the updated conversation 
		conv.innerHTML +=nm;
	}
	//if we are on a new conversation, beside the dropdown list, we have to display the input and the send button
	var visib='<?php if($_SESSION["new_conv"]==1) echo "visible"; else echo "hidden";?>';
	if(visib=="visible")
	{
		document.getElementById("new_message").style.visibility=visib;
		document.getElementById("send").style.visibility=visib;
		if(document.getElementById("list"))
			document.getElementById("list").style.visibility=visib;
	}
}
</script>
</div>
<div class="vertical-menu">
	<form action="" method="GET">
		<input class="conversations" type='submit' name='new_conversation' value='New conversation'></input>
<?php
//Here we will filter the users in order to display a list of the pending conversations for the authenticated user
foreach($msgs as $elem)
{
	$m=(array)$elem;
	if($m['Sender']==$id || $m['Receiver']==$id)
	{
		if($m['Sender']==$id) {$usr_id=$client->request('GET', 'https://63ef781c4d5eb64db0ca0583.mockapi.io/user', ['query' => ['id' => $m['Receiver']]]);}
		if($m['Receiver']==$id) {$usr_id=$client->request('GET', 'https://63ef781c4d5eb64db0ca0583.mockapi.io/user', ['query' => ['id' => $m['Sender']]]);}
		$r2=new stdClass;
		$r2=json_decode($usr_id->getBody());
		if ($r2 !=null){
		$r=$r2['0'];
		$r=(array)$r;
		$status="document.getElementById('status').style.visibility = 'visible';";
		if($r['connected']=="False")
			$status=$status." document.getElementById('status').innerHTML='NOT CONNECTED'; document.getElementById('status').style.color = 'gray'; console.log(document.getElementById('status'));";
		else
			$status=$status." document.getElementById('status').innerHTML='CONNECTED'; document.getElementById('status').style.color = 'green';";
		$obj='<input class="conversations" type=\'submit\' name=\'username\' value='.$r['username'].'></input>';
		echo $obj;}
	}
}
?>
</div>
</body>
</html>