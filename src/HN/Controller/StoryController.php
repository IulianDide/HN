<?php
namespace HN\Controller;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use HN\Service\Item as ItemService;

class StoryController {
    
	
	public function top(Request $request, Application $app) {
		$page = $request->get('page',1);
        $type = 'top';
		$result = $app['service.item']->getStoriesData($type, $page);
		return $app['twig']->render('index.twig', ["stories" => $result, 'nextPage' => $type.'/'.++$page]);
    }
	
	public function new(Request $request, Application $app) {
        $page = $request->get('page',1);
        $type = 'new';
		$result = $app['service.item']->getStoriesData($type, $page);
		return $app['twig']->render('index.twig', ["stories" => $result, 'nextPage' => $type.'/'.++$page]);
    }
	
	public function page(Request $request, Application $app) {
        $page = $request->get('page',1);
        $type = 'page';
		$result = $app['service.item']->getStoriesData($type, $page);
		return $app['twig']->render('index.twig', ["stories" => $result, 'nextPage' => $type.'/'.++$page]);
    }
	
	public function show(Request $request, Application $app) {
        $page = $request->get('page',1);
        $type = 'show';
		$result = $app['service.item']->getStoriesData($type, $page);
		return $app['twig']->render('index.twig', ["stories" => $result, 'nextPage' => $type.'/'.++$page]);
    }
	
	public function ask(Request $request, Application $app) {
		$page = $request->get('page',1);
        $type = 'ask';
		$result = $app['service.item']->getStoriesData($type, $page);
		return $app['twig']->render('index.twig', ["stories" => $result, 'nextPage' => $type.'/'.++$page]);
    }
	
	public function job(Request $request, Application $app) {
        $page = $request->get('page',1);
        $type = 'job';
		$result = $app['service.item']->getStoriesData($type, $page);
		return $app['twig']->render('index.twig', ["stories" => $result, 'nextPage' => $type.'/'.++$page]);
    }
	
	public function item(Request $request, Application $app) {
		$itemId = $request->get('itemId');
        $result = $app['service.item']->getItemData($itemId, true);
		return $app['twig']->render('item.twig', ["story" => $result]);
    }
}