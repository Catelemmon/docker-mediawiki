FROM php:7.3-fpm

# Change UID and GID of www-data user to match host privileges
RUN usermod -u 999 www-data && \
    groupmod -g 999 www-data

# Utilities
RUN set -eux; \
    \
    apt-get update; \ 
    apt-get install -y --no-install-recommends \
    git curl librsvg2-bin imagemagick python3 apt-transport-https ca-certificates \
    libicu-dev g++; \
    rm -r /var/lib/apt/lists/*

# Install the PHP extentions we need
RUN set -eux; \
    \
    savedAptMark="$(apt-mark showmanual)"; \
    \
    apt-get update; \
    apt-get install -y --no-install-recommends libicu-dev; \
    docker-php-ext-install -j $(nproc) intl mbstring mysqli opcache; \
    pecl install APCu-5.1.18; \
    docker-php-ext-enable apcu; \
    \
    apt-mark auto '.*' > /dev/null; \
    apt-mark manual $savedAptMark; \
    ldd "$(php -r 'echo ini_get("extension_dir");')"/*.so \
        | awk '/=>/ { print $3 }' \
        | sort -u \
        | xargs -r dpkg-query -S \
        | cut -d: -f1 \
        | sort -u \
        | xargs -rt apt-mark manual; \
    \
    apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false; \
    rm -rf /var/lib/apt/lists/*

# set recommended PHP.ini settings
# see https://secure.php.net/manual/en/opcache.installation.php
RUN { \
		echo 'opcache.memory_consumption=128'; \
		echo 'opcache.interned_strings_buffer=8'; \
		echo 'opcache.max_accelerated_files=4000'; \
		echo 'opcache.revalidate_freq=60'; \
		echo 'opcache.fast_shutdown=1'; \
	} > /usr/local/etc/php/conf.d/opcache-recommended.ini

# MySQL PHP extension
RUN docker-php-ext-install mysqli

# Imagick with PHP extension
#RUN apt-get update && apt-get install -y imagemagick libmagickwand-6.q16-dev --no-install-recommends && \
#    ln -s /usr/lib/x86_64-linux-gnu/ImageMagick-6.8.9/bin-Q16/MagickWand-config /usr/bin/ && \
#    pecl install imagick-3.4.0RC6 && \
#    echo "extension=imagick.so" > /usr/local/etc/php/conf.d/ext-imagick.ini && \
#    rm -rf /var/lib/apt/lists/*

# Nginx
RUN apt-get update && \
    apt-get -y install nginx && \
    rm -r /var/lib/apt/lists/*
COPY config/nginx/* /etc/nginx/

# PHP-FPM
COPY config/php-fpm/php-fpm.conf /usr/local/etc/ 
COPY config/php-fpm/php.ini /usr/local/etc/php/
RUN mkdir -p /var/run/php7-fpm/ && \
    chown www-data:www-data /var/run/php7-fpm/

# Supervisor
RUN apt-get update && \
    apt-get install -y supervisor --no-install-recommends && \
    rm -r /var/lib/apt/lists/*
COPY config/supervisor/supervisord.conf /etc/supervisor/conf.d/
COPY config/supervisor/kill_supervisor.py /usr/bin/

# NodeJS
RUN curl -sL https://deb.nodesource.com/setup_lts.x | bash - && \
    apt-get install -y nodejs

# Parsoid
RUN useradd parsoid --no-create-home --home-dir /usr/lib/parsoid --shell /usr/sbin/nologin; \
    apt-get update && \
    apt-get -y install dirmngr --no-install-recommends; \
    apt-key advanced --keyserver keys.gnupg.net --recv-keys AF380A3036A03444 && \
    echo "deb https://releases.wikimedia.org/debian jessie-mediawiki main" | tee /etc/apt/sources.list.d/parsoid.list && \
    apt-get -y install apt-transport-https; \
    apt-get update && \
    apt-get -y install parsoid --no-install-recommends
COPY config/parsoid/config.yaml /usr/lib/parsoid/src/config.yaml
ENV NODE_PATH /usr/lib/parsoid/node_modules:/usr/lib/parsoid/src

# MediaWiki
ARG MEDIAWIKI_VERSION_MAJOR=1
ARG MEDIAWIKI_VERSION_MINOR=34
ARG MEDIAWIKI_VERSION_BUGFIX=2

RUN curl -s -o /tmp/keys.txt https://www.mediawiki.org/keys/keys.txt && \
    curl -s -o /tmp/mediawiki.tar.gz https://releases.wikimedia.org/mediawiki/$MEDIAWIKI_VERSION_MAJOR.$MEDIAWIKI_VERSION_MINOR/mediawiki-$MEDIAWIKI_VERSION_MAJOR.$MEDIAWIKI_VERSION_MINOR.$MEDIAWIKI_VERSION_BUGFIX.tar.gz && \
    curl -s -o /tmp/mediawiki.tar.gz.sig https://releases.wikimedia.org/mediawiki/$MEDIAWIKI_VERSION_MAJOR.$MEDIAWIKI_VERSION_MINOR/mediawiki-$MEDIAWIKI_VERSION_MAJOR.$MEDIAWIKI_VERSION_MINOR.$MEDIAWIKI_VERSION_BUGFIX.tar.gz.sig && \
    gpg --import /tmp/keys.txt && \
    gpg --list-keys --fingerprint --with-colons | sed -E -n -e 's/^fpr:::::::::([0-9A-F]+):$/\1:6:/p' | gpg --import-ownertrust && \
    gpg --verify /tmp/mediawiki.tar.gz.sig /tmp/mediawiki.tar.gz && \
    mkdir -p /var/www/mediawiki /data /images && \
    tar -xzf /tmp/mediawiki.tar.gz -C /tmp && \
    mv /tmp/mediawiki-$MEDIAWIKI_VERSION_MAJOR.$MEDIAWIKI_VERSION_MINOR.$MEDIAWIKI_VERSION_BUGFIX/* /var/www/mediawiki && \
    rm -rf /tmp/mediawiki.tar.gz /tmp/mediawiki-$MEDIAWIKI_VERSION_MAJOR.$MEDIAWIKI_VERSION_MINOR.$MEDIAWIKI_VERSION_BUGFIX/ /tmp/keys.txt && \
    rm -rf /var/www/mediawiki/images && \
    ln -s /images /var/www/mediawiki/images && \
    chown -R www-data:www-data /data /images /var/www/mediawiki/images
COPY config/mediawiki/* /var/www/mediawiki/

# VisualEditor extension
RUN curl -s -o /tmp/extension-visualeditor.tar.gz https://extdist.wmflabs.org/dist/extensions/VisualEditor-REL${MEDIAWIKI_VERSION_MAJOR}_${MEDIAWIKI_VERSION_MINOR}-`curl -s https://extdist.wmflabs.org/dist/extensions/ | grep -o -P "(?<=VisualEditor-REL${MEDIAWIKI_VERSION_MAJOR}_${MEDIAWIKI_VERSION_MINOR}-)[0-9a-z]{7}(?=.tar.gz)" | head -1`.tar.gz && \
    tar -xzf /tmp/extension-visualeditor.tar.gz -C /var/www/mediawiki/extensions && \
    rm /tmp/extension-visualeditor.tar.gz

# User merge and delete extension
RUN curl -s -o /tmp/extension-usermerge.tar.gz https://extdist.wmflabs.org/dist/extensions/UserMerge-REL${MEDIAWIKI_VERSION_MAJOR}_${MEDIAWIKI_VERSION_MINOR}-`curl -s https://extdist.wmflabs.org/dist/extensions/ | grep -o -P "(?<=UserMerge-REL${MEDIAWIKI_VERSION_MAJOR}_${MEDIAWIKI_VERSION_MINOR}-)[0-9a-z]{7}(?=.tar.gz)" | head -1`.tar.gz && \
    tar -xzf /tmp/extension-usermerge.tar.gz -C /var/www/mediawiki/extensions && \
    rm /tmp/extension-usermerge.tar.gz

# Set work dir
WORKDIR /var/www/mediawiki

# Copy docker entry point script
COPY docker-entrypoint.sh /docker-entrypoint.sh

# Copy install and update script
RUN mkdir /script
COPY script/* /script/

# General setup
VOLUME ["/var/cache/nginx", "/data", "/images"]
EXPOSE 8080
ENTRYPOINT ["/docker-entrypoint.sh"]
CMD []
