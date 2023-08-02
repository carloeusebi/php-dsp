<?php

use app\app\App;
use app\controllers\AuthController;
use app\controllers\PatientsController;
use app\controllers\SurveysController;
use app\controllers\QuestionsController;
use app\controllers\MailController;
use app\controllers\TestsController;

/**
 *@var App $app
 */
$router = $app->router;

/*---------------------------------------------------------------*/
/*----------------------------- API -----------------------------*/
/*---------------------------------------------------------------*/

$router->post('/api/login', [AuthController::class, 'login']);
$router->delete('/api/logout', [AuthController::class, 'logout']);

$router->get('/api/validate', [AuthController::class, 'validate']);

$router->get('/api/patients', [PatientsController::class, 'get']);
$router->post('/api/patients', [PatientsController::class, 'save']);
$router->delete('/api/patients', [PatientsController::class, 'delete']);

$router->get('/api/surveys', [SurveysController::class, 'get']);
$router->post('/api/surveys', [SurveysController::class, 'save']);
$router->delete('/api/surveys', [SurveysController::class, 'delete']);

$router->get('/api/questions', [QuestionsController::class, 'get']);
$router->post('/api/questions', [QuestionsController::class, 'save']);
$router->delete('/api/questions', [QuestionsController::class, 'delete']);

$router->get('/api/tests', [TestsController::class, 'get']);
$router->post('/api/tests', [TestsController::class, 'save']);

$router->post('/api/email', [MailController::class, 'sendFromAdmin']);
$router->post('/api/email/support', [MailController::class, 'contactSupport']);
