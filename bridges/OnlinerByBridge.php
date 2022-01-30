<?php
class OnlinerByBridge extends BridgeAbstract {

	const MAINTAINER = 'lassana';
	const NAME = 'Onliner па-беларуску';
	const URI = 'https://sp.onliner.by';
	const DESCRIPTION = 'Штотыднёва мы перакладаем з рускай мовы некалькi'
						. ' найцікавейшых тэкстаў і змяшчаем іх на гэтай старонцы.';
	const CACHE_TIMEOUT = 3600; // 1 hour

	public function collectData() {

		$mainPageUrl = self::URI . '/in_belarusian';
		$html = getSimpleHTMLDOM($mainPageUrl);
		$limit = 0;

		foreach($html->find('li.news-list__item') as $element) {
			if($limit < 30) {
				$item = array();
				$item['uid'] = 'urn:sha1:' 
					. hash('sha1', $element->find('a.news-list__item-link', 0)->href);
				$item['uri'] = $element->find('a.news-list__item-link', 0)->href;
				$item['title'] = trim($element->find('span.news-list__item-title', 0)->innertext);
				preg_match(
					'#\(([^]]+)\)#',
					$element->find('span.news-list__item-img', 0)->style,
					$imageUrlMatches);
				if($imageUrlMatches) {
					$item['enclosures'] = array(
						$imageUrlMatches[1]
					);
					$item['content'] = 
						'<img src="' . $imageUrlMatches[1] . '" />'
						. '<p>'
						. trim($element->find('span.news-list__item-text', 0)->innertext);
				} else {
					$item['content'] = trim($element->find('span.news-list__item-text', 0)->innertext);
				}
				// timestamp is not present on the page as of 30-Jan-2022

				$this->items[] = $item;
				$limit++;
			}
		}
	}

	public function getIcon() {
		return static::URI . '/wp-content/uploads/2021/12/favicon.ico';
	}
}
