<?php
class KastBridge extends BridgeAbstract {

	const MAINTAINER = 'lassana';
	const NAME = 'KAST Blog';
	const URI = 'https://www.kast.xyz';
	const DESCRIPTION = 'KAST\'s editorial hub for company and product updates';
	const CACHE_TIMEOUT = 0; // 0 min

	public function collectData() {
		$mainPageUrl = self::URI;

		$html = getSimpleHTMLDOM($mainPageUrl . '/blog')
			or returnServerError('Could not request kast.xyz.');
		$limit = 0;


		foreach($html->find('div.blog-item-wrap') as $element) {
			$item = array();

			$item['title'] = $element->find('h3.heading-style-h5', 0)->innertext;

			$item['uri'] = self::URI . $element->find('a.blog-item-parent', 0)->href;

			$imageNode = $element->find('img.blog-category-image', 0);
			if($imageNode) {
				$item['enclosures'] = array(
					$imageNode->getAttribute('src')
				);
			}
			$item['content'] = $element->find('div.text-size-regular', 0)->innertext;

			$categoryNode = $element->find('div.blog3-category_category-link', 0);
			if($categoryNode) {
				$item['categories'] = [$categoryNode->innertext];
			}

			$this->items[] = $item;
		}
	}

	public function getIcon() {
		return 'https://cdn.prod.website-files.com/660c048ecf246f8d15a85d0a/660fd853ad726e1e59eb159d_big-logo.png';
	}
}
