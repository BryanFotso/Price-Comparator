import scrapy
from ..items import ScrapyProjectItem

class amazon_Spider(scrapy.Spider):
    name = 'kronos360'
    start_urls = ['https://www.kronos360.com/fr/montre-collection/rolex/']
      
    def parse(self, response):
        # Extraction des liens des montres sur la page actuelle
        watches_links = response.xpath('//strong[@class="product-name"]/a/@href').getall()
        for link in watches_links:
            yield response.follow(link, self.parse_link, meta={'url': link})

        # Pagination - limiter à 4 pages
        current_page = response.meta.get('page', 1) 
        if current_page < 5: 
            next = response.xpath('//div[@class="pagination_next"]/a/@href').get()
            if next is not None:
                next_link = response.urljoin(next)
                yield scrapy.Request(next_link, self.parse, meta={'page': current_page + 1})

              
    def parse_link(self, response):
        
        watch = ScrapyProjectItem()
        model = response.xpath('//li[span[contains(text(),"Modèle")]]/span[2]/text()').get()
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
        watch['image_url'] = response.xpath('//img[contains(@alt,"Rolex")]/@src').get()
        if watch.get('category') is not None and watch.get('image_url') is not None:
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