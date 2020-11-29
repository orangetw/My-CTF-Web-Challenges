#!/usr/bin/python3

import sys, json
from rq import Queue
from redis import Redis
from base64 import b64decode

# fix python-rq bug...
import pickle
from functools import partial
class Hook:
    dumps = partial(pickle.dumps, protocol=4)
    loads = pickle.loads


CONFIG     = json.load(open('/config.json', 'r'))
URL_BASE   = CONFIG.get('URL_BASE')
REDIS_HOST = CONFIG.get('REDIS_HOST')
REDIS_PASS = CONFIG.get('REDIS_PASS')

address = sys.argv[1]
url     = b64decode(sys.argv[2]).decode()

def die(msg):
    print(msg)
    exit()

if __name__ == '__main__':
    if len(url) < 12 or not url.startswith(URL_BASE):
        die('Wrong URL ;(')

    try:
        conn = Redis(host=REDIS_HOST, password=REDIS_PASS)
        key  = 'LOCKER_%s' % address

        if conn.get(key):
            die('Too fast ;(')
        else:
            conn.setex(key, 16, 'ok')

            q = Queue(connection=conn, serializer=Hook)
            q.enqueue('bot.add', url)
            die('Please wait for admin ;)')

    except Exception as e:
        die('Something wrong [%s] ;(' % repr(e))