<?php

namespace HN\Service;

use \GuzzleHttp\Client as Client;

class Base{
	
	/** Client $client */
	protected $client;
	
	protected $itemUrl = 'https://hacker-news.firebaseio.com/v0/item/';
	protected $storiesBaseUrl = 'https://hacker-news.firebaseio.com/v0/';
	protected $userUrl = 'https://hacker-news.firebaseio.com/v0/user/';
	
	protected $dataFormat = '.json';
	
	public function __construct() {
		$this->client = new Client();
		return $this;
	}
}