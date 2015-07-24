<?php
/**
* 
*/
include("constants.php");
require 'vendor/autoload.php';
class Elastic_search //extends AnotherClass
{
	
	public $ini_query="";// initial search query which will be maintained after applying filter
    public $ret_array=array(); // global array for storing  arrays of results]
    public $s_brand=array();
    public $s_range=array();
	//public $user_query;
	/*public $bname;
	public $range_array */
	
    public function user_query_search($user_query)
    {
    	$searchParams = array();
        $searchParams['index'] = INDEX;
        $searchParams['type']  = TYPE;
        $searchParams['body']['query']['match']['title'] = $user_query;
        $searchParams['size']=SIZE;
        return $searchParams;
    }
    public function check_operation()
    {
    	global $ret_array;
    	global $ini_query;
    	global $s_brand;
    	global $s_range;

    	if (isset($_POST['search']))
    	{ 

	        $user_query=$_POST["search"];
	        
	        $ini_query=$user_query;
	        $searchParams=$this->user_query_search($user_query); 
	        //echo $user_query;
	        //die();
	        $ret_array=$this->Elastic_search_common($searchParams);
	        //var_dump($ret_array);


    	}
	    else
	    {
	        $bname=array();
	        $range_array=array();
	        $ini_query=$_GET['initial_query'];
	        print_r($_get['selected_brand']);
	        //$s_brand=$_GET['selecte'];
	        //var_dump($s_brand);
	        //die();
	        /*$s_range=$_GET['selected'];
	        /*$bname=$s_brand;
	        $range_array=$s_range;*/
	        //var_dump($_GET['brand_name']);
	        //die();
	        //array_push($bname,)

	       /* if($s_brand)
	        {
		        $bname=$bname+$s_brand;
		        var_dump($brand_name);

	    	}*/
	    	/*print_r($_GET['selecte']);
	    	foreach($_GET['selecte'] as $temp)
	        {
	    	var_dump($temp);

	    	
	    	}//die();
	    	*/

	        //$range_array=$range_array+$s_range;


	        foreach($_GET['brand_name'] as $temp)
	        {
	            array_push($bname,$temp);
	        }
	        
	        foreach ($_GET['price_range'] as $temp) 
	        {
	        	//echo $temp;
	            $filter=array();
	            switch ($temp) {
	                case ($temp>=0&&$temp<10000):
	                    $lower_limit=0;
	                    $upper_limit=10000;
	                    break;
	                case ($temp>=10000&&$temp<20000):
	                    $lower_limit=10000;
	                    $upper_limit=20000;
	                    break;
	                case ($temp>=20000&&$temp<35000):
	                    $lower_limit=20000;
	                    $upper_limit=35000;
	                    break;
	                case ($temp>=35000&&$temp<50000):
	                    $lower_limit=35000;
	                    $upper_limit=50000;
	                    break;
	                case ($temp>=50000):
	                    $lower_limit=50000;
	                    $upper_limit=1500000;
	                    break;
	                
	                default:
	                    $lower_limit=0;
	                    $upper_limit=0;
	                    break;
	            }
	            $filter["range"]['price']['gte']=$lower_limit;
	            $filter["range"]['price']['lt']=$upper_limit;
	            array_push($range_array, $filter);
	           
	        }
	        $s_brand=$bname;
	        $s_range=$range_array;
	        $searchParams=$this->Filtered_Query($bname,$ini_query,$range_array);
	        $ret_array=$this->Elastic_search_common($searchParams); 
	        //print_r($_GET);
	        //var_dump($s_brand);
	        //die();
    	}
    }
    public function Filtered_Query($bname,$ini_query,$range_array)
    {
    	$searchParams = array();
        $searchParams['index'] = INDEX;
        $searchParams['type']  = TYPE;
        $query=array();
        $must_array=array();
        
        if ($bname) 
        {
             $query['terms']['brand']=$bname;
             array_push($must_array,$query);
        }
        $filter=array();
        $filter['bool']['should']=$range_array;
        
        array_push($must_array,$filter);
        $searchParams['body']['query']['filtered']['filter']['bool']['must']=$must_array;
        $searchParams['body']['query']['filtered']['query']['match']['title']=$ini_query;
        $searchParams['body']['size']=SIZE;

        //var_dump(json_encode($searchParams));
        //die(3);
        return $searchParams;
    }
    public function Elastic_search_common($searchParams)
    {
        $client = new Elasticsearch\Client(); // create a elastcsearch client
        $retDoc = $client->search($searchParams);//echo json_encode($retDoc);
        $retDoc=$retDoc[hits][hits];
        for ($i=0;$i<10;$i=$i+1)
        {
            $table_array[$i]=$retDoc[$i];
        }
        for ($i=0;$i<count($retDoc);$i=$i+1)
        {    
            $temp=$retDoc[$i][_source][brand];
            $temp1=$retDoc[$i][_source][price];
            if(!array_key_exists($temp, $brand_list_count))
            {
                $brand_list_count[$temp]=0;
            }
            // switch case  for hardcoded price  range count
            switch ($temp1) 
            {
                case ($temp1<10000):
                    $price_range[0]+=1;
                    break;
                case ($temp1<20000&&$temp1>=10000):
                    $price_range[1]+=1;
                    break;
                case ($temp1<35000&&$temp1>=20000):
                    $price_range[2]+=1;
                    break;
                case ($temp1<50000&&$temp1>=35000):
                    $price_range[3]+=1;
                    break;
                case ($temp1>=50000):
                    $price_range[4]+=1;
                    break;
                default:
                    $price_range[5]+=1;
                    break;
            }
            // end of switch
            $brand_list_count[$temp]+=1;
        }
        return array($table_array,$brand_list_count,$price_range);

    }
	/*function __construct(argument)
	{
		# code...
	}*/
}

$es_client=new Elastic_search();
$es_client->check_operation();
//$es_client->echo_test();
//$ret_arra=$es_client->$ret_array;
//echo $ret_arra;
?>