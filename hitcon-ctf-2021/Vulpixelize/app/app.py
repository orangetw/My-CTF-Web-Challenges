# coding: UTF-8
import io, os, sys, uuid

from subprocess import run, PIPE
from hashlib import md5

from PIL import Image
from selenium import webdriver, common
from flask import Flask, render_template, request

secret = run(['/read_secret'], stdout=PIPE).stdout
FLAG   = 'hitcon{%s}' % '-'.join(md5(secret).hexdigest())
def init_chrome():
    options = webdriver.ChromeOptions()
    options.add_argument('--headless')
    options.add_argument('--disable-gpu')
    options.add_argument('--disable-dev-shm-usage')
    options.add_argument('--window-size=1920x1080')
    options.add_experimental_option("prefs", {
        'download.prompt_for_download': True, 
        'download.default_directory': '/dev/null'
    })

    driver = webdriver.Chrome(options=options)
    driver.set_page_load_timeout(5)
    driver.set_script_timeout(5)

    return driver

def message(msg):
    return render_template('index.html', msg=msg)

### initialize ###
driver = init_chrome()
app = Flask(__name__)
### initialize ###


@app.route('/flag')
def flag():
    if request.remote_addr == '127.0.0.1':
        return message(FLAG)
    return message("allow only from local")
    
@app.route('/', methods=['GET'])
def index():
    return render_template('index.html')

@app.route('/submit', methods=['GET'])
def submit():
    path = 'static/images/%s.png' % uuid.uuid4().hex
    url  = request.args.get('url')
    if url:
        # secrity check
        if not url.startswith('http://') and not url.startswith('https://'):
            return message(msg='malformed url')

        # access url
        try:
            driver.get(url)
            data = driver.get_screenshot_as_png()
        except common.exceptions.WebDriverException as e:
            return message(msg=str(e))

        # save result
        img = Image.open(io.BytesIO(data))
        img = img.resize((64,64), resample=Image.BILINEAR)
        img = img.resize((1920,1080), Image.NEAREST)
        img.save(path)
        
        return message(msg=path)
    else:
        return message(msg="url not found :(")

if __name__ == '__main__':
    app.run('0.0.0.0', 8000)