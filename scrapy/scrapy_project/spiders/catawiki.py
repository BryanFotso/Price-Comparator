import scrapy
from ..items import ScrapyProjectItem

class amazon_Spider(scrapy.Spider):
    name = 'catawiki'
    start_urls = ['https://www.catawiki.com/fr/s?q=montre&filters=909%255B%255D%3D60922']
    
      
    def parse(self, response):
        
        watches_links = response.xpath('//article[@class="c-lot-card__container"]/a/@href').getall()
        prices = response.xpath('//article[@class="c-lot-card__container"]/a/div[2]/p[3]/text()').getall()
        
        for index,link in enumerate(watches_links) :
            price = prices[index]
            yield response.follow(link, self.parse_link, meta={'url': link, 'price':price})
        
        for page in range(1, 6):
            next_page = f'https://www.catawiki.com/fr/s?q=montre&filters=909%255B%255D%3D60922&page={page}'
            yield scrapy.Request(next_page)
              
    def parse_link(self, response):
        
        watch = ScrapyProjectItem()
        watch['stores'] = "Catawiki"
        watch['model'] = response.xpath('//div[span[contains(text(),"Modèle")]]/div/span/text()').get()
        watch['brand'] = response.xpath('//div[span[contains(text(),"Marque")]]/div/span/text()').get()
        watch['price_whole'] = response.meta.get('price').replace('\xa0', ' ').strip()
        end_url = response.meta.get('url')
        if end_url :
            watch['url'] = response.urljoin(end_url)
            
        watch['dimensions'] = response.xpath('//div[span[contains(text(),"Diameter/ Width Case")]]/div/span/text()').get()
        watch['image_url'] = response.xpath("//img[contains(@alt, 'Rolex')]/@src").get()
        yield watch