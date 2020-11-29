#!/bin/bash

docker rm -f `docker ps -a -q`
docker rmi -f oshell

docker build . -t oshell
# docker run -ti --name team-$1 -u oShell oshell