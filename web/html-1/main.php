<?php
include("constants.php");
require_once 'vendor/autoload.php';
class SearchElastic 
{
    public $initial_user_query;
// ######################################### get the elasticsearch index params #############################################################
	public function get_es_index_params($index, $type, $size)
   {
   		$searchParams = array();
        $searchParams['index'] = $index;
        $searchParams['type']  = $type;
        $searchParams['size']  = $size;
        return $searchParams;
   }
// ###########################################################################################################################################

// ######################################### only this function depends on user input ########################################################	 
    public function perform_es($user_query_string, $selected_brand_name, $iquery, $selected_price_range, $page_no, $selected_os_name, $selected_ram_range)//depent on user input which opretion to perform
    {
    	if (isset($user_query_string))
    	{ 	//call search function;
    		$retrive_result_array         =  $this->user_query_search($user_query_string);
    	}
	    else
	    {	//call filtered search function
	    	$retrive_result_array            =  $this->filtered_query($selected_brand_name, $iquery, $selected_price_range, $page_no, $selected_os_name, $selected_ram_range);
	    }
	    return $retrive_result_array;
    }
// ###########################################################################################################################################
   
// ################################################# get results for html ####################################################################
    public function get_results_for_html_display($searchParams, $page_no)
    {

        $client                         = new Elasticsearch\Client();
        $get_elasticsearch_result       = $client->search($searchParams);//echo json_encode($get_elasticsearch_result);
        $brand_list_array_temp          = $get_elasticsearch_result['aggregations']['global_agg']['filter_scope']['brand_bucket']['buckets'];
        $price_range_list_array_temp    = $get_elasticsearch_result['aggregations']['global_agg']['filter_scope']['price_range_bucket']['buckets'];
        $os_list_array_temp             = $get_elasticsearch_result['aggregations']['global_agg']['filter_scope']['os_bucket']['buckets'];
        $ram_range_list_array_temp      = $get_elasticsearch_result['aggregations']['global_agg']['filter_scope']['ram_range_bucket']['buckets'];
        $get_elasticsearch_result       = $get_elasticsearch_result[hits][hits];
    	$brand_list_array               = array();
        for ($i=0; $i < 10; $i = $i+1)
        {
            $result_array_for_html_table[$i] = $get_elasticsearch_result[($i+10*$page_no)];
        }
        for ($i = 0; $i < count($brand_list_array_temp); $i = $i+1)
        {
            $brand_list_array[$i]=$brand_list_array_temp[$i]['key']; 
        }
        for ($i = 0; $i < count($os_list_array_temp); $i = $i+1)
        {
            $os_list_array[$i]=$os_list_array_temp[$i]['key']; 
        }
        for ($i = 0; $i < count($price_range_list_array_temp); $i = $i+1)
        {
            $temp                       = $price_range_list_array_temp[$i]['key'];
            $pos                        = strpos($temp, '-');
            $lower_linit                = substr($temp, 0, $pos-2);
            $upper_limit                = substr($temp, $pos+1, -2);
            $price_range_list_array[$i] = $lower_linit.' - '.$upper_limit;
        }
        for ($i = 0; $i < count($ram_range_list_array_temp); $i = $i+1)
        {
            $temp                     = $ram_range_list_array_temp[$i]['key'];
            $pos                        = strpos($temp, '-');
            $lower_linit              = substr($temp, 0, $pos-2);
            $upper_limit              = substr($temp, $pos+1, -2);
            $ram_range_list_array[$i] = $lower_linit.' - '.$upper_limit;
        }
        return array($result_array_for_html_table, $brand_list_array, $price_range_list_array, $os_list_array, $ram_range_list_array);

    }
// ###########################################################################################################################################

// ######################################### user query search function ###################################################################### 
    public function user_query_search($user_query_string)
    {
    	$user_query_string=trim($user_query_string);

    	$this->initial_user_query = $user_query_string;
        $searchParams=$this->get_es_index_params(INDEX, TYPE, SIZE);
        if($user_query_string) // check condition for null search query
        {
        	$searchParams['body']['query']['match']['title'] = $user_query_string;
        }
        else
        {
        	$searchParams['body']['query']['match_all'] = new StdClass();
        }
        $aggregation_query            = $this->aggregations_query_builder($this->initial_user_query);   
        $searchParams['body']['aggs'] = $aggregation_query;
        var_dump(json_encode($searchParams));
        return $this->get_results_for_html_display($searchParams);
    }
// ###########################################################################################################################################

// ########################################## bulid brand filter #############################################################################
    public function brand_filter_builder($selected_brand_name)
    {
    	$temp_brand_name_array = array();
	    foreach($selected_brand_name as $temp)
	    {
	        array_push($temp_brand_name_array, $temp);
	    }
        $brand_filter          = array();
        if ($temp_brand_name_array) 
        {
             $brand_filter['terms']['brand'] = $temp_brand_name_array;
        }
        return $brand_filter;
    }
// ###########################################################################################################################################

// ########################################## bulid os filter #############################################################################
    public function os_filter_builder($selected_os_name)
    {
    	$temp_os_name_array = array();
	    foreach($selected_os_name as $temp)
	    {
	        array_push($temp_os_name_array, $temp);
	    }
        $os_filter          = array();
        if ($temp_os_name_array) 
        {
             $os_filter['terms']['os'] = $temp_os_name_array;
        }
        return $os_filter;
    }
// ###########################################################################################################################################
// // ###########################################  build ram filter ########################################################################### 
    public function ram_range_filter_builder($selected_ram_range)
    {
        $ram_range_array=array();
         foreach ($selected_ram_range as $temp) 
            {   
                $filter = array();
                switch ($temp)
                {
                    case ("0 - 1"):
                        $lower_limit = 0;
                        $upper_limit = 1;
                        break;
                    case ("1 - 2"):
                        $lower_limit = 1;
                        $upper_limit = 2;
                        break;
                    case ("2 - 3"):
                        $lower_limit = 2;
                        $upper_limit = 3;
                        break;
                    case ("3 - 4"):
                        $lower_limit = 3;
                        $upper_limit = 4;
                        break;
                    case ("4 - 15"):
                        $lower_limit = 4;
                        $upper_limit = 15;
                        break;
                    
                    default:
                        $lower_limit = 0;
                        $upper_limit = 0;
                        break;
                }
                $filter["range"]['ram']['gte'] = $lower_limit;
                $filter["range"]['ram']['lt']  = $upper_limit;
                array_push($ram_range_array, $filter);
            }
        $ram_range_filter                   = array();
        $ram_range_filter['bool']['should'] = $ram_range_array;
        return $ram_range_filter;
    }
// ###########################################################################################################################################

// ###########################################  build price range filter ###########################################################################
    public function price_range_filter_builder($selected_price_range)
    {
    	$price_range_array=array();
    	 foreach ($selected_price_range as $temp) 
	        {   
	            $filter = array();
	            switch ($temp)
	            {
	                case ("0 - 10000"):
	                    $lower_limit = 0;
	                    $upper_limit = 10000;
	                    break;
	                case ("10000 - 20000"):
	                    $lower_limit = 10000;
	                    $upper_limit = 20000;
	                    break;
	                case ("20000 - 35000"):
	                    $lower_limit = 20000;
	                    $upper_limit = 35000;
	                    break;
	                case ("35000 - 50000"):
	                    $lower_limit = 35000;
	                    $upper_limit = 50000;
	                    break;
	                case ("50000 - 1550000"):
	                    $lower_limit = 50000;
	                    $upper_limit = 1550000;
	                    break;
	                
	                default:
	                    $lower_limit = 0;
	                    $upper_limit = 0;
	                    break;
	            }
	            $filter["range"]['price']['gte'] = $lower_limit;
	            $filter["range"]['price']['lt']  = $upper_limit;
	            array_push($price_range_array, $filter);
	        }
    	$price_range_filter                   = array();
        $price_range_filter['bool']['should'] = $price_range_array;
        return $price_range_filter;
    }
// ###########################################################################################################################################

// #####################################  build initial user search query ####################################################################
    public function initial_query_builder($iquery)
    {
        $this->initial_user_query=$iquery;
    	$initial_string=array();
    	if($iquery) // check condition for null search query
        {
        	$initial_string['match']['title'] = $iquery;
        }
        else
        {
        	$initial_string['match_all'] = new StdClass();
        }
        return $initial_string;
    }
// ###########################################################################################################################################

// ############################################## aggregation builder ########################################################################
    public function aggregations_query_builder($iquery)
    {
    	$aggregation_query =array();
    	$aggregation_query['global_agg']['global'] = new StdClass();
        if($iquery)
    	$aggregation_query['global_agg']['aggs']['filter_scope']['filter']['query']['match']['title'] = $iquery;
        else 
        $aggregation_query['global_agg']['aggs']['filter_scope']['filter']['query']['match_all'] = new StdClass();
    	//----------------------------------- brand aggregation ------------------------------------------------------------
        $aggregation_query['global_agg']['aggs']['filter_scope']['aggs']['brand_bucket']['terms']['field'] = "brand";
    	$aggregation_query['global_agg']['aggs']['filter_scope']['aggs']['brand_bucket']['terms']['size'] = 0;
        $aggregation_query['global_agg']['aggs']['filter_scope']['aggs']['brand_bucket']['terms']['min_doc_count'] =1;
        //------------------------------------------------------------------------------------------------------------------
        //----------------------------------- os aggregation ------------------------------------------------------------
        $aggregation_query['global_agg']['aggs']['filter_scope']['aggs']['os_bucket']['terms']['field'] = "os";
        $aggregation_query['global_agg']['aggs']['filter_scope']['aggs']['os_bucket']['terms']['size'] = 0;
        $aggregation_query['global_agg']['aggs']['filter_scope']['aggs']['os_bucket']['terms']['min_doc_count'] =1;
        //------------------------------------------------------------------------------------------------------------------
        //----------------------------------- price range aggregation ------------------------------------------------------------
        $aggregation_query['global_agg']['aggs']['filter_scope']['aggs']['price_range_bucket']['range']['field'] = "price";
        $price_range_array=array();
        $temp['from']='0'; $temp['to']='10000';
        array_push($price_range_array, $temp);
        $temp['from']='10000'; $temp['to']='20000';
        array_push($price_range_array, $temp);
        $temp['from']='20000'; $temp['to']='35000';
        array_push($price_range_array, $temp);
        $temp['from']='35000'; $temp['to']='50000';
        array_push($price_range_array, $temp);
        $temp['from']='50000'; $temp['to']='1550000';
        array_push($price_range_array, $temp);
        $aggregation_query['global_agg']['aggs']['filter_scope']['aggs']['price_range_bucket']['range']['ranges'] = $price_range_array;
        //------------------------------------------------------------------------------------------------------------------
        //----------------------------------- ram range aggregation ------------------------------------------------------------
        $aggregation_query['global_agg']['aggs']['filter_scope']['aggs']['ram_range_bucket']['range']['field'] = "ram";
        $ram_range_array=array();
        $temp['from']='0'; $temp['to']='1';
        array_push($ram_range_array, $temp);
        $temp['from']='1'; $temp['to']='2';
        array_push($ram_range_array, $temp);
        $temp['from']='2'; $temp['to']='3';
        array_push($ram_range_array, $temp);
        $temp['from']='3'; $temp['to']='4';
        array_push($ram_range_array, $temp);
        $temp['from']='4'; $temp['to']='15';
        array_push($ram_range_array, $temp);
        $aggregation_query['global_agg']['aggs']['filter_scope']['aggs']['ram_range_bucket']['range']['ranges'] = $ram_range_array;
        //------------------------------------------------------------------------------------------------------------------


    	return $aggregation_query;
    }
// ###########################################################################################################################################

// ############################################## filtered query (for filtering user search) #################################################
    public function filtered_query($selected_brand_name, $iquery, $selected_price_range, $page_no, $selected_os_name, $selected_ram_range)
    {
        $brand_filter      = $this->brand_filter_builder($selected_brand_name);		// call brand filter builder
        $range_filter      = $this->price_range_filter_builder($selected_price_range); 	// call range filter builder
        $os_filter 		   = $this->os_filter_builder($selected_os_name); 		   // call os 	  filter builder
        $ram_filter        = $this->ram_range_filter_builder($selected_ram_range);         // call ram filter builder
        $initial_query     = $this->initial_query_builder($iquery);				   // call initial search builder
        $aggregation_query = $this->aggregations_query_builder($iquery);// call aggregations   builder
        // combine all the filtered in must array
        $must_array = array();

        if($brand_filter)
        {
        	array_push($must_array, $brand_filter);
        }
        if($os_filter)
        {
        	array_push($must_array, $os_filter);
        }
        array_push($must_array, $range_filter);
        array_push($must_array, $ram_filter);
	    // combine aggregation ,filters,initial user query 
    	$searchParams=$this->get_es_index_params(INDEX, TYPE, SIZE);
        $searchParams['body']['query']['filtered']['filter']['bool']['must']  = $must_array;
        $searchParams['body']['query']['filtered']['query']					  = $initial_query;  	//initial user query builder
        $searchParams['body']['aggs'] 								  		  = $aggregation_query; // aggregation on brand
        var_dump(json_encode($searchParams));
        return $this->get_results_for_html_display($searchParams, $page_no);
    }
// ###########################################################################################################################################
}
?>