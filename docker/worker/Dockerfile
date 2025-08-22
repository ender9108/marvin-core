FROM php:8.4.5RC1-cli-bookworm

RUN id -u ${UNIX_USER} &>/dev/null || adduser --uid ${UNIX_UID} ${UNIX_USER} --gid ${UNIX_GID}
RUN apt update && apt install -y curl supervisor

# Install PHP extensions
RUN curl -sSLf -o /usr/local/bin/install-php-extensions https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions && chmod +x /usr/local/bin/install-php-extensions
RUN install-php-extensions @composer pdo_mysql amqp swoole apcu intl opcache zip
RUN docker-php-ext-configure pcntl --enable-pcntl && docker-php-ext-install pcntl;

# Run supervisor
CMD ["/usr/bin/supervisord", "-n", "-c",  "/etc/supervisor/supervisord.conf"]
