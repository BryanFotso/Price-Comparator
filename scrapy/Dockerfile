FROM python:3.12

WORKDIR /app

COPY ./ /app/

RUN pip install --no-cache-dir -r requirements.txt

RUN apt-get update && apt-get install -y netcat-openbsd && apt-get clean

# Rendre le script d'entrée exécutable
COPY entrypoint.sh /app/entrypoint.sh
RUN chmod +x /app/entrypoint.sh

# Définir le script d'entrée comme commande par défaut
ENTRYPOINT ["/app/entrypoint.sh"] 