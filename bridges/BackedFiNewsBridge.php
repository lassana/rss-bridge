<?php
class BackedFiNewsBridge extends BridgeAbstract {

	const MAINTAINER = 'lassana';
	const NAME = 'Backed.fi News & Updates';
	const URI = 'https://backed.fi';
	const DESCRIPTION = 'All the latest news and updates from Backed.';
	const CACHE_TIMEOUT = 0; // 0 min


	public function collectData() {
		$mainPageUrl = self::URI;

		$html = getSimpleHTMLDOM($mainPageUrl . '/news-updates')
			or returnServerError('Could not request backed.fi.');
		$limit = 0;

		$section = $html->find('div.blog9_featured-blog', 0);
		foreach ($section->find('div.collection-item-7') as $element) {
			$item = array();

			$item['uri'] = $mainPageUrl . $element->find('a', 0)->href;

			$item['title'] = $element->find('.blog-heading', 0)->innertext;
			$item['content'] = $element->find('div.text-size-regular-blog', 0)->innertext;

			$imageNode = $element->find('img', 0);
			if($imageNode) {
				$item['enclosures'] = array($imageNode->getAttribute('src'));
			}

			$item['timestamp'] = strtotime($element->find('div.news-date', 0)->innertext);

			$this->items[] = $item;
		}
	}

	public function getIcon() {
		return 'https://cdn.prod.website-files.com/603de78742be86399f94ec70/67cf26edfc364f8cf88e87dd_apple-touch-icon.png';
	}
}