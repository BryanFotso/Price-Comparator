import scrapy
from ..items import ScrapyProjectItem

class amazon_Spider(scrapy.Spider):
    name = 'kronos360'
    start_urls = ['https://www.kronos360.com/fr/montre-collection/rolex/']
    
      
    def parse(self, response):
        watches_links = response.xpath('//strong[@class="product-name"]/a/@href').getall()
        for link in watches_links :
            yield response.follow(link, self.parse_link, meta={'url': link})
        
        next = response.xpath('//div[@class="pagination_next"]/a/@href').get()
        if next is not None:
            next_link = response.urljoin(next)
            yield scrapy.Request(next_link)
              
    def parse_link(self, response):
        
        watch = ScrapyProjectItem()
        watch['stores'] = "kronos360"
        watch['brand'] = response.xpath('//span[@itemprop = "brand"]/text()').get()
        watch['model'] = response.xpath('//li[span[contains(text(),"Modèle")]]/span[2]/text()').get()
        watch['price_whole'] = response.xpath('//span[@class="current-price"]/text()').get()
        end_url = response.meta.get('url')
        if end_url :
            watch['url'] = response.urljoin(end_url)
        
        yield watch