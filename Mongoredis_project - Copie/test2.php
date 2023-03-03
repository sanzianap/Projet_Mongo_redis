<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<?php 
//session_start();
//$id=$_SESSION['id'];
$id=2;
require 'C:/wamp64/www/Mongoredis_project/vendor/autoload.php';

use GuzzleHttp\Client;
$client = new \GuzzleHttp\Client(array( 'curl' => array( CURLOPT_SSL_VERIFYPEER => false, ), ));
$u = $client->get('https://63ef781c4d5eb64db0ca0583.mockapi.io/user',[
    'auth' => ['user', 'pass']
]);
$m = $client->get('https://63ef781c4d5eb64db0ca0583.mockapi.io/conversation',[
    'auth' => ['user', 'pass']
]);
#echo $res->getStatusCode();           // 200
#echo $res->getBody();
$users=$u->getBody();                 // {"type":"User"...' 
$users=(array)json_decode($users);
$msgs=$m->getBody();
$msgs=(array)json_decode($msgs);

$_GET['username']='sprichici91';
if (isset($_GET['username']))
{
	foreach ($users as $usr)
	{
		$usr=(array)$usr;
		if($usr['username']==$_GET['username'])
		{
			$receiver=$client->request('GET', 'https://63ef781c4d5eb64db0ca0583.mockapi.io/user', ['query' => ['id' => $usr['id']]]);
			$receiver=json_decode($receiver->getBody());
			$receiver=(array)$receiver[0];
		}
	}
}
echo $id.' '.$receiver['id'].'<br>';
if (isset($_GET['username']))
{
	foreach($msgs as $elem) 
		{
			$m=(array)$elem; 
			echo 'MESS '.$m['Sender'].' '.$m['Receiver'].'<br>';
			if($m['Sender']==$id && $m['Receiver']==$receiver['id'] || $m['Sender']==$receiver['id'] && $m['Receiver']==$id) 
			{	
				foreach($m['Messages'] as $ms) 
				{
					$ms=(array)$ms; 
					if($ms['user']==$id) 
					{
						echo '<div>'.$ms['text'].'</div>';
					} 
					else {
						echo '<div style=\'text-align: end;\'>'.$ms['text'].'</div>';
					     }
				}
			}
		}
}
?>
</body>
</html>