compile:
	gcc -o files/readflag files/readflag.c

build: compile clean
	docker build -t metamon-verse . --no-cache

test-run:
	docker run --rm -p 12345:80 --name test --add-host=nfs.server:host-gateway --log-driver=syslog --privileged=true --env CTF_PASSWD=ctf -t -i metamon-verse

test-exec:
	docker exec -ti test /bin/bash

pack: compile
	rm -rf static/metamon-verse.tgz
	tar --exclude='flag' --exclude='readflag.c' --transform='flags=r;s|fake-flag|flag|' -zcvf static/metamon-verse.tgz Dockerfile app/ files/

mount:
	mount -t ext4 /dev/nvme1n1 /data

clean:
	-docker rm  -f `docker ps -a -q`
	-docker rmi -f `docker images -a -q`

run: pack
	python3 run.py 2>&1 | tee -a logs/log.txt

debug-run:
	python3 run.py debug