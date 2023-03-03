<?php
#if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')   
#         $url = "https://";   
#    else  
#         $url = "http://";   
#    // Append the host(domain name, ip) to the URL.   
#    $url.= $_SERVER['HTTP_HOST'];   
#    
#    // Append the requested resource location to the URL   
#    $url.= $_SERVER['REQUEST_URI'];    
#      
#    echo $url;  
#echo "aici prima data";
#$post = file_get_contents('D:\\out.txt');
#if(isset($post))
#{
	#header("refresh: 5; url = $post");
	#$post=null;
#}
#$firstname = htmlspecialchars($_GET["firstname"]);
#$lastname = htmlspecialchars($_GET["lastname"]);
#$password = htmlspecialchars($_GET["password"]);
#echo "firstname: $firstname lastname: $lastname password: $password";
echo implode(" ", $_POST);
#foreach ($_SERVER as $parm => $value)  echo "$parm = '$value'\n";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $city = $_POST['city'];
	echo $name;
    
    // process the data
    // ...
    
    // send a response
    echo 'Data received successfully!';
}
?>
