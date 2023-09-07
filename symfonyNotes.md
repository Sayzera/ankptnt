symfony console make:controller ConferenceController
composer dump-env prod
APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear

- Error Controller
  -packages/framework.yaml
  # error_controller: App\Controller\ErrorController::show

composer dump-env dev || env güncelledikten sonra yapılır

**Symfony**
php bin/console doctrine:database:create --connection=custom

- php bin/console doctrine:migrations:diff
- php bin/console doctrine:migrations:migrate
- php bin/console doctrine:migrations:diff --em=custom
- php bin/console doctrine:migrations:migrate --em=custom

**Docker Mysql**

- docker-compose exec php /bin/bash
- docker-compose exec database /bin/bash
- mysql -u root -p symfony_docker
- USE mydatabase;
- show tables;
- show columns from Lang
- php bin/console doctrine:schema:drop -n -q --force --full-database --em=custom
- php bin/console doctrine:schema:update --force --em=custom

**Docker**
sudo chown -R $(whoami) ~/.docker

** Symfony Global Varialbe **
Önce config/services.yaml içerisinde tanımlama yapılır.

** PHP storm **
open -a "PhpStorm"

** SSH oluşturma\***
ssh-keygen -t ed25519 -C "white.code.text@gmail.com"

**_ git _**
git rm --cached app
git ad..

** terminal **
mv ankptnt/\* . klasorun içindekileri bulunduğu dizine taşır
rm -r ankptnt

Yarım kalmış boyutu uzun bir dosyayı iptal etmek için
git filter-branch -f --tree-filter 'rm -f Arşiv.zip' HEAD --all
