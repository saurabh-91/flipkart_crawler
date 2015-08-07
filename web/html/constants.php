<?php
// ##################################### error page link #######################################################
	define("ERROR_PAGE_LOCATION", "Location: http://localhost/flipkart/html/error_page.html" );
// #############################################################################################################

// ##################################### elasticsearch constants ###############################################
	define("ESHOST", "127.0.0.1:9200");
	define("INDEX", "flipkart_data");
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
// #############################################################################################################

// ############################### mysql db constants ##########################################################
	define("MYSQL_DB_SERVER", "127.0.0.1");
	define("MYSQL_DB_USER", "root");
	define("MYSQL_DB_PASS", "9595");
	define("MYSQL_DB", "mobile_db");
	define("MYSQL_DB_TABLE", "master");
	define("MYSQL_DB_PRIMARY_KEY", "auto_inc_id");
	define("USER_INDEX_ID", "index_id");
	define("KEY_NAME_COLUMN", "key_name");
	define("KEY_VALUE_COLUMN", "key_value");

// #############################################################################################################

// ############################## some other constants(related to elasticsearch ) ##############################
	define("NAME", "name");								// elasticsearch index fields
	define("RAM", 'ram');								// these constants value
	define("OS", "os");									// depends on the extracted scrapy fields
	define("PRICE", "price");							// means field name in scrapy and web must
	define("LINK", "link");								// be same.
	define("I_LINK", "i_link");							// these constants value can't be change
	define("BRAND", "brand");							// independently
	define("TITLE", "tille");							//
	define("FEATURE", "feature");						//
	define("RAM_RANGE_BUCKET", "ram_range_bucket");    	// name of ram bucket used in aggregation
	define("PRICE_RANGE_BUCKET", "price_range_bucket");	// name of price bucket used in aggregation
	define("OS_BUCKET", "os_bucket");				   	// name of os bucket used in aggregation
	define("BRAND_BUCKET", "brand_bucket");			   	// name of brand bucket used in aggregation
	define("GLOBAL_AGG", "global_agg");				  	// name  of global aggregation
	define("FILTER_AGG", "filter_scope");			  	// name of nested aggregation


// #############################################################################################################

?>