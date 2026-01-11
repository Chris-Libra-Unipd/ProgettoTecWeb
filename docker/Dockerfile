FROM php:8.2-apache

# Abilita i moduli per la connessione al database (mysqli e pdo)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Abilita il modulo rewrite di Apache (utile per URL amichevoli/routing)
RUN a2enmod rewrite