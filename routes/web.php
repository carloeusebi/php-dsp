<?php

use app\app\App;
use app\controllers\SiteController;
use app\controllers\MailController;

/**
 * @var App $app
 */

/*---------------------------------------------------------------*/
/*---------------------------- WEB ------------------------------*/
/*---------------------------------------------------------------*/

$app->router->get('/', [SiteController::class, 'index']);
$app->router->get('/chi-sono', [SiteController::class, 'index']);
$app->router->get('/cosa-aspettarsi', [SiteController::class, 'index']);
$app->router->get('/di-cosa-mi-occupo', [SiteController::class, 'index']);
$app->router->get('/contatti', [SiteController::class, 'index']);

$app->router->post('/', [MailController::class, 'sendFromForm']);
$app->router->post('/chi-sono', [MailController::class, 'sendFromForm']);
$app->router->post('/cosa-aspettarsi', [MailController::class, 'sendFromForm']);
$app->router->post('/di-cosa-mi-occupo', [MailController::class, 'sendFromForm']);
$app->router->post('/contatti', [MailController::class, 'sendFromForm']);
