
FROM debian

RUN apt-get update && \
    apt-get install -y apache2 && \
    apt-get install -y libapache2-mod_php8.2 && \
    apt-get install -y php_mysql

COPY ./www /var/www/html/

EXPOSE 80

# Démarrage du serveur Apache
ENTRYPOINT [ "/usr/sbin/apache2ctl", "-D", "FOREGROUND" ]