#!/bin/bash

# Vérifier si la base de données MySQL est prête
echo "Attente de la base de données..."
until nc -z -v -w30 mysql 3306; do
   echo "Base de données non disponible, nouvelle tentative dans 5 secondes..."
   sleep 5
done

echo "Base de données prête !"

# Lancer tous les spiders Scrapy
echo "Lancement des spiders..."
scrapy crawl kronos360
scrapy crawl catawiki

# Garder le conteneur actif après l'exécution des spiders (facultatif)
tail -f /dev/null
