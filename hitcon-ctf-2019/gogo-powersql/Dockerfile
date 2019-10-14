FROM ubuntu:latest
MAINTAINER <Orange Tsai> orange@chroot.org

EXPOSE 80/tcp

WORKDIR /root/
RUN apt-get update
RUN apt install -y git make gcc libmysqlclient20
RUN git clone https://github.com/embedthis/goahead.git

WORKDIR /root/goahead/
RUN git checkout v4.0.0
RUN sed -i 's/DME_DEBUG=1/DME_DEBUG=0/' projects/goahead-linux-static.mk
RUN make PROFILE=static ME_COM_SSL=0 ME_GOAHEAD_SSL=0 ME_COM_MBEDTLS=0 DEBUG=0
RUN make  -- DEBUG=0 ME_COM_MBEDTLS=0 ME_GOAHEAD_SSL=0 ME_COM_SSL=0 PROFILE=static install

WORKDIR /etc/goahead
RUN mkdir web/
RUN mkdir cgi-bin/
RUN sed -i 's/CGI/\x0\x0\x0/g' /usr/local/bin/goahead
COPY db.conf    .
COPY route.txt  .
COPY index.html web/
COPY query      cgi-bin/

COPY FLAG       /
CMD ["goahead", "-v"]