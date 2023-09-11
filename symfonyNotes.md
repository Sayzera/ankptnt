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
git pull --no-rebase

** terminal **
mv ankptnt/\* . klasorun içindekileri bulunduğu dizine taşır
rm -r ankptnt

Yarım kalmış boyutu uzun bir dosyayı iptal etmek için
git filter-branch -f --tree-filter 'rm -f Arşiv.zip' HEAD --all

Yeni bir branch açmak
git branch eski-kodlar # Yeni bir dal oluşturun
git checkout eski-kodlar # Yeni dalı seçin
git add . # Tüm değişiklikleri sahneye ekleyin
git commit -m "Eski kodları sakla" # Değişiklikleri kaydedin
git push origin eski-kodlar # Uzak depoya yeni dalı gönderin

// açılan brancı uzak repo ile birleştirme
git fetch origin eski-kodlar # uzak repoyu bağla
git checkout -b eski-kodlar origin/eski-kodlar // uzak repoda yerel olarak geçiş yap
git merge eski-kodlar // kodları birleştir
git push origin eski-kodlar

php bin/console doctrine:cache:clear-metadata
php bin/console doctrine:cache:clear-query
php bin/console doctrine:cache:clear-result

# fixtures

php bin/console doctrine:fixtures:load --purge-exclusions=Lang --purge-exclusions=LangMessages

- sadece belirtilen tabloları temizle

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE Lang;
SET FOREIGN_KEY_CHECKS = 1;
