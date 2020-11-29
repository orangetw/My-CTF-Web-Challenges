FROM alpine:3.7
MAINTAINER Orange Tsai<orange@chroot.org>

# add user
RUN adduser oShell -h / -s /bin/nologin -D -u 1337 -h /home/oShell/

# copy file
ADD oShell.py /
ADD readflag /
ADD flag /
ADD tcpdump /bin/


# cmd
RUN apk update
RUN apk add htop strace procps libcap python2

# permission
RUN chmod 400 /flag
RUN chmod +s /readflag
RUN chmod 774 /oShell.py

# setup
RUN setcap cap_net_raw=eip /bin/tcpdump cap_net_raw=eip /bin/busybox
RUN rm -rf /usr/bin/top && sed -i 's/UNKNOWN/3.3.12 /g' /bin/top

CMD ["sleep", "300"]