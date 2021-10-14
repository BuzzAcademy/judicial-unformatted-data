#!/bin/bash
cd /data/app/judicial-unformatted-data;
git pull;
/usr/bin/php bin/crawler.php $1 $2;
git add .
git commit -am "Auto commit @"`date +%Y%m%d`
git push origin --force --all;
