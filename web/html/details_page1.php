
<?php
require "predis/autoload.php";
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
            $find_index = strpos($detail,'feature');
            $detail_without_features=substr($detail,1,$find_index-1);
            $detail_without_features= explode(", ",$detail_without_features);
            $product_details=array();
            for ($i=0;$i<count($detail_without_features);$i=$i+1)
            {
                $temp=$detail_without_features[$i];
                $temp=explode(":",$temp);
                $product_details[$temp[0]]= substr($temp[1],1);
                array_push($feature_array,$temp);

            }
            $detail = substr($detail,$find_index+10);
            $detail = str_replace("},", " {", $detail);
            $detail = str_replace("}", "", $detail);
            $detail=explode(" { ",$detail);
            for ($i=0;$i<count($detail);$i=$i+2)
            {
                $product_details[$detail[$i]]=$detail[$i+1];
            }
            return $product_details;
        }
        catch (Exception $e)
        {
            echo "Couldn't connected to Redis";
            echo $e->getMessage();
            // get data from mysql db
            return $this->get_detail_from_mysql($product_id, MYSQL_DB_SERVER, MYSQL_DB_USER, MYSQL_DB_PASS, MYSQL_DB, MYSQL_DB_TABLE);

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
        $product_details=array();
        $product_details['name']=$fetch_details_in_array[2];
        $product_details['brand']=$fetch_details_in_array[3];
        $product_details['price']=$fetch_details_in_array[4];
        $product_details['link']=$fetch_details_in_array[5];
        $product_details['i_link']=$fetch_details_in_array[6];
        $product_details['ram']=$fetch_details_in_array[8];
        $product_details['os']=$fetch_details_in_array[9];
        $detail=$fetch_details_in_array[7];
        $detail = str_replace("u'", "", $detail);
        $detail = str_replace("'", "", $detail);
        $detail = str_replace(": ,", "", $detail);
        $detail = str_replace("http://", "", $detail);
        $detail = str_replace("https://", "", $detail);
        $find_index = strpos($detail,"},");
        $product_details['Warranty:']=substr($detail,12,$find_index-12);
        $detail = str_replace("},", " {", $detail);
        $detail = str_replace("}", "", $detail);
        $detail=explode(" { ",$detail);
        for ($i=0;$i<count($detail);$i=$i+2)
        {
            $product_details[$detail[$i]]=$detail[$i+1];
        }
        return $product_details;
    }

}
$client_redis= new RedisResult();
$details=$client_redis->get_detail_from_redis($product_id);
?>
<!DOCTYPE html>
<html><head></head>
<body>
<div style="float:left; width:30%;">
    <img src="<?php echo $details['i_link'];?>" style="width:180px;height:400px">
</div>
<div style="float:left; width:70%;">
    <h4>Name: </h4>
    <?php echo $details['name'];?><br>
    <h4>price: </h4>
    <?php echo $details['price'];?><br>
    <h4>brand: </h4>
    <?php echo $details['brand'];?><br>
    <h4>operating system: </h4>
    <?php echo $details['os'];?><br>
    <h4>Ram: </h4>
    <?php echo $details['ram']." GB";?><br>
    <a href="<?php echo $details['link'] ?>"><H2>buy now</H2></a>
</div>
<br><br><br><br><br><br><br><br>

<B>GENERAL FEATURES  </B><br>
<?php echo nl2br($details['GENERAL FEATURES:']);?>
<br><br>


<B>CAMERA  </B><br>
<?php echo nl2br($details['Camera:']);?>
<br><br>


<B>Multimedia  </B><br>
<?php echo nl2br($details['Multimedia:']);?>
<br><br>


<B>Internet and connectivity  </B><br>
<?php echo nl2br($details['Internet & Connectivity:']);?>
<br><br>


<B>Others Features   </B><br>
<?php echo nl2br($details['Other Features:']);?>
<br><br>


<B>Display  </B><br>
<?php echo nl2br($details['Display:']);?>
<br><br>


<B>Dimensions </B><br>
<?php echo nl2br($details['Dimensions:']);?>
<br><br>


<B>Warranty </B><br>
<?php echo nl2br($details['Warranty:']);?>
<br><br>


<B>Battry </B><br>
<?php echo nl2br($details['Battery:']);?>
<br><br>


<B>MEMORY </B><br>
<?php echo nl2br($details['Memory and Storage:']);?>
<br><br>


<B>Platform </B><br>
<?php echo nl2br($details['Platform:']);?>
<br><br>
</body>
</html>
