
<?php
#require 'vendor/autoload.php';
require_once "predis/autoload.php";
//use \Predis;
Predis\Autoloader::register();
$id = $_GET['id'];
try {
    $redis = new Predis\Client(array(
        "scheme" => "tcp",
        "host" => "127.0.0.1",
        "port" => 6379,
        "db"=>0));

    $detail=$redis->hget("flipkart_hash",$id);
    $detail=str_replace("u'","",$detail);
    $detail=str_replace("'","",$detail);
    $detail=str_replace(": ,","",$detail);
    $detail=str_replace("http://","",$detail);
    $detail=str_replace("https://","",$detail);
    /*$detail=str_replace(":","=>",$detail);
   $detail=substr($detail,1);*/
   $detail=str_replace(",","\n",$detail);
   echo nl2br($detail);
}
catch (Exception $e) {
    echo "Couldn't connected to Redis";
    echo $e->getMessage();
}
//$detail=$r.HGET('flip_hash',$id);
//var_dump($detail);
//echo $id;


   // echo "               start_bc   ";
/*
$mstr = explode(",",$detail);
$a = array();
foreach($mstr as $nstr )
{
    $narr = explode("=>",$nstr);
//$narr[0] = str_replace("\x98","",$narr[0]);
$ytr[1] = $narr[1];
$a[$narr[0]] = $ytr[1];
}
print_r($a);

/* $detail=substr($detail,-1);
$array = explode(',',$detail);
$new_array = array();
//$value=explode(' : ',$array);
//die(3);
var_dump($array);

foreach ($array as $value)
{
    $v=explode(': ',$value);
    $new_array[$v[0]]=$v[1];
}
//echo $new_array["'Model ID'"]."                                                        ";
var_dump($new_array);


*/



    //parse_str($detail);
    //echo $link; 
    //$string=substr($detail,28);//$string=substr($string,-3);
    //echo $link;
?>
<!DOCTYPE html>
<html>	
<head>
</head>
<body>
</body>
</html>