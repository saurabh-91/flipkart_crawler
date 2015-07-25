<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body>


<?php
    require 'vendor/autoload.php';
    include "query.php";

    $client = new Elasticsearch\Client();
    $bname=$_POST['brand_name'];
    if (isset($bname))
    {
       brand_filter_function();
    }
    function brand_filter_function()
    {
        $searchParams = array();
        $searchParams['index'] = "flipkart";
        $searchParams['type']  = "mobile";
        $searchParams['body']['query']['match']['brand'] = $bname;
        $searchParams['size']=2000;
        return $searchParams;
    }


?>
</body>
</html>