
<?php
require_once "predis/autoload.php";
include "constants.php";
Predis\Autoloader::register();
$product_id = $_GET['id'];

class RedisResult
{
    public function get_redis_client($redis_scheme, $redis_host, $redis_port, $redis_db)
    {
        $redis_client = new Predis\Client(array(
            "scheme" => $redis_scheme,
            "host"   => $redis_host,
            "port"   => $redis_port,
            "db"     => $redis_db));
        return $redis_client;

    }
    public function get_detail_from_redis($product_id)
    {
        $redis = $this->get_redis_client(REDIS_SCHEME, REDIS_HOST, REDIS_PORT, REDIS_DB);
        try
        {
            $detail = $redis->hget(REDIS_HASH, $product_id);
            $detail = str_replace("u'", "", $detail);
            $detail = str_replace("'", "", $detail);
            $detail = str_replace(": ,", "", $detail);
            $detail = str_replace("http://", "", $detail);
            $detail = str_replace("https://", "", $detail);
            /*$detail=str_replace(":","=>",$detail);
            $detail=substr($detail,1);*/
            $detail = str_replace(",", "\n", $detail);
            echo nl2br($detail);
        }
        catch (Exception $e)
        {
            echo "Couldn't connected to Redis";
            echo $e->getMessage();
            // get data from mysql db
            $this->get_detail_from_mysql($product_id, MYSQL_DB_SERVER, MYSQL_DB_USER, MYSQL_DB_PASS, MYSQL_DB, MYSQL_DB_TABLE);

        }


    }
    public function get_detail_from_mysql($product_id, $db_host, $db_user, $db_pass, $db, $db_table)
    {

        $mysql_client = mysql_connect($db_host, $db_user, $db_pass);

        if(! $mysql_client )
        {
            die('Could not connect: ' . mysql_error());
        }
        echo nl2br("\n");
        echo nl2br("\n");
        echo "get result from mysql";
        echo nl2br("\n");
        echo nl2br("\n");
        mysql_select_db($db);
        $make_query   = "SELECT * FROM $db_table where id = '$product_id'";
        $fetch_details =  mysql_query($make_query);
        $fetch_details_in_array = mysql_fetch_array($fetch_details);
        for ($i=1; $i<count($fetch_details_in_array); $i+=1)
        {
            echo $fetch_details_in_array[$i];
            echo nl2br("\n");


        }

    }

}
$client_redis= new RedisResult();
$client_redis->get_detail_from_redis($product_id);

?>
<!DOCTYPE html>
<html>	
<head>
</head>
<body>
</body>
</html>