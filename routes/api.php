<?php

use app\app\App;
use app\controllers\AuthController;
use app\controllers\admin\FilesController;
use app\controllers\admin\PatientsController;
use app\controllers\admin\SurveysController;
use app\controllers\admin\QuestionsController;
use app\controllers\admin\TagsController;
use app\controllers\MailController;
use app\controllers\TestsController;

$router = App::$app->router;

/*---------------------------------------------------------------*/
/*----------------------------- API -----------------------------*/
/*---------------------------------------------------------------*/

$router->get('/sanctum/csrf-cookie', [AuthController::class, 'mockSanctum']);

$router->post('/api/login', [AuthController::class, 'login']);
$router->delete('/api/logout', [AuthController::class, 'logout']);

$router->get('/api/patients', [PatientsController::class, 'index']);
$router->get('/api/patients/{id}', [PatientsController::class, 'show']);
$router->post('/api/patients', [PatientsController::class, 'store']);
$router->put('/api/patients/{id}', [PatientsController::class, 'update']);
$router->delete('/api/patients/{id}', [PatientsController::class, 'destroy']);

$router->get('/api/questions', [QuestionsController::class, 'index']);
$router->get('/api/questions/{id}', [QuestionsController::class, 'show']);
$router->post('/api/questions', [QuestionsController::class, 'store']);
$router->put('/api/questions/{id}', [QuestionsController::class, 'update']);
$router->delete('/api/questions/{id}', [QuestionsController::class, 'destroy']);

$router->get('/api/surveys', [SurveysController::class, 'index']);
$router->get('/api/surveys/{id}', [SurveysController::class, 'show']);
$router->post('/api/surveys', [SurveysController::class, 'store']);
$router->put('/api/surveys/{id}', [SurveysController::class, 'update']);
$router->delete('/api/surveys/{id}', [SurveysController::class, 'destroy']);

$router->get('/api/surveys/score', [SurveysController::class, 'getScores']);

$router->get('/api/tags', [TagsController::class, 'index']);
$router->post('/api/tags', [TagsController::class, 'store']);
$router->put('/api/tags/{id}', [TagsController::class, 'update']);
$router->delete('/api/tags/{id}', [TagsController::class, 'destroy']);

$router->get('/api/tests', [TestsController::class, 'index']);
$router->post('/api/tests', [TestsController::class, 'store']);
$router->get('/api/tests/patient', [TestsController::class, 'getPatient']);
$router->post('/api/tests/patient', [TestsController::class, 'updatePatientInfo']);

$router->post('/api/email', [MailController::class, 'sendFromAdmin']);
$router->post('/api/email/support', [MailController::class, 'contactSupport']);

$router->get('/api/file', [FilesController::class, 'download']);
$router->post('/api/file', [FilesController::class, 'upload']);
$router->delete('/api/file', [FilesController::class, 'destroy']);
