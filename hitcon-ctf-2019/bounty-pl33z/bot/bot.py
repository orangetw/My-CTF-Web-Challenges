#!/usr/bin/python
# coding: utf-8

from selenium import webdriver
import time, sys, json, selenium

# cd /bot/ && rq worker -v -u redis://:orangenogg@127.0.0.1:6379/

def add(url):
    chrome_options = webdriver.ChromeOptions()
    chrome_options.add_argument('--headless')
    chrome_options.add_argument('--disable-gpu')
    chrome_options.add_argument('--disable-dev-shm-usage')
    # chrome_options.add_argument('--no-sandbox')

    with open('config.json', 'r') as fp:
        ADMIN_URL = json.load(fp)['admin_url']

    client = webdriver.Chrome(chrome_options=chrome_options)
    client.set_page_load_timeout(5)
    client.set_script_timeout(5)
    client.get(ADMIN_URL)
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
    add(sys.argv[1])
