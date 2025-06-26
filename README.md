


## Instalação


```sh
sudo apt update && sudo apt upgrade -y
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
```


```sh
sudo apt update && sudo apt upgrade -y
sudo apt install -y git unzip curl php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-bcmath php8.2-zip php8.2-readline php8.2-gd php8.2-intl
```

```sh
 sudo apt install -y  nginx mysql-server composer
```

```sh

cd /var/www
sudo git clone https://github.com/danielcarlosmacao/Meu-Gestor.git gestor
cd gestor
```


```sh
sudo chown -R www-data:www-data .
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache
```

```sh
sudo mysql_secure_installation
```


Validar senha com plugin? → escolha n (se quiser definir manualmente)

Nova senha para root → digite sua senha segura

Remover usuários anônimos? → y

Desabilitar login remoto do root? → y

Remover banco de testes? → y

Recarregar privilégios? → y



```sh
sudo mysql
```

```sh
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'SUA_SENHA_AQUI';
FLUSH PRIVILEGES;
```




```sh
CREATE DATABASE Gestor DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```




```sh
cp .env.example .env

nano .env
```
```sh
APP_NAME="Meu Sistema"
APP_ENV=production
APP_KEY=base64:ChaveGeradaAqui==
APP_DEBUG=false
APP_URL=https://meusite.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=usuario
DB_PASSWORD=senha

# CACHE e QUEUE (recomendado)
CACHE_DRIVER=file
QUEUE_CONNECTION=database

# SESSÕES
SESSION_DRIVER=file
SESSION_LIFETIME=120

# LOCALIZAÇÃO
APP_LOCALE=pt_BR
```


```sh
composer install --no-dev --optimize-autoloader
```


```sh
php artisan key:generate
```




```sh
php artisan migrate --force
php artisan db:seed
```

```sh
sudo nano /etc/nginx/sites-available/gestor
```


```sh
server {
    listen 80;
    server_name dominio.com; # ou IP do servidor

    root /var/www/gestor/public;

    index index.php index.html index.htm;

    location ~ /.well-known/acme-challenge/ {
        allow all;
        root /var/www/gestor/public;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock; # ou 8.1/8.3 dependendo da versão instalada
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }

    access_log /var/log/nginx/laravel_access.log;
    error_log /var/log/nginx/laravel_error.log;
}
```

```sh

sudo ln -s /etc/nginx/sites-available/gestor /etc/nginx/sites-enabled/
sudo nginx -t
```

```sh
sudo systemctl reload nginx
```

```sh
sudo apt install certbot python3-certbot-nginx
```

```sh
sudo certbot --nginx -d seu-dominio.com
```

```sh

sudo apt install supervisor
sudo nano /etc/supervisor/conf.d/gestor-worker.conf
```

```sh
[program:gestor-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/gestor/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/gestor/storage/logs/worker.log
```


```sh

sudo supervisorctl reread
sudo supervisorctl update
```

```sh
crontab -e
```

```sh
* * * * * cd /var/www/gestor && php artisan schedule:run >> /dev/null 2>&1
```

```sh
 git config --global --add safe.directory /var/www/gestor
git update-index --assume-unchanged .env
git update-index --assume-unchanged vendor/composer/autoload_real.php
git update-index --assume-unchanged vendor/autoload.php
```


```sh
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
sudo rm -rf /var/www/gestor/public/storage
php artisan storage:link

```
# aumentar memoria PHP

 ```sh
nano /etc/php/8.2/fpm/php.ini
```
procure memory_limit = 128M e mude para para memory_limit = 1024M

 ```sh
sudo service php8.2-fpm restart
sudo service nginx restart
```

# Update 

```sh
cd /var/www/gestor
git config --global --add safe.directory /var/www/gestor
git pull origin main
composer install --no-dev
php artisan migrate --force
php artisan db:seed
```

```sh
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```
----

```sh

composer dump-autoload
```