import scrapy
from ..items import ScrapyProjectItem

class amazon_Spider(scrapy.Spider):
    name = 'amazon'
    start_urls = ['https://www.amazon.fr/s?k=montres']
    

      
    def parse(self, response):
        watches_links = response.xpath('//a[@class="a-link-normal s-line-clamp-2 s-link-style a-text-normal"]/@href').getall()
        for link in watches_links :
            yield response.follow(link, self.parse_link, meta={'url': link})
        
        
        
          
    def parse_link(self, response):
        
        watch = ScrapyProjectItem()
        watch['name'] = response.xpath('//h1[@class="a-size-large a-spacing-none"]/span/text()').get()
        watch['price_whole'] = response.xpath('//span[@class="a-price-whole"]/text()').get()
        watch['price_decimal'] = response.xpath('//span[@class="a-price-fraction"]/text()').get()
        watch['currency'] = response.xpath('//span[@class="a-price-symbol"]/text()').get()
        end_url = response.meta.get('url')
        if end_url :
            watch['url'] = response.urljoin(end_url)
        watch['brand'] = response.xpath('//a[@id="bylineInfo"]/text()').get().replace('Marque\xa0: ', '').strip()
        #watch['description'] = response.xpath('//span[@id="productTitle"]/text()').get()
        
        yield watch