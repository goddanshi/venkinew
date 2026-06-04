FROM php:8.3-cli

WORKDIR /var/www/html

# Нужен json для работы с data/*.json
RUN docker-php-ext-install json 2>/dev/null || true

EXPOSE 80

# Встроенный PHP-сервер — router.php уже настроен для него
CMD ["php", "-S", "0.0.0.0:80", "router.php"]
