# PetitChef - Guides de déploiement

## 🌐 Déploiement sur serveur (Production)

### Option 1: DigitalOcean App Platform (Recommandé pour MVP)

#### Prérequis
- Compte DigitalOcean
- Code sur GitHub
- Domaine (optionnel)

#### Étapes

1. **Créer app**
   - Connect GitHub repository
   - Select branch: `main`
   - Choose DigitalOcean database: PostgreSQL 14

2. **Configuration environnement**
   ```
   APP_ENV=production
   APP_DEBUG=false
   DB_CONNECTION=pgsql
   DB_HOST=db-postgresql-xxx
   DB_DATABASE=petitchef
   DB_USERNAME=petitchef
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.sendgrid.net
   MAIL_USERNAME=apikey
   MAIL_PASSWORD=[SendGrid API key]
   ```

3. **Build & Deploy**
   ```yaml
   # app.yaml
   databases:
   - engine: PG
     name: petitchef-db
     version: "14"

   services:
   - name: petitchef
     github:
       branch: main
       repo: username/petitchef
     build_command: composer install && npm run build
     run_command: php -S 0.0.0.0:8080 -t public
     envs:
     - key: APP_ENV
       value: production
     - key: APP_DEBUG
       value: "false"
   ```

4. **Post-deploy**
   ```bash
   # SSH into app
   php artisan migrate --force
   php artisan db:seed --class=AdminSeeder
   ```

---

### Option 2: Heroku (Gratuit pour MVP)

#### Prérequis
```bash
# Installer Heroku CLI
brew install heroku
heroku login
```

#### Déploiement
```bash
# Créer app
heroku create petitchef

# Configurer base de données
heroku addons:create heroku-postgresql:hobby-dev

# Push code
git push heroku main

# Migrations
heroku run "php artisan migrate --force"

# Seeders
heroku run "php artisan db:seed"

# Voir les logs
heroku logs --tail
```

**Fichiers requis:**
```
# Procfile
web: vendor/bin/heroku-php-apache2 public/

# .env.example
# Ajouter: FORCE_HTTPS=true
```

---

### Option 3: AWS (Scalable)

#### Avec AWS App Runner

```bash
# 1. Créer ECR registry
aws ecr create-repository --repository-name petitchef

# 2. Build image
docker build -t petitchef:latest .

# 3. Tag & push
docker tag petitchef:latest [ACCOUNT_ID].dkr.ecr.[REGION].amazonaws.com/petitchef:latest
docker push [ACCOUNT_ID].dkr.ecr.[REGION].amazonaws.com/petitchef:latest

# 4. Créer App Runner service
aws apprunner create-service \
  --service-name petitchef \
  --source-configuration ImageRepository='...elasticcontainerregistry' \
  --instance-role-arn arn:aws:iam::[ACCOUNT_ID]:role/AppRunnerECRAccessRole
```

**Dockerfile:**
```dockerfile
FROM php:8.2-apache

RUN docker-php-ext-install pdo_pgsql

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev
RUN npm install && npm run build

RUN a2enmod rewrite
RUN chown -R www-data:www-data storage

EXPOSE 8080
```

---

## 🐳 Docker Compose (Local + Production)

```yaml
# docker-compose.yml
version: '3.8'

services:
  app:
    build: .
    ports:
      - "80:8000"
    environment:
      - DB_HOST=db
      - DB_USERNAME=petitchef
      - DB_PASSWORD=secret
      - DB_DATABASE=petitchef
    depends_on:
      - db

  db:
    image: postgres:14
    environment:
      - POSTGRES_USER=petitchef
      - POSTGRES_PASSWORD=secret
      - POSTGRES_DB=petitchef
    volumes:
      - postgres_data:/var/lib/postgresql/data

  redis:
    image: redis:7

volumes:
  postgres_data:
```

**Lancer:**
```bash
docker-compose up -d
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
```

---

## 📋 Checklist pré-déploiement

### Sécurité
- [ ] APP_DEBUG=false
- [ ] APP_ENV=production
- [ ] Toutes les .env variables configurées
- [ ] HTTPS activé
- [ ] Sessions: Redis ou DB (pas file)
- [ ] CORS configuré correctement

### Database
- [ ] Backup avant migration
- [ ] Migrations testées localement
- [ ] Seeders pour données d'init
- [ ] Indexes en place
- [ ] Foreign keys en place

### Performance
- [ ] Config cache: `php artisan config:cache`
- [ ] Route cache: `php artisan route:cache`
- [ ] Assets compilés: `npm run build`
- [ ] CDN configuré pour images
- [ ] Compression gzip activée

### Monitoring
- [ ] Logs centralisés (Sentry/Rollbar)
- [ ] Uptime monitoring (StatusPage)
- [ ] Performance monitoring (DataDog)
- [ ] Alertes email configurées

### Maintenance
- [ ] Backup quotidien de DB
- [ ] Logs rotationnés
- [ ] SSL cert auto-renew
- [ ] Server updates planifiés

---

## 🚨 En cas de problème

### Application ne démarre pas
```bash
# Vérifier les logs
tail -f storage/logs/laravel.log

# Vérifier les permissions
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# ClearCache
php artisan cache:clear
php artisan config:clear
```

### Database connection error
```bash
# Vérifier les credentials
# Vérifier que le host est accessible
telnet db-host 5432

# Vérifier migrations
php artisan migrate:status

# Refaire migrations
php artisan migrate:reset
php artisan migrate
```

### Erreur "CSRF token mismatch"
```bash
# Vérifier SESSION_DRIVER
# Si file: vérifier permissions storage/
# Si autre: vérifier connexion Redis/DB
```

### Haute utilisation CPU/Mémoire
```bash
# Vérifier les queries lentes
# Vérifier les jobs en queue
# Augmenter resources (scaling horizontal)
```

---

## 📊 Monitoring en production

### Logs
```bash
# Tail logs in real-time
tail -f storage/logs/laravel.log | grep -i error

# Compter erreurs
tail -f storage/logs/laravel.log | grep -c ERROR
```

### Database
```bash
# Slow queries
SET log_min_duration_statement = 5000; -- PostgreSQL

# Tableau de bord admin
# Voir orders/jour, revenue, etc.
```

### Performance
```bash
# Response time
curl -w "@curl-format.txt" -o /dev/null -s http://petitchef.local

# PageSpeed
# https://pagespeed.web.dev/
```

---

## 🔄 CI/CD Pipeline (GitHub Actions)

```yaml
# .github/workflows/deploy.yml
name: Deploy to Production

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Deploy to server
        run: |
          ssh -i ${{ secrets.SSH_KEY }} user@server.com << 'EOF'
          cd /var/www/petitchef
          git pull origin main
          composer install --no-dev
          npm install && npm run build
          php artisan migrate --force
          php artisan config:cache
          php artisan route:cache
          EOF
      
      - name: Run tests
        run: |
          composer install
          php artisan test
      
      - name: Alert Slack
        if: always()
        uses: slackapi/slack-github-action@v1
        with:
          payload: |
            {"text": "Deployment ${{ job.status }}"}
```

---

## 🎯 Scaling pour croissance

### Étape 1: Croissance faible (< 100 users)
- ✅ Single server
- ✅ SQLite → PostgreSQL
- ✅ Session driver: File/DB

### Étape 2: Croissance moyenne (100-1000 users)
- Load balancer
- Multiple app servers
- Session driver: Redis
- Queue driver: Redis
- Cache driver: Redis
- Separate DB server

### Étape 3: Croissance forte (> 1000 users)
- Auto-scaling groups
- Multi-region deployment
- DB read replicas
- CDN global
- Message queues (RabbitMQ)
- Microservices

---

## 💰 Estimations de coûts

### Option 1: DigitalOcean App Platform
```
App server:    $5-12/month
Database:      $10-25/month
Bandwidth:     $0.10/GB
Total (start): ~$15-40/month
```

### Option 2: Heroku
```
Hobby dyno:    Free (ou $5/month)
Database:      $9+/month
Total (start): ~$9+/month (ou free!)
```

### Option 3: AWS
```
App Runner:    $0.005/vCPU-hour
Database:      $0.017/hour
CDN:           $0.085/GB
Total (start): ~$50+/month
```

**Recommandation:** Commencer avec Heroku (gratuit), migrer à DigitalOcean si croissance.

---

## ✅ Validation avant production

```bash
# 1. Tests
php artisan test

# 2. Static analysis
composer require --dev phpstan/phpstan
./vendor/bin/phpstan analyse app/

# 3. Code style
composer require --dev laravel/pint
./vendor/bin/pint

# 4. Security check
composer audit

# 5. Performance test
npm run build && du -sh public/build/
```

---

## 📞 Support déploiement

### Resources
- [Laravel Deployment](https://laravel.com/docs/deployment)
- [DigitalOcean Docs](https://docs.digitalocean.com)
- [Heroku Docs](https://devcenter.heroku.com)

### Pour aide
1. Consulter logs: `storage/logs/laravel.log`
2. Vérifier `.env` sur serveur
3. SSH et lancer commandes manuellement
4. Créer issue GitHub avec logs

