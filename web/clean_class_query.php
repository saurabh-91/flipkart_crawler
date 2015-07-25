<?php
/**
* 
*/
include("constants.php");
require 'vendor/autoload.php';

class SearchElastic //extends AnotherClass
{
	
	public $ini_query="";// initial search query which will be maintained after applying filter
    public $ret_array=array(); // global array for storing  arrays of results]
    public $s_brand=array();
    public $s_range=array();
 

    public function user_query_search($user_query_string)
    {
    	global $ini_query;
    	$ini_query=$user_query_string;
    	$searchParams = array();
        $searchParams['index'] = INDEX;
        $searchParams['type']  = TYPE;
        $searchParams['body']['query']['match']['title'] = $user_query_string;
        $searchParams['size']=SIZE;
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
    public function brand_filter_builder($selected_brand_name)
    {
    	$bname = array();
	    
	    foreach($selected_brand_name as $temp)
	    {
	        array_push($bname,$temp);
	    }
        $brand_filter          = array();
        $must_array            = array();
        
        if ($bname) 
        {
             $brand_filter['terms']['brand'] = $bname;
             array_push($must_array, $brand_filter);
        }
        //var_dump(json_encode($must_array));
        return $brand_filter;
    }
    public function range_filter_builder($selected_price_range)
    {
    	$range_array=array();
    	 foreach ($selected_price_range as $temp) 
	        {
	   
	            $filter = array();
	            switch ($temp)
	            {
	                case ($temp >= 0 && $temp < 10000):
	                    $lower_limit = 0;
	                    $upper_limit = 10000;
	                    break;
	                case ($temp >= 10000 && $temp < 20000):
	                    $lower_limit = 10000;
	                    $upper_limit = 20000;
	                    break;
	                case ($temp >= 20000 && $temp < 35000):
	                    $lower_limit = 20000;
	                    $upper_limit = 35000;
	                    break;
	                case ($temp >= 35000 && $temp < 50000):
	                    $lower_limit = 35000;
	                    $upper_limit = 50000;
	                    break;
	                case ($temp >= 50000):
	                    $lower_limit = 50000;
	                    $upper_limit = 1500000;
	                    break;
	                
	                default:
	                    $lower_limit = 0;
	                    $upper_limit = 0;
	                    break;
	            }
	            $filter["range"]['price']['gte'] = $lower_limit;
	            $filter["range"]['price']['lt']  = $upper_limit;
	            array_push($range_array, $filter);
	           
	        }

    	$range_filter                   = array();
        $range_filter['bool']['should'] = $range_array;
        $must_array                     = array();
        array_push($must_array, $range_filter);
        return $range_filter;
       
    }
    public function filtered_query($selected_brand_name, $ini_query, $selected_price_range)
    {
    	// call brand filter builder
    	// call range filter builder
    	$searchParams          = array();
        $searchParams['index'] = INDEX;
        $searchParams['type']  = TYPE;
    	
    	$must_array=array();

        $brand_filter = $this->brand_filter_builder($selected_brand_name);
        $range_filter = $this->range_filter_builder($selected_price_range);
        // combine all the filtered in must array
        array_push($must_array, $brand_filter);
        array_push($must_array, $range_filter);
        $searchParams['body']['query']['filtered']['filter']['bool']['must']  = $must_array;
        $searchParams['body']['query']['filtered']['query']['match']['title'] = $ini_query;  //initial user query builder
        $searchParams['body']['size']										  = SIZE;
        //making of searchable json done

        //var_dump(json_encode($searchParams));
        //die(3);
        //return $searchParams;

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

 }
?>