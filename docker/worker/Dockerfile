FROM php:8.4.5RC1-cli-bookworm

ARG UNIX_UID=1000
ARG UNIX_GID=1000

RUN groupadd -g ${UNIX_GID} marvin || true
RUN useradd -u ${UNIX_UID} -g ${UNIX_GID} -m -s /bin/bash marvin || true

RUN apt update && apt install -y curl supervisor

COPY docker/worker/config/supervisord.conf /etc/supervisor/supervisord.conf

# Install PHP extensions
RUN curl -sSLf -o /usr/local/bin/install-php-extensions https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions && chmod +x /usr/local/bin/install-php-extensions
RUN install-php-extensions @composer pdo_pgsql amqp swoole apcu intl opcache zip
RUN docker-php-ext-configure pcntl --enable-pcntl && docker-php-ext-install pcntl;

# Run supervisor
CMD ["/usr/bin/supervisord", "-n", "-c",  "/etc/supervisor/supervisord.conf"]
