import scrapy
from ..items import ScrapyProjectItem

class amazon_Spider(scrapy.Spider):
    name = 'kronos360'
    start_urls = ['https://www.kronos360.com/fr/montre-collection/rolex/']
    categories = {'lady_datejust':['lady datejust','lady-datejust','lady date just','lady-date just'],}
      
    def parse(self, response):
        # Extraction des liens des montres sur la page actuelle
        watches_links = response.xpath('//strong[@class="product-name"]/a/@href').getall()
        for link in watches_links:
            yield response.follow(link, self.parse_link, meta={'url': link})

        # Pagination - limiter à 5 pages
        current_page = response.meta.get('page', 1)  # Par défaut, page 1
        if current_page < 5:  # Limite à 5 pages
            next = response.xpath('//div[@class="pagination_next"]/a/@href').get()
            if next is not None:
                next_link = response.urljoin(next)
                yield scrapy.Request(next_link, self.parse, meta={'page': current_page + 1})

              
    def parse_link(self, response):
        
        watch = ScrapyProjectItem()
        model = response.xpath('//li[span[contains(text(),"Modèle")]]/span[2]/text()').get()
        for key, value in self.categories.items():
            if any([x in model.lower() for x in value]):
                watch['category'] = key
        if watch.get('category') is not None:
            watch['stores'] = "kronos360"
            watch['brand'] = response.xpath('//span[@itemprop = "brand"]/text()').get()
            watch['model'] = model
            watch['price_whole'] = response.xpath('//span[@class="current-price"]/text()').get()
            end_url = response.meta.get('url')
            if end_url :
                watch['url'] = response.urljoin(end_url)
            watch['image_url'] = response.xpath('//img[contains(@alt,"Rolex")]/@src').get()
            watch['dimensions'] = response.xpath('//li[span[contains(text(),"Dimensions")]]/span[2]/text()').get()
            yield watch