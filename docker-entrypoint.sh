#!/bin/bash
set -e

/docker-entrypoint.sh "$@" &
WP_PID=$!

sleep 10

if [ -f /var/www/html/wp-config.php ]; then
    if ! wp plugin is-active woocommerce --allow-root 2>/dev/null; then
        echo "Activating WooCommerce..."
        wp plugin activate woocommerce --allow-root || true
    fi
fi

wait $WP_PID
