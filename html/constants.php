<?php
$host_params=array();
$host_params['hosts']=array
	(
		'localhost:9200',
	);
// ##################################### elasticsearch constants ###############################################
define("INDEX", "flipkart_new");
define("TYPE", "mobile");
define("SIZE", 10);
// #############################################################################################################

// ################################# html result page constants ################################################
define("SHOWCHECKED", "checked='checked'");
// #############################################################################################################

// ################################### redis constants #########################################################
define("REDIS_SCHEME", "tcp");
define("REDIS_HOST", "127.0.0.1");
define("REDIS_PORT", 6379);
define("REDIS_DB", 0);
define("REDIS_HASH", "flipkart_hash");
// #############################################################################################################

// ############################### mysql db constants ##########################################################
	define("MYSQL_DB_SERVER", "127.0.0.1");
	define("MYSQL_DB_USER", "root");
	define("MYSQL_DB_PASS", "9595");
	define("MYSQL_DB", "flipkart_master");
	define("MYSQL_DB_TABLE", "masterdetails");
	define("MYSQL_DB_PRIMARY_KEY", "id");

// #############################################################################################################
// my sql defaults port number 3306
/*define("INITIAL_QUERY", $_POST['initial_query']);
define("SEARCH", $_POST['search']);// user query
define("BRAND_NAME",  $_POST['brand_name']);
define("OS_NAME", $_POST['os_name']);
define("PRICE_RANGE", $_POST['price_range']);
define("RAM_RANGE", $_POST['ram_range']);*/
//define("LISTBRAND", $list_selected_brand);

?>