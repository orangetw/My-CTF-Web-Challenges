# coding: UTF-8
import os, sys
from hashlib import md5
from functools import wraps
from flask import Flask, render_template, request

import pycurl
import certifi

PORT = 80

def login_required(f):
    @wraps(f)
    def wrapped_view(**kwargs):
        def check_auth(username, password):
            return username == 'ctf' and password == os.environ['CTF_PASSWD']
        auth = request.authorization
        if not (auth and check_auth(auth.username, auth.password)):
            return ('Unauthorized', 401, {
                'WWW-Authenticate': 'Basic realm="Login Required"'
            })

        return f(**kwargs)

    return wrapped_view

app = Flask(__name__)
app.config['TEMPLATES_AUTO_RELOAD'] = True

@app.route('/', methods=['GET'])
@login_required
def index():
    return render_template('index.html')

@app.route('/', methods=['POST'])
@login_required
def submit():
    url = request.form.get('url')
    if not url:
        return render_template('index.html', msg='empty url')

    opt_name, opt_value = None, None
    for key, value in request.form.items():
        if key.startswith('CURLOPT_'):
            name = key.split('_', 1)[1].upper()
            try:
                opt_name  = getattr(pycurl, name)
                opt_name  = int(opt_name)
                opt_value = int(value)
            except (AttributeError, ValueError, TypeError):
                break

            break

    name = md5(request.remote_addr.encode() + url.encode()).hexdigest()
    filename = 'static/images/%s.jpg' % name
    with open(filename, 'wb+') as fp:
        c = pycurl.Curl()
        c.setopt(c.URL, url)
        c.setopt(c.WRITEDATA, fp)
        c.setopt(c.CAINFO, certifi.where())

        if opt_name and opt_value:
            c.setopt(opt_name, opt_value)
        
        try:
            c.perform()
            c.close()
            msg = filename
        except pycurl.error as e:
            msg = str(e)
        
    return render_template('index.html', msg=msg)

if __name__ == '__main__':
    if 'debug' in sys.argv:
        app.debug = True
        PORT = 8000

    app.run('0.0.0.0', PORT)