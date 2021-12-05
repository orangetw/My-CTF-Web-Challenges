#!/bin/bash

# perm
chown root.root /*secret
chmod 400 /secret
chmod 111 /read_secret
chmod +s  /read_secret
chown seluser.seluser /app/static/images/


# run
timeout -s 9 900 su -s /bin/bash seluser -c 'python3 /app/app.py'