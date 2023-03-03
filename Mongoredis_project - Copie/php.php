<?php
$msg=$_POST['message'];
session_start();
$id_s=$_SESSION['id'];
echo $id_s;
$id_s=str_replace("\n", "", $id_s);
$id_r=$_SESSION['id_r'];
$id_conv=$_SESSION['id_conv'];

$data = [$msg,$id_r,$id_s,$id_conv]; 
$after_json = json_encode($data);
echo $after_json."<br>";
$output=shell_exec("py C:\wamp64\www\Mongoredis_project\S3.py ".$after_json);
echo $output;
?>