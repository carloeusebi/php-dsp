<?php

use app\App;
use controllers\AuthController;
use controllers\PatientsController;
use controllers\SurveysController;
use controllers\QuestionsController;
use controllers\EmailController;
use controllers\TestsController;

/**
 *@var App $app
 */

/*---------------------------------------------------------------*/
/*----------------------------- API -----------------------------*/
/*---------------------------------------------------------------*/

$app->router->post('/api/login', [AuthController::class, 'login']);
$app->router->delete('/api/logout', [AuthController::class, 'logout']);

$app->router->get('/api/validate', [AuthController::class, 'validate']);

$app->router->get('/api/patients', [PatientsController::class, 'get']);
$app->router->post('/api/patients', [PatientsController::class, 'save']);
$app->router->delete('/api/patients', [PatientsController::class, 'delete']);

$app->router->get('/api/surveys', [SurveysController::class, 'get']);
$app->router->post('/api/surveys', [SurveysController::class, 'save']);
$app->router->delete('/api/surveys', [SurveysController::class, 'delete']);

$app->router->get('/api/questions', [QuestionsController::class, 'get']);
$app->router->post('/api/questions', [QuestionsController::class, 'save']);
$app->router->delete('/api/questions', [QuestionsController::class, 'delete']);

$app->router->get('/api/tests', [TestsController::class, 'get']);
$app->router->post('/api/tests', [TestsController::class, 'save']);

$app->router->post('/api/email', [EmailController::class, 'sendFromAdmin']);
$app->router->post('/api/email/support', [EmailController::class, 'contactSupport']);
