<?php

use app\app\App;
use app\controllers\AuthController;
use app\controllers\FilesController;
use app\controllers\PatientsController;
use app\controllers\SurveysController;
use app\controllers\QuestionsController;
use app\controllers\TagsController;
use app\controllers\MailController;
use app\controllers\TestsController;

$router = App::$app->router;

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

$router->get('/api/tags', [TagsController::class, 'get']);
$router->post('/api/tags', [TagsController::class, 'save']);
$router->delete('/api/tags', [TagsController::class, 'delete']);

$router->get('/api/tests', [TestsController::class, 'get']);
$router->post('/api/tests', [TestsController::class, 'save']);
$router->get('/api/tests/patient', [TestsController::class, 'getPatient']);
$router->post('/api/tests/patient', [TestsController::class, 'updatePatientInfo']);
$router->get('/api/tests/score', [TestsController::class, 'getScores']);

$router->post('/api/email', [MailController::class, 'sendFromAdmin']);
$router->post('/api/email/support', [MailController::class, 'contactSupport']);

$router->get('/api/file', [FilesController::class, 'download']);
$router->post('/api/file', [FilesController::class, 'upload']);
$router->delete('/api/file', [FilesController::class, 'delete']);
