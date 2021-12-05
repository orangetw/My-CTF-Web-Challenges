compile:
	gcc -o files/readflag files/readflag.c

build: compile clean
	docker build -t one-bit-man . --no-cache

test-run:
	docker run --rm -p 12345:80 --name test --log-driver="syslog" --env CTF_PASSWD=ctf --env "CTF_BASE=http://192.168.11.11:12345/" --env "CTF_FILENAME=/var/www/html/index.php" --env "CTF_POSITION=0" --env "CTF_BITPOS=0" -ti one-bit-man

test-exec:
	docker exec -ti test /bin/bash

pack: compile
	rm -rf static/one-bit-man.tgz
	tar --exclude='flag' --exclude='readflag.c' --transform='flags=r;s|fake-flag|flag|' -zcvf static/one-bit-man.tgz Dockerfile files/

run: pack
	python3 run.py 2>&1 | tee -a logs/log.txt

debug-run:
	python3 run.py debug

clean:
	-docker rm  -f `docker ps -a -q`
	-docker rmi -f `docker images -a -q`