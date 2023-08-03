<?php

use app\app\App;
use app\controllers\SiteController;
use app\controllers\MailController;

$router = App::$app->router;

/*---------------------------------------------------------------*/
/*---------------------------- WEB ------------------------------*/
/*---------------------------------------------------------------*/

$router->get('/', [SiteController::class, 'index']);
$router->get('/chi-sono', [SiteController::class, 'index']);
$router->get('/cosa-aspettarsi', [SiteController::class, 'index']);
$router->get('/di-cosa-mi-occupo', [SiteController::class, 'index']);
$router->get('/contatti', [SiteController::class, 'index']);

$router->post('/', [MailController::class, 'sendFromForm']);
$router->post('/chi-sono', [MailController::class, 'sendFromForm']);
$router->post('/cosa-aspettarsi', [MailController::class, 'sendFromForm']);
$router->post('/di-cosa-mi-occupo', [MailController::class, 'sendFromForm']);
$router->post('/contatti', [MailController::class, 'sendFromForm']);
