# README — Déploiement / Production (simple)

## Objectif
Fournir les commandes et bonnes pratiques minimales pour déployer l'application localement en production (serveur privé).

## Prérequis
- Docker & Docker Compose installés sur le serveur
- Ports 80/443 disponibles (ou proxy inverse devant les conteneurs)
- Certificats SSL si exposé publiquement

## Fichiers importants
- [docos/Dockerfile](docos/Dockerfile) : image multi-stage (PHP-FPM + nginx)
- [docos/docker-compose.prod.yml](docos/docker-compose.prod.yml) : composition pour prod (web + db), secrets chargés via `env_file: .env.prod`
- [docos/docker-entrypoint.sh](docos/docker-entrypoint.sh) : prépare l'environnement, démarre PHP-FPM, lance les migrations si demandé
- [docos/deploy.sh](docos/deploy.sh) : script de déploiement (build + validation des secrets + démarrage)

## Variables d'environnement (essentielles)

Toutes ces variables vivent dans **`.env.prod`**, à créer manuellement à la racine du projet — **jamais committé dans git** (ajouté à `.gitignore` et `.dockerignore`).

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_KEY` : clé d'application — **différente de celle utilisée en dev**, générée avant le premier démarrage
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `DB_ROOT_PASSWORD`
- `MAIL_*` : identifiants SMTP si l'envoi d'email est utilisé
- `RUN_MIGRATIONS=true` (optionnel — à activer seulement pour le premier déploiement, puis repasser en commentaire)

`docker-compose.prod.yml` ne contient plus aucun secret en clair — uniquement `APP_ENV`, `APP_DEBUG`, `APP_URL`, qui ne sont pas sensibles.

## Déploiement (méthode recommandée : deploy.sh)

```bash
cd docos
chmod +x deploy.sh
./deploy.sh
```

Le script :
1. Vérifie que `.env.prod` existe et que `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `DB_ROOT_PASSWORD` sont bien renseignés (arrête le déploiement sinon, avec un message clair).
2. Génère `APP_KEY` automatiquement si absente ou vide dans `.env.prod`.
3. Build l'image (`--no-cache`) et démarre les services.
4. Affiche l'état des conteneurs (`docker compose ps`).
5. Vérifie l'alias `ubuntusrv.local` dans `/etc/hosts` — l'ajoute si le script tourne en root, sinon affiche la commande `sudo` à lancer toi-même.

Le site est alors accessible via :
```text
http://ubuntusrv.local
```

## Déploiement manuel (sans deploy.sh)

```bash
# depuis le dossier docos/, avec .env.prod déjà créé et complet
docker compose -f docker-compose.prod.yml build --no-cache
docker compose -f docker-compose.prod.yml up -d
```

Pour générer `APP_KEY` manuellement si besoin :
```bash
docker run --rm php:8.2-cli php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"
```
Copiez la valeur dans `.env.prod` → `APP_KEY=`.

Pour l'alias local :
```bash
# Linux / macOS
printf '\n127.0.0.1 ubuntusrv.local\n' | sudo tee -a /etc/hosts
```

## Cache & optimisation (après démarrage)

```bash
docker compose -f docker-compose.prod.yml exec web php artisan config:cache
docker compose -f docker-compose.prod.yml exec web php artisan route:cache
docker compose -f docker-compose.prod.yml exec web php artisan view:cache
```

⚠️ Une fois `config:cache` exécuté, toute modification de `.env.prod` nécessite de relancer `config:cache` (ou `config:clear`) pour être prise en compte — contrairement au dev où l'environnement est relu à chaque requête.

## Sauvegarde & restauration de la base

Le service `db` monte `db_data` comme volume Docker persistant. Sauvegarde via `mysqldump` :

```bash
docker compose -f docker-compose.prod.yml exec db sh -c 'exec mysqldump --databases docosdb -u root -p"$MYSQL_ROOT_PASSWORD"' > backup.sql
```

Restauration :
```bash
cat backup.sql | docker compose -f docker-compose.prod.yml exec -T db sh -c 'mysql -u root -p"$MYSQL_ROOT_PASSWORD"'
```

Mets en place une sauvegarde régulière (cron) avant de considérer ce déploiement comme fiable.

## Logs & supervision

- `docker compose -f docker-compose.prod.yml logs -f web`
- `docker compose -f docker-compose.prod.yml logs -f db`
- Les logs applicatifs Laravel sont dans `storage/logs/laravel.log` (monté depuis l'hôte via le volume `./storage`).
- Prévoir une rotation de logs si le volume grossit avec le temps.

## Sécurité & recommandations

- Ne jamais exposer MySQL (port 3306) publiquement — pas de mapping de port sur `db` en prod, réseau Docker interne uniquement.
- Garder `APP_DEBUG=false` en permanence en prod.
- `.env.prod` doit avoir des secrets **différents** de ceux du dev (`APP_KEY`, `DB_PASSWORD`, `DB_ROOT_PASSWORD`).
- Limiter l'accès réseau au port 80 au réseau interne / VPN plutôt que de l'exposer publiquement sans TLS.
- Si accès externe nécessaire : reverse-proxy avec certificat TLS (Let's Encrypt) devant le conteneur, jamais de HTTP brut exposé directement.

## Rollback

- Toujours faire une sauvegarde DB avant une migration majeure.
- Taguer les images de build (ex. `nextmux_app:2026-08-14`) pour faciliter un retour en arrière rapide.

## Dépannage rapide

- **`vendor` manquant** : reconstruire l'image (`docker compose -f docker-compose.prod.yml build --no-cache`).
- **`MissingAppKeyException`** : vérifier que `APP_KEY` est bien définie dans `.env.prod`, puis `php artisan config:clear` dans le conteneur si un cache de config périmé persiste.
- **Erreur DB** : vérifier `DB_*` dans `.env.prod` et l'état du healthcheck (`docker compose -f docker-compose.prod.yml ps`).
- **500 malgré des logs Docker propres** : consulter `storage/logs/laravel.log` directement — les erreurs applicatives PHP n'apparaissent pas dans `docker compose logs` (canal `LOG_CHANNEL=stack` → fichier, pas stdout).

## Nom local (ubuntusrv.local)

- `docker-compose.prod.yml` utilise `APP_URL=http://ubuntusrv.local`.
- `docos/nginx/default.conf` répond à `ubuntusrv.local`.
- `deploy.sh` vérifie/propose l'ajout de l'alias dans `/etc/hosts`.
- Pour un accès depuis d'autres machines du réseau local, ajouter le même alias dans leur `/etc/hosts` respectif, pointant vers l'IP réelle du serveur (pas `127.0.0.1`).

## Reverse-proxy nginx (optionnel)

Si le projet est placé derrière un reverse-proxy sur un serveur dédié (plusieurs services sur la même machine) : ajouter une vhost avec `server_name ubuntusrv.local;` et `proxy_pass http://127.0.0.1;`.