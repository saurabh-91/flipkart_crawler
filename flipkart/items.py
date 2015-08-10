# -*- coding: utf-8 -*-

# Define here the models for your scraped items
#
# See documentation in:
# http://doc.scrapy.org/en/latest/topics/items.html
from scrapy.item import Item, Field
import scrapy


class CrawlingItem(scrapy.Item):
    ind_id  = scrapy.Field()
    name    = scrapy.Field()
    price   = scrapy.Field()
    link    = scrapy.Field()
    i_link  = scrapy.Field()

class FlipkartItem(CrawlingItem):
    # define the fields for your item here like:
    ind_id  = scrapy.Field()
    name    = scrapy.Field()
    details = scrapy.Field()
    price   = scrapy.Field()
    link    = scrapy.Field()
    i_link  = scrapy.Field()
    desc    = scrapy.Field()
    brand   = scrapy.Field()
    title   = scrapy.Field()
    feature = scrapy.Field()
    ram     = scrapy.Field()
    os      = scrapy.Field()