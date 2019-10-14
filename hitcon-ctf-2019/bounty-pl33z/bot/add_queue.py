#!/usr/bin/python
# coding: utf-8

import sys, json
from rq import Queue
from redis import Redis
from base64 import b64decode

import bot

try:
    with open('/bot/config.json', 'r') as fp:
        REDIS_PASSWORD = json.load(fp)['password']
    q = Queue(connection=Redis(password=REDIS_PASSWORD))
    q.enqueue(bot.add, b64decode(sys.argv[1]))
    print 'Done! Please waiting for the admin :)'
except Exception as e:
    print 'Error[%s] Please contact admin' % e.message
