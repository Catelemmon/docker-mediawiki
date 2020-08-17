docker run --name=mediawiki_mariadb \
  -e MYSQL_DATABASE="wikidb" \
  -e MYSQL_USER=wikiuser \
  -e MYSQL_PASSWORD=wikipassword \
  -e MYSQL_RANDOM_ROOT_PASSWORD='yes' \
  -v $PWD/mediawiki_mysql:/var/lib/mysql \
  -d mariadb:latest