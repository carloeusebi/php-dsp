<?php

use app\App;
use controllers\SiteController;

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

$app->router->post('/', [SiteController::class, 'post']);
$app->router->post('/chi-sono', [SiteController::class, 'post']);
$app->router->post('/cosa-aspettarsi', [SiteController::class, 'post']);
$app->router->post('/di-cosa-mi-occupo', [SiteController::class, 'post']);
$app->router->post('/contatti', [SiteController::class, 'post']);
