
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
<!-- ####################################################### some of html BC #######################################################-->
<html>
<head>
    <!--<title><?php echo $detail[title]?></title>
    <H1><?php echo $detail[title]?></H1>-->
</head>
<body>
</body>
</html>