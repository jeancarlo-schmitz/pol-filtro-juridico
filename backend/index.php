<?php

date_default_timezone_set('America/Bahia');

//se você está debugando o projeto, lembrese de colocar no arquivo de constantes IS_DEV = true;
require_once 'constants.php';
require_once 'src/Infrastructure/Utils/debugger.php';
require_once 'autoload.php';

use Infrastructure\Utils\CorsHandler;
use Presentation\App;

$corsHandler = new CorsHandler();
$corsHandler->handleCors();

$app = new App();
$app->handleRequest();

