# Define your item pipelines here
#
# Don't forget to add your pipeline to the ITEM_PIPELINES setting
# See: https://docs.scrapy.org/en/latest/topics/item-pipeline.html


# useful for handling different item types with a single interface
from itemadapter import ItemAdapter
import mysql.connector
from dotenv import load_dotenv
import os

load_dotenv()

class ScrapyProjectPipeline:
    def process_item(self, item, spider):
        return item

class MySQLPipeline:
    def open_spider(self, spider):
        self.connection = mysql.connector.connect(
            host='localhost',
            user=os.getenv("MYSQL_USER"),
            password=os.getenv("MYSQL_PASSWORD"),
            db=os.getenv("MYSQL_DATABASE")
        )
        self.cursor = self.connection.cursor()

    def close_spider(self, spider):
        self.cursor.close()
        self.connection.close()

    def process_item(self, item, spider):
        sql = """
            INSERT INTO watch_db (brand, model, price, url)
            VALUES (%s, %s, %s, %s)
        """ 
        
        val = (item['brand'], item['model'], item['price_whole'], item['url'])
        self.cursor.execute(sql,val)
        self.connection.commit()
        return item