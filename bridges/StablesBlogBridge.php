<?php
class StablesBlogBridge extends BridgeAbstract {

	const MAINTAINER = 'lassana';
	const NAME = 'Stables Blog';
	const URI = 'https://www.stables.money';
	const DESCRIPTION = 'Latest updates on stablecoins, payments, and Web3 fintech innovation.';
	const CACHE_TIMEOUT = 0; // 0 min

	public function collectData() {
		$mainPageUrl = self::URI;

		$html = getSimpleHTMLDOM($mainPageUrl . '/blog')
			or returnServerError('Could not request stables.money.');
		$limit = 0;

		foreach($html->find('div.blog-post') as $element) {
			$item = array();

			$item['uri'] = $mainPageUrl . $element->find('a', 0)->href;

			$item['title'] = $element->find('#blog-title', 0)->innertext;

			$imageNode = $element->find('img.blog-item-thumbnail', 0);
			if($imageNode) {
				$item['enclosures'] = array(
					$imageNode->getAttribute('src')
				);
			}

			$dateNode = $element->find('p.paragraph-9', 0);
			if ($dateNode) {
				$item['timestamp'] = strtotime($dateNode->innertext);
			}

			$categoryNode = $element->find('div.mini-tag', 0);
			if($categoryNode) {
				$item['categories'] = [$categoryNode->innertext];
			}

			$this->items[] = $item;
		}
	}

	public function getIcon() {
		return 'https://cdn.prod.website-files.com/647fe94790c03420770215ff/648ffd5cf8719a50eb3647ba_webclip.png';
	}
}
