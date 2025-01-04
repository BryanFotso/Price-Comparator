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
scrapy crawl catawiki
scrapy crawl kronos360


# Garder le conteneur actif après l'exécution des spiders (facultatif)
# La commande tail -f /dev/null lit de manière continue un fichier vide (/dev/null), ce qui garde le processus actif sans rien faire.
tail -f /dev/null
