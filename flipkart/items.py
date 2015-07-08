# -*- coding: utf-8 -*-

# Define here the models for your scraped items
#
# See documentation in:
# http://doc.scrapy.org/en/latest/topics/items.html
from scrapy.item import Item, Field
import scrapy


class FlipkartItem(scrapy.Item):
    # define the fields for your item here like:
    # name = scrapy.Field()

    #pass
    name=scrapy.Field()
    details=scrapy.Field()
    price=scrapy.Field()
    #title= scrapy.Field()
    link = scrapy.Field()
    i_link=scrapy.Field()
    #desc = scrapy.Field()
