FROM nginx:alpine
ARG UID
ARG GID

# bash
RUN apk add --no-cache bash

#RUN apk add --no-cache fail2ban
COPY docker/images/nginx/jail.local /etc/fail2ban/jail.local
COPY docker/images/nginx/paths-overrides.local /etc/fail2ban/paths-overrides.local
COPY docker/images/nginx/filter.d/nginx-401.conf /etc/fail2ban/filter.d/nginx-401.conf
COPY docker/images/nginx/filter.d/nginx-404.conf /etc/fail2ban/filter.d/nginx-404.conf

# user
USER root
RUN mkdir -p /root/app/temp && \
    mkdir -p /root/app/log && \
    touch /root/app/log/access.log && \
    touch /root/app/log/error.log

COPY public /var/www/project/public


CMD exec nginx -c /etc/nginx/conf.d/default.conf

