FROM php:latest

ENV INPUT_WAKATIME_USER_ID \
    INPUT_WAKATIME_API_KEY \
    INPUT_GH_TOKEN

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libxml2-dev \
    zip \
    unzip

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer and set up app
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN mkdir /app
COPY . /app/
RUN cd /app && composer install


ENTRYPOINT [ "php", "/app/src/stats.php" ]