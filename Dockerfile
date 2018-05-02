FROM csmjulian/resin-wpe:eighth
COPY splash-black.png /resin-boot/splash/resin-logo.png
COPY udev-rules/ /etc/udev/rules.d/
COPY php.ini /etc/php/apache2-php7/php.ini
COPY htdocs/ /usr/share/apache2/htdocs/
COPY crontab /var/spool/cron/root
ENV WPE_URL="http://localhost/index.php"
COPY wpe-init /wpe-init
CMD ["/wpe-init"]


ENV INITSYSTEM on

RUN apt-get update \
    && apt-get install -y dnsmasq wireless-tools \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /usr/src/app

RUN curl https://api.github.com/repos/resin-io/resin-wifi-connect/releases/latest -s \
    | grep -hoP 'browser_download_url": "\K.*%%RESIN_ARCH%%\.tar\.gz' \
    | xargs -n1 curl -Ls \
    | tar -xvz -C /usr/src/app/

COPY scripts/start.sh .

CMD ["bash", "start.sh"]
