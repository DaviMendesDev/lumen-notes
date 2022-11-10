FROM nginx

RUN export DEBIAN_FRONTEND=noninteractive

RUN yes | apt-get upgrade && \
    yes | apt-get update && \
    apt-get install -y \
    lsb-release \
    ca-certificates \
    apt-transport-https \
    software-properties-common \
    wget \
    gnupg2 \
    nano \
    vim \
    kakoune \
    neovim \
    net-tools \
    less

RUN echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/sury-php.list
RUN wget -qO - https://packages.sury.org/php/apt.gpg | apt-key add -

RUN yes | apt-get update && \
    apt-get install -y \
    php8.1 \
    php8.1-fpm \
    php8.1-curl \
    php8.1-zip \
    php8.1-xdebug \
    php8.1-dom \
    php8.1-xml \
    php8.1-pdo \
    php8.1-pgsql \
    php8.1-common \
    php8.1-gd \
    php8.1-redis \
    php8.1-mbstring \
    php8.1-sqlite

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN mkdir -p /run/php/
RUN touch /run/php/php8.1-fpm.sock
RUN chmod 755 /run/php/php8.1-fpm.sock
RUN chown nginx:nginx /run/php/php8.1-fpm.sock
