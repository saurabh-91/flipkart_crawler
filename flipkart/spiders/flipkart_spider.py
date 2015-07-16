from scrapy.spiders import Spider
from scrapy.selector import Selector
import scrapy
#import clean
from elasticsearch import Elasticsearch
es = Elasticsearch()
import re
import time
from flipkart.items import FlipkartItem
#ind_id=""
class FlipkartSpider(Spider):
    name = "flipkart"
    allowed_domains = ["flipkart.com"]
    orig="http://www.flipkart.com/mobiles/pr?sid=tyy,4io&start=";
    start_urls=[]
    for j in range(1,1500,20):
        x=orig+str(j)
        start_urls.append(x)
    def parse_details(self,response):
        item=response.meta['item']
        item['desc']=''.join(response.xpath('//div[@class="rpdSection"]/p[1]/text()').extract())
        item['details']=','.join(response.xpath('//div[@class="specifications-wrap line unit"]/ul[1]/li/text()').extract())
        item['i_link']=''.join(response.xpath('//meta[@name="og_image"]/@content').extract())
        item['brand']=''.join(response.xpath('//div[@class="productSpecs specSection"]/table[1]/tr[2]/td[2]/text()').extract()).strip()
        '''desc=''.join(response.xpath('//div[@class="rpdSection"]/p[1]/text()').extract())
        details=','.join(response.xpath('//div[@class="specifications-wrap line unit"]/ul[1]/li/text()').extract())
        i_link=''.join(response.xpath('//meta[@name="og_image"]/@content').extract())
        '''
        ind_id=''.join(response.xpath('//div[@class="pincode-widget-container omniture-field"]/@data-pid').extract())
        #brand=''.join(response.xpath('//div[@class="productSpecs specSection"]/table[1]/tr[2]/td[2]/text()').extract()).strip()
        #gen_feature=(','.join(response.xpath('//div[@class="productSpecs specSection"]/table[1]/tr/td/text()').extract())).strip()
        #item['general_feature']=re.sub('\s+',' ',gen_feature)
        #mulit_media=(','.join(response.xpath('//div[@class="productSpecs specSection"]/table[2]/tr/td/text()').extract())).strip()
        #item['multimedia']=re.sub('\s+',' ',mulit_media)
        #cam=(','.join(response.xpath('//div[@class="productSpecs specSection"]/table[3]/tr/td/text()').extract())).strip()
        #item['camera']=re.sub('\s+',' ',cam)
        #item['general_feature']=(','.join(response.xpath('//div[@class="productSpecs specSection"]/table[1]/tr/td/text()').extract())).split()#strip(' \t\n\r'))
        doc = {
                'name': item['name'],
                'price': item['price'],
                'link': item['link'],
                'brand':item['brand'],
                #'brand':brand,
                #'details':details,
                #'desc':desc,
                #'i_link':i_link,
                  }
        '''res = es.get(index="test-index", doc_type='tweet', id=ind_id)
        if res!=0:
            res=es.delete(index="test-index", doc_type='tweet', id=ind_id)
            print "done".
        '''
        res=es.create(index="flipkart",doc_type='mobile', id=ind_id, body=doc,ignore=409)
        return item
    def parse(self,response):
        sel=Selector(response)
        sites=sel.xpath('//div[@class="product-unit unit-4 browse-product new-design "]')
        items=[]
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
            #start_index=ul.find("=")
            #ind_id=ul[start_index+1:start_index+17]
            #item['brand']=""
            
            yield scrapy.Request(ul,meta={'item':item},callback=self.parse_details)
            #request.meta['item']=item
            #yield request
            #time.sleep(3)
            '''
            name=(''.join(site.xpath('.//div[@class="pu-title fk-font-13"]/a/text()').extract())).strip()
            '''
            
            #link=ul
            #print ind_id
            #'{"index":{"_id":"' + str(i) + '"}}\n'
            #price=int(pri_ce.replace(",",""))

            #item['details']=''.join(site.xpath('.//ul[@class="pu-usp"]/li/span/text()').extract())
            #item['i_link']=''.join(site.xpath('./div[1]/a[1]/img/@data-src').extract())
            #print item
            #res = es.get(index="test-index", doc_type='tweet', id=ind_id)
            
            #brand=""
            #if res!=0:
            #    res=es.delete(index="test-index", doc_type='tweet', id=ind_id)
            #    print "done"
            #res = es.create(index="test-index", doc_type='tweet', id=ind_id, body=item)
            #print(res['created'])
            #items.append(item)
#clean.clean_data()
