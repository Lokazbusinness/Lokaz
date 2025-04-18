#!/bin/bash

# Remplace le port par celui donné par Render (stocké dans $PORT)
if [ -n "$PORT" ]; then
  sed -i "s/80/${PORT}/g" /etc/apache2/ports.conf
  sed -i "s/80/${PORT}/g" /etc/apache2/sites-enabled/000-default.conf
fi

# Démarre Apache
apache2-foreground
