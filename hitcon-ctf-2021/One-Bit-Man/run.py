#!/usr/bin/python -u

import os, sys
import shutil
import uuid
import string
from time import sleep
from random import shuffle
from subprocess import run, PIPE

from flask import Flask, request, make_response

from redis import Redis

'''
pip install redis
apt install redis-server
'''

PORT = 80
PUBLIC_ADDRESS  = '18.177.186.21'
AVAILABLE_PORTS = list(range(20000, 40000))
shuffle(AVAILABLE_PORTS)

INDEX = '''
Your <a target='_blank' href='{BASE}'>WordPress</a> is launched, please login with <b><font color=red>ctf / {PASS}</font></b>. If the WordPress is unreachable for a long time and you have verified you are all good on the local enviroment. Try contact @orange on <a href="https://discord.gg/x6bMqQu3">Discord</a>.

<h3>Exec Command:</h3>    <b>> {CMD1}</b>
<h3>Exec Result:</h3>    <b>> {RES}</b>
'''


app  = Flask(__name__)
conn = Redis(host='127.0.0.1', password='')

def my_exec(cmds):
    return run(cmds, stdout=PIPE, stderr=PIPE)

def checkf_filename(filename):
    if not filename.startswith('/var/www/html/'):
        return False

    normalized = os.path.normpath(filename)
    if normalized != filename:
        return False

    if not os.path.exists(filename.replace('/var/www/html/', './wordpress/')):
        return False

    return True

def check_position(filename, position):
    size = os.path.getsize(filename.replace('/var/www/html/', './wordpress/'))
    if position < 0:
        return False
    if position > size:
        return False

    return True

def response(msg):
    msg = "<pre style='white-space: pre-wrap'>\n" + msg
    return msg

@app.route('/')
def index():
    msg  = ''
    msg += '<a target="_blank" href="static/one-bit-man.tgz">Download Dockerfile</a><br><br>\n'
    msg += '<form method="POST" action="/" autocomplete="off">\n'
    msg += 'Filename: <input type="text" placeholder="/var/www/html/index.php" name="filename"> <br>\n'
    msg += 'Position: <input type="text" placeholder="0" name="position"> <br>\n'
    msg += 'Flipping-bit: <select name="bit">\n'
    msg += '<option value="0">0</option>\n'
    msg += '<option value="1">1</option>\n'
    msg += '<option value="2">2</option>\n'
    msg += '<option value="3">3</option>\n'
    msg += '<option value="4">4</option>\n'
    msg += '<option value="5">5</option>\n'
    msg += '<option value="6">6</option>\n'
    msg += '<option value="7">7</option>\n'
    msg += '</select> <br>\n'
    msg += '<input type="submit">\n'
    msg += '</form> <br>\n'
    msg += '<i>P.S. We limit the docker-lanching rate (once per minute) by your IP address.</i>'
    return msg

@app.route('/', methods=['POST'])
def submit():
    filename = request.form.get('filename', '')
    position = request.form.get('position', -1)
    bit_pos  = request.form.get('bit', -1)

    try:
        position, bit_pos = int(position), int(bit_pos)
    except ValueError:
        return response('bad number')

    if not checkf_filename(filename):
        return response('bad filename')
    if not check_position(filename, position):
        return response('bad position')
    if bit_pos not in range(8):
        return response('bad bit')
    
    key = 'lock_%s' % request.remote_addr
    if conn.get(key):
        return response('too quick... %d second remaining' % conn.ttl(key))
    else:
        conn.setex(key, 60, 'ok')

    password  = uuid.uuid4().hex[:16]
    port = AVAILABLE_PORTS.pop()
    name = 'team-%s' % uuid.uuid4().hex[:16]
    base = 'http://%s:%d/' % (PUBLIC_ADDRESS, port)
    launch_cmd = [
        'docker', 'run', '--rm', 
        '-p', '%d:80' % port, 
        '--name', name, 
        # '-v logs/log-%s:/log.txt' % name, 
        '--log-driver=syslog', 
        '--env', 'CTF_PASSWD=%s' % password, 
        '--env', 'CTF_BASE=%s' % base, 
        '--env', 'CTF_FILENAME=%s' % filename, 
        '--env', 'CTF_POSITION=%s' % str(position), 
        '--env', 'CTF_BITPOS=%s' % str(bit_pos), 
        '-itd', 'one-bit-man'
    ]

    log_cmd = 'IP=[%s] port=[%d] name=[%s] file=[%s] pos=[%d] bit=[%d]\n' % (request.remote_addr, port, name, filename, position, bit_pos)
    with open('logs/team.log', 'a+') as fp:
        fp.write(log_cmd)

    p = my_exec(launch_cmd)
    sleep(3)
    result = p.stdout.decode()
    if p.stderr:
        result = p.stderr.decode()

    msg = INDEX.strip()
    msg = msg.replace('{BASE}', base)
    msg = msg.replace('{PASS}', password)
    msg = msg.replace('{CMD1}', ' '.join(launch_cmd))
    msg = msg.replace('{RES}', result)
    return response(msg)

if __name__ == '__main__':
    if 'debug' in sys.argv:
        app.debug = True
        PORT = 8000

    if not os.path.exists('./wordpress/'):
        print('./wordpress/ not found!')
        exit()

    print('Listening on http://%s:%d/' % (PUBLIC_ADDRESS, PORT))
    app.run('0.0.0.0', PORT)
