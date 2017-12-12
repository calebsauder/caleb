FROM csmjulian/resin-wpe:latest
COPY udev-rules/ /etc/udev/rules.d/
COPY php.ini /etc/php/apache2-php7/php.ini
COPY htdocs/ /usr/share/apache2/htdocs/
#testCOPY crontab /etc/crontab
ENV WPE_URL="http://localhost/index.php"
COPY wpe-init /wpe-init
CMD ["/wpe-init"]
