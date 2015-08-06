<?php
include "constants.php";        // all constants used in this file is defined in file named "constants.php"
require "vendor/autoload.php";
class SearchElastic 
{
    public $initial_user_query;
// ######################################### get the elasticsearch index params #############################################################
	public function get_es_index_params($index, $type, $size)
   {
   		$searchParams          = array();
        $searchParams['index'] = $index;
        $searchParams['type']  = $type;
        $searchParams['size']  = $size;
        return $searchParams;
   }
// ###########################################################################################################################################

// ######################################### main function (execution start from here) ########################################################
    public function perform_es($user_query_string, $term_filter_input_array, $range_filter_input_array, $iquery, $page_no)//depent on user input which opretion to perform
    {
    	if (isset($user_query_string))
    	{ 	//call search function;
    		$retrive_result_array =  $this->user_query_search($user_query_string);
    	}
	    else
	    {	//call filtered search function
	    	$retrive_result_array =  $this->filtered_query($term_filter_input_array, $range_filter_input_array, $iquery, $page_no);
	    }
	    return $retrive_result_array;
    }
// ###########################################################################################################################################

// ################################################# get range filter for display ############################################################
    public function get_range_result($range_list_array_temp)
    {
        for ($i = 0; $i < count($range_list_array_temp); $i = $i+1)
        {
            $temp                       = $range_list_array_temp[$i]['key'];
            $pos                        = strpos($temp, '-');                               // process the
            $lower_linit                = substr($temp, 0, $pos-2);                         // retrieved
            $upper_limit                = substr($temp, $pos+1, -2);                        // aggregation
            $range_list_array[$i][0]    = $lower_linit.' - '.$upper_limit;                  // results
            $range_list_array[$i][1]    = $range_list_array_temp[$i]['doc_count'];
        }
        return $range_list_array;
    }
// ###########################################################################################################################################

// ################################################## get term filter for display ############################################################
    public function get_term_result($term_list_array_temp)
    {
        for ($i = 0; $i < count($term_list_array_temp); $i = $i+1)
        {
            $term_list_array[$i]=$term_list_array_temp[$i]['key'];
        }
        return $term_list_array;
    }

// ###########################################################################################################################################

// ################################ get results for html(some new line of code is needed for addition of new filters) ########################
    public function get_results_for_html_display($searchParams)
    {
        $params = array();
        $params['hosts']=array(ESHOST);
        $client                         = new Elasticsearch\Client($params);
        $get_elasticsearch_result       = $client->search($searchParams);//echo json_encode($get_elasticsearch_result);
        $brand_list_array_temp          = $get_elasticsearch_result['aggregations'][GLOBAL_AGG][FILTER_AGG][BRAND_BUCKET]['buckets'];
        $price_range_list_array_temp    = $get_elasticsearch_result['aggregations'][GLOBAL_AGG][FILTER_AGG][PRICE_RANGE_BUCKET]['buckets'];
        $os_list_array_temp             = $get_elasticsearch_result['aggregations'][GLOBAL_AGG][FILTER_AGG][OS_BUCKET]['buckets'];
        $ram_range_list_array_temp      = $get_elasticsearch_result['aggregations'][GLOBAL_AGG][FILTER_AGG][RAM_RANGE_BUCKET]['buckets'];
        $get_elasticsearch_result       = $get_elasticsearch_result[hits][hits];
        $result                         = array();
        for ($i=0; $i < 10; $i = $i+1)
        {
            $result_array_for_html_table[$i] = $get_elasticsearch_result[$i];
        }
        $brand_list_array   = $this->get_term_result($brand_list_array_temp);
        $os_list_array      = $this->get_term_result($os_list_array_temp);
        $price_range_list_array = $this->get_range_result($price_range_list_array_temp);
        $ram_range_list_array   = $this->get_range_result($ram_range_list_array_temp);

        array_push($result, $result_array_for_html_table);
        array_push($result, $brand_list_array);
        array_push($result, $price_range_list_array);
        array_push($result, $os_list_array);
        array_push($result, $ram_range_list_array);
        return $result;

    }
// ###########################################################################################################################################

// ######################################### range filter builder ############################################################################
    public function range_filter_builder($range_filter_input_array)
    {
        $range_filter_array = array();
        foreach($range_filter_input_array as $range_filter_name => $temp_range_filter_input)
        {
            if($temp_range_filter_input)
            {
                $temp_single_range_filter=array();
                foreach($temp_range_filter_input as $temp)
                {
                    $filter      = array();
                    $i = strpos($temp, '-');                         // index of ' - '
                    $lower_limit = substr($temp, 0, $i - 1);
                    $lower_limit = intval($lower_limit);
                    $upper_limit = substr($temp, $i + 2);
                    $upper_limit = intval($upper_limit);
                    $filter["range"][$range_filter_name]['gte'] = $lower_limit;
                    $filter["range"][$range_filter_name]['lt']  = $upper_limit;
                    array_push($temp_single_range_filter, $filter);
                }
                $single_range_filter                = array();
                $single_range_filter['bool']['should'] = $temp_single_range_filter;
                array_push($range_filter_array,$single_range_filter);
            }
        }
        return $range_filter_array;                                  // need to to parsed for used
    }
// ###########################################################################################################################################

// ######################################### term filter builder #############################################################################
    public function term_filter_builder($term_filter_input_array)
    {
        $term_filter_array = array();
        foreach($term_filter_input_array as $term_filter_name => $temp_term_filter_input)
        {
            if($temp_term_filter_input)
            {
                $temp_term_bulid        = array();
                $temp_term_filter_array = array();
                foreach ($temp_term_filter_input as $temp)
                {
                    array_push($temp_term_filter_array, $temp);
                }
                $temp_term_bulid['terms'][$term_filter_name] = $temp_term_filter_array;
                array_push($term_filter_array, $temp_term_bulid);
            }
        }
        return $term_filter_array; // it return an array which need to be processed before making final must query
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
        	$searchParams['body']['query']['match']['title'] = $user_query_string; // perform search on title field
        }
        else
        {
        	$searchParams['body']['query']['match_all'] = new StdClass();
        }
        $aggregation_query            = $this->aggregations_query_builder($this->initial_user_query);   
        $searchParams['body']['aggs'] = $aggregation_query;
        return $this->get_results_for_html_display($searchParams);
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

// ################################ aggregation builder (some new line of code is needed for addition of new filters)#########################
    public function aggregations_query_builder($iquery)
    {
    	$aggregation_query =array();
    	$aggregation_query[GLOBAL_AGG]['global'] = new StdClass();
        if($iquery)
    	$aggregation_query[GLOBAL_AGG]['aggs'][FILTER_AGG]['filter']['query']['match']['title'] = $iquery;
        else 
        $aggregation_query[GLOBAL_AGG]['aggs'][FILTER_AGG]['filter']['query']['match_all'] = new StdClass();
    	//----------------------------------- brand aggregation ------------------------------------------------------------------
        $aggregation_query[GLOBAL_AGG]['aggs'][FILTER_AGG]['aggs'][BRAND_BUCKET]['terms']['field'] = BRAND;
    	$aggregation_query[GLOBAL_AGG]['aggs'][FILTER_AGG]['aggs'][BRAND_BUCKET]['terms']['size'] = 0;
        $aggregation_query[GLOBAL_AGG]['aggs'][FILTER_AGG]['aggs'][BRAND_BUCKET]['terms']['min_doc_count'] =1;
        //------------------------------------------------------------------------------------------------------------------------
        //----------------------------------- os aggregation ---------------------------------------------------------------------
        $aggregation_query[GLOBAL_AGG]['aggs'][FILTER_AGG]['aggs'][OS_BUCKET]['terms']['field'] = OS;
        $aggregation_query[GLOBAL_AGG]['aggs'][FILTER_AGG]['aggs'][OS_BUCKET]['terms']['size'] = 0;
        $aggregation_query[GLOBAL_AGG]['aggs'][FILTER_AGG]['aggs'][OS_BUCKET]['terms']['min_doc_count'] =1;
        //------------------------------------------------------------------------------------------------------------------------
        //----------------------------------- price range aggregation ------------------------------------------------------------
        $aggregation_query[GLOBAL_AGG]['aggs'][FILTER_AGG]['aggs'][PRICE_RANGE_BUCKET]['range']['field'] = PRICE;
        $price_range_array=array();
        $temp['from']=0; $temp['to']=10000;
        array_push($price_range_array, $temp);
        $temp['from']=10000; $temp['to']=20000;
        array_push($price_range_array, $temp);
        $temp['from']=20000; $temp['to']=35000;
        array_push($price_range_array, $temp);
        $temp['from']=35000; $temp['to']=50000;
        array_push($price_range_array, $temp);
        $temp['from']=50000; $temp['to']=1550000;
        array_push($price_range_array, $temp);
        $aggregation_query[GLOBAL_AGG]['aggs'][FILTER_AGG]['aggs'][PRICE_RANGE_BUCKET]['range']['ranges'] = $price_range_array;
        //------------------------------------------------------------------------------------------------------------------------
        //----------------------------------- ram range aggregation --------------------------------------------------------------
        $aggregation_query[GLOBAL_AGG]['aggs'][FILTER_AGG]['aggs'][RAM_RANGE_BUCKET]['range']['field'] = RAM;
        $ram_range_array=array();
        $temp['from']=0; $temp['to']=1;
        array_push($ram_range_array, $temp);
        $temp['from']=1; $temp['to']=2;
        array_push($ram_range_array, $temp);
        $temp['from']=2; $temp['to']=3;
        array_push($ram_range_array, $temp);
        $temp['from']=3; $temp['to']=4;
        array_push($ram_range_array, $temp);
        $temp['from']=4; $temp['to']=15;
        array_push($ram_range_array, $temp);
        $aggregation_query[GLOBAL_AGG]['aggs'][FILTER_AGG]['aggs'][RAM_RANGE_BUCKET]['range']['ranges'] = $ram_range_array;
        //-----------------------------------------------------------------------------------------------------------------------
    	return $aggregation_query;
    }
// ###########################################################################################################################################

// ############################################## filtered query (for filtering user search) #################################################
    public function filtered_query($term_filter_input_array, $range_filter_input_array, $iquery, $page_no)
    {
        $must_array = array();
        if($term_filter_input_array)
        {
            $term_filter   = $this->term_filter_builder($term_filter_input_array); //$term filter need to be processed and append into must array
            foreach($term_filter as $temp)
            {
                array_push($must_array, $temp);
            }
        }
        if($range_filter_input_array)
        {
            $range_filter  = $this->range_filter_builder($range_filter_input_array);  //$range filter need to be processed and append into must array
            foreach($range_filter as $temp)
            {
                array_push($must_array, $temp);
            }
        }
        $initial_query     = $this->initial_query_builder($iquery);				   // call initial search builder
        $aggregation_query = $this->aggregations_query_builder($iquery);           // call aggregations   builder
    	$searchParams=$this->get_es_index_params(INDEX, TYPE, SIZE);
        $searchParams['body']['query']['filtered']['filter']['bool']['must']  = $must_array;
        $searchParams['body']['query']['filtered']['query']					  = $initial_query;  	//initial user query builder
        $searchParams['body']['aggs'] 								  		  = $aggregation_query; // aggregation on brand
        $searchParams['from']                                                 = 10*$page_no;
        //var_dump(json_encode($searchParams));
        return $this->get_results_for_html_display($searchParams);
    }
// ###########################################################################################################################################
}
?>