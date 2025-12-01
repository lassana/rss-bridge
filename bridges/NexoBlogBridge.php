<?php
class NexoBlogBridge extends BridgeAbstract {

	const MAINTAINER = 'lassana';
	const NAME = 'Nexo Blog';
	const URI = 'https://nexo.com';
	const DESCRIPTION = 'Crypto Insights & Market Trends • Nexo Blog.';
	const CACHE_TIMEOUT = 0; // 0 min


	public function collectData() {
		$mainPageUrl = self::URI;

		$html = getSimpleHTMLDOM($mainPageUrl . '/blog')
			or returnServerError('Could not request nexo.com.');
		$limit = 0;

		$section = $html->find('section', 0);
		foreach ($section->find('a.group[data-click-type="link-internal"]') as $element) {
			$item = array();

			$item['uri'] = $mainPageUrl . $element->href;

			$item['title'] = $element->find('p', 0)->innertext;

			$imageNode = $element->find('img', 0);
			if($imageNode) {
				$item['enclosures'] = array(
					str_replace(' ', '%20', $imageNode->getAttribute('src'))
				);
			}

			$item['timestamp'] = strtotime($element->find('span', 0)->innertext);

			$this->items[] = $item;
		}
	}

	public function getIcon() {
		return 'https://content.nexo.com/media/meta-generic.jpg';
	}
}