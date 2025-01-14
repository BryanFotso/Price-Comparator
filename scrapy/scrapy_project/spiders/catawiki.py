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

        # Gestion de la pagination (limiter à 4 pages)
        for page in range(1, 5):
            next_page = f'https://www.catawiki.com/fr/s?q=montre&filters=909%255B%255D%3D60922&page={page}'
            yield scrapy.Request(next_page, self.parse)
              
    def parse_link(self, response):
        
        watch = ScrapyProjectItem()
        model = response.xpath('//div[span[contains(text(),"Modèle")]]/div/span/text()').get()
        categories = {'Lady-datejust':['lady datejust','lady date just','lady-date just','Datejust Lady','Datejust-Lady','DatejustLady','Lady-Datejust','LadyDatejust'],
                  'Oyster Perpetual' : ['OysterPerpetual','Oyster Perpetual','Oyster-Perpetual'],
                  'Day-Date' : ['DayDate','Day Date','Day-Date'],
                'Datejust' : ['Date-just','Date Just','Datejust'],
                'Submariner' : ['Submariner',],
                'Daytona' : ['Daytona','Day tona','Day-tona'],
                'GMT-Master' : ['GMT-Master','GMT Master','GMTMaster'],
                'Explorer' : ['Explorer'],
                'Yacht-Master' : ['Yacht-Master','Yacht Master','YachtMaster'],
                'Sea-Dweller' : ['Sea-Dweller','Sea Dweller','SeaDweller'],
                'Sky-Dweller' : ['Sky-Dweller','Sky Dweller','SkyDweller'],
                'Air-King' : ['Air-King','Air King','AirKing'],
                }
        for key, value in categories.items():
            if any([x.lower() in model.lower() for x in value]):
                watch['category'] = key
        if watch.get('category') is not None:
            watch['stores'] = "Catawiki"
            watch['model'] = model
            watch['brand'] = response.xpath('//div[span[contains(text(),"Marque")]]/div/span/text()').get()
            watch['price_whole'] = response.meta.get('price').replace('\xa0', ' ').strip()
            end_url = response.meta.get('url')
            if end_url :
                watch['url'] = response.urljoin(end_url)    
            watch['dimensions'] = response.xpath('//div[span[contains(text(),"Diameter/ Width Case")]]/div/span/text()').get()
            watch['image_url'] = response.xpath("//img[contains(@alt, 'Rolex')]/@src").get()
            yield watch