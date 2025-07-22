FROM php:8.3-apache

ENV TERM=xterm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    dos2unix \
    git \
    default-mysql-client \
    libzip-dev \
    unzip \
    zip \
    libxml2-dev \
    libonig-dev \
    libicu-dev \
    tzdata \
    curl \
    build-essential \
    ca-certificates \
    gnupg \
 && rm -rf /var/lib/apt/lists/*

RUN ln -fs /usr/share/zoneinfo/America/Sao_Paulo /etc/localtime \
 && dpkg-reconfigure -f noninteractive tzdata

RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    zip \
    intl \
    xml

RUN pecl install pcov \
    && docker-php-ext-enable pcov

RUN { \
      echo "extension=pcov.so"; \
      echo "pcov.enabled=1"; \
      echo "pcov.directory=/var/www/html"; \
    } > /usr/local/etc/php/conf.d/pcov.ini

RUN a2enmod rewrite headers

RUN echo "date.timezone=America/Sao_Paulo" > /usr/local/etc/php/conf.d/timezone.ini


RUN curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=/usr/local/bin --filename=composer


COPY entrypoint/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY entrypoint/tdd.sh       /usr/local/bin/tdd.sh
COPY sh/porcentage.sh        /usr/local/bin/porcentage.sh
COPY sh/color.sh             /usr/local/bin/color.sh

RUN dos2unix /usr/local/bin/*.sh \
 && chmod +x /usr/local/bin/*.sh

COPY etc/host.conf                 /etc/apache2/conf-enabled/host.conf
COPY etc/000-default-backend.conf  /etc/apache2/sites-enabled/000-default.conf
COPY etc/apache2.conf              /etc/apache2/apache2.conf
COPY etc/php.ini                   /usr/local/etc/php/php.ini

EXPOSE 80

CMD ["sh", "-c", "/usr/local/bin/entrypoint.sh; apache2-foreground"]
