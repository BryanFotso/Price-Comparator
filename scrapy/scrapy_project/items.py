# Define here the models for your scraped items
#
# See documentation in:
# https://docs.scrapy.org/en/latest/topics/items.html

import scrapy


class ScrapyProjectItem(scrapy.Item):
    # define the fields for your item here like:
    # name = scrapy.Field()
    price_whole = scrapy.Field()
    currency = scrapy.Field()
    brand = scrapy.Field()
    image_url = scrapy.Field()
    url = scrapy.Field()
    description = scrapy.Field()
    # stores = scrapy.Field()
    model = scrapy.Field()

