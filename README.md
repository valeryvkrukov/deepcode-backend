Installation
============

- `# composer install`
- `# php bin/console doctrine:database:create`
- `# php bin/console doctrine:schema:update --force`
- `# php bin/console doctrine:fixtures:load`
- `# php bin/console assets:install --symlink`
- `# mkdir var/jwt`
- `# openssl genrsa -out var/jwt/private.pem -aes256 4096`
- `# openssl rsa -pubout -in var/jwt/private.pem -out var/jwt/public.pem`
