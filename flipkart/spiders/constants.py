################################################ this constants is common to all #########################################
INITIAL_PREFIX_OF_INDEX_ID = "flipkart_"
##########################################################################################################################

############################################### es constants #############################################################
ES_HOST  = "127.0.0.1:9200" 		# elasticsearch host
ES_INDEX = "flipkart_test"	# elasticsearch index
ES_TYPE  = "mobile"					# elastcsearch index's type

##########################################################################################################################

############################################### scrapy constant name #####################################################
INDEX_ID     	= 'ind_id'
NAME         	= 'name'
TITLE    	 	= 'title'
RAM      	 	= 'ram'
OS       	 	= 'os'
BRAND    	 	= 'brand'
LINK     	 	= 'link'
IMAGE_LINK   	= 'i_link'
PRICE        	= 'price'
FULL_FEATURE 	= 'feature'
SPIDER_NAME  	= 'flipkart'
ALLOWED_DOMAINS = 'flipkart.com'
ORIGIN_URL      = 'http://www.flipkart.com/mobiles/pr?sid=tyy,4io&start='
##########################################################################################################################

############################################### redis constants ##########################################################
R_HOST  = '127.0.0.1'	  		# redis host
R_PORT  = 6379			  		# redis port
R_DB    = 0               		# redis db
R_HASH  = 'flipkart_hash'       # redis hash 
##########################################################################################################################

############################################## mysql db constants #######################################################
MYSQL_HOST             = "127.0.0.1"       # mysql host
MYSQL_USER             = "root"			# mysql username
MYSQL_PASS             = "9595"			# mysql password
MYSQL_DB               = "mobile_db"		# mysql database name
MYSQL_TABLE 		   = "test"			# mysql table name
MYSQL_INDEX_ID_COLUMN  = "index_id"
MYSQL_KEY_NAME_COLUMN  = "key_name"
MYSQL_KEY_VALUE_COLUMN = "key_value"
###########################################################################################################################