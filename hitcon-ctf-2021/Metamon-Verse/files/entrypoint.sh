#!/bin/bash

# perm
chown nobody.nogroup -R /app/*
chown root.root /*flag
chmod 400 /flag
chmod 111 /readflag
chmod +s  /readflag

# service
mkdir /data
ln -s /data/ /app/static/images
mount -t nfs nfs.server:/data /data -o nolock

# run
timeout -s 9 900 su -s /bin/bash nobody -c 'python3 /app/app.py'