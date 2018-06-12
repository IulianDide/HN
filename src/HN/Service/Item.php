<?php
namespace HN\Service;

use HN\Service\Base as BaseService;

class Item extends BaseService{
	
	public function __construct() {
		parent::__construct();
		return $this;
	}
	
	public function getStoriesData($type, $page) {
		$perPage = 30;
		$url = $this->storiesBaseUrl . "{$type}stories" . $this->dataFormat;
		
		$storyIds = json_decode($this->client->request('GET', $url)->getBody(), true);
		$result = [];
		$counter = 0;
		foreach ($storyIds as $storyId) {
			if ($counter++ < $perPage * ($page - 1)) {
				continue;
			}
			
			$result[$counter] = $this->getItemData($storyId);
			if ($counter > ($perPage * $page) - 1) {
				break;
			}
		}
		
		return $result;
	}
	
	function getItemData($itemId, $getChildren = false) {
		
		$url = $this->itemUrl . $itemId . $this->dataFormat;
		$result = json_decode($this->client->request('GET', $url)->getBody(), true);
		
		if ($getChildren) {
			$result['comments'] = [];
			if (isset($result['kids'])) {
				foreach ($result['kids'] as $childId) {
					$result['comments'][] = $this->getItemData($childId,  true);
				}
			}
		}
		
		if (!isset($result['url'])) {
			$result['url'] = '/HN/item/' . $result['id'];
		}
		
		if (!isset($result['by'])) {
			$result['by'] = 'deleted';
		}
		
		if (!isset($result['descendants'])) {
			$result['descendants'] = 0;
		}
		
		if (!isset($result['text'])) {
			$result['text'] = '';
		}
		
		if (!isset($result['title'])) {
			$result['title'] = $result['text'];
		}
		
		if (!isset($result['score'])) {
			$result['score'] = 0;
		}
		
		$result['time'] = $this->timeAgo($result['time']);
		
		return $result;
	}
	
	function timeAgo($timestamp) {
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
}