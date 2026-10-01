#!/bin/sh
set -e

# Espera a que la base de datos acepte conexiones y crea/actualiza las tablas.
echo "Esperando la base de datos..."
i=0
until php bin/migrate.php; do
    i=$((i + 1))
    if [ "$i" -ge 30 ]; then
        echo "La base de datos no respondió a tiempo." >&2
        exit 1
    fi
    sleep 2
done

# Crea el administrador inicial si se definieron las variables (se ignora si ya existe).
if [ -n "$ADMIN_EMAIL" ] && [ -n "$ADMIN_PASSWORD" ]; then
    php bin/create-user.php "$ADMIN_EMAIL" "$ADMIN_PASSWORD" || true
fi

exec "$@"
