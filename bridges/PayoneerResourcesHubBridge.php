<?php
class PayoneerResourcesHubBridge extends BridgeAbstract {

	const MAINTAINER = 'lassana';
	const NAME = 'Payoneer Resources Hub';
	const URI = 'https://www.payoneer.com';
	const DESCRIPTION = 'Learn about online payments and banking with Payoneer&#039;s comprehensive resources and guides.';
	const CACHE_TIMEOUT = 0; // 0 min

	public function collectData() {
		$mainPageUrl = self::URI;

		$html = getSimpleHTMLDOM($mainPageUrl . '/resources')
			or returnServerError('Could not request payoneer.com.');
		$limit = 0;

		foreach($html->find('div.wp-block-post') as $element) {
			$item = array();

			$titleNode = $element->find('h3.wp-block-post-title', 0)->find('a', 0);
			$item['uri'] = $titleNode->href;

			// Check if $this->items already has a record with the same 'uri' and a valid 'timestamp'.
			// If so, we are dealing with a duplicate.
			if ($this->hasValidItem($item['uri'])) {
				continue;
			}

			$item['title'] = $titleNode->innertext;

			$imageNode = $element->find('img.wp-post-image', 0);
			if($imageNode) {
				$item['enclosures'] = array(
					$imageNode->getAttribute('data-lazy-src')
				);
			}
			$item['content'] = $element->find('div.wp-block-post-excerpt', 0)->innertext;

			$dateNode = $element->find('div.wp-block-post-date', 0);
			if ($dateNode) {
				$item['timestamp'] = $dateNode->find('time', 0)->getAttribute('datetime');
			}

			$categories = $element->getAttribute('data-mini-hub');
			$moreCategories = $element->getAttribute('data-mini-mini-hub');
			if($categories) {
				$item['categories'] = [$categories . ', ' . $moreCategories];
			}

			$this->items[] = $item;
		}
	}

	private function hasValidItem($uri) {
		if (empty($uri) || empty($this->items)) {
			return false;
		}
		foreach ($this->items as $existing) {
			if (!isset($existing['uri'])) {
				continue;
			}
			if ($existing['uri'] !== $uri) {
				continue;
			}
			if (!isset($existing['timestamp'])) {
				continue;
			}
			return true;
		}
		return false;
	}

	public function getIcon() {
		return 'https://cdn.prod.website-files.com/660c048ecf246f8d15a85d0a/660fd853ad726e1e59eb159d_big-logo.png';
	}
}
