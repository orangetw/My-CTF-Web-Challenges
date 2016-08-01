#!/usr/bin/python
# coding: utf-8

import os
import re
import sys
import cgi
import hashlib
from urllib import unquote
from passlib.utils.pbkdf2 import pbkdf2

###

sys.path.append('/home/orange/secret_file')
from secret_file import SECRET # 160 bytes secret
from secret_file import FLAG

###

print 'Content-Type: text/html\n\n'

### 

def _pbkdf2(text):
    return pbkdf2(text, 'noggnogg', 1337).encode('hex').lower()

def _md5(text):
    return hashlib.md5( text ).hexdigest().lower()

def getenv(name):
    return unquote( os.environ.get(name) ) or ''

def gotoFail():
    print 'goto fail'
    print
    exit()

def m_hash(password):
    nr = int( 'P0W5'.encode('hex'), 16 )
    add = 7
    nr2 = 305419889

    for c in (ord(x) for x in password if x not in (' ', '\t')):
        nr^= (((nr & 63)+add)*c)+ (nr << 8) & 0xFFFFFFFF
        nr2= (nr2 + ((nr2 << 8) ^ nr)) & 0xFFFFFFFF
        add= (add + c) & 0xFFFFFFFF

    return "%08x%08x" % (nr & 0x7FFFFFFF,nr2 & 0x7FFFFFFF)
###

request = cgi.FieldStorage() 

checksum  = request.getvalue('checksum') or ''
query_str = getenv('QUERY_STRING')
if _md5( SECRET + query_str ) == checksum:
    mode = request.getvalue('mode') or ''

    if mode == 'download':
        filename = request.getvalue('filename') or ''
        filename = os.path.basename( filename )
        try:
            print open(filename).read()
        except IOError as e:
            print 'No such file or directory'
    elif mode == 'eval':
        bad_string = request.getvalue('filename') or ''
        good_string = bad_string.encode('hex')
        eval(good_string)

    else:
        stage1 = request.getvalue('stage1') or ''
        if m_hash(stage1) != '4141414141414141':
            gotoFail()

        ### 
        
        plaintext = getenv('HTTP_USER_AGENT')
        stage2 = request.getvalue('stage2') or ''
        if stage2 == plaintext:
            gotoFail()
        
        if _pbkdf2(plaintext) != _pbkdf2(stage2):
            gotoFail()

        ###

        stage3 = request.getvalue('stage3') or ''
        stage3 = stage3[0]+stage3[1]+stage3[3]+stage3[5]
        if _md5( stage3 ) != '90954349a0e42d8e4426a4672bde16b9':
            gotoFail()

        ###

        print 'Congrat! The flag is', 
        print 'HITCON{%s}' % FLAG

else:
    checksum = _md5( SECRET + 'filename=py4h4sher&mode=download' )
    print """
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
  <meta name="author" content="orange@chroot.org">
  <title> PY4H4SHER </title>
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">

  <style>
/* 
Inspired by http://dribbble.com/shots/890759-Ui-Kit-Metro/attachments/97174
*/
.out {
    white-space: -moz-pre-wrap;
    white-space: -pre-wrap;
    white-space: -o-pre-wrap;
    white-space: pre-wrap; 
    word-wrap: break-word; /* Internet Explorer 5.5+ */ 
    background-color: white;
    border: 0px;
}

.nav-row {
  text-align: center;
}
.nav-row p {
  padding: 5px;
}
.nav-row .col-md-2 {
  background-color: #fff;
  border: 1px solid #e0e1db;
  border-right: none;
}
.nav-row .col-md-2:last-child {
  border: 1px solid #e0e1db;
}
.nav-row .col-md-2:first-child {
  border-radius: 5px 0 0 5px;
}
.nav-row .col-md-2:last-child {
  border-radius: 0 5px 5px 0;
}
.nav-row .col-md-2:hover {
  color: #e92d00;
  cursor: pointer;
}
.nav-row .glyphicon {
  padding-top: 15px;
  font-size: 40px;
}

  </style>
   <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
</head>
<body>             
<script>
  var script_name = '/cgi-bin/py4h4sher';

  function gohome(){
    window.open( 'http://hitcon.org' );
  }
  function getflag(){
      $.get( script_name , 
              function(data){         
                  $('.out').text('nothing to do :(');
              });
  }
  function getsource(){
      $.post( script_name + '?filename=py4h4sher&mode=download', 
              {'checksum': '%s'},
              function(data){
                  $('.out').text(data);
              });
  }
</script>

<div class="container" style="margin-top:160px;">
    <div class="row nav-row">
      <div class="col-md-3">
      </div>
      <div class="col-md-2" onclick='gohome()'>
        <span class="glyphicon glyphicon-home"></span>
        <p> Go Home </p>
      </div>
      <div class="col-md-2" onclick='getflag()'>
        <span class="glyphicon glyphicon-flag"></span>
        <p> Get Flag </p>
      </div>
      <div class="col-md-2" onclick='getsource()'>
        <span class="glyphicon glyphicon-cloud-download"></span>
        <p> Get Source </p>
      </div>
    </div>

    <div class='row nav-row'>
      <pre class='out' style='padding-top:64px; '>
      </pre>
    </div>
</div>

</body>
</html>
""" % checksum
