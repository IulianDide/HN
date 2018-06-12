<?php
namespace HN\Controller;
use \Silex\Application;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;
use HN\Service\Item as ItemService;
use HN\Service\User as UserService;

class UserController {
    
	
	public function user(Application $app, Request $request) {
        $userId = $request->get('userId');
		$itemService = new ItemService();
		$userService = new UserService($itemService);
        $userData = $userService->getUserData($userId);
		return $app['twig']->render('user.twig', ["user" => $userData]);
    }
	
	public function submited(Request $request, Application $app) {
		$userId = $request->get('userId');
		$page = $request->get('page', 1);
        $url = "submitted/{$userId}/";
		$itemService = new ItemService();
		$userService = new UserService($itemService);
		$stories = $userService->getStoriesDataForUser($userId, $page);
		return $app['twig']->render('index.twig', ["stories" => $stories, 'nextPage' => $url.++$page]);
    }
}