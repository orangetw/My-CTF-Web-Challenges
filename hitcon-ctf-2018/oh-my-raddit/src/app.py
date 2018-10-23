# coding: UTF-8
import os
import web
import urllib
import urlparse
from Crypto.Cipher import DES

web.config.debug = False
ENCRPYTION_KEY = 'megnnaro'


urls = (
    '/', 'index'
)
app = web.application(urls, globals())
db = web.database(dbn='sqlite', db='db.db')


def encrypt(s):
    length = DES.block_size - (len(s) % DES.block_size)
    s = s + chr(length)*length

    cipher = DES.new(ENCRPYTION_KEY, DES.MODE_ECB)
    return cipher.encrypt(s).encode('hex')

def decrypt(s):
    try:
        data = s.decode('hex')
        cipher = DES.new(ENCRPYTION_KEY, DES.MODE_ECB)

        data = cipher.decrypt(data)
        data = data[:-ord(data[-1])]
        return dict(urlparse.parse_qsl(data))
    except Exception as e:
        print e.message
        return {}

def get_posts(limit=None):
    records = []
    for i in db.select('posts', limit=limit, order='ups desc'):
        tmp = {
            'm': 'r', 
            't': i.title.encode('utf-8', 'ignore'), 
            'u': i.id, 
        } 
        tmp['param'] = encrypt(urllib.urlencode(tmp))
        tmp['ups'] = i.ups
        if i.file:
            tmp['file'] = encrypt(urllib.urlencode({'m': 'd', 'f': i.file}))
        else:
            tmp['file'] = ''
        
        records.append( tmp )
    return records

def get_urls():
    urls = []
    for i in [10, 100, 1000]:
        data = {
            'm': 'p', 
            'l': i
        }
        urls.append( encrypt(urllib.urlencode(data)) )
    return urls

class index:
    def GET(self):
        s = web.input().get('s')
        if not s:
            return web.template.frender('templates/index.html')(get_posts(), get_urls())
        else:
            s = decrypt(s)
            method = s.get('m', '')
            if method and method not in list('rdp'):
                return 'param error'
            if method == 'r':
                uid = s.get('u')
                record = db.select('posts', where='id=$id', vars={'id': uid}).first()
                if record:
                    raise web.seeother(record.url)
                else:
                    return 'not found'
            elif method == 'd':
                file = s.get('f')
                if not os.path.exists(file):
                    return 'not found'
                name = os.path.basename(file)
                web.header('Content-Disposition', 'attachment; filename=%s' % name)
                web.header('Content-Type', 'application/pdf')
                with open(file, 'rb') as fp:
                    data = fp.read()
                return data
            elif method == 'p':
                limit = s.get('l')
                return web.template.frender('templates/index.html')(get_posts(limit), get_urls())
            else:
                return web.template.frender('templates/index.html')(get_posts(), get_urls())


if __name__ == "__main__":
    app.run()
