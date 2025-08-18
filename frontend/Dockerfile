FROM php:8.2-apache

# Instala extensões PHP necessárias
RUN docker-php-ext-install pdo pdo_mysql
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    && docker-php-ext-install curl \
    && rm -rf /var/lib/apt/lists/*

# Habilita mod_rewrite do Apache
RUN a2enmod rewrite

# Copia configuração do Apache
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia os arquivos PHP
COPY . /var/www/html/

# Define permissões
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

EXPOSE 80