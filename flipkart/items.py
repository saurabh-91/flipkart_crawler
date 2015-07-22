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
    desc = scrapy.Field()
    brand=scrapy.Field()
    #multimedia=scrapy.Field()
    #camera=scrapy.Field()
    index=scrapy.Field()
    title=scrapy.Field()
    feature=scrapy.Field()
    #general_feature=scrapy.Field() #includes brands, sim size, sim type, touchscreen, handset color, form, cell feature, model name, model id, in yhe box, e.t.c
