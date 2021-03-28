<?php
class A1ByBridge extends BridgeAbstract {

	const MAINTAINER = 'lassana';
	const NAME = 'A1 Навiны';
	const URI = 'https://a1.by';
	const DESCRIPTION = 'Gets the service news from A1.by website.';
	const CACHE_TIMEOUT = 0;

	public function collectData(){
		$mainPageUrl = self::URI . '/be/company/c/news';
		$html = getSimpleHTMLDOM($mainPageUrl)
			or returnServerError('Could not request a1.by.');

		foreach($html->find('div.masonry-grid-item') as $element){
			$item = array();
			$item['title'] = $element->find('div.article-listing-item-title', 0)->first_child()->innertext;
			$item['timestamp'] =
				 DateTime::createFromFormat(
				' d.m.Y',
				str_replace(' ', '', $element->find('div.article-listing-item-date', 0)->first_child()->innertext)
				)->getTimestamp()
				;
			$item['uri'] = self::URI . $element->find('a.article-listing-item-link', 0)->href;
			$imageNode = $element->find('img.img', 0);
			if($imageNode){
				$item['enclosures'] = array(
					$imageNode->getAttribute('data-desktop-src')
				);
			}
			$item['content'] = $element->find('div.article-listing-item-title', 0)->first_child()->outertext;

			$this->items[] = $item;
		}
	}
}