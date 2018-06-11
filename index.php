<?php

require_once __DIR__.'/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

$app = new Silex\Application();
$app['debug'] = true;
$app->register(new Silex\Provider\TwigServiceProvider(), array(
	'twig.path' => __DIR__.'/views',
));

$guzzle = new GuzzleHttp\Client();

//news controller
$app->get('/top/{page}', function ($page = 1) use ($app) {
	$type = 'top';
	$result = getStoriesData($type, $page);
	return $app['twig']->render('index.twig', ["stories" => $result, 'nextPage' => $type.'/'.++$page]);
})
->value('page', '1')
->assert('page', '\d+');

$app->get('/', function() use ($app){
	// forward to /news
    $subRequest = Request::create('/top', 'GET');

    return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
});

$app->get('/new/{page}', function ($page = 1) use ($app) {
	$type = 'new';
	$result = getStoriesData($type, $page);
	return $app['twig']->render('index.twig', ["stories" => $result, 'nextPage' => $type.'/'.++$page]);
})
->value('page', '1')
->assert('page', '\d+');

$app->get('/show/{page}', function ($page = 1) use ($app) {
	$type = 'show';
	$result = getStoriesData($type, $page);
	return $app['twig']->render('index.twig', ["stories" => $result, 'nextPage' => $type.'/'.++$page]);
})
->value('page', '1')
->assert('page', '\d+');


$app->get('/ask/{page}', function ($page = 1) use ($app) {
	$type = 'ask';
	$result = getStoriesData($type, $page);
	return $app['twig']->render('index.twig', ["stories" => $result, 'nextPage' => $type.'/'.++$page]);
})
->value('page', '1')
->assert('page', '\d+');

$app->get('/job/{page}', function ($page = 1) use ($app) {
	$type = 'job';
	$result = getStoriesData($type, $page);
	return $app['twig']->render('index.twig', ["stories" => $result, 'nextPage' => $type.'/'.++$page]);
})
->value('page', '1')
->assert('page', '\d+');


//controller for items
$app->get('/item/{id}', function ($id) use ($app) {
	$result = getItemData($id, true);
	
	return $app['twig']->render('item.twig', ["story" => $result]);
})
->assert('id', '\d+');

$app->get('/user/{id}', function ($id) use ($app) {
	$result = getUserData($id);
	return $app['twig']->render('user.twig', ["user" => $result]);
});

$app->get('/submitted/{userId}/{page}', function ($userId, $page = 1) use ($app) {
	$url = "submitted/{$userId}/";
	$result = getStoriesDataForUser($userId, $page);
	return $app['twig']->render('index.twig', ["stories" => $result, 'nextPage' => $url.++$page]);
})
->value('page', '1')
->assert('page', '\d+');

function getItemData($itemId, $getChildren = false) {
	
	global $guzzle;
	$url = "https://hacker-news.firebaseio.com/v0/item/{$itemId}.json";
	$result = json_decode($guzzle->request('GET', $url)->getBody(), true);
	
	if ($getChildren) {
		$result['comments'] = [];
		if (isset($result['kids'])) {
			foreach ($result['kids'] as $childId) {
				$result['comments'][] = getItemData($childId,  true);
			}
		}
	}
	
	if (!isset($result['url'])) {
		$result['url'] = '/HN/item/' . $result['id'];
	}
	
	if (!isset($result['descendants'])) {
		$result['descendants'] = 0;
	}
	
	if (!isset($result['title'])) {
		$result['title'] = $result['text'];
	}
	
	if (!isset($result['score'])) {
		$result['score'] = 0;
	}
	
	$result['time'] = time_ago($result['time']);
	
	return $result;
}

function getStoriesData($type, $page) {
	global $guzzle;
	$perPage = 30;
	$url = "https://hacker-news.firebaseio.com/v0/{$type}stories.json";
	
	$storyIds = json_decode($guzzle->request('GET', $url)->getBody(), true);
	$result = [];
	$counter = 0;
	foreach ($storyIds as $storyId) {
		if ($counter++ < $perPage * ($page - 1)) {
			continue;
		}
		
		$result[$counter] = getItemData($storyId);
		if ($counter > ($perPage * $page) - 1) {
			break;
		}
	}
	
	return $result;
}


function getStoriesDataForUser($userId, $page) {
	$perPage = 30;
	$userData = getUserData($userId);
	
	$result = [];
	$counter = 0;
	foreach ($userData['submitted'] as $storyId) {
		if ($counter++ < $perPage * ($page - 1)) {
			continue;
		}
		
		$result[$counter] = getItemData($storyId);
		if ($counter > ($perPage * $page) - 1) {
			break;
		}
	}
	
	return $result;
}

function getUserData($userId) {
	global $guzzle;
	$url = "https://hacker-news.firebaseio.com/v0/user/{$userId}.json";
	$result = json_decode($guzzle->request('GET', $url)->getBody(), true);
	
	return $result;
}

function time_ago($timestamp)
{
    $etime = time() - $timestamp;
 
    if ($etime < 1)
    {
        return 'Just now';
    }
 
    $a = [
		12 * 30 * 24 * 60 * 60  =>  'year',
		30 * 24 * 60 * 60 => 'month',
		24 * 60 * 60 => 'day',
		60 * 60 => 'hour',
		60 => 'minute',
		1 => 'second'
	];
 
    foreach ($a as $secs => $str)
    {
        $d = $etime / $secs;
        if ($d >= 1)
        {
            $r = floor($d);
            return $r . ' ' . $str . ($r > 1 ? 's' : '') . ' ago';
        }
    }
}



$app->run();
