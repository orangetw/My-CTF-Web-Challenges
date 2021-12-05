#!/bin/bash

export WORDPRESS_DB_HOST='127.0.0.1'
export WORDPRESS_DB_USER='wordpress'
export WORDPRESS_DB_PASSWORD='wordpress'
export WORDPRESS_DB_NAME='wordpress'

echo "CTF_BASE=$CTF_BASE"
echo "CTF_PASSWD=$CTF_PASSWD"
echo "CTF_FILENAME=$CTF_FILENAME"
echo "CTF_POSITION=$CTF_POSITION"
echo "CTF_BITPOS=$CTF_BITPOS"

# check
if [[ -z $CTF_BASE || -z $CTF_PASSWD ]]; then
    echo "env CTF_BASE or CTF_PASSWD not found"
    exit -1
fi

if [[ -z $CTF_FILENAME || -z $CTF_POSITION || -z $CTF_BITPOS ]]; then
    echo "env PHP-Hack not found"
    exit -1
fi

# perm
chown root.root /*flag
chmod 400 /flag
chmod 111 /readflag
chmod +s  /readflag

# db
service mariadb start
mysqladmin create wordpress && mysqladmin password root
sed -i 's@{BASE}@'"$CTF_BASE"'@g' /init.sql
mysql -uroot -proot < /init.sql

# web
htpasswd -cb /etc/apache2/.htpasswd ctf "$CTF_PASSWD"

# flip the bit with some dirty hacks...
sed -i 's/exec "$@"//' /usr/local/bin/docker-entrypoint.sh
cat << EOF >> /usr/local/bin/docker-entrypoint.sh

result=\$(php /hack.php "\$CTF_FILENAME" "\$CTF_POSITION" "\$CTF_BITPOS")
if [[ "\$result" != "all good" ]]; then 
    echo \$result
    exit -1
fi

exec "\$@"
EOF

# original command
timeout -s 9 900 docker-entrypoint.sh apache2-foreground