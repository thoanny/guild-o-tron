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

`rm -rf .git assets bin node_modules .htaccess .gitignore composer.json composer.lock package.json symfony.lock webpack.config.js yarn.lock var .env README.md translation-update.sh`

Step 9

`mv htaccess .htaccess`

Step 10

Transfer files with FTP

Step 11

Update database, at your own risk !

Step 12

Delete `var/cache/prod` folder

## Translation

Launch `translation-update.sh` to generate **new version** of translation.

Send `translations/messages.terms.json` to PoEditor. "Import terms" and check "Import terms only".

Export each lang to JSON (key: value), with filename `messages.{lang}.json` (ie: messages.fr.json), overwrite files, commit and push.
