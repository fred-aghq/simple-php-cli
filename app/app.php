<?php

use App\Command\Hello;
use App\DynamoDB\Command\BulkModifyElasticsearchUrl;
use App\DynamoDB\Command\ImportDataFromFile;
use App\DynamoDB\Command\ImportLocalTableSchema;
use App\DynamoDB\Command\SeedTable;
use App\DynamoDB\Command\SeedTenantTables;
use Symfony\Component\Console\Application;
use App\Command\CurrentEnv;

const APP_PATH = __DIR__;
require __DIR__ . '/vendor/autoload.php';

$app = new Application();

$app->add(new Hello());
$app->add(new ImportLocalTableSchema());
$app->add(new ImportDataFromFile());
$app->add(new SeedTable());
$app->add(new CurrentEnv());

$app->run();