<?php

/*
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
*/

const THROTTLE_COUNT = 20;
const THROTTLE_TIME_SPAN = 360;
const SITES_HOME = '/home';
const SITES_USER = 'www';
const SITES_PUBLIC = 'public';
const SITES_INSTALL = 'deploy:install';
const PHP_AGENT_VERSION = '8.0';
const ENGINE_VERSION = '8.0';
const ENGINE_PATH = '/usr/bin/php';
const SCHEMA_VERSION = 1;
const DB_SERVER_PASSWORD = '/home/sculptor/.db_password';
// const UPDATES_URL = 'https://repo.packagist.org/p/sculptor-devops/sculptor-cli.json';

const UPDATES_URL = 'https://repo.packagist.org/p/sculptor-devops/installer.json';
const UPDATES_DOWNLOAD_URL = 'https://github.com/sculptor-devops/installer/releases/latest/download/installer';
const UPDATES_PACKAGE = 'installer';
