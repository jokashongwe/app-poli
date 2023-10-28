# app-poli
# Solution de gestio d'association
# Inscription des membres, Fédéralisation, Impression des cartes et suivis de cotisation
## Symfony 5

Mise en place après avoir cloné le repos

cp .env.example .env
composer install
yarn install
yarn encore dev

doctrine:database:create  : To create the Database
doctrine:migrations:migrate
doctrine:fixtures:load

pip install -r scripts/requirements.txt