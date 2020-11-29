#!/usr/bin/python3
# coding: utf-8

import time, sys, json, selenium
from redis import Redis
from selenium import webdriver
from rq import Connection, Worker, Queue
import logging

logging.getLogger('rq.worker').addHandler(logging.FileHandler("rq.log"))
logging.getLogger('rq.worker').addHandler(logging.StreamHandler(sys.stdout))


FLAG = 'hitcon{var flag.map(arbitrary-file-upload-to-XSS-again!)}'
CONFIG   = json.load(open('oStyle/config.json', 'rb'))
URL_BASE   = CONFIG.get('URL_BASE')
REDIS_PASS = CONFIG.get('REDIS_PASS')

def add(url):
    chrome_options = webdriver.ChromeOptions()
    chrome_options.add_argument('--headless')
    chrome_options.add_argument('--disable-gpu')
    chrome_options.add_argument('--disable-dev-shm-usage')
    chrome_options.add_experimental_option("prefs", {
            'download.prompt_for_download': True, 
            'download.default_directory': '/dev/null'
    })
    # chrome_options.add_argument('--no-sandbox')

    client = webdriver.Chrome('./chromedriver', chrome_options=chrome_options)
    client.set_page_load_timeout(5)
    client.set_script_timeout(5)

    client.get(URL_BASE)
    client.add_cookie({
        'name': 'this-is-for-you', 
        'value': FLAG, 
    })

    with open('save_url.log', 'a+') as fp:
        print(url)
        fp.write(url + '\n')
    client.get(url)
    i = 0
    while 1:
        try:
            client.switch_to_alert().accept()
            i += 1
            if i > 1000:
                break
        except selenium.common.exceptions.NoAlertPresentException:
            break

    time.sleep(2)
    client.quit()

if __name__ == '__main__':
    if 'test' in sys.argv:
        add(sys.argv[2])
        exit()

    with Connection(connection=Redis(host="127.0.0.1", password=REDIS_PASS)):
        w = Worker('default', log_job_description=True, serializer=json)
        w.work()
