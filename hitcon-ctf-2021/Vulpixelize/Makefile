compile:
	gcc -o files/read_secret files/read_secret.c

build: compile clean
	docker build -t vulpixelize . --no-cache

test:
	docker run --rm -p 12345:8000 --name test --log-driver="syslog" --env CTF_PASSWD=ctf -ti vulpixelize

pack: compile
	rm -rf static/vulpixelize.tgz
	tar --exclude='secret' --exclude='read_secret.c' --transform='flags=r;s|fake-secret|secret|' -zcvf static/vulpixelize.tgz Dockerfile app/ files/

flag:
	python3 -c 's=__import__("hashlib").md5(open("files/secret").read().encode()).hexdigest();print("hitcon{" + "-".join(list(s)) + "}")'

run: pack
	python3 run.py 2>&1 | tee -a logs/log.txt

clean:
	-docker rm  -f `docker ps -a -q`
	-docker rmi -f `docker images -a -q`