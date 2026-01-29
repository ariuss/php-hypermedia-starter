# PHP Hypermedia Starter

A base starter for building hypermedia-driven applications in PHP. It relies on a simple, low-ops technology stack to keep the focus on HTML, HTTP, and server-side rendering rather than tooling complexity. The stack is composed of the following layers:

| Layer            | Responsibility                                          |
| ---------------- | ------------------------------------------------------- |
| **NGINX**        | Routing, static caching, compression                    |
| **PHP**          | Template rendering, partials for HTMX, business logic   |
| **SQLite**       | Simple, zero-ops database for app data                  |
| **Bootstrap**    | Customized with CSS variables, no build tool            |
| **HTMX**         | HTML-over-HTTP for dynamic UI, no JS framework          |
| **Docker**       | Dev environment isolation, easy deployment              |

## Setup

Create directories and log files:

```bash
mkdir -p certbot/{conf,www}
mkdir -p public/assets/{images,css,js}
mkdir -p storage/{database,logs,tmp}
touch storage/logs/{app,nginx-access,nginx-error,php-error}.log
chmod 666 storage/logs/*.log
```

Setup sqlite database:

```bash
touch storage/database/app.db
./migrations.sh up
```

Download Bootstrap and HTMX:
```bash
wget https://github.com/twbs/bootstrap/raw/refs/heads/main/dist/css/bootstrap.min.css -P public/assets/css/
wget https://github.com/twbs/bootstrap/raw/refs/heads/main/dist/js/bootstrap.bundle.min.js -P public/assets/js/
wget https://github.com/bigskysoftware/htmx/raw/refs/heads/master/dist/htmx.min.js -P public/assets/js/
```

## Development Server

### PHP CLI
Start the development server on http://localhost:

```bash
php -S localhost:80 -t public router.php
```

### Docker Compose

Generate a local SSL certificate with [`mkcert`](https://github.com/FiloSottile/mkcert) (one-time):

```bash
# Install the local CA
mkcert -install

# Generate SSL certificate and key for localhost
mkcert \
  -cert-file ./storage/cert.pem \
  -key-file ./storage/key.pem \
  "$(hostname).local" "*.localhost" localhost 127.0.0.1 ::1
```

Start the development server on https://localhost:

```bash
docker-compose up -d nginx
```

Stop the development server:

```bash
docker-compose down
```

## Production Server

Obtain the HTTPS certificate for your domain name with [Certbot](https://certbot.eff.org/) (one-time):

```bash
docker compose run --rm certbot certonly \
  --webroot \
  --webroot-path=/var/www/certbot \
  -d yourdomain.com -d www.yourdomain.com \
  --email admin@yourdomain.com \
  --agree-tos \
  --no-eff-email
```

> Make sure you replace `yourdomain.com` with your real domain name!

Update `nginx.conf`:

```bash
sed -i 's|/var/www/html/storage/cert.pem|/etc/letsencrypt/live/yourdomain.com/fullchain.pem|' ./nginx.conf
sed -i 's|/var/www/html/storage/key.pem|/etc/letsencrypt/live/yourdomain.com/privkey.pem|' ./nginx.conf
sed -i 's|server_name localhost;|server_name yourdomain.com www.yourdomain.com;|g' ./nginx.conf
```

Start the production server:

```bash
docker-compose up -d nginx
```

Stop the production server:

```bash
docker-compose down
```

## Database Backup & Restore

Back up the database:

```bash
sqlite3 storage/database/app.db ".backup 'storage/database/app_backup_$(date +%Y%m%d_%H%M%S).db'"
```

Restore the latest backup:

```bash
cd storage/database
rm -f app.db
latest_backup=$(ls -t app_backup_*.db 2>/dev/null | head -n 1)
[ -n "$latest_backup" ] && cp "$latest_backup" app.db
```