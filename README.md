# Guild-o-Tron

## Update Production

Step 1

`git clone git@bitbucket.org:anthony-d/guild-o-tron.git`

Step 2

`cd guild-o-tron`

Step 3

Edit `.env`, line 17 to `prod`

Step 4

`composer install --optimize-autoloader`

Step 5

`composer dump-autoload --optimize --classmap-authoritative`

Step 6

`yarn install`

Step 7

`yarn build`

Step 8

`rm -rf .git assets bin node_modules .htaccess .gitignore composer.json composer.lock package.json symfony.lock webpack.config.js yarn.lock var .env README.md`

Step 9

`mv htaccess .htaccess`

Step 10

Transfer files with FTP

Step 11

Update database, at your own risk !

Step 12

Delete `var/cache/prod` folder
