#!/bin/sh -x

if [ ! -f /home/project/certs/${DOMAIN}.pem ]; then
  apt update
  apt install haveged
  service haveged start
  openssl dhparam -out /etc/nginx/dhparam.pem 2048
  openssl req -x509 -newkey rsa:2048 -nodes -keyout /home/project/certs/${DOMAIN}.key \
    -out /home/project/certs/${DOMAIN}.pem -days 365 -subj "/C=US/ST=Oregon/L=Portland/O=${DOMAIN}/OU=Org/CN=${DOMAIN}"
fi

nginx -g "daemon off;"
