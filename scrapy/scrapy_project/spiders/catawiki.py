import scrapy
from ..items import ScrapyProjectItem

class amazon_Spider(scrapy.Spider):
    name = 'catawiki'
    start_urls = ['https://www.catawiki.com/fr/s?q=montre&filters=909%255B%255D%3D60922']
    
      
def parse(self, response):
    # Extraction des liens des montres et des prix sur la page actuelle
    watches_links = response.xpath('//article[@class="c-lot-card__container"]/a/@href').getall()
    prices = response.xpath('//article[@class="c-lot-card__container"]/a/div[2]/p[3]/text()').getall()

    # Associer chaque montre à son prix et suivre le lien
    for index, link in enumerate(watches_links):
        price = prices[index] if index < len(prices) else None  # Éviter les erreurs d'index
        yield response.follow(link, self.parse_link, meta={'url': link, 'price': price})

    # Gestion de la pagination (limiter à 5 pages)
    current_page = response.meta.get('page', 1)  # Page actuelle par défaut : 1
    if current_page < 3:  # Limiter à 5 pages
        next_page = f'https://www.catawiki.com/fr/s?q=montre&filters=909%255B%255D%3D60922&page={current_page + 1}'
        yield scrapy.Request(next_page, self.parse, meta={'page': current_page + 1})

              
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