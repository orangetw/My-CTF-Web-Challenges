#!/usr/bin/python

import os, sys
import uuid
from random import shuffle
from subprocess import run, PIPE

from flask import Flask, request, make_response
from redis import Redis

'''
pip install redis
apt install redis-server
'''

PORT = 80
PUBLIC_ADDRESS  = '54.250.88.37'
AVAILABLE_PORTS = list(range(20000, 40000))
shuffle(AVAILABLE_PORTS)

INDEX = '''
Your <a target='_blank' href='{BASE}'>Metamon-Verse</a> is launched, please login with <b><font color=red>ctf / {PASS}</font></b>

<h3>Exec Command:</h3>    <b>> {CMD}</b>
<h3>Exec Result:</h3>    <b>> {RES}</b>
'''


app  = Flask(__name__)
conn = Redis(host='127.0.0.1', password='')

def my_exec(cmds):
    return run(cmds, stdout=PIPE, stderr=PIPE)

def response(msg):
    msg = "<pre style='white-space: pre-wrap'>\n" + msg
    return msg

@app.route('/')
def index():
    msg  = ''
    msg += '<a target="_blank" href="static/metamon-verse.tgz">Download Dockerfile</a> <a target="_blank" href="static/hint.txt">[Hint]</a><br><br>\n'
    msg += '<form method="POST" action="/">\n'
    msg += '<input type="submit" value="Launch">\n'
    msg += '</form> <br> <br>\n'
    msg += '<i>P.S. We limit the docker-lanching rate (once per minute) by your IP address.</i>'
    return msg

@app.route('/', methods=['POST'])
def submit():    
    key = 'lock_%s' % request.remote_addr
    if conn.get(key):
        return response('too quick... %d second remaining' % conn.ttl(key))
    else:
        conn.setex(key, 60, 'ok')

    password  = uuid.uuid4().hex[:16]
    port = AVAILABLE_PORTS.pop()
    name = 'team-%s' % uuid.uuid4().hex[:16]

    launch_cmd = [
        'docker', 'run', '--rm', 
        '-p', '%d:80' % port, 
        '--name', name, 
        '--add-host=nfs.server:host-gateway', 
        '--log-driver=syslog', 
        '--privileged', 
        '--env', 'CTF_PASSWD=%s' % password, 
        '-itd', 'metamon-verse'
    ]
    p = my_exec(launch_cmd)
    result = p.stdout.decode()
    if p.stderr:
        result = p.stderr.decode()

    msg = INDEX.strip()
    msg = msg.replace('{BASE}', 'http://%s:%d/' % (PUBLIC_ADDRESS, port))
    msg = msg.replace('{PASS}', password)
    msg = msg.replace('{CMD}', ' '.join(launch_cmd))
    msg = msg.replace('{RES}', result)
    return response(msg)

if __name__ == '__main__':
    if 'debug' in sys.argv:
        app.debug = True
        PORT = 80

    print('Listening on http://%s:%d/' % (PUBLIC_ADDRESS, PORT))
    app.run('0.0.0.0', PORT)
