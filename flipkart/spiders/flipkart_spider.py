from scrapy.spiders import Spider
from scrapy.selector import Selector
import scrapy
#from scrapy.request import Request
#des="test"
from flipkart.items import FlipkartItem
class FlipkartSpider(Spider):
    name = "flipkart"
    allowed_domains = ["flipkart.com"]
    orig="http://www.flipkart.com/mobiles/pr?sid=tyy,4io&start=";
    start_urls=[]
    for j in range(1,1500,20):
        x=orig+str(j)
        start_urls.append(x)
        #print j'''
    #start_urls=['http://www.flipkart.com/mobiles/pr?sid=tyy,4io&start=1421', 'http://www.flipkart.com/mobiles/pr?sid=tyy,4io&start=1441']
    def parse_details(self,response):
        #s=Selector(response)
        item=response.meta['item']
        item['desc']=response.xpath('//div[@class="rpdSection"]/p[1]/text()').extract()
        return item
    def parse(self,response):
        sel=Selector(response)
        sites=sel.xpath('//div[@class="product-unit unit-4 browse-product new-design "]')
        items=[]
        for site in sites:
            item=FlipkartItem()
            item['name']=site.xpath('.//div[@class="pu-title fk-font-13"]/a/text()').extract()
            item['price']=site.xpath('.//span[@class="fk-font-17 fk-bold"]/text()').extract()
            item['details']=site.xpath('.//ul[@class="pu-usp"]/li/span/text()').extract()
            initial="https://flipkart.com"
            #item['link']=initial+''.join(site.xpath('./div[1]/a[1]/@href').extract())
            ul=initial+''.join(site.xpath('./div[1]/a[1]/@href').extract())
            item['link']=ul
            request=scrapy.Request(ul,callback=self.parse_details)
            request.meta['item']=item
            yield request
            item['i_link']=site.xpath('./div[1]/a[1]/img/@data-src').extract()
            #item['desc']=des;
            items.append(item)
        #return items
    