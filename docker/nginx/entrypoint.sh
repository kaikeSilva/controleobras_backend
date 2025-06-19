#!/bin/sh
set -e

echo "Aguardando PHP-FPM estar disponível..."
while ! nc -z php 9000; do
    echo "PHP-FPM ainda não está pronto, aguardando..."
    sleep 2
done
echo "PHP-FPM está disponível!"

# Executar comando original do nginx
exec "$@"