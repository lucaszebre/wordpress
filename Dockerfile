FROM wordpress:latest

RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp

RUN apt-get update && apt-get install -y --no-install-recommends unzip wget \
    && wget -q -O /tmp/woocommerce.zip https://downloads.wordpress.org/plugin/woocommerce.latest-stable.zip \
    && unzip -q /tmp/woocommerce.zip -d /usr/src/wordpress/wp-content/plugins/ \
    && rm /tmp/woocommerce.zip \
    && apt-get purge -y --auto-remove unzip wget \
    && rm -rf /var/lib/apt/lists/*


