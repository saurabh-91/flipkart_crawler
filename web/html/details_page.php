
<?php
#require 'vendor/autoload.php';
require "predis/autoload.php";
//use \Predis;
Predis\Autoloader::register();
$id = $_GET['id'];
echo $id;
//$r = new Predis\Client();
try {
    //$redis = new Predis\Client();
	//redis client connect parameters
    $redis = new Predis\Client(array(
        "scheme" => "tcp",
        "host" => "127.0.0.1",
        "port" => 6379,
        "db"=>0));

    //echo "Successfully connected to Redis";
    $detail=$redis->hget("flip_hash",$id);
    var_dump($detail);
   // echo "               start_bc   ";
    $string=substr($detail,28);
//$string=substr($string,-3);

$array = explode(',',$string);
//var_dump( $array);
$new_array = array();
//$value=explode(' : ',$array);
//die(3);
//var_dump($value);

foreach ($array as $value)
{
	$v=explode(': ',$value);
	//var_dump($v);
	//die(5);
	
	$v[0]=substr($v[0],2);
	//echo $v[0];
	$new_array[$v[0]]=$v[1];
}
//echo $new_array["'Model ID'"]."                                                        ";
//var_dump($new_array);
}
catch (Exception $e) {
    echo "Couldn't connected to Redis";
    echo $e->getMessage();
}
//$detail=$r.HGET('flip_hash',$id);
//var_dump($detail);
//echo $id;
?>
<!DOCTYPE html>
<html>	
<head>
</head>
<body>
</body>
</html>