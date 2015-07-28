<?php
/**
* 
*/
include("constants.php");
require 'vendor/autoload.php';

class SearchElastic 
{
	
	public $ini_query = "";// initial search query which will be maintained after applying filter
// ######################################### only this function depends on user input ########################################################	 
    public function check_operation()//depent on user input which opretion to perform
    {
    	global $ini_query;
    	if (isset($_POST["search"]))
    	{ 	//call search function;
    		$user_query_string =  $_POST["search"];
    		$ret_array         =  $this->user_query_search($user_query_string);
    	}
	    else
	    {	//call filtered search function
	    	$ini_query            =  $_POST['initial_query'];
	    	$selected_brand_name  =  $_POST['brand_name'];
	    	$selected_price_range =  $_POST['price_range'];
	    	$ret_array            =  $this->filtered_query($selected_brand_name, $ini_query, $selected_price_range);
	    }
	    return $ret_array;
    }
// ###########################################################################################################################################

// ######################################### user query search function ###################################################################### 
    public function user_query_search($user_query_string)
    {
    	global $ini_query;
    	$user_query_string=trim($user_query_string);
    	$ini_query = $user_query_string;
    	$searchParams = array();
        $searchParams['index'] = INDEX;
        $searchParams['type']  = TYPE;
        if($user_query_string) // check condition for null search query
        {
        	$searchParams['body']['query']['match']['title'] = $user_query_string;
        }
        else
        {
        	$searchParams['body']['query']['match_all'] = new StdClass();
        }
        $searchParams['size'] = SIZE;
        var_dump(json_encode($searchParams));
        $client = new Elasticsearch\Client(); // create a elastcsearch client
        $retDoc = $client->search($searchParams);//echo json_encode($retDoc);
        $retDoc = $retDoc[hits][hits];
        for ($i=0; $i < 10; $i = $i+1)
        {
            $table_array[$i] = $retDoc[$i];
        }
        for ($i = 0; $i < count($retDoc); $i = $i+1)
        {    
            $temp  = $retDoc[$i][_source][brand];
            $temp1 = $retDoc[$i][_source][price];
            if(!array_key_exists($temp, $brand_list_count))
            {
                $brand_list_count[$temp] = 0;
            }
            // switch case  for hardcoded price  range count
            switch ($temp1) 
            {
                case ($temp1 < 10000):
                    $price_range[0]+= 1;
                    break;
                case ($temp1<20000&&$temp1>=10000):
                    $price_range[1]+= 1;
                    break;
                case ($temp1<35000&&$temp1>=20000):
                    $price_range[2]+= 1;
                    break;
                case ($temp1<50000&&$temp1>=35000):
                    $price_range[3]+= 1;
                    break;
                case ($temp1>=50000):
                    $price_range[4]+= 1;
                    break;
                default:
                    $price_range[5]+= 1;
                    break;
            }
            // end of switch
            $brand_list_count[$temp]+= 1;
        }
        return array($table_array, $brand_list_count, $price_range);

    }
// ###########################################################################################################################################

// ########################################## bulid brand filter #############################################################################
    public function brand_filter_builder($selected_brand_name)
    {
    	$bname = array();
	    
	    foreach($selected_brand_name as $temp)
	    {
	        array_push($bname, $temp);
	    }
        $brand_filter          = array();
        $must_array            = array();
        
        if ($bname) 
        {
             $brand_filter['terms']['brand'] = $bname;
        }
        return $brand_filter;
    }
// ###########################################################################################################################################

// ###########################################  build range filter ########################################################################### 
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
        return $range_filter;
       
    }
// ###########################################################################################################################################


// #####################################  build initial user search query ####################################################################
    public function initial_query_builder($ini_query)
    {
    	$initial_string=array();
    	if($ini_query) // check condition for null search query
        {
        	$initial_string['match']['title'] = $ini_query;
        }
        else
        {
        	$initial_string['match_all'] = new StdClass();
        }
        return $initial_string;
    }
// ###########################################################################################################################################

// ############################################## filtered query (for filtering user search) #################################################
    public function filtered_query($selected_brand_name, $ini_query, $selected_price_range)
    {
    	// call brand filter builder
    	// call range filter builder
    	// call initial search builder
        $brand_filter = $this->brand_filter_builder($selected_brand_name);
        $range_filter = $this->range_filter_builder($selected_price_range);
        $initial_query=$this->initial_query_builder($ini_query);
        // combine all the filtered in must array
        $must_array=array();

        if($brand_filter)
        {
        	array_push($must_array, $brand_filter);
        }
        
        array_push($must_array, $range_filter);



    	$searchParams          = array();
        $searchParams['index'] = INDEX;
        $searchParams['type']  = TYPE;
        $searchParams['body']['query']['filtered']['filter']['bool']['must']  = $must_array;
        $searchParams['body']['query']['filtered']['query']					  = $initial_query;  //initial user query builder
        $searchParams['body']['size']										  = SIZE;
        //making of searchable json done
        var_dump(json_encode($searchParams));
        $client = new Elasticsearch\Client(); // create a elastcsearch client
        $retDoc = $client->search($searchParams);//echo json_encode($retDoc);
        $retDoc = $retDoc[hits][hits];
        for ($i = 0; $i < 10; $i = $i+1)
        {
            $table_array[$i] = $retDoc[$i];
        }
        for ($i = 0; $i < count($retDoc); $i = $i+1)
        {    
            $temp  = $retDoc[$i][_source][brand];
            $temp1 = $retDoc[$i][_source][price];
            if(!array_key_exists($temp, $brand_list_count))
            {
                $brand_list_count[$temp]=0;
            }
            // switch case  for hardcoded price  range count
            switch ($temp1) 
            {
                case ($temp1<10000):
                    $price_range[0]+= 1;
                    break;
                case ($temp1<20000&&$temp1>=10000):
                    $price_range[1]+= 1;
                    break;
                case ($temp1<35000&&$temp1>=20000):
                    $price_range[2]+= 1;
                    break;
                case ($temp1<50000&&$temp1>=35000):
                    $price_range[3]+= 1;
                    break;
                case ($temp1>=50000):
                    $price_range[4]+= 1;
                    break;
                default:
                    $price_range[5]+= 1;
                    break;
            }
            // end of switch
            $brand_list_count[$temp]+= 1;
        }
        return array($table_array, $brand_list_count, $price_range);

    }
// ###########################################################################################################################################

 }
?>