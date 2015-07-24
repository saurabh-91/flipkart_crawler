################################################ some import BC #####################################
from scrapy.spiders import Spider
from scrapy.selector import Selector
import scrapy
from elasticsearch import Elasticsearch
import re
import time
from flipkart.items import FlipkartItem
import MySQLdb
import redis
###################################################################################################
es = Elasticsearch() # elastcsearch client 
############################################## my sql db parameter ################################
db = MySQLdb.connect(
                        host="localhost", # your host, usually localhost
                        user="root", # your username
                        passwd="9595", # your password
                        db="flipkart_master" # name of the data base
                    ) 
# you must create a Cursor object. It will let
#  you execute all the query you need
cur = db.cursor()
if (cur==0):
    print "connection failed"

############################################## redis client ########################################
r = redis.StrictRedis(host='localhost', port=6379, db=0) #redis client

######################################## insert into redis, mysql,elasticsearch ####################
def insert(ind_id,title,name,brand,price,link,i_link,feature):
   
    doc = {                         # make a json like doc for insertion into redis elasticsearch
                'name': name,
                'price': price,
                'link': link,
                'brand':brand,
                'title':title,
                'i_link':i_link,
                'feature':feature,
                
                }    
    r.hset('flip_hash2',ind_id,doc) #redis client insert
    res=es.index(index="flipkart3",doc_type='mobile', id=ind_id, body=doc,ignore=[400,401,409]) # elasticsearch client insertion
    #  for test
    feature=str(feature) # convert to string       
    cur.execute("INSERT INTO test VALUES(%s, %s, %s,%s,%s,%s,%s,%s)",(ind_id,title,name,brand,price,link,i_link,feature)) # mysql db insert
    db.commit() # mysql commit

 ####################################################################################################


############################################## main scrapy class ####################################
class FlipkartSpider(Spider):
    name = "flipkart"
    allowed_domains = ["flipkart.com"]
    orig="http://www.flipkart.com/mobiles/pr?sid=tyy,4io&start=";
    start_urls=[]
    for j in range(1,1500,20):
        x=orig+str(j)
        start_urls.append(x)
    ################################################ callback function ###############################
    def parse_details(self,response):
        item=response.meta['item']
        item['title']=''.join(response.xpath('//title/text()').extract()).strip()
        item['desc']=''.join(response.xpath('//div[@class="rpdSection"]/p[1]/text()').extract())
        item['details']=','.join(response.xpath('//div[@class="specifications-wrap line unit"]/ul[1]/li/text()').extract())
        item['i_link']=''.join(response.xpath('//meta[@name="og_image"]/@content').extract())
        item['brand']=''.join(response.xpath('//div[@class="productSpecs specSection"]/table[1]/tr[2]/td[2]/text()').extract()).strip()
        item['title']=item['title'][:-15]
        ind_id=''.join(response.xpath('//div[@class="pincode-widget-container omniture-field"]/@data-pid').extract())
        feature={}
        full_desc=response.xpath('//table[@class="specTable"]')
        for row in full_desc:
            table_name=''.join(row.xpath('./tr[1]/th/text()').extract())
            temp_sub_feature={}
            r1=row.xpath('.//tr')
            for r in r1:

                key=(''.join(r.xpath('./td[1]/text()').extract())).strip()
                value=(''.join(r.xpath('./td[2]/text()').extract())).strip()
                temp_sub_feature[key]=value
            feature[table_name]=temp_sub_feature
        item['feature']=feature


#################################################### call insert function #########################################################
        insert(ind_id,item['title'],item['name'],item['brand'],item['price'],item['link'],item['i_link'],item['feature'])
        return item
###############################################################  main parser function  ###################################################################
    def parse(self,response):
        sel=Selector(response)
        sites=sel.xpath('//div[@class="product-unit unit-4 browse-product new-design "]')
        items=[]
        BATCH_SIZE = len(sites)
        for site in sites:
            #global ind_id
            item=FlipkartItem()
            item['name']=(''.join(site.xpath('.//div[@class="pu-title fk-font-13"]/a/text()').extract())).strip()
            pri_ce=''.join(site.xpath('.//span[@class="fk-font-17 fk-bold"]/text()').extract())
            pri_ce=pri_ce.replace("Rs. ","")
            item['price']=int(pri_ce.replace(",",""))
            initial="https://flipkart.com"
            ul=initial+''.join(site.xpath('./div[1]/a[1]/@href').extract())
            item['link']=ul
            yield scrapy.Request(ul,meta={'item':item},callback=self.parse_details)
