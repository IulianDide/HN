<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

$app = new Silex\Application();

$app['service.item'] = function ($app) {
    return new HN\Service\Item();
};

$app['service.user'] = function ($app) {
    return new HN\Service\User($app['service.item']);
};



// $app['service.item'] = new Service\Item();
// $app['service.user'] = new Service\User();

$app['debug'] = true;
$app->register(new Silex\Provider\TwigServiceProvider(), array(
	'twig.path' => __DIR__.'/views',
));

$app->get('/', function() use ($app){
	// forward to /top
    $subRequest = Request::create('/top', 'GET');
    return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
});

//news controller
$app->get('/top/{page}', '\HN\Controller\StoryController::top')
->value('page', '1')
->assert('page', '\d+');

$app->get('/new/{page}', '\HN\Controller\StoryController::new')
->value('page', '1')
->assert('page', '\d+');

$app->get('/show/{page}', '\HN\Controller\StoryController::show')
->value('page', '1')
->assert('page', '\d+');


$app->get('/ask/{page}', '\HN\Controller\StoryController::ask')
->value('page', '1')
->assert('page', '\d+');

$app->get('/job/{page}', '\HN\Controller\StoryController::job')
->value('page', '1')
->assert('page', '\d+');


//controller for items
$app->get('/item/{itemId}','\HN\Controller\StoryController::item')
->assert('id', '\d+');

// user data route
$app->get('/user/{userId}', '\HN\Controller\UserController::user');

$app->get('/submitted/{userId}/{page}', '\HN\Controller\UserController::submited')
->value('page', '1')
->assert('page', '\d+');

return $app;