# coding: UTF-8
# Author: orange@chroot.org
# 

import requests
import socket
import time
from multiprocessing.dummy import Pool as ThreadPool
try:
    requests.packages.urllib3.disable_warnings()
except:
    pass

def run(i):
    while 1:
        HOST = '13.115.31.205'
        PORT = 80
        s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        s.connect((HOST, PORT))
        s.sendall('GET / HTTP/1.1\nHost: 54.238.212.199\nConnection: Keep-Alive\n\n')
        # s.close()
        print 'ok'
        time.sleep(0.5)

i = 8
pool = ThreadPool( i )
result = pool.map_async( run, range(i) ).get(0xffff)
