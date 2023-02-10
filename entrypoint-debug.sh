#!/bin/bash
export PHP_IDE_CONFIG="serverName=toolkit"
php -dxdebug.mode=debug -dxdebug.client_host=host.docker.internal -dxdebug.client_port=9003 -dxdebug.start_with_request=yes -f /app/app.php "$@"
