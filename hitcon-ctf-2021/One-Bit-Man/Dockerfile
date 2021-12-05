FROM wordpress:5.8.2-apache
MAINTAINER <Orange Tsai> orange@chroot.org

EXPOSE 80/tcp

RUN DEBIAN_FRONTEND=noninteractive apt update && apt install -y --no-install-recommends mariadb-server

COPY files/entrypoint.sh /entrypoint.sh
COPY files/init.sql      /init.sql
COPY files/hack.php      /hack.php
COPY files/htaccess      /var/www/html/.htaccess
COPY files/readflag      /readflag
COPY files/flag          /flag

WORKDIR /var/www/html/
CMD ["/entrypoint.sh"]