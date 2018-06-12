<?php

namespace HN\Service;

use HN\Service\Base as BaseService;
use HN\Service\Item as ItemService;

class User extends BaseService{
	
	/** ItemService $itemService*/
	protected $itemService;
	
	public function __construct(ItemService $itemService) {
		parent::__construct();
		$this->itemService = $itemService;
		
		return $this;
	}
	
	public function getStoriesDataForUser($userId, $page) {
		$perPage = 30;
		$userData = $this->getUserData($userId);
		
		$result = [];
		$counter = 0;
		foreach ($userData['submitted'] as $storyId) {
			if ($counter++ < $perPage * ($page - 1)) {
				continue;
			}
			
			$result[$counter] = $this->itemService->getItemData($storyId);
			if ($counter > ($perPage * $page) - 1) {
				break;
			}
		}
		
		return $result;
	}

	function getUserData($userId) {
		$url = $this->userUrl . $userId. $this->dataFormat;
		$userData = json_decode($this->client->request('GET', $url)->getBody(), true);
		
		if (!isset($userData['about'])) {
			$userData['about'] = '';
		}
		
		return $userData;
	}
	
}