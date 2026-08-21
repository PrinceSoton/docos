#!/bin/bash
set -euo pipefail

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_HOST="ubuntusrv.local"
COMPOSE_FILE="$PROJECT_DIR/docker-compose.prod.yml"
ENV_FILE="$PROJECT_DIR/.env.prod"
HOSTS_FILE="/etc/hosts"

echo "[deploy] Démarrage du déploiement pour $APP_HOST"

if [ ! -f "$COMPOSE_FILE" ]; then
  echo "[deploy] Fichier docker-compose.prod.yml introuvable : $COMPOSE_FILE" >&2
  exit 1
fi

cd "$PROJECT_DIR"

if [ ! -f "$ENV_FILE" ]; then
  echo "[deploy] Aucun .env.prod détecté. Ce fichier doit être créé manuellement avec des secrets propres à la production (voir README)." >&2
  exit 1
fi

# Vérifie que les variables critiques sont présentes et non vides
REQUIRED_VARS="DB_DATABASE DB_USERNAME DB_PASSWORD DB_ROOT_PASSWORD"
MISSING=0
for VAR in $REQUIRED_VARS; do
  VALUE="$(grep "^${VAR}=" "$ENV_FILE" 2>/dev/null | cut -d= -f2- || true)"
  if [ -z "$VALUE" ]; then
    echo "[deploy] ERREUR : $VAR est absent ou vide dans .env.prod" >&2
    MISSING=1
  fi
done
if [ "$MISSING" -eq 1 ]; then
  echo "[deploy] Complétez .env.prod avant de relancer le déploiement." >&2
  exit 1
fi

if ! grep -q '^APP_KEY=' "$ENV_FILE" 2>/dev/null || [ -z "$(grep '^APP_KEY=' "$ENV_FILE" | cut -d= -f2-)" ]; then
  echo "[deploy] Génération de la clé d'application de production..."
  KEY="base64:$(docker run --rm php:8.2-cli php -r 'echo base64_encode(random_bytes(32));')"
  if grep -q '^APP_KEY=' "$ENV_FILE"; then
    sed -i "s#^APP_KEY=.*#APP_KEY=${KEY}#" "$ENV_FILE"
  else
    printf "\nAPP_KEY=%s\n" "$KEY" >> "$ENV_FILE"
  fi
  echo "[deploy] Nouvelle APP_KEY générée et écrite dans .env.prod."
fi

# --env-file est indispensable ici : il permet à Compose de substituer les
# ${DB_ROOT_PASSWORD} etc. DANS le YAML lui-même. env_file: dans le service
# n'injecte les variables QUE dans le conteneur, pas dans l'interprétation du YAML.
COMPOSE="docker compose -f $COMPOSE_FILE --env-file $ENV_FILE"

echo "[deploy] Build de l'image de production..."
docker build --no-cache --network=host -t nextmux_app:prod -f "$PROJECT_DIR/Dockerfile" "$PROJECT_DIR"

echo "[deploy] Démarrage des services..."
$COMPOSE up -d

echo "[deploy] Attente que les services soient prêts..."
sleep 5
$COMPOSE ps

if ! grep -q "${APP_HOST}" "$HOSTS_FILE" 2>/dev/null; then
  if [ "$(id -u)" -eq 0 ]; then
    echo "[deploy] Ajout de l'alias local ${APP_HOST} dans /etc/hosts"
    printf '\n127.0.0.1 %s\n' "$APP_HOST" >> "$HOSTS_FILE"
  else
    echo "[deploy] Alias ${APP_HOST} absent de /etc/hosts."
    echo "[deploy] Ajoutez-le manuellement (root requis) :"
    echo "         echo '127.0.0.1 ${APP_HOST}' | sudo tee -a $HOSTS_FILE"
  fi
fi

echo "[deploy] Déploiement terminé."
echo "[deploy] Accès local : http://${APP_HOST}"
echo "[deploy] Logs        : docker compose -f $COMPOSE_FILE --env-file $ENV_FILE logs --tail=50 web"