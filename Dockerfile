FROM php:8.2-apache

# Cài đặt thư viện cURL cho PHP
RUN apt-get update && apt-get install -y libcurl4-openssl-dev \
    && docker-php-ext-install curl

# Copy toàn bộ mã nguồn vào thư mục web của Apache
COPY . /var/www/html/

# Mở cổng 80 mặc định của Apache
EXPOSE 80
