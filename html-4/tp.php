<?php
require "predis/autoload.php";
Predis\Autoloader::register();


$redis_client = new Predis\Client(array(
             "scheme" => "tcp",
             "host"   => "localhost",
             "port"   => 6397,
             "db"     => 0));
$x= $redis_client->hgetall("test123");
var_dump($x);
?>