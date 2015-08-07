################################################ some import BC ###################################
from scrapy.spiders import Spider
from scrapy.selector import Selector
import scrapy
from elasticsearch import Elasticsearch
import re
import time
from flipkart.items import FlipkartItem
import MySQLdb
import redis
import hashlib
import json
import sys
from constants import *
###################################################################################################

###################################################################################################
es = Elasticsearch([ES_HOST]) # elastcsearch client 
###################################################################################################

############################################## mysql db parameter #################################
db = MySQLdb.connect(
                        host   = MYSQL_HOST,
                        user   = MYSQL_USER,
                        passwd = MYSQL_PASS,
                        db     = MYSQL_DB
                    ) 
# you must create a Cursor object. It will let
# you execute all the query you need
cur = db.cursor()
if (cur == 0):
    print "connection failed"
###################################################################################################

############################################## redis client #######################################
r = redis.StrictRedis(host = R_HOST, port = R_PORT, db = R_DB) #redis client
###################################################################################################

######################################## insert into redis ########################################
def dict_to_redis_hset(r, hkey, dict_to_store):
    return all([r.hset(hkey, k, v) for k, v in dict_to_store.items()])
###################################################################################################

######################################## insert into mysql ########################################
def insert_into_mysql(ind_id, doc):                 # do insert and update in a single query (may be not possible)
    for key_name in doc:
        update_flag=cur.execute("SELECT * FROM "+MYSQL_TABLE+" WHERE "+MYSQL_INDEX_ID_COLUMN+"=%s AND "+MYSQL_KEY_NAME_COLUMN+"=%s" ,(ind_id, key_name)) # table name test having columns(index_id,key_name,key_value)
        if update_flag :
            cur.execute("UPDATE "+MYSQL_TABLE+" SET "+MYSQL_KEY_VALUE_COLUMN+"=%s WHERE "+MYSQL_INDEX_ID_COLUMN+"=%s AND "+MYSQL_KEY_NAME_COLUMN+"=%s" ,(str(doc[key_name]), ind_id, key_name))
        else :
            cur.execute("INSERT INTO "+MYSQL_TABLE+"("+MYSQL_INDEX_ID_COLUMN+", "+MYSQL_KEY_NAME_COLUMN+", "+MYSQL_KEY_VALUE_COLUMN+") VALUES(%s, %s, %s)", (ind_id, key_name, str(doc[key_name])))
    db.commit()
####################################################################################################

######################################## insert into elasticsearch  ################################
def insert_into_es(ind_id, doc):
    error_test = es.index(index = ES_INDEX, doc_type = ES_TYPE, id = ind_id, body = doc, ignore = [400,401,409]) # elasticsearch client insertion
    if(error_test == 0):
        print "elasticsearch insertion failed"
####################################################################################################

######################################## insert into redis, mysql,elasticsearch ####################
def insert(item):
    doc = {
                NAME          : item[NAME],
                PRICE         : item[PRICE],
                LINK          : item[LINK],
                BRAND         : item[BRAND],
                TITLE         : item[TITLE],
                IMAGE_LINK    : item[IMAGE_LINK],
                FULL_FEATURE  : item[FULL_FEATURE],
                RAM           : item[RAM],
                OS            : item[OS],
                
                }
    ind_id = item[INDEX_ID]
    ind_id = INITIAL_PREFIX_OF_INDEX_ID+ind_id
    dict_to_redis_hset(r,ind_id, doc)
    insert_into_es(ind_id, doc)
    insert_into_mysql(ind_id, doc) 
    
####################################################################################################


############################################## main scrapy class ###################################
class FlipkartSpider(Spider):
    name            = SPIDER_NAME
    allowed_domains = [ALLOWED_DOMAINS]
    orig            = ORIGIN_URL
    start_urls      = []
    for j in range(1, 1500, 20):
        x = orig+str(j)
        start_urls.append(x)
    ################################################ callback function #############################
    def parse_details(self,response):
        item = response.meta['item']
        mem  = ''.join(response.xpath('//tr[td/text()="Memory"]/td[2]/text()').extract()).strip()
        i    = mem.find("RAM");#starting index of ram 
        #item[RAM]=mem[:i-1]
        if(i>0):
            i = mem[:i-1]
            j = i.find("KB")
            k = i.find("MB")
            if(j>0):
                t = i[:j-1]
                t = float(t)
                t = t/(1024*1024)
                item[RAM]=t
            
            elif(k>0):
                t = i[:k-1]
                t = float(t)
                t = t/(1024)
                #t=t+" GB"
                item[RAM]=t
            else :
                j = i.find("GB")
                t = i[:j-1]
                t = float(t)
                item[RAM] = t
        else:
            item[RAM] = "NA"

        os = ''.join(response.xpath('//tr[td/text()="OS"]/td[2]/text()').extract()).strip()
        if(os):
            i = os.find(" ");
            if(i<0):
                item[OS] = os
            else:
                item[OS] = os[:i]
        else:
            item[OS]  = "NA"
        item[TITLE]       = ''.join(response.xpath('//title/text()').extract()).strip()
        item['desc']      = ''.join(response.xpath('//div[@class="rpdSection"]/p[1]/text()').extract())
        item['details']   = ','.join(response.xpath('//div[@class="specifications-wrap line unit"]/ul[1]/li/text()').extract())
        item[IMAGE_LINK]  = ''.join(response.xpath('//meta[@name="og_image"]/@content').extract())
        item[BRAND]       = ''.join(response.xpath('//tr[td/text()="Brand"]/td[2]/text()').extract()).strip()
        item[TITLE]       =item[TITLE][:-15]
        feature   = {}
        full_desc = response.xpath('//table[@class="specTable"]')
        for row in full_desc:
            table_name       = ''.join(row.xpath('./tr[1]/th/text()').extract())
            temp_sub_feature = {}
            r1 = row.xpath('.//tr')
            for r in r1:

                key                   = (''.join(r.xpath('./td[1]/text()').extract())).strip()
                value                 = (''.join(r.xpath('./td[2]/text()').extract())).strip()
                temp_sub_feature[key] = value
            feature[table_name] = temp_sub_feature
        item[FULL_FEATURE] = feature
        #################################################### call insert function #########################################################
        insert(item)
        return item
##########################################################################################################################################################


###############################################################  main parser function  ###################################################################
    def parse(self, response):
        sel   = Selector(response)
        sites = sel.xpath('//div[@class="product-unit unit-4 browse-product new-design "]')
        items = []
        BATCH_SIZE = len(sites)
        for site in sites:
            item           = FlipkartItem()
            item[NAME]     = (''.join(site.xpath('.//div[@class="pu-title fk-font-13"]/a/text()').extract())).strip()
            pri_ce         = ''.join(site.xpath('.//span[@class="fk-font-17 fk-bold"]/text()').extract())
            pri_ce         = pri_ce.replace("Rs. ","")
            item[PRICE]    = int(pri_ce.replace(",",""))
            initial        = "https://flipkart.com"
            ul             = initial+''.join(site.xpath('./div[1]/a[1]/@href').extract())
            item[LINK]     = ul
            pid_index      = ul.find("pid=")              # for flipkart unique index for flipkart
            item[INDEX_ID] = ul[pid_index+4:pid_index+20] #
            yield scrapy.Request(ul, meta={'item':item}, callback=self.parse_details)
